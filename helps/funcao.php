<?php

date_default_timezone_set('America/Fortaleza');
$data = date('Y-m-d H:i:s');
$hora_atual = date("H:i:s");

$data_lancamento = date('Y-m-d');

$servidor = $_SERVER['SERVER_NAME'];
if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    $protocolo = "https://";
} else {
    $protocolo = "http://";
}
$url_init = $protocolo . $servidor;


///formatar data 
function formatarTimeStamp($value)
{
    if (($value != "") and ($value != "0000-00-00")) {
        $value = date("d/m/Y H:i:s", strtotime($value));
        return $value;
    }
}

function auth($codigo_nf)
{ //usuario logado
    global $conecta;
    // Verificar se o cookie 'lgbrd' está definido
    $sessao = isset($_COOKIE['lgbrd']) ? $_COOKIE['lgbrd'] : '';
    $produtosCart = [];
    $produtosFav = [];
    $somaProdutosQtd = 0;
    $qtd_cart = 0;
    $dados_usuario = [];
    if (!empty($sessao)) {
        $id = consulta_tabela("tb_user_loja", 'cl_sessao', $sessao, 'cl_id');
        $usuario = consulta_tabela("tb_user_loja", 'cl_id', $id, 'cl_nome');

        /*dados do usuario */
        if (!empty($id)) {
            $query = "SELECT * from tb_user_loja as user where user.cl_id = '$id' ";
            $consulta = mysqli_query($conecta, $query);
            $linha = mysqli_fetch_assoc($consulta);
            $data_cadastro = ($linha['cl_data']);
            $nome = utf8_encode($linha['cl_nome']);
            $cpf_cnpj = utf8_encode($linha['cl_cpf_cnpj']);
            $email = ($linha['cl_email']);
            $cep = ($linha['cl_cep']);
            $cidade = utf8_encode($linha['cl_cidade']);
            $telefone = ($linha['cl_telefone']);
            $endereco = utf8_encode($linha['cl_endereco']);
            $bairro = utf8_encode($linha['cl_bairro']);
            $numero = ($linha['cl_numero']);
            $cookie = $linha['cl_cookie'];
            // Create an array to store the information
            $dados_usuario = [
                'id' => $id,
                'data_cadastro' => $data_cadastro,
                'nome' => $nome,
                'cpf_cnpj' => $cpf_cnpj,
                'email' => $email,
                'cep' => $cep,
                'cidade' => $cidade,
                'telefone' => $telefone,
                'endereco' => $endereco,
                'bairro' => $bairro,
                'numero' => $numero,
                'cookie' => $cookie,
            ];
        }

        /*favoritos */
        $query = "SELECT  prd.*,prd.cl_id as idproduto, fav.* FROM tb_favorito_loja as fav left join tb_produtos as prd 
        on prd.cl_id = fav.cl_produto_id where fav.cl_usuario_id = '$id' and prd.cl_status_ativo ='SIM' ";
        $select = mysqli_query($conecta, $query);
        $qtd_fav = mysqli_num_rows($select);
        if ($qtd_fav > 0) {
            while ($row = mysqli_fetch_assoc($select)) { //litar todos os produtos que estão no favoritos do usuário
                $produtosFav[] = $row;
            }
        }

        if (!empty($codigo_nf)) { //venda em andamento
            $query = "SELECT prd.*, prdl.*, prdl.cl_produto_Id as idproduto 
            FROM tb_produto_pedido_loja as prdl 
            inner join tb_produtos as prd on prd.cl_id = prdl.cl_produto_id
             where prdl.cl_codigo_nf ='$codigo_nf' and prd.cl_status_ativo ='SIM' ";
            $select = mysqli_query($conecta, $query);
            $qtd_cart = mysqli_num_rows($select);
            if ($qtd_cart > 0) {
                while ($row = mysqli_fetch_assoc($select)) {
                    $produtosCart[] = $row; // Adicionar informações do produto favorito ao array
                }
            }

            /*somatorio da quantidade */
            $query = "SELECT sum(cl_quantidade) as totalprd from tb_produto_pedido_loja where cl_codigo_nf = '$codigo_nf'  ";
            $select = mysqli_query($conecta, $query);
            $linha = mysqli_fetch_assoc($select);
            $somaProdutosQtd = $linha['totalprd'];
        } else {
            /*carrinho */
            $query = "SELECT prd.*,prd.cl_id as idproduto,cart.* FROM tb_carrinho_loja as cart left join tb_produtos as prd 
        on prd.cl_id = cart.cl_produto_id where cart.cl_usuario_id = '$id' 
        and prd.cl_status_ativo ='SIM' ";
            $select = mysqli_query($conecta, $query);
            $qtd_cart = mysqli_num_rows($select);
            // $produtosCart = array();
            if ($qtd_cart > 0) {
                while ($row = mysqli_fetch_assoc($select)) { //litar todos os produtos que estão no carrinho do usuário
                    $produtosCart[] = $row;
                }
            }

            /*somatorio da quantidade */
            $query = "SELECT sum(cart.cl_quantidade) as totalprd FROM tb_carrinho_loja as cart left join tb_produtos as prd 
            on prd.cl_id = cart.cl_produto_id where cart.cl_usuario_id = '$id' and prd.cl_status_ativo ='SIM' ";
            $select = mysqli_query($conecta, $query);
            $linha = mysqli_fetch_assoc($select);
            $somaProdutosQtd = $linha['totalprd'];
        }




        if (!empty($id)) {
            return array(
                'id' => $id, "nome" => $usuario, "dados_usuario" => $dados_usuario,  "qtd_fav" => $qtd_fav,
                "qtd_cart" => $qtd_cart, 'produtos_cart' => $produtosCart, 'produtos_qtd_total' => $somaProdutosQtd, 'produtos_fav' => $produtosFav // Adiciona o array de produtos ao retorno

            );
        }
    }

    return false;
}


function cookieAuth($codigo_nf)
{ //cookies, usuário não logado
    global $conecta;
    $qtd_fav = 0;
    $qtd_cart = 0;
    $produtosFav = array();
    $produtosCart = array();
    $produtos_qtd_total = 0;
    $produtos_qtd_total = 0;

    if (isset($_COOKIE['fav_lgbrd'])) { //favoritos
        $favCookie = json_decode($_COOKIE["fav_lgbrd"], true);
        $qtd_fav = count($favCookie);

        if ($qtd_fav > 0) {
            foreach ($favCookie as $product) {
                $id = $product['productID'];
                $query = "SELECT prd.*,prd.cl_id as idproduto FROM tb_produtos as prd where prd.cl_id = $id and prd.cl_status_ativo ='SIM' ";
                $select = mysqli_query($conecta, $query);
                $row = mysqli_fetch_assoc($select);
                $qtd_registro = mysqli_num_rows($select);
                if ($qtd_registro > 0) {
                    // Verifica se a linha não está vazia ou nula antes de adicionar ao array
                    $produtosFav[] = $row; // Adicionar informações do produto favorito ao array
                }
            }
        }
    }

    if (!empty($codigo_nf)) { //venda em andamento
        $query = "SELECT prd.*, prdl.*, prdl.cl_produto_id as idproduto 
        FROM tb_produto_pedido_loja as prdl 
        inner join tb_produtos as prd on prd.cl_id = prdl.cl_produto_id
         where prdl.cl_codigo_nf ='$codigo_nf' and prd.cl_status_ativo ='SIM' ";
        $select = mysqli_query($conecta, $query);
        $qtd_cart = mysqli_num_rows($select);
        if ($qtd_cart > 0) {
            while ($row = mysqli_fetch_assoc($select)) {
                $produtosCart[] = $row; // Adicionar informações do produto favorito ao array
            }
        }
        /*somatorio da quantidade */
        $query = "SELECT sum(cl_quantidade) as totalprd from tb_produto_pedido_loja where cl_codigo_nf = '$codigo_nf'  ";
        $select = mysqli_query($conecta, $query);
        $linha = mysqli_fetch_assoc($select);
        $produtos_qtd_total = $linha['totalprd'];
    } else {
        if (isset($_COOKIE['cart_lgbrd'])) { //carrinho
            $cartCookie = json_decode($_COOKIE["cart_lgbrd"], true);
            $qtd_cart = count($cartCookie);

            if ($qtd_cart > 0) {
                foreach ($cartCookie as $product) {
                    $id = $product['productID'];
                    $quantidade = $product['qtd'];
                    $produtos_qtd_total += $quantidade;
                    $query = "SELECT  prd.*,prd.cl_id as idproduto  FROM tb_produtos as prd where cl_id = '$id' and cl_status_ativo ='SIM' ";
                    $select = mysqli_query($conecta, $query);
                    $qtd_registro = mysqli_num_rows($select);
                    if ($qtd_registro > 0) {
                        $row = mysqli_fetch_assoc($select);
                        $row['cl_quantidade'] = $quantidade;


                        // Verifica se a linha não está vazia ou nula antes de adicionar ao array
                        $produtosCart[] = $row; // Adicionar informações do produto favorito ao array
                    }
                }
            }
        }
    }

    return array(
        "qtd_fav" => $qtd_fav, "qtd_cart" => $qtd_cart,
        'produtos_cart' => $produtosCart,
        'produtos_qtd_total' => $produtos_qtd_total, 'produtos_fav' => $produtosFav
    );
}




function verificaProdutoAuth($productID)
{ //verificar se o produto está no carrinho ou no favorito do usuário
    global $conecta;
    // Verificar se o cookie 'lgbrd' está definido
    $sessao = isset($_COOKIE['lgbrd']) ? $_COOKIE['lgbrd'] : '';
    $id = consulta_tabela("tb_user_loja", 'cl_sessao', $sessao, 'cl_id'); //id do usuário

    $qtd_fav = 0;
    $qtd_cart = 0;

    if (!empty($id)) {
        $query = "SELECT fav.* FROM tb_favorito_loja as fav inner join tb_produtos as prd on prd.cl_id = fav.cl_produto_id where fav.cl_usuario_id = '$id'
         and fav.cl_produto_id = '$productID' and prd.cl_status_ativo ='SIM' ";
        $select = mysqli_query($conecta, $query);
        $qtd_fav = mysqli_num_rows($select);


        $query = "SELECT cart.* FROM tb_carrinho_loja as cart inner join  tb_produtos as prd on prd.cl_id = cart.cl_produto_id
             where cart.cl_usuario_id = '$id' and cart.cl_produto_id = '$productID' and prd.cl_status_ativo ='SIM' ";
        $select = mysqli_query($conecta, $query);
        $qtd_cart = mysqli_num_rows($select);
    } else { //não logado
        if (isset($_COOKIE['fav_lgbrd'])) { //favoritos
            $favCookie = json_decode($_COOKIE["fav_lgbrd"], true);
            $productIndex = array_search($productID, array_column($favCookie, 'productID'));
            if ($productIndex !== false) { // Verifica se o produto está nos favoritos
                $qtd_fav = 1; // Define a quantidade de favoritos como 1 se o produto estiver nos favoritos
            }
        }

        if (isset($_COOKIE['cart_lgbrd'])) {
            $cartCookie = json_decode($_COOKIE["cart_lgbrd"], true);
            $productIndex = array_search($productID, array_column($cartCookie, 'productID'));
            if ($productIndex !== false) { // Verifica se o produto está nos favoritos
                $qtd_cart = 1;
            }
        }
    }
    return array("qtd_fav" => $qtd_fav, "qtd_cart" => $qtd_cart);
}


