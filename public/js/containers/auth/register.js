
/*abrir o modal para se realizar o login */
$("#login").click(function (e) {
    e.preventDefault()
    modalLogin()
})


$('#register').submit(function (e) {
    e.preventDefault();
    var formulario = $(this);
    register(formulario);
});

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
function modalMsgConfimEmail(email) {
    $(".btn-close").trigger('click'); //fechar o modal
    $.ajax({
        type: 'GET',
        data: "page=auth&containers=confirmEmailPedding&email=" + email,
        url: "resources/views/containers/auth/confirmEmailMessagePending.php",
        success: function (result) {
            return $("main .modal-externo").html(result) + $("#modal_confirm_email_message_pending").modal('show');;
        },
    });
}

function register(dados) {
    // Remover classes is-invalid de todos os elementos
    dados.find('.is-invalid').removeClass('is-invalid');
    dados.find('.invalid-feedback').text('');
    $("#next").prop("disabled", true);

    $.ajax({
        type: "POST",
        data: "form=auth&acao=register&" + dados.serialize(),
        url: "app/Http/Controllers/Auth/Register.php",
        async: false
    }).then(sucesso, falha);

    function sucesso(data) {
        var $data = $.parseJSON(data)["data"];
        if ($data.status == true) {
            modalMsgConfimEmail($data.email)
        } else {
            $("#next").prop("disabled", false);

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