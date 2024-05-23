<?php include "../../../../app/Http/Controllers/User.php"; ?>


<div class="modal fade" id="modal_perfil" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5"> <?= ($component == "profile") ? 'Minhas informações' : 'Endereço'; ?></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="user" class="">
                    <div class="container-sm">
                        <?php if ($component == "profile") { ?>
                            <div class="row mb-2">
                                <div class="col-12  mb-2">
                                    <label for="Nome" class="form-label">Nome</label>
                                    <input type="text" class="form-control " placeholder="Nome completo *" id="nome" name="nome">
                                    <div class="feedback-nome">
                                    </div>
                                </div>
                                <div class="col-12  mb-2">
                                    <label for="cpfcnpj" class="form-label">Cpf/Cpnj</label>
                                    <input type="text" class="form-control" id="cpfcnpj" name="cpfcnpj" placeholder="Cpf ou cnpj *" value="">
                                    <div class="feedback-cpfcnpj">
                                    </div>
                                </div>
                                <div class="col-12  mb-2">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="text" class="form-control " placeholder="seuemail@email.com" id="email" name="email">
                                    <div class="feedback-email">
                                    </div>
                                </div>
                                <div class="col-12  mb-2">
                                    <label for="telefone" class="form-label">Telefone</label>
                                    <input type="telefone" class="form-control" name="telefone" id="telefone" placeholder="Telefone *">
                                    <div class="feedback-telefone">
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if ($component == "address") { ?>
                            <div class="row mb-2">
                                <div class="col-12  mb-3">
                                    <label for="cep" class="form-label">Cep *</label>
                                    <input type="text" class="form-control" id="cep" name="cep" autocomplete="off" placeholder="Cep *">
                                    <div class="feedback-cep">
                                    </div>
                                </div>
                                <div class="col-8  mb-3">
                                    <label for="endereco" class="form-label">Endereço</label>
                                    <input type="text" class="form-control" id="endereco" name="endereco" placeholder="Endereco" value="">
                                    <div class="feedback-endereco">
                                    </div>
                                </div>
                                <div class="col-4  mb-3">
                                    <label for="numero" class="form-label">Número</label>
                                    <input type="text" class="form-control " placeholder="Numero" id="numero" name="numero">
                                    <div class="feedback-numero">
                                    </div>
                                </div>
                                <div class="col-12  mb-3">
                                    <label for="bairro" class="form-label">Bairro</label>
                                    <input type="text" class="form-control" name="bairro" id="bairro" placeholder="Bairro ">
                                    <div class="feedback-bairro">
                                    </div>
                                </div>
                                <div class="col-12  mb-3">
                                    <label for="bairro" class="form-label">Cidade</label>
                                    <input type="text" class="form-control" name="cidade" id="cidade" placeholder="Cidade ">
                                    <div class="feedback-bairro">
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if ($component == "password") { ?>
                            <div class="row mb-2">
                                <div class="col-12  mb-3">
                                    <label for="senha" class="form-label">Nova senha *</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="senha" name="senha" autocomplete="off" aria-describedby="imgSenha">
                                        <button class="btn btn-outline-secondary" type="button" id="visualizarSenha"><i class="bi bi-eye"></i></button>
                                        <div class="feedback-senha">
                                        </div>
                                    </div>
                                    <!-- <div id="imgSenha" class="form-text">A senha deve conter números, letras maiúsculas e minúsculas.</div> -->
                                </div>

                                <div class="col-12  mb-3">
                                    <label for="confirmar_senha" class="form-label">Confirmar senha *</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="confirmar_senha" id="confirmar_senha">
                                        <button class="btn btn-outline-secondary" type="button" id="visualizarSenhaConfirmar"><i class="bi bi-eye"></i></button>
                                        <div class="feedback-confirmar_senha">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <div id="warning"></div>
                        <div class="feedback-warning mb-2"></div>

                        <div class="d-grid gap-2">
                            <button type="submit" id="update" class="btn btn-sm  btn-dark">Alterar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="public/js/containers/user/perfil.js"></script>
<script src="helps/funcao.js"></script>