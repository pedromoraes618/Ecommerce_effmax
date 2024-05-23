const params = new URLSearchParams(window.location.search);
$(document).ready(function () {
    $(".span-loader").html('<div class="loader"></div>');

    $.ajax({
        type: 'GET',
        data: "page=inicial&containers=baner_2",
        url: "resources/views/containers/initial/carousel_information.php",
        success: function (result) {
            return $(".main .promo").html(result);
        },
    });

    $.ajax({
        type: 'GET',
        data: "page=checkout&layouts=topo",
        url: "resources/views/layouts/header.php",
        success: function (result) {
            return $(".main .header").html(result);
        },
    });

    $.ajax({
        type: 'GET',
        data: "page=checkout&layouts=breadcrumb&" + params,
        url: "resources/views/layouts/breadcrumb.php",
        success: function (result) {
            return $(".main .breadcrumb").html(result);
        },
    });

    $.ajax({
        type: 'GET',
        data: "page=checkout&containers=checkout&" + params,
        url: "resources/views/containers/checkout/checkout.php",
        success: function (result) {
            return $(".main .conteudo-1").html(result);
        },
    });

    $.ajax({
        type: 'GET',
        data: "page=checkout&layouts=footer",
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

