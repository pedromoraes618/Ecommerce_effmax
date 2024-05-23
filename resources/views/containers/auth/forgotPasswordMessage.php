<div class="modal fade" id="modal_forgot_password_message" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">Redefinir senha</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-sm">
                    <div class="border rounded  shadow mb-3 p-3" style="text-align: center;">
                        <i class="bi bi-envelope-arrow-up" style="font-size: 1.7em;text-align:center"></i>
                        <div>
                            Um email de verificação foi enviado para este endereço de email <?= $_GET['email']; ?>.
                            Por favor verifique
                        </div>
                    </div>
                    <div class="d-grid mb-2 gap-2">
                        <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-sm btn-dark rounded">OK</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

