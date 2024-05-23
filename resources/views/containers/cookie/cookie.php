<?php
include "../../../../app/Http/Controllers/Cookie.php";
?>
<?php if ($cookie != 1) { ?>
    <div class="box-cookies hide shadow ">

        <div class="container">
            <p class="msg-cookies">
                Nosso site utiliza cookies próprios e de terceiros
                para proporcionar uma experiência personalizada ao usuário.
                Ao clicar em concordar, você concorda com nossa <strong>
                    <a href="?company&rules=privacypolicy">política de privacidade</a></strong>.
            </p>
            <button type="button" class="btn-cookies accept-btn border-0 p-1 rounded">Aceitar</button>
        </div>
    </div>
<?php } ?>

<script>
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
</script>