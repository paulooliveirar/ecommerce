<?php 

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;

class MethodsPayment extends Model {

	public static function listAll(){

		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_methodspayment ORDER BY idmethod");
	}

	public static function listMethodOn(){

		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_methodspayment WHERE inactive = 1");
	}

	public function updateStatus($idmethod, $inactive){
		$sql = new Sql();

		switch ($inactive) {
			case '0':
				$inactive = 1;		
				break;
			
			case '1':
				$inactive = 0;
				break;
		}
		
		$sql->query("UPDATE tb_methodspayment SET inactive = :inactive WHERE idmethod = :idmethod", array(
			":inactive"=>$inactive,
			":idmethod"=>$idmethod
		));
	}

	public function updateStatusSandbox($idmethod, $sandbox){
		$sql = new Sql();

		switch ($sandbox) {
			case '0':
				$sandbox = 1;		
				break;
			
			case '1':
				$sandbox = 0;
				break;
		}
		
		$sql->query("UPDATE tb_methodspayment SET sandbox = :sandbox WHERE idmethod = :idmethod", array(
			":sandbox"=>$sandbox,
			":idmethod"=>$idmethod
		));
	}

/* Finalizar a inclusão de Métodos de Pagamento
	public function save(){
		$sql = new Sql();
		
		$results = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
			":desperson"=>utf8_decode($this->getdesperson()),
			":deslogin"=>$this->getdeslogin(),
			":despassword"=>User::getPasswordHash($this->getdespassword()),
			":desemail"=>$this->getdesemail(),
			":nrphone"=>$this->getnrphone(),
			":inadmin"=>$this->getinadmin()
		));

		$this->setData($results[0]);
	}
*/
	public function get($idpayment){
		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_methodspayment WHERE idmethod = :idmethod", array(
			":idmethod"=>$idpayment
		));

		$data = $results[0];

		$data['desname'] = utf8_encode($data['desname']);
		$data['descompany'] = utf8_encode($data['descompany']);
		$data['desresume'] = utf8_encode($data['desresume']);

		$results;

		$this->setData($data);
	}


	public function update(){
		$sql = new Sql();

		$results = $sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
			":iduser"=>$this->getiduser(),
			":desperson"=>utf8_decode($this->getdesperson()),
			":deslogin"=>$this->getdeslogin(),
			":despassword"=>User::getPasswordHash($this->getdespassword()),
			":desemail"=>$this->getdesemail(),
			":nrphone"=>$this->getnrphone(),
			":inadmin"=>$this->getinadmin()
		));

		$this->setData($results[0]);
	}

	public function delete(){
		$sql = new Sql();
		$sql->query("CALL sp_users_delete(:iduser)" , array(
			":iduser"=>$this->getiduser()
		));
	}

	public function checkPhoto(){
		if(file_exists($_SERVER["DOCUMENT_ROOT"] . 
			DIRECTORY_SEPARATOR . "res" . 
			DIRECTORY_SEPARATOR . "site" . 
			DIRECTORY_SEPARATOR . "img" . 
			DIRECTORY_SEPARATOR . "users" . 
			DIRECTORY_SEPARATOR . $this->getiduser() . ".jpg")){
			$url = "/res/site/img/users/" . $this->getiduser() . ".jpg";
		}
		else{
			$url = "/res/site/img/user.jpg";
		}

		return $this->setdesphoto($url);
	}

	public static function checkPhotoAdmin(){

		$user = new User();

		if(file_exists($_SERVER["DOCUMENT_ROOT"] . 
			DIRECTORY_SEPARATOR . "res" . 
			DIRECTORY_SEPARATOR . "site" . 
			DIRECTORY_SEPARATOR . "img" . 
			DIRECTORY_SEPARATOR . "users" . 
			DIRECTORY_SEPARATOR . $_SESSION[User::SESSION]['iduser']  . ".jpg") && User::checkLogin()){
			$url = "/res/site/img/users/" . $_SESSION[User::SESSION]['iduser'] . ".jpg";
		}
		else{
			$url = "/res/site/img/user.jpg";
		}

		return $url;
	}


	public static function checkPhotoUser($iduser){

		if(file_exists($_SERVER["DOCUMENT_ROOT"] . 
			DIRECTORY_SEPARATOR . "res" . 
			DIRECTORY_SEPARATOR . "site" . 
			DIRECTORY_SEPARATOR . "img" . 
			DIRECTORY_SEPARATOR . "users" . 
			DIRECTORY_SEPARATOR . $iduser  . ".jpg") && User::checkLogin()){
			$url = "/res/site/img/users/" . $iduser . ".jpg";
		}
		else{
			$url = "/res/site/img/user.jpg";
		}

		return $url;
	}

