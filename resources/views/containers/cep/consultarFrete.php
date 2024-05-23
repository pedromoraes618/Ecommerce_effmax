<div>
    <form id="consultarFrete">
        <div class="row">
            <div class="col-md  mb-2">
                <label for class="form-label"><i class="bi bi-truck"></i> Meios de Envio</label>

                <div class="input-group mb-2">
                    <input class="form-control" type="text" id="cep" name="cep" placeholder="Informe o seu cep">
                    <button class="btn btn-outline-secondary" type="submit" id="buscarCep">Calcular</button>
                    <div class="feedback-cep"> </div>
                </div>

                <div class="option-frete">
                    <div class="form-text">
                        <a target="_blank" style="text-decoration: none;" href="https://buscacepinter.correios.com.br/app/endereco/index.php">Não sei meu cep</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="public/js/containers/cep/consultarFrete.js"> </script>