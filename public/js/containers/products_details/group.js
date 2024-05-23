

// Adiciona ação ao botão de incremento
$(".increment").on("click", function () {
    var currentValue = parseInt($("#qtd_prd").val());
    $("#qtd_prd").val(currentValue + 1);
});

// Adiciona ação ao botão de decremento
$(".decrement").on("click", function () {
    var currentValue = parseInt($("#qtd_prd").val());
    if (currentValue > 1) {
        $("#qtd_prd").val(currentValue - 1);
    }
});

$(".open-favorite").click(function () {
    offcanvasFavorite()
})

$(document).ready(function () {

    $('.left-img').click(function () {
        // Obtenha o src da imagem clicada
        var src = $(this).find('img').attr('src');

        // Remova a classe "new-image" e adicione novamente para reiniciar a animação
        $('.main-image img').removeClass("new-image").delay(10).queue(function (next) {
            $(this).addClass("new-image");
            next();
        });

        // Substitua o src da imagem principal pelo src da imagem clicada
        $('.main-image img').attr('src', src);
    });


    $('.owl-carousel.product-group-1').owlCarousel({
        items: 1, // Número de itens visíveis no carrossel
        loop: true,
        margin: 5, // Espaçamento entre os cards
        nav: false, // Mostrar setas de navegação
        dots: false, // Ocultar pontos de navegação
        autoplay: true,
        autoplayTimeout: 6000,
        responsive: {
            0: {
                items: 1.7 // Número de itens visíveis em telas menores
            },
            740: {
                items: 2.9 // Número de itens visíveis em telas médias
            },
            992: {
                items: 5 // Número de itens visíveis em telas maiores
            }
        }
    })


    /*frete*/
    sessaoCep()

})

