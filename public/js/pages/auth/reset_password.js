
const params = new URLSearchParams(window.location.search);

$.ajax({
    type: 'GET',
    data: "page=inicial&layouts=topo",
    url: "resources/views/layouts/header.php",
    success: function (result) {
        return $(".main .header").html(result);
    },
});


$.ajax({
    type: 'GET',
    data: "page=inicial&layouts=footer",
    url: "resources/views/layouts/footer.php",
    success: function (result) {
        return $(".main .footer").html(result);
    },
});

$(document).ready(function () {
    modalResetPassword()
})


function modalResetPassword() {
    $(".btn-close").trigger('click'); //fechar o modal
    $.ajax({
        type: 'GET',
        data: "page=auth&containers=resetPassword&" + params,
        url: "resources/views/containers/auth/resetPassword.php",
        success: function (result) {
            return $("main .modal-externo").html(result) + $("#modal_reset_password").modal('show');;
        },
    });
}

ScrollReveal().reveal('.conteudo-1', { delay: 400 });
ScrollReveal().reveal('.conteudo-2', { delay: 400 });


// ScrollReveal().reveal('.conteudo-1', slideUp);