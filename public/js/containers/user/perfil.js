//mostrar as informações no formulario show
const params = new URLSearchParams(window.location.search); // pesquisa pela descrição


$(document).ready(function () {
    show()
    $('#telefone').inputmask('(99) 99999-9999'); // Defina a máscara desejada para o telefone
    $('#cep').inputmask('99999-999'); // Defina a máscara desejada para o telefone
})

$('#cep').on('change', function () {
    var cep = $("#cep").val();
    $(".span-loader").html('<div class="loader"></div>');
    consultarEndereco(cep);
});



function consultarEndereco(cep) {
    $('input').each(function () {
        $(this).removeClass('is-invalid');
        $(this).siblings('.invalid-feedback').text('');
    });

    $(".option-frete").html('')
    $.ajax({
        type: "POST",
        data: {
            form: 'frete',
            acao: 'consultarDados',
            tipo: 'consultar',
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

            $("#endereco").val($data.dadosCep.logradouro)
            $("#bairro").val($data.dadosCep.bairro)
            $("#cidade").val($data.dadosCep.localidade)
        } else {
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
    }

    function falha() {
        console.log("Erro na requisição AJAX");
    }
}



$('#user').submit(function (e) {
    e.preventDefault();
    var formulario = $(this);
    //e.preventDefault()
    Swal.fire({
        title: 'Tem certeza?',
        text: "Deseja alterar essa informação ?",
        icon: 'warning',
        showCancelButton: true,
        cancelButtonText: 'Não',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim'
    }).then((result) => {
        if (result.isConfirmed) {
            update(formulario);
        }
    })

});

function update(dados) {
    $(".span-loader").html('<div class="loader"></div>');

    // Remover classes is-invalid de todos os elementos
    dados.find('.is-invalid').removeClass('is-invalid');
    dados.find('.invalid-feedback').text('');
    $("#update").prop("disabled", true);

    $.ajax({
        type: "POST",
        data: "form=auth&acao=update&" + dados.serialize(),
        url: "app/Http/Controllers/User.php",
        async: false
    }).then(sucesso).fail(falha).always(function () {
        $(".span-loader").html('');
        $("#update").prop("disabled", false);

    });

    function sucesso(data) {
        var $data = $.parseJSON(data)["data"];
        if ($data.status == true) {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: $data.message,
                showConfirmButton: false,
                timer: 3500
            })
    
            setTimeout(function () {
                $(".btn-close").trigger('click')
                location.reload(true); // O parâmetro true força o carregamento do cache do servidor

            }, 2000);

        } else {
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
    }

    function falha() {
        console.log("erro");
    }
}




function show() {//preencher os campos do usuário de acordo com o seu cadastro
    $(".span-loader").html('<div class="loader"></div>');
    $.ajax({
        type: "POST",
        data: "form=true&acao=show&" + params,
        url: "app/Http/Controllers/User.php",
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
            $("#endereco").val($data.response['endereco'])
            $("#bairro").val($data.response['bairro'])
            $("#cidade").val($data.response['cidade'])

            // var cep = $data.response['cep']
            // consultarFrete(cep);
        }
    }

    function falha() {
        console.log("erro");
    }

}
