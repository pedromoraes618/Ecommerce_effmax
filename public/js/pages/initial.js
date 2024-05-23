
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
        data: "page=inicial&layouts=topo",
        url: "resources/views/layouts/header.php",
        success: function (result) {
            return $(".main .header").html(result);
        },
    });
    $.ajax({
        type: 'GET',
        data: "page=inicial&layouts=categories",
        url: "resources/views/layouts/categories.php",
        success: function (result) {
            return $(".main .nav-menu").html(result);
        },
    });

    $.ajax({
        type: 'GET',
        data: "page=inicial&containers=baner_1",
        url: "resources/views/containers/baner_1.php",
        success: function (result) {
            return $(".main .conteudo .conteudo-1").html(result);
        },
    });
    // $.ajax({
    //     type: 'GET',
    //     data: "page=inicial&containers=information",
    //     url: "resources/views/containers/initial/information.php",
    //     success: function (result) {
    //         return $(".main .conteudo .conteudo-4").html(result);
    //     },
    // });
    $.ajax({
        type: 'GET',
        data: "page=inicial&containers=products",
        url: "resources/views/containers/initial/products.php",
        success: function (result) {
            return $(".main .conteudo .conteudo-2").html(result);
        },
    });
    $.ajax({
        type: 'GET',
        data: "page=inicial&containers=member",
        url: "resources/views/containers/initial/member.php",
        success: function (result) {
            return $(".main .conteudo .conteudo-3").html(result);
        },
    });
    $.ajax({
        type: 'GET',
        data: "page=inicial&layouts=footer",
        url: "resources/views/layouts/footer.php",
        success: function (result) {
            return $(".main .footer").html(result);
        },
    });
    // $.ajax({
    //     type: 'GET',
    //     data: "page=inicial&containers=cookie",
    //     url: "resources/views/containers/cookie/cookie.php",
    //     success: function (result) {
    //         return $(".main .cookie").html(result);
    //     },
    // });

    // Evento para esconder o loader após o carregamento completo da página
    $(window).on("load", function () {
        $(".span-loader").fadeOut(); // Esconde o loader
    });

    ScrollReveal().reveal('.conteudo-1', { delay: 400 });
    ScrollReveal().reveal('.conteudo-2', { delay: 400 });

})






// ScrollReveal().reveal('.conteudo-1', slideUp);