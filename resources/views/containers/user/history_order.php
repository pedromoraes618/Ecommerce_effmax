<?php include "../../../../app/Http/Controllers/User.php" ?>
<?php if ($qtd_registro_historico > 0) { ?>
    <div class="table-responsive">

        <table class="table table-hover">
            <thead>
                <tr>

                    <th scope="col">Data</th>
                    <th scope="col">#Pedido</th>
                    <th scope="col">Status</th>
                    <th scope="col">Valor</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php

                while ($linha = mysqli_fetch_assoc($consultaHistorico)) {
                    $pedido_id = ($linha['cl_id']);
                    $codigo_nf = ($linha['cl_codigo_nf']);
                    $nome = utf8_encode($linha['cl_nome']);
                    $data_pedido = ($linha['cl_data']);
                    $pedido = ($linha['cl_pedido']);
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
                    $status_pagamento = utf8_encode($linha['cl_status_pagamento']);
                    $status_compra = utf8_encode($linha['cl_status_compra']);
                    $codigo_rastreio = ($linha['cl_codigo_rastreio']);

                    $valor_frete = ($linha['cl_valor_frete']);
                    $valor_produto = ($linha['cl_valor_produto']);
                    $valor_desconto = ($linha['cl_desconto']);
                    $valor_liquido = ($linha['cl_valor_liquido']);

                    
                    if ($status_pagamento == "approved") {
                        $status_pagamento = "Aprovado";
                    } elseif ($status_pagamento == "pending") {
                        $status_pagamento = "Pagamento em pendente";
                    } elseif ($status_pagamento == "in_process") {
                        $status_pagamento = "Pagamento em processamento";
                    } elseif ($status_pagamento == "rejected") {
                        $status_pagamento = "Pagamento rejeitado";
                    } elseif ($status_pagamento == "cancelled") {
                        $status_pagamento = "Pagamento não concluido";
                    }

                    if ($status_compra == "CANCELADO") {
                        $status_pagamento = "Cancelado";
                    }
                ?>
                    <tr>
                        <th><?php echo formatDateB($data_pedido); ?></th>
                        <td><?php echo $pedido ?></td>
                        <td><?php echo ($status_pagamento); ?>
                        <td><?php echo real_format($valor_liquido); ?></td>
                        <td class="td-btn">
                            <div class="btn-group">
                                <button type="buttom" id='<?php echo $pedido_id; ?>' class="btn btn-sm btn-dark btn-det">Detalhe</button>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>

        </table>
    </div>
<?php } ?>
<script src="public/js/containers/user/history_order.js"> </script>