public function getValues(){

	$this->checkPhoto();

	$values = parent::getValues();

	return $values;
}

public function setPhoto($file){
	$extension = explode('.', $file["name"]);
	$extension = end($extension);

	switch ($extension) {
		case 'jpg':
		case 'jpeg':
		$image = imagecreatefromjpeg($file["tmp_name"]);
		break;

		case 'gif':
		$image = imagecreatefromgif($file["tmp_name"]);
		break;

		case 'png':
		$image = imagecreatefrompng($file["tmp_name"]);
		break;
	}

	$dist = $_SERVER["DOCUMENT_ROOT"] . 
	DIRECTORY_SEPARATOR . "res" . 
	DIRECTORY_SEPARATOR . "site" . 
	DIRECTORY_SEPARATOR . "img" . 
	DIRECTORY_SEPARATOR . "users" . 
	DIRECTORY_SEPARATOR . $this->getiduser() . ".jpg";

	imagejpeg($image, $dist);

	imagedestroy($image);

	$this->checkPhoto();
}	

public static function getForgot($email, $inadmin = true){
	$sql = new Sql();
	$results = $sql->select("SELECT * FROM tb_persons a INNER JOIN tb_users b USING(idperson) WHERE a.desemail = :email", array(
		":email" => $email
	));


	if(count($results) === 0)
	{
		throw new \Exception("Não foi possível recuperar a senha.");
	}
	else{

		$data = $results[0];

		$results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
			":iduser"=> $data["iduser"], 
			":desip"=> $_SERVER["REMOTE_ADDR"]
		));

		if(count($results2) === 0){
			throw new \Exception("Não foi possível recuperar a senha.");
		}
		else{

			$dataRecovery = $results2[0];

			$iv = random_bytes(openssl_cipher_iv_length('aes-256-cbc'));

			$code = openssl_encrypt($dataRecovery["idrecovery"], 'aes-256-cbc', User::SECRET, 0, $iv);

			$result = base64_encode($iv.$code);

			$conf = Conf::getConf();

			if($inadmin === true){
				$link = "http://" . $conf->getdesurl() . "/admin/forgot/reset?code=$result";

			}
			else{
				$link = "http://" . $conf->getdesurl() ."/forgot/reset?code=$result";
			}

			$mailer = new Mailer($data["desemail"],$data["desperson"], "Redefinir senha da sua conta em Minha Loja Virtual", "forgot", 
				array(
					"name"=>$data["desperson"],
					"link"=>$link
				));

			$mailer->send();

			return $link;
		}
	}
}

public static function validForgotDecrypt($result){
	$result = base64_decode($result);
	$code = mb_substr($result, openssl_cipher_iv_length('aes-256-cbc'), null, '8bit');
	$iv = mb_substr($result, 0, openssl_cipher_iv_length('aes-256-cbc'), '8bit');
	$idrecovery = openssl_decrypt($code, 'aes-256-cbc', User::SECRET, 0 , $iv);
	$sql = new Sql();

	$results = $sql->select("SELECT * 	FROM tb_userspasswordsrecoveries a 
		INNER JOIN tb_users b USING(iduser)
		INNER JOIN tb_persons c USING(idperson) 
		WHERE a.idrecovery = :idrecovery AND a.dtrecovery IS NULL AND DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();", 
		array(
			":idrecovery"=> $idrecovery)
	);

	if(count($results) === 0){
		throw new \Exception("Não foi possível recuperar a senha.");		
	}
	else{
		return $results[0];
	}
}

