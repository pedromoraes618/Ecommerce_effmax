<?php include "../../../app/Http/Controllers/Initial.php"; ?>

<div class="container-lg mt-5 pt-5">
    <div class="row justify-content-between gy-4">
        <div class="col-12 col-lg-4">
            <div class="d-flex align-items-center mb-3">
                <a href="./"><img src="<?= $diretorio_logo . "?" . time(); ?>" width="90" alt="logo da Brisa"></a>
            </div>
            <div class=" mb-1 fw-medium lh-sm fs-9">
                <?= $texto_footer; ?>
            </div>
        </div>
        <div class="col-6 col-md-auto">
            <p class="fw-medium mb-3 fs-6">Sobre a Empresa</p>
            <div class="d-flex flex-column">
                <a class=" fw-medium fs-9 mb-1" href="?company&rules=about">Sobre Nós</a>
                <a class=" fw-medium fs-9 mb-1" href="?company&rules=privacypolicy">Politica de Privacidades</a>
                <a class=" fw-medium fs-9 mb-1" href="?company&rules=termsconditions">Termos &amp; Condições</a>
                <a class=" fw-medium fs-9 mb-1" href="?company&rules=devolution">Política de devolução</a>

            </div>
        </div>
        <div class="col-6 col-md-auto">
            <p class="fw-medium mb-3 fs-6">Nossas Redes</p>
            <div class="d-flex flex-column">
                <?php if (!empty($whatsap)) : ?>
                    <a href="https://api.whatsapp.com/send?phone=<?= $whatsap; ?>" target="_blank" class=" fw-medium fs-9 mb-1">
                        <i class="bi bi-whatsapp"></i>
                        Whatsapp
                    </a>
                <?php endif; ?>
                <?php if (!empty($whatsap)) : ?>
                    <a href="<?= $facebook; ?>" title="facebook" target="_blank" class=" fw-medium fs-9 mb-1">
                        <i class="bi bi-facebook"></i>
                        Facebook
                    <?php endif; ?>

                    <!-- <a href="https://www.tiktok.com/@brisadiscos?_t=8k9WXJ8O2kv&_r=1" target="_blank" class=" fw-semibold fs-9 mb-1">
                            <i class="fab fa-tiktok "></i>
                            Tiktok
                        </a> -->
                    <?php if (!empty($instagram)) : ?>
                        <a href="<?= $instagram; ?>" title="Instagram" target="_blank" class="  fw-medium fs-9 mb-1">
                            <i class="bi bi-instagram"></i>
                            Instagram
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($tiktok)) : ?>
                        <a href="<?= $tiktok; ?>" title="Tiktok" target="_blank" class="  fw-medium fs-9 mb-1">
                            <i class="bi bi-tiktok"></i>
                            Tiktok
                        </a>
                    <?php endif; ?>
            </div>
        </div>

        <div class="col-6 col-md-auto">
            <p class="fw-medium mb-3 fs-6">Dúvidas</p>
            <div class="d-flex flex-column">
                <?php if (!empty($email)) : ?>
                    <a class="fw-medium fs-9 mb-1"><i class="bi bi-envelope"></i> <?= $email; ?></a>
                <?php endif; ?>

                <?php if (!empty($telefone)) : ?>
                    <a class=" fw-medium fs-9 mb-1"><i class="bi bi-telephone"></i> <?= $telefone; ?></a>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-6 col-md-auto">
            <form id="newsletter">
                <p class="fw-medium mb-3 fs-6">Newsletter</p>
                <div class="d-flex flex-column">
                    <div class="row mb-2">
                        <div class="col-md">
                            <input type="text" class="form-control" name="nomeNewsletter" id="nomeNewsletter" placeholder="Nome">
                            <div class="feedback-nomeNewsletter">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md">
                            <input type="text" class="form-control" name="emailNewsletter" id="emailNewsletter" placeholder="Email">
                            <div class="feedback-emailNewsletter"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md">
                            <button type="submit" id="enviar_newsletter" class="btn btn-sm btn-danger">Enviar</button>
                        </div>
                    </div>
                    <div id="warning"></div>
                    <div class="feedback-warning mb-2"></div>
                </div>
            </form>
        </div>
    </div>
    <div class="d-flex flex-column flex-sm-row justify-content-between direitos_reservados   border-top">
        <p class="mb-1" style="font-size:0.8em;">© 2024 Brisadiscos, Inc. Todos os Direitos Resevados</p>
        <p class="mb-1" style="font-size:0.7em;color:gray;">Desenvolvido por Effmax.com.br</p>
    </div>
</div>

<div class="whatsapp-icon">
    <a href="https://api.whatsapp.com/send?phone=<?= $whatsap ?>" target="_blank">
        <i class="bi bi-whatsapp"></i>
    </a>
</div>

<?php if ($cookie != 1) { ?>
    <div class="box-cookies hide shadow ">

        <div class="container">
            <p class="msg-cookies">
                Nosso site utiliza cookies próprios e de terceiros
                para proporcionar uma experiência personalizada ao usuário.
                Ao clicar em concordar, você concorda com nossa <strong>
                    <a href="?company&rules=privacypolicy">política de privacidade</a></strong>.
            </p>
            <button type="button" class="btn-cookies px-3 accept-btn border-0 p-1 rounded">Aceitar</button>
        </div>
    </div>
<?php } ?>
<script src="public/js/layouts/footer.js"></script>
<script>
    const boxCookies = document.querySelector('.box-cookies');
    if (boxCookies) {
        if (!getCookie("cookiebrisaShop")) {
            document.querySelector(".box-cookies").classList.remove('hide');
        }

        const acceptCookies = () => {
            document.querySelector('.box-cookies').classList.add('hide');
            setCookie("cookiebrisaShop", "accept", 365); // 365 days expiration
        }

        const btnCookies = document.querySelector('.btn-cookies');
        btnCookies.addEventListener('click', acceptCookies);

        // Função para obter o valor de um cookie
        function getCookie(name) {
            const value = "; " + document.cookie;
            const parts = value.split("; " + name + "=");
            if (parts.length === 2) return parts.pop().split(";").shift();
        }

        // Função para definir um cookie
        function setCookie(name, value, days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            const expires = "expires=" + date.toUTCString();
            document.cookie = name + "=" + value + "; " + expires + "; path=/";
        }
    }
</script>