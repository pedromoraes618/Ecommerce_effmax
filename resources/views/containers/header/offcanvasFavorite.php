<?php
include "../../../../app/Http/Controllers/Favorite.php";
?>

<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasFavorite" aria-labelledby="offcanvasFavoriteLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasFavoriteLabel"><i class="bi bi-heart fs-5"></i> Favoritos</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <?php
        if ($dados_usuario) :

            $produtosFav = $dados_usuario['produtos_fav'];
            $qtd_fav = $dados_usuario['qtd_fav'];
            if ($qtd_fav > 0) {
                $total = 0;
                $registro = 0;
                foreach ($produtosFav as $linha) :
                    $id = $linha['idproduto'];
                    $titulo = utf8_encode($linha['cl_descricao']);
                    $referencia = utf8_encode($linha['cl_referencia']);
                    $preco_venda = ($linha['cl_preco_venda']);
                    $preco_promocao = ($linha['cl_preco_promocao']);
                    $data_validade_promocao = ($linha['cl_data_valida_promocao']);
                    $estoque = ($linha['cl_estoque']);

                    $codigo = ($linha['cl_codigo']);

                    $imagem_produto_default = verficar_paramentro($conecta, 'tb_parametros', "cl_id", "34");
                    $img_produto = consulta_tabela_query(
                        $conecta,
                        "select * from tb_imagem_produto where cl_codigo_nf ='$codigo' order by cl_ordem asc limit 1",
                        'cl_descricao'
                    );
                    $diretorio_imagem  = $img_produto == "" ? "../../../../../$empresa/$imagem_produto_default" : "../../../../../$empresa/img/produto/$img_produto";


                    $condicao = ($linha['cl_condicao']);
                    $span_condicao = $condicao == "USADO" ? "Usado" : '';
                    // $span_cart = verificaProdutoAuth($id)['qtd_cart'] > 0 ? 'Remover' : 'Adicionar';


                    // $diretorio_imagem = "../../../../../$empresa/img/produto/$imagem_capa$extensao_img_capa";

                    if (($data_validade_promocao >= $data_lancamento) and $preco_promocao > 0) {
                        $valores = "<small class='original-price-promo text-muted text-decoration-line-through'  style='font-size:0.7em'>" . real_format($preco_venda) . "</small>
                    <span class='promo-price fw-bold''> " . real_format($preco_promocao) .
                            "</span>";
                        $total += $preco_promocao;
                    } else {
                        // Se não houver promoção, mostrar apenas o preço normal e centralizar
                        $valores = "<span class='original-price fw-bold''>" . real_format($preco_venda) . "</span>";
                        $total += $preco_venda;
                    }
        ?>

                    <div class="card bg-body-tertiary border-0  mb-3 ">
                        <div class="row position-relative d-flex align-items-center ">
                            <a href="#" class="delete-product  text-dark" onclick="updateFavorite(this,<?= $id ?>)">
                                <i class="bi bi-x-circle-fill mx-2 position-absolute top-0 end-0 pe-auto"></i>
                            </a>
                            <div class="col-4 position-relative">
                                <!-- <img src="..." class="img-fluid rounded-start" alt="..."> -->
                                <a href="?product-details=<?= $id; ?>&<?= $titulo; ?>" class="text-decoration-none">
                                    <img class="border-0 border img-card-offcanvas" width="120" src='<?= $diretorio_imagem; ?>' alt="<?= $titulo; ?>">
                                </a>
                                <div class="item-condition rounded shadow bg-danger px-2 text-light">
                                    <span class="fw-semibold"><?= $span_condicao; ?></span>
                                </div>
                            </div>
                            <div class="col-8">
                                <div class="card-body">
                                    <div class="mb-1">
                                        <p class="card-title fw-bold"><?= $titulo; ?> </p>
                                        <p class="card-subtitle"><?= $referencia; ?></p>
                                    </div>
                                    <div class="mb-2">
                                        <p class="card-text"><?= $valores; ?></p>
                                    </div>
                                    <div>
                                        <button type="button" class="btn  add-cart border" onclick="updateFavorite(this,<?= $id ?>),
                                    updateCart(this,<?= $id; ?>,1,'adicionar')"> <i class="bi bi-cart-plus mx-2"></i>
                                            <span class="rounded span-cart-<?= $id; ?>">Adicionar</span></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                endforeach;
            } else {
                ?>
                <div class="border border-primary-subtle rounded p-2 text-center">
                    <i class="bi bi-info-circle"></i> O seus favoritos estão vazios
                </div>
    </div>
<?php }
        endif; ?>
</div>