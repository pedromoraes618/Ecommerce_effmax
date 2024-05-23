<div class="modal fade" id="modal_login" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5"> Login</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="login" class="">
                    <div class="container-sm">
                        <div class="row mb-2">
                            <div class="col-md  mb-2">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control " placeholder="seuemail@email.com" id="email" name="email">
                                <div class="feedback-email">

                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md ">
                                <label for="senha" class="form-label">Senha</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="senha" id="senha" placeholder="*****">
                                    <button class="btn btn-outline-secondary" type="button" id="visualizarSenha"><i class="bi bi-eye"></i></button>
                                    <div class="feedback-senha">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md">
                                <a href="#" id="forgotPassowrd" class="text-decoration-none text-dark">Esqueci meus dados de acesso</a>
                            </div>
                        </div>

                        <div id="warning"></div>
                        <div class="feedback-warning mb-2"></div>

                        <div class="d-grid mb-2 gap-2">
                            <button type="submit" class="btn btn-sm btn-dark rounded">Iniciar Sessão</button>
                        </div>

                        <div class="text-center">
                            <p>Não possui uma conta ainda? <a href="" class="criar-conta text-decoration-none" id="register">Criar uma conta</a> </p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="public/js/containers/auth/login.js"></script>
<script src="helps/funcao.js"></script>