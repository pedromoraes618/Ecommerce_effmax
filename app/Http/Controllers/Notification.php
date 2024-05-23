<?php
include "../../../db/conn.php";
include "../../../helps/funcao.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../../../public/lib/vendor/phpmailer/phpmailer/src/Exception.php';
require '../../../public/lib/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../../../public/lib/vendor/phpmailer/phpmailer/src/SMTP.php';

$ambiente_mercado_pago = consulta_tabela("tb_parametros", "cl_id", "70", "cl_valor");
$homologacao_mercado_pago = consulta_tabela("tb_parametros", "cl_id", "71", "cl_valor");
$producao_mercado_pago = consulta_tabela("tb_parametros", "cl_id", "72", "cl_valor");
//$producao_mercado_pago = "APP_USR-2348807058961-021415-67c5f595783c7472179428152ab67b67-1682207570";

if ($ambiente_mercado_pago == '1') {
    $accesstoken = $homologacao_mercado_pago;
} elseif ($ambiente_mercado_pago == '2') {
    $accesstoken = $producao_mercado_pago;
} else {
    $accesstoken = "";
}

$body   = json_decode(file_get_contents('php://input'));
if (isset($body->data->id)) {
    $id = $body->data->id;
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.mercadopago.com/v1/payments/$id",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accesstoken
        ),
    ));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($curl);
    curl_close($curl);


    $payment = json_decode($response);
    if (isset($payment->id)) {
        $external_reference = isset($payment->external_reference) ? $payment->external_reference : null;
        $status = isset($payment->status) ? $payment->status : null;
        $date_approved = isset($payment->date_approved) ? $payment->date_approved : null;
        $email_mercado_pago = isset($payment->payer->email) ? $payment->payer->email : null;
        $pagamento_id = isset($payment->id) ? $payment->id : null;
        $tipo_pagamento = isset($payment->payment_method->id) ? $payment->payment_method->id : null;
        $preferencial_id = isset($payment->collector_id) ? $payment->collector_id : null;

        update_registro("tb_pedido_loja", 'cl_id', $external_reference, "", "", "cl_data_pagamento", $date_approved);
        update_registro("tb_pedido_loja", 'cl_id', $external_reference, "", "", "cl_email_mercado_pago", $email_mercado_pago);
        update_registro("tb_pedido_loja", 'cl_id', $external_reference, "", "", "cl_status_pagamento", $status);
        update_registro("tb_pedido_loja", 'cl_id', $external_reference, "", "", "cl_preference_id", $preferencial_id);
        update_registro("tb_pedido_loja", 'cl_id', $external_reference, "", "", "cl_pagamento_id", $pagamento_id);
        update_registro("tb_pedido_loja", 'cl_id', $external_reference, "", "", "cl_tipo_pagamento", $tipo_pagamento);

        $nomeEcommerce = utf8_encode(consulta_tabela('tb_parametros', 'cl_id', '64', 'cl_valor')); //nome do ecommerce
        $nomeFantasia = utf8_encode(consulta_tabela('tb_empresa', 'cl_id', '1', 'cl_nome_fantasia')); //nome do ecommerce

        $query = "SELECT pd.*,fpg.cl_descricao as formapagamento FROM tb_pedido_loja as pd 
        left join tb_forma_pagamento as fpg on fpg.cL_id = pd.cl_pagamento_id_interno where pd.cl_id = '$external_reference' ";
        $consulta = mysqli_query($conecta, $query);
        if ($consulta) {
            $linha = mysqli_fetch_assoc($consulta);
            $nome = utf8_encode($linha['cl_nome']);
            $nome_abreviado = $nome;
            if (strpos($nome, ' ') !== false) {
                $nome_abreviado = strstr($nome, ' ', true);
            }
            $pedido = ($linha['cl_pedido']);
            $usuario_id = ($linha['cl_usuario_id']);
            $codigo_nf = ($linha['cl_codigo_nf']);
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
            $formapagamento = utf8_encode($linha['formapagamento']);

            $valor_frete = real_format($linha['cl_valor_frete']);
            $valor_produto = real_format($linha['cl_valor_produto']);
            $desconto = ($linha['cl_desconto']);
            $valor_cupom = ($linha['cl_valor_cupom']);
            $valor_desconto = real_format($valor_cupom + $desconto);
            $valor_liquido = real_format($linha['cl_valor_liquido']);
            $valor_liquido_decimal = ($linha['cl_valor_liquido']);

            $fbp = ($linha['cl_fbp_pixel']);
            $fbc = ($linha['cl_fbc_pixel']);
        }

        $query = "SELECT * FROM tb_produto_pedido_loja where cl_codigo_nf = '$codigo_nf' ";
        $consultaProdutos = mysqli_query($conecta, $query);
        if (!$consultaProdutos) {
            $execute['data'] = array("status" => false, "type" => "aplicacao", "message" => "Erro ao realizar a consulta ao produtos do pedido função gerarLinkVendaMp " . str_replace("'", "", mysqli_error($conecta)));
            return $execute;
        }
        $dadosConta = "";

        if ($status == "approved") {
            ajusteEstoqueLoja($codigo_nf, "saida"); //ajustar o estoque

            update_registro("tb_pedido_loja", 'cl_id', $external_reference, "", "", "cl_status_compra", 'CONCLUIDO');
            if (empty($usuario_id)) {
                $verificaUser = consulta_tabela('tb_user_loja', 'cl_cpf_cnpj', $cpfcnpj, 'cl_email'); //coletar o email
                $userIDnaoLogado = consulta_tabela('tb_user_loja', 'cl_cpf_cnpj', $cpfcnpj, 'cl_id'); //coletar o id
                if (!empty($verificaUser)) { //cliente já tem cadastro
                    $dadosConta = "<div>Identificamos que você já possui um cadastro em nossa loja. Faça login utilizando seu e-mail $verificaUser e senha para acompanhar seu pedido.</div>";
                    update_registro("tb_pedido_loja", 'cl_id', $external_reference, "", "", "cl_usuario_id", $userIDnaoLogado);
                } else { //criar um usuário para o cliente
                    $registerUser = registerAuth($nome, $email, $cpfcnpj, $endereco, $bairro, $numero, $cidade, $estado, $cep, $telefone, '1');
                    if ($registerUser['data']['status']) {
                        update_registro("tb_pedido_loja", 'cl_id', $external_reference, "", "", "cl_usuario_id", $registerUser['data']['id']);
                        $usuario_id = $registerUser['data']['id'];
                        $dadosConta = "<div>Dados da sua conta:<br>
                        Email: " . $registerUser['data']['email'] . "<br>Senha: " . $registerUser['data']['senha'] . "<br>
                        Acesse a área de pedidos em nossa loja para acompanhar o seu pedido.</div>";
                    } else {
                        $dadosConta = "<div>Dados da sua conta:<br>
                        Email: erro</div>";
                    }
                }
            }

            $html = "<div style='max-width: 700px; margin-top: 5rem; margin-left: auto; margin-right: auto;'>
            <p style='margin-bottom: 1rem;'>Pedido #$pedido</p>
            <h3>Obrigado pela sua compra!</h3>
            $dadosConta
            <p>Por favor, verifique se os produtos, quantidades e formas de envio estão corretos. Se notar algo errado, entre em contato imediatamente para que possamos solucionar antes do envio.</p>
            <table width='100%'>
                <tr>
                    <td>Produto</td>
                    <td>Qtd</td>
                    <td>Valor Unit</td>
                    <td>Valor Total</td>
                </tr>";
            while ($linha = mysqli_fetch_assoc($consultaProdutos)) {
                $produto_id = ($linha['cl_produto_id']);
                $descricao = utf8_encode($linha['cl_descricao']);
                $referencia = utf8_encode($linha['cl_referencia']);
                $quantidade = $linha['cl_quantidade'];
                $valor = ($linha['cl_valor']);
                $total = $valor * $quantidade;
                $valor = real_format($valor);
                $total = real_format($total);
                $produtosCart[] = $linha; // Adicionar informações do produto favorito ao array

                $html .= "
                <tr>
                    <td>$descricao<br>$referencia</td>
                    <td>$quantidade</td>
                    <td>$valor</td>
                    <td>$total</td>
                 </tr>";
            }

            $html .= "</table>
            <hr>
            <table width='100%'>
            <tr>
                <td>Produto</td>
                <td>Frete</td>
                <td>Desconto</td>
                <td>Total</td>
            </tr>
            <tr>
                <td>$valor_produto</td>
                <td>$valor_frete</td>
                <td>$valor_desconto</td>
                <td>$valor_liquido</td>
            </tr>
            </table>

            <hr>
                <div>
                    <p>Dados de envio</p>
                        <div>
                             <span>$nome <br> $endereco - $numero, $bairro <br> $complemento <br> $cidade - $estado</span>
                        </div>
                        <div>
                             <span>$transportadora</span>
                        </div>
                        <div>
                             <span>$formapagamento</span>
                         </div>
                </div>
            </div>";

            $attbody = " Olá, $nome_abreviado,
            Agradecemos por sua compra. Seu pedido foi processado com sucesso.
            Agradecemos pela preferência.
            Atenciosamente,
            $nomeFantasia";
            $assunto = "Confirmação de Compra";
            $mail = new PHPMailer(true);
            $sendEmail = sendEmail($mail, $email, $assunto, $attbody, $html); //cliente
            if ($sendEmail) { //emeio de confirmação da compra enviado com sucesso
                update_registro("tb_pedido_loja", 'cl_id', $external_reference, "", "", "cl_email_verificado", 1);
            } else {
                update_registro("tb_pedido_loja", 'cl_id', $external_reference, "", "", "cl_email_verificado", 0);
            }

            $attbody = "Novo pedido,
            A $nomeFantasia recebeu um novo pedido que requer sua atenção e confirmação.";
            $assunto = "$nomeFantasia, pedido novo!";

            $html = "<div style='max-width: 700px;  margin-left: auto; margin-right: auto;'>
            <p style='margin-bottom: 1rem;'>Pedido #$pedido</p>
            <h3>A $nomeFantasia recebeu novo pedido que requer sua atenção e confirmação</h3>
            <hr>
            <table width='100%'>
            <tr>
                <td>Produto</td>
                <td>Frete</td>
                <td>Desconto</td>
                <td>Total</td>
            </tr>
            <tr>
                <td>$valor_produto</td>
                <td>$valor_frete</td>
                <td>$valor_desconto</td>
                <td>$valor_liquido</td>
            </tr>
            </table>

            <hr>
            <ul style='list-style-type: none; padding-left: 0;'>
                <li><strong>Pagamento:</strong> $formapagamento</li>
                <li><strong>Dados de envio:</strong> $transportadora</li>
                <li><strong>Dados do cliente:</strong></li>
                <li>$nome</li>
                <li>$email</li>
                <li>$telefone</li>
                <li>$endereco - $numero, $bairro, $complemento</li>
                <li>$cidade - $estado</li>
              </ul>
             </div>";
            $sendEmailConfirmar = sendEmailConfirm($mail, $assunto, $attbody, $html); //gerenciador 

            /*pixel */
            $dados = [
                'dados_usuario' => [
                    "id" => $usuario_id,
                    "nome" => $nome,
                    "email" => $email,
                    "cep" => $cep,
                    "telefone" => $telefone,
                    "cidade" => $cidade,
                    "estado" => $estado,
                ],
                'dados' => [
                    "pagina" => "?finalizado",
                    "valor_total" => $valor_liquido_decimal,
                    "forma_pagamento" => $formapagamento,
                    "fbp" => $fbp,
                    "fbc" => $fbc,
                ],
                "produtos" => $produtosCart,
            ];
            pixel('Purchase', $dados);
        } elseif ($status == "pending" or $status == "in_process") {
            //    ajusteEstoqueLoja($codigo_nf, "saida");
            if (empty($usuario_id)) {
                $registerUser = registerAuth($nome, $email, $cpfcnpj, $endereco, $bairro, $numero, $cidade, '', $cep, $telefone, '1');
                if ($registerUser['data']['status']) {
                    update_registro("tb_pedido_loja", 'cl_id', $external_reference, "", "", "cl_usuario_id", $registerUser['data']['id']);

                    $dadosConta = "<div>Dados da sua conta, acesse a área de pedidos para acompanhar o seu pedido<br>
                    Email: " . $registerUser['data']['email'] . "<br>Senha: " . $registerUser['data']['senha'] . "</div>";
                }
            }

            $html = "<div style='max-width: 700px; margin-top: 5rem; margin-left: auto; margin-right: auto;'>
            <p style='margin-bottom: 1rem;'>Pedido #$pedido</p>
            <h3>Obrigado pela sua compra!</h3>
            $dadosConta
            <p>Estamos aguardando a confirmação do pagamento, que pode levar até 72 horas (esse prazo pode variar de acordo com o método de pagamento escolhido). Geralmente, a validação do pagamento com cartão de crédito é instantânea.
            Não se preocupe, você receberá uma notificação assim que o pagamento for confirmado.</p>
            
            <table width='100%'>
                <tr>
                    <td>Produto</td>
                    <td>Qtd</td>
                    <td>Valor Unit</td>
                    <td>Valor Total</td>
                </tr>";
            while ($linha = mysqli_fetch_assoc($consultaProdutos)) {
                $descricao = utf8_encode($linha['cl_descricao']);
                $referencia = utf8_encode($linha['cl_referencia']);
                $quantidade = $linha['cl_quantidade'];
                $valor = ($linha['cl_valor']);
                $total = $valor * $quantidade;
                $valor = real_format($valor);
                $total = real_format($total);

                $html .= "
                <tr>
                    <td>$descricao<br>$referencia</td>
                    <td>$quantidade</td>
                    <td>$valor</td>
                    <td>$total</td>
                 </tr>";
            }

            $html .= "</table>
            <hr>
            <table width='100%'>
            <tr>
                <td>Produto</td>
                <td>Frete</td>
                <td>Desconto</td>
                <td>Total</td>
            </tr>
            <tr>
                <td>$valor_produto</td>
                <td>$valor_frete</td>
                <td>$valor_desconto</td>
                <td>$valor_liquido</td>
            </tr>
            </table>

            <hr>
                <div>
                    <p>Dados de envio</p>
                        <div>
                             <span>$nome <br> $endereco - $numero, $bairro <br> $complemento <br> $cidade - $estado</span>
                        </div>
                        <div>
                             <span>$transportadora</span>
                        </div>
                </div>
            </div>";

            $attbody = " Olá, $nome_abreviado,
            Agradecemos por sua compra. Seu pedido #$pedido está em processamento.
            Agradecemos pela preferência.
            Atenciosamente,
            $nomeFantasia";
            $assunto = "Confirmação de Compra";

            $mail = new PHPMailer(true);
            $sendEmail = sendEmail($mail, $email, $assunto, $attbody, $html);
            if ($sendEmail) { //emeio de confirmação da compra enviado com sucesso
                update_registro("tb_pedido_loja", 'cl_id', $external_reference, "", "", "cl_email_verificado", 1);
            } else {
                update_registro("tb_pedido_loja", 'cl_id', $external_reference, "", "", "cl_email_verificado", 0);
            }
        } elseif ($status == "cancelled") {
            ajusteEstoqueLoja($codigo_nf, "cancelado");
            update_registro("tb_pedido_loja", 'cl_id', $external_reference, "", "", "cl_status_compra", 'CANCELADO');

            $html = "<div style='max-width: 700px; margin-top: 5rem; margin-left: auto; margin-right: auto;'>
            <p style='margin-bottom: 1rem;'>Pedido #$pedido</p>
            <h3>Olá $nome_abreviado, lamentamos informar que seu pedido foi cancelado.</h3>
            <p>Se tiver alguma dúvida, por favor, não hesite em responder a esta mensagem.
            Estamos à sua disposição para ajudar!</p>";

            $attbody = "Olá $nome_abreviado, lamentamos informar que seu pedido #$pedido foi cancelado";
            $assunto = "Cancelamento de pedido";

            $mail = new PHPMailer(true);
            $sendEmail = sendEmail($mail, $email, $assunto, $attbody, $html);
            if ($sendEmail) { //emeio de confirmação da compra enviado com sucesso
                update_registro("tb_pedido_loja", 'cl_id', $external_reference, "", "", "cl_email_verificado", 1);
            } else {
                update_registro("tb_pedido_loja", 'cl_id', $external_reference, "", "", "cl_email_verificado", 0);
            }
        } elseif ($status == "rejected") {
            ajusteEstoqueLoja($codigo_nf, "cancelado");
        } else {
            ajusteEstoqueLoja($codigo_nf, "cancelado");
        }
    }
}