function transferirProduto($userId)
{ //assim que o usuario logar será transferidos do cookie para o banco de dados
    global $conecta;
    global $data;
    $nome_do_arquivo = __FILE__;
    $execute['data'] = array("status" => true);

    if (isset($_COOKIE['cart_lgbrd'])) { //carrinho
        $cartCookie = json_decode($_COOKIE["cart_lgbrd"], true);
        $qtd_cart = count($cartCookie);
        if ($qtd_cart > 0) {
            foreach ($cartCookie as $product) {
                $id = $product['productID'];
                $qtdCookie = $product['qtd'];

                $query = "SELECT * FROM tb_carrinho_loja  where cl_produto_id = '$id' and cl_usuario_id = '$userId' "; //verifica se o produto já no carrinho autenticado do usuario
                $select = mysqli_query($conecta, $query);
                if (!$select) :
                    $execute['data'] = array("status" => false, "message" => 'erro, função transferirProduto(), ao realizar o consulta na tabela tb_carrinho_loja');
                else :
                    $qtd_linha = mysqli_num_rows($select);
                    if ($qtd_linha > 0) :
                        $linha = mysqli_fetch_assoc($select);
                        $id_cart =  $linha['cl_id']; //quantidade do produto no carrinho já existente
                        $qtd_produto =  $linha['cl_quantidade']; //quantidade do produto no carrinho já existente
                        if ($qtdCookie > $qtd_produto) :
                            $valida_estoque = valida_estoque($id, $qtdCookie)['status']; //validar se o estoque vai atender a quantidade que está armazenada no cookie 
                            if ($valida_estoque == true) { //estoque a tende a nova quantidade
                                $update =  update_registro('tb_carrinho_loja', 'cl_id', $id_cart, '', '', 'cl_quantidade', $qtdCookie);
                                if ($update == false) {
                                    $execute['data'] = array("status" => false, "message" => 'erro, função transferirProduto(), ao realizar o update da quantidade do produto no carrinho ');
                                }
                            }
                        endif;
                    else : //inserir o produto no carrinho do usuário autenticado
                        $query = "INSERT INTO tb_carrinho_loja (cl_produto_id,cl_usuario_id,cl_quantidade,cl_data) 
                        VALUES ('$id','$userId','$qtdCookie','$data' )";
                        $insert = mysqli_query($conecta, $query);
                        if (!$insert) {
                            $execute['data'] = array("status" => false, "message" => 'erro, função transferirProduto(), ao realizar o insert do produto no carrinho, cookie para bd');
                        }
                    endif;
                endif;
            }
        }
    }

    if (isset($_COOKIE['fav_lgbrd'])) { //favoritos
        $favCookie = json_decode($_COOKIE["fav_lgbrd"], true);
        $qtd_fav = count($favCookie);
        if ($qtd_fav > 0) {
            foreach ($favCookie as $product) {
                $id = $product['productID'];

                $query = "SELECT * FROM tb_favorito_loja  where cl_produto_id = '$id' and cl_usuario_id = '$userId' "; //verifica se o produto já no carrinho autenticado do usuario
                $select = mysqli_query($conecta, $query);
                if (!$select)
                    $execute['data'] = array("status" => false, "message" => 'erro, função transferirProduto(), 
                ao realizar o consulta na tabela tb_favorito_loja');
                else {
                    $qtd_linha = mysqli_num_rows($select);
                    if ($qtd_linha == 0) {
                        $query = "INSERT INTO tb_favorito_loja (cl_produto_id,cl_usuario_id,cl_data) 
                        VALUES ('$id','$userId','$data' ) ";
                        $insert = mysqli_query($conecta, $query);
                        if (!$insert) {
                            $execute['data'] = array("status" => false, "message" => 'erro, função transferirProduto(), ao realizar
                             o insert do produto no favorito, cookie para bd');
                        }
                    }
                }
            }
        }
    }

    if ($execute['data']['status'] == true) {
        return true;
    } else {
        $mensagem = utf8_decode("Ecommerce - $nome_do_arquivo -" . $execute['data']['message']);
        registrar_log($conecta, 'ecommerce', $data, $mensagem); // Registrar log do err
        return false;
    }
}

//mensagem de alerta cadastro
function required($campo)
{
    return "Insira $campo";
}

function formatDateB($value) //formatar data para padrão 00/00/0000
{
    if (($value != "") and ($value != "0000-00-00")) {
        $value = date("d/m/Y", strtotime($value));
        return $value;
    }
}

//consultar qualuer tabela do bd
function consulta_tabela_query($conecta, $query, $coluna_valor)
{
    $select = $query;
    $consulta_tabela = mysqli_query($conecta, $select);
    $linha = mysqli_fetch_assoc($consulta_tabela);
    if (isset($linha["$coluna_valor"])) {
        $valor = $linha["$coluna_valor"];
    } else {
        // Lógica para lidar com o caso em que o índice não existe
        $valor = ""; // Por exemplo, atribuir um valor padrão
    }
    return $valor;
}

// Consultar todas as linhas de uma tabela dinamicamente
function consulta_linhas_tb_query($conecta, $query)
{
    $select = $query;
    $consulta_tabela = mysqli_query($conecta, $select);

    if (!$consulta_tabela) {
        die("Erro na consulta: " . mysqli_error($conecta));
    }

    $linhas = array();
    while ($linha = mysqli_fetch_assoc($consulta_tabela)) {
        $linhas[] = $linha;
    }

    return $linhas;
}

//consultar qualuer tabela do bd
function consulta_tabela($tabela, $coluna_filtro, $valor, $coluna_valor)
{
    global $conecta;

    $select = "SELECT * from $tabela  ";
    if ($coluna_filtro != "") {
        $select .= " where $coluna_filtro = '$valor' ";
    }
    $consulta_tabela = mysqli_query($conecta, $select);
    $linha = mysqli_fetch_assoc($consulta_tabela);
    if (isset($linha["$coluna_valor"])) {
        $valor = $linha["$coluna_valor"];
    } else {
        // Lógica para lidar com o caso em que o índice não existe
        $valor = ""; // Por exemplo, atribuir um valor padrão
    }
    return $valor;
}
//consultar qualuer tabela do bd
function consulta_tabela_2_filtro($tabela, $coluna_filtro, $valor, $conula_filtro_2, $valor_2, $coluna_valor)
{
    global $conecta;

    $select = "SELECT * from $tabela  ";
    if ($coluna_filtro != "") {
        $select .= " where $coluna_filtro = '$valor' ";
    }
    if ($conula_filtro_2 != "") {
        $select .= " and $conula_filtro_2 = '$valor_2' ";
    }

    $consulta_tabela = mysqli_query($conecta, $select);
    $linha = mysqli_fetch_assoc($consulta_tabela);
    if (isset($linha["$coluna_valor"])) {
        $valor = $linha["$coluna_valor"];
    } else {
        // Lógica para lidar com o caso em que o índice não existe
        $valor = ""; // Por exemplo, atribuir um valor padrão
    }
    return $valor;
}


// Consultar todas as linhas de uma tabela dinamicamente
function consulta_linhas_tb($tabela, $filtro, $valor_filtro, $filtro2, $valor_filtro2)
{
    global $conecta;
    $select = "SELECT * from $tabela  ";
    if ($filtro != "") {
        $select .= " where $filtro = '$valor_filtro' ";
    }

    if ($filtro2 != "") {
        $select .= "  and $filtro2 = '$valor_filtro2'  ";
    }

    $consulta_tabela = mysqli_query($conecta, $select);

    if (!$consulta_tabela) {
        die("Erro na consulta: " . mysqli_error($conecta));
    }

    $linhas = array();
    while ($linha = mysqli_fetch_assoc($consulta_tabela)) {
        $linhas[] = $linha;
    }

    return $linhas;
}

//atualizar registro
function update_registro($tabela, $coluna_filtro, $valor_filtro, $coluna_filtro2, $valor_filtro2, $coluna_referencia, $valor_referencia)
{
    global $conecta;
    $update = "UPDATE $tabela SET $coluna_referencia = '$valor_referencia'  WHERE $coluna_filtro ='$valor_filtro'";
    if ($coluna_filtro2 != "") {
        $update .= " and $coluna_filtro2 = '$valor_filtro2' ";
    }
    $operacao_update  = mysqli_query($conecta, $update);
    if ($operacao_update) {
        return true;
    } else {
        return false;
    }
}

function delete_registro($tabela, $coluna_filtro, $valor_filtro, $coluna_filtro2, $valor_filtro2)
{
    global $conecta;
    $delete = "Delete from $tabela WHERE $coluna_filtro ='$valor_filtro'";
    if ($coluna_filtro2 != "") {
        $delete .= " and $coluna_filtro2 = '$valor_filtro2' ";
    }
    $operacao_delete  = mysqli_query($conecta, $delete);
    if ($operacao_delete) {
        return true;
    } else {
        return false;
    }
}



//registrar log da acão
function registrar_log($conecta, $nome_usuario_logado, $data, $mensagem)
{
    $inset = "INSERT INTO tb_log (cl_data_modificacao,cl_usuario,cl_descricao) VALUES ('$data','$nome_usuario_logado','$mensagem')";
    $operacao_inserir = mysqli_query($conecta, $inset);
    return $operacao_inserir;
}

function verficar_paramentro($conecta, $tabela, $filtro, $valor)
{
    $select = "SELECT * from $tabela where $filtro = $valor";
    $consultar_parametros = mysqli_query($conecta, $select);
    $linha = mysqli_fetch_assoc($consultar_parametros);
    $valor_parametro = $linha['cl_valor'];
    return $valor_parametro;
}

//formatar para moeda real
function real_format($valor)
{
    $valor  = number_format($valor, 2, ",", ".");
    return "R$ " . $valor;
}


//verificar se tem virgula na string
function verificaVirgula($valor)
{
    if (strpos($valor, ',') !== false) {
        // echo "A string contém uma vírgula.";
        return true;
    } else {
        // echo "A string não contém uma vírgula.";
        return false;
    }
}

//substituir uma virgula por um ponto
function formatDecimal($valor)
{
    $string_com_virgula = $valor;
    $string_com_ponto = str_replace(",", ".", $string_com_virgula);
    return $string_com_ponto;
}


function formatarNumero($numero)
{
    if (is_int($numero)) {
        return number_format($numero, 2);
    } else {
        return number_format($numero, 0);
    }
}

function valida_estoque($produto_id, $quantidade)
{
    $estoque = consulta_tabela("tb_produtos", 'cl_id', $produto_id, "cl_estoque");
    if ($quantidade > $estoque) {
        $retornar["data"] = array(
            "status" => false,
            "message" => "Estoque não atende a essa quantidade"
        );
    } else {
        $retornar["data"] = array(
            "status" => true,
            "message" => "ok"
        );
    }
    return $retornar["data"];
}


function sendEmail($mail, $email_destinatario, $assunto, $attbody, $html) //enviar email
{
    global $conecta;
    $host_email = consulta_tabela('tb_parametros', 'cl_id', '73', "cl_valor");
    $nome_ecommerce = consulta_tabela('tb_parametros', 'cl_id', '64', "cl_valor");
    $email_remetente = consulta_tabela('tb_parametros', 'cl_id', '74', "cl_valor");
    $senha_email = consulta_tabela('tb_parametros', 'cl_id', '62', "cl_valor");
    $porta_email = consulta_tabela('tb_parametros', 'cl_id', '77', "cl_valor");
    $nome_fantasia = utf8_encode(consulta_tabela('tb_empresa', 'cl_id', '1', "cl_nome_fantasia"));

    $whatsap = (consulta_tabela('tb_parametros', 'cl_id', 44, 'cl_valor')); //numero whatsap
    $instagram = (consulta_tabela('tb_parametros', 'cl_id', 43, 'cl_valor')); //link do instagram
    $facebook = (consulta_tabela('tb_parametros', 'cl_id', 80, 'cl_valor')); //link do facebook
    $email = (consulta_tabela('tb_parametros', 'cl_id', 74, 'cl_valor')); //email para contato
    $telefone = (consulta_tabela('tb_parametros', 'cl_id', 81, 'cl_valor')); //telefone para contato


    try {
        // Configurações do servidor SMTP
        $mail->isSMTP();
        $mail->Host = $host_email;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Username = $email_remetente;
        $mail->Password = $senha_email;
        $mail->Port = $porta_email;
        $mail->CharSet = 'UTF-8'; // Configuração da codificação

        // Define o remetente
        $mail->setFrom($email_remetente, $nome_ecommerce);
        // Define o destinatário
        $mail->addAddress($email_destinatario);
        $mail->Subject = $assunto;
        $mail->AltBody = $attbody; // Caso o cliente de e-mail não suporte HTML

        $html = $html . "<div style='max-width: 700px; margin-left: auto; margin-right: auto;'>
            <div  style='text-align: center;'>
              <p>Se você tiver alguma dúvida, responda a esta mensagem ou entre em contato conosco pelo e‑mail 
              <a href='mailto:$email_remetente'>$email_remetente</a>.
              </p>
            </div>
            <hr>
            <div style='text-align: center;'>
            <p style='font-size: 0.8em;'> Atenciosamente, $nome_fantasia</p>
                <a href='https://api.whatsapp.com/send?phone=$whatsap'><img src='https://i.pinimg.com/564x/26/88/29/268829190281a967d829180a7e0db375.jpg' alt='Whatsapp' style='width: 30px;'></a>
                <a href='$instagram'><img src='https://toppng.com/uploads/preview/ew-instagram-logo-transparent-related-keywords-logo-instagram-vector-2017-115629178687gobkrzwak.png' alt='Instagram' style='width: 30px;'></a>
                <a href='$facebook'><img src='https://i1.wp.com/www.multarte.com.br/wp-content/uploads/2019/03/logo-facebook-png.png?fit=696%2C696&ssl=1' alt='Facebook' style='width: 30px; margin-left: 5px;'></a>
            </div>

        <div>";

        $mail->Body = $html;

        // Envia o e-mail
        $mail->send();
        // update_registro("tb_pre_venda", 'cl_id', $external_reference, "", "", "cl_email_verificado", 1);
        return true;
    } catch (Exception $e) {
        // update_registro("tb_pre_venda", 'cl_id', $external_reference, "", "", "cl_email_verificado", 0);
        return false;
        // echo 'Erro ao enviar o e-mail: ', $mail->ErrorInfo;
    }
}


function sendEmailConfirm($mail, $assunto, $attbody, $html)
{
    $host_email = consulta_tabela('tb_parametros', 'cl_id', '73', "cl_valor");
    $nome_ecommerce = consulta_tabela('tb_parametros', 'cl_id', '64', "cl_valor");
    $email_remetente = consulta_tabela('tb_parametros', 'cl_id', '74', "cl_valor");
    $senha_email = consulta_tabela('tb_parametros', 'cl_id', '62', "cl_valor");
    $porta_email = consulta_tabela('tb_parametros', 'cl_id', '77', "cl_valor");
    $nome_fantasia = utf8_encode(consulta_tabela('tb_empresa', 'cl_id', '1', "cl_nome_fantasia"));

    $whatsapp = consulta_tabela('tb_parametros', 'cl_id', 44, 'cl_valor'); // número WhatsApp
    $instagram = consulta_tabela('tb_parametros', 'cl_id', 43, 'cl_valor'); // link do Instagram
    $facebook = consulta_tabela('tb_parametros', 'cl_id', 80, 'cl_valor'); // link do Facebook
    $email = consulta_tabela('tb_parametros', 'cl_id', 74, 'cl_valor'); // email para contato
    $telefone = consulta_tabela('tb_parametros', 'cl_id', 81, 'cl_valor'); // telefone para contato
    $email_destinatarios_param = consulta_tabela('tb_parametros', 'cl_id', 98, 'cl_valor'); // Endereço de email que irão receber email de confirmação - loja (separado por espaço)
    $email_destinatarios_array = explode(' ', $email_destinatarios_param);

    try {
        // Configurações do servidor SMTP
        $mail->isSMTP();
        $mail->Host = $host_email;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Username = $email_remetente;
        $mail->Password = $senha_email;
        $mail->Port = $porta_email;
        $mail->CharSet = 'UTF-8'; // Configuração da codificação

        // Define o remetente
        $mail->setFrom($email_remetente, $nome_ecommerce);
        // Adiciona os destinatários
        $html = "<div style='max-width: 700px; margin-top: 5rem; margin-left: auto; margin-right: auto;'>
        <h3>Novo Pedido!</h3>
        <p>verifique e confirme o pedido:</p>
        </div>" . $html;

        foreach ($email_destinatarios_array as $email_destinatario) {
            // Adiciona o destinatário ao e-mail
            $mail->addAddress(trim($email_destinatario));
        }


        $mail->Subject = $assunto;
        $mail->AltBody = $attbody; // Caso o cliente de e-mail não suporte HTML
        $mail->Body = $html;

        // Envia o e-mail
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}



function buscar_cep($cep)
{

    // Remova caracteres não numéricos do CEP
    $cep_replace = preg_replace("/[^0-9]/", "", $cep);

    // Construa a URL com o CEP
    $tamanhoDaString = strlen($cep_replace);

    if ($tamanhoDaString == 8) {
        $url = "https://viacep.com.br/ws/{$cep_replace}/json/";

        // Faça a requisição usando file_get_contents
        $response = file_get_contents($url);

        if ($response) {
            // Decodifique a resposta JSON
            $data = json_decode($response, true);

            // Verifique se a requisição foi bem-sucedida
            if ($data !== null && !isset($data['erro'])) {
                // Os dados estão agora no array associativo $data
                // Faça o que for necessário com os dados aqui

                $retornar["data"] = array("status" => true, "response" => $data);
            } else {
                // Trate caso ocorra algum erro na requisição ou se o CEP for inválido
                $retornar["data"] = array("status" => false, "message" => "CEP inválido");
            }
        } else {
            $retornar["data"] = array("status" => false, "message" => "CEP inválido");
        }
    } else {
        $retornar["data"] = array("status" => false, "message" => "CEP inválido");
    }

    return $retornar;
}



function simularFreteKangu(
    $responseCep,
    $vlrMerc,
    $peso_mercadoria,
    $altura,
    $largura,
    $comprimento,
    $quantidade
) {

    /*parametros */

    $retornar = array();

    $tokenFrete = consulta_tabela('tb_parametros', 'cl_id', '67', 'cl_valor'); //token do frete


    $freteGratis = consulta_tabela('tb_parametros', 'cl_id', '87', 'cl_valor');
    $freteCondicaoValorEstado = consulta_tabela('tb_parametros', 'cl_id', '88', 'cl_valor'); //FRETE GRATIS PARA DENTRO DO ESTADO
    $freteCondicaoValorForaEstado = consulta_tabela('tb_parametros', 'cl_id', '89', 'cl_valor'); //FRETE GRATIS PARA FORA DO ESTADO
    $valor_entrega_local = consulta_tabela('tb_parametros', 'cl_id', '100', 'cl_valor');

    /*empresa */
    $estado_empresa = consulta_tabela('tb_empresa', 'cl_id', '1', 'cl_estado'); //estado da empresa
    $cepOrigem = consulta_tabela('tb_empresa', 'cl_id', '1', 'cl_cep'); //cep da empresa
    $cepOrigem = preg_replace("/[^0-9]/", "", $cepOrigem);

    $retiradaProduto = consulta_tabela('tb_parametros', 'cl_id', '90', 'cl_valor'); //retirar produto no estabelecimento
    $cepFreteGratis = consulta_tabela('tb_parametros', 'cl_id', '92	', 'cl_valor'); //cep para a mesma cidade ou redondezas

    $qtdDiasEntregaLocal = consulta_tabela('tb_parametros', 'cl_id', '93', 'cl_valor'); //quantida de dias para frete gratis
    $nome_fantasia =  utf8_encode(consulta_tabela('tb_empresa', 'cl_id', '1', 'cl_nome_fantasia'));


    $pFreteGratisEstado = false;
    $pFreteGratisForaEstado = false;

    if (!isset($responseCep['uf'])) { //validar a função buscar_cep
        $retornar["data"] = array("status" => false, "message" => 'Formato inválido para o CEP');
        return $retornar;
    }

    if ($freteGratis == "true" and $estado_empresa == ($responseCep['uf'])) {
        if ($vlrMerc >=  $freteCondicaoValorEstado) { //frete gratis para dentro do estado acima ou igual do valor que está no parametro parametro
            $pFreteGratisEstado = true;
        }
    } elseif ($freteGratis == "true" and $estado_empresa != ($responseCep['uf'])) {
        if ($vlrMerc >= $freteCondicaoValorForaEstado) { //frete gratis para fora do estado acima ou igual do valor que está no parametro parametro
            $pFreteGratisForaEstado = true;
        }
    }


    $url = "https://portal.kangu.com.br/tms/transporte/simular";
    $headers = array(
        "accept: application/json",
        "token: $tokenFrete",
        "Content-Type: application/json"
    );

    $data = array(
        "cepOrigem" => $cepOrigem,
        "cepDestino" => $responseCep['cep'],
        "vlrMerc" => $vlrMerc,
        "pesoMerc" => $peso_mercadoria,
        "volumes" => array(
            array(
                "peso" => $peso_mercadoria,
                "altura" => $altura,
                "largura" => $largura,
                "comprimento" => $comprimento,
                "tipo" => "string",
                "valor" => $vlrMerc,
                "quantidade" => $quantidade
            )
        ),
        "servicos" => array("string"),
        "ordernar" => "string"
    );

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $response = json_decode($response, true);

    //$cep = substr($cep, 0, 3);

    if ($httpCode == "200") { //cpde 200 Operação bem sucedida

        $cepDestino = $responseCep['cep'];
        $cepDestino = substr($cepDestino, 0, 3); // Mantém apenas os três primeiros dígitos do CEP de origem
        $cepsFreteGratis = explode(" ", $cepFreteGratis); // Divide os CEPs em um array
        $encontrado = false; // Variável para indicar se algum CEP foi encontrado

        foreach ($cepsFreteGratis as $cep) {
            if ($cep == $cepDestino) {
                // O valor de $cepOrigem está presente em $cepFreteGratis
                $encontrado = true;
                break; // Sai do loop assim que encontrar um CEP igual
            }
        }

        if (!empty($cepFreteGratis) and $encontrado == true) { //frete gratis apenas para algumas regiões do mesmo estado da loja
            $response[] = array(
                "cnpjTransp" => "99999999000000",
                "cnpjTranspResp" => "99999999000000",
                "descricao" => "Frete",
                "dtPrevEnt" => "2024-04-02 14:05:44",
                "dtPrevEntMin" => "2024-04-02 14:05:44",
                "error" => array(
                    "codigo" => "",
                    "mensagem" => ""
                ),
                "idSimulacao" => "grtlocalizacao",
                "idTransp" => 0,
                "idTranspResp" => 0,
                "nf_obrig" => "N",
                "prazoEnt" => $qtdDiasEntregaLocal,
                "prazoEntMin" => $qtdDiasEntregaLocal,
                "referencia" => "kangu_X_99999999000000_1192230637",
                "servico" => "X",
                "tarifas" => array(
                    array(
                        "valor" => $valor_entrega_local,
                        "descricao" => "Frete "
                    )
                ),
                "transp_nome" => "Frete - entregue pela $nome_fantasia",
                "url_logo" => "https://portal.kangu.com.br/ged/documento/download/file/3962/Logo_Correios.png",
                "vlrFrete" => $valor_entrega_local
            );
        } elseif ($pFreteGratisEstado == true) { //frete gratis para fora do estado
            $response[] = array(
                "cnpjTransp" => "99999999000000",
                "cnpjTranspResp" => "99999999000000",
                "descricao" => "Correios Sedex via Kangu",
                "dtPrevEnt" => "2024-04-02 14:05:44",
                "dtPrevEntMin" => "2024-04-02 14:05:44",
                "error" => array(
                    "codigo" => "",
                    "mensagem" => ""
                ),
                "idSimulacao" => "grtestado",
                "idTransp" => 0,
                "idTranspResp" => 0,
                "nf_obrig" => "N",
                "prazoEnt" => 10,
                "prazoEntMin" => 10,
                "referencia" => "kangu_X_99999999000000_1192230637",
                "servico" => "X",
                "tarifas" => array(
                    array(
                        "valor" => 0,
                        "descricao" => "Frete Grátis"
                    )
                ),
                "transp_nome" => "Frete Grátis",
                "url_logo" => "https://portal.kangu.com.br/ged/documento/download/file/3962/Logo_Correios.png",
                "vlrFrete" => 0
            );
        }
        if ($pFreteGratisForaEstado == true) { //frete gratis para dentro do estado
            $response[] = array(
                "cnpjTransp" => "99999999000000",
                "cnpjTranspResp" => "99999999000000",
                "descricao" => "Grátis",
                "dtPrevEnt" => "2024-04-02 14:05:44",
                "dtPrevEntMin" => "2024-04-02 14:05:44",
                "error" => array(
                    "codigo" => "",
                    "mensagem" => ""
                ),
                "idSimulacao" => "grtdemaisestado",
                "idTransp" => 0,
                "idTranspResp" => 0,
                "nf_obrig" => "N",
                "prazoEnt" => 10,
                "prazoEntMin" => 10,
                "referencia" => "kangu_X_99999999000000_1192230637",
                "servico" => "X",
                "tarifas" => array(
                    array(
                        "valor" => 0,
                        "descricao" => "Frete Grátis"
                    )
                ),
                "transp_nome" => "Frete Grátis",
                "url_logo" => "https://portal.kangu.com.br/ged/documento/download/file/3962/Logo_Correios.png",
                "vlrFrete" => 0
            );
        }

        if ($retiradaProduto == "S") { //retirada de produtos na loja
            $response[] = array(
                "cnpjTransp" => "99999999000000",
                "cnpjTranspResp" => "99999999000000",
                "descricao" => "Retirada",
                "dtPrevEnt" => "2024-04-02 14:05:44",
                "dtPrevEntMin" => "2024-04-02 14:05:44",
                "error" => array(
                    "codigo" => "",
                    "mensagem" => ""
                ),
                "idSimulacao" => "retirada",
                "idTransp" => 0,
                "idTranspResp" => 0,
                "nf_obrig" => "N",
                "prazoEnt" => 0,
                "prazoEntMin" => 0,
                "referencia" => "retirada",
                "servico" => "X",
                "tarifas" => array(
                    array(
                        "valor" => 0,
                        "descricao" => "Retirada"
                    )
                ),
                "transp_nome" => "Retirada",
                "url_logo" => "https://portal.kangu.com.br/ged/documento/download/file/3962/Logo_Correios.png",
                "vlrFrete" => 0
            );
        }



        $retornar["data"] = array("status" => true, "response" => $response);
    } else { //code 400
        $errorCode = $response['error']['codigo'];
        $errorMessage = $response['error']['Mensagem'];

        switch ($errorCode) {
            case 500:
                $errorMessage = "Erro interno no servidor: " . $errorMessage;
                break;
            case 815:
                $errorMessage = "Um dos volumes não tem peso";
                break;
            case 816:
                $errorMessage = "Um dos volumes não tem altura";
                break;
            case 817:
                $errorMessage = "Um dos volumes não tem comprimento";
                break;
            case 870:
                $errorMessage = "Não foi possível determinar a origem da Mercadoria! CEP inválido";
                break;
            case 871:
                $errorMessage = "Não foi possível determinar o destino da Mercadoria! CEP inválido";
                break;
            case 873:
                $errorMessage = "Não foi possível identificar o serviço de postagem. Código 'P'.";
                break;
            default:
                $errorMessage = "Erro desconhecido: " . $errorMessage;
                break;
        }

        $retornar["data"] = array("status" => false, "message" => $errorMessage);
    }

    if (curl_errno($ch)) {
        $retornar["data"] = array("status" => false, "message" => curl_error($ch));
    }

    curl_close($ch);
    return $retornar;
}


function simularValoresFrete($produtoID, $codigo_nf)
{
    global $data_lancamento;
    global $conecta;
    $valorTotalVenda = 0;
    $pesoTotalProduto = 0;
    if (!empty($codigo_nf)) { //venda em andamento
        if (auth('') !== false) {
            $qtd_produtos = auth($codigo_nf)['produtos_qtd_total'];
            $qtd_cart = auth($codigo_nf)['qtd_cart'];
            $produtoCart = auth($codigo_nf)['produtos_cart'];
        } else {
            $qtd_produtos = cookieAuth($codigo_nf)['produtos_qtd_total'];
            $qtd_cart = cookieAuth($codigo_nf)['qtd_cart'];
            $produtoCart = cookieAuth($codigo_nf)['produtos_cart'];
        }

        $query = "SELECT *
        FROM tb_modelo_caixa_ecommerce
        WHERE cl_limite_produto >= $qtd_produtos
        ORDER BY cl_limite_produto ASC 
        LIMIT 1 ";
        $select = mysqli_query($conecta, $query);
        $linha = mysqli_fetch_assoc($select);
        $alturaCaixa = $linha['cl_altura'];
        $comprimentoCaixa = $linha['cl_comprimento'];
        $larguraCaixa = $linha['cl_largura'];
        $peso_caixa = $linha['cl_peso'];

        foreach ($produtoCart as $product) {
            $peso_produto = $product['cl_peso_produto'];
            $preco_venda = ($product['cl_preco_venda']);
            $preco_promocao = ($product['cl_preco_promocao']);
            $data_validade_promocao = ($product['cl_data_valida_promocao']);
            $quantidade = ($product['cl_quantidade']);

            if (($data_validade_promocao >= $data_lancamento) and $preco_promocao > 0) {
                $preco_venda = $preco_promocao * $quantidade;
            } else {
                // Se não houver promoção, mostrar apenas o preço normal e centralizar
                $preco_venda = $preco_venda * $quantidade;
            }
            $valorTotalVenda += $preco_venda;
            $pesoTotalProduto += $peso_produto;
        }

        $peso_mercadoria = $peso_caixa  + ($pesoTotalProduto * $qtd_produtos);
        $retornar["data"] = array(
            "status" => true,
            "pesoMercadoria" => $peso_mercadoria,
            "valorTotalVenda" => $valorTotalVenda,
            "alturaCaixa" => $alturaCaixa,
            "larguraCaixa" => $larguraCaixa,
            "comprimentoCaixa" => $comprimentoCaixa,
        );
    } elseif (auth('') !== false) { //usuario logado
        $qtd_produtos = auth('')['produtos_qtd_total'];
        $qtd_cart = auth('')['qtd_cart'];
        $produtoCart = auth('')['produtos_cart'];
        if (empty($produtoID)) { //consultar para todos os produtos que estão no carrinho
            $query = "SELECT *
        FROM tb_modelo_caixa_ecommerce
        WHERE cl_limite_produto >= $qtd_produtos
        ORDER BY cl_limite_produto ASC 
        LIMIT 1 ";
            $select = mysqli_query($conecta, $query);
            $linha = mysqli_fetch_assoc($select);
            $alturaCaixa = $linha['cl_altura'];
            $comprimentoCaixa = $linha['cl_comprimento'];
            $larguraCaixa = $linha['cl_largura'];
            $peso_caixa = $linha['cl_peso'];

            foreach ($produtoCart as $product) {
                $peso_produto = $product['cl_peso_produto'];
                $preco_venda = ($product['cl_preco_venda']);
                $preco_promocao = ($product['cl_preco_promocao']);
                $data_validade_promocao = ($product['cl_data_valida_promocao']);
                $quantidade = ($product['cl_quantidade']);

                if (($data_validade_promocao >= $data_lancamento) and $preco_promocao > 0) {
                    $preco_venda = $preco_promocao * $quantidade;
                } else {
                    // Se não houver promoção, mostrar apenas o preço normal e centralizar
                    $preco_venda = $preco_venda * $quantidade;
                }
                $valorTotalVenda += $preco_venda;
                $pesoTotalProduto += $peso_produto;
            }

            $peso_mercadoria = $peso_caixa  + ($pesoTotalProduto * $qtd_produtos);
            $retornar["data"] = array(
                "status" => true,
                "pesoMercadoria" => $peso_mercadoria,
                "valorTotalVenda" => $valorTotalVenda,
                "alturaCaixa" => $alturaCaixa,
                "larguraCaixa" => $larguraCaixa,
                "comprimentoCaixa" => $comprimentoCaixa,
            );
        } else {
            $query = "SELECT * FROM tb_produtos where cl_id = '$produtoID' ";
            $select = mysqli_query($conecta, $query);
            $linha = mysqli_fetch_assoc($select);
            $peso_produto = $linha['cl_peso_produto'];
            $preco_venda = ($linha['cl_preco_venda']);
            $preco_promocao = ($linha['cl_preco_promocao']);
            $data_validade_promocao = ($linha['cl_data_valida_promocao']);
            if (($data_validade_promocao >= $data_lancamento) and $preco_promocao > 0) {
                $preco_venda = $preco_promocao * 1;
            } else {
                // Se não houver promoção, mostrar apenas o preço normal e centralizar
                $preco_venda = $preco_venda * 1;
            }
            $valorTotalVenda += $preco_venda;
            $pesoTotalProduto += $peso_produto;

            $query = "SELECT * FROM tb_modelo_caixa_ecommerce WHERE cl_limite_produto >= 1
             ORDER BY cl_limite_produto ASC LIMIT 1 ";
            $select = mysqli_query($conecta, $query);
            $linha = mysqli_fetch_assoc($select);
            $alturaCaixa = $linha['cl_altura'];
            $comprimentoCaixa = $linha['cl_comprimento'];
            $larguraCaixa = $linha['cl_largura'];
            $peso_caixa = $linha['cl_peso'];

            $peso_mercadoria = $peso_caixa  + ($pesoTotalProduto * $qtd_produtos);
            $retornar["data"] = array(
                "status" => true,
                "pesoMercadoria" => $peso_mercadoria,
                "valorTotalVenda" => $valorTotalVenda,
                "alturaCaixa" => $alturaCaixa,
                "larguraCaixa" => $larguraCaixa,
                "comprimentoCaixa" => $comprimentoCaixa,
            );
        }
    } else {
        $qtd_produtos = cookieAuth('')['produtos_qtd_total'];
        $qtd_cart = cookieAuth('')['qtd_cart'];
        $produtoCart = cookieAuth('')['produtos_cart'];
        if (empty($produtoID)) { //verificar pelo carrinho
            $query = "SELECT *
            FROM tb_modelo_caixa_ecommerce
            WHERE cl_limite_produto >= $qtd_produtos
            ORDER BY cl_limite_produto ASC 
            LIMIT 1 ";
            $select = mysqli_query($conecta, $query);
            $linha = mysqli_fetch_assoc($select);
            $alturaCaixa = $linha['cl_altura'];
            $comprimentoCaixa = $linha['cl_comprimento'];
            $larguraCaixa = $linha['cl_largura'];
            $peso_caixa = $linha['cl_peso'];

            foreach ($produtoCart as $product) {
                $peso_produto = $product['cl_peso_produto'];
                $preco_venda = ($product['cl_preco_venda']);
                $preco_promocao = ($product['cl_preco_promocao']);
                $data_validade_promocao = ($product['cl_data_valida_promocao']);
                $quantidade = ($product['cl_quantidade']);

                if (($data_validade_promocao >= $data_lancamento) and $preco_promocao > 0) {
                    $preco_venda = $preco_promocao * $quantidade;
                } else {
                    // Se não houver promoção, mostrar apenas o preço normal e centralizar
                    $preco_venda = $preco_venda * $quantidade;
                }
                $valorTotalVenda += $preco_venda;
                $pesoTotalProduto += $peso_produto;
            }

            $peso_mercadoria = $peso_caixa  + ($pesoTotalProduto * $qtd_produtos);
            $retornar["data"] = array(
                "status" => true,
                "pesoMercadoria" => $peso_mercadoria,
                "valorTotalVenda" => $valorTotalVenda,
                "alturaCaixa" => $alturaCaixa,
                "larguraCaixa" => $larguraCaixa,
                "comprimentoCaixa" => $comprimentoCaixa,
            );
        } else { //verificar pelo produto em especifico
            $query = "SELECT * FROM tb_produtos where cl_id = '$produtoID' ";
            $select = mysqli_query($conecta, $query);
            $linha = mysqli_fetch_assoc($select);
            $peso_produto = $linha['cl_peso_produto'];
            $preco_venda = ($linha['cl_preco_venda']);
            $preco_promocao = ($linha['cl_preco_promocao']);
            $data_validade_promocao = ($linha['cl_data_valida_promocao']);
            if (($data_validade_promocao >= $data_lancamento) and $preco_promocao > 0) {
                $preco_venda = $preco_promocao * 1;
            } else {
                // Se não houver promoção, mostrar apenas o preço normal e centralizar
                $preco_venda = $preco_venda * 1;
            }
            $valorTotalVenda += $preco_venda;
            $pesoTotalProduto += $peso_produto;

            $query = "SELECT * FROM tb_modelo_caixa_ecommerce WHERE cl_limite_produto >= 1
             ORDER BY cl_limite_produto ASC LIMIT 1 ";
            $select = mysqli_query($conecta, $query);
            $linha = mysqli_fetch_assoc($select);
            $alturaCaixa = $linha['cl_altura'];
            $comprimentoCaixa = $linha['cl_comprimento'];
            $larguraCaixa = $linha['cl_largura'];
            $peso_caixa = $linha['cl_peso'];

            $peso_mercadoria = $peso_caixa  + ($pesoTotalProduto * $qtd_produtos);
            $retornar["data"] = array(
                "status" => true,
                "pesoMercadoria" => $peso_mercadoria,
                "valorTotalVenda" => $valorTotalVenda,
                "alturaCaixa" => $alturaCaixa,
                "larguraCaixa" => $larguraCaixa,
                "comprimentoCaixa" => $comprimentoCaixa,
            );
        }
    }
    return $retornar['data'];
}

function consultaQueryBd($query)
{
    global $conecta;
    $select = $query;
    $consulta = mysqli_query($conecta, $select);
    $linhas = array();
    while ($linha = mysqli_fetch_assoc($consulta)) {
        $linhas[] = $linha;
    }

    return $linhas;
}


function validarCPF($cpf)
{
    // Remove caracteres não numéricos
    $cpf = preg_replace('/[^0-9]/', '', $cpf);

    // Verifica se o CPF possui 11 dígitos
    if (strlen($cpf) != 11) {
        return false;
    }

    // Verifica se todos os dígitos são iguais
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

    // Calcula os dígitos verificadores
    for ($i = 9; $i < 11; $i++) {
        $soma = 0;
        for ($j = 0; $j < $i; $j++) {
            $soma += $cpf[$j] * (($i + 1) - $j);
        }
        $resto = $soma % 11;
        $digito = $resto < 2 ? 0 : 11 - $resto;
        if ($cpf[$i] != $digito) {
            return false;
        }
    }

    return true;
}


//validar cnpj
function validarCNPJ($cnpj)
{
    // Remove caracteres especiais
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

    // Verifica se o CNPJ possui 14 dígitos
    if (strlen($cnpj) != 14) {
        return false;
    }

    // Verifica se todos os dígitos são iguais
    if (preg_match('/(\d)\1{13}/', $cnpj)) {
        return false;
    }

    // Verifica o primeiro dígito verificador
    $soma = 0;
    for ($i = 0; $i < 12; $i++) {
        $soma += intval($cnpj[$i]) * (($i < 4) ? 5 - $i : 13 - $i);
    }
    $digito1 = (($soma % 11) < 2) ? 0 : 11 - ($soma % 11);
    if ($cnpj[12] != $digito1) {
        return false;
    }

    // Verifica o segundo dígito verificador
    $soma = 0;
    for ($i = 0; $i < 13; $i++) {
        $soma += intval($cnpj[$i]) * (($i < 5) ? 6 - $i : 14 - $i);
    }
    $digito2 = (($soma % 11) < 2) ? 0 : 11 - ($soma % 11);
    if ($cnpj[13] != $digito2) {
        return false;
    }

    // Se chegou até aqui, o CNPJ é válido
    return true;
}


function calcularValorCarrinho($payment, $simulacaoFrete, $selectedIdSimulacao, $codigo_nf, $cupom)
{
    $valorFrete = 0;
    $valorDesconto = 0;
    $valorSubTotal = 0;
    $valorTotal = 0;
    $valorCupom = 0;
    $prazoEnt = "";
    $transp_nome = "";
    $idSimulacao = "";
    $msgCupom = "";
    global $data_lancamento;
    global $conecta;

    if (auth('') != false) {
        $dados_usuario = auth($codigo_nf); // Supondo que a função auth('') retorna os dados do usuário e dos produtos
    } else {
        $dados_usuario = cookieAuth($codigo_nf);
    }


    if ($dados_usuario) { //subtotal
        $produtosCart = $dados_usuario['produtos_cart'];
        $qtd_cart = $dados_usuario['qtd_cart'];
        if ($qtd_cart > 0) {

            if (($simulacaoFrete) != '') { //simulação de frete, valor do frete escolhido
                foreach ($simulacaoFrete as $simulacao) {
                    if ($simulacao['idSimulacao'] == $selectedIdSimulacao) {
                        $valorFrete = $simulacao['vlrFrete'];
                        $transp_nome = $simulacao['transp_nome'];
                        $prazoEnt = $simulacao['prazoEnt'];
                        $idSimulacao = $simulacao['idSimulacao'];
                        break; // Encerra o loop quando encontrar o vlrFrete correspondente
                    }
                }
            }

            foreach ($produtosCart as $linha) { //valor total dos produtos no carrinho 
                $id = $linha['idproduto'];
                $quantidade = ($linha['cl_quantidade']);
                $preco_venda = ($linha['cl_preco_venda']);
                $preco_promocao = ($linha['cl_preco_promocao']);
                $data_validade_promocao = ($linha['cl_data_valida_promocao']);
                $estoque = $linha['cl_estoque'];
                if ($estoque > 0) {
                    if (($data_validade_promocao >= $data_lancamento) and $preco_promocao > 0) {
                        $valorSubTotal += $preco_promocao * $quantidade;
                    } else {
                        $valorSubTotal += $preco_venda * $quantidade;
                    }
                }
            }

            if ($payment != "") { //validar forma de pagamento descontos etc..
                $desconto  =  consulta_tabela('tb_forma_pagamento', 'cl_id', $payment, 'cl_desconto'); //valor de desconto pela forma de pagamento
                if ($desconto != 0) {
                    // Calcula o valor do desconto em porcentagem
                    $descontoPorcentagem = ($desconto / 100);

                    // Calcula o valor do desconto em reais
                    $valorDesconto = $valorSubTotal * $descontoPorcentagem;
                }
            }
            /*funcao cupom */
            $dados_cupom = array('valor_produto' => $valorSubTotal, 'cupom' => $cupom);
            $validaCupom = validaCupom($dados_cupom);
            $msgCupom = $validaCupom['msgCupom'];
            $valorCupom = $validaCupom['valorCupom'];
            if ($valorCupom != 0) {
                // Calcula o valor do cupom em porcentagem
                $cupomPorcentagem = ($valorCupom / 100);
                // Calcula o valor do cupom em reais
                $valorCupom = $valorSubTotal * $cupomPorcentagem;
            }

            $valorTotal = $valorSubTotal + $valorFrete - $valorDesconto - $valorCupom; //total

            $informacao = array(
                "valorSubTotal" => ($valorSubTotal),
                "valorFrete" => ($valorFrete),
                "valorDesconto" => ($valorDesconto),
                "valorTotal" => ($valorTotal),
                "transp_nome" => $transp_nome,
                "prazoEnt" => $prazoEnt,
                "idSimulacao" => $idSimulacao,
                "valorCupom" => $valorCupom,
                "msgCupom" => $msgCupom,

            );


            $retornar["data"] = array(
                "status" => true,
                "response" => $informacao
            );
        } else {
            $retornar["data"] = array(
                "status" => false,
                "message" => "Não existem produtos no carrinho"
            );
        }
    }
    return $retornar;
}


function validaCupom($dados)
{
    global $conecta;
    global $data_lancamento;
    $valor_produto = $dados['valor_produto'];
    $cupom = $dados['cupom'];
    $retornar = array('msgCupom' => "", 'valorCupom' => 0);

    //validar cupom
    if (!empty($cupom)) {
        $query = "SELECT cp.*,cdp.* FROM tb_cupom as cp left join tb_condicao_cupom as cdp on cp.cl_condicao_id  = cdp.cl_id
         where cp.cl_codigo= '$cupom'";
        $consulta = mysqli_query($conecta, $query);
        if ($consulta) {
            $qtd_registro = mysqli_num_rows($consulta);
            if ($qtd_registro == 0) { //validar se existe o cupom
                $retornar['msgCupom'] = ("Código de cupom inexstente!");
            } else {
                $linha = mysqli_fetch_assoc($consulta);
                $data_validade = $linha['cl_data_validade'];
                $limite_utilizado = $linha['cl_limite_utilizado'];
                $valor_minimo = $linha['cl_valor_minimo'];
                $primeira_compra = $linha['cl_primeira_compra'];
                $status = $linha['cl_status'];


                if ($data_validade < $data_lancamento or $status == "0") { //validação de data ou de status
                    $retornar['msgCupom']  = "Cupom expirado!";
                }

                if ($limite_utilizado > 0) { //validação de quantidade de cupons utilizados
                    $validar_limite_cupom = consulta_tabela_query($conecta, "SELECT count(*) as total FROM tb_pedido_loja 
                    WHERE cl_cupom = '$cupom' and cl_status_compra= 'CONCLUIDO'", 'total');
                    if ($limite_utilizado >= $validar_limite_cupom) {
                        $retornar['msgCupom']  = "Limite do cupom excedido";
                    }
                }
                if ($valor_minimo > 0) { // Validação do valor mínimo do cupom

                    if ($valor_minimo > $valor_produto) {
                        $retornar['msgCupom'] = "Para aplicar este cupom, o valor mínimo de produtos no carrinho deve ser de " . real_format($valor_produto);
                    }
                }

                if (auth('') != false) { //usuario está logado
                    $dados_usuario = auth('')['dados_usuario'];
                    $cpf_cnpj = $dados_usuario['cpf_cnpj'];
                    $email = $dados_usuario['email'];

                    if ($primeira_compra == 1) { //validação primeira compra
                        $valida_primeira_compra = consulta_tabela_query($conecta, "SELECT count(*) as total FROM tb_pedido_loja 
                        WHERE ((cl_cpf_cnpj = '$cpf_cnpj' or cl_email = '$email') and cl_status_compra= 'CONCLUIDO' and cl_status_pagamento ='approved')", 'total');

                        if ($valida_primeira_compra > 0) {
                            $retornar['msgCupom']  = "Este cupom é válido apenas para a primeira compra";
                        }
                    }

                    $valida_usuario_cupom = consulta_tabela_query($conecta, "SELECT * FROM tb_pedido_loja 
                    WHERE cl_cupom = '$cupom' 
                    AND ((cl_cpf_cnpj = '$cpf_cnpj' AND cl_status_compra = 'CONCLUIDO' and cl_status_pagamento ='approved') 
                    OR (cl_email = '$email' AND cl_status_compra = 'CONCLUIDO' and cl_status_pagamento ='approved')) ", 'cl_id');

                    if (!empty($valida_usuario_cupom)) { //validação de usuario, utilização de apenas uma vez por usuário
                        $retornar['msgCupom']  = "Você já utilizou este cupom anteriormente.";
                    }
                }

                if (empty($retornar['msgCupom'])) { //cupom  válido, disponibilizar o valor
                    $retornar['valorCupom'] = $linha['cl_valor'];
                }
            }
        }
    }
    return $retornar;
}


function inserirProdutoBd($codigo_nf, $nome_do_arquivo)
{
    global $conecta;
    global $data_lancamento;
    global $data;
    $dados_usuario = auth('') != false ? auth('') : cookieAuth('');
    if ($dados_usuario) {
        $produtosCart = $dados_usuario['produtos_cart'];
        $qtd_cart = $dados_usuario['qtd_cart'];
        if ($qtd_cart > 0) {
            $total  = 0;
            foreach ($produtosCart as $linha) {
                $id = $linha['idproduto'];
                $titulo = ($linha['cl_descricao']);
                $referencia = ($linha['cl_referencia']);
                $preco_venda = ($linha['cl_preco_venda']);
                $preco_promocao = ($linha['cl_preco_promocao']);
                $data_validade_promocao = ($linha['cl_data_valida_promocao']);
                $estoque = ($linha['cl_estoque']);
                $quantidade = ($linha['cl_quantidade']);
                if ($estoque > 0) {
                    if (($data_validade_promocao >= $data_lancamento) and $preco_promocao > 0) {
                        $total = $preco_promocao;
                    } else {
                        $total = $preco_venda;
                    }

                    $query = "INSERT INTO `tb_produto_pedido_loja` (`cl_codigo_nf`, `cl_data`, `cl_produto_id`,
         `cl_descricao`, `cl_referencia`, `cl_quantidade`, `cl_valor`) VALUES ('$codigo_nf', '$data', '$id', '$titulo', '$referencia', '$quantidade', '$total') ";
                    $operacaoProduto = mysqli_query($conecta, $query);
                    if (!$operacaoProduto) {
                        $retornar["data"] = array("status" => false, "message" => "Ops, o site está apresentando um mau funcionamento,
                    Lamentamos o inconveniente, mas estamos trabalhando para resolver o problema o mais rápido possível. Por favor, tente acessar novamente em alguns minutos");
                        $mensagem = utf8_decode("Ecommerce - $nome_do_arquivo - operacaoProduto  " . str_replace("'", "", mysqli_error($conecta)));
                        registrar_log($conecta, 'ecommerce', $data, $mensagem); // Registrar log do erro
                        return $retornar;
                    }
                }
            }
        }
    }
    $retornar["data"] = array("status" => true);
    return $retornar;
}




function gerarPagamentoMercadoPago($accesstoken, $id)
{
    global $conecta;
    global $url_init;
    $query = "SELECT pdl.cl_desconto as desconto, pdl.*,fpg.* FROM tb_pedido_loja as pdl 
    left join tb_forma_pagamento as fpg on fpg.cl_id =  pdl.cl_pagamento_id_interno where pdl.cl_id = '$id'  ";
    $consulta = mysqli_query($conecta, $query);
    if (!$consulta) {
        $execute['data'] = array("status" => false, "type" => "aplicacao", "message" => "Erro ao realizar a consulta ao pedido função gerarLinkVendaMp " . str_replace("'", "", mysqli_error($conecta)));
        return $execute;
    }
    $linha = mysqli_fetch_assoc($consulta);
    $codigo_nf = ($linha['cl_codigo_nf']);
    $pedido = ($linha['cl_pedido']);
    $nome = utf8_encode($linha['cl_nome']);
    $email = utf8_encode($linha['cl_email']);
    $cpfcnpj = ($linha['cl_cpf_cnpj']);
    $telefone = ($linha['cl_telefone']);
    $endereco = utf8_encode($linha['cl_endereco']);
    $bairro = utf8_encode($linha['cl_bairro']);
    $numero = utf8_encode($linha['cl_numero']);
    $complemento = utf8_encode($linha['cl_complemento']);
    $cep = ($linha['cl_cep']);
    $cidade = utf8_encode($linha['cl_cidade']);
    $estado = utf8_encode($linha['cl_estado']);
    $transportadora = utf8_encode($linha['cl_transportadora']);
    $valor_frete = ($linha['cl_valor_frete']);
    $valor_produto = ($linha['cl_valor_produto']);
    $valor_desconto = ($linha['desconto']);
    $valor_cupom = ($linha['cl_valor_cupom']);
    $valor_liquido = utf8_encode($linha['cl_valor_liquido']);
    $tipo_pagamento_nf = ($linha['cl_tipo_pagamento_nf']);
    $valor_desconto = $valor_desconto + $valor_cupom;

    $query = "SELECT * FROM tb_produto_pedido_loja where cl_codigo_nf = '$codigo_nf' ";
    $consulta = mysqli_query($conecta, $query);
    if (!$consulta) {
        $execute['data'] = array("status" => false, "type" => "aplicacao", "message" => "Erro ao realizar a consulta ao produtos do pedido função gerarLinkVendaMp " . str_replace("'", "", mysqli_error($conecta)));
        return $execute;
    }

    // $query = "SELECT sum(cl_quantidade) as qtd FROM tb_produto_pedido_loja where cl_codigo_nf = '$codigo_nf' ";
    // $consulta_qtd = mysqli_query($conecta, $query);
    // if (!$consulta_qtd) {
    //     $execute['data'] = array("status" => false, "type" => "aplicacao", "message" => "Erro ao realizar a consulta ao produtos do pedido função gerarLinkVendaMp " . str_replace("'", "", mysqli_error($conecta)));
    //     return $execute;
    // }
    // $linha = mysqli_fetch_assoc($consulta_qtd);
    // $qtd_produtos = $linha['qtd'];

    $desconto_check = false;

    $items = [];
    while ($linha = mysqli_fetch_assoc($consulta)) {

        $descricao = utf8_encode($linha['cl_descricao']);
        $referencia = utf8_encode($linha['cl_referencia']);
        $quantidade = $linha['cl_quantidade'];
        $valor = $linha['cl_valor'];

        if (($valor > $valor_desconto or $valor > $valor_cupom) and $desconto_check == false) { //desconto = desconto - cuopom, atribuir ao valor do produto que suporta o total do desconto
            $valor = $valor - $valor_desconto;
            $desconto_check = true;
        }



        $item = '{
            "title": "' . $descricao . '",
            "description": "' . $referencia . '",
            "picture_url": "http://www.myapp.com/myimage.jpg",
            "category_id": "musical",
            "quantity": ' . $quantidade . ',
            "currency_id": "BRL",
            "unit_price": ' . $valor . ',
        }';

        $items[] = $item;
    }

    $items_json = '[' . implode(',', $items) . ']';



    $frete = '"shipments":{
            "cost": ' . $valor_frete . ',
            "mode": "not_specified",
         }';


    if ($tipo_pagamento_nf == "17") { //pix
        $excluir_pgt = '"excluded_payment_types": [
            {"id": "credit_card"},
            {"id": "ticket"},
            {"id": "debit_card"}
        ],
        ["default_payment_method_id":"bank_trnasfer"]';
    } elseif ($tipo_pagamento_nf == "03") { //cartão de crédito
        $excluir_pgt = '"excluded_payment_types": [
            {"id": "ticket"},
            {"id": "bank_transfer"},
            {"id": "debit_card"}
        ],
        ["default_payment_method_id":"credit_card"]
        ';
    } elseif ($tipo_pagamento_nf == "04") { //cartão de debito
        $excluir_pgt = '"excluded_payment_types": [
            {"id": "ticket"},
            {"id": "bank_transfer"},
            {"id": "credit_card"}

        ],
        ["default_payment_method_id":"debit_card"]
        ';
    } elseif ($tipo_pagamento_nf == "15") { //boleto
        $excluir_pgt = '"excluded_payment_types": [
            {"id": "debit_card"},
            {"id": "bank_transfer"},
            {"id": "credit_card"}
        ],
        ["default_payment_method_id":"ticket"]
        ';
    }
    $nomeEcommerce = utf8_encode(consulta_tabela('tb_parametros', 'cl_id', '64', 'cl_valor')); //nome do ecommerce

    $curl = curl_init();
    //  $servidor = $_SERVER['SERVER_NAME'];
    //"auto_return": "approved",
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.mercadopago.com/checkout/preferences',
        CURLOPT_RETURNTRANSFER => true,

        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
            "back_urls": {
                "success": "' . $url_init . '/' . $nomeEcommerce . '/?order-completed=1&code=' . $codigo_nf . '",
                "pending":  "' . $url_init . '/' . $nomeEcommerce . '/?order-completed=2&order=' . $pedido . '&code=' . $codigo_nf . '",
                "failure":  "' . $url_init . '/' . $nomeEcommerce . '/?confirm-order=true&order=' . $pedido . '&code=' . $codigo_nf . '",
            },

            "external_reference": "' . $id . '",
            "notification_url":  "' . $url_init . '/' . $nomeEcommerce . '/app/Http/Controllers/Notification.php",
            "auto_return": "approved",
            "items": ' . $items_json . ',
            "track": {
                "type": "facebook_ad",
                "values": {
                    "facebook_id": "671236655116226",
                    "pixel_id": "951529083447445"
                }
            },
            "payment_methods": {
               
              ' . $excluir_pgt . '
            },

            ' . $frete . '

            }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accesstoken
        ),
    ));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($curl);

    curl_close($curl);

    $obj = json_decode($response);

    if (isset($obj->id)) {
        if ($obj->id != NULL) {
            $link_externo = $obj->init_point;
            // $external_reference = $obj->external_reference;

            $execute["data"] = array(
                "status" => true,
                "link_externo" => $link_externo,
            );
        } else {
            $execute['data'] = array("status" => false, "type" => "aplicacao", "message" => "Erro, ao gerar o link de pagamento mercado pago");
        }
    } else {
        $execute['data'] = array("status" => false, "type" => "aplicacao", "message" => "Erro, ao gerar o link de pagamento mercado pago");
    }
    return $execute;

    mysqli_close($conecta);
}


