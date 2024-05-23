<?php
$nome_do_arquivo = __FILE__;


if (isset($_GET['page'])) {
    $containers = isset($_GET['containers']) ? $_GET['containers'] : '';
    $layouts = isset($_GET['layouts']) ? $_GET['layouts'] : '';

    /*parametros */
    $products_filter = isset($_GET['products_filter']) ? utf8_decode($_GET['products_filter']) : '';

    $subcategory = isset($_GET['subcategory']) ? $_GET['subcategory'] : '';

    $news = isset($_GET['news']) ? $_GET['news'] : '';
    $catalog = isset($_GET['catalog']) ? $_GET['catalog'] : '';
    $discount = isset($_GET['discount']) ? $_GET['discount'] : '';
    $current_page = isset($_GET['pagination']) ? (($_GET['pagination'] == "") ? 1 : $_GET['pagination']) : '1';

    if ($news == "true") {
        $dados = ['pagina' => '?products-filter&news=true'];
    } elseif ($discount == "true") {
        $dados = ['pagina' => '?products-filter&discount=true'];
    } else {
        $dados = ['pagina' => '?products-filter&catalog=true'];
    }


    if ($containers == "products") { // Produtos
        include "../../../../db/conn.php";
        include "../../../../helps/funcao.php";
        $empresa = consulta_tabela('tb_empresa', 'cl_id', '1', 'cl_empresa'); // Diretório raiz do sistema gerenciador

        $diferencia_dias_lancamento = verficar_paramentro($conecta, 'tb_parametros', "cl_id", "41");

        $limite = consulta_tabela('tb_parametros', 'cl_id', 85, 'cl_valor'); //limite por pagina
        $inicio = ($current_page * $limite);
        // Consulta SQL para obter os produtos com a cláusula LIMIT e OFFSET para a paginação
        $inicio = ($current_page - 1) * $limite;

        $subcategory_desc = utf8_encode(consulta_tabela('tb_subgrupo_estoque', 'cl_id', $subcategory, 'cl_descricao'));

        /*titulo da seção */
        $titulo_secao_novidade = utf8_encode(consulta_tabela('tb_parametros', 'cl_id', '113', 'cl_valor')); //verifica se está habiltado
        $titulo_secao_desconto = utf8_encode(consulta_tabela('tb_parametros', 'cl_id', '112', 'cl_valor')); //verifica se está habiltado
        $titulo_secao_catalogo = utf8_encode(consulta_tabela('tb_parametros', 'cl_id', '115', 'cl_valor')); //verifica se está habiltado
        if (!empty($subcategory)) {
            $title_session = $subcategory_desc;
        } elseif ($news == "true") {
            $title_session = $titulo_secao_novidade;
        } elseif ($catalog == "true") {
            $title_session = $titulo_secao_catalogo;
        } elseif ($discount == "true") {
            $title_session = $titulo_secao_desconto;
        } else {
            $title_session = "Resultado da pesquisa";
        }

        /*filtros */
        $order = isset($_GET['order']) ? $_GET['order'] : '';
        $min_preco = isset($_GET['min_preco']) ? $_GET['min_preco'] : '';
        $max_preco = isset($_GET['max_preco']) ? $_GET['max_preco'] : '';

        $condicao_todos = isset($_GET['condicao_todos']) ? $_GET['condicao_todos'] : '';
        $condicao_novo = isset($_GET['condicao_novo']) ? $_GET['condicao_novo'] : '';
        $condicao_usado = isset($_GET['condicao_usado']) ? $_GET['condicao_usado'] : '';
        $unidade = isset($_GET['unidade']) ? $_GET['unidade'] : '';
        $promocao = isset($_GET['promocao']) ? $_GET['promocao'] : '';
        $condicao = "";

        if (isset($_GET['condicao_usado']) && isset($_GET['condicao_novo'])) {
            $condicao = '';
        } elseif (isset($_GET['condicao_usado'])) {
            $condicao = 'USADO';
        } elseif (isset($_GET['condicao_novo'])) {
            $condicao = 'NOVO';
        }
        // echo $condicao_novo;
        // Definindo a ordem
        switch ($order) {
            case "a_z":
                $order = "cl_descricao ASC";
                break;
            case "z_a":
                $order = "cl_descricao DESC";
                break;
            case "menor_maior_preco":
                $order = "cl_preco_venda ASC";
                break;
            case "maior_menor_preco":
                $order = "cl_preco_venda DESC";
                break;
            case "mais_vendidos":
                $order = "total_vendas DESC";
                break;
            case "menos_vendidos":
                $order = "total_vendas ASC";
                break;
            default:
                $order = "cl_id DESC";
                break;
        }


        $query = "SELECT prd.*,prd.cl_id as produtoid FROM tb_produtos as prd 
        WHERE prd.cl_status_ativo = 'SIM' and prd.cl_estoque > 0 and prd.cl_tipo_id ='1' ";

        if (!empty($products_filter)) { // Pesquisa pela descrição, título etc.
            $products_filter = (str_replace("'", "", $products_filter));
            $query .= " AND (cl_descricao LIKE '%{$products_filter}%' OR 
              cl_referencia LIKE '%{$products_filter}%' OR
              cl_descricao_extendida_delivery LIKE '%{$products_filter}%') ";
        }
        if (!empty($subcategory)) { // produtos que pertencem ao grupo 
            $query .= " AND ( cl_grupo_id ='$subcategory' ) ";
        }

        if (!empty($discount) or !empty($promocao)) { // produtos que estão com desconto
            $query .= " AND ( prd.cl_preco_promocao > 0
            and prd.cl_data_valida_promocao >= '$data_lancamento' ) ";

            if (!empty($min_preco) and !empty($max_preco)) { //filtro por preço
                $query .= " AND (prd.cl_preco_venda BETWEEN $min_preco AND $max_preco or prd.cl_preco_promocao BETWEEN $min_preco AND $max_preco   ) ";
            }
        } else {
            if (!empty($min_preco) and !empty($max_preco)) { //filtro por preço
                $query .= " AND (prd.cl_preco_venda BETWEEN $min_preco AND $max_preco ) ";
            }
        }

        if ($news == "true") { // produtos que estão em lançamento
            $query .= " AND ( prd.cl_data_cadastro >= DATE_SUB('$data_lancamento', INTERVAL '$diferencia_dias_lancamento' DAY) ) ";
        }

        if ($condicao != "") { //condição do produto usado ou novo
            $query .= " AND ( prd.cl_condicao = '$condicao' ) ";
        }

        $resultados = consulta_linhas_tb_query($conecta, "SELECT * FROM tb_unidade_medida");
        if ($resultados) {
            $unidades_ids = []; // Array para armazenar os IDs das unidades selecionadas
            foreach ($resultados as $linha) {
                $id = $linha['cl_id'];
                if (isset($_GET['unidade' . $id])) {
                    $unidades_ids[] = $id; // Adiciona o ID ao array escapado
                }
            }

            if (!empty($unidades_ids)) {
                $unidades_ids_str = implode("','", $unidades_ids); // Transforma o array em uma string separada por vírgulas e aspas
                $query .= " AND prd.cl_und_id IN ('$unidades_ids_str') "; // Usa IN para verificar se o ID está em uma lista de valores
            }
        }


        if (!empty($order)) { //ordenar
            $query .= " ORDER BY $order";
        }

        $query .= " LIMIT $inicio, $limite";
        $consultar_produtos = mysqli_query($conecta, $query); // Consulta
        $qtd_prd = mysqli_num_rows($consultar_produtos); // Quantidade de produtos
        if (!$consultar_produtos) {
            $erro = str_replace("'", "", mysqli_error($conecta));
            $mensagem = utf8_decode("Ecommerce - $nome_do_arquivo - containers == products / consultar produtos / erro - $erro");
            registrar_log($conecta, 'ecommerce', $data, $mensagem); // Registrar log do erro
        }


        // Calcular o número total de páginas
        $query_limite = "SELECT COUNT(*) AS count FROM tb_produtos AS prd 
        WHERE prd.cl_status_ativo = 'SIM' and prd.cl_tipo_id ='1' and prd.cl_estoque > 0 ";
        if (!empty($products_filter)) { // Pesquisa pela descrição, título etc.
            $query_limite .= " AND (cl_descricao LIKE '%{$products_filter}%' OR 
              cl_referencia LIKE '%{$products_filter}%' OR
              cl_descricao_extendida_delivery LIKE '%{$products_filter}%')";
        }
        if (!empty($subcategory)) { // produtos que pertencem ao grupo 
            $query_limite .= " AND ( cl_grupo_id ='$subcategory' ) ";
        }

        if (!empty($discount)  or !empty($promocao)) { // produtos que estão com desconto
            $query_limite .= " AND ( prd.cl_preco_promocao > 0
            and prd.cl_data_valida_promocao >= '$data_lancamento' ) ";
            if (!empty($min_preco) and !empty($max_preco)) { //filtro por preço
                $query_limite .= " AND (prd.cl_preco_venda BETWEEN $min_preco AND $max_preco or prd.cl_preco_promocao BETWEEN $min_preco AND $max_preco   ) ";
            }
        } else {
            if (!empty($min_preco) && !empty($max_preco)) { //filtro por preço
                $query_limite .= " AND (prd.cl_preco_venda BETWEEN $min_preco AND $max_preco) ";
            }
        }

        if ($news == "true") { // produtos que estão em lançamento
            $query_limite .= " AND ( prd.cl_data_cadastro >= DATE_SUB('$data_lancamento', INTERVAL '$diferencia_dias_lancamento' DAY )) ";
        }


        if ($condicao != "") { //condição do produto usado ou novo
            $query_limite .= " AND  (prd.cl_condicao = '$condicao' ) ";
        }

        $resultados = consulta_linhas_tb_query($conecta, "SELECT * FROM tb_unidade_medida");
        if ($resultados) {
            $unidades_ids = []; // Array para armazenar os IDs das unidades selecionadas
            foreach ($resultados as $linha) {
                $id = $linha['cl_id'];
                if (isset($_GET['unidade' . $id])) {
                    $unidades_ids[] = $id; // Adiciona o ID ao array escapado
                }
            }

            if (!empty($unidades_ids)) {
                $unidades_ids_str = implode("','", $unidades_ids); // Transforma o array em uma string separada por vírgulas e aspas
                $query_limite .= " AND prd.cl_und_id IN ('$unidades_ids_str') "; // Usa IN para verificar se o ID está em uma lista de valores
            }
        }



        if (!empty($order)) : //ordenar
            $query_limite .= " ORDER BY $order";
        endif;

        $consultar_produtos_limite = mysqli_query($conecta, $query_limite); // Consulta para contar o total de resultados
        $linha = mysqli_fetch_assoc($consultar_produtos_limite);
        $registros = $linha['count'];
        $total_pages = ceil($registros / $limite); // Número total de páginas


        /*pixel */
        if (!empty($products_filter)) {
            $dados = ['pagina' => '?products-filter&catalog=true', 'pesquisa' => $products_filter];
            if (auth('') !== false) {
                $dados_usuario = auth('')['dados_usuario'];
                $dados = [
                    'dados_usuario' => $dados_usuario,
                    'dados' => $dados
                ];

                pixel('Search', $dados);
            }
        } else {
            if (auth('') !== false) {
                $dados_usuario = auth('')['dados_usuario'];
                $dados = [
                    'dados_usuario' => $dados_usuario,
                    'dados' => $dados
                ];
                pixel('ViewCategory', $dados);
            }
        }
    } elseif ($containers == "group") {
        include "../../../../db/conn.php";
        include "../../../../helps/funcao.php";
    }
}
