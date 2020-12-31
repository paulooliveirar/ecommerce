<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Product;

$app->get("/admin/products", function(){
	
	User::verifyLogin();

	$search = (isset($_GET['search'])) ? $_GET['search']: "";
	$page = (isset($_GET['page'])) ? $_GET['page']: 1;
	$productspage = (isset($_GET['products-page']) && $_GET['products-page'] != "") ? $_GET['products-page']: 15;

	$allproducts = count(Product::listAll());	

	if($search != ""){
		$pagination = Product::getProductsPageSearch($search, $page, $allproducts);
	} else{
		$pagination = Product::getProductsPage($page, $allproducts);
	}

	//$pagination = User::getUsersPage($page, passar o número máximo por página);
	$pages = [];

	for($x = 0; $x < $pagination["pages"]; $x++){
		array_push($pages, [
			"href"=>"/admin/products?" . http_build_query([
				"page"=>$x+1,
				"search"=>$search
			]),
			"text"=>$x+1
		]);
	}
	

	$page = new PageAdmin();

	$page->setTpl("products", array(
		"products"=>$pagination['data'],
		"search"=>$search,
		"pagination"=>$productspage,
		"pages"=>$pages,
		"allproducts"=> $allproducts
	));
});

$app->get("/admin/products", function(){
	
	User::verifyLogin();

	$products = Product::listAll();

	$page = new PageAdmin();

	$page->setTpl("products", array(
		"products"=>$products
	));
});

$app->get("/admin/products/create", function(){
	
	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("products-create"); 
});


$app->post("/admin/products/create", function(){

	User::verifyLogin();

	$products = new Product();

	$products->setData($_POST);

	$products->save();

	header("Location: /admin/products");
	exit;
});

$app->get('/admin/products/:idproduct', function($idproduct){

	User::verifyLogin();

	$product = new Product();

	$product->get((int)$idproduct);

	$page = new PageAdmin();

	$page->setTpl("products-update", array(
		"product"=>$product->getValues()
	));

});

$app->post('/admin/products/:idproduct', function($idproduct){

	User::verifyLogin();

	$product = new Product();

	$product->get((int)$idproduct);

	$product->setData($_POST);

	$product->save();

	$product->setPhoto($_FILES["file"]);

	header("Location: /admin/products");
	exit;		
});

$app->get('/admin/products/:idproduct/delete', function($idproduct){

	User::verifyLogin();

	$products = new Product();

	$products->get((int)$idproduct);

	$products->delete();

	header("Location: /admin/products");
	exit;

});

?>