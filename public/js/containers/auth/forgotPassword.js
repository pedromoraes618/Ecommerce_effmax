
function modalforgotPasswordMessage() {
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

function modalMsgForgotPassword(email) {
    $(".btn-close").trigger('click'); //fechar o modal
    $.ajax({
        type: 'GET',
        data: "page=auth&containers=ForgotPasswordMessage&email=" + email,
        url: "resources/views/containers/auth/forgotPasswordMessage.php",
        success: function (result) {
            return $("main .modal-externo").html(result) + $("#modal_forgot_password_message").modal('show');;
        },
    });
}

$('#forgotPassword').submit(function (e) {
    e.preventDefault();
    var formulario = $(this);
    forgotPassword(formulario);
});

function forgotPassword(dados) {
    // Remover classes is-invalid de todos os elementos
    $(".span-loader").html('<div class="loader"></div>');
    dados.find('.is-invalid').removeClass('is-invalid');
    dados.find('.invalid-feedback').text('');
    $("#next").prop("disabled", true);
    $.ajax({
        type: "POST",
        data: "form=auth&acao=forgotPassword&" + dados.serialize(),
        url: "app/Http/Controllers/Auth/ForgotPassword.php",
        async: false
    }).then(sucesso, falha);

    function sucesso(data) {
        var $data = $.parseJSON(data)["data"];
        if ($data.status == true) {
            modalMsgForgotPassword($data.email)

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
        $(".span-loader").html('');//remover o loader

    }

    function falha() {
        console.log("erro");
    }
}