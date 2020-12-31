<?php

use \Hcode\Page;
use \Hcode\Model\Order;
use \Hcode\Model\User;
use \GuzzleHttp\Client;
use \Hcode\PagSeguro\Config;
use \Hcode\PagSeguro\Transporter;
use \Hcode\PagSeguro\Document;
use \Hcode\PagSeguro\Phone;
use \Hcode\PagSeguro\Address;
use \Hcode\PagSeguro\Shipping;
use \Hcode\PagSeguro\Sender;
use \Hcode\PagSeguro\CreditCard;
use \Hcode\PagSeguro\Item;
use \Hcode\PagSeguro\Payment;
use \Hcode\PagSeguro\Bank;
use \Hcode\PagSeguro\CreditCard\Installment;
use \Hcode\PagSeguro\CreditCard\Holder;

$app->get('/payment/success/boleto', function(){

	User::verifyLogin(false);

	$order = new Order();

	$order->getFromSession();

	$order->get((int)$order->getidorder());

	$page = new Page();

	$page->setTpl('payment-success-boleto',[
		"order"=>$order->getValues()
	]);
});

$app->get('/payment/success/debit', function(){

	User::verifyLogin(false);

	$order = new Order();

	$order->getFromSession();

	$order->get((int)$order->getidorder());

	$page = new Page();

	$page->setTpl('payment-success-debit',[
		"order"=>$order->getValues()
	]);
});

$app->get('/profile/payment/:idorder', function($idorder) {

	User::verifyLogin(false);

	$payment = Payment::getPaymentProfile($idorder);

	if(strlen($payment) > 0){
		header('Location: ' . $payment);
	}

	else{
		header('Location: okskoakos');
	}

	exit;
});

$app->get('/payment/success', function(){

	User::verifyLogin(false);

	$order = new Order();

	$order->getFromSession();

	$page = new Page();

	$page->setTpl('payment-success',[
		"order"=>$order->getValues()
	]);
});

$app->post('/payment/notification', function(){
	Transporter::getNotification($_POST['notificationCode'], $_POST['notificationType']);
});

$app->post('/payment/debit', function(){	

	User::verifyLogin(false);

	$order = new Order();

	$order->getFromSession();

	$order->get((int)$order->getidorder());

	$address = $order->getAddress();

	$cart = $order->getCart();

	$cpf = new Document(Document::CPF, $_POST['cpf']);

	$phone = new Phone($_POST['ddd'], $_POST['phone']);

	$birthDate = new Datetime($_POST['birth']);

	$sender = new Sender(
		$order->getdesperson(), 
		$cpf, 
		$birthDate, 
		$phone, 
		$order->getdesemail(), 
		$_POST['hash']
	);

	$shippingAddress = new Address(
		$address->getdesaddress(), 
		$address->getdesnumber(),
		$address->getdescomplement(),
		$address->getdesdistrict(), 
		$address->getdeszipcode(), 
		$address->getdescity(), 
		$address->getdesstate(), 
		$address->getdescountry()
	);

	$shipping = new Shipping($shippingAddress, Shipping::SEDEX, (float)$cart->getvlfreight());


	$payment = new Payment(
		$order->getidorder(),
		$sender,
		$shipping
	);


	foreach ($cart->getProducts() as $product) {
		$item = new Item(
			(int)$product['idproduct'], 
			$product['desproduct'], 
			(float)$product['vlprice'], 
			(int)$product['nrqtd']
		);

		$payment->addItem($item);
	}

	$bank = new Bank($_POST['bank']);

	$payment->setBank($bank);

	Transporter::sendTransaction($payment);

	echo json_encode([
		'success'=>true
	]);

});

$app->post('/payment/boleto', function(){	

	User::verifyLogin(false);

	$order = new Order();

	$order->getFromSession();

	$order->get((int)$order->getidorder());

	$address = $order->getAddress();

	$cart = $order->getCart();

	$cpf = new Document(Document::CPF, $_POST['cpf']);

	$phone = new Phone($_POST['ddd'], $_POST['phone']);

	$birthDate = new Datetime($_POST['birth']);

	$sender = new Sender(
		$order->getdesperson(), 
		$cpf, 
		$birthDate, 
		$phone, 
		$order->getdesemail(), 
		$_POST['hash']
	);

	$shippingAddress = new Address(
		$address->getdesaddress(), 
		$address->getdesnumber(),
		$address->getdescomplement(),
		$address->getdesdistrict(), 
		$address->getdeszipcode(), 
		$address->getdescity(), 
		$address->getdesstate(), 
		$address->getdescountry()
	);

	$shipping = new Shipping($shippingAddress, Shipping::SEDEX, (float)$cart->getvlfreight());


	$payment = new Payment(
		$order->getidorder(),
		$sender,
		$shipping
	);


	foreach ($cart->getProducts() as $product) {
		$item = new Item(
			(int)$product['idproduct'], 
			$product['desproduct'], 
			(float)$product['vlprice'], 
			(int)$product['nrqtd']
		);

		$payment->addItem($item);
	}

	$payment->setBoleto();

	Transporter::sendTransaction($payment);

	echo json_encode([
		'success'=>true
	]);

});


$app->post('/payment/credit', function() {

	User::verifyLogin(false);

	$order = new Order();

	$order->getFromSession();

	$order->get((int)$order->getidorder());

	$address = $order->getAddress();

	$cart = $order->getCart();

	$cpf = new Document(Document::CPF, $_POST['cpf']);

	$phone = new Phone($_POST['ddd'], $_POST['phone']);

	$birthDate = new Datetime($_POST['birth']);

	$sender = new Sender(
		$order->getdesperson(), 
		$cpf, 
		$birthDate, 
		$phone, 
		$order->getdesemail(), 
		$_POST['hash']
	);

	$shippingAddress = new Address(
		$address->getdesaddress(), 
		$address->getdesnumber(),
		$address->getdescomplement(),
		$address->getdesdistrict(), 
		$address->getdeszipcode(), 
		$address->getdescity(), 
		$address->getdesstate(), 
		$address->getdescountry()
	);

	$holder = new Holder($order->getdesperson(), $cpf, $birthDate, $phone);

	$shipping = new Shipping($shippingAddress, Shipping::SEDEX, (float)$cart->getvlfreight());

	$installment = new Installment((int)$_POST['installments_qtd'], (float)$_POST['installments_value']);

	$billingAddress = new Address(
		$address->getdesaddress(), 
		$address->getdesnumber(),
		$address->getdescomplement(),
		$address->getdesdistrict(), 
		$address->getdeszipcode(), 
		$address->getdescity(), 
		$address->getdesstate(), 
		$address->getdescountry()
	);

	$payment = new Payment(
		$order->getidorder(),
		$sender,
		$shipping
	);

	$creditCard = new creditCard($_POST['token'], $installment, $holder, $billingAddress);

	foreach ($cart->getProducts() as $product) {
		$item = new Item(
			(int)$product['idproduct'], 
			$product['desproduct'], 
			(float)$product['vlprice'], 
			(int)$product['nrqtd']
		);

		$payment->addItem($item);
	}

	$payment->setCreditCard($creditCard);

	Transporter::sendTransaction($payment);

	echo json_encode([
		'success'=>true
	]);


});


$app->get('/payment/pagseguro', function() {

	$client = new Client();
	$res = $client->request('POST', Config::getUrlSessions() . "?" . http_build_query(Config::getAuthentication()));

	echo $res->getBody()->getContents(); # '{"id": 1420053, "name": "guzzle", ...}'
});


$app->get('/payment', function() {

	User::verifyLogin(false);

	$order = new Order();

	$order->getFromSession();

	$years = [];

	for($y = date('Y'); $y < date('Y')+14; $y++){
		array_push($years, $y);
	};

	$page = new Page();

	$page->setTpl("payment", [
		"order"=>$order->getValues(),
		//"order"=>2000,
		"msgError"=>Order::getError(),
		"years"=>$years,
		"pagseguro"=>[
			"urlJS"=>Config::getUrlJS(),
			"id"=>Transporter::createSession(),
			"maxInstallmentNoInterest"=>Config::MAX_INSTALLMENT_NO_INTEREST,
			"maxInstallment"=>Config::MAX_INSTALLMENT
		]
	]);

});


?>