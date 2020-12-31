<?php 

namespace Hcode\PagSeguro;

class Config{
	//ATENÇÃO QUANDO A VARIÁVEL FOR PARA FALSE ESTARÁ FUNCIONANDO EM PRODUÇÃO OU SEJA VAI ESTAR GASTANDO DINHEIRO
	const SANDBOX = true;

	const SANDBOX_EMAIL = "paulooliveirar@hotmail.com";
	const PRODUCTION_EMAIL = "paulooliveirar@hotmail.com";

	//ATENÇÃO TOKEN DO SANDBOX E DA PRODUÇÃO SÃO DIFERENTES
	const SANDBOX_TOKEN = "C68D4D665ED446CEA1C5E4C084C20648";
	const PRODUCTION_TOKEN = "A1E7D6A081F54F71BA6BCA3D6100F477";

	const SANDBOX_URL = "https://ws.sandbox.pagseguro.uol.com.br/v2/sessions";
	const PRODUCTION_URL = "https://ws.pagseguro.uol.com.br/v2/sessions";

	const SANDBOX_URL_JS = "https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js";
	const PRODUCTION_URL_JS = "https://stc.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js";

	const SANDBOX_URL_TRANSACTION = "https://ws.sandbox.pagseguro.uol.com.br/v2/transactions";
	const PRODUCTION_URL_TRANSACTION = "https://ws.pagseguro.uol.com.br/v2/transactions";

	//Número de Parcelas sem Juros
	const MAX_INSTALLMENT_NO_INTEREST = 3;
	
	//Número de Parcelas Aceitas pelo site
	const MAX_INSTALLMENT = 10;

	//Quando Pagamento for Pago ou Não, a URL
	const NOTIFICATION_URL = "http://wwww.orbittech.com.br/payment/notification";

	const SANDBOX_URL_NOTIFICATION = "https://ws.sandbox.pagseguro.uol.com.br/v2/transactions/notifications/";
	const PRODUCTION_URL_NOTIFICATION = "https://ws.pagseguro.uol.com.br/v2/transactions/notifications/";

	public static function getAuthentication(){
		if(Config::SANDBOX === true){
			return [
				"email"=>Config::SANDBOX_EMAIL,
				"token"=>Config::SANDBOX_TOKEN
			];
		}
		else{
			return [
				"email"=>Config::PRODUCTION_EMAIL,
				"token"=>Config::PRODUCTION_TOKEN
			];
		}
	}

	public static function getUrlSessions():string{
		return (Config::SANDBOX === true) ? Config::SANDBOX_URL : Config::PRODUCTION_URL; 
	}

	public static function getUrlJS(){
		return (Config::SANDBOX === true) ? Config::SANDBOX_URL_JS : Config::PRODUCTION_URL_JS; 
	}

	public static function getUrlTransaction(){
		return (Config::SANDBOX === true) ? Config::SANDBOX_URL_TRANSACTION : Config::PRODUCTION_URL_TRANSACTION; 
	}

	public static function getNotificationTransactionURL(){
		return (Config::SANDBOX === true) ? Config::SANDBOX_URL_NOTIFICATION : Config::PRODUCTION_URL_NOTIFICATION; 	
	}

}

?>