<?php

use \Hcode\Model\User;
use \Hcode\Model\Cart;
use \Hcode\Model\Order;
use \Hcode\Model\Conf;
use \Hcode\Model\Product;
use \Hcode\Model\Category;
use \Hcode\Model\MethodsPayment;

function format_price($vlprice, int $plots = 1){

	if(!$vlprice > 0){$vlprice = 0;}

	$vlprice = $vlprice/$plots;

	return number_format($vlprice, 2 ,"," , ".");
		
}

function format_date($date){
	return date("d/m/Y", strtotime($date));
}

function getLastAccess(){
	date_default_timezone_set('America/Sao_Paulo');
	return date("d/m/Y H:m:s");
}

function checkLogin($inadmin = true){
	return User::checkLogin($inadmin);
}

function getUserName(){
	$user = User::getFromSession();

	return utf8_decode($user->getdesperson());
}

function getPhotoAdmin(){
	return User::checkPhotoAdmin();
}

function getNamePayment($name){
	$name = strtolower($name);
	$name = str_replace(" ", "", $name);

	return $name;
}

function getMethodsPaymentOn(){
	return count(MethodsPayment::listMethodOn());
}

function getTotalOrder(){
	return count(Order::listAll());
}

function getTotalOrderPay(){
	$total = (count(Order::totalPay())/count(Order::listAll())) * 100;
	return number_format($total,2,".",",");
}

function getTotalOrdersPay(){
	return count(Order::totalPay());
}

function getTotalOrdersPendent(){
	return count(Order::totalPendent());
}

function getTotalOrdersPayRecused(){
	return count(Order::totalPayRecused());
}
function getTotalCategories(){
	return count(Category::listAll());
}

function getTotalUsers(){
	return count(User::listAll());
}

function getTotalProducts(){
	return count(Product::listAll());
}


function getNameStore(){
	$conf = Conf::getConf();

	return $conf->getdesname();
}

function getURLStore(){
	$conf = Conf::getConf();


	if( (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443 ){
    	$url = "https://" . $conf->getdesurl();
	}
	else{
		$url = "http://" . $conf->getdesurl();
	}


	return $url;
}

function getUserEmail(){
	$user = User::getFromSession();

	return $user->getdesemail();
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

function getInfoFooter(){
	$conf = Conf::getConf();

	$name = $conf->getdesname();
	$cnpj_cpf = $conf->getdescnpj_cpf();
	$address = $conf->getdesaddress();
	$city = $conf->getdescity();
	$state = $conf->getdesstate();
	$number = $conf->getdesnumber();
	$complement = $conf->getdescomplement();
	$phone = $conf->getdesphone();
	$zipcode = $conf->getdeszipcode();

	$text =  $name . " &copy; " . date('Y') . "<br>CNPJ: " . $cnpj_cpf . "<br>" . $address . ", " . $number . "<br> " . $zipcode . " " .  $city . " - " . $state . " " . $complement . "<br>Telefone: " . $phone;

	return $text;
}


/*********************************************************************
*******				Integrações com o Google				**********
**********************************************************************/

function googleAnalytics(){

  	//$idgoogle = Google::getID();
  	$idgoogle = "";

  	if($idgoogle === ""){
  		return false;
  	}
  	$code = "
  	<!-- Google Analytics -->
  	<script async src='https://www.googletagmanager.com/gtag/js?id=" . $idgoogle . "'></script>
  	<script>
  		window.dataLayer = window.dataLayer || [];
    	function gtag(){dataLayer.push(arguments);}
    	gtag('js', new Date());
    	gtag('config', '" . $idgoogle . "');
  	</script>
  	<!-- End Google Analytics -->";
	return $code;
}

function googleTagManager(){
	$idGTM = "";
	if($idGTM === ""){
  		return false;
  	}
	$code = "
	<!-- Google Tag Manager -->
		<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
		j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
		'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
		})(window,document,'script','dataLayer','GTM-" . $idGTM . "');</script>
	<!-- End Google Tag Manager -->";

	$code .= '
	<!-- Google Tag Manager (noscript) -->
		<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-' . $idGTM . '"
		height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<!-- End Google Tag Manager (noscript) -->';

	return $code;
}


/*********************************************************************
*******				Integração com Tawk.to 					**********
**********************************************************************/

function chatTawkTo(){
	$link = "";
	if($link === ""){
  		return false;
  	}
	$code = "
	<!--Start of Tawk.to Script-->
	<script type='text/javascript'>
		var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
		(function(){
		var s1=document.createElement('script'),s0=document.getElementsByTagName('script')[0];
		s1.async=true;
		s1.src='$link';
		s1.charset='UTF-8';
		s1.setAttribute('crossorigin','*');
		s0.parentNode.insertBefore(s1,s0);
		})();
	</script>
	<!--End of Tawk.to Script-->";
	return $code;
}

/*********************************************************************
*******				Integração com Tawk.to 					**********
**********************************************************************/
?>