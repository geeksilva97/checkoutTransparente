<?php 
	include_once 'config/PagSeguroConfig.php';	

	// Iniciando sessão
	$arrayData = [
		'email' => EMAIL,
		'token' => TOKEN
	];


	$data = http_build_query($arrayData);

	$curl=curl_init( $urls['session'] );
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	$xml= curl_exec($curl);

	$response = simplexml_load_string($xml);

 ?>
 <!DOCTYPE html>
<html>
<head>
	<title>Teste Final - Checkout Transparente</title>
	<meta charset="utf-8">
	
</head>
<body>

	<span class="pagseguro-sessionid"><?=$response->id?></span>

	<h1>Finalizar Compra</h1>
	<hr>

	<h2>Notebook Asus Prata</h2>
	<p><strong>Valor </strong>: R$ 1300,00</p>

	<hr>
	<h2>Dados do Comprador</h2>
	<form>
		
		<p>
			<label>Nome : </label><input type="text" name="nome">
		
			<label>E-mail : </label><input type="text" name="email" value="c30015648174349793083@sandbox.pagseguro.com.br">

			<label>Telefone : </label><input type="tel" name="telefone" value="85989769682">

			<label>CPF/CNPJ : </label><input type="text" name="cpf_cnpj" value="89347782610">
		</p>

		<p>
			
			<label>Endereço : </label><input type="text" name="endereco"> <label>N°</label> <input type="number" name="numero">

			<label>Complemento : </label><input type="text" name="complemento">

			<label>Bairro : </label><input type="text" name="bairro">

			<label>CEP : </label><input type="text" name="cep">

			<label>Cidade : </label> <input type="text" name="cidade">

			<label>Estado : </label>
			<select name="estado">
				<option value="">Selecione...</option>
				<option value="AM">Amazonas</option>
				<option value="CE">Ceará</option>
				<option value="MG">Minas Gerais</option>
				<option value="SP">São Paulo</option>
			</select>

		</p>

	</form>


	<div class="info-cartao">
		<hr>
		<h2>Dados do cartão</h2>

		<form>
			<p><strong>Usar dados do formulário anterior</strong> <input type="checkbox" name="dados_anteriores"></p>
			<p>
				<label>Nome : </label> <input type="text" name="cartao_nome">

				<label>CPF : </label> <input type="text" name="cartao_cpf_cnpj">

				<label>Telefone : </label> <input type="text" name="cartao_telefone">

				<label>Endereço : </label> <input type="text" name="cartao_endereco">
				<label>N° : </label> <input type="text" name="cartao_numero">

				<label>Complemento : </label> <input type="text" name="cartao_complemento">

				<label>Bairro : </label><input type="text" name="cartao_bairro">

				<label>CEP : </label><input type="text" name="cartao_cep">

				<label>Cidade : </label><input type="text" name="cartao_cidade">

				<label>Estado : </label>
				<select name="cartao_estado">
				<option value="">Selecione...</option>
				<option value="AM">Amazonas</option>
				<option value="CE">Ceará</option>
				<option value="MG">Minas Gerais</option>
				<option value="SP">São Paulo</option>
			</select>

			</p>

		</form>

	</div>


	<div class="cartao">
		<hr>
		<h2>Dados do Cartão</h2>

		<form>
			
			<p>
				<label>Número Cartao : </label><input type="text" name="numero_cartao" value="38304782155453">

				<label>CVV : </label><input type="text" name="cvv" value="742">

				<label>Expiração : </label> <input type="text" name="expiracao" value="11/2017">
			</p>

			<p class="informacoes-bandeira">
				<label>Bandeira : </label> <span class="bandeira">diners</span>
			</p>

		</form>
	</div>

	<br>
	<br>
	<button name="finalizar-compra">Finalizar Compra</button>
	<strong class="carregando" style="display: none;">Carregando....</strong>


<!-- PagSeguroDirectPayment -->
<script type="text/javascript" src="https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js">
</script>

<!-- JQuery -->
<script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>

