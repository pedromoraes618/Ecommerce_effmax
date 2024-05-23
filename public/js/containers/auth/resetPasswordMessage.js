const token = new URLSearchParams(window.location.search).get('code');


function modalLogin() {
    $(".btn-close").trigger('click'); //fechar o modal
    $.ajax({
        type: 'GET',
        data: "page=auth&containers=login&forgot-password=true",
        url: "resources/views/containers/auth/login.php",
        success: function (result) {
            return $("main .modal-externo").html(result) + $("#modal_login").modal('show');;
        },
    });
}


$("#login").click(function () {
    modalLogin()
})

var countdown = 3; // Definir o valor inicial do contador
// Função para atualizar o contador regressivo
function updateCountdown() {
    $('.time').text(countdown + "s"); // Atualizar o texto da span com o contador
    countdown--; // Decrementar o contador

    if (countdown >= 0) {
        setTimeout(updateCountdown, 1000); // Chamada recursiva a cada segundo (1000 milissegundos)
    } else {
        modalLogin()
    }
}

// Iniciar o contador quando o documento estiver pronto
updateCountdown();