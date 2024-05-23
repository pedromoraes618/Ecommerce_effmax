<?php
$id = ($linha['produtoid']);

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
    $valores = "<span class='original-price-promo text-muted text-decoration-line-through'>" . real_format($preco_venda) . "</span>
    <span class='promo-price fw-bold'> " . real_format($preco_promocao) .
        "</span>";
} else {
    // Se não houver promoção, mostrar apenas o preço normal e centralizar
    $valores = "<span class='original-price fw-bold'>" . real_format($preco_venda) . "</span>";
}
?>

<div class="item position-relative shadow-sm">
    <div class="product-card card border-0">
        <div class="border  text-decoration-none img product-card-header position-relative">
            <a href="?product-details=<?= $id; ?>&<?= $titulo; ?>">
                <img class="img-thumbnail border-0" src='<?= $diretorio_imagem  ?>' alt=" <?= $titulo; ?>">
            </a>
            <div class="item-condition rounded shadow 
                bg-danger px-2 text-light">
                <span class="fw-semibold"><?= $span_condicao; ?></span>
            </div>
        </div>

        <div href="?product-details=<?= $id; ?>&<?= $titulo; ?>" class="product-details-text p-2 product-card-body text-decoration-none">
            <div class="mb-2">
                <p class="card-title fw-semibold"><?= $titulo;  ?></p>
                <p class="card-subtitle text-muted"><?= $referencia; ?></p>
            </div>
            <div class="price"><?php echo $valores; ?></div>
        </div>

        <div class="d-flex justify-content-center product-card-footer bg-body-tertiary">
            <a href="?product-details=<?= $id; ?>&<?= $titulo; ?>" class="btn-card fw-semibold btn-card-left"><i class="bi bi-eye"></i> Detalhes</a>
            <a href="#" class="btn-card btn-card-right fw-semibold " onclick="updateCart(this,<?= $id; ?>,1,'adicionar')"><i class="bi bi-cart-plus "></i> Comprar</a>
        </div>
    </div>

    <div class="favorite-icon mb-3 <?= $class_fav; ?>" onclick="updateFavorite(this,<?= $id; ?>)">
        <i class="bi bi-heart-fill"></i>
    </div>


</div>