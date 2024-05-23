<?php

$nome_do_arquivo = __FILE__;



if (isset($_GET['containers'])) {
    $container = $_GET['containers'];
    if ($container == "order_completed") {

        include "../../../../db/conn.php";
        include "../../../../helps/funcao.php";
        $empresa = consulta_tabela('tb_empresa', 'cl_id', '1', 'cl_empresa'); //diretorio raiz sistema
        $email_empresa = consulta_tabela("tb_parametros", "cl_id", '74', 'cl_valor');
        $retirada_endereco = utf8_encode(consulta_tabela("tb_parametros", "cl_id", '91', 'cl_valor'));
        $instrucao_retirada = utf8_encode(consulta_tabela("tb_parametros", "cl_id", 99, "cl_valor"));

        $codigo_nf = isset($_GET['code']) ? $_GET['code'] : '';

        $qtd_registro = 0;
        $query = "SELECT pd.*,fpg.cl_descricao as formapagamento FROM tb_pedido_loja as pd 
        left join tb_forma_pagamento as fpg on fpg.cL_id = pd.cl_pagamento_id_interno where pd.cl_codigo_nf = '$codigo_nf' ";
        $consulta = mysqli_query($conecta, $query);
        if ($consulta) {
            $qtd_registro = mysqli_num_rows($consulta);
            if ($qtd_registro > 0) {
                $linha = mysqli_fetch_assoc($consulta);
                $nome = utf8_encode($linha['cl_nome']);
                $usuario_id = ($linha['cl_usuario_id']);
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


                $valor_frete = ($linha['cl_valor_frete']);
                $valor_produto = ($linha['cl_valor_produto']);
                $valor_cupom = ($linha['cl_valor_cupom']);
                $valor_desconto = ($linha['cl_desconto']);
                $valor_liquido = ($linha['cl_valor_liquido']);
            }
        }

        $query = "SELECT pdl.*,prd.*,prd.cl_id as produtoid  FROM tb_produto_pedido_loja as pdl inner join tb_produtos as prd on prd.cl_id = pdl.cl_produto_id where pdl.cl_codigo_nf = '$codigo_nf' ";
        $consultaProdutos = mysqli_query($conecta, $query);
        if (!$consultaProdutos) {
            $execute['data'] = array("status" => false, "type" => "aplicacao", "message" => "Erro ao realizar a consulta ao produtos do pedido função gerarLinkVendaMp " . str_replace("'", "", mysqli_error($conecta)));
            return $execute;
        }
    }
}
