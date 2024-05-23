const params = new URLSearchParams(window.location.search);
$(document).ready(function () {
    $(".span-loader").html('<div class="loader"></div>');


    $.ajax({
        type: 'GET',
        data: "page=confirm-order&layouts=topo",
        url: "resources/views/layouts/header_2.php",
        success: function (result) {
            return $(".main .header").html(result);
        },
    });

    $.ajax({
        type: 'GET',
        data: "page=confirm-order&layouts=breadcrumb&" + params,
        url: "resources/views/layouts/breadcrumb.php",
        success: function (result) {
            return $(".main .breadcrumb").html(result);
        },
    });

    $.ajax({
        type: 'GET',
        data: "page=user&containers=user&" + params,
        url: "resources/views/containers/user/user.php",
        success: function (result) {
            return $(".main .conteudo-1").html(result);
        },
    });

    $.ajax({
        type: 'GET',
        data: "page=confirm-order&layouts=footer",
        url: "resources/views/layouts/footer.php",
        success: function (result) {
            return $(".main .footer").html(result);
        },
    });
    $(window).on("load", function () {
        $(".span-loader").fadeOut(); // Esconde o loader
    });
    ScrollReveal().reveal('.conteudo-1', { delay: 400 });
    ScrollReveal().reveal('.conteudo-2', { delay: 400 });

})