function registerAuth($nome, $email, $cpfCnpj, $endereco, $bairro, $numero, $cidade, $uf, $cep, $telefone, $confirmaEmail)
{
    global $conecta;
    global $data;
    $senhaGerada = gerarSenhaSegura();

    $nome = utf8_decode($nome);
    $endereco = utf8_decode($endereco);
    $bairro = utf8_decode($bairro);
    $cidade = utf8_decode($cidade);

    $senha = password_hash($senhaGerada, PASSWORD_DEFAULT); //codificando senha
    $query = "INSERT INTO `tb_user_loja` (`cl_nome`,`cl_data`,`cl_email`,`cl_cpf_cnpj`, `cl_senha`, `cl_endereco`, `cl_bairro`, `cl_numero`, `cl_cidade`, `cl_cep`,`cl_telefone`,`cl_confimar_email`,`cl_cookie` ) 
    VALUES ('$nome', '$data','$email', '$cpfCnpj', '$senha','$endereco','$bairro','$numero','$cidade','$cep','$telefone','$confirmaEmail','1' )";
    $insert = mysqli_query($conecta, $query);

    if ($insert) {
        $id = mysqli_insert_id($conecta);
        $retornar['data'] = array('status' => true, 'id' => $id, 'email' => $email, 'senha' => $senhaGerada);

        /*pixel */
        $dados = [
            'dados' => ['pagina' => '?register'],
            'dados_usuario' => [
                "id" => $id,
                "nome" => $nome,
                "email" => $email,
                "cep" => $cep,
                "telefone" => $telefone,
                "cidade" => $cidade,
                "estado" => $uf,
            ],
        ];
        pixel('CompleteRegistration', $dados);
    } else {
        $retornar['data'] = array('status' => false);
        $mensagem = utf8_decode("Ecommerce - funcao registerAuthr");
        registrar_log($conecta, 'ecommerce', $data, $mensagem); // Registrar log do erro
    }
    return $retornar;
    mysqli_close($conecta);
}


