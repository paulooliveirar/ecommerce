<?php 

namespace Hcode\Model;

use \Hcode\Model;
use \Hcode\DB\Sql;

class Conf extends Model {

	const SESSION = "Conf";
	const SESSION_ERROR = "ConfError";	
	const SUCCESS = "ConfSuccess";
	
	public function update(){

		$sql = new Sql();

		$results = $sql->select("CALL sp_conf_update(:desname, :desurl, :descnpj_cpf, :desphone, :desemail, :deszipcode, :descountry, :desstate, :descity, :desaddress, :desnumber, :descomplement)", [
			":desname"=>$this->getdesname(), 
			":desurl"=>$this->getdesurl(),
			":descnpj_cpf"=>$this->getdescnpj_cpf(),
			":desphone"=>$this->getdesphone(),
			":desemail"=>$this->getdesemail(),
			":deszipcode"=>$this->getdeszipcode(),
			":descountry"=>$this->getdescountry(),
			":desstate"=>$this->getdesstate(),
			":descity"=>$this->getdescity(),
			":desaddress"=>$this->getdesaddress(),
			":desnumber"=>$this->getdesnumber(),
			":descomplement"=>$this->getdescomplement()
		]);
	}

	public static function setMsgError($msg){
		$_SESSION[Conf::SESSION_ERROR] = $msg;
	}

	public static function getMsgError(){
		$msg = (isset($_SESSION[Conf::SESSION_ERROR])) ? $_SESSION[Conf::SESSION_ERROR] : "";
		Conf::cleanMsgError();
		return $msg;
	}

	public static function cleanMsgError(){
		$_SESSION[Conf::SESSION_ERROR] = NULL;
	}	

	public static function setMsgSuccess($msg){
		$_SESSION[Conf::SUCCESS] = $msg;
	}

	public static function getMsgSuccess(){
		$msg = (isset($_SESSION[Conf::SUCCESS])) ? $_SESSION[Conf::SUCCESS] : "";
		Conf::cleanMsgSuccess();
		return $msg;
	}

	public static function cleanMsgSuccess(){
		$_SESSION[Conf::SUCCESS] = NULL;
	}	

	public static function getConf(){

		$sql = new Sql();

		$results =  $sql->select("SELECT * FROM tb_conf");

		if(count($results) === 0)
		{
			throw new \Exception("Informações não cadastradas.");
		}

		$conf = new Conf();

		$conf->setData($results[0]);

		return $conf;
	}
}

?>