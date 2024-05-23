const params = new URLSearchParams(window.location.search); // pesquisa pela descrição
const code = new URLSearchParams(window.location.search).get('code'); // pesquisa pela descrição




/*abrir o modal para se realizar o login */
function modalLogin() {
    $(".btn-close").trigger('click'); //fechar o modal
    $.ajax({
        type: 'GET',
        data: "page=auth&containers=login",
        url: "resources/views/containers/auth/login.php",
        success: function (result) {
            return $("main .modal-externo").html(result) + $("#modal_login").modal('show');;
        },
    });
}

var simulacaoGlobal; // Variável global para armazenar simulacao
var valorTotalGlobal; // Variável global para armazenar o valor total
$(document).ready(function () {

    $('#telefone').inputmask('(99) 99999-9999'); // Defina a máscara desejada para o telefone
    $('#cep').inputmask('99999-999'); // Defina a máscara desejada para o telefone


    show()

    $('#cep').on('change', function () {
        var cep = $("#cep").val();
        $(".span-loader").html('<div class="loader"></div>');
        consultarFrete(cep);
    });

    $(".alter-cep").click(function () {
        $('.information-cep').css('display', 'block')
        $("#cep").val('')
        $('.customer-address').css('display', 'none')
        $(".option-frete").html('')
    })

    $("#checkout").submit(function (e) {
        e.preventDefault();
        var formularioCheckout = $(this);
        createChekout(formularioCheckout);
    })


})

function consultarFrete(cep) {
    $('input').each(function () {
        $(this).removeClass('is-invalid');
        $(this).siblings('.invalid-feedback').text('');
    });
    $(".option-frete").html('')
    $.ajax({
        type: "POST",
        data: {
            form: 'frete',
            acao: 'consultarFrete',
            tipo: 'opcao',
            produtoID: '',
            cep: cep
        },
        url: "app/Http/Controllers/Frete.php",
        async: false
    }).then(sucesso).fail(falha).always(function () {
        $(".span-loader").html('');
    });

    function sucesso(data) {

        var $data = $.parseJSON(data)["data"];

        if ($data.status == true) {

            var dadosCep = $data.dadosCep
            var simulacao = $data.simulacao
            simulacaoGlobal = $data.simulacao; // Armazena simulacao na variável global
            //  console.log(simulacaoGlobal)
            $(".option-frete").html($data.response);
            $(".logradouro").html($data.dadosCep.logradouro)
            $(".cep").html($data.dadosCep.cep)
            $(".bairro").html($data.dadosCep.bairro)
            $(".localidade").html($data.dadosCep.localidade)
            $(".uf").html($data.dadosCep.uf)

            $('.customer-address').css('display', 'block')
            $('.information-cep').css('display', 'none')


            $('.selected_simulacao').click(function () {
                calcularValor(simulacao)
            })

        } else {
            $('.customer-address').css('display', 'none')
            $('.information-cep').css('display', 'block')
            // console.log($data.response)
            if ($data.response !== undefined) { //erro de usuário
                $.each($data.response, function (key, value) {
                    $("#" + key).addClass("is-invalid")
                    $(".feedback-" + key).addClass("invalid-feedback").html(value)
                });
            } else { //erro de aplicação
                Swal.fire({
                    icon: 'error',
                    title: 'Verifique!',
                    text: $data.message,
                    timer: 7500,
                })
            }
        }
        $(".span-loader").html('');
    }

    function falha() {
        console.log("Erro na requisição AJAX");
    }
}



function createChekout(dados) {
    $("#btn_checkout").prop('disabled', true);//desabilitar o botão

    // Remover classes is-invalid de todos os elementos
    dados.find('.is-invalid').removeClass('is-invalid');
    dados.find('.invalid-feedback').text('');
    $(".span-loader").html('<div class="loader"></div>');

    $.ajax({
        type: "POST",
        data: "form=true&acao=create&codigo_nf=" + code + "&simulacaoFrete=" + JSON.stringify(simulacaoGlobal) + "&" + dados.serialize(),
        url: "app/Http/Controllers/Checkout.php",
        async: false
    }).then(sucesso).fail(falha).always(function () {
        $(".span-loader").html('');
    });
    function sucesso(data) {
        var $data = $.parseJSON(data)["data"];
        if ($data.status == true) {
            window.location.href = $data.link_order

            // console.log($data.response)
        } else {
            // console.log($data.response)
            if ($data.response) { //erro de usuário
                $.each($data.response, function (key, value) {
                    $("#" + key).addClass("is-invalid")
                    $(".feedback-" + key).addClass("invalid-feedback").html(value)
                });

                // Scroll para a primeira div com a classe "is-invalid"
                if ($(document).height() > $(window).height()) {
                    const firstInvalidElement = $(".is-invalid")[0];
                    if (firstInvalidElement) {
                        firstInvalidElement.scrollIntoView({ behavior: 'smooth' });
                    }
                }
            } else { //erro de aplicação
                Swal.fire({
                    icon: 'error',
                    title: 'Verifique!',
                    text: $data.message,
                    timer: 7500,

                })
            }
            $("#btn_checkout").prop('disabled', false);//desabilitar o botão

        }
    }

    function falha() {
        console.log("erro");
    }

}



