<?php 
	include_once '../config/PagSeguroConfig.php';
	$action = (isset($_POST['action'])) ? $_POST['action'] : '';

	switch ($action) {
		case 'finalizar-compra':
			$data = $_POST['data'];

			$cliente = $data['cliente'];
			$donoCartao = $data['donoCartao'];
			$tokenCartao = $data['creditCardToken'];
			$senderHash = $data['hash'];

			$forma_pagamento = $data['forma_pagamento'];

			$info = [];

			$info['email']= EMAIL;
			$info['token']=TOKEN;
			$info['paymentMode'] = 'default';
			$info['paymentMethod'] = $forma_pagamento;
			$info['receiverEmail'] = 'edigleyssonsilva@gmail.com';
			$info['currency'] = 'BRL';
			$info['extraAmount'] = '0.00';
			$info['itemId1'] = '0001';
			$info['itemDescription1'] = 'Notebook Asus Prata';
			$info['itemAmount1'] = '1300.00';
			$info['itemQuantity1'] = 1;
			$info['notificationURL'] = 'http://codesilva.esy.es';
			$info['reference'] = 'REF12233';
			$info['senderName'] = $cliente['nome'];
			$info['senderCPF'] = $cliente['cpf'];
			$info['senderAreaCode'] = substr($cliente['telefone'], 0, 2);
			$info['senderPhone'] = substr($cliente['telefone'], 2);
			$info['senderEmail'] = $cliente['email'];

			$info['senderHash'] = $senderHash;
			$info['shippingAddressStreet'] = $cliente['endereco'];
			$info['shippingAddressNumber'] = $cliente['numero'];
			$info['shippingAddressComplement'] = $cliente['complemento'];
			$info['shippingAddressDistrict'] = $cliente['bairro'];
			$info['shippingAddressPostalCode'] = $cliente['cep'];
			$info['shippingAddressCity'] = $cliente['cidade'];
			$info['shippingAddressState'] = $cliente['estado'];
			$info['shippingAddressCountry'] = 'BRA';
			$info['shippingType'] = 1;
			$info['shippingCost'] = '0.00';

			if( $forma_pagamento == 'creditCard' ){

				$info['creditCardToken'] = $tokenCartao;
				$info['installmentQuantity'] = 1;
				$info['installmentValue'] = '1300.00';
				// $info['noInterestInstallmentQuantity'] = 2;
				$info['creditCardHolderName'] = $donoCartao['nome'];
				$info['creditCardHolderCPF'] = $donoCartao['cpf'];
				// $info['creditCardHolderBirthDate'] = '08/03/1997';
				$info['creditCardHolderAreaCode'] = substr($donoCartao['telefone'], 0,2);
				$info['creditCardHolderPhone'] = substr($donoCartao['telefone'], 2);
				$info['billingAddressStreet'] = $donoCartao['endereco'];
				$info['billingAddressNumber'] = $donoCartao['numero'];
				$info['billingAddressComplement'] =$donoCartao['complemento'];
				$info['billingAddressDistrict'] =$donoCartao['bairro'];
				$info['billingAddressPostalCode'] = $donoCartao['cep'];
				$info['billingAddressCity'] = $donoCartao['cidade'];
				$info['billingAddressState'] =$donoCartao['estado'];
				$info['billingAddressCountry'] ='BRA';


			}

			$info = http_build_query($info);

			$curl=curl_init( $urls['transaction'] );
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			// curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
			curl_setopt($curl, CURLOPT_POSTFIELDS, $info);
			$responseXML = curl_exec($curl);
			$response = simplexml_load_string($responseXML);

			echo json_encode($response);


		break;
		
		default:
			# code...
			break;
	}

 ?>