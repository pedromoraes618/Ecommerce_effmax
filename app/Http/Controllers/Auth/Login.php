<?php
$nome_do_arquivo = __FILE__;



if (isset($_POST['form'])) {
    include "../../../../db/conn.php";
    include "../../../../helps/funcao.php";

    //Load Composer's autoloader
    $retornar = array();
    $acao = $_POST['acao'];
    $cookie = "lgbrd";

    if ($acao == "login") {
        // Define as variáveis a partir dos valores do formulário
        foreach ($_POST as $name => $value) {
            ${$name} = $value;
            ${$name} = str_replace("'", "", ${$name}); //remover aspas simples

        }

        // Valida o email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $retornar["errors"]["email"] = "Insira um email válido.";
        }

        // Valida a senha
        if (empty($senha)) {
            $retornar["errors"]["senha"] = "Insira sua senha.";
        }

        // Verifica se não há erros de validação
        if (!isset($retornar["errors"])) {
            $id = consulta_tabela('tb_user_loja', 'cl_email', $email, 'cl_id');
            $senha_hash = consulta_tabela('tb_user_loja', 'cl_id', $id, 'cl_senha');
            $confirmacao_email = consulta_tabela('tb_user_loja', 'cl_id', $id, 'cl_confimar_email');

            // Verifica a senha e a confirmação do email
            if (!password_verify($senha, $senha_hash)) {
                $retornar["errors"]["warning"] = "
                <div class='alert alert-danger' role='alert'>
                <div>
               Credenciais incorretas, tente novamente
                </div>
              </div>";
            } elseif ($confirmacao_email == "0" && !empty($id) && !empty($senha_hash)) {
                $retornar["errors"]["warning"] = "
                <div class='alert alert-warning' role='alert'>
                <div>
                A confirmação para a ativação da conta foi enviada para o seu e-mail
                </div>
              </div>";
            }
        }

        // Define a resposta com base nos erros ou no sucesso do login
        if (isset($retornar["errors"])) {
            $retornar["data"] = array("status" => false, "response" => $retornar["errors"]);
            echo json_encode($retornar); //retornando o array
            exit;
        }

        $sessao = md5(uniqid(time())); // Gera um novo código para a sessão
        $tempo_vida = time() + (30 * 24 * 60 * 60); // Define o tempo de vida do cookie (um mês em segundos)
        setcookie($cookie, $sessao, $tempo_vida, "/"); // Cria o cookie de sessão

        $chek = update_registro("tb_user_loja", "cl_id", $id, '', '', "cl_sessao", $sessao); // Atualiza a sessão no banco de dados

        // Verifica se a atualização foi bem-sucedida
        if ($chek == true) {
            transferirProduto($id); //realizar a transferencia de produtos que estão no cookie para o bando de dados
            $retornar["data"] = array("status" => true, "message" => "Login efetuado com sucesso");
        } else {
            $retornar["data"] = array("status" => false, "message" => "Ops, o site está apresentando um mau funcionamento. Lamentamos o inconveniente, mas estamos trabalhando para resolver o problema o mais rápido possível. Por favor, tente acessar novamente em alguns minutos");
            $mensagem = utf8_decode("Ecommerce - $nome_do_arquivo -  login usuário");
            registrar_log($conecta, 'ecommerce', $data, $mensagem); // Registrar log do erro
        }
    }

    if ($acao == "logout") {

        // Define o tempo de expiração do cookie no passado para removê-lo
        setcookie($cookie, null, time() - 1, '/');
        $retornar["data"] = array("status" => true, "message" => "Logout efeturado com sucesso");
    }
    // Encerre a conexão com o banco de dados
    mysqli_close($conecta);
    echo json_encode($retornar); //retornando o array
}
