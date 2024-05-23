<?php
$nome_do_arquivo = __FILE__;


if (isset($_POST['form'])) {
    include "../../../db/conn.php";
    include "../../../helps/funcao.php";


    //Load Composer's autoloader
    $retornar = array();
    $acao = $_POST['acao'];
    $tipo = $_POST['tipo'];
    $nomeCookieFrete = "frete_lgbrd";
    $time_cookie = 30 * 24 * 60 * 60;
    $valor_total_venda = 0;
    $peso_total_produto = 0;
    $apiFrete = consulta_tabela('tb_parametros', 'cl_id', '86', 'cl_valor');
    $enderecoRetirada =  utf8_encode(consulta_tabela('tb_parametros', 'cl_id', '91', 'cl_valor')); //retirada do produto

    if ($acao == "consultarFrete") { //consultar o frete, sem opção de selecionar
        foreach ($_POST as $name => $value) { //define os valores das variaveis e os nomes com refencia do name do input no formulario
            ${$name} = $value;
            ${$name} = str_replace("'", "", ${$name}); //remover aspas simples
        }

        $buscarCep = buscar_cep($cep);
        if ($buscarCep['data']['status'] == false) {
            $retornar["errors"]["cep"] = $buscarCep['data']['message'];
        } else {
            if ($apiFrete == "kangu") {
                $dadosCep = $buscarCep['data']['response'];
                $codigo_nf = isset($_POST['codigo_nf']) ? $_POST['codigo_nf'] : '';
                $valor_total_venda = simularValoresFrete($produtoID, $codigo_nf)['valorTotalVenda'];
                $peso_mercadoria = simularValoresFrete($produtoID, $codigo_nf)['pesoMercadoria'];
                $alturaCaixa = simularValoresFrete($produtoID, $codigo_nf)['alturaCaixa'];
                $larguraCaixa = simularValoresFrete($produtoID, $codigo_nf)['larguraCaixa'];
                $comprimentoCaixa = simularValoresFrete($produtoID, $codigo_nf)['comprimentoCaixa'];

                $simular_frete =  simularFreteKangu(
                    $dadosCep,
                    $valor_total_venda,
                    $peso_mercadoria,
                    $alturaCaixa,
                    $larguraCaixa,
                    $comprimentoCaixa,
                    1
                );

                if ($simular_frete['data']['status'] == true) {
                    $simular_frete = $simular_frete['data']['response'];

                    if ($tipo == "consulta") { //cliente apenas irá consultar o frete
                        $html = "
                 
                    <div class='list-group w-auto'>";
                        foreach ($simular_frete as $info) {
                            $idSimulacao = $info["idSimulacao"];
                            $titulo = $info["transp_nome"];
                            $prazoEnt = $info["prazoEnt"];
                            $prazoEntMin = $prazoEnt - 1;

                            $valor = real_format($info["vlrFrete"]);
                            if ($idSimulacao == "retirada") {
                                $p = "<p class='mb-0 fw-semibold'>Retirada<br>$enderecoRetirada</p>";
                            } else {
                                $p = "<p class='mb-0 fw-semibold'>$titulo  <small>($prazoEntMin a $prazoEnt dias úteis)</small></p>";
                            }
                            $borderGratis = ($idSimulacao == "grtestado" or  $idSimulacao == "grtdemaisestado" or $idSimulacao == "grtlocalizacao") ? 'border border-success ' : '';
                            $html .= "<label style='cursor: pointer;' href='#'  class='list-group-item list-group-item-action d-flex gap-3  $borderGratis' aria-current='true'>
                        <!-- <input type='radio' name='selected_simulacao' class='selected_simulacao' value=''> -->
                        <div class='d-flex gap-2 w-100 justify-content-between' >
                            <div>
                              $p
                            </div>
                            <small class='opacity-10 text-nowrap'>$valor</small>
                        </div>
                    </label>";
                        }
                        $html .= "</div>";
                    } elseif ($tipo == "opcao") { //cliente irá selecionar o frete
                        $html = "
                        <label  class='form-label'><i class='bi bi-truck'></i> Opção de frete</label>
                        <div class='list-group w-auto'>";
                        foreach ($simular_frete as $info) {
                            $idSimulacao = $info["idSimulacao"];
                            $titulo = $info["transp_nome"];
                            $prazoEnt = $info["prazoEnt"];
                            $prazoEntMin = $prazoEnt - 1;

                            $valor = real_format($info["vlrFrete"]);
                            if ($idSimulacao == "retirada") {
                                $p = "<p class='mb-0 fw-semibold'>Retirada<br>$enderecoRetirada</p>";
                            } else {
                                $p = "<p class='mb-0 fw-semibold'>$titulo  <small>($prazoEntMin a $prazoEnt dias úteis)</small></p>";
                            }

                            $check = ($idSimulacao == "grtestado" or  $idSimulacao == "grtdemaisestado" or $idSimulacao == "grtlocalizacao") ? 'checked' : '';
                            $borderGratis = ($idSimulacao == "grtestado" or  $idSimulacao == "grtdemaisestado" or $idSimulacao == "grtlocalizacao") ? 'border border-success' : '';
                            $html .= "<label style='cursor: pointer;' href='#' class='list-group-item list-group-item-action d-flex gap-3 $borderGratis' aria-current='true'>
                             <input type='radio' name='selected_simulacao' class='selected_simulacao ' value='$idSimulacao'> 
                            <div class='d-flex gap-2 w-100 justify-content-between ' >
                                <div>
                                    $p
                                </div>
                                <small class='opacity-10 text-nowrap'>$valor</small>
                            </div>
                        </label>";
                        }
                        $html .= "</div>";
                    }
                } else {
                    $retornar["errors"]["option-frete"] = $simular_frete['data']['message'];
                }
            }
        }


        if (isset($retornar["errors"])) {
            $retornar["data"] = array("status" => false, "response" => $retornar["errors"]);
            echo json_encode($retornar); //retornando o array
            exit;
        }

        $retornar["data"] = array("status" => true, "response" => $html, "simulacao" => $simular_frete, "dadosCep" => $dadosCep);

        // // Verificar se a operação foi executada com sucesso
        // if ($execute['data']['status']) { //executado som sucesso
        //     $retornar["data"] = array(
        //         "status" => true, "message" => "Produto adicionado ao carrinho com sucesso",
        //         "qtd_cart" => $qtd_cart
        //     );
        // } else {
        //     if ($execute['data']['type'] == "usuario") { //erro de usuário, validação
        //         $retornar["data"] = array(
        //             "status" => false,
        //             "message" => $execute['data']['message']
        //         );
        //     } else { //erro interno da aplicação
        //         $retornar["data"] = array(
        //             "status" => false,
        //             "message" => "Ops, o site está apresentando um mau funcionamento.
        //             Lamentamos o inconveniente, mas estamos trabalhando para resolver o 
        //             problema o mais rápido possível. Por carrinhoor, tente acessar novamente em alguns minutos"
        //         );
        //         // Registrar log do erro
        //         $mensagem = utf8_decode("Ecommerce - $nome_do_arquivo -" . $execute['data']['message']);
        //         registrar_log($conecta, 'ecommerce', $data, $mensagem);
        //     }
        // }
    }

    if ($acao == "consultarDados") {
        foreach ($_POST as $name => $value) { //define os valores das variaveis e os nomes com refencia do name do input no formulario
            ${$name} = $value;
            ${$name} = str_replace("'", "", ${$name}); //remover aspas simples
        }


        $buscarCep = buscar_cep($cep);
        if ($buscarCep['data']['status'] == false) {
            $retornar["errors"]["cep"] = $buscarCep['data']['message'];
        } else {
            $dadosCep = $buscarCep['data']['response'];
        }

        if (isset($retornar["errors"])) {
            $retornar["data"] = array("status" => false, "response" => $retornar["errors"]);
            echo json_encode($retornar); //retornando o array
            exit;
        }

        $retornar["data"] = array("status" => true,  "dadosCep" => $dadosCep);
    }

    // Encerre a conexão com o banco de dados
    mysqli_close($conecta);
    echo json_encode($retornar); //retornando o array
}