<script type="text/javascript">
	
	$(function(){
		

		var sessionId = $('.pagseguro-sessionid').text();
		PagSeguroDirectPayment.setSessionId(sessionId);

		var $valorCompra = 1300;
		var $brand = {name:'diners'};
		var brandLoaded = false;


		var carregarBandeira = function( cardBin ){
			PagSeguroDirectPayment.getBrand({
			    cardBin: cardBin,

			    success: function( response ){

			    	$brand = response.brand;
			    	brandLoaded=true;
			    	$('.bandeira').html( $brand.name );
			    	console.log(response);
			    },

			    error: function( response ){
			    	alert("Houve um erro na identificação do seu cartao.\nVerifique se o número passado está correto");
			    	console.log(response);
			    }
			});
		};

		$('body').on('click', 'button[name=finalizar-compra]', function(){

			var expiracao = $('input[name=expiracao]').val();
			expiracao = expiracao.toString()


			var param = {
				cardNumber: $('input[name=numero_cartao]').val(),
				cvv: $('input[name=cvv]').val(),
				expirationMonth: expiracao.substring(0,2),
				expirationYear: expiracao.substring(3),
				success: function(response) {

		
			    	tokenCartao = response.card.token;
			    	var hashCode = PagSeguroDirectPayment.getSenderHash();


			    	$.post('http/request.php', {
			    		action : 'finalizar-compra',
			    		data : {
			    			creditCardToken : tokenCartao,
			    			forma_pagamento : 'creditCard',
			    			hash : hashCode,
			    			cliente : {
			    				nome : $('input[name=nome]').val(),
			    				email : $('input[name=email]').val(),
			    				telefone : $('input[name=telefone]').val(),
			    				cpf : $('input[name=cpf_cnpj]').val(),
			    				endereco : $('input[name=endereco]').val(),
			    				numero : $('input[name=cartao_numero]').val(),
			    				complemento : $('input[name=complemento]').val(),
			    				bairro : $('input[name=bairro]').val(),
			    				cep : $('input[name=cep]').val(),
			    				cidade : $('input[name=cidade]').val(),
			    				estado : $('select[name=estado]').val()
			    			},

			    			donoCartao : {
			    				nome : $('input[name=cartao_nome]').val(),
			    				cpf : $('input[name=cartao_cpf_cnpj]').val(),
			    				telefone : $('input[name=cartao_telefone]').val(),
			    				endereco : $('input[name=cartao_endereco]').val(),
			    				numero : $('input[name=cartao_numero]').val(),
			    				complemento : $('input[name=cartao_complemento]').val(),

			    				bairro : $('input[name=cartao_bairro]').val(),
			    				cep : $('input[name=cartao_cep]').val(),
			    				cidade : $('input[name=cartao_cidade]').val(),
			    				estado : $('select[name=cartao_estado]').val()
			    			}
			    		}
			    	}, function(data){
			    		$('.carregando').css('display', 'none');
			    		$('button[name=finalizar-compra]').removeAttr('disabled');
			    		alert(data);
			    	});
			    	
			    },
			    error: function(response) {
			        alert("Houve um erro");
			        console.log(response);
			    },
			    complete: function(response) {
			        //tratamento comum para todas chamadas
			    }
		}

		//parâmetro opcional para qualquer chamada
		 param.brand = $brand.name;





		 console.log(param);

			PagSeguroDirectPayment.createCardToken(param);

		});

		$('body').on('blur', 'input[name=numero_cartao]', function(){

			var cardBin = $(this).val().toString();
			cardBin = cardBin.toString().substring(0,6);
			
			carregarBandeira( cardBin );
			

		});

		$('body').on('click', 'input[name=dados_anteriores]', function(){
			if( $(this).prop('checked') ){

				$('input[name=cartao_nome]').val( $('input[name=nome]').val() );
				$('input[name=cartao_cpf_cnpj]').val( $('input[name=cpf_cnpj]').val() );
				$('input[name=cartao_telefone]').val( $('input[name=telefone]').val() );
				$('input[name=cartao_endereco]').val( $('input[name=endereco]').val() );
				$('input[name=cartao_numero]').val( $('input[name=numero]').val() );
				$('input[name=cartao_complemento]').val( $('input[name=complemento]').val() );

				$('input[name=cartao_bairro]').val( $('input[name=bairro]').val() );

				$('input[name=cartao_cep]').val( $('input[name=cep]').val() );

				$('input[name=cartao_cidade]').val( $('input[name=cidade]').val() );

				$('select[name=cartao_estado]').val( $('select[name=estado]').val() );

			}
		});

	});

</script>

</body>
</html>