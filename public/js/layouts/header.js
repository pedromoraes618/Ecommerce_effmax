const products_filter = new URLSearchParams(window.location.search).get('products-filter'); // pesquisa pela descrição
$("#products-filter").val(products_filter)

$(document).on('click', function(event) {
    // Verifica se o clique não ocorreu dentro da div .filter-search
    if (!$(event.target).closest('.filter-search').length) {
        $('.filter-search').html(''); // Limpa o conteúdo de .filter-search
    }
});

$("#search").submit(function (e) {
    var q = $("#products-filter").val()
    if (q == "") {
        e.preventDefault()
    }
})

$("#products-filter").on("input", function () {
    var q = $(this).val(); // Captura o valor digitado no input

    if (q.trim() !== '') { // Verifica se o valor não está vazio
        $.ajax({
            type: 'GET',
            data: "page=header&containers=filterSearch&q=" + q,
            url: "resources/views/containers/header/filterSearch.php",
            success: function (result) {
                $("main .header .filter-search").html(result);
            },
        });
    } else {
        $('.filter-search').html(''); // Limpa o conteúdo de .filter-search
    }
});

/*abrir o modal para se realizar o login */
$("#login").click(function (e) {
    e.preventDefault()
    $(".btn-close").trigger('click'); //fechar o modal

    $.ajax({
        type: 'GET',
        data: "page=auth&containers=login",
        url: "resources/views/containers/auth/login.php",
        success: function (result) {
            return $("main .modal-externo").html(result) + $("#modal_login").modal('show');;
        },
    });
})


/*abrir o modal para se registrar */
$("#register").click(function (e) {
    e.preventDefault()
    $(".btn-close").trigger('click'); //fechar o modal

    $.ajax({
        type: 'GET',
        data: "page=auth&containers=register",
        url: "resources/views/containers/auth/register.php",
        success: function (result) {
            return $("main .modal-externo").html(result) + $("#modal_register").modal('show');;
        },
    });
})


$(".open-cart").click(function () {
    offcanvasCart()
})

$(".open-favorite").click(function () {
    offcanvasFavorite()
})
/*função para realizar o logout */
$('#logout').click(function (e) {
    e.preventDefault();
    logout();
});

function logout() {
    $.ajax({
        type: "POST",
        data: "form=auth&acao=logout",
        url: "app/Http/Controllers/Auth/Login.php",
        async: false
    }).then(sucesso, falha);

    function sucesso(data) {
        var $data = $.parseJSON(data)["data"];
        if ($data.status == true) {
            location.reload();
        } else { //erro de aplicação
            Swal.fire({
                icon: 'error',
                title: 'Verifique!',
                text: $data.message,
                timer: 7500,

            })
        }
    }

    function falha() {
        console.log("erro");
    }
}