const product_details = new URLSearchParams(window.location.search).get('product-details'); // pesquisa pelo produto especifico



/*funcionalidade para visualizar a senha */
$('#visualizarSenha').on('click', function () {
  var senhaInput = $('#senha');
  var tipo = senhaInput.attr('type');
  if (tipo === 'password') {
    senhaInput.attr('type', 'text');
  } else {
    senhaInput.attr('type', 'password');
  }
});

$('#visualizarSenhaConfirmar').on('click', function () {
  var confirmarSenhaInput = $('#confirmar_senha');
  var tipo = confirmarSenhaInput.attr('type');
  if (tipo === 'password') {
    confirmarSenhaInput.attr('type', 'text');
  } else {
    confirmarSenhaInput.attr('type', 'password');
  }
});


function updateFavorite(element, id) {//adicionar no favorito
  $(".span-loader").html('<div class="loader"></div>');

  $.ajax({
    type: "POST",
    data: "form=Favorite&acao=updateFavorite&productID=" + id,
    url: "app/Http/Controllers/Favorite.php",
    async: false
  }).then(sucesso, falha);

  function sucesso(data) {
    var $data = $.parseJSON(data)["data"];
    if ($data.status == true) {
      offcanvasFavorite()//abrir o offcanvas do carrinho
      $('.qtd-fav').html($data.qtd_fav);


      if ($(element).hasClass('fav-true')) {
        $(element).removeClass('fav-true'); // Remove a classe da div pai se já estiver presente
      } else {
        $(element).addClass('fav-true'); // Adiciona a classe à div que foi clicada
      }


    } else {
      Swal.fire({
        icon: 'error',
        title: 'Verifique!',
        text: $data.message,
        timer: 7500,

      })
    }
    $(".span-loader").html('');

  }

  function falha() {
    console.error();
  }
}


function updateCart(element, id, qtd, operacao) {//adicionar no favorito
  $(".span-loader").html('<div class="loader"></div>');

  $.ajax({
    type: "POST",
    data: "form=Favorite&acao=updateCart&productID=" + id + "&qtd=" + qtd + "&operacao=" + operacao,
    url: "app/Http/Controllers/Cart.php",
    async: false
  }).then(sucesso, falha);

  function sucesso(data) {

    var $data = $.parseJSON(data)["data"];
    if ($data.status == true) {
      offcanvasCart()//abrir o offcanvas do carrinho
      $('.qtd-cart').html($data.qtd_cart);

      // if ($('.span-cart-' + id).html() == "Adicionar") {
      //   $('.span-cart-' + id).html('Remover') // Remove a classe da div pai se já estiver presente
      // } else {
      //   $('.span-cart-' + id).html('Adicionar') // Remove a classe da div pai se já estiver presente
      // }

    } else {
      Swal.fire({
        icon: 'error',
        title: 'Verifique!',
        text: $data.message,
        timer: 7500,
      })
    }

    $(".span-loader").html('');

  }

  function falha() {
    console.error();
  }
}
function qtdCart(element, id, qtd) {//adicionar no favorito
  $(".span-loader").html('<div class="loader"></div>');

  $.ajax({
    type: "POST",
    data: "form=Favorite&acao=qtdCart&productID=" + id + "&qtd=" + qtd,
    url: "app/Http/Controllers/Cart.php",
    async: false
  }).then(sucesso, falha);

  function sucesso(data) {

    var $data = $.parseJSON(data)["data"];
    if ($data.status == true) {
      offcanvasCart()//abrir o offcanvas do carrinho
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Verifique!',
        text: $data.message,
        timer: 7500,
      })
    }

    $(".span-loader").html('');

  }

  function falha() {
    console.error();
  }
}


function offcanvasCart() {//canvas do carrinho
  $(".btn-close").trigger('click'); //fechar o modal
  $.ajax({
    type: 'GET',
    data: { page: 'headers', containers: 'offcanvasCart' },
    url: 'resources/views/containers/header/offcanvasCart.php',
    success: function (result) {
      // Define o conteúdo do offcanvas com o resultado da requisição AJAX
      $("main .offcanvas-open").html(result);

      // Abre o offcanvas depois que o conteúdo foi carregado
      var offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasCart'));
      offcanvas.show();
    },
  });
}


function offcanvasFavorite() {//canvas do favorito
  $(".btn-close").trigger('click'); //fechar o modal
  $.ajax({
    type: 'GET',
    data: { page: 'headers', containers: 'offcanvasFavorite' },
    url: 'resources/views/containers/header/offcanvasFavorite.php',
    success: function (result) {
      // Define o conteúdo do offcanvas com o resultado da requisição AJAX
      $("main .offcanvas-open").html(result);

      // Abre o offcanvas depois que o conteúdo foi carregado
      var offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasFavorite'));
      offcanvas.show();
    },
  });
}


function sessaoCep() {//canvas do favorito
  $.ajax({
    type: 'GET',
    data: "page=products_details&layouts=consultarFrete",
    url: "resources/views/containers/cep/consultarFrete.php",
    success: function (result) {
      return $(".main .section-cep").html(result);
    },
  });
}