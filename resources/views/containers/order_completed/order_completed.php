<?php include "../../../../app/Http/Controllers/OrderCompleted.php" ?>
<div class="container-lg order_completed  mt-3 p-0">
    <?php
    if ($qtd_registro > 0) {
    ?>
        <div class="row g-4">
            <div class="col-md-5 col-lg-4  order-md-last order-2 text-center bloco-right">
                <div class="p-3 border-0 shadow gx-5 rounded">
                    <div>
                        <?php while ($linha = mysqli_fetch_assoc($consultaProdutos)) {
                            $descricao = utf8_encode($linha['cl_descricao']);
                            $referencia = utf8_encode($linha['cl_referencia']);
                            $quantidade = $linha['cl_quantidade'];
                            $valor = ($linha['cl_valor']);
                            $total = $valor * $quantidade;
                            $valor = real_format($valor);
                            $total = real_format($total);

                            $codigo = ($linha['cl_codigo']);

                            $imagem_produto_default = verficar_paramentro($conecta, 'tb_parametros', "cl_id", "34");
                            $img_produto = consulta_tabela_query(
                                $conecta,
                                "select * from tb_imagem_produto where cl_codigo_nf ='$codigo' order by cl_ordem asc limit 1",
                                'cl_descricao'
                            );
                            $diretorio_imagem  = $img_produto == "" ? "../../../../../$empresa/$imagem_produto_default" : "../../../../../$empresa/img/produto/$img_produto";


                            $condicao = ($linha['cl_condicao']);
                            $span_condicao = $condicao == "USADO" ? "Usado" : '';
                        ?>

                            <div class="card  bg-body-tertiary  border-0 position-relative mb-1 ">
                                <div class=" g-0 position-relative d-flex justify-content-start just align-items-center">
                                    <div class="position-relative">
                                        <!-- <img src="..." class="img-fluid rounded-start" alt="..."> -->
                                        <img class="img-thumbnail border-0" width="100" src='<?= $diretorio_imagem; ?>' alt="<?= $descricao; ?>">
                                        <div class="item-condition rounded shadow bg-danger px-2 text-light">
                                            <span class="fw-semibold"><?= $span_condicao; ?></span>
                                        </div>
                                    </div>

                                    <div class="card-body text-start">
                                        <div class="mb-2">
                                            <p class="card-title fw-bold"><?= $descricao . " x " . $quantidade; ?> </p>
                                            <p class="card-subtitle text-muted"><?= $referencia; ?></p>
                                        </div>
                                        <div class="card-text fw-bold"><?= $total; ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="mt-4">
                            <div class="d-flex justify-content-between">
                                <div class="text-muted">Subtotal</div>
                                <div class="text-muted valorSubTotalCheckout"><?= real_format($valor_produto); ?></div>
                            </div>
                            <?php if ($valor_frete > 0) { ?>
                                <div class="d-flex justify-content-between">
                                    <div class="text-muted">Frete</div>
                                    <div class="text-muted"><?= real_format($valor_frete); ?></div>
                                </div>
                            <?php } ?>
                            <?php if ($valor_desconto > 0) { ?>
                                <div class="d-flex justify-content-between">
                                    <div class="text-muted">Desconto</div>
                                    <div class="text-muted">- <?= real_format($valor_desconto); ?></div>
                                </div>
                            <?php } ?>
                            <?php if ($valor_cupom > 0) { ?>
                                <div class="d-flex justify-content-between">
                                    <div class="text-muted">Cupom</div>
                                    <div class="text-muted">- <?= real_format($valor_cupom); ?></div>
                                </div>
                            <?php } ?>
                            <hr>
                            <div class="d-flex justify-content-between mb-4">
                                <div class="fw-bold">Total</div>
                                <div class="fw-bold valorTotalCheckout"><?= real_format($valor_liquido); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-7 col-lg-8 order-1 ">
                <div class="border p-3 rounded mb-3">
                    <div>
                        <p class="mb-3">Pedido <b>#<?= $pedido; ?></b></p>
                        <?php if ($status_compra == "CANCELADO") { ?>
                            <h5 class="text-danger">Pedido Cancelado</h5>
                            <span>O pedido foi cancelado</span>
                        <?php } elseif ($status_pagamento == "approved") { ?>
                            <h5 class="text-success">Pedido Confirmado</h5>
                            <span>O pagamento foi aprovado. Seu pedido está confirmado.</span>
                        <?php } elseif ($status_pagamento == "pending" or $status_pagamento == "in_process") { ?>
                            <h5 class="text-warning">Aguardando Pagamento</h5>
                            <span>Seu pagamento está sendo processado.</span>
                        <?php } elseif ($status_pagamento == "rejected") { ?>
                            <h5 class="text-danger">Pagamento Rejeitado</h5>
                            <span>O pagamento foi rejeitado. Infelizmente não podemos prosseguir com o seu pedido neste momento.</span>
                        <?php } elseif ($status_pagamento == "cancelled") { ?>
                            <h5 class="text-danger">Pagamento não concluido</h5>
                            <span>O pagamento não foi concluido. Infelizmente não podemos prosseguir com o seu pedido neste momento.</span>
                        <?php } else { ?>
                            <h5 class="text-secondary">Status Desconhecido</h5>
                            <span>O status do pagamento não pôde ser verificado. Entre em contato conosco para mais informações.</span>
                        <?php } ?>
                        <?php if ($status_pagamento == "approved" or $status_pagamento == "in_process" or $status_pagamento == "pending") {
                            if ((auth('') !== false and auth('')['id'] == $usuario_id)) { ?>
                                <div class="mb-2"> <a href="?user=order" class="btn btn-track-order rounded mt-3">Acompanhar Pedido</a> </div>
                            <?php
                            } else { ?>
                                <div class="mb-2">
                                    <span class="mb-1">Esteja atento à sua caixa de entrada! Em breve,
                                        receberá um e-mail importante de <?= $email_empresa ?>
                                        com detalhes sobre o seu pedido.</span>
                                    <span>O e-mail será enviado para
                                        <b><?= $email; ?></b>. Caso encontre algum problema,
                                        entre em contato conosco para correção.</span>
                                </div>
                        <?php }
                        } ?>
                        <hr>
                        <p class="mb-1 fw-normal">Detalhes do Pedido</p>
                        <nav>
                            <ul style="list-style-type: none; padding-left: 0;">
                                <li><i class="bi bi-person"></i> <?= $nome; ?></li>
                                <li><i class="bi bi-envelope"></i> <?= $email; ?></li>
                                <li><i class="bi bi-telephone"></i> <?= $telefone; ?></li>
                                <li><i class="bi bi-geo-alt"></i> <?= $cep . ", " . $endereco . " - " . $numero . ", " . $bairro . ", " . $cidade . " - " . $estado; ?></li>
                                <li><i class="bi bi-truck"></i> <?php if ($transportadora == "Retirada") {
                                                                    echo "$transportadora em $retirada_endereco <br> $instrucao_retirada";
                                                                } else {
                                                                    echo  $transportadora;
                                                                }  ?></li>
                                <li><i class="bi bi-wallet"></i> <?= $formapagamento;  ?></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    <?php
    } else {
    ?>
        <div style="max-width: 400px;margin:0 auto" class="border text-center border-primary-subtle rounded p-2 text-center">
            <a href="./">
                <i class="bi bi-info-circle"></i> Adicione produtos ao seu carrinho
            </a>
        </div>
    <?php
    }
    ?>
</div>