$(".btn-det").click(function () {
    var orderID = $(this).attr("id")
    order(orderID)
    $("html, body").scrollTop(0);

})