<?php
$nome_do_arquivo = __FILE__;


if (isset($_GET['page'])) {
    include "../../../db/conn.php";
    include "../../../helps/funcao.php";
    $containers = isset($_GET['containers']) ? $_GET['containers'] : '';
    $products_filter = isset($_GET['products-filter']) ? $_GET['products-filter'] : '';
    $subcategory = isset($_GET['subcategory']) ? $_GET['subcategory'] : '';
    $rules = isset($_GET['rules']) ? $_GET['rules'] : '';
    $product_details = isset($_GET['product-details']) ? $_GET['product-details'] : '';
    $news = isset($_GET['news']) ? $_GET['news'] : '';
    $catalog = isset($_GET['catalog']) ? $_GET['catalog'] : '';
    $discount = isset($_GET['discount']) ? $_GET['discount'] : '';
    $checkout = isset($_GET['checkout']) ? true : '';
    $confirm_order = isset($_GET['confirm-order']) ? true : '';
    $order_completed = isset($_GET['order-completed']) ? true : '';

    $titulo_secao_novidade = utf8_encode(consulta_tabela('tb_parametros', 'cl_id', '113', 'cl_valor')); //verifica se está habiltado
    $titulo_secao_desconto = utf8_encode(consulta_tabela('tb_parametros', 'cl_id', '112', 'cl_valor')); //verifica se está habiltado
    $titulo_secao_catalogo = utf8_encode(consulta_tabela('tb_parametros', 'cl_id', '115', 'cl_valor')); //verifica se está habiltado

    $path = null;

    if ($rules == "about") { //empresa
        $rules = "Sobré Nós";
        $path .= "<li class='breadcrumb-item'><a >$rules</a></li>";
    } elseif ($rules == "privacypolicy") {
        $rules = "Política de Privacidade";
        $path .= "<li class='breadcrumb-item'><a >$rules</a></li>";
    } elseif ($rules == "termsconditions") {
        $rules = "Termos e condições";
        $path .= "<li class='breadcrumb-item'><a >$rules</a></li>";
    } elseif ($rules == "devolution") {
        $rules = "Política de Devolução";
        $path .= "<li class='breadcrumb-item'><a >$rules</a></li>";
    }

    if (!empty($products_filter)) { //pesquisa aleatoria
        $path .= "<li class='breadcrumb-item'><a >$products_filter</a></li>";
    }

    if (!empty($subcategory)) { //categoria
        $categoria_id = consulta_tabela('tb_subgrupo_estoque', 'cl_id', $subcategory, 'cl_grupo_id'); //consultar id da categoria
        $descricao_categoria = utf8_encode(consulta_tabela('tb_grupo_estoque', 'cl_id', $categoria_id, 'cl_descricao')); //descrição da categoria
        $descricao_subcategoria = utf8_encode(consulta_tabela('tb_subgrupo_estoque', 'cl_id', $subcategory, 'cl_descricao')); //descricao da subcategoria

        $path .= "<li class='breadcrumb-item'><a >$descricao_categoria</a></li>
        <li class='breadcrumb-item'><a >$descricao_subcategoria</a></li>";
    }


    if (!empty($product_details)) {

        $subcategoria_id = consulta_tabela('tb_produtos', 'cl_id', $product_details, 'cl_grupo_id'); //consultar id da categoria
        $descricao_subcategoria = utf8_encode(consulta_tabela('tb_subgrupo_estoque', 'cl_id', $subcategoria_id, 'cl_descricao')); //descricao da subcategoria

        $categoria_id = consulta_tabela('tb_subgrupo_estoque', 'cl_id', $subcategoria_id, 'cl_grupo_id'); //consultar id da categoria
        $descricao_categoria = utf8_encode(consulta_tabela('tb_grupo_estoque', 'cl_id', $categoria_id, 'cl_descricao')); //descrição da categoria

        $path .= "<li class='breadcrumb-item'><a >$descricao_categoria</a></li>
        <li class='breadcrumb-item'><a class='text-decoration-none' href='?products-filter&subcategory=$subcategoria_id' >$descricao_subcategoria</a></li>";

        $descricao_produto = utf8_encode(consulta_tabela('tb_produtos', 'cl_id', $product_details, 'cl_descricao')); //descrição dp produto
        $path .= "<li class='breadcrumb-item'><a >$descricao_produto</a></li>";
    }
    if ($news == "true") {
        $path .= "<li class='breadcrumb-item'><a >$titulo_secao_novidade</a></li>";
    }
    if ($catalog == "true") {
        $path .= "<li class='breadcrumb-item'><a >$titulo_secao_catalogo</a></li>";
    }


    if ($discount == "true") {
        $path .= "<li class='breadcrumb-item'><a >$titulo_secao_desconto</a></li>";
    }
    if ($checkout == true) {
        $path .= "<li class='breadcrumb-item'><a >Checkout</a></li>";
    }
    if ($confirm_order == true) {
        $code = isset($_GET['code']) ? $_GET['code'] : '';
        $order = isset($_GET['order']) ? $_GET['order'] : '';
        $path .= "<li class='breadcrumb-item'><a class='text-decoration-none' href='?checkout=true=true&order=$order&code=$code'>Checkout</a></li>
        <li class='breadcrumb-item'><a >Confirmar Pedido</a></li>";
    }
    if ($order_completed == "order_completed") {
        $path .= "<li class='breadcrumb-item'><a>Pedido Confirmado</a></li>";
    }
}
