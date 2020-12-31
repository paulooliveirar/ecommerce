<?php
use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\MethodsPayment;

$app->get('/admin/methods-payment', function() {
	
	User::verifyLogin();

	$page = new PageAdmin();

	$payment = MethodsPayment::listAll();

	$page->setTpl("methods-payment", array(
		"payment"=>$payment
	));
});


$app->get('/admin/methods-payment/:idmethod-:inactive', function($idmethod, $inactive) {
	
	User::verifyLogin();

	$payment = new MethodsPayment();

	$payment->updateStatus($idmethod, $inactive);

	header("Location: /admin/methods-payment");
	exit;
});

$app->get('/admin/methods-payment/:idmethod/:sandbox', function($idmethod, $sandbox) {
	
	User::verifyLogin();

	$payment = new MethodsPayment();

	$payment->updateStatusSandbox($idmethod, $sandbox);

	header("Location: /admin/methods-payment/$idmethod");
	exit;
});

$app->get('/admin/methods-payment/:idmethod', function($idmethod) {
	
	User::verifyLogin();

	$payment = new MethodsPayment();

	$payment->get((int)$idmethod);

	$page = new PageAdmin();

	$page->setTpl("methods-payment-update", array(
		"payment"=>$payment->getValues(),
		"error"=>"",
		"success"=>""			
	));	
	exit;
});
?>