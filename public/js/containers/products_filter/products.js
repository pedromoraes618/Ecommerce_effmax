
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
        pagination: page,
    },
    url: "resources/views/containers/products_filter/products.php",
    success: function (result) {
        $(".main .conteudo .conteudo-1 .group-products").html(result);
    },
});