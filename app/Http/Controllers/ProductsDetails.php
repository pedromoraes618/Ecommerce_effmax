<?php
$nome_do_arquivo = __FILE__;


if (isset($_GET['page'])) {
    $page = isset($_GET['page']) ? $_GET['page'] : '';
    if ($page == "products_details") {
        include "../../../../db/conn.php";
        include "../../../../helps/funcao.php";
        $products_details = $_GET['product-details'];

        $empresa = consulta_tabela('tb_empresa', 'cl_id', '1', 'cl_empresa'); // Diretório raiz do sistema gerenciador
        $nome_ecommerce = consulta_tabela('tb_parametros', 'cl_id', '64', "cl_valor");
        $select = "SELECT prd.*,prd.cl_id as produtoid,cat.cl_mensagem, md.cl_descricao as und FROM tb_produtos 
        as prd left join tb_unidade_medida as md on md.cl_id = prd.cl_und_id left join tb_subgrupo_estoque as cat on cat.cl_id = prd.cl_grupo_id
         where prd.cl_id = $products_details and prd.cl_tipo_id ='1' and prd.cl_estoque >0  and prd.cl_status_ativo = 'SIM'  ";
        $consultar_produtos = mysqli_query($conecta, $select); // Consulta
        if ($consultar_produtos) {
            $qtd_prd = mysqli_num_rows($consultar_produtos); // Quantidade de produtos

            $linha = mysqli_fetch_assoc($consultar_produtos);
            $id = ($linha['produtoid']);

            $codigo = ($linha['cl_codigo']);
            $imagem_capa = consulta_tabela("tb_imagem_produto", 'cl_descricao', $codigo . "_1", 'cl_descricao'); //imagem capa
            $extensao_img_capa = consulta_tabela("tb_imagem_produto", 'cl_descricao', $codigo . "_1", 'cl_extensao'); //imagem capa
            $class_fav = verificaProdutoAuth($id)['qtd_fav'] > 0 ? 'fav-true' : '';
            $text_fav = verificaProdutoAuth($id)['qtd_fav'] > 0 ? '<i class="bi bi-check fs-5"></i><span class="open-favorite">Ver favoritos</span></small>' :
                '<i class="bi bi-heart"></i> Adicionar aos favoritos';

            $titulo = utf8_encode($linha['cl_descricao']);
            $referencia = utf8_encode($linha['cl_referencia']);
            $grupo_id = utf8_encode($linha['cl_grupo_id']);
            $unidade = utf8_encode($linha['und']);

            $descricao_produto = utf8_encode($linha['cl_descricao_extendida_delivery']);
            $mensagem_grupo = utf8_encode($linha['cl_mensagem']);

            $preco_venda = ($linha['cl_preco_venda']);
            $preco_promocao = ($linha['cl_preco_promocao']);
            $data_validade_promocao = ($linha['cl_data_valida_promocao']);
            $estoque = ($linha['cl_estoque']);
            $condicao = ($linha['cl_condicao']);
            $span_condicao = $condicao == "USADO" ? "<span class='badge text-bg-danger'>Usado</span>" : '';

            $link_compartilhar =  $_SERVER['SERVER_NAME'] . "/$nome_ecommerce/?product-details=$id&$titulo";



            $imagem_produto_default = verficar_paramentro($conecta, 'tb_parametros', "cl_id", "34");
            $img_produto = consulta_tabela_query(
                $conecta,
                "select * from tb_imagem_produto where cl_codigo_nf ='$codigo' order by cl_ordem asc limit 1",
                'cl_descricao'
            );
            if ($img_produto == "") {
                $diretorio_imagem = "../../../../../$empresa/$imagem_produto_default";
            } else {
                $diretorio_imagem = "../../../../../$empresa/img/produto/$img_produto";
            }

            // $span_cart = verificaProdutoAuth($id)['qtd_cart'] ? 'Remover' : 'Adicionar';
            if (($data_validade_promocao >= $data_lancamento) and $preco_promocao > 0) {
                $valores = "<small class='original-price-promo text-muted text-decoration-line-through fs-5'>" . real_format($preco_venda) . "</small>
    <span class='promo-price fw-bold fs-4' > " . real_format($preco_promocao) .
                    "</span>";
                $total = $preco_promocao;
            } else {
                // Se não houver promoção, mostrar apenas o preço normal e centralizar
                $valores = "<span class='original-price fw-bold fs-4'>" . real_format($preco_venda) . "</span>";
                $total = $preco_venda;
            }


            /*pixel */
            if (auth('') !== false) {
                $dados = ['pagina' => "?product-details=true&id=$id"];
                $dados_usuario = auth('')['dados_usuario'];
                $dados = [
                    'dados_usuario' => $dados_usuario,
                    'dados' => $dados,
                    'produto' => [
                        'produtoID' => $id,
                        'descricao' => $titulo
                    ]
                ];
                pixel('viewContentDetProd', $dados);
            }


            /*consultar as fotos secundarias */
            // $select = "SELECT img.* FROM tb_imagem_produto as img where cl_codigo_nf = '$codigo' ";
            // $consultar_img = mysqli_query($conecta, $select); // Consulta
            // $qtd_img = mysqli_num_rows($consultar_img); // Quantidade de produtos



            /*consultar produto Similares */
            $select = "SELECT prd.*,prd.cl_id as produtoid, cat.cl_mensagem   FROM tb_produtos 
            as prd left join tb_subgrupo_estoque as cat on cat.cl_id = prd.cl_grupo_id
             where ( prd.cl_grupo_id ='$grupo_id' or prd.cl_referencia ='$referencia' ) and prd.cl_estoque >0 
             and  prd.cl_status_ativo = 'SIM' and cl_tipo_id ='1' order by rand()";
            $consultar_produtos_similares = mysqli_query($conecta, $select); // Consulta
            if ($consultar_produtos_similares) {
                $qtd_prd_similares = mysqli_num_rows($consultar_produtos_similares);
            } else {
                $erro = str_replace("'", "", mysqli_error($conecta));
                $mensagem = utf8_decode("Ecommerce - $nome_do_arquivo -  consultar_produtos_similares / erro - $erro");
                registrar_log($conecta, 'ecommerce', $data, $mensagem); // Registrar log do erro
            }
        } else {
            $erro = str_replace("'", "", mysqli_error($conecta));
            $mensagem = utf8_decode("Ecommerce - $nome_do_arquivo - page == products_details / erro - $erro");
            registrar_log($conecta, 'ecommerce', $data, $mensagem); // Registrar log do erro
        }

        $qtd_parcela = 0;

        $query = "SELECT max(cl_parcelamento_sem_juros) as qtd,cl_id,cl_tipo_pagamento_id FROM tb_forma_pagamento WHERE  cl_ativo_delivery='S' and cl_ativo='S' group by cl_id order by cl_parcelamento_sem_juros desc";
        $resultados = mysqli_query($conecta, $query);
        if ($resultados) {
            $linha = mysqli_fetch_assoc($resultados);
            $qtd_parcela = $linha['qtd'];
            $tipo_pagamento = utf8_encode($linha['cl_tipo_pagamento_id']);
            $icone_fpg_parcela = consulta_tabela('tb_tipo_pagamento', 'cl_id', $tipo_pagamento, 'cl_icone');
        }

        $descontoFpg = 0;
        $query = "SELECT max(cl_desconto) as desconto,cl_id,fpg.* FROM tb_forma_pagamento as fpg where cl_ativo_delivery='S' and cl_ativo='S' 
         group by cl_id order by cl_desconto desc";
        $resultados = mysqli_query($conecta, $query);
        if ($resultados) {
            $linha = mysqli_fetch_assoc($resultados);
            $descontoFpg = $linha['desconto'];
            $descricaoFpg = utf8_encode($linha['cl_descricao']);
            $tipo_pagamento = utf8_encode($linha['cl_tipo_pagamento_id']);
            $icone_fpg_desconto = consulta_tabela('tb_tipo_pagamento', 'cl_id', $tipo_pagamento, 'cl_icone');
        }
    }
}
