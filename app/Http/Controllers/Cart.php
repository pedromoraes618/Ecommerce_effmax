<?php
$nome_do_arquivo = __FILE__;


if (isset($_POST['form'])) {
    include "../../../db/conn.php";
    include "../../../helps/funcao.php";


    //Load Composer's autoloader
    $retornar = array();
    $acao = $_POST['acao'];
    $nomeCookieCart = "cart_lgbrd";
    $time_cookie = 30 * 24 * 60 * 60;

    if ($acao == "updateCart") { //adicionar ou remover 
        $operacao = isset($_POST['operacao']) ? $_POST['acao'] : '';
        $userId = null;
        $qtd_cart = 0;
        foreach ($_POST as $name => $value) { //define os valores das variaveis e os nomes com refencia do name do input no formulario
            ${$name} = $value;
            ${$name} = str_replace("'", "", ${$name}); //remover aspas simples
        }
        $execute['data'] = array("status" => true, "meesage" => "ok");

        // Verificar se o usuário está logado
        if (auth('') !== false) { //USUARIO ESTÁ AUTENTICADO
            $userId = auth('')['id'];
            if ($operacao == "adicionar") { //adicionar o produto

                $query = "SELECT * FROM tb_carrinho_loja WHERE cl_usuario_id = '$userId' AND cl_produto_id = '$productID'";
                $consulta = mysqli_query($conecta, $query);
                if ($consulta) {
                    $qtd_registro = mysqli_num_rows($consulta);
                    if ($qtd_registro > 0) { //update na
                        $linha = mysqli_fetch_assoc($consulta);
                        $qtdAtual = $linha['cl_quantidade'];
                        $qtd = $qtdAtual + $qtd; //quantidade nova, quantidade que já está no carrinho com a quantidade que o usuario está adicionando no momento

                        $valida_estoque = valida_estoque($productID, $qtd);
                        if ($valida_estoque['status']) { //estoque atende a demanda
                            //update na quantidade
                            $query = "UPDATE tb_carrinho_loja SET cl_quantidade = '$qtd' where  cl_usuario_id = '$userId' AND cl_produto_id = '$productID' ";
                            $insert = mysqli_query($conecta, $query);
                            if (!$insert) {
                                $execute['data'] = array("status" => false, "type" => "aplicacao", "meesage" => str_replace("'", "", mysqli_error($conecta)));
                            }
                        } else { //o estoque não atende a demanda
                            $execute['data'] = array("status" => false, "type" => "usuario", "message" => $valida_estoque['message']);
                        }
                    } else {
                        // Inserir o produto nos carrinho se não estiver presente
                        $query = "INSERT INTO `tb_carrinho_loja` (`cl_produto_id`, `cl_usuario_id`, `cl_quantidade`,`cl_data`) 
                             VALUES ('$productID', '$userId','$qtd', '$data')";
                        $insert = mysqli_query($conecta, $query);
                        if (!$insert) {
                            $execute['data'] = array("status" => false, "type" => "aplicacao", "meesage" => str_replace("'", "", mysqli_error($conecta)));
                        }
                    }

                    /*pixel */
                    $dados_usuario = auth('')['dados_usuario'];
                    $descricao = utf8_encode(consulta_tabela('tb_produtos', 'cl_id', $productID, 'cl_descricao'));
                    $dados = [
                        'dados_usuario' => $dados_usuario,
                        'dados' => ['pagina' => '?addCarrinho'],
                        'produto' => [
                            'produtoID' => $productID,
                            'qtdProduto' => $qtd,
                            'descricao' => $descricao
                        ]
                    ];
                    pixel('AddToCart', $dados);
                }
            } elseif ($operacao == "remover") { //remoer o produto
                $query = "SELECT * FROM tb_carrinho_loja WHERE cl_usuario_id = '$userId' AND cl_produto_id = '$productID'";
                $select = mysqli_query($conecta, $query);
                if ($select) {
                    $qtd_select = mysqli_num_rows($select);
                    if ($qtd_select > 0) { //inserir produto no carrinho
                        $delete = delete_registro('tb_carrinho_loja', 'cl_usuario_id', $userId, 'cl_produto_id', $productID);
                        if (!$delete) {
                            $execute['data'] =   array("status" => false, "type" => "aplicacao", "message" => "Erro ao remover um produto do carrinho ");
                        }
                    } else { //remover produto do carrinho
                        $execute['data'] =   array("status" => false, "type" => "aplicacao", "message" => "o produto não está no seu carrinho");
                    }
                } else {
                    $execute['data'] = array("status" => false, "type" => "aplicacao", "message" => str_replace("'", "", mysqli_error($conecta)));
                }
            } else {
                $execute['data'] =   array("status" => false, "type" => "aplicacao", "message" => "operação não identificado, adicionar ou remover produto do carrinho");
            }
            //obter a quantidade atualizar de produtos que estão contidos no carrinho do usuário autenticado ou não
            $qtd_cart = auth('')['qtd_cart'];
        } else {

            if ($operacao == "adicionar") { //adicionar o produto
                // Verificar se o cookie já existe
                if (isset($_COOKIE["$nomeCookieCart"])) {
                    $cartCookie = json_decode($_COOKIE["$nomeCookieCart"], true);

                    // Verifica se o produto já está nos carrinho
                    $productIndex = array_search($productID, array_column($cartCookie, 'productID'));


                    if ($productIndex === false) {
                        // O produto não está no carrinho, adicionando ao cookie
                        $valida_estoque = valida_estoque($productID, $qtd);
                        if ($valida_estoque['status'] == false) { //estoque não atende a demanda
                            $execute['data'] = array("status" => false, "type" => "usuario", "message" => $valida_estoque['message']);
                        } else {
                            $cartCookie[] = array("productID" => $productID, "qtd" => $qtd);
                            // Atualiza o cookie com o novo valor
                            $jsonCartCookie = json_encode($cartCookie);
                            setcookie("$nomeCookieCart", $jsonCartCookie, time() + ($time_cookie), "/"); // Atualiza o valor do cookie
                        }
                    } else {
                        $qtdAtual = $cartCookie[$productIndex]['qtd'];
                        $qtd = $qtdAtual + $qtd;
                        // O produto não está no carrinho, adicionando ao cookie
                        $valida_estoque = valida_estoque($productID, $qtd);
                        if ($valida_estoque['status'] == false) { //estoque não atende a demanda
                            $execute['data'] = array("status" => false, "type" => "usuario", "message" => $valida_estoque['message']);
                        } else {
                            // O produto já está no carrinho, atualizando a quantidade
                            $cartCookie[$productIndex]['qtd'] = $qtd; // Adiciona a quantidade atual à quantidade existente no carrinho
                            // Atualiza o cookie com o novo valor
                            $jsonCartCookie = json_encode($cartCookie);
                            setcookie("$nomeCookieCart", $jsonCartCookie, time() + ($time_cookie), "/"); // Atualiza o valor do cookie
                        }
                    }

                    $qtd_cart = count($cartCookie);
                } else {
                    $cartCookie[] = array("productID" => $productID, "qtd" => $qtd);

                    // inserindo array em um cookie
                    $cartCookie = json_encode($cartCookie);
                    setcookie("$nomeCookieCart", $cartCookie, time() + ($time_cookie), "/");
                    $qtd_cart = 1;
                }
            } else {
                // Verificar se o cookie já existe
                if (isset($_COOKIE["$nomeCookieCart"])) {
                    $cartCookie = json_decode($_COOKIE["$nomeCookieCart"], true);
                    // Verifica se o produto já está nos carrinho
                    $productIndex = array_search($productID, array_column($cartCookie, 'productID'));
                    if ($productIndex !== false) {
                        unset($cartCookie[$productIndex]);
                        // Reindexa o array para evitar lacunas numéricas
                        $cartCookie = array_values($cartCookie);
                        // Atualiza o cookie com o novo valor
                        $jsonCartCookie = json_encode($cartCookie);
                        setcookie("$nomeCookieCart", $jsonCartCookie, time() + ($time_cookie), "/"); // Atualiza o valor do cookie
                    } else {
                        $execute['data'] = array("status" => false, "type" => "usuario", "message" => "O produto não está no carrinho");
                    }
                    $qtd_cart = count($cartCookie);
                } else {
                    $execute['data'] = array("status" => false, "type" => "usuario", "message" => "O produto não está no carrinho");
                }
            }
        }

        // Verificar se a operação foi executada com sucesso
        if ($execute['data']['status']) { //executado som sucesso
            $retornar["data"] = array(
                "status" => true, "message" => "Produto adicionado ao carrinho com sucesso",
                "qtd_cart" => $qtd_cart
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
                    problema o mais rápido possível. Por carrinhoor, tente acessar novamente em alguns minutos"
                );
                // Registrar log do erro
                $mensagem = utf8_decode("Ecommerce - $nome_do_arquivo -" . $execute['data']['message']);
                registrar_log($conecta, 'ecommerce', $data, $mensagem);
            }
        }
    }


    if ($acao == "qtdCart") { //adicionar ou remover 
        $userId = null;
        $qtd_cart = 0;
        foreach ($_POST as $name => $value) { //define os valores das variaveis e os nomes com refencia do name do input no formulario
            ${$name} = $value;
            ${$name} = str_replace("'", "", ${$name}); //remover aspas simples
        }
        $execute['data'] = array("status" => true, "meesage" => "ok"); //inicializando

        // Verificar se o usuário está logado
        if (auth('') !== false) { //USUARIO ESTÁ AUTENTICADO
            $userId = auth('')['id'];

            // Consultar se o produto já está nos carrinho do usuário
            $query = "SELECT * FROM tb_carrinho_loja WHERE cl_usuario_id = '$userId' AND cl_produto_id = '$productID'";
            $select = mysqli_query($conecta, $query);
            if ($select) {
                $qtd_select = mysqli_num_rows($select);
                $linha = mysqli_fetch_assoc($select);
                $cartId = $linha['cl_id'];
                $valida_estoque = valida_estoque($productID, $qtd);
                if ($valida_estoque['status']) { //estoque atende a demanda
                    if ($qtd_select == 0) { //inserir produto no carrinho

                        // Inserir o produto nos carrinho se não estiver presente
                        $query = "INSERT INTO `tb_carrinho_loja` (`cl_produto_id`, `cl_usuario_id`, `cl_quantidade`,`cl_data`) 
                         VALUES ('$productID', '$userId','$qtd', '$data')";
                        $insert = mysqli_query($conecta, $query);
                        if (!$insert) {
                            $execute['data'] = array("status" => false, "type" => "aplicacao", "meesage" => str_replace("'", "", mysqli_error($conecta)));
                        }
                    } else { //remover produto do carrinho
                        if ($qtd == 0) { //remover o produto do carrinho
                            $delete = delete_registro('tb_carrinho_loja', 'cl_usuario_id', $userId, 'cl_produto_id', $productID);
                            if (!$delete) {
                                $execute['data'] =   array("status" => false, "type" => "aplicacao", "message" => "Erro ao remover um produto do carrinho ");
                            }
                        } else { //atualizar a quantidade do produto no carrinho
                            $query = "UPDATE tb_carrinho_loja SET cl_quantidade = '$qtd' where cl_id ='$cartId'";
                            $update = mysqli_query($conecta, $query);
                            if (!$update) {
                                $execute['data'] =   array("status" => false, "type" => "aplicacao", "message" =>  str_replace("'", "", mysqli_error($conecta)));
                            }
                        }
                    }
                } else { //o estoque não atende a demanda
                    $execute['data'] = array("status" => false, "type" => "usuario", "message" => $valida_estoque['message']);
                }
            } else {
                $execute['data'] = array("status" => false, "type" => "aplicacao", "message" => str_replace("'", "", mysqli_error($conecta)));
            }

            //obter a quantidade atualizar de produtos que estão contidos no carrinho do usuário autenticado ou não
            $qtd_cart = auth('')['qtd_cart'];
        } else {
            // Verificar se o cookie já existe

            if (isset($_COOKIE["$nomeCookieCart"])) {
                $cartCookie = json_decode($_COOKIE["$nomeCookieCart"], true);

                // Verifica se o produto já está nos carrinho
                $productIndex = array_search($productID, array_column($cartCookie, 'productID'));
                $valida_estoque = valida_estoque($productID, $qtd);

                if ($valida_estoque['status'] == false) { //estoque não atende a demanda
                    $execute['data'] = array("status" => false, "type" => "usuario", "message" => $valida_estoque['message']);
                } else { //estoque atende a demanda
                    if ($productIndex === false) {
                        // O produto não está no carrinho, adicionando ao cookie
                        $cartCookie[] = array("productID" => $productID, "qtd" => $qtd);

                        // Atualiza o cookie com o novo valor
                        $jsonCartCookie = json_encode($cartCookie);
                        setcookie("$nomeCookieCart", $jsonCartCookie, time() + ($time_cookie), "/"); // Atualiza o valor do cookie
                    } else {
                        if ($qtd == 0) { //remover o produto do carrinho
                            unset($cartCookie[$productIndex]);

                            // Reindexa o array para evitar lacunas numéricas
                            $cartCookie = array_values($cartCookie);

                            // Atualiza o cookie com o novo valor
                            $jsonCartCookie = json_encode($cartCookie);
                            setcookie("$nomeCookieCart", $jsonCartCookie, time() + ($time_cookie), "/"); // Atualiza o valor do cookie
                        } else { //atualizar o produto com a nova quantidade
                            $cartCookie[$productIndex]['qtd'] = $qtd; // Atualiza a quantidade do produto

                            // Atualiza o cookie com o novo valor
                            $jsonCartCookie = json_encode($cartCookie);
                            setcookie("$nomeCookieCart", $jsonCartCookie, time() + ($time_cookie), "/"); // Atualiza o valor do cookie
                        }
                    }
                }
                $qtd_cart = count($cartCookie);
            } else {
                $cartCookie[] = array("productID" => $productID, "qtd" => $qtd);

                // inserindo array em um cookie
                $cartCookie = json_encode($cartCookie);
                setcookie("$nomeCookieCart", $cartCookie, time() + ($time_cookie), "/");
                $qtd_cart = 1;
            }
        }



        // Verificar se a operação foi executada com sucesso
        if ($execute['data']['status']) { //executado som sucesso
            $retornar["data"] = array(
                "status" => true, "message" => "Produto adicionado ao carrinho com sucesso",
                "qtd_cart" => $qtd_cart
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
                    problema o mais rápido possível. Por carrinhoor, tente acessar novamente em alguns minutos"
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
    if ($container == "offcanvasCart") {
        include "../../../../db/conn.php";
        include "../../../../helps/funcao.php";
        $empresa = consulta_tabela('tb_empresa', 'cl_id', '1', 'cl_empresa'); //diretorio raiz sistema
        $estado_empresa = consulta_tabela('tb_empresa', 'cl_id', '1', 'cl_estado'); //estado da empresa

        $freteGratis = consulta_tabela('tb_parametros', 'cl_id', '87', 'cl_valor');
        $freteCondicaoValorEstado = consulta_tabela('tb_parametros', 'cl_id', '88', 'cl_valor'); //FRETE GRATIS PARA DENTRO DO ESTADO
        $freteCondicaoValorForaEstado = consulta_tabela('tb_parametros', 'cl_id', '89', 'cl_valor'); //FRETE GRATIS PARA FORA DO ESTADO

        if (auth('') != false) {
            $dados_usuario = auth(''); // Supondo que a função auth('') retorna os dados do usuário e dos produtos
            
        } else {
            $dados_usuario = cookieAuth('');
        }
        // $qtd_parcela = 0;
        // $query = "SELECT max(cl_parcelamento_sem_juros) as qtd,cl_id FROM tb_forma_pagamento ";
        // $resultados = consultaQueryBd($query);
        // if ($resultados) {
        //     foreach ($resultados as $linha) {
        //         $id = $linha['cl_id'];
        //         $qtd_parcela = $linha['qtd'];
        //     }
        // }

        
        $qtd_parcela = 0;
        $query = "SELECT max(cl_parcelamento_sem_juros) as qtd,cl_id FROM tb_forma_pagamento WHERE  cl_ativo_delivery='S' and cl_ativo='S' ";
        $resultados = mysqli_query($conecta, $query);
        if ($resultados) {
            $linha = mysqli_fetch_assoc($resultados);
            $qtd_parcela = $linha['qtd'];
        }


    }
}
