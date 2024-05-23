<?php

$nome_do_arquivo = __FILE__;


if (isset($_POST['form'])) {
    include "../../../db/conn.php";
    include "../../../helps/funcao.php";


    $retornar = array();
    $acao = $_POST['acao'];
    $ambientePagamento = consulta_tabela('tb_parametros', 'cl_id', '70', 'cl_valor');
    $tokenPagamentoHomologacao = consulta_tabela('tb_parametros', 'cl_id', '71', 'cl_valor');
    $tokenPagamentoProducao = consulta_tabela('tb_parametros', 'cl_id', '72', 'cl_valor');
    //$tokenPagamentoProducao = "APP_USR-2348807058961-021415-67c5f595783c7472179428152ab67b67-1682207570";

    if ($ambientePagamento == "1") { //homologacao
        $tokenPagamento = $tokenPagamentoHomologacao;
    } elseif ($ambientePagamento == "2") { //producao
        $tokenPagamento = $tokenPagamentoProducao;
    }

    if ($acao == "create") { //crição do pedido
        foreach ($_POST as $name => $value) { //define os valores das variaveis e os nomes com refencia do name do input no formulario
            ${$name} = utf8_decode($value);
            ${$name} = str_replace("'", "", ${$name}); //remover aspas simples
        }

        $pedidoID = consulta_tabela('tb_pedido_loja', 'cl_codigo_nf', $codigo_nf, 'cl_id');
        $valor_produto = consulta_tabela('tb_pedido_loja', 'cl_codigo_nf', $codigo_nf, 'cl_valor_produto');
        $status_pagamento = consulta_tabela('tb_pedido_loja', 'cl_codigo_nf', $codigo_nf, 'cl_status_pagamento');
        $status_compra = consulta_tabela('tb_pedido_loja', 'cl_codigo_nf', $codigo_nf, 'cl_status_compra');
        if (!empty($pedidoID)) {
            $data_expirar = consulta_tabela('tb_pedido_loja', 'cl_id', $pedidoID, 'cl_data_expirar');
            if ((!empty($data_expirar) and ($data >= $data_expirar))) {
                $retornar["errors"]["warning"] = "
                <div class='alert alert-warning' role='alert'>
                    <div>
                    O tempo para finalizar o seu pedido expirou. Por favor, refaça o pedido para continuar</div>
                  </div>";
            }
        } elseif ($valor_produto == 0) {
            $retornar["errors"]["warning"] = "
            <div class='alert alert-warning' role='alert'>
                <div>O seu carrinho está vazio!</div>
              </div>";
        } elseif ($status_compra == "CANCELADO") {
            $retornar["errors"]["warning"] = "
            <div class='alert alert-warning' role='alert'>
                <div>Esse pedido foi Cancelado!</div>
              </div>";
        } elseif ($status_pagamento == "approved") {
            $retornar["errors"]["warning"] = "
            <div class='alert alert-warning' role='alert'>
                <div>Esse pedido já foi concluido!</div>
              </div>";
        } else {
            $retornar["errors"]["warning"] = "
            <div class='alert alert-warning' role='alert'>
                <div>
                Pedido não encontrado. Se você estiver enfrentando algum problema com o seu pedido, por favor, entre em contato conosco ou refaça o pedido para que possamos ajudá-lo a concluí-lo                </div>
              </div>";
        }

        if (isset($retornar["errors"])) {
            $retornar["data"] = array("status" => false, "response" => $retornar["errors"]);
            echo json_encode($retornar); //retornando o array
            exit;
        }



        $gerarPagamentoStatus = gerarPagamentoMercadoPago($tokenPagamento, $pedidoID)['data']['status'];
        if ($gerarPagamentoStatus == true) {
            $retornar["data"] = array("status" => true, "link_externo" => gerarPagamentoMercadoPago($tokenPagamento, $pedidoID)['data']['link_externo']);
        } else {
            $retornar["data"] = array("status" => false, "message" => "Ops, o site está apresentando um mau funcionamento,
             Lamentamos o inconveniente, mas estamos trabalhando para resolver o problema o mais rápido possível. Por favor, tente acessar novamente em alguns minutos");
            $mensagem = utf8_decode("Ecommerce - $nome_do_arquivo - " . gerarPagamentoMercadoPago($tokenPagamento, $pedidoID)['data']['message']);
            registrar_log($conecta, 'ecommerce', $data, $mensagem); // Registrar log do erro
        }
    }

    mysqli_close($conecta);
    echo json_encode($retornar); //retornando o array
}




if (isset($_GET['containers'])) {
    $container = $_GET['containers'];
    if ($container == "confirm_order") {

        include "../../../../db/conn.php";
        include "../../../../helps/funcao.php";
        $empresa = consulta_tabela('tb_empresa', 'cl_id', '1', 'cl_empresa'); //diretorio raiz sistema
        $email_empresa = consulta_tabela("tb_parametros", "cl_id", '74', 'cl_valor');

        $estado_empresa = consulta_tabela('tb_empresa', 'cl_id', '1', 'cl_estado'); //estado da empresa

        $freteGratis = consulta_tabela('tb_parametros', 'cl_id', '87', 'cl_valor');
        $freteCondicaoValorEstado = consulta_tabela('tb_parametros', 'cl_id', '88', 'cl_valor'); //FRETE GRATIS PARA DENTRO DO ESTADO
        $freteCondicaoValorForaEstado = consulta_tabela('tb_parametros', 'cl_id', '89', 'cl_valor'); //FRETE GRATIS PARA FORA DO ESTADO

        $codigo_nf = isset($_GET['code']) ? $_GET['code'] : '';
        if (auth('') != false) {
            $dados_usuario = auth($codigo_nf); // Supondo que a função auth('') retorna os dados do usuário e dos produtos
        } else {
            $dados_usuario = cookieAuth($codigo_nf);
        }

        $qtd_registro = 0;
        $query = "SELECT pd.*,fpg.cl_descricao as formapagamento FROM tb_pedido_loja as pd 
        left join tb_forma_pagamento as fpg on fpg.cL_id = pd.cl_pagamento_id_interno where pd.cl_codigo_nf = '$codigo_nf' ";
        $consulta = mysqli_query($conecta, $query);
        if ($consulta) {
            $qtd_registro = mysqli_num_rows($consulta);
            if ($qtd_registro > 0) {
                $linha = mysqli_fetch_assoc($consulta);
                $nome = utf8_encode($linha['cl_nome']);
                $email = ($linha['cl_email']);
                $cpfcnpj = ($linha['cl_cpf_cnpj']);
                $telefone = ($linha['cl_telefone']);
                $endereco = utf8_encode($linha['cl_endereco']);
                $bairro = utf8_encode($linha['cl_bairro']);
                $numero = utf8_encode($linha['cl_numero']);
                $complemento = utf8_encode($linha['cl_complemento']);
                $cep = ($linha['cl_cep']);
                $cidade = utf8_encode($linha['cl_cidade']);
                $estado = utf8_encode($linha['cl_estado']);
                $transportadora = utf8_encode($linha['cl_transportadora']);
                $formapagamento = utf8_encode($linha['formapagamento']);


                $valor_frete = ($linha['cl_valor_frete']);
                $valor_produto = ($linha['cl_valor_produto']);
                $valor_desconto = ($linha['cl_desconto']);
                $valor_liquido = ($linha['cl_valor_liquido']);
                $status_compra = utf8_encode($linha['cl_status_compra']);
                $status_pagamento = utf8_encode($linha['cl_status_pagamento']);
                $valor_cupom = ($linha['cl_valor_cupom']);

                if ($valor_frete == 0) {
                    $valor_frete = "Grátis";
                }
            }
        }
    }
}
