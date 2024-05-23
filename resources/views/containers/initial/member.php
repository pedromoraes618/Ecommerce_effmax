<?php include "../../../../app/Http/Controllers/Initial.php"; ?>
<?php if (auth('') === false and $sessao_inscreva_se == "S") { ?>
    <div class="container-lg member">
        <div class="row group-member justify-content-center align-items-center">
            <div class="col-md-auto"><img width="220" src="../../../../<?= $empresa ?>/img/ecommerce/assets/persona_login.svg" alt=""></div>
            <div class="col-md-4 ">
                <h5 class="text-body-highlight mb-0">Quer ter a melhor <span class="fw-semibold">experiência do cliente?</span></h5>
                <div class="fw-medium"><?= $span_componente_inscrever; ?></div>
                <button type="button" class="btn btn-danger register 
                fw-semibold rounded text-center text-md-start border-0 btn-register-member">Inscreva-se</button>
            </div>
        </div>
    </div>
<?php } ?>

<script src="public/js/containers/initial/member.js"> </script>