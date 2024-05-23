<?php include "../../../app/Http/Controllers/Initial.php"; ?>
<div class="navbar categorias ">
    <nav class="container-lg">
        <ul class="ul-1 p-0 m-0">
            <li><a class="titulo-mobile" href="#"><i class="bi bi-list fs-5 fw-semibold"></i><span class="fw-semibold"> Categorias</span></a>
                <ul class="ul-2">

                    <?php
                    $consulta = consulta_linhas_tb('tb_grupo_estoque', 'cl_grupo_venda', '1', '', '');
                    if ($consulta) :
                        foreach ($consulta as $linha) :
                            $id = $linha['cl_id'];
                            $descricao_categoria = utf8_encode($linha['cl_descricao']);

                    ?>
                            <li><a style="cursor:pointer" class="d-block d-flex justify-content-between">
                                    <span class="mx-1 fw-bold"><?= $descricao_categoria; ?></span>
                                    <!-- <span><i class="bi bi-chevron-down"></i></span> -->
                                </a>
                                <?php
                                $consulta = consulta_linhas_tb('tb_subgrupo_estoque', 'cl_delivery', 'SIM', 'cl_grupo_id', $id);
                                if (count($consulta) > 0) : ?>
                                    <ul class="rounded-1 border ul-3">
                                        <?php
                                        foreach ($consulta as $linha) :
                                            $id = $linha['cl_id'];
                                            $descricao_subcategoria = utf8_encode($linha['cl_descricao']);
                                        ?>
                                            <li><a href="?products-filter&categoria=<?= $descricao_categoria ?>&<?= $descricao_subcategoria ?>&subcategory=<?= $id; ?>" class="d-block fw-semibold"><?= $descricao_subcategoria; ?></a></li>
                                        <?php endforeach; ?>
                                        <!-- Adicione mais subcategorias conforme necessário -->
                                    </ul>
                                <?php endif; ?>
                            </li>
                    <?php
                        endforeach;
                    endif;
                    ?>

                </ul>
            </li>
        </ul>
    </nav>
</div>