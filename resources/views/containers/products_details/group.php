<?php include "../../../../app/Http/Controllers/ProductsDetails.php"; ?>

<div class="product-details-container mt-2 mt-md-4">
    <?php if ($consultar_produtos and $qtd_prd > 0) { ?>
        <div class="row g-4 mb-5">
            <div class="col-md-1 secondary-img order-md-1 order-2">
                <?php
                $resultados = consulta_linhas_tb_query($conecta, "select * from tb_imagem_produto where cl_codigo_nf='$codigo' order by cl_ordem asc");
                if ($resultados) {
                    foreach ($resultados as $linha) {
                        $descricao = $linha['cl_descricao'];
                        $diretorio_img_secundario = "../../../../../$empresa/img/produto/$descricao";

                ?>
                        <div class="left-img rounded mb-2">
                            <img class="img-thumbnail border-0" src="<?= $diretorio_img_secundario; ?>" alt="">
                        </div>
                <?php };
                }; ?>
            </div>
            <div class="col-md-5 main-image order-md-2 order-1">
                <!-- Coluna para a foto principal, descrição, preço, etc. -->
                <div class="main-image-1  rounded">
                    <img class="img-thumbnail border-0" src="<?= $diretorio_imagem; ?>" alt="">
                </div>

                <!-- Adicione mais detalhes do produto conforme necessário -->
            </div>

            <div class="col-md-4 product-details-text order-md-3 order-4">
                <div class="mb-4">
                    <p class="title mb-0 fw-bolder"><?= $titulo; ?></p>
                    <p class="subtitle mb-1 text-muted"><?= $referencia; ?></p>
                    <div>
                        <span class="badge text-bg-secondary">Formato: <?= $unidade ?></span>
                        <?= $span_condicao; ?>
                    </div>

                </div>
                <div class="price mb-0"><?= $valores; ?></div>
                <?php if ($qtd_parcela > 0) { ?>
                    <div class="text-muted text-danger">
                        <?php
                        $totalParcelado = real_format($total / $qtd_parcela);
                        echo "$icone_fpg_parcela $qtd_parcela" . "x de $totalParcelado sem juros";
                        ?>
                    </div>
                <?php } ?>

                <?php if ($descontoFpg > 0) { ?>
                    <div class="text-muted mb-1">
                        <?php
                        echo "$icone_fpg_desconto $descontoFpg" . "% de desconto pagando com $descricaoFpg";
                        ?>

                    </div>
                <?php } ?>

                <div class="quantity-controls d-flex justify-content-start align-items-center mb-3 mt-3">
                    <select id="qtd_prd" class="form-select">
                        <?php for ($i = 1; $i <= $estoque; $i++) { ?>
                            <option value="<?= $i; ?>"><?= $i; ?></option>
                        <?php } ?>
                    </select>
                    <button type="button" class="btn rounded add-cart  mx-3" style="width: 100%;" onclick="updateCart(this,<?= $id; ?>,
                        document.getElementById('qtd_prd').value,'adicionar' )"> <i class="bi bi-cart-plus mx-2"></i>
                        <span class="span-cart-<?= $id; ?>">Adicionar</span></button>
                </div>

                <div class="section-cep"></div>


                <div class="d-flex justify-content-between product-details-footer text-muted  mb-2">
                    <div style="cursor: pointer;" class="<?= $class_fav; ?> add-fav-procts-details" <?php if ($class_fav == "") { ?> onclick="updateFavorite(this,<?= $id; ?>)" <?php } ?>>
                        <?= $text_fav; ?>
                    </div>
                    <div>Compartilhar:
                        <a class="text-dark" href="https://api.whatsapp.com/send?text=<?php echo $link_compartilhar; ?>">
                            <i class="bi bi-whatsapp"></i></a>
                        <a class="text-dark" href="https://facebook.com/sharer/sharer.php?u=<?php echo $link_compartilhar; ?>">
                            <i class="bi bi-facebook"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row gap-5 mb-5">
            <!-- Abas -->
            <div class="col-md-8">
                <ul class="nav nav-tabs mb-2" id="myTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="descricao-tab" data-bs-toggle="tab" href="#descricao" role="tab" aria-controls="descricao" aria-selected="true">Descrição</a>
                    </li>
                </ul>
                <div class="tab-content mt-2">
                    <!-- Aba de Serviço -->
                    <div class="tab-pane fade show active" id="descricao" role="tabpanel" aria-labelledby="servico-tab">
                        <?= $descricao_produto; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php if (($consultar_produtos_similares) and $qtd_prd_similares > 0) { ?>
            <div class="row">
                <div class="col-md">
                    <div class="d-flex justify-content-between mb-1">

                        <h4 clas>Produtos Similares</h4>
                        <!-- <p class="mb-0 text-body-tertiary fw-semibold"><?= $mensagem_grupo; ?></p> -->

                        <!-- <a href="">Ver tudo</a> -->
                    </div>

                    <div class="owl-carousel product-group-1">
                        <?php while ($linha = mysqli_fetch_assoc($consultar_produtos_similares)) { ?>
                            <?php include "../card-produto/modelo_1.php"; ?>
                        <?php }; ?>
                    </div>

                </div>
            </div>
        <?php };
    } else { ?>
        <div class="d-flex justify-content-around gap-3 d-flex align-items-center  
           p-3 border rounded-2 ui-search" style="max-width: 500px;">
            <div class="text-center"><i style="font-size: 2.8em;" class="bi bi-search"></i></div>
            <div class="">
                <h4>Produto não encontrado</h4>
                <a class="mb-1" href="./">Voltar para página inicial</a>

            </div>
        </div><?php }; ?>
</div>


<script src="public/js/containers/products_details/group.js"> </script>