<?php
include "../../../../app/Http/Controllers/Initial.php";
?>
<div class="filter-search-group" style="width: 100%;">
    <?php
    while ($linha = mysqli_fetch_assoc($consulta_filter)) {
        $id = ($linha['cl_id']);
        $codigo = ($linha['cl_codigo']);
        $titulo = utf8_encode($linha['cl_descricao']);
        $referencia = utf8_encode($linha['cl_referencia']);
        $preco_venda = ($linha['cl_preco_venda']);
        $preco_promocao = ($linha['cl_preco_promocao']);
        $data_validade_promocao = ($linha['cl_data_valida_promocao']);
        $estoque = ($linha['cl_estoque']);
        $condicao = ($linha['cl_condicao']);
        $span_condicao = $condicao == "USADO" ? "Usado" : '';


        $imagem_produto_default = verficar_paramentro($conecta, 'tb_parametros', "cl_id", "34");
        $img_produto = consulta_tabela_query(
            $conecta,
            "select * from tb_imagem_produto where cl_codigo_nf ='$codigo' order by cl_ordem asc limit 1",
            'cl_descricao'
        );
        $diretorio_imagem  = $img_produto == "" ? "../../../../../$empresa/$imagem_produto_default" : "../../../../../$empresa/img/produto/$img_produto";

        $class_fav = verificaProdutoAuth($id)['qtd_fav'] > 0 ? 'fav-true' : '';
        $span_cart = verificaProdutoAuth($id)['qtd_cart'] > 0 ? 'Remover' : 'Adicionar';

        // var_dump(verificaProdutoAuth($id)['qtd_fav']);
        // $diretorio_imagem = "../../../../../$empresa/img/produto/$imagem_capa$extensao_img_capa";
        if (($data_validade_promocao >= $data_lancamento) and $preco_promocao > 0) {
            $valor = real_format($preco_promocao);
        } else {
            // Se não houver promoção, mostrar apenas o preço normal e centralizar
            $valor = real_format($preco_venda);
        }
    ?>
        <div class="card bg-body-tertiary rounded-0 border-0 ">
            <a href="?product-details=<?= $id; ?>&<?= $titulo; ?>" 
            class="text-decoration-none  row position-relative d-flex pb-1 pt-1 align-items-center card-body ">
                <div class="col-auto position-relative">
                    <img width="50" src='<?= $diretorio_imagem; ?>' alt="<?= $titulo; ?> ?>">

                </div>
                <div class="col-9">
                    <div>
                        <span class="mb-0 title"><?= $titulo; ?> - </span>
                        <span class="mb-0 ref"><?= $referencia; ?></span>
                    </div>
                    <div class="valor"><?= $valor; ?></div>
                </div>
            </a>
        </div>
        <hr class="m-0">
    <?php } ?>
</div>