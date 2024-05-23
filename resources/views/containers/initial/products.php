<?php include "../../../../app/Http/Controllers/Initial.php"; ?>
<?php
if ($secao_novidades == "S") {
    if ($consulta_produtos_novidades) {
        if ($qtd_prd_lancamento > 4) { ?>

            <div class="p-0 mb-1 mb-md-4 ">
                <div class="d-flex justify-content-between">
                    <div class="mb-2">
                        <h4 class="fw-semibold"><?= $titulo_secao_novidade; ?></h4>
                    </div>
                    <!-- <div><a class="fw-bolder text-decoration-none" href="?products-filter&news=true">Veja mais <i class="bi bi-chevron-right"></i></a></div> -->
                </div>

                <div class="row mb-md-3 row-cols-2 row-cols-sm-4 row-cols-md-5 g-3">
                    <?php while ($linha = mysqli_fetch_assoc($consulta_produtos_novidades)) { ?>
                        <div class="mb-3">
                            <?php include "../card-produto/modelo_1.php"; ?>
                        </div>
                    <?php } ?>
                </div>
                <div class="d-flex justify-content-center mb-2 mt-2">
                    <a href="?products-filter&news=true" class="btn btn-view-more rounded-0 btn-danger border-1">Ver mais</a>
                </div>
            </div>
        <?php
        };
    } else { ?>
        <div class='img-erro'>
            <img width='200' src='public/imagens/erro/503.svg'>
            <span class='text'>Seção em Manutenção</span>
        </div>
<?php }
}; ?>

<?php
if ($secao_desconto == "S") {
    if ($consulta_produtos_desconto) {
        if ($qtd_prd_desconto > 4) { ?>
            <div class="p-0 mb-2 mb-md-4 rounded session-desconto text-dark">
                <div class="d-flex justify-content-between">
                    <div class="mb-2">
                        <h4 class="fw-semibold "><?= $titulo_secao_desconto; ?></h4>
                    </div>
                    <div><a class="fs-6 text-decoration-none text-dark" href="?products-filter&discount=true">Veja mais <i class="bi bi-chevron-right"></i></a></div>
                </div>
                <div class="owl-carousel ofertas ">
                    <?php while ($linha = mysqli_fetch_assoc($consulta_produtos_desconto)) { ?>
                        <div class="mb-3">
                            <?php include "../card-produto/modelo_1.php"; ?>
                        </div>
                    <?php } ?>
                </div>
                <!-- <div class="d-flex justify-content-center mb-2 mt-2">
                <a  href="?products-filter&discount=true" class="btn btn-view-more rounded btn-danger border-0">Ver mais</a>
            </div> -->
            </div>
        <?php
        };
    } else { ?>
        <div class='img-erro'>
            <img width='200' src='public/imagens/erro/503.svg'>
            <span class='text'>Seção em Manutenção</span>
        </div>
<?php }
}; ?>

<?php if ($consulta_produtos_catalogo and $qtd_prd_catologo > 0) : ?>
    <div class="p-0 mb-1 mb-md-4">
        <div class="d-flex justify-content-between">
            <div class="mb-2">
                <h4 class="fw-semibold"><?= $titulo_secao_catalogo; ?></h4>
            </div>
            <!-- <div><a class="fw-bolder text-decoration-none">Veja mais <i class="bi bi-chevron-right"></i></a></div> -->
        </div>
        <div class="row mb-md-3  row-cols-2 row-cols-sm-4  row-cols-md-5 g-3">
            <?php while ($linha = mysqli_fetch_assoc($consulta_produtos_catalogo)) {
            ?>
                <div class="mb-3">
                    <?php include "../card-produto/modelo_1.php"; ?>
                </div>
            <?php } ?>
        </div>
        <div class="d-flex justify-content-center mb-2 mt-2">
            <a href="?products-filter&catalog=true" class="btn btn-view-more rounded-0 btn-danger ">Ver mais</a>
        </div>
    </div>
<?php else : ?>
    <div class='img-erro'>
        <img width='200' src='public/imagens/erro/503.svg'>
        <span class='text'>Seção em Manutenção</span>
    </div>
<?php endif; ?>


<script src="public/js/containers/initial.js"> </script>