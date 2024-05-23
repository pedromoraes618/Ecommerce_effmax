
<?php
// include "db/conn.php";
// include "helps/funcao.php";

if (isset($_GET['company'])) { //empresa
    $pagina = isset($_GET['company']) ? "company" : "initial";
} elseif (isset($_GET['products-filter'])) { //categorias
    $pagina = isset($_GET['products-filter']) ? "products-filter" : "initial";
} elseif (isset($_GET['product-details'])) { //detalhes do produto
    $pagina = isset($_GET['product-details']) ? "product-details" : "initial";
} elseif (isset($_GET['forgot-password'])) { //resetar senha
    $pagina = isset($_GET['forgot-password']) ? "forgot-password" : "initial";
} elseif (isset($_GET['confirm-email'])) { //confirmar email
    $pagina = isset($_GET['confirm-email']) ? "confirm-email" : "initial";
} elseif (isset($_GET['checkout'])) { //carrinho
    $pagina = isset($_GET['checkout']) ? "checkout" : "initial";
} elseif (isset($_GET['confirm-order'])) { //carrinho
    $pagina = isset($_GET['confirm-order']) ? "confirm-order" : "initial";
} elseif (isset($_GET['order-completed'])) { //carrinho
    $pagina = isset($_GET['order-completed']) ? "order-completed" : "initial";
} elseif (isset($_GET['user']) and auth('') !== false) { //carrinho
    $pagina = isset($_GET['user']) ? "user" : "initial";
} else {
    $pagina = "inicial";
}

$url = ($_GET);
switch ($pagina) {
    case "subcategory":
        include "resources/views/pages/subcategory.php";
        break;
    case "company":
        include "resources/views/pages/company.php";
        break;
    case "products-filter":
        include "resources/views/pages/products_filter.php";
        break;
    case "product-details":
        include "resources/views/pages/products_details.php";
        break;
    case "forgot-password":
        include "resources/views/pages/auth/reset_password.php";
        break;
    case "confirm-email":
        include "resources/views/pages/auth/confirm_email.php";
        break;
    case "checkout":
        include "resources/views/pages/checkout.php";
        break;
    case "confirm-order":
        include "resources/views/pages/confirm_order.php";
        break;
    case "order-completed":
        include "resources/views/pages/order_completed.php";
        break;
    case "user":
        include "resources/views/pages/user.php";
        break;
    default:
        include "app/Http/Controllers/Initial.php";
        include "resources/views/pages/initial.php";
        break;
}

?>

