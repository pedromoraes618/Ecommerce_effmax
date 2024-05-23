<?php include "../../../../app/Http/Controllers/User.php"; ?>


<div class="container-lg user  mt-3 p-0">
    <!-- Abas -->
    <ul class="nav nav-tabs" id="myTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link <?= $page == "profile" ? 'active' : ''; ?> " id="profile-tab" data-bs-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="true">Perfil</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link <?= $page == "order" ? 'active' : ''; ?> " id="order-tab" data-bs-toggle="tab" href="#order" role="tab" aria-controls="order" aria-selected="false">Pedidos</a>
        </li>
    </ul>

    <!-- Conteúdo das Abas -->
    <div class="tab-content mt-2">
        <!-- Aba de Serviço -->
        <div class="tab-pane fade show <?= $page == "profile" ? 'active show' : ''; ?> " id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <div class="card border-0">
                <div class="card-body p-0">
                    <div class="row ">
                        <div class="col-md-3 ">
                            <div class="p-3" style="background-color:#F8F9FA">
                                <div class="d-flex  flex-column justify-content-center">
                                    <div class="text-center"><i class="bi bi-person-circle " style="font-size:70px"></i></div>
                                    <div class="text-center ">
                                        <p class="fw-semibold "><?= $nome_usuario; ?></p>
                                    </div>
                                </div>
                                <ul class="list-group mb-2">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Data de Cadastro
                                        <span class=""><?= ($data_cadastro); ?></span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Itens Favoritos
                                        <span class=""><?= auth('')['qtd_fav'] ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Itens no Carrinho
                                        <span class=""><?= auth('')['qtd_cart'] ?></span>
                                    </li>
                                </ul>
                                <div class="d-grid gap-2">
                                    <a type="button" class="btn btn-sm btn-dark btn-alter-password" href="#">Alterar Senha</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="p-3" style="background-color:#F8F9FA">
                                <div class="rounded p-3 mb-2">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="fw-bold mb-3">Informações pessoais</h6>
                                        <div class="btn border btn-personal-information">Editar <i class="bi bi-pencil-square"></i></div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md">
                                            <label for="nome" class="m-0 form-label">Nome</label>
                                            <p class="m-0 "><?= $nome_usuario; ?></p>
                                        </div>
                                        <div class="col-md">
                                            <label for="nome" class="m-0 form-label">Cpf/Cnpj</label>
                                            <p class="m-0"><?= $cpf_cnpj_usuario; ?></p>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md">
                                            <label for="nome" class="m-0 form-label">Email</label>
                                            <p class="m-0 "><?= $email_usuario; ?></p>
                                        </div>
                                        <div class="col-md">
                                            <label for="nome" class="m-0 form-label">Telefone</label>
                                            <p class="m-0 "><?= formatarNumeroTelefone($telefone_usuario); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="  rounded p-3 mb-2">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="fw-bold mb-3">Endereço</h6>
                                        <div class="btn border  btn-address">Editar <i class="bi bi-pencil-square"></i></div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md">
                                            <label for="nome" class="m-0 form-label">Rua</label>
                                            <p class="m-0"><?= $endereco_usuario . " - " . $numero_usuario; ?></p>
                                        </div>
                                        <div class="col-md">
                                            <label for="nome" class="m-0 form-label">Bairro</label>
                                            <p class="m-0 "><?= $bairro_usuario; ?></p>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md">
                                            <label for="nome" class="m-0 form-label">Cep</label>
                                            <p class="m-0"><?= $cep_usuario; ?></p>
                                        </div>
                                        <div class="col-md">
                                            <label for="nome" class="m-0 form-label">Cidade</label>
                                            <p class="m-0"><?= $cidade_usuario; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade <?= $page == "order" ? 'active show' : ''; ?> " id="order" role="tabpanel" aria-labelledby="order-tab">
            <div class="card border-0 mb-2">
                <div class="card-body p-0">
                    <div id="order-perfil">

                    </div>
                </div>
            </div>
            <div class="card border-0" style="background-color:#F8F9FA">
                <div class="card-body p-3">
                    <h6 class="fw-semibold mb-3">Histórico de Pedidos</h6>
                    <div class="row mb-2">
                        <div class="col-md-auto  mb-2">
                            <label for="data">Data do Pedido</label>
                            <input type="date" id="data" class="form-control">
                        </div>
                    </div>
                    <div class="table_history_table"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="public/js/containers/user/user.js"> </script>