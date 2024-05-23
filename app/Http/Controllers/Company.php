<?php
$nome_do_arquivo = __FILE__;

if (isset($_GET['page'])) {

    $rules = isset($_GET['rules']) ? $_GET['rules'] : ''; //regras
    include "../../../../db/conn.php";
    include "../../../../helps/funcao.php";

    if ($rules == "about") { //sobre a empresa, politicas de privacidade..
        $html = utf8_encode(consulta_tabela('tb_parametros', 'cl_id', 82, 'cl_valor'));
    } elseif ($rules == "privacypolicy") {

        $html = utf8_encode(consulta_tabela('tb_parametros', 'cl_id', 83, 'cl_valor'));
    } elseif ($rules == "termsconditions") {

        $html = utf8_encode(consulta_tabela('tb_parametros', 'cl_id', 84, 'cl_valor'));
    }elseif ($rules == "devolution") {
        $html = utf8_encode(consulta_tabela('tb_parametros', 'cl_id', 102, 'cl_valor'));
    }
}