function gerarSenhaSegura()
{
    $tamanho = 12;
    $caracteresPermitidos = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $senha = '';

    // Gera a senha até atender os requisitos
    while (strlen($senha) < $tamanho || !preg_match('/[0-9]/', $senha) || !preg_match('/[a-z]/', $senha) || !preg_match('/[A-Z]/', $senha)) {
        $senha = substr(str_shuffle($caracteresPermitidos), 0, $tamanho);
    }

    return $senha;
}


function autoUpdateAuth($usuarioID, $cpfCnpj, $cep, $endereco, $bairro, $cidade, $numero, $telefone)
{

    if (!empty($usuarioID)) {
        $verificaCpf = consulta_tabela('tb_user_loja', 'cl_id', $usuarioID, 'cl_cpf_cnpj');
        if (empty($verificaCpf) and !empty($cpfCnpj)) {
            update_registro('tb_user_loja', 'cl_id', $usuarioID, '', '', 'cl_cpf_cnpj', $cpfCnpj); //informar o cadastro do cliente o cpf da compra se no cadastro não tiver o preenchido
        }

        $verificaCep = consulta_tabela('tb_user_loja', 'cl_id', $usuarioID, 'cl_cep');
        if (empty($verificaCep) and !empty($cep)) {
            update_registro('tb_user_loja', 'cl_id', $usuarioID, '', '', 'cl_cep', $cep);
        }

        $verificaEndereco = consulta_tabela('tb_user_loja', 'cl_id', $usuarioID, 'cl_endereco');
        if (empty($verificaEndereco) and !empty($endereco)) {
            update_registro('tb_user_loja', 'cl_id', $usuarioID, '', '', 'cl_endereco', $endereco);
        }

        $verificaBairro = consulta_tabela('tb_user_loja', 'cl_id', $usuarioID, 'cl_bairro');
        if (empty($verificaBairro) and !empty($bairro)) {
            update_registro('tb_user_loja', 'cl_id', $usuarioID, '', '', 'cl_bairro', $bairro);
        }

        $verificaCidade = consulta_tabela('tb_user_loja', 'cl_id', $usuarioID, 'cl_cidade');
        if (empty($verificaCidade) and !empty($cidade)) {
            update_registro('tb_user_loja', 'cl_id', $usuarioID, '', '', 'cl_cidade', $cidade);
        }

        $verificaTelefone = consulta_tabela('tb_user_loja', 'cl_id', $usuarioID, 'cl_telefone');
        if (empty($verificaTelefone) and !empty($telefone)) {
            update_registro('tb_user_loja', 'cl_id', $usuarioID, '', '', 'cl_telefone', $telefone);
        }
        $verificaNumero = consulta_tabela('tb_user_loja', 'cl_id', $usuarioID, 'cl_numero');
        if (empty($verificaNumero) and !empty($numero)) {
            update_registro('tb_user_loja', 'cl_id', $usuarioID, '', '', 'cl_numero', $numero);
        }
    }
}

