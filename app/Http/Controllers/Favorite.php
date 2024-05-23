<?php
$nome_do_arquivo = __FILE__;


if (isset($_POST['form'])) {
    include "../../../db/conn.php";
    include "../../../helps/funcao.php";


    //Load Composer's autoloader
    $retornar = array();
    $acao = $_POST['acao'];
    $nomeCookieFav = "fav_lgbrd";
    $time_cookie = 30 * 24 * 60 * 60;

    if ($acao == "updateFavorite") { //adicionar o produto ao favorito oi remover
        $userId = null;
        $qtd_fav = 0;
        foreach ($_POST as $name => $value) { //define os valores das variaveis e os nomes com refencia do name do input no formulario
            ${$name} = $value;
            ${$name} = str_replace("'", "", ${$name}); //remover aspas simples
        }


        // Verificar se o usuário está logado
        if (auth('') !== false) { //USUARIO ESTÁ AUTENTICADO
            $userId = auth('')['id'];

            // Consultar se o produto já está nos favoritos do usuário
            $query = "SELECT * FROM tb_favorito_loja WHERE cl_usuario_id = '$userId' AND cl_produto_id = '$productID'";
            $select = mysqli_query($conecta, $query);
            $qtd_select = mysqli_num_rows($select);

            if ($select) {
                $qtd_select = mysqli_num_rows($select);
                if ($qtd_select == 0) { //inserir produto no carrinho
                    // Inserir o produto nos favoritos se não estiver presente
                    $query = "INSERT INTO `tb_favorito_loja` (`cl_produto_id`, `cl_usuario_id`, `cl_data`) 
                        VALUES ('$productID', '$userId', '$data')";
                    $insert = mysqli_query($conecta, $query);
                    $execute['data'] = $insert == true ?
                        array("status" => true, "meesage" => "ok") : array("status" => false, "type" => "aplicacao", "meesage" => str_replace("'", "", mysqli_error($conecta)));
                } else { //remover produto ds favoritos
                    $delete = delete_registro('tb_favorito_loja', 'cl_usuario_id', $userId, 'cl_produto_id', $productID);

                    $execute['data'] = $delete == true ?  array("status" => true, "meesage" => "ok") :
                        array("status" => false, "type" => "aplicacao", "message" => "Erro ao remover um produto do carrinho ");
                }
            } else {
                $execute['data'] = array("status" => false, "type" => "aplicacao", "message" => str_replace("'", "", mysqli_error($conecta)));
            }

            //obter a quantidade atualizar de produtos que estão contidos no carrinho do usuário autenticado ou não
            $qtd_fav = auth('')['qtd_fav'];
            
        } else {
            // Verificar se o cookie já existe
            if (isset($_COOKIE["$nomeCookieFav"])) {
                $favCookie = json_decode($_COOKIE["$nomeCookieFav"], true);

                // Verifica se o produto já está nos favoritos
                $productIndex = array_search($productID, array_column($favCookie, 'productID'));

                if ($productIndex === false) {
                    // O produto não está nos favoritos, então adiciona ao array
                    $favCookie[] = array("productID" => $productID);

                    // Atualiza o cookie com o novo valor
                    $jsonFavCookie = json_encode($favCookie);
                    setcookie("$nomeCookieFav", $jsonFavCookie, time() + ($time_cookie), "/"); // Atualiza o valor do cookie
                } else { //remover o produto do array

                    unset($favCookie[$productIndex]);
                    // Reindexa o array para evitar lacunas numéricas
                    $favCookie = array_values($favCookie);

                    // Atualiza o cookie com o novo valor
                    $jsonFavCookie = json_encode($favCookie);
                    setcookie("$nomeCookieFav", $jsonFavCookie, time() + ($time_cookie), "/"); // Atualiza o valor do cookie
                }

                $qtd_fav = count($favCookie);//contabilizar quantos produtos estão no favoritos
            } else {
                $favCookie = array(
                    ['productID' => $productID],
                );

                // inserindo array em um cookie
                $favCookie = json_encode($favCookie);
                setcookie("$nomeCookieFav", $favCookie, time() + ($time_cookie), "/");
                $qtd_fav = 1;
            }

            $execute['data'] = array("status" => true, "meesage" => "ok");
        }




        // Verificar se a operação foi executada com sucesso
        if ($execute['data']['status']) { //executado som sucesso
            $retornar["data"] = array(
                "status" => true, "message" => "Produto adicionado aos favoritos com sucesso",
                "qtd_fav" => $qtd_fav
            );
        } else {
            if ($execute['data']['type'] == "usuario") { //erro de usuário, validação
                $retornar["data"] = array(
                    "status" => false,
                    "message" => $execute['data']['message']
                );
            } else { //erro interno da aplicação
                $retornar["data"] = array(
                    "status" => false,
                    "message" => "Ops, o site está apresentando um mau funcionamento.
                    Lamentamos o inconveniente, mas estamos trabalhando para resolver o 
                    problema o mais rápido possível. Por favor, tente acessar novamente em alguns minutos"
                );
                // Registrar log do erro
                $mensagem = utf8_decode("Ecommerce - $nome_do_arquivo -" . $execute['data']['message']);
                registrar_log($conecta, 'ecommerce', $data, $mensagem);
            }
        }
    }

    // Encerre a conexão com o banco de dados
    mysqli_close($conecta);
    echo json_encode($retornar); //retornando o array
}


if (isset($_GET['containers'])) {
    $container = $_GET['containers'];
    if ($container == "offcanvasFavorite") {
        include "../../../../db/conn.php";
        include "../../../../helps/funcao.php";
        if (auth('') != false) {
            $dados_usuario = auth(''); // Supondo que a função auth('') retorna os dados do usuário e dos produtos
        } else {
            $dados_usuario = cookieAuth('');
        }
        $empresa = consulta_tabela('tb_empresa', 'cl_id', '1', 'cl_empresa'); //diretorio raiz sistema
    }
}
