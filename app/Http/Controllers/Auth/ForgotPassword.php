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
    $nome_ecommerce = consulta_tabela('tb_parametros', 'cl_id', '64', "cl_valor");

    if ($acao == "forgotPassword") {
        foreach ($_POST as $name => $value) { //define os valores das variaveis e os nomes com refencia do name do input no formulario
            ${$name} = $value;
            ${$name} = str_replace("'", "", ${$name}); //remover aspas simples
        }

        $validaEmail =  consulta_tabela('tb_user_loja', 'cl_email', $email, 'cl_email'); //validar se o email existe

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $retornar["errors"]["email"] = required("um email válido");
        } elseif (!empty($email) and $email != $validaEmail) {
            $retornar["errors"]["email"] = ("Email não registrado");
        }

        if (isset($retornar["errors"])) {
            $retornar["data"] = array("status" => false, "response" => $retornar["errors"]);
            echo json_encode($retornar); //retornando o array
            exit;
        }

        if (!empty($email) and $email == $validaEmail) {
            $nome_cliente = utf8_encode(consulta_tabela('tb_user_loja', 'cl_email', $email, 'cl_nome'));
            $chave = md5(uniqid(time())); // Gera um novo código para a sessão
            $server_name = $_SERVER['SERVER_NAME'];
            $html = "
            <div style='width:100%;max-width:600px;margin:0 auto;text-align:center;'>
                <p style='padding:10px;text-align:left;font-family:Arial, sans-serif;'>
                    Olá, $nome_cliente<br><br>
                    Recebemos sua solicitação de mudança de senha na $nome_ecommerce. Clique no link abaixo para criar uma nova senha:<br><br>
                    <a href=$server_name/$nome_ecommerce/?forgot-password=true&code=$chave' style='
                    color:#e74c3c;text-decoration:none;' target='_blank'>Criar Nova Senha</a><br><br>
                    Se o link não funcionar, copie e cole este URL no seu navegador:<br>
                    Após a alteração, você receberá um e-mail confirmando a mudança de senha.<br><br>
                    Se você não solicitou essa mudança, por favor, entre em contato conosco.
                </p>
            </div>";

            $mail = new PHPMailer(true);
            $sendEmail = sendEmail($mail, $email, 'Redefina a sua senha', $html, $html);

            if ($sendEmail) {
                $retornar["data"] = array("status" => true, "message" => "login efetuado com sucesso", "email" => $email);

                // Converter a string em um objeto DateTime
                $link_expirar = new DateTime($data);

                // Adicionar 30 minutos
                $link_expirar->add(new DateInterval('PT30M'));
                $link_expirar = $link_expirar->format('Y-m-d H:i:s');

                update_registro("tb_user_loja", 'cl_email', $email, '', '', 'cl_chave_reset_senha', $chave);
                update_registro("tb_user_loja", 'cl_email', $email, '', '', 'cl_expirar_chave_senha', $link_expirar);
            } else {
                $retornar["data"] = array("status" => false, "message" => "Ops, o site está apresentando um mau funcionamento,
                Lamentamos o inconveniente, mas estamos trabalhando para resolver o problema o mais rápido possível. Por favor, tente acessar novamente em alguns minutos");

                $mensagem = utf8_decode("Ecommerce - $nome_do_arquivo - email para resetar senha");
                registrar_log($conecta, 'ecommerce', $data, $mensagem); // Registrar log do erro
            }
        }
    }
    if ($acao == "resetPassword") {
        foreach ($_POST as $name => $value) { //define os valores das variaveis e os nomes com refencia do name do input no formulario
            ${$name} = $value;
            ${$name} = str_replace("'", "", ${$name}); //remover aspas simples
        }

        if (empty($senha)) {
            $retornar["errors"]["senha"] = required("a sua nova senha");
        } elseif (!preg_match('/[0-9]/', $senha) || !preg_match('/[a-z]/', $senha) || !preg_match('/[A-Z]/', $senha)) {
            $retornar["errors"]["senha"] = ("A senha deve conter números, letras maiúsculas e minúsculas.");
        }
        if (empty($confirmar_senha) and !empty($senha)) {
            $retornar["errors"]["confirmar_senha"] = required("a confirmação da senha");
        } elseif ((!empty($confirmar_senha) and !empty($senha)) and ($senha != $confirmar_senha)) {
            $retornar["errors"]["confirmar_senha"] = "A confirmação da senha está diferente da senha informada";
        }

        if (empty($token)) {
            $retornar["errors"]["warning"] = "
            <div class='alert alert-danger' role='alert'>
            <div>
            Link não encontrado, favor, refaça o processo
            </div>
          </div>";
        } else {
            $validaToken = consulta_tabela('tb_user_loja', 'cl_chave_reset_senha', $token, 'cl_id');
            if (!empty($validaToken)) {
                $data_limite = consulta_tabela('tb_user_loja', 'cl_chave_reset_senha', $token, 'cl_expirar_chave_senha');
                if ($data > $data_limite) {
                    $retornar["errors"]["senha"] = ("Esse link expirou, refaça o processo novamente");
                }
            } else {
                $retornar["errors"]["warning"] = "
                <div class='alert alert-danger' role='alert'>
                <div>
               Email não registrado!
                </div>
              </div>";
            }
        }


        if (isset($retornar["errors"])) {
            $retornar["data"] = array("status" => false, "response" => $retornar["errors"]);
            echo json_encode($retornar); //retornando o array
            exit;
        }


        $senha = password_hash($senha, PASSWORD_DEFAULT); //codificando senha
        $reset_senha = update_registro('tb_user_loja', 'cl_id', $validaToken, '', '', 'cl_senha', $senha);
        if ($reset_senha) {
            $retornar["data"] = array("status" => true, "message" => "Login efetuado com sucesso");
        } else {
            $retornar["data"] = array("status" => false, "message" => "Ops, o site está apresentando um mau funcionamento. Lamentamos o inconveniente, mas estamos trabalhando para resolver o problema o mais rápido possível. Por favor, tente acessar novamente em alguns minutos");
            $mensagem = utf8_decode("Ecommerce - $nome_do_arquivo -  resetar senha");
            registrar_log($conecta, 'ecommerce', $data, $mensagem); // Registrar log do erro
        }
    }

    // Encerre a conexão com o banco de dados
    mysqli_close($conecta);
    echo json_encode($retornar); //retornando o array
}
