<div class="modal fade" id="modal_reset_password" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">Defina sua nova senha</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="resetPassword" class="">
                    <div class="container-sm">
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

                        <div id="warning"></div>
                        <div class="feedback-warning mb-2"></div>
                        <span class="span-loader"></span>

                        <div class="d-grid mb-2 gap-2">
                            <button type="submit" id="next" class="btn btn-sm btn-dark 
                            rounded">Continuar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="public/js/containers/auth/resetPassword.js"></script>
<script src="helps/funcao.js"></script>