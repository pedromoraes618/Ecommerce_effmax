<?php
include "db/conn.php";
include "helps/funcao.php";
$empresa = consulta_tabela('tb_empresa', 'cl_id', '1', 'cl_empresa'); //diretorio raiz sistema
$diretorio_logo = "../../../$empresa/img/ecommerce/logo/logo.png";
$nomeEcommerce = utf8_encode(consulta_tabela('tb_parametros', 'cl_id', '64', 'cl_valor')); //nome do ecommerce
$subTituloPagina = "";
if (isset($_GET['company'])) { //empresa
    $subTituloPagina = "/Informação";
} elseif (isset($_GET['products-filter'])) { //categorias
    $subTituloPagina = "/Filtro";
} elseif (isset($_GET['product-details'])) { //detalhes do produto
    $subTituloPagina = "/Detalhe";
} elseif (isset($_GET['forgot-password'])) { //resetar senha
    $subTituloPagina = "/Esqueçeu a senha";
} elseif (isset($_GET['confirm-email'])) { //confirmar email
    $subTituloPagina = "/Confirmar Email";
} elseif (isset($_GET['checkout'])) { //carrinho
    $subTituloPagina = "/Checkout";
} elseif (isset($_GET['confirm-order'])) { //carrinho
    $subTituloPagina = "/Confirmar";
} elseif (isset($_GET['order-completed'])) { //carrinho
    $subTituloPagina = "/Completo";
} elseif (isset($_GET['user'])) { //carrinho
    $subTituloPagina = "/Usuário";
}
$nomeEcommerce = $nomeEcommerce . $subTituloPagina;
$empresa = consulta_tabela('tb_empresa', 'cl_id', '1', 'cl_empresa'); //diretorio raiz sistema

// $datasetId = consulta_tabela('tb_parametros', 'cl_id', '95', 'cl_valor'); //id pixel
// $accessToken = consulta_tabela('tb_parametros', 'cl_id', '96', 'cl_valor'); //token pixel
// $ativoPixel = consulta_tabela('tb_parametros', 'cl_id', '97', 'cl_valor'); //pixel ativo
