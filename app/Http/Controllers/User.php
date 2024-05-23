<?php

$nome_do_arquivo = __FILE__;


if (isset($_POST['form'])) {
    include "../../../db/conn.php";
    include "../../../helps/funcao.php";

    //Load Composer's autoloader
    $retornar = array();
    $acao = $_POST['acao'];
    if ($acao == "show") {
        $user_id = auth('')['id'];
        $query = "SELECT * from tb_user_loja as user where user.cl_id = '$user_id' ";
        $consulta = mysqli_query($conecta, $query);
        $linha = mysqli_fetch_assoc($consulta);
        $nome = utf8_encode($linha['cl_nome']);
        $cpf_cnpj = ($linha['cl_cpf_cnpj']);
        $email = ($linha['cl_email']);
        $cep = ($linha['cl_cep']);

        $telefone = ($linha['cl_telefone']);
        $endereco = utf8_encode($linha['cl_endereco']);
        $bairro = utf8_encode($linha['cl_bairro']);
        $cidade = utf8_encode($linha['cl_cidade']);
        $numero = ($linha['cl_numero']);


        $informacao = array(
            "nome" => $nome,
            "cpf_cnpj" => $cpf_cnpj,
            "email" => $email,
            "cep" => $cep,
            "telefone" => $telefone,

            "endereco" => $endereco,
            "bairro" => $bairro,
            "cidade" => $cidade,
            "numero" => $numero,

        );

        $retornar["data"] = array("status" => true, "response" => $informacao);
    }
    if ($acao == "update") {
        mysqli_begin_transaction($conecta);

        foreach ($_POST as $name => $value) { //define os valores das variaveis e os nomes com refencia do name do input no formulario
            ${$name} = utf8_decode($value);
            ${$name} = str_replace("'", "", ${$name}); //remover aspas simples
        }
        $user_id = auth('')['id'];

        if (isset($_POST['nome'])) { //altera perfil
            $telefone = preg_replace('/[^0-9]/', '', $telefone); // remover caracteres especias
            $cpfcnpj = preg_replace('/[^0-9]/', '', $cpfcnpj); // remover caracteres especias

            if (empty($nome)) {
                $retornar["errors"]["nome"] = required("seu nome");
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $retornar["errors"]["email"] = required("um email válido");
            }

            if (empty($cpfcnpj)) {
                $retornar["errors"]["cpfcnpj"] = required("um cpf ou cnpj válido");
            } elseif ((!validarCPF($cpfcnpj)) and (strlen($cpfcnpj) > 0 and strlen($cpfcnpj) <= 11)) { //validar cpf
                $retornar["errors"]["cpfcnpj"] = required("um seu cpf válido");
            } elseif ((!validarCNPJ($cpfcnpj)) and (strlen($cpfcnpj) > 12)) { //validar cnpj
                $retornar["errors"]["cpfcnpj"] = required("um cnpj válido");
            }

            $query = "UPDATE `tb_user_loja` SET `cl_cpf_cnpj` = '$cpfcnpj', `cl_nome` = '$nome', `cl_email` = '$email', `cl_telefone` = '$telefone' WHERE `cl_id` = '$user_id'";
            $operacao = mysqli_query($conecta, $query);
        }

        if (isset($_POST['endereco'])) { //altera perfil
            $cep = preg_replace('/[^0-9]/', '', $cep); // remover caracteres especias

            if (empty($cep)) {
                $retornar["errors"]["cep"] = required("cep");
            }

            $query = "UPDATE `tb_user_loja` SET `cl_endereco` = '$endereco', `cl_numero` = '$numero',
             `cl_bairro` = '$bairro', `cl_cep` = '$cep',`cl_cidade` = '$cidade'  WHERE `cl_id` = '$user_id'";
            $operacao = mysqli_query($conecta, $query);
        }


        if (isset($_POST['senha'])) { //altera senha
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

            $senha = password_hash($senha, PASSWORD_DEFAULT); //codificando senha
            $query = "UPDATE `tb_user_loja` SET `cl_senha` = '$senha' WHERE `cl_id` = '$user_id'";
            $operacao = mysqli_query($conecta, $query);
        }

        if (isset($retornar["errors"])) {
            $retornar["data"] = array("status" => false, "response" => $retornar["errors"]);
            echo json_encode($retornar); //retornando o array
            exit;
        }


        if ($operacao) {
            mysqli_commit($conecta);
            $retornar["data"] = array("status" => true, "message" => "Alteração realizada com sucesso");
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


if (isset($_GET['containers'])) {
    $container = $_GET['containers'];
    if ($container == "user" or $container == "perfil" or $container == "order") {

        include "../../../../db/conn.php";
        include "../../../../helps/funcao.php";
        $empresa = consulta_tabela('tb_empresa', 'cl_id', '1', 'cl_empresa'); //diretorio raiz sistema
        $email_empresa = consulta_tabela("tb_parametros", "cl_id", '74', 'cl_valor');
        $retirada_endereco = utf8_encode(consulta_tabela("tb_parametros", "cl_id", '91', 'cl_valor'));
        $accesstoken = consulta_tabela("tb_parametros", "cl_id", '67', 'cl_valor'); //token kangu
        $instrucao_retirada = utf8_encode(consulta_tabela("tb_parametros", "cl_id", 99, "cl_valor"));

        $qtd_registro  = 0;
        $user_id = auth('')['id'];
        $page = isset($_GET['user']) ? $_GET['user'] : '';
        $query = "SELECT * from tb_user_loja as user where user.cl_id = '$user_id'";
        $consulta = mysqli_query($conecta, $query);
        $linha = mysqli_fetch_assoc($consulta);

        $data_cadastro = formatDateB($linha['cl_data']);
        $nome_usuario = utf8_encode($linha['cl_nome']);
        $cpf_cnpj_usuario = utf8_encode($linha['cl_cpf_cnpj']);
        $email_usuario = ($linha['cl_email']);
        $cep_usuario = ($linha['cl_cep']);
        $cidade_usuario = utf8_encode($linha['cl_cidade']);
        $telefone_usuario = ($linha['cl_telefone']);
        $endereco_usuario = utf8_encode($linha['cl_endereco']);
        $bairro_usuario = utf8_encode($linha['cl_bairro']);
        $numero_usuario = ($linha['cl_numero']);
        $component = isset($_GET['component']) ? $_GET['component'] : '';
        $pedidoID = isset($_GET['orderID']) ? $_GET['orderID'] : '';



        $query = "SELECT pd.*,fpg.cl_descricao as formapagamento FROM tb_pedido_loja as pd 
        left join tb_forma_pagamento as fpg on fpg.cL_id = pd.cl_pagamento_id_interno WHERE pd.cl_usuario_id ='$user_id'  ";
        if (!empty($pedidoID)) {
            $query .= " and pd.cl_id='$pedidoID' ";
        } else {
            $query .= " and pd.cl_status_compra ='CONCLUIDO' ";
        }
        $query .= " order by pd.cl_id desc";
        $consulta = mysqli_query($conecta, $query);
        if ($consulta) {
            $qtd_registro = mysqli_num_rows($consulta);
            if ($qtd_registro > 0) {
                $linha = mysqli_fetch_assoc($consulta);
                $codigo_nf = ($linha['cl_codigo_nf']);
                $nome = utf8_encode($linha['cl_nome']);
                $data_pedido = ($linha['cl_data']);
                $pedido = ($linha['cl_pedido']);
                $email = ($linha['cl_email']);
                $cpfcnpj = ($linha['cl_cpf_cnpj']);
                $telefone = ($linha['cl_telefone']);
                $endereco = utf8_encode($linha['cl_endereco']);
                $bairro = utf8_encode($linha['cl_bairro']);
                $numero = utf8_encode($linha['cl_numero']);
                $complemento = utf8_encode($linha['cl_complemento']);
                $cep = utf8_encode($linha['cl_cep']);
                $cidade = utf8_encode($linha['cl_cidade']);
                $estado = utf8_encode($linha['cl_estado']);
                $transportadora = utf8_encode($linha['cl_transportadora']);
                $formapagamento = utf8_encode($linha['formapagamento']);
                $status_pagamento = utf8_encode($linha['cl_status_pagamento']);
                $status_compra = utf8_encode($linha['cl_status_compra']);
                $codigo_rastreio = ($linha['cl_codigo_rastreio']);
                $id_simulacao_frete = ($linha['cl_id_simulacao_frete']);
                $data_entrega = ($linha['cl_data_entrega']);

                $valor_frete = ($linha['cl_valor_frete']);
                $valor_produto = ($linha['cl_valor_produto']);
                $valor_desconto = ($linha['cl_desconto']);
                $valor_cupom = ($linha['cl_valor_cupom']);
                $valor_liquido = ($linha['cl_valor_liquido']);


                $ambiente = verficar_paramentro($conecta, 'tb_parametros', 'cl_id', '35'); // 1 - homologacao 2 - producao
                if ($ambiente == "1") { //consultar o pdf da nota fiscal
                    $server = verficar_paramentro($conecta, 'tb_parametros', 'cl_id', '60');
                } elseif ($ambiente == "2") {
                    $server =  verficar_paramentro($conecta, 'tb_parametros', 'cl_id', '61');
                }

                $numero_nf = consulta_tabela('tb_nf_saida', 'cl_codigo_nf', $codigo_nf, 'cl_numero_nf');
                if ($numero_nf != "") {
                    $serie_nf = consulta_tabela('tb_nf_saida', 'cl_codigo_nf', $codigo_nf, 'cl_serie_nf');
                    $pdf_nf =  consulta_tabela('tb_nf_saida', 'cl_codigo_nf', $codigo_nf, 'cl_pdf_nf');
                    $caminho_nf = $server . $pdf_nf;
                }

                $query = "SELECT pdl.*,prd.*,prd.cl_id as produtoid  FROM tb_produto_pedido_loja as pdl 
        inner join tb_produtos as prd on prd.cl_id = pdl.cl_produto_id where pdl.cl_codigo_nf = '$codigo_nf' ";
                $consultaProdutos = mysqli_query($conecta, $query);
            }
        }


        $data_pedido_historico = isset($_GET['data']) ? $_GET['data'] : '';
        $qtd_registro_historico = 0;
        $query = "SELECT pd.*,fpg.cl_descricao as formapagamento FROM tb_pedido_loja as pd 
        left join tb_forma_pagamento as fpg on fpg.cL_id = 
        pd.cl_pagamento_id_interno WHERE pd.cl_usuario_id ='$user_id' and pd.cl_status_compra ='CONCLUIDO'";
        if (!empty($data_pedido_historico)) {
            $data_inicial = ($data_pedido_historico . ' 01:01:01');
            $data_final = ($data_pedido_historico . ' 23:59:59');
            $query .= " and cl_data between '$data_inicial' and '$data_final' ";
        }
        $query .= " order by pd.cl_id desc";
        $consultaHistorico = mysqli_query($conecta, $query);
        if ($consultaHistorico) {
            $qtd_registro_historico = mysqli_num_rows($consultaHistorico);
        }
    }
}
