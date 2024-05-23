<?php include "../../../app/Http/Controllers/Initial.php"; ?>

<div class="py-0">
    <div class="container-lg">
        <div class="d-flex align-items-center row d-flex justify-content-between">
            <div class="col-auto col-sm-auto ">
                <a class="text-decoration-none " id="logo" href="./">
                    <img src="<?= $diretorio_logo ?>" alt="logo" width="150">

                </a>
            </div>

            <div class="col-auto order-md-2">
                <ul class="list-inline mb-0 d-flex justify-content-center align-items-center">
                    <li class="list-inline-item position-relative">
                        <a href="#" class="text-dark open-favorite"><i class="bi bi-heart fs-5"></i></a>
                        <span style="font-size: 0.69em;" class="position-absolute top-0 start-100 
                        translate-middle badge rounded-pill bg-danger 
                        qtd-fav">
                            <?php
                            if (auth('') !== false) {
                                echo auth('')['qtd_fav'];
                            } else {
                                if (cookieAuth('') !== false) {
                                    echo cookieAuth('')['qtd_fav'];
                                }
                            }
                            ?>
                            <span class="visually-hidden">Favoritos</span>
                        </span>
                    </li>
                    <li class="list-inline-item position-relative"><a href="#" class="text-dark open-cart"><i class="bi bi-cart3 fs-5"></i></a>
                        <span style="font-size: 0.69em;" class="position-absolute top-0 start-100 translate-middle badge 
                        rounded-pill bg-danger 
                        qtd-cart">
                            <?php
                            if (auth('') !== false) {
                                echo auth('')['qtd_cart'];
                            } else {
                                echo (cookieAuth('')['qtd_cart']);
                            }
                            ?>
                            <span class="visually-hidden">Carrinho</span>
                        </span>
                    </li>
                    <li class="list-inline-item dropdown">
                        <a href="#" class="dropdown-toggle text-dark text-decoration-none" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person fs-4"></i> <?= (auth('') !== false) ? explode(' ', auth('')['nome'])[0] : ''; ?>
                        </a>
                        <ul class="dropdown-menu " aria-labelledby="userDropdown">
                            <?php
                            if (auth('') != false) :
                            ?>
                                <li><a class="dropdown-item" href="?user=profile">Perfil</a></li>
                                <li><a class="dropdown-item" href="?user=order">Pedidos</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="#" id="logout">Sair</a></li>
                            <?php else :
                            ?>
                                <li><a class="dropdown-item" id="login" href="#">Login</a></li>
                                <li><a class="dropdown-item" href="#" id="register">Registre-se</a></li>
                            <?php
                            endif; ?>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>


<div class="offcanvas-open"></div>

<script src="public/js/layouts/header.js"></script>
<script src="helps/funcao.js"></script>