function formatarNumeroTelefone($numero)
{
    // Remove caracteres indesejados
    $numero = str_replace(array('(', ')', ' '), '', $numero);

    // Verifica se o número tem 11 dígitos (incluindo o DDD)
    if (strlen($numero) == 11) {
        // Formata o número com o código de área separado
        $formatado = '(' . substr($numero, 0, 2) . ') ' . substr($numero, 2, 5) . '-' . substr($numero, 7);
    } else {
        // Caso contrário, assume que não tem código de área e apenas formata o número
        $formatado = substr($numero, 0, 5) . '-' . substr($numero, 5);
    }

    return $formatado;
}



function ajusteEstoqueLoja($codigo_nf, $operacao)
{
    global $conecta;
    global $data;
    global $data_lancamento;
    $pedido = consulta_tabela('tb_pedido_loja', 'cl_codigo_nf', $codigo_nf, 'cl_pedido');
    $execute['data'] = array("status" => true);
    $query = "SELECT * FROM tb_produto_pedido_loja where cl_codigo_nf = '$codigo_nf' ";
    $consultaProdutos = mysqli_query($conecta, $query);
    $qtd_registro = mysqli_num_rows($consultaProdutos);

    if (!$consultaProdutos) {
        $execute['data'] = array("status" => false, "message" => "Erro ao realizar a consulta ao produtos do pedido função ajusteEstoqueLoja " . str_replace("'", "", mysqli_error($conecta)));
        return false;
    }
    if ($qtd_registro > 0) {
        while ($linha = mysqli_fetch_assoc($consultaProdutos)) {
            $id = utf8_encode($linha['cl_id']);
            $produto_id = ($linha['cl_produto_id']);
            $descricao = utf8_encode($linha['cl_descricao']);
            $referencia = utf8_encode($linha['cl_referencia']);
            $quantidade = $linha['cl_quantidade'];
            $valor = ($linha['cl_valor']);
            $total = $valor * $quantidade;
            $doc = "PedidoLoja-$pedido";
            if ($operacao == "saida") {
                $ajuste_estoque = ajusteEstoque($data_lancamento, $doc, "SAIDA", $produto_id, $quantidade, "1", '', '', '', $total, 0, 0, '', $codigo_nf, $id, '');
                if (!$ajuste_estoque) {
                    $execute['data'] = array("status" => false, "message" => "Erro ao realizar o ajuste de estoque id $id");
                }
            } elseif ($operacao == "cancelado") {
                $validar_produto_ajuste = consulta_tabela_2_filtro('tb_ajuste_estoque', 'cl_id_nf', $id, 'cl_codigo_nf', $codigo_nf, 'cl_id');
                if (!empty($validar_produto_ajuste)) {
                    update_registro('tb_ajuste_estoque', 'cl_id_nf', $id, 'cl_codigo_nf', $codigo_nf, 'cl_status', 'cancelado');
                    atualizarEstoque($produto_id, $quantidade, "cancelado");
                }
            }
        }
    }

    if ($execute['data']['status'] == false) {
        $mensagem = utf8_decode("Ecommerce - " . $execute['data']['message']);
        registrar_log($conecta, 'ecommerce', $data, $mensagem); // Registrar log do erro
        return false;
    }

    return true;
    mysqli_close($conecta); // Fechar conexão com o banco de dados
}