public static function setForgotUsed($idrecovery){
	$sql = new Sql();

	$sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery = NOW() WHERE idrecovery = :idrecovery", array(
		":idrecovery"=>$idrecovery
	));
}

public function setPassword($password){
	$sql = new Sql();

	$sql->query("UPDATE tb_users SET despassword = :password WHERE iduser = :iduser", array(
		":password"=>$password,
		":iduser"=>$this->getiduser()
	));
}

public static function setError($msg){
	$_SESSION[User::ERROR] = $msg;
}

public static function getError(){
	$msg = (isset($_SESSION[User::ERROR])) ? $_SESSION[User::ERROR] : "";
	User::cleanError();
	return $msg;
}

public static function cleanError(){
	$_SESSION[User::ERROR] = NULL;
}


public static function setSuccess($msg){
	$_SESSION[User::SUCCESS] = $msg;
}

public static function getSuccess(){
	$msg = (isset($_SESSION[User::SUCCESS])) ? $_SESSION[User::SUCCESS] : "";
	User::cleanSuccess();
	return $msg;
}

public static function cleanSuccess(){
	$_SESSION[User::SUCCESS] = NULL;
}	

public static function setErrorRegister($msg){
	$_SESSION[User::ERROR_REGISTER] = $msg;
}

public static function getErrorRegister(){
	$msg = (isset($_SESSION[User::ERROR_REGISTER]) && $_SESSION[User::ERROR_REGISTER]) ? $_SESSION[User::ERROR_REGISTER] : "";
	User::cleanErrorRegister();
	return $msg;
}

public static function cleanErrorRegister(){
	$_SESSION[User::ERROR_REGISTER] = NULL;
}		

public static function checkLoginExists($login){
	$sql = new Sql();

	$results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :deslogin",[
		":deslogin"=>$login
	]);

	return (count($results) > 0);
}

public static function getPasswordHash($password){
	return password_hash($password, PASSWORD_DEFAULT, [
		'cost'=>12
	]);
}

public function getOrders(){

	$sql = new Sql();

	$results = $sql->select("SELECT * 
		FROM tb_orders a
		INNER JOIN tb_ordersstatus b USING(idstatus) 
		INNER JOIN tb_carts c USING(idcart)
		INNER JOIN tb_users d ON d.iduser = a.iduser 
		INNER JOIN tb_addresses e USING (idaddress) 
		INNER JOIN tb_persons f ON f.idperson = d.idperson
		WHERE a.iduser = :iduser", [
			":iduser"=>$this->getiduser()
		]);

	return $results;

}

public static function getUsersPage($page = 1, $itemsPerPage = 10){

	$start = ($page-1) * $itemsPerPage;

	$sql = new Sql();

	$results = $sql->select("SELECT SQL_CALC_FOUND_ROWS * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson LIMIT $start, $itemsPerPage");

	$resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");

	return[
		'data'=>$results,
		'total'=>(int)$resultTotal[0]["nrtotal"],
		'pages'=>ceil($resultTotal[0]["nrtotal"]/$itemsPerPage)
	];
}

public static function getUsersPageSearch($search, $page = 1, $itemsPerPage = 15){

	$start = ($page-1) * $itemsPerPage;

	$sql = new Sql();

	$results = $sql->select("SELECT SQL_CALC_FOUND_ROWS * 
		FROM tb_users a INNER JOIN tb_persons b USING(idperson) 
		WHERE b.desperson LIKE :search OR b.desemail LIKE :search OR a.deslogin LIKE :search
		ORDER BY b.desperson LIMIT $start, $itemsPerPage", [
			":search"=>"%" . $search . "%"
		]);

	$resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");

	return[
		'data'=>$results,
		'total'=>(int)$resultTotal[0]["nrtotal"],
		'pages'=>ceil($resultTotal[0]["nrtotal"]/$itemsPerPage)
	];
}	
}

?>