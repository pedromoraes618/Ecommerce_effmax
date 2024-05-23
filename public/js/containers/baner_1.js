$(document).ready(function () {
    $('.owl-carousel.baner-1').owlCarousel({
        items: 1, // Número de itens visíveis no carrossel
        loop: true,
        margin: 0, // Espaçamento entre os cards
        nav: false, // Mostrar setas de navegação
        dots: false, // Ocultar pontos de navegação
        //   navText: ['<i class="bi bi-chevron-left"></i>', '<i class="bi bi-chevron-right"></i>'], // Ícones das setas de navegação (ajuste os caminhos conforme necessário)
        autoplay: true,
        autoplayTimeout: 7000,
        merge:true,
        responsive:{
         
            0: {
                mergeFit:false
            },
            740: {
                mergeFit:false
            },
            992: {
                mergeFit:true
            }
        }
    });
});