//funcao para realizar ajuste de estoque
function ajusteEstoque(
    $data,
    $doc,
    $tipo,
    $produto_id,
    $quantidade,
    $empresa_id,
    $parceiro_id,
    $usuario_id,
    $forma_pagamento_id,
    $valor_venda,
    $valor_compra,
    $ajuste_inical,
    $motivo,
    $codigo_nf,
    $item_nf_id,
    $item_nf_id_pai
) {
    global $conecta;
    $execute['data'] = array("status" => true);
    $query = "INSERT INTO `tb_ajuste_estoque` (`cl_data_lancamento`, `cl_documento`, `cl_produto_id`, `cl_tipo`, `cl_quantidade`, 
    `cl_empresa_id`,`cl_parceiro_id`,`cl_usuario_id`, `cl_forma_pagamento_id`, `cl_valor_venda`, `cl_valor_compra`,`cl_ajuste_inicial`,`cl_status`,`cl_motivo`,`cl_codigo_nf`, `cl_id_nf`,`cl_id_nf_pai`  ) VALUES 
    ('$data', '$doc', '$produto_id', '$tipo', '$quantidade', '$empresa_id','$parceiro_id','$usuario_id', '$forma_pagamento_id', '$valor_venda', '$valor_compra','$ajuste_inical','ok','$motivo','$codigo_nf', '$item_nf_id', '$item_nf_id_pai' )";
    $operacao = mysqli_query($conecta, $query);
    if (!$operacao) {
        $execute['data'] = array("status" => false, "message" => "Função ajusteEstoque" . str_replace("'", "", mysqli_error($conecta)));
    } else {
        atualizarEstoque($produto_id, $quantidade, "saida");
    }

    if ($execute['data']['status'] == false) {
        $mensagem = utf8_decode("Ecommerce - " . $execute['data']['message']);
        registrar_log($conecta, 'ecommerce', $data, $mensagem); // Registrar log do erro
        return false;
    }

    return true;
    mysqli_close($conecta);
}

