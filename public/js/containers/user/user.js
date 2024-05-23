/*abrir o modal para se registrar */
$(".btn-personal-information").click(function () {
    var component = 'profile'
    modalProfile(component)
})
$(".btn-address").click(function () {
    var component = 'address'
    modalProfile(component)
})
$(".btn-alter-password").click(function () {
    var component = 'password'
    modalProfile(component)
})
function modalProfile(component) {
    $(".btn-close").trigger('click'); //fechar o modal
    $.ajax({
        type: 'GET',
        data: "page=userh&containers=perfil&component=" + component,
        url: "resources/views/containers/user/perfil.php",
        success: function (result) {
            return $("main .modal-externo").html(result) + $("#modal_perfil").modal('show');
        },
    });
}

$(document).ready(function () {
    order('')
    historyOrder("")
})


function order(pedido_id) {

    $.ajax({
        type: 'GET',
        data: "page=userh&containers=order&orderID=" + pedido_id,
        url: "resources/views/containers/user/order.php",
        success: function (result) {
            return $("main #order-perfil").html(result)
        },
    });

}

$("#data").on("change", function () {
    var date = $(this).val()
    historyOrder(date)
});

function historyOrder(date) {
    $.ajax({
        type: 'GET',
        data: "page=userh&containers=order&data=" + date,
        url: "resources/views/containers/user/history_order.php",
        success: function (result) {
            return $("main .table_history_table").html(result)
        },
    });
}
