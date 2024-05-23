<?php include "../../../../app/Http/Controllers/ProductsFilter.php";
if ($consultar_produtos && $qtd_prd > 0) {

?>
    <div class="d-flex mb-3">
        <div>
            <h4 class="mb-0 fw-mediun"><?= $title_session; ?></h4>
            <p class="text-muted mb-0" style="font-size: 0.8em;"> Produtos (<?= $qtd_prd; ?>)</p>
        </div>
    </div>

    <div class="row row-cols-2 row-cols-md-4 g-3">
        <?php while ($linha = mysqli_fetch_assoc($consultar_produtos)) { ?>
            <div class="mb-3">
                <?php include "../card-produto/modelo_1.php"; ?>
            </div>
        <?php }; ?>
    </div>
    <?php if ($total_pages > 1) { ?>
        <div class=" mt-5 d-flex justify-content-center ">
            <nav aria-label="Page navigation example">
                <ul class="pagination">
                    <li <?php if ($current_page > 1) { ?>onclick="product(<?= $current_page - 1; ?>)" <?php }; ?> class="page-item <?php echo $current_page == 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                        <li onclick="product(<?= $i; ?>)" class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                            <a href="#" class="page-link"><?php echo $i; ?></a>
                        </li>
                    <?php }; ?>
                    <li <?php if ($current_page != $total_pages) { ?> onclick="product(<?= $current_page + 1; ?>)" <?php } ?> class="page-item <?php echo $current_page == $total_pages ? 'disabled' : ''; ?>">

                        <a class="page-link" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php
    };
} else {
    ?>
    <div class="d-flex justify-content-around gap-3 d-flex align-items-center  
        p-3 border rounded-2 ui-search">
        <div class="text-center"><i style="font-size: 2.8em;" class="bi bi-search"></i></div>
        <div>
            <h4>Não há anúncios que correspondam à sua busca</h4>
            <p class="mb-1">Revise a ortografia da palavra.</p>
            <p class="mb-1">Utilize palavras mais genéricas.</p>
            <p class="mb-1">Navegue pelas categorias para encontrar um produto semelhante.</p>
        </div>
    </div>
<?php
};
?>