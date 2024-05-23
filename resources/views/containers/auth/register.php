<div class="modal fade" id="modal_register" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5"> Registre-se</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="register" class="">
                    <div class="container-sm">
                        <div class="row mb-2">
                            <div class="col-md mb-2">
                                <label for="nome" class="form-label">Nome *</label>
                                <input type="text" class="form-control" id="nome" name="nome" placeholder="Seu nome completo">
                                <div class="feedback-nome"></div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md mb-2">
                                <label for="email" class="form-label">Email *</label>
                                <input type="text" class="form-control" id="email" name="email" placeholder="seuemail@email.com">
                                <div class="feedback-email"></div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md">
                                <label for="senha" class="form-label">Senha *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="senha" name="senha" placeholder="*****">
                                    <button class="btn btn-outline-secondary" type="button" id="visualizarSenha"><i class="bi bi-eye"></i></button>
                                    <div class="feedback-senha"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md">
                                <label for="confirmar_senha" class="form-label">Confirmar Senha *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" placeholder="*****">
                                    <button class="btn btn-outline-secondary" type="button" id="visualizarSenhaConfirmar"><i class="bi bi-eye"></i></button>
                                    <div class="feedback-confirmar_senha"></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="aceita_termos" name="aceita_termos">
                            <label class="form-check-label" for="aceita_termos">
                                Aceito os termos e condições <a href="?company&rules=termsconditions">Veja</a>
                            </label>
                            <div class="feedback-aceita_termos"></div>
                        </div>


                        <div class="d-grid mb-2 gap-2">
                            <button type="submit" class="btn btn-sm btn-dark rounded" id="next">Cadastrar</button>
                        </div>
                        <div class="text-center">
                            <p>Já possui uma conta? <a href="#" class="iniciar-sessao text-decoration-none" id="login" data-bs-dismiss="modal">Faça login</a></p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="public/js/containers/auth/register.js"></script>
<script src="helps/funcao.js"></script>