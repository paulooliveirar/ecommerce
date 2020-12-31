<?php
use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\MethodsShipping;

$app->get('/admin/methods-shipping', function() {
	
	User::verifyLogin();

	$page = new PageAdmin();

	$shipping = MethodsShipping::listAll();

	$page->setTpl("methods-shipping", array(
		"shipping"=>$shipping
	));
});


$app->get('/admin/methods-shipping/:idshipping-:inactive', function($idshipping, $inactive) {
	
	User::verifyLogin();

	$shipping = new MethodsShipping();

	$shipping->updateStatus($idshipping, $inactive);

	header("Location: /admin/methods-shipping");
	exit;
});

$app->get('/admin/methods-shipping/:idshipping/:sandbox', function($idshipping, $sandbox) {
	
	User::verifyLogin();

	$shipping = new MethodsShipping();

	$shipping->updateStatusSandbox($idshipping, $sandbox);

	header("Location: /admin/methods-shipping/$idshipping");
	exit;
});

$app->get('/admin/methods-shipping/:idshipping', function($idshipping) {
	
	User::verifyLogin();

	$shipping = new MethodsShipping();

	$shipping->get((int)$idshipping);

	$page = new PageAdmin();

	$page->setTpl("methods-shipping-update", array(
		"shipping"=>$shipping->getValues(),
		"error"=>"",
		"success"=>""			
	));	
	exit;
});
?>