$('.payments').click(function () {
    calcularValor(simulacaoGlobal)
})




function show() {//preencher os campos do usuário de acordo com o seu cadastro
    $(".span-loader").html('<div class="loader"></div>');
    $.ajax({
        type: "POST",
        data: "form=true&acao=show&" + params,
        url: "app/Http/Controllers/Checkout.php",
        async: false
    }).then(sucesso).fail(falha).always(function () {
        $(".span-loader").html('');
    });

    function sucesso(data) {

        $data = $.parseJSON(data)["data"];
        if ($data.status == true) {
            $("#nome").val($data.response['nome'])
            $("#cpfcnpj").val($data.response['cpf_cnpj'])
            $("#email").val($data.response['email'])
            $("#cep").val($data.response['cep'])
            $("#telefone").val($data.response['telefone'])
            $("#numero").val($data.response['numero'])
            $("#complemento").val($data.response['complemento'])
            $("#cupom").val($data.response['cupom'])

            $('input[name="payment"]').filter('[value="' + $data.response['forma_pagamento_id'] + '"]').prop('checked', true);

            var cep = $data.response['cep']
            consultarFrete(cep);
            calcularValor(simulacaoGlobal)

        }
    }

    function falha() {
        console.log("erro");
    }
}




$('#cupom').on('change', function () {
    calcularValor(simulacaoGlobal)
})


function calcularValor(simulacaoFrete) {
    $(".span-loader").html('<div class="loader"></div>');
    var dados = {}; // Objeto para armazenar os dados a serem enviados na solicitação AJAX

    $('input[type="radio"]:checked').each(function () {
        var name = $(this).attr('name');
        var valor = $(this).val(); // Obtém o valor do input
        dados[name] = valor; // Adiciona o nome do ID como chave e o valor como valor ao objeto dados
    });

    $('input[type="text"]').each(function () {
        var name = $(this).attr('name');
        var valor = $(this).val(); // Obtém o valor do input
        dados[name] = valor; // Adiciona o nome do ID como chave e o valor como valor ao objeto dados
    });

    $.ajax({
        type: "POST",
        data: {
            form: true,
            acao: 'calcularValor',
            codigo_nf: code,
            simulacaoFrete: simulacaoFrete,
            dados: dados
        }, // Correção na sintaxe para enviar um objeto
        url: "app/Http/Controllers/Checkout.php",
        async: false
    }).then(sucesso).fail(falha).always(function () {
        $(".span-loader").html('');
    });

    function sucesso(data) {
        var $data = $.parseJSON(data)["data"];
        if ($data.status == true) {

            if ($data.response['valorDescontoDecimal'] > 0) {
                $(".spanDescontoCheckout").html('<div class="d-flex justify-content-between"><div class="my-0">Desconto</div><div class="text-muted valorDescontoCheckout">' + $data.response['valorDesconto'] + '</div></div>');
            } else {
                $(".spanDescontoCheckout").html('')
            }

            if ($data.response['valorCupomDecimal'] > 0) {//cupom valido
                $(".spanDescontoCupom").html('<div class="d-flex justify-content-between"><div class="my-0">Cupom</div><div class="text-muted valorCupomCheckout">' + $data.response['valorCupom'] + '</div></div>');
                $(".feedback-cupom").html('')
            } else {//cupom invalido
                $(".spanDescontoCupom").html('')
                $(".feedback-cupom").html("<span class='text-danger'>" + $data.response['msgCupom'] + "</span>")

            }

            // $(".valorSubTotalCheckout").html()
            $(".valorFreteCheckout").html($data.response['valorFrete'])
            $(".valorTotalCheckout").html($data.response['valorTotal'])



        }
    }

    function falha() {
        console.log("Erro na solicitação AJAX");
    }
}


