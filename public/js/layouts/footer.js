const formNewsletter = document.getElementById("newsletter");

$("#newsletter").submit(function (e) {
    e.preventDefault();
    var formularioNewsletter = $(this);
    createNewsletter(formularioNewsletter);
})


function createNewsletter(dados) {
    $("#enviar_newsletter").prop('disabled', true);//desabilitar o botão

    // Remover classes is-invalid de todos os elementos
    dados.find('.is-invalid').removeClass('is-invalid');
    dados.find('.invalid-feedback').text('');
    $(".span-loader").html('<div class="loader"></div>');

    $.ajax({
        type: "POST",
        data: "form=true&acao=create&" + dados.serialize(),
        url: "app/Http/Controllers/Footer.php",
        async: false
    }).then(sucesso).fail(falha).always(function () {
        $(".span-loader").html('');
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
            formNewsletter.reset()
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
        $("#enviar_newsletter").prop('disabled', false);//desabilitar o botão
    }

    function falha() {
        console.log("erro");
    }

}

