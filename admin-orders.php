<?php 

use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Conf;
use \Hcode\Model\Order;
use \Hcode\Model\OrderStatus;


$app->get("/admin/orders/:idorder/delete", function($idorder){

	User::verifyLogin();

	$order = new Order();

	$order->get((int)$idorder);

	$order->delete();

	header("Location: /admin/orders");
	exit;

});

$app->get("/admin/orders/:idorder/status", function($idorder){

	User::verifyLogin();

	$order = new Order();

	$order->get((int)$idorder);

	$cart = $order->getCart();

	$page = new PageAdmin();

	$page->setTpl("order-status", [
		"order"=>$order->getValues(),	
		"status"=>OrderStatus::listAll(),
		"msgSuccess"=>Order::getSuccess(),		
		"msgError"=>Order::getError()
	]);

});


$app->post("/admin/orders/:idorder/status", function($idorder){

	User::verifyLogin();

	if(!isset($_POST['idstatus']) || !(int)$_POST['idstatus'] > 0){
		Order::setError("Informe o status atual");
		header("Location: /admin/orders/" . $idorder . "status");
		exit;

	}

	$order = new Order();

	$order->get((int)$idorder);

	$order->setidstatus((int)$_POST['idstatus']);

	$order->save();

	Order::setSuccess("Status atualizado com Successo!");

	header("Location: /admin/orders/" . $idorder . "/status");
	exit;

});


$app->get("/admin/orders/:idorder", function($idorder){

	User::verifyLogin();

	$order = new Order();

	$order->get((int)$idorder);

	$cart = $order->getCart();
	
	$conf = new Conf();

	$conf = $conf->getConf();

	$array_conf = [];

	$keys = [];

	foreach((array)$conf as $data)
	{
		if(is_array($data))
		{
			foreach($data as $key => $other_data)
			{
				array_push($keys, $key);
				array_push($array_conf, $other_data);
			}
		}
	}

	$array_conf = array_combine($keys, $array_conf);

	//var_dump($order->getValues());

	$page = new PageAdmin();

	$page->setTpl("order", [
		"order"=>$order->getValues(),
		"cart"=>$cart->getValues(),
		"products"=>$cart->getProducts(),
		"conf"=>$array_conf
	]);

});

$app->get("/admin/orders", function(){

	User::verifyLogin();

	$search = (isset($_GET['search'])) ? $_GET['search']: "";
	$page = (isset($_GET['page'])) ? $_GET['page']: 1;
	$orderspage = (isset($_GET['orders-page']) && $_GET['orders-page'] != "") ? $_GET['orders-page']: 15;

	$allorders = count(Order::listAll());

	if($search != ""){
		$pagination = Order::getOrdersPageSearch($search, $page, $orderspage);
	} else{
		$pagination = Order::getOrdersPage($page, $orderspage);
	}

	//$pagination = User::getOrdersPage($page, passar o número máximo por página);
	$pages = [];

	for($x = 0; $x < $pagination["pages"]; $x++){
		array_push($pages, [
			"href"=>"/admin/orders?" . http_build_query([
				"page"=>$x+1,
				"search"=>$search
			]),
			"text"=>$x+1
		]);
	}	

	$page = new PageAdmin();

	$page->setTpl("orders", [
		"orders"=>$pagination['data'],
		"search"=>$search,
		"pagination"=>$orderspage,
		"pages"=>$pages,
		"allorders"=> $allorders
	]);

});

?>