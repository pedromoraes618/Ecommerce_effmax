<?php

$nome_do_arquivo = __FILE__;


if (isset($_POST['form'])) {
    include "../../../db/conn.php";
    include "../../../helps/funcao.php";


    //Load Composer's autoloader
    $retornar = array();
    $apiFrete = consulta_tabela('tb_parametros', 'cl_id', '86', 'cl_valor');
    $enderecoRetirada = utf8_encode(consulta_tabela('tb_parametros', 'cl_id', '91', 'cl_valor'));
    $acao = $_POST['acao'];

    if ($acao == "show") {
        $codigo_nf = isset($_POST['code']) ? $_POST['code'] : '';
        if ($codigo_nf != "") {
            $query = "SELECT * FROM tb_pedido_loja  where cl_codigo_nf = '$codigo_nf'";
            $consulta = mysqli_query($conecta, $query);
            if ($consulta) {
                $qtd_registro = mysqli_num_rows($consulta);
                if ($qtd_registro > 0) {
                    $linha = mysqli_fetch_assoc($consulta);
                    $nome = utf8_encode($linha['cl_nome']);
                    $email = ($linha['cl_email']);
                    $cpfcnpj = ($linha['cl_cpf_cnpj']);
                    $telefone = ($linha['cl_telefone']);
                    $endereco = utf8_encode($linha['cl_endereco']);
                    $bairro = utf8_encode($linha['cl_bairro']);
                    $numero = utf8_encode($linha['cl_numero']);
                    $complemento = utf8_encode($linha['cl_complemento']);
                    $cep = utf8_encode($linha['cl_cep']);
                    $cidade = utf8_encode($linha['cl_cidade']);
                    $estado = utf8_encode($linha['cl_estado']);
                    $transportadora = utf8_encode($linha['cl_transportadora']);
                    $pagamento_id_interno = ($linha['cl_pagamento_id_interno']);
                    $cupom = ($linha['cl_cupom']);


                    $valor_frete = utf8_encode($linha['cl_valor_frete']);
                    $valor_produto = utf8_encode($linha['cl_valor_produto']);
                    $valor_desconto = utf8_encode($linha['cl_desconto']);
                    $valor_liquido = utf8_encode($linha['cl_valor_liquido']);


                    $informacao = array(
                        "nome" => $nome,
                        "cpf_cnpj" => $cpfcnpj,
                        "email" => $email,
                        "cep" => $cep,
                        "telefone" => $telefone,
                        "forma_pagamento_id" => $pagamento_id_interno,
                        "numero" => $numero,
                        "complemento" => $complemento,
                        "cupom" => $cupom,
                    );

                    $retornar["data"] = array("status" => true, "response" => $informacao);
                    echo json_encode($retornar);
                    exit;
                }
            }
        }

        if (auth('')) { //usuario logado
            $id = auth('')['id'];
            $query = "SELECT * FROM tb_user_loja where cl_id = '$id'";
            $consultar = mysqli_query($conecta, $query);
            $linha = mysqli_fetch_assoc($consultar);
            $nome = utf8_encode($linha['cl_nome']);
            $cpf_cnpj = utf8_encode($linha['cl_cpf_cnpj']);
            $email = ($linha['cl_email']);
            $cep = ($linha['cl_cep']);
            $telefone = ($linha['cl_telefone']);

            $informacao = array(
                "nome" => $nome,
                "cpf_cnpj" => $cpf_cnpj,
                "email" => $email,
                "cep" => $cep,
                "telefone" => $telefone,
                "forma_pagamento_id" => "",
                "numero" => "",
                "complemento" => "",
            );

            $retornar["data"] = array("status" => true, "response" => $informacao);
        } else {
            $retornar["data"] = array("status" => false);
        }
    }

    if ($acao == "create") { //crição do pedido
        mysqli_begin_transaction($conecta);

        foreach ($_POST as $name => $value) { //define os valores das variaveis e os nomes com refencia do name do input no formulario
            ${$name} = utf8_decode($value);
            ${$name} = str_replace("'", "", ${$name}); //remover aspas simples
        }
        $statusInserirProdutoBd = true;
        $user_id = auth('') != false ? auth('')['id'] : '';
        $cpfcnpj = preg_replace('/[^0-9]/', '', $cpfcnpj); // remover caracteres especias
        $telefone = preg_replace('/[^0-9]/', '', $telefone); // remover caracteres especias
        $cep = preg_replace('/[^0-9]/', '', $cep); // remover caracteres especias
        $simulacaoFrete = isset($_POST['simulacaoFrete']) ? json_decode($_POST['simulacaoFrete'], true) : ''; //Simualção de frete feito pelo cliente
        $buscarCep = buscar_cep($cep);
        $codigo_nf = ($codigo_nf != "null") ? $codigo_nf : '';


        if (empty($nome)) {
            $retornar["errors"]["nome"] = required("seu nome");
        }
        if (empty($cpfcnpj)) {
            $retornar["errors"]["cpfcnpj"] = required("um cpf ou cnpj válido");
        } elseif ((!validarCPF($cpfcnpj)) and (strlen($cpfcnpj) > 0 and strlen($cpfcnpj) <= 11)) { //validar cpf
            $retornar["errors"]["cpfcnpj"] = required("um cpf válido");
        } elseif ((!validarCNPJ($cpfcnpj)) and (strlen($cpfcnpj) > 12)) { //validar cnpj
            $retornar["errors"]["cpfcnpj"] = required("um cnpj válido");
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $retornar["errors"]["email"] = required("um email válido");
        }
        if (empty($telefone) or strlen($telefone) < 11) {
            $retornar["errors"]["telefone"] = required("um telefone válido");
        }

        if (empty($cep)) {
            $retornar["errors"]["cep"] = required("um cep válido");
        } elseif ($buscarCep['data']['status'] == false) {
            $retornar["errors"]["cep"] = $buscarCep['data']['message'];
        } else {
            if ($apiFrete == "kangu") {
                if ($buscarCep['data']['status'] == true) {
                    $dadosCep = $buscarCep['data']['response'];
                    $valor_total_venda = simularValoresFrete('', $codigo_nf)['valorTotalVenda'];
                    $peso_mercadoria = simularValoresFrete('', $codigo_nf)['pesoMercadoria'];
                    $alturaCaixa = simularValoresFrete('', $codigo_nf)['alturaCaixa'];
                    $larguraCaixa = simularValoresFrete('', $codigo_nf)['larguraCaixa'];
                    $comprimentoCaixa = simularValoresFrete('', $codigo_nf)['comprimentoCaixa'];

                    $simular_frete =  simularFreteKangu(
                        $dadosCep,
                        $valor_total_venda,
                        $peso_mercadoria,
                        $alturaCaixa,
                        $larguraCaixa,
                        $comprimentoCaixa,
                        1
                    );
                };
            } else {
                $retornar["errors"]["option-frete"] = "Ops, o site está apresentando um mau funcionamento,
                Lamentamos o inconveniente, mas estamos trabalhando para resolver o problema o mais rápido possível. Por favor, tente acessar novamente em alguns minutos";
            }

            if ($simular_frete['data']['status'] == false) {
                $retornar["errors"]["option-frete"] = $simular_frete['data']['message'];
            } elseif (empty($numero)) {
                $retornar["errors"]["numero"] = required("o número");
            } elseif (!isset($_POST['selected_simulacao'])) {
                $retornar["errors"]["option-frete"] = "É necessario escolher um opção de frete";
            }
        }

        if (!isset($_POST['payment'])) {
            $retornar["errors"]["payments"] = "Selecione a forma de pagamento";
        }

        if (auth('') == false) { //usuario não logado
            if (!isset($aceita_termos)) {
                $retornar["errors"]["aceita_termos"] = ("É necessario aceitar os termos");
            }
        }
        if (isset($retornar["errors"])) {
            $retornar["data"] = array("status" => false, "response" => $retornar["errors"]);
            echo json_encode($retornar); //retornando o array
            exit;
        }

        $payment = $_POST['payment']; //forma de pagamento
        if ($simulacaoFrete != "") { //valores do frete 
            $valorFrete = 0;
            $valorDesconto = 0;
            $valorSubTotal = 0;
            $valorTotal = 0;

            $dadosCep = $buscarCep['data']['response'];
            $uf = utf8_decode($dadosCep['uf']);
            $bairro = utf8_decode($dadosCep['bairro']);
            $cidade = utf8_decode($dadosCep['localidade']);
            $endereco = utf8_decode($dadosCep['logradouro']);

            $selectedIdSimulacao = $_POST['selected_simulacao']; //oplção de frete selecionado pelo cliente
            $calcularCarrinho = calcularValorCarrinho($payment, $simulacaoFrete, $selectedIdSimulacao, $codigo_nf, $cupom);
            if ($calcularCarrinho['data']['status']) {
                $dadosCarrinho = $calcularCarrinho['data']['response'];
                $valorSubTotal = $dadosCarrinho['valorSubTotal'];
                $valorFrete = $dadosCarrinho['valorFrete'];
                $valorDesconto = $dadosCarrinho['valorDesconto'];
                $valorTotal = $dadosCarrinho['valorTotal'];
                $transp_nome = $dadosCarrinho['transp_nome'];
                $prazoEntFim = $dadosCarrinho['prazoEnt'];
                $idSimulacao = $dadosCarrinho['idSimulacao'];
                $valorCupom = $dadosCarrinho['valorCupom'];
                $msgCupom = ($dadosCarrinho['msgCupom']);

                if ($transp_nome == "Retirada") {
                    $transportadora =  utf8_decode($transp_nome);
                } else {
                    $prazoEntIni = $prazoEntFim - 1;
                    $transportadora = utf8_decode($transp_nome . " $prazoEntIni - $prazoEntFim dias");
                }
            }
        }
        if (auth('') == false and !empty($cupom)) {
            $retornar["errors"]["cupom"] = "Para usar cupons, você precisa estar logado em sua conta.";
        } elseif ((!empty($cupom) and !empty($msgCupom))) {
            $retornar["errors"]["cupom"] = $msgCupom;
        }

        if (isset($retornar["errors"])) {
            $retornar["data"] = array("status" => false, "response" => $retornar["errors"]);
            echo json_encode($retornar); //retornando o array
            exit;
        }


        $usuarioID = auth('') !== false ? auth('')['id'] : '';
        $fbp = isset($_COOKIE['_fbp']) ? $_COOKIE['_fbp'] : ''; //fbp cookie pixel
        $fbc = isset($_COOKIE['_fbc']) ? $_COOKIE['_fbc'] : ''; //fbc cookie pixel

        if ($codigo_nf == "") { //insert
            $codigo_nf = md5(uniqid(time())); //gerar um novo codigo para nf
            $pedido = rand(100000000000, 999999999999);
            $data_expirar = date('Y-m-d H:i:s', strtotime('+1 day', strtotime($data))); // Adiciona um dia à data atual

            $link_order = "?confirm-order=true&order=$pedido&code=$codigo_nf";
            $query = "INSERT INTO `tb_pedido_loja` (`cl_pedido`, `cl_codigo_nf`, 
            `cl_data`,`cl_usuario_id`,`cl_nome`, `cl_cpf_cnpj`, `cl_email`, `cl_telefone`,
             `cl_cep`, `cl_cidade`, `cl_estado`, `cl_endereco`, `cl_bairro`, `cl_numero`, 
             `cl_complemento`, `cl_valor_frete`, `cl_valor_produto`, `cl_desconto`, `cl_valor_cupom`, 
             `cl_valor_liquido`,`cl_cupom`,`cl_pagamento_id_interno`,`cl_status_compra`,`cl_transportadora`,`cl_data_expirar`,
             `cl_id_simulacao_frete`,`cl_fbp_pixel`,`cl_fbc_pixel` )
               VALUES ('$pedido', '$codigo_nf', '$data','$usuarioID', '$nome', '$cpfcnpj', '$email', '$telefone',
                '$cep', '$cidade', '$uf', '$endereco', '$bairro', 
                '$numero', '$complemento', '$valorFrete', '$valorSubTotal', '$valorDesconto','$valorCupom',
                 '$valorTotal', '$cupom', '$payment', 
                'ANDAMENTO', '$transportadora', '$data_expirar', '$idSimulacao','$fbp','$fbc' ) ";
            $operacao = mysqli_query($conecta, $query);


            /*INSERIR OS PRODUTOS DO CARRINHO PARA O BD */
            $inserirProdutoBd = inserirProdutoBd($codigo_nf, $nome_do_arquivo);
            $statusInserirProdutoBd = $inserirProdutoBd['data']['status'];

            /*pixel */
            $forma_pagamento = consulta_tabela('tb_forma_pagamento', 'cl_id', $payment, 'cl_descricao');
            $produtos = auth('') != false ? auth('') : cookieAuth('');
            $produtosCart = $produtos['produtos_cart'];
            $dados = [
                'dados_usuario' => [
                    "id" => $usuarioID,
                    "nome" => $nome,
                    "email" => $email,
                    "cep" => $cep,
                    "telefone" => $telefone,
                    "cidade" => $cidade,
                    "estado" => $uf,
                ],
                'dados' => [
                    'pagina' => '?checkout',
                    "valor_total" => $valorTotal,
                    "forma_pagamento" => $forma_pagamento
                ],
                "produtos" => $produtosCart,
            ];
            pixel('InitiateCheckout', $dados);
        } else { //update
            $order = consulta_tabela('tb_pedido_loja', 'cl_codigo_nf', $codigo_nf, 'cl_pedido');
            $link_order = "?confirm-order=true&order=$order&code=$codigo_nf";
            $query = "UPDATE `tb_pedido_loja` SET 
            `cl_usuario_id` = '$usuarioID', 
            `cl_nome` = '$nome', 
            `cl_cpf_cnpj` = '$cpfcnpj',
            `cl_email` = '$email', 
            `cl_telefone` = '$telefone', 
            `cl_cep` = '$cep', 
            `cl_cidade` = '$cidade', 
            `cl_estado` = '$uf', 
            `cl_endereco` = '$endereco', 
            `cl_bairro` = '$bairro', 
            `cl_numero` = '$numero', 
            `cl_complemento` = '$complemento', 
            `cl_valor_frete` = '$valorFrete', 
            `cl_valor_produto` = '$valorSubTotal', 
            `cl_desconto` = '$valorDesconto', 
            `cl_valor_cupom` = '$valorCupom', 
            `cl_valor_liquido` = '$valorTotal', 
            `cl_cupom` = '$cupom',
            `cl_pagamento_id_interno` = '$payment',
            `cl_transportadora` = '$transportadora',
            `cl_id_simulacao_frete` = '$idSimulacao'
            WHERE `cl_codigo_nf` = '$codigo_nf' ";
            $operacao = mysqli_query($conecta, $query);
        }

        autoUpdateAuth($usuarioID, $cpfcnpj, $cep, $endereco, $bairro, $cidade, $numero, $telefone); //adicionar dados do usuário que não está preenchido ao seu cadastro




        if ($operacao and $statusInserirProdutoBd) {
            mysqli_commit($conecta);
            $retornar["data"] = array("status" => true, "link_order" => $link_order);
        } else {
            mysqli_rollback($conecta);
            $retornar["data"] = array("status" => false, "message" => "Ops, o site está apresentando um mau funcionamento,
             Lamentamos o inconveniente, mas estamos trabalhando para resolver o problema o mais rápido possível. Por favor, tente acessar novamente em alguns minutos");
            $mensagem = utf8_decode("Ecommerce - $nome_do_arquivo -  checkout");
            registrar_log($conecta, 'ecommerce', $data, $mensagem); // Registrar log do erro
            // $retornar["dados"] =  array("sucesso" => false, "title" => "Não foi possivel realizar o cadastro, para saber mais informações, entra em contato com a equipe da $empresa pelo email $email_remetente ");
        }
    }



    if ($acao == "calcularValor") {
        $dados = $_POST['dados'];
        $payment = isset($_POST['dados']['payment']) ? $_POST['dados']['payment'] : ''; //opção de forma de pagamento
        $simulacaoFrete = isset($_POST['simulacaoFrete']) ? $_POST['simulacaoFrete'] : '';
        $selectedIdSimulacao = isset($_POST['dados']['selected_simulacao']) ? $_POST['dados']['selected_simulacao'] : ''; //opção de frete
        $codigo_nf = isset($_POST['codigo_nf']) ? $_POST['codigo_nf'] : '';
        $cupom = isset($_POST['dados']['cupom']) ? $_POST['dados']['cupom'] : ''; //opção de frete

        // $valorFrete = 0;
        // $valorDesconto = 0;
        // $valorSubTotal = 0;
        // $valorTotal = 0;
        // $valorCupom = 0;


        if (calcularValorCarrinho($payment, $simulacaoFrete, $selectedIdSimulacao, $codigo_nf, $cupom)['data']['status']) {
            $dadosCarrinho = calcularValorCarrinho($payment, $simulacaoFrete, $selectedIdSimulacao, $codigo_nf, $cupom)['data']['response'];
            $valorSubTotal = $dadosCarrinho['valorSubTotal'];
            $valorFrete = $dadosCarrinho['valorFrete'];
            $valorDesconto = $dadosCarrinho['valorDesconto'];
            $valorTotal = $dadosCarrinho['valorTotal'];
            $valorCupom = $dadosCarrinho['valorCupom'];
            $msgCupom = $dadosCarrinho['msgCupom'];

            $informacao = array(
                "valorSubTotal" => real_format($valorSubTotal),
                "valorFrete" => real_format($valorFrete),
                "valorDesconto" => real_format($valorDesconto),
                "valorDescontoDecimal" => ($valorDesconto),
                "valorTotal" => real_format($valorTotal),

                "msgCupom" => ($msgCupom),
                "valorCupom" => real_format($valorCupom),
                "valorCupomDecimal" => ($valorCupom),
            );
            $retornar["data"] = array("status" => true, "response" => $informacao);
        } else {
            $retornar["data"] = array("status" => false, "message" => calcularValorCarrinho($payment, $simulacaoFrete, $selectedIdSimulacao, $codigo_nf, $cupom)['data']['message']);
        }
    }
    mysqli_close($conecta);
    echo json_encode($retornar); //retornando o array
}




if (isset($_GET['containers'])) {
    $container = $_GET['containers'];
    if ($container == "checkout") {

        include "../../../../db/conn.php";
        include "../../../../helps/funcao.php";
        $empresa = consulta_tabela('tb_empresa', 'cl_id', '1', 'cl_empresa'); //diretorio raiz sistema
        $estado_empresa = consulta_tabela('tb_empresa', 'cl_id', '1', 'cl_estado'); //estado da empresa

        $freteGratis = consulta_tabela('tb_parametros', 'cl_id', '87', 'cl_valor');
        $freteCondicaoValorEstado = consulta_tabela('tb_parametros', 'cl_id', '88', 'cl_valor'); //FRETE GRATIS PARA DENTRO DO ESTADO
        $freteCondicaoValorForaEstado = consulta_tabela('tb_parametros', 'cl_id', '89', 'cl_valor'); //FRETE GRATIS PARA FORA DO ESTADO

        $codigo_nf = isset($_GET['code']) ? $_GET['code'] : '';
        $data_expirar = "";
        if (!empty($codigo_nf)) {
            $data_expirar = consulta_tabela("tb_pedido_loja", 'cl_codigo_nf', $codigo_nf, 'cl_data_expirar');
        }

        if (auth('') != false) {
            $dados_usuario = auth($codigo_nf); // Supondo que a função auth('') retorna os dados do usuário e dos produtos
        } else {
            $dados_usuario = cookieAuth($codigo_nf);
        }
        // var_dump($dados_usuario);
    }
}
