$('.btn-register-member').click(function () {
    modalRegister();
});


function modalRegister() {
    $(".btn-close").trigger('click'); //fechar o modal
    $.ajax({
        type: 'GET',
        data: "page=auth&containers=register",
        url: "resources/views/containers/auth/register.php",
        success: function (result) {
            return $("main .modal-externo").html(result) + $("#modal_register").modal('show');;
        },
    });
}