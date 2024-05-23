<?php

$nome_do_arquivo = __FILE__;



if (isset($_POST['form'])) {
    include "../../../db/conn.php";
    include "../../../helps/funcao.php";

    //Load Composer's autoloader
    $retornar = array();
    $acao = $_POST['acao'];

    if ($acao == "create") { //crição do pedido
        mysqli_begin_transaction($conecta);

        foreach ($_POST as $name => $value) { //define os valores das variaveis e os nomes com refencia do name do input no formulario
            ${$name} = utf8_decode($value);
            ${$name} = str_replace("'", "", ${$name}); //remover aspas simples
        }

        $validaEmail = consulta_tabela('tb_cadastro_newsletter', 'cl_email', $emailNewsletter, 'cl_id');
        if (!empty($validaEmail)) {
            $retornar["errors"]["warning"] = "
            <div class='alert alert-warning' role='alert'>
                <div>
                Email já registrado!
                </div>
              </div>";
        }
        if (empty($nomeNewsletter)) {
            $retornar["errors"]["nomeNewsletter"] = required("seu nome");
        }
        if (empty($emailNewsletter) || !filter_var($emailNewsletter, FILTER_VALIDATE_EMAIL)) {
            $retornar["errors"]["emailNewsletter"] = required("um email válido");
        }

        if (isset($retornar["errors"])) {
            $retornar["data"] = array("status" => false, "response" => $retornar["errors"]);
            echo json_encode($retornar); //retornando o array
            exit;
        }

        $query = "INSERT INTO `tb_cadastro_newsletter`
         (`cl_data`, `cl_nome`, `cl_email`) 
         VALUES ('$data', '$nomeNewsletter', '$emailNewsletter')";
        $insert = mysqli_query($conecta, $query);

        if ($insert) {
            mysqli_commit($conecta);
            $retornar["data"] = array("status" => true,  "message" => "Cadastro realizado com sucesso");
        } else {
            mysqli_rollback($conecta);
            $retornar["data"] = array("status" => false, "message" => "Ops, o site está apresentando um mau funcionamento,
             Lamentamos o inconveniente, mas estamos trabalhando para resolver o problema o mais rápido possível. Por favor, tente acessar novamente em alguns minutos");
            $mensagem = utf8_decode("Ecommerce - $nome_do_arquivo");
            registrar_log($conecta, 'ecommerce', $data, $mensagem); // Registrar log do erro
            // $retornar["dados"] =  array("sucesso" => false, "title" => "Não foi possivel realizar o cadastro, para saber mais informações, entra em contato com a equipe da $empresa pelo email $email_remetente ");
        }
    }

    mysqli_close($conecta);
    echo json_encode($retornar); //retornando o array
}





// if (isset($_GET['layouts'])) {
//     $cookie = 0;
//     echo "sim";
//     $layouts = $_GET['layouts'];

//     if ($layouts == "footer") {

//         include "../../../../db/conn.php";
//         include "../../../../helps/funcao.php";
//         $whatsap = (consulta_tabela('tb_parametros', 'cl_id', 44, 'cl_valor')); //numero whatsap

//         if (auth('') !== false) {
//             $cookie = auth('')['dados_usuario']['cookie'];
//         }
//     }
// }
