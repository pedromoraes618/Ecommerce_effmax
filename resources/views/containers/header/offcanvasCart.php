<?php
include "../../../../app/Http/Controllers/Cart.php";
?>
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasCart" aria-labelledby="offcanvasCartLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasCartLabel"><i class="bi bi-cart3"></i> Carrinho</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">

        <?php
        if ($dados_usuario) {
            $produtosCart = $dados_usuario['produtos_cart'];
            $qtd_cart = $dados_usuario['qtd_cart'];
            if ($qtd_cart > 0) {
                $total = 0;
                $registro = 0;
                foreach ($produtosCart as $linha) {
                    $id = $linha['idproduto'];
                    $titulo = utf8_encode($linha['cl_descricao']);
                    $referencia = utf8_encode($linha['cl_referencia']);
                    $preco_venda = ($linha['cl_preco_venda']);
                    $preco_promocao = ($linha['cl_preco_promocao']);
                    $data_validade_promocao = ($linha['cl_data_valida_promocao']);
                    $estoque = ($linha['cl_estoque']);
                    $quantidade = ($linha['cl_quantidade']);

                    $codigo = ($linha['cl_codigo']);

                    $imagem_produto_default = verficar_paramentro($conecta, 'tb_parametros', "cl_id", "34");
                    $img_produto = consulta_tabela_query(
                        $conecta,
                        "select * from tb_imagem_produto where cl_codigo_nf ='$codigo' order by cl_ordem asc limit 1",
                        'cl_descricao'
                    );
                    $diretorio_imagem  = $img_produto == "" ? "../../../../../$empresa/$imagem_produto_default" : "../../../../../$empresa/img/produto/$img_produto";

                    $registro += $quantidade;
                    $condicao = ($linha['cl_condicao']);
                    $span_condicao = $condicao == "USADO" ? "Usado" : '';

                    // $diretorio_imagem = "../../../../../$empresa/img/produto/$imagem_capa$extensao_img_capa";
                    $span_estoque = $estoque < 1 ? "<span class='badge text-bg-dark'>Sem estoque</span>" : "";

                    if ($estoque > 0) {
                        if (($data_validade_promocao >= $data_lancamento) and $preco_promocao > 0) {
                            $valores = "<small class='original-price-promo text-muted text-decoration-line-through ' style='font-size:0.7em'>" . real_format($preco_venda) . "</small></br>
                    <span class='promo-price fw-bold'> " . real_format($preco_promocao * $quantidade) .
                                "</span>";
                            $total += $preco_promocao * $quantidade;
                        } else {
                            // Se não houver promoção, mostrar apenas o preço normal e centralizar
                            $valores = "<span class='original-price fw-bold'>" . real_format($preco_venda * $quantidade) . "</span>";
                            $total += $preco_venda * $quantidade;
                        }
                    }
        ?>
                    <div class="card bg-body-tertiary border-0 mb-3 ">
                        <div class="row  position-relative d-flex align-items-center ">
                            <a href="#" class="delete-product  text-dark" onclick="updateCart(this,<?= $id ?>,1,'remover')">
                                <i class="bi bi-x-circle-fill mx-2 position-absolute top-0 end-0 pe-auto"></i>
                            </a>
                            <div class="col-4 position-relative">
                                <!-- <img src="..." class="img-fluid rounded-start" alt="..."> -->
                                <a <?php if ($estoque > 0) { ?> href="?product-details=<?= $id; ?>&<?= $titulo; ?>" <?php }; ?> class="text-decoration-none">
                                    <img width="120" src='<?= $diretorio_imagem; ?>' alt="<?= $titulo; ?> ?>">
                                </a>
                                <div class="item-condition rounded shadow bg-danger px-2 text-light">
                                    <span class="fw-semibold"><?= $span_condicao; ?></span>
                                </div>

                            </div>

                            <div class="col-8">
                                <div class="card-body">
                                    <div class="mb-2">
                                        <p class="card-title fw-bold"><?= $titulo; ?> </p>
                                        <p class="card-subtitle"><?= $referencia . " " . $span_estoque; ?></p>
                                    </div>
                                    <?php if ($estoque > 0) { ?>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex " data-quantity="data-quantity">
                                                <button class="btn btn-custom btn-sm btn-outline border border-1 p-0" data-type="minus" onclick="qtdCart(this,<?= $id ?>,<?= $quantidade - 1 ?>)"> <i style="font-size:1.5em;" class="bi bi-dash decrement"></i></button>

                                                <input readonly class="form-control text-center input-spin-none bg-transparent
                          border-1 outline-none p-0" style="width:40px;" id="qtd_prd_cart" type="text" min="1" value="<?= $quantidade; ?>">

                                                <button class="btn btn-sm btn-custom btn-outline border border-1  p-0" data-type="plus">
                                                    <i style="font-size:1.5em;" onclick="qtdCart(this,<?= $id ?>,<?= $quantidade + 1 ?>)" class="bi bi-plus increment"></i></button>
                                            </div>
                                            <div class="card-text"><?= $valores; ?></div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>

                    </div>
                <?php
                };
                ?>
    </div>

    <?php if ($total > 0) { ?>
        <div class="offcanvas-footer p-2">
            <div class="p-2">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="fs-5 fw-semibold ">
                            Subtotal
                        </span>
                        <br>
                        <span class="fs-6"><?= $registro ?> <?= $registro > 1 ? 'Itens' : 'Item' ?></span>
                    </div>
                    <div>
                        <div class="fs-5 fw-semibold text-end">
                            <?= real_format($total); ?>
                        </div>
                        <div class="text-muted" style="color:#2980b9">
                            <?php if ($qtd_parcela > 0) {
                                $totalParcelado = real_format($total / $qtd_parcela);
                                echo "até $qtd_parcela x de $totalParcelado sem juros</div>";
                            } ?>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <?php if ($freteGratis == "true") { ?>
                        <div class="mb-3">
                            <small><i class="bi bi-info-circle"></i> <b>FRETE GRÁTIS</b>
                                <?php if ($freteCondicaoValorEstado > 0) { ?>
                                    Para o estado de <?= $estado_empresa; ?> - acima de <?= real_format($freteCondicaoValorEstado); ?>.
                                <?php } ?>
                                <?php if ($freteCondicaoValorForaEstado > 0) { ?>
                                    Demais estados - acima de <?= real_format($freteCondicaoValorForaEstado); ?>.
                                <?php } ?>
                            </small>
                        </div>
                    <?php } ?>
                </div>
                <div>
                    <a type="button" class="btn-danger finalize-purchase rounded btn" href="?checkout=true">Iniciar Compra</a>
                </div>
            </div>
        </div>
    <?php }
            } else {
    ?>
    <div class="border border-primary-subtle rounded p-2 text-center">
        <i class="bi bi-info-circle"></i> O carrinho de compras está vazio
    </div><?php }
        };
