<?php

namespace Hcode\Pagseguro;

use Exception;
use DOMDocument;
Use DOMElement;
use Hcode\Model;
use Hcode\DB\Sql;
use Hcode\Model\User;
Use Hcode\Pagseguro\Payment\Method;

class Payment{

	private $mode = "default";
	private $currency = "BRL";
	private $extraAmount =0;
	private $reference = "";
	private $items = [];
	private $sender;
	private $shipping;
	private $method;
	private $creditCard;
	private $bank;

	public function __construct(string $reference, Sender $sender, Shipping $shipping, float $extraAmount = 0){

		$this->reference = $reference;
		$this->sender = $sender;
		$this->shipping = $shipping;
		$this->extraAmount = number_format($extraAmount, 2, ".", "");

	}

	public function addItem(Item $item){
		array_push($this->items, $item);
	}

	public function setCreditCard(creditCard $creditCard){
		$this->creditCard = $creditCard;
		$this->method = Method::CREDIT_CARD;
	}

	public function setBank(Bank $bank){
		$this->bank = $bank;
		$this->method = Method::DEBIT;
	}

	public function setBoleto(){
		$this->method = Method::BOLETO;
	}

	public function getDOMDocument():DOMDocument{

		$dom = new DOMDocument("1.0", "ISO-8859-1");

		$payment = $dom->createElement("payment");
		$payment = $dom->appendChild($payment);

		$mode = $dom->createElement("mode", $this->mode);
		$mode = $payment->appendChild($mode);

		$method = $dom->createElement("method", $this->method);
		$method = $payment->appendChild($method);

		$sender = $this->sender->getDOMElement();
		$sender = $dom->importNode($sender, true);
		$sender = $payment->appendChild($sender);

		$currency = $dom->createElement("currency", $this->currency);
		$currency = $payment->appendChild($currency);

		$notificationURL = $dom->createElement("notificationURL", Config::NOTIFICATION_URL);
		$notificationURL = $payment->appendChild($notificationURL);

		$receiverEmail = $dom->createElement("receiverEmail", Config::PRODUCTION_EMAIL);
		$receiverEmail = $payment->appendChild($receiverEmail);

		$items = $dom->createElement("items");
		$items = $payment->appendChild($items);		

		foreach ($this->items as $_item) {
			$item = $_item->getDOMElement();
			$item = $dom->importNode($item, true);
			$item = $items->appendChild($item);
		}
		$extraAmount = $dom->createElement("extraAmount", $this->extraAmount);
		$extraAmount = $payment->appendChild($extraAmount);

		$reference = $dom->createElement("reference", $this->reference);
		$reference = $payment->appendChild($reference);

		$shipping = $this->shipping->getDOMElement();
		$shipping = $dom->importNode($shipping, true);
		$shipping = $payment->appendChild($shipping);
		

		switch ($this->method) {
			case Method::CREDIT_CARD:
				$creditCard = $this->creditCard->getDOMElement();
				$creditCard = $dom->importNode($creditCard, true);
				$creditCard = $payment->appendChild($creditCard);
			break;
			
			case Method::DEBIT:
				$bank = $this->bank->getDOMElement();
				$bank = $dom->importNode($bank, true);
				$bank = $payment->appendChild($bank);
			break;

			case Method::BOLETO:

			break;
		}		

		return $dom;
	}

	public static function getPaymentProfile($idorder){

		$sql = new Sql();

		$results = $sql->select("SELECT despaymentlink
			FROM tb_orderspagseguro 
			WHERE idorder = :idorder", [
				":idorder"=>$idorder
			]);

		if(count($results) > 0){
			return implode(":",$results[0]);
		}

	}
}

?>