
const produtoID = new URLSearchParams(window.location.search).get('product-details'); // pesquisa pela descrição


$('#consultarFrete').submit(function (e) {
    e.preventDefault();
    var formulario = $(this);
    consultarFrete(formulario, produtoID);
});


function consultarFrete(dados, id) {
    // Remover classes is-invalid de todos os elementos
    dados.find('.is-invalid').removeClass('is-invalid');
    dados.find('.invalid-feedback').text('');
    $(".span-loader").html('<div class="loader"></div>');
    $(".option-frete").html('')

    $.ajax({
        type: "POST",
        data: "form=frete&acao=consultarFrete&tipo=consulta&produtoID=" + id + "&" + dados.serialize(),
        url: "app/Http/Controllers/Frete.php",
        async: false
    }).then(sucesso, falha);

    function sucesso(data) {
        var $data = $.parseJSON(data)["data"];
        if ($data.status == true) {
            $(".option-frete").html($data.response);
            // console.log($data.response)
        } else {
            // console.log($data.response)
            if ($data.response !== undefined) { //erro de usuário
                $.each($data.response, function (key, value) {

                    // Adicionar classe is-invalid e mensagem de erro aos elementos correspondentes
                    // dados.find('[name="' + key + '"]').addClass('is-invalid');
                    // dados.find('[name="' + key + '"]').siblings('.invalid-feedback').html(value);
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
        console.log("erro");
    }

}