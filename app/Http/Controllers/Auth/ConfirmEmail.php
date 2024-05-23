<?php
$nome_do_arquivo = __FILE__;



if (isset($_POST['form'])) {
    include "../../../../db/conn.php";
    include "../../../../helps/funcao.php";

    //Load Composer's autoloader
    $retornar = array();
    $acao = $_POST['acao'];
    $cookie = "lgbrd";

    if ($acao == "confirmEmail") {
        // Define as variáveis a partir dos valores do formulário
        foreach ($_POST as $name => $value) {
            ${$name} = $value;
            ${$name} = str_replace("'", "", ${$name}); //remover aspas simples

        }

        if (!empty($token)) {
            $id = consulta_tabela("tb_user_loja", 'cl_chave_confirmar_email', $token, 'cl_id');
            if (!empty($id)) {
                $sessao = md5(uniqid(time())); // Gera um novo código para a sessão
                $tempo_vida = time() + (30 * 24 * 60 * 60); // Define o tempo de vida do cookie (um mês em segundos)
                setcookie($cookie, $sessao, $tempo_vida, "/"); // Cria o cookie de sessão
                update_registro("tb_user_loja", "cl_id", $id, '', '', "cl_sessao", $sessao); // Atualiza a sessão no banco de dados
                update_registro("tb_user_loja", "cl_id", $id, '', '', "cl_confimar_email", '1');
                transferirProduto($id); //realizar a transferencia de produtos que estão no cookie para o bando de dados
                $retornar["data"] = array("status" => true, "message" => "Login efetuado com sucesso");
            }
        } else {
            $retornar["data"] = array("status" => false, "message" => "token não encontrado");
        }
    }


    // Encerre a conexão com o banco de dados
    mysqli_close($conecta);
    echo json_encode($retornar); //retornando o array
}
