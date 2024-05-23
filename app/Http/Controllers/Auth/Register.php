<?php
$nome_do_arquivo = __FILE__;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


if (isset($_POST['form'])) {
    include "../../../../db/conn.php";
    include "../../../../helps/funcao.php";

    require '../../../../public/lib/vendor/phpmailer/phpmailer/src/Exception.php';
    require '../../../../public/lib/vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require '../../../../public/lib/vendor/phpmailer/phpmailer/src/SMTP.php';

    //Load Composer's autoloader
    $retornar = array();
    $acao = $_POST['acao'];
    $cookie = "lgbrd";
    $nome_ecommerce = consulta_tabela('tb_parametros', 'cl_id', '64', "cl_valor");

    if ($acao == "register") {
        mysqli_begin_transaction($conecta);
        foreach ($_POST as $name => $value) { //define os valores das variaveis e os nomes com refencia do name do input no formulario
            ${$name} = $value;
            ${$name} = str_replace("'", "", ${$name}); //remover aspas simples
        }

        $validaEmail =  consulta_tabela('tb_user_loja', 'cl_email', $email, 'cl_email'); //validar se o email já está cadastrado

        if (empty($nome)) {
            $retornar["errors"]["nome"] = required("seu nome");
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $retornar["errors"]["email"] = required("um email válido");
        } elseif (!empty($email) and $email == $validaEmail) {
            $retornar["errors"]["email"] = ("Esse email já está registrado");
        }

        if (empty($senha)) {
            $retornar["errors"]["senha"] = required("sua senha");
        }

        if (!preg_match('/[0-9]/', $senha) || !preg_match('/[a-z]/', $senha) || !preg_match('/[A-Z]/', $senha)) {
            $retornar["errors"]["senha"] = ("A senha deve conter números, letras maiúsculas e minúsculas.");
        }


        if (empty($confirmar_senha) and !empty($senha)) {
            $retornar["errors"]["confirmar_senha"] = required("a confirmação da senha");
        }

        if ((!empty($confirmar_senha) and !empty($senha)) and ($senha != $confirmar_senha)) {
            $retornar["errors"]["confirmar_senha"] = "A confirmação da senha está diferente da senha informada";
        }

        if (!isset($aceita_termos)) {
            $retornar["errors"]["aceita_termos"] = ("É necessario aceitar os termos");
        }


        if (isset($retornar["errors"])) {
            $retornar["data"] = array("status" => false, "response" => $retornar["errors"]);
            echo json_encode($retornar); //retornando o array
            exit;
        }


        $chave = password_hash($senha . date("Y-m-d H:i:s"), PASSWORD_DEFAULT); //gerar uma chave
        $senha = password_hash($senha, PASSWORD_DEFAULT); //codificando senha
        $query = "INSERT INTO `tb_user_loja` (`cl_data`,`cl_nome`,`cl_email`, `cl_senha`, `cl_confimar_email`, `cl_chave_confirmar_email`,`cl_cookie`) 
        VALUES ('$data','$nome', '$email', '$senha', '0', '$chave','1')";
        $insert = mysqli_query($conecta, $query);

        if ($insert) {
            mysqli_commit($conecta);
            $retornar["data"] = array("status" => true, "email" => $email, "message" => "Cadastro efetuado com sucesso");

            $nome_cliente =  consulta_tabela('tb_user_loja', 'cl_email', $email, 'cl_nome');
            $server_name = $_SERVER['SERVER_NAME'];

            $html = "
            <div style='width:100%;max-width:600px;margin:0 auto;text-align:center;'>
            <div style='padding:10px;text-align:left;font-family:Arial, sans-serif;'>
            <p>Olá, $nome_cliente,</p>
            <p>Recebemos sua solicitação de cadastro na $nome_ecommerce. Para ativar sua conta, clique no botão abaixo:</p>
            <a href='$server_name/$nome_ecommerce/?confirm-email=true&code=$chave' class='btn' target='_blank'>Confirmar E-mail</a>
            <p>Se o botão acima não funcionar, copie e cole o seguinte link em seu navegador:</p>
            <p>$server_name/$nome_ecommerce/?confirm-email=true&code=$chave</p>
            <p>Após a confirmação do seu e-mail, você terá acesso completo à nossa loja online.</p>
            <p>Se você não realizou esse cadastro, por favor, entre em contato conosco imediatamente.</p>
            <p>Obrigado,</p>
            <p>Equipe $nome_ecommerce</p>
            </div> </div>";
            $mail = new PHPMailer(true);
            $sendEmail = sendEmail($mail, $email, 'Confirme seu e-mail', $html, $html);


            // $retornar["dados"] =  array("sucesso" => true, "title" => "Email enviado", 'email_remetente' => $email_remetente, "email_destinatario" => $email);
        } else {
            mysqli_rollback($conecta);
            $retornar["data"] = array("status" => false, "message" => "Ops, o site está apresentando um mau funcionamento,
             Lamentamos o inconveniente, mas estamos trabalhando para resolver o problema o mais rápido possível. Por favor, tente acessar novamente em alguns minutos");

            $erro = str_replace("'", "", mysqli_error($conecta));
            $mensagem = utf8_decode("Ecommerce - $nome_do_arquivo -  Cadastrar um novo usuário / erro - $erro");
            registrar_log($conecta, 'ecommerce', $data, $mensagem); // Registrar log do erro
            // $retornar["dados"] =  array("sucesso" => false, "title" => "Não foi possivel realizar o cadastro, para saber mais informações, entra em contato com a equipe da $empresa pelo email $email_remetente ");
        }
    }




    // Encerre a conexão com o banco de dados
    mysqli_close($conecta);
    echo json_encode($retornar); //retornando o array
}
