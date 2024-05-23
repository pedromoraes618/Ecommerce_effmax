const params = new URLSearchParams(window.location.search);
const rules = new URLSearchParams(window.location.search).get('rules');//pegar valor do parametro da url

$(document).ready(function () {
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
        data: "page=products_filter&layouts=breadcrumb&" + params,
        url: "resources/views/layouts/breadcrumb.php",
        success: function (result) {
            return $(".main .breadcrumb").html(result);
        },
    });

    $.ajax({
        type: 'GET',
        data: "page=company&rules=" + rules,
        url: "resources/views/containers/company/rules.php",
        success: function (result) {
            return $(".main .conteudo-1").html(result);
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

    $(window).on("load", function () {
        $(".span-loader").fadeOut(); // Esconde o loader
    });

    ScrollReveal().reveal('.conteudo-1', { delay: 400 });
    ScrollReveal().reveal('.conteudo-2', { delay: 400 });


});
