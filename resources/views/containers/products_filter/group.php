<?php include "../../../../app/Http/Controllers/ProductsFilter.php"; ?>
<div class="product-filter-container mt-2 mt-md-5">
    <div class="row">
        <div class="col-md-2  ">
            <div class="productFilterColumn">
                <div class="list-unstyled mb-3">
                    <h5 class="d-none fw-semibold d-sm-block d-md-block">Filtro</h5>
                    <button class="btn btn-sm btn-phoenix-secondary text-body-tertiary d-lg-none btn-filters-mobile" data-phoenix-toggle="offcanvas" data-phoenix-target="#productFilterColumn">
                        <i class="bi bi-funnel-fill"></i> Filtro</button>
                </div>

                <div class="filters-dropdown">
                    <div class="card border-0">
                        <div class="card-body p-0">
                            <div class="row mb-3">
                                <label class="collapse-indicator-order" onclick="rotateIcon(this)" data-bs-toggle="collapse" href="#collapseOrder" role="button" aria-expanded="true" aria-controls="collapseOrder">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span for="" class="form-label">Ordenar por</span>
                                        <i class="bi bi-chevron-up rotate-icon"></i>
                                    </div>
                                </label>
                                <div class="collapse show" id="collapseOrder">
                                    <div class="d-flex justify-content-between">
                                        <select onchange="product(null)" name="order" class="form-select" id="order">
                                            <option value="">Selecione..</option>
                                            <!-- <option value="mais_vendidos">Mais Vendidos</option>
                                            <option value="menos_vendidos">Menos Vendidos</option> -->
                                            <option value="menor_maior_preco">Preço: Menor ao maior</option>
                                            <option value="maior_menor_preco">Preço: Maior ao menor</option>
                                            <option value="a_z">A-Z</option>
                                            <option value="z_a">Z-A</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- Adicione mais filtros conforme necessário -->
                            <div class="row  mb-3">
                                <label class="collapse-indicator-preco d-block" onclick="rotateIcon(this)" data-bs-toggle="collapse" href="#collapsePriceRange" role="button" aria-expanded="true" aria-controls="collapsePriceRange">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span for="" class="form-label">Preço</span>
                                        <i class="bi bi-chevron-up rotate-icon"></i>
                                    </div>
                                </label>

                                <div class="collapse show" id="collapsePriceRange">
                                    <div class="d-flex justify-content-between">
                                        <div class="input-group me-2">
                                            <input class="form-control" name="min_preco" id="min_preco" type="text" aria-label="First name" placeholder="Min">
                                            <input class="form-control" name="max_preco" id="max_preco" type="text" aria-label="Last name" placeholder="Max">
                                        </div><button class="btn btn-outline-secondary border px-3 border-1 consultar_preco" onclick="product(null)" type=" button">Ir</button>
                                    </div>
                                </div>
                            </div>
                            <div class="row  mb-3">
                                <label class="collapse-indicator-preco d-block" onclick="rotateIcon(this)" data-bs-toggle="collapse" href="#collapseCondition" role="button" aria-expanded="true" aria-controls="collapsePriceRange">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span for="" class="form-label">Condição</span>
                                        <i class="bi bi-chevron-up rotate-icon"></i>
                                    </div>
                                </label>

                                <div class="collapse show" id="collapseCondition">
                                    <div class="d-flex justify-content-between ">
                                        <div class="form-check form-check-inline p-0">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="condicao_novo" checked onclick="product(null)" id="condicao_novo" value="NOVO">
                                                <label class="form-check-label" for="condicao_novo">
                                                    Novo
                                                </label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="condicao_usado" checked onclick="product(null)" id="condicao_usado" value="USADO">
                                                <label class="form-check-label" for="condicao_usado">
                                                    Usado
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row  mb-3">
                                <label class="collapse-indicator-preco d-block" onclick="rotateIcon(this)" data-bs-toggle="collapse" href="#collapsePromotion" role="button" aria-expanded="true" aria-controls="collapsePromotion">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span for="" class="form-label">Promoção</span>
                                        <i class="bi bi-chevron-up rotate-icon"></i>
                                    </div>
                                </label>

                                <div class="collapse show" id="collapsePromotion">
                                    <div class="form-switch form-check-inline p-0">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" name="promocao" id="promocao" onclick="product(null)">
                                            <label class="form-check-label" for="promocao">Ativo</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row  mb-3">
                                <label class="collapse-indicator-preco d-block" onclick="rotateIcon(this)" data-bs-toggle="collapse" href="#collapseFormat" role="button" aria-expanded="true" aria-controls="collapseFormat">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span for="" class="form-label">Formato</span>
                                        <i class="bi bi-chevron-up rotate-icon"></i>
                                    </div>
                                </label>

                                <div class="collapse show" id="collapseFormat">
                                    <div class="form-check form-check-inline p-0">
                                        <?php
                                        $resultados = consulta_linhas_tb_query($conecta, "SELECT * from tb_unidade_medida");
                                        if ($resultados) {
                                            foreach ($resultados as $linha) {
                                                $id = $linha['cl_id'];
                                                $descricao = utf8_encode($linha['cl_descricao']);
                                        ?>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" name="unidade<?= $id; ?>" checked onclick="product(null)" id="unidade<?= $id; ?>" value="<?= $id; ?>">
                                                    <label class="form-check-label" for="unidade<?= $id; ?>">
                                                        <?= $descricao; ?>
                                                    </label>
                                                </div>
                                        <?php }
                                        } ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md   group-products">
        </div>
    </div>
</div>


<script src="public/js/containers/products_filter/group.js"> </script>