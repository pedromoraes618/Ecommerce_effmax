const code = new URLSearchParams(window.location.search).get('code'); // pesquisa pela descrição
$("#confirmOrder").submit(function (e) {
    e.preventDefault();
    var formulario = $(this);
    createOrder(formulario);
})
function createOrder(dados) {
    $("#btn_confirm_order").prop('disabled', true);//desabilitar o botão

    // Remover classes is-invalid de todos os elementos
    dados.find('.is-invalid').removeClass('is-invalid');
    dados.find('.invalid-feedback').text('');
    $(".span-loader").html('<div class="loader"></div>');

    $.ajax({
        type: "POST",
        data: "form=true&acao=create&codigo_nf=" + code,
        url: "app/Http/Controllers/ConfirmOrder.php",
        async: false
    }).then(sucesso).fail(falha).always(function () {
        $(".span-loader").html('');
    });
    function sucesso(data) {
        var $data = $.parseJSON(data)["data"];
        if ($data.status == true) {
            $("#btn_confirm_order").html("Rederecionando para o pagamento");//desabilitar o botão

            setTimeout(function () {
                window.location.href = $data.link_externo
            }, 2000); //
        } else {
            if ($data.response !== undefined) { //erro de usuário
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
            $("#btn_confirm_order").prop('disabled', false);//desabilitar o botão

        }
    }

    function falha() {
        console.log("erro");
    }

}