function atualizarEstoque($produto_id, $quantidade, $operacao)
{
    $estoque = consulta_tabela('tb_produtos', 'cl_id', $produto_id, 'cl_estoque');

    if ($operacao == "saida") {
        $nvoEstoque = $estoque - $quantidade;
    } else {
        $nvoEstoque = $estoque + $quantidade;
    }
    update_registro('tb_produtos', 'cl_id', $produto_id, '', '', 'cl_estoque', $nvoEstoque);
}


function formatarDataTimeStampToDataData($data)
{
    if (!empty($data)) {
        // Converte a data para timestamp
        $timestamp = strtotime($data);

        // Formata a data no formato desejado
        $dataFormatada = date('d/m/Y', $timestamp);

        return $dataFormatada;
    };
}


function rastrearObjetoKangu($rastreio, $accesstoken)
{
    global $conecta;
    global $data;
    // URL da API
    $url = "https://portal.kangu.com.br/tms/transporte/rastrear/$rastreio";

    // Cabeçalhos da requisição
    $headers = array(
        "accept: application/json",
        "token: $accesstoken",
        "Content-Type: application/json"
    );

    // Inicializa o cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($httpCode == 200) {
        $retornar["data"] = array("status" => true, "response" => json_decode($response, true));
    } else { //code 400
        $decoded_response = json_decode($response, true);
        $errorCode = $decoded_response['error']['codigo'];
        $errorMessage = $decoded_response['error']['mensagem'];

        switch ($errorCode) {
            case 500:
                $errorMessage = "Parametro 'token' não informado no cabeçalho do request";
                break;
            case 510:
                $errorMessage = "Código de rastreio pendente";
                break;
            default:
                $errorMessage = "Erro desconhecido: " . $errorMessage;
                break;
        }

        $mensagem = utf8_decode("Ecommerce - código de rastreio $rastreio - " . str_replace("'", "", $errorMessage));
        registrar_log($conecta, 'ecommerce', $data, $mensagem); // Registrar log do erro


        $retornar["data"] = array("status" => false, "message" => str_replace("'", "", $errorMessage));
    }

    if (curl_errno($ch)) {

        $mensagem = utf8_decode("Ecommerce - código de rastreio $rastreio - " . str_replace("'", "", curl_error($ch)));
        registrar_log($conecta, 'ecommerce', $data, $mensagem); // Registrar log do erro
        $retornar["data"] = array("status" => false, "message" => str_replace("'", "", curl_error($ch)));
    }

    // Fecha o cURL
    curl_close($ch);
    return $retornar;
    mysqli_close($conecta);
}



function pixel($tipo, $dados)
{
    // Substitua os valores abaixo pelos seus próprios dados
    global $conecta;
    global $data;
    $apiVersion = consulta_tabela('tb_parametros', 'cl_id', '94', 'cl_valor'); //versão pixel
    $datasetId = consulta_tabela('tb_parametros', 'cl_id', '95', 'cl_valor'); //id pixel
    $accessToken = consulta_tabela('tb_parametros', 'cl_id', '96', 'cl_valor'); //token pixel
    $ativoPixel = consulta_tabela('tb_parametros', 'cl_id', '97', 'cl_valor'); //pixel ativo
    if ($ativoPixel == "S") {

        // URL para enviar eventos
        $url = "https://graph.facebook.com/{$apiVersion}/{$datasetId}/events?access_token={$accessToken}";

        $id = isset($dados['dados_usuario']['id']) ? $dados['dados_usuario']['id'] : '';
        $email = isset($dados['dados_usuario']['email']) ? $dados['dados_usuario']['email'] : '';
        $nome = isset($dados['dados_usuario']['nome']) ? utf8_decode(strtolower($dados['dados_usuario']['nome'])) : '';
        $cep = isset($dados['dados_usuario']['cep']) ? $dados['dados_usuario']['cep'] : '';
        $telefone = isset($dados['dados_usuario']['telefone']) ? "55" . $dados['dados_usuario']['telefone'] : '';
        $cidade = isset($dados['dados_usuario']['cidade']) ? utf8_decode(strtolower($dados['dados_usuario']['cidade'])) : '';
        $estado = isset($dados['dados_usuario']['estado']) ? utf8_decode(strtolower($dados['dados_usuario']['estado'])) : '';
        $fbp = isset($_COOKIE['_fbp']) ? $_COOKIE['_fbp'] : ''; //fbp cookie
        $fbc = isset($_COOKIE['_fbc']) ? $_COOKIE['_fbc'] : ''; //fbp cookie

        $fbp_bd = isset($dados['dados']['fbp']) ? $dados['dados']['fbp'] : '';
        $fbc_bd = isset($dados['dados']['fbc']) ? $dados['dados']['fbc'] : '';

        /*carrinho */
        $produtos = isset($dados['produtos']) ? $dados['produtos'] : '';

        $produtoID = isset($dados['produto']['produtoID']) ? $dados['produto']['produtoID'] : '';
        $qtdProduto = isset($dados['produto']['qtdProduto']) ? $dados['produto']['qtdProduto'] : '';
        $descricaoProduto = isset($dados['produto']['descricao']) ? $dados['produto']['descricao'] : '';

        $valor_total = isset($dados['dados']['valor_total']) ? $dados['dados']['valor_total'] : '';
        $forma_pagamento = isset($dados['dados']['forma_pagamento']) ? utf8_decode(strtolower($dados['dados']['forma_pagamento'])) : '';


        $pagina = isset($dados['dados']['pagina']) ? $dados['dados']['pagina'] : '';
        $pesquisa = isset($dados['dados']['pesquisa']) ? utf8_decode(strtolower($dados['dados']['pesquisa'])) : '';


        $nomeEcommerce = utf8_encode(consulta_tabela('tb_parametros', 'cl_id', '64', 'cl_valor')); //nome do ecommerce
        $servidor = $_SERVER['SERVER_NAME'];

        // Inicialize cURL
        $ch = curl_init($url);

        //  $ip = $_SERVER['REMOTE_ADDR']; // Obtém o endereço IP do usuário
        $ip = $_SERVER['REMOTE_ADDR'];
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = getenv('HTTP_CLIENT_IP');
        }

        // Faz uma solicitação para o serviço ipinfo.io
        $api_url = "https://ipinfo.io/{$ip}/json";
        $response = file_get_contents($api_url);

        // Decodifica a resposta JSON
        $data = json_decode($response);

        // Obtém o país do usuário
        $country = isset($data->country) ? $data->country : 'BR';
        if ($tipo == "ViewContentNotLog") {
            // Dados do evento ViewContent
            $eventData = [
                'data' => [
                    [
                        'event_name' => 'ViewContent',
                        'event_time' => time(),
                        'event_source_url' => "https://$servidor/$nomeEcommerce/$pagina",
                        'action_source' => 'website',

                        // Substitua com a fonte da ação apropriada
                        'user_data' => [
                            'client_ip_address' => $ip,
                            'client_user_agent' => hash('sha256', $_SERVER['HTTP_USER_AGENT']),
                            'country' =>  hash('sha256', $country),
                            'fbp' => "$fbp", // Substitua com o valor correto do fbp
                            'fbc' => "$fbc", // Substitua com o valor correto do fbc
                           // 'external_id' =>  hash('sha256', $id),
                            // 'em' => hash('sha256', $email), // Exemplo de hash para o e-mail
                            // 'ph' => hash('sha256', $telefone), // Exemplo de hash para o número de telefone
                            // 'fn' => hash('sha256', $nome), // Exemplo de hash para o nome
                            // 'ln' => hash('sha256', $nome), // Exemplo de hash para o sobrenome
                            // 'zp' => hash('sha256', $cep), // Exemplo de hash para o código postal
                            // 'ct' => hash('sha256', $cidade), // Exemplo de hash para a cidade
                            // 'st' => hash('sha256', $estado), // Exemplo de hash para o estado

                            // Adicione outros parâmetros conforme necessário
                        ],
                    ],
                ],
            ];
        }elseif ($tipo == "ViewContent") {
            // Dados do evento ViewContent
            $eventData = [
                'data' => [
                    [
                        'event_name' => 'ViewContent',
                        'event_time' => time(),
                        'event_source_url' => "https://$servidor/$nomeEcommerce/$pagina",
                        'action_source' => 'website',

                        // Substitua com a fonte da ação apropriada
                        'user_data' => [
                            'client_ip_address' => $ip,
                            'client_user_agent' => hash('sha256', $_SERVER['HTTP_USER_AGENT']),
                            'country' =>  hash('sha256', $country),
                            'external_id' =>  hash('sha256', $id),
                            'fbp' => "$fbp", // Substitua com o valor correto do fbp
                            'fbc' => "$fbc", // Substitua com o valor correto do fbc

                            'em' => hash('sha256', $email), // Exemplo de hash para o e-mail
                            'ph' => hash('sha256', $telefone), // Exemplo de hash para o número de telefone
                            'fn' => hash('sha256', $nome), // Exemplo de hash para o nome
                            'ln' => hash('sha256', $nome), // Exemplo de hash para o sobrenome
                            'zp' => hash('sha256', $cep), // Exemplo de hash para o código postal
                            'ct' => hash('sha256', $cidade), // Exemplo de hash para a cidade
                            'st' => hash('sha256', $estado), // Exemplo de hash para o estado

                            // Adicione outros parâmetros conforme necessário
                        ],
                    ],
                ],
            ];
        } elseif ($tipo == "viewContentDetProd") {
            // Dados do evento ViewContent
            $eventData = [
                'data' => [
                    [
                        'event_name' => 'ViewContent',
                        'event_time' => time(),
                        'event_source_url' => "https://$servidor/$nomeEcommerce/$pagina",
                        'action_source' => 'website',
                        'content_ids' => [$produtoID], // ID do produto
                        'content_name' => $descricaoProduto, // Nome do produto

                        // Substitua com a fonte da ação apropriada
                        'user_data' => [
                            'client_ip_address' => $ip,
                            'client_user_agent' => hash('sha256', $_SERVER['HTTP_USER_AGENT']),
                            'country' =>  hash('sha256', $country),
                            'external_id' =>  hash('sha256', $id),
                            'fbp' => "$fbp", // Substitua com o valor correto do fbp
                            'fbc' => "$fbc", // Substitua com o valor correto do fbc
                            
                            'em' => hash('sha256', $email), // Exemplo de hash para o e-mail
                            'ph' => hash('sha256', $telefone), // Exemplo de hash para o número de telefone
                            'fn' => hash('sha256', $nome), // Exemplo de hash para o nome
                            'ln' => hash('sha256', $nome), // Exemplo de hash para o sobrenome
                            'zp' => hash('sha256', $cep), // Exemplo de hash para o código postal
                            'ct' => hash('sha256', $cidade), // Exemplo de hash para a cidade
                            'st' => hash('sha256', $estado), // Exemplo de hash para o estado

                            // Adicione outros parâmetros conforme necessário
                        ],
                    ],
                ],
            ];
        } elseif ($tipo == "ViewCategory") {
            // Dados do evento ViewCategory
            $eventData = [
                'data' => [
                    [
                        'event_name' => 'ViewCategory',
                        'event_time' => time(),
                        'event_source_url' => "https://$servidor/$nomeEcommerce/$pagina",
                        'action_source' => 'website',

                        // Substitua com a fonte da ação apropriada
                        'user_data' => [
                            'client_ip_address' => $ip,
                            'client_user_agent' => hash('sha256', $_SERVER['HTTP_USER_AGENT']),
                            'country' =>  hash('sha256', $country),
                            'external_id' =>  hash('sha256', $id),
                            'em' => hash('sha256', $email), // Exemplo de hash para o e-mail
                            'ph' => hash('sha256', $telefone), // Exemplo de hash para o número de telefone
                            'fn' => hash('sha256', $nome), // Exemplo de hash para o nome
                            'ln' => hash('sha256', $nome), // Exemplo de hash para o sobrenome
                            'zp' => hash('sha256', $cep), // Exemplo de hash para o código postal
                            'ct' => hash('sha256', $cidade), // Exemplo de hash para a cidade
                            'st' => hash('sha256', $estado), // Exemplo de hash para o estado

                            // Adicione outros parâmetros conforme necessário
                        ],
                    ],
                ],
            ];
        } elseif ($tipo == "AddToCart") {
            $eventData = [
                'data' => [
                    [
                        'event_name' => 'AddToCart',
                        'event_time' => time(),
                        'event_source_url' => "https://$servidor/$nomeEcommerce/$pagina",
                        'action_source' => 'website',

                        // Substitua com a fonte da ação apropriada
                        'user_data' => [
                            'client_ip_address' => $ip,
                            'client_user_agent' => hash('sha256', $_SERVER['HTTP_USER_AGENT']),
                            'country' =>  hash('sha256', $country),
                            'external_id' =>  hash('sha256', $id),
                            'fbp' => "$fbp", // Substitua com o valor correto do fbp
                            'fbc' => "$fbc", // Substitua com o valor correto do fbc

                            'em' => hash('sha256', $email), // Exemplo de hash para o e-mail
                            'ph' => hash('sha256', $telefone), // Exemplo de hash para o número de telefone
                            'fn' => hash('sha256', $nome), // Exemplo de hash para o nome
                            'ln' => hash('sha256', $nome), // Exemplo de hash para o sobrenome
                            'zp' => hash('sha256', $cep), // Exemplo de hash para o código postal
                            'ct' => hash('sha256', $cidade), // Exemplo de hash para a cidade
                            'st' => hash('sha256', $estado), // Exemplo de hash para o estado

                            // Adicione outros parâmetros conforme necessário
                        ],
                        'custom_data' => [
                            'content_type' => 'product',
                            'content_ids' => ["$produtoID"], // Substitua pelo ID real do produto
                            'content_name' => $descricaoProduto,
                            'quantity' => $qtdProduto,
                        ],
                    ],
                ],
            ];
        } elseif ($tipo == "Search") {
            $eventData = [
                'data' => [
                    [
                        'event_name' => 'Search',
                        'event_time' => time(),
                        'event_source_url' => "https://$servidor/$nomeEcommerce/$pagina",
                        'action_source' => 'website',

                        'user_data' => [
                            'client_ip_address' => $ip,
                            'client_user_agent' => hash('sha256', $_SERVER['HTTP_USER_AGENT']),
                            'country' =>  hash('sha256', $country),
                            'external_id' =>  hash('sha256', $id),
                            'fbp' => "$fbp", // Substitua com o valor correto do fbp
                            'fbc' => "$fbc", // Substitua com o valor correto do fbc

                            'em' => hash('sha256', $email), // Exemplo de hash para o e-mail
                            'ph' => hash('sha256', $telefone), // Exemplo de hash para o número de telefone
                            'fn' => hash('sha256', $nome), // Exemplo de hash para o nome
                            'ln' => hash('sha256', $nome), // Exemplo de hash para o sobrenome
                            'zp' => hash('sha256', $cep), // Exemplo de hash para o código postal
                            'ct' => hash('sha256', $cidade), // Exemplo de hash para a cidade
                            'st' => hash('sha256', $estado), // Exemplo de hash para o estado
                        ],
                        'search_string' => "$pesquisa", // String de pesquisa utilizada pelo usuário
                    ],
                ],
            ];
        } elseif ($tipo == "Purchase") {
            $currency = "BRL"; // Substitua pela moeda real do pedido
            // Preparar o array de produtos
            $contents = [];
            foreach ($produtos as $item) {
                $contents[] = [
                    'id' => $item['cl_produto_id'], // Substitua pelo ID real do produto
                    'quantity' => $item['cl_quantidade'], // ou qualquer lógica para definir a quantidade
                    // Adicione outros campos conforme necessário para cada item do produto
                ];
            }


            $eventData = [
                'data' => [
                    [
                        'event_name' => 'Purchase',
                        'event_time' => time(),
                        'event_source_url' => "https://$servidor/$nomeEcommerce/$pagina",
                        'action_source' => 'website',
                        // 'external_id' => '123', // Adicione o ID externo da transação aqui

                        // Substitua com a fonte da ação apropriada
                        'user_data' => [
                            'client_ip_address' => $ip,
                            'client_user_agent' => hash('sha256', $_SERVER['HTTP_USER_AGENT']),
                            'country' =>  hash('sha256', $country),
                            'external_id' =>  hash('sha256', $id),
                            'fbp' => "$fbp_bd", // Substitua com o valor correto do fbp
                            'fbc' => "$fbc_bd", // Substitua com o valor correto do fbc

                            'em' => hash('sha256', $email), // Exemplo de hash para o e-mail
                            'ph' => hash('sha256', $telefone), // Exemplo de hash para o número de telefone
                            'fn' => hash('sha256', $nome), // Exemplo de hash para o nome
                            'ln' => hash('sha256', $nome), // Exemplo de hash para o sobrenome
                            'zp' => hash('sha256', $cep), // Exemplo de hash para o código postal
                            'ct' => hash('sha256', $cidade), // Exemplo de hash para a cidade
                            'st' => hash('sha256', $estado), // Exemplo de hash para o estado
                            // Adicione outros parâmetros conforme necessário
                        ],

                        'custom_data' => [
                            'value' => $valor_total,
                            'currency' => $currency,
                            'content_type' => 'product',
                            'quantity' => 1,
                            'contents' => $contents, // Atribuir o array de produtos preparado aqui
                            'payment_method' => $forma_pagamento,


                        ],

                    ],

                ],
            ];
        } elseif ($tipo == "InitiateCheckout") {
            $currency = "BRL"; // Substitua pela moeda real do pedido

            $contents = [];
            foreach ($produtos as $item) {
                $contents[] = [
                    'id' => $item['cl_id'], // Substitua pelo ID real do produto
                    'quantity' => $item['cl_quantidade'], // ou qualquer lógica para definir a quantidade
                    // Adicione outros campos conforme necessário para cada item do produto
                ];
            }

            $eventData = [
                'data' => [
                    [
                        'event_name' => 'InitiateCheckout',
                        'event_time' => time(),
                        'event_source_url' => "https://$servidor/$nomeEcommerce/$pagina",
                        'action_source' => 'website',

                        // Substitua com a fonte da ação apropriada
                        'user_data' => [
                            'client_ip_address' => $ip,
                            'client_user_agent' => hash('sha256', $_SERVER['HTTP_USER_AGENT']),
                            'country' =>  hash('sha256', $country),
                            'external_id' =>  hash('sha256', $id),
                            'fbp' => "$fbp", // Substitua com o valor correto do fbp
                            'fbc' => "$fbc", // Substitua com o valor correto do fbc

                            'em' => hash('sha256', $email), // Exemplo de hash para o e-mail
                            'ph' => hash('sha256', $telefone), // Exemplo de hash para o número de telefone
                            'fn' => hash('sha256', $nome), // Exemplo de hash para o nome
                            'ln' => hash('sha256', $nome), // Exemplo de hash para o sobrenome
                            'zp' => hash('sha256', $cep), // Exemplo de hash para o código postal
                            'ct' => hash('sha256', $cidade), // Exemplo de hash para a cidade
                            'st' => hash('sha256', $estado), // Exemplo de hash para o estado
                            // Adicione outros parâmetros conforme necessário
                        ],

                        'custom_data' => [
                            [
                                'value' => $valor_total,
                                'content_type' => 'product',
                                'quantity' => 1,
                                'currency' => $currency,
                                'contents' => $contents, // Adicionando o array 'contents' aqui
                                'payment_method' => $forma_pagamento,
                            ],
                        ],
                    ],
                ],
            ];
        } elseif ($tipo == "CompleteRegistration") {
            $eventData = [
                'data' => [
                    [
                        'event_name' => 'CompleteRegistration',
                        'event_time' => time(),
                        'event_source_url' => "https://$servidor/$nomeEcommerce/$pagina",
                        'action_source' => 'website',
                        'user_data' => [
                            'client_ip_address' => $ip,
                            'client_user_agent' => hash('sha256', $_SERVER['HTTP_USER_AGENT']),
                            'country' =>  hash('sha256', $country),
                            'external_id' =>  hash('sha256', $id),
                            'fbp' => "$fbp", // Substitua com o valor correto do fbp
                            'fbc' => "$fbc", // Substitua com o valor correto do fbc

                            'em' => hash('sha256', $email),
                            'ph' => hash('sha256', $telefone),
                            'fn' => hash('sha256', $nome),
                            'ln' => hash('sha256', $nome),
                            'zp' => hash('sha256', $cep),
                            'ct' => hash('sha256', $cidade),
                            'st' => hash('sha256', $estado),
                            // Adicione outros parâmetros conforme necessário
                        ],
                    ],
                ],
            ];
        }
        // Certifique-se de que a chave 'data' está presente
        if (empty($eventData['data'])) {

            $mensagem = utf8_decode("Ecommerce - Erro: O parâmetro data não está configurado corretamente.");
            registrar_log($conecta, 'ecommerce', $data, $mensagem); // Registrar log do erro
        }



        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // Configurações da solicitação cURL
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($eventData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute a solicitação e capture a resposta
        curl_exec($ch);
        //$response = curl_exec($ch);

        // // Verifique se houve algum erro
        if (curl_errno($ch)) {
            return false;
        } else {
            return true;
        }
        // Feche a conexão cURL
        curl_close($ch);

        // Exiba a resposta do Facebook (para fins de depuração)
        // echo $response;
    } else {
        return true;
    }
}
