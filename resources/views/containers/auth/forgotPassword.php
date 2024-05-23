<div class="modal fade" id="modal_forgot_password" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">Redefinir senha</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="forgotPassword" class="">
                    <div class="container-sm">
                        <div class="row mb-2">
                            <div class="col-md  mb-2">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control" placeholder="seuemail@email.com" id="email" name="email">
                                <div class="feedback-email"></div>
                            </div>
                        </div>

                        <div id="warning"></div>
                        <div class="feedback-warning mb-2"></div>
                        <span class="span-loader"></span>

                        <div class="d-grid mb-2 gap-2">
                            <button type="submit" id="next" class="btn btn-sm btn-dark rounded">Próximo</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="public/js/containers/auth/forgotPassword.js"></script>