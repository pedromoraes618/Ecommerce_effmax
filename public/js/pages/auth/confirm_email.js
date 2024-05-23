const token = new URLSearchParams(window.location.search).get('code');

confirmEmail()
function confirmEmail() {
    $.ajax({
        type: "POST",
        data: "form=auth&acao=confirmEmail&token=" + token,
        url: "app/Http/Controllers/Auth/ConfirmEmail.php",
        async: false
    }).then(sucesso, falha);

    function sucesso(data) {
        var $data = $.parseJSON(data)["data"];
        window.location.href = "./"
    }

    function falha() {
        console.log("erro");
    }
}