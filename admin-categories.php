<?php 

use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Category;
use \Hcode\Model\Product;

$app->get("/admin/categories", function(){
	
	User::verifyLogin();

	$search = (isset($_GET['search'])) ? $_GET['search']: "";
	$page = (isset($_GET['page'])) ? $_GET['page']: 1;

	if($search != ""){
		$pagination = Category::getUsersPageSearch($search, $page);
	} else{
		$pagination = Category::getUsersPage($page);
	}

	//$pagination = User::getUsersPage($page, passar o número máximo por página);
	$pages = [];

	for($x = 0; $x < $pagination["pages"]; $x++){
		array_push($pages, [
			"href"=>"/admin/categories?" . http_build_query([
				"page"=>$x+1,
				"search"=>$search
			]),
			"text"=>$x+1
		]);
	}
	

	$page = new PageAdmin();

	$page->setTpl("categories", array(
		"categories"=>$pagination['data'],
		"search"=>$search,
		"pages"=>$pages
	));
});

$app->get("/admin/categories/create", function(){

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("categories-create"); 
});

$app->post("/admin/categories/create", function(){

	User::verifyLogin();

	$categories = new Category();

	$categories->setData($_POST);

	$categories->save();

	$page = new PageAdmin();

	$page->setTpl("categories-create"); 

	header("Location: /admin/categories");
	exit;
});

$app->get('/admin/categories/:idcategory/delete', function($idcategory){

	User::verifyLogin();

	$categories = new Category();

	$categories->get((int)$idcategory);

	$categories->delete();

	header("Location: /admin/categories");
	exit;
		
});


$app->get('/admin/categories/:idcategory', function($idcategory){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$page = new PageAdmin();

	$page->setTpl("categories-update", array(
		"category"=>$category->getValues()
	));
		
});

$app->post('/admin/categories/:idcategory', function($idcategory){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$category->setData($_POST);

	$category->save();

	header("Location: /admin/categories");
	exit;		
});

$app->get('/admin/categories/:idcategory/products', function($idcategory){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$page = new PageAdmin();

	$page->setTpl("categories-products", array(
		"category"=>$category->getValues(),
		"productsRelated"=>$category->getProducts(),
		"productsNotRelated"=>$category->getProducts(false)
	));
		
});


$app->get('/admin/categories/:idcategory/products/:idproduct/add', function($idcategory, $idproduct){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$product = new Product();

	$product->get((int)$idproduct);

	$category->addProduct($product);

	header("Location: /admin/categories/" . $idcategory . "/products");
	exit;
});

$app->get('/admin/categories/:idcategory/products/:idproduct/remove', function($idcategory, $idproduct){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$product = new Product();

	$product->get((int)$idproduct);

	$category->removeProduct($product);

	header("Location: /admin/categories/" . $idcategory . "/products");
	exit;
		
});


?>