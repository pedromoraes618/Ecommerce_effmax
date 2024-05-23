<?php include "../../../../app/Http/Controllers/User.php";

if ($qtd_registro > 0) { ?>
    <div class="container p-3  "  style="background-color:#F8F9FA">
        <h6 class="fw-semibold mb-3">Pedido #<?= $pedido; ?></h6>
        <div class="row">
            <?php if (($status_compra == "CONCLUIDO" or $status_compra == "ANDAMENTO")  and ($status_pagamento == "approved" or
                $status_pagamento == "pending" or $status_pagamento == "in_process")) { ?>
                <div class="col-md-auto mb-3">
                    <div class="d-flex justify-content-center p-1 ">
                        <div class="status-line">
                            <div class="<?php if ($status_compra == "CONCLUIDO") {
                                            echo 'completed';
                                        } else {
                                            echo 'incompleted';
                                        } ?>"><i class="bi bi-check-circle-fill fs-4"></i></div>
                            <div class="connector"></div>
                            <div class="<?php if ($status_pagamento == "approved") {
                                            echo 'completed';
                                        } else {
                                            echo 'incompleted';
                                        } ?>"> <i class="bi bi-check-circle-fill fs-4"></i></div>

                            <div class="connector"></div>
                            <div class=" <?php if (($status_pagamento == "approved" and $transportadora == "Retirada") or
                                                ($status_pagamento == "approved" and  $transportadora != "Retirada" and
                                                    !empty($codigo_rastreio)) or ($id_simulacao_frete == "grtlocalizacao" and formatDateB($data_entrega) != '')
                                            ) {
                                                echo 'completed';
                                            } else {
                                                echo 'incompleted';
                                            } ?>">
                                <i class="bi bi-check-circle-fill fs-4"></i>
                            </div>
                        </div>
                        <div style="font-size:0.8em">
                            <div class="status mb-3 ">
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <div class="item">Pedido realizado</div>
                                    <div class="sub-item"><?= formatarDataTimeStampToDataData($data_pedido); ?></div>
                                </div>
                            </div>

                            <div class="status mb-3 ">
                                <i class="fas fa-check-circle"></i>
                                <div class="item"> Pagamento confirmado</div>
                            </div>


                            <div class="status">
                                <i class="fas fa-check-circle"></i>
                                <div class="item">
                                    <?php if ($transportadora == "Retirada") {
                                        echo "Pronto para retirada ";
                                    } elseif ($transportadora != "Retirada" and !empty($codigo_rastreio)) {
                                        echo 'Objeto postado';
                                    } elseif ($id_simulacao_frete == "grtlocalizacao") {
                                        echo "<div class='item'>Entregue</div>
                                        <div class='sub-item'>" . formatDateB($data_entrega) . "</div>";
                                    } else {
                                        echo 'Objeto postado';
                                    } ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
                    if (!empty($codigo_rastreio)) {
                        $status_response = rastrearObjetoKangu($codigo_rastreio, $accesstoken);

                        // Extrai o status do response
                        $status = "";
                        $data_pedido = "";

                        // Exibir a linha de status dinamicamente

                        if ($status_response["data"]["status"]) {
                            $historico = array_reverse($status_response["data"]["response"]["historico"]);
                            $total_items = count($historico);
                    ?>
                            <hr>
                            <div class="d-flex justify-content-center p-1 ">
                                <div class="status-line">
                                    <?php foreach ($historico as $index => $evento) { ?>
                                        <div class="completed">
                                            <i class="bi bi-check-circle-fill fs-4"></i>
                                        </div>
                                        <?php if ($index < $total_items - 1) : ?>
                                            <div class="connector"></div>
                                        <?php endif; ?>
                                    <?php }; ?>
                                </div>

                                <div>
                                    <?php foreach ($historico as $evento) {
                                    ?>
                                        <div class="status completed mb-4 ">
                                            <i class="fas fa-check-circle"></i>
                                            <div style="font-size:0.75em">
                                                <div class="item"><?= formatDateB($evento['data']) . " - " . $evento['ocorrencia'] . " - " . $evento['recebedor']; ?><br>

                                                </div>

                                            </div>
                                        </div>
                                    <?php }; ?>
                                </div>
                            </div>
                    <?php }
                    } ?>
                </div><?php } ?>


            <div class="col-md">
                <div class=" rounded mb-3">
                    <div>
                        <?php
                        if ($status_compra == "CANCELADO") { ?>
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
                        <?php } elseif ($status_compra == "ANDAMENTO") {
                        ?>
                            <h5 class="text-dark">Pedido em andamento</h5>
                            <span>O pedido ainda não foi finalizado</span>
                        <?php
                        } else { ?>
                            <h5 class="text-secondary">Status Desconhecido</h5>
                            <span>O status do pagamento não pôde ser verificado. Entre em contato conosco para mais informações.</span>
                        <?php } ?>


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
                                <?php if (!empty($pdf_nf)) { ?>
                                    <li><a href="<?= $caminho_nf; ?>" target='_blank'><i class='bi bi-stickies'></i></a> Nota fiscal</li>
                                <?php } ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="px-2 border-1 border  rounded">
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
                                        <img class="img-thumbnail  bg-body-tertiary  border-0" width="100" src='<?= $diretorio_imagem; ?>' alt="<?= $descricao; ?>">
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
        </div>
    </div>
<?php } ?>