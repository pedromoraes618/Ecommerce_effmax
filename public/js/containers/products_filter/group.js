var min_preco = document.getElementById("min_preco")
var max_preco = document.getElementById("max_preco")

$(document).ready(function () {
 

    // Função para fechar o menu quando clicar fora dele
    $(document).click(function (event) {
        if (!$(event.target).closest('.filters-dropdown, .btn-filters-mobile').length) {
            $('.filters-dropdown').removeClass('show');
        }
    });

    // Ouvinte de evento para abrir o menu ao clicar no botão
    $('.btn-filters-mobile').click(function () {
        $('.filters-dropdown').toggleClass('show');
    });
});
$('.rotate-icon').click(function () {
    $(this).toggleClass('rotate-up');
});

function rotateIcon(element) {
    // Verificar se a classe rotate-up está presente no ícone
    if ($(element).find('.rotate-icon').hasClass('rotate-up')) {
        // Remover a classe rotate-up para virar a seta para baixo
        $(element).find('.rotate-icon').removeClass('rotate-up');
    } else {
        // Adicionar a classe rotate-up para virar a seta para cima
        $(element).find('.rotate-icon').addClass('rotate-up');
    }
}

const products_filter = new URLSearchParams(window.location.search).get('products-filter'); // pesquisa pela descrição
const subcategory = new URLSearchParams(window.location.search).get('subcategory'); // pesquisa pela descrição
const news = new URLSearchParams(window.location.search).get('news'); // produto novos
const catalog = new URLSearchParams(window.location.search).get('catalog'); // catalogo
const discount = new URLSearchParams(window.location.search).get('discount'); // produto em desconto
const page = new URLSearchParams(window.location.search).get('page'); // produto em desconto

$.ajax({
    type: 'GET',
    data:
    {
        page: "products_filter",
        containers: "products",
        products_filter: products_filter,
        subcategory: subcategory,
        news: news,
        discount: discount,
        catalog: catalog,
        pagination: 1,
    },
    url: "resources/views/containers/products_filter/products.php",
    success: function (result) {
        $(".main .conteudo .conteudo-1 .group-products").html(result);
    },
});



function product(page) {

    var dados = {}; // Objeto para armazenar os dados a serem enviados na solicitação AJAX

    var dados = {
        page: "products_filter",
        containers: "products",
        products_filter: products_filter,
        subcategory: subcategory,
        news: news,
        discount: discount,     
        pagination: page,
        // Adicione outros dados conforme necessário
    };
    // Itera sobre cada elemento input
    $('input[type="text"]').each(function () {
        var idNome = $(this).attr('name'); // Obtém o nome do ID do input
        var valor = $(this).val(); // Obtém o valor do input
        dados[idNome] = valor; // Adiciona o nome do ID como chave e o valor como valor ao objeto dados
    });

    $('input[type="checkbox"]:checked').each(function () {
        var idNome = $(this).attr('name'); // Obtém o nome do ID do input
        var valor = $(this).val(); // Obtém o valor do input
        dados[idNome] = valor; // Adiciona o nome do ID como chave e o valor como valor ao objeto dados
    });
    
    console.log(dados)
    // Itera sobre cada elemento select
    $('select').each(function () {
        var idNome = $(this).attr('name'); // Obtém o nome do ID do select
        var valor = $(this).val(); // Obtém o valor do select
        dados[idNome] = valor; // Adiciona o nome do ID como chave e o valor como valor ao objeto dados
    });

    $.ajax({
        type: 'GET',
        data: dados,
        url: "resources/views/containers/products_filter/products.php",
        success: function (result) {
            $(".main .conteudo .conteudo-1 .group-products").html(result);
        },
    });
}


// Espera até que o documento esteja completamente carregado

// Seleciona todos os elementos com a classe "placeholder" e remove-os
