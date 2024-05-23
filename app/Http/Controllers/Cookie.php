<?php
$cookie = 0;
if (isset($_GET['containers'])) {
    $container = $_GET['containers'];
    include "../../../../db/conn.php";
    include "../../../../helps/funcao.php";

    if (auth('') !== false) {
        $cookie = auth('')['dados_usuario']['cookie'];

    } else {
        echo 0;
    }
    // var_dump($dados_usuario);

}
