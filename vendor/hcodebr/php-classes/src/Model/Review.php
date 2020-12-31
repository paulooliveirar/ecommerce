<?php 

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Model\User;
use \Hcode\Mailer;

class Review extends Model {

	public static function listAll(){

		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_reviews ORDER BY idreview");
	}

	public function checkList($list){
		foreach ($list as &$row) {
			$p = new Review();
			$p->setData($row);
			$row = $p->getValues();
		}

		return $list;
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


	public static function averageReview($idproduct){

		$sql = new Sql();

		$results = $sql->select("SELECT b.nrreview FROM tb_products a INNER JOIN tb_reviews b ON a.idproduct = b.idproduct 
			WHERE b.idproduct = :idproduct", [
				":idproduct"=>$idproduct
			]);

		return $results;			
	}

	public function save(){
		
		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_products a INNER JOIN tb_reviews b ON a.idproduct = b.idproduct 
			WHERE b.idproduct = :idproduct", [
				":idproduct"=>$this->getidproduct()
			]);

		$results = $sql->select("CALL sp_reviews_save(:idproduct, :desreview, :nrreview)", [
			":idproduct"=>$this->getidproduct(),
			":desreview"=>$this->getdesreview(), 
			":nrreview"=>$this->getnrreview()
		]);

		$this->setData($results[0]);

		User::createLog("Depoimento salvo com sucesso");
	}

	public function getProduct($idproduct){
		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_products a INNER JOIN tb_reviews b ON a.idproduct = b.idproduct INNER JOIN tb_persons c ON b.idperson = c.idperson
			INNER JOIN tb_users d ON c.idperson = d.idperson
			WHERE b.idproduct = :idproduct", [
				":idproduct"=>$idproduct
			]);

		return 	Review::checkList($results);
	}	

	public function get($idreview){
		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_reviews WHERE idreview = :idreview", [
			":idreview"=>$idreview
		]);

		$this->setData($results[0]);
	}

	public function delete(){
		$sql = new Sql();
		$sql->query("DELETE FROM tb_products WHERE idproduct = :idproduct" , array(
			":idproduct"=>$this->getidproduct()
		));

		Product::updateFile();
		User::createLog("Produto excluído com sucesso");		
	}

	public function getValues(){

		$this->checkPhoto();

		$values = parent::getValues();

		return $values;
	}

	public static function getReviewsPage($page = 1, $itemsPerPage = 15){
		
		$start = ($page-1) * $itemsPerPage;

		$sql = new Sql();

		$results = $sql->select("SELECT SQL_CALC_FOUND_ROWS * FROM tb_products ORDER BY desproduct LIMIT $start, $itemsPerPage");
		$resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");

		return[
			'data'=>Product::checkList($results),
			'total'=>(int)$resultTotal[0]["nrtotal"],
			'pages'=>ceil($resultTotal[0]["nrtotal"]/$itemsPerPage)
		];
	}

	public static function getReviewsPageSearch($search, $page = 1, $itemsPerPage = 15){
		
		$start = ($page-1) * $itemsPerPage;

		$sql = new Sql();

		$results = $sql->select("SELECT SQL_CALC_FOUND_ROWS * 
			FROM tb_products
			WHERE desproduct LIKE :search OR desurl LIKE :search OR vlprice = :price
			ORDER BY desproduct LIMIT $start, $itemsPerPage", [
				":search"=>"%" . $search . "%",
				":price"=>$search
			]);

		$resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");

		return[
			'data'=>Product::checkList($results),
			'total'=>(int)$resultTotal[0]["nrtotal"],
			'pages'=>ceil($resultTotal[0]["nrtotal"]/$itemsPerPage)
		];
	}
}

?>