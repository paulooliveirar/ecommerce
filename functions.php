<?php

use \Hcode\Model\User;
use \Hcode\Model\Cart;

function format_price($vlprice){

	if(!$vlprice > 0){$vlprice = 0;}

	return number_format($vlprice, 2 ,"," , ".");
		
}

function format_date($date){
	return date("d/m/Y", strtotime($date));
}

function checkLogin($inadmin = true){
	return User::checkLogin($inadmin);
}

function getUserName(){
	$user = User::getFromSession();

	return $user->getdesperson();
}

function getDateRegister(){
	$user = User::getFromSession();

	return $user->getdtregister();

}

function getCartNrQtd(){

	$cart = Cart::getFromSession();

	$totals = $cart->getProductsTotals();

	return $totals['nrqtd'];
}

function getCartVlSubTotal(){

	$cart = Cart::getFromSession();

	$totals = $cart->getProductsTotals();

	return format_price($totals['vlprice']);
}

?>