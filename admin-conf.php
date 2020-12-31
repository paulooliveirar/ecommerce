<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Conf;

$app->get('/admin/data-store', function() {
	User::verifyLogin();

	$page = new PageAdmin();

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

	$page->setTpl("conf-update", array(
		"conf"=>$array_conf,
		"error"=>"",
		"success"=>""
	));
});

$app->post('/admin/data-store/update', function() {
	User::verifyLogin();

	$page = new PageAdmin();	

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

	if (!isset($_POST['desname']) || $_POST['desname'] === '') {
		Conf::setMsgError("Informe a razão social.");
		$page->setTpl("conf-update", array(
			"conf"=>$_POST,
			"error"=>Conf::getMsgError(),
			"success"=>""
		));	
		exit;		
	}

	if (!isset($_POST['desurl']) || $_POST['desurl'] === '') {
		Conf::setMsgError("Informe a url do site.");
		$page->setTpl("conf-update", array(
			"conf"=>$_POST,
			"error"=>Conf::getMsgError(),
			"success"=>""	
		));	
		exit;		
	}

	if (!isset($_POST['descnpj_cpf']) || $_POST['descnpj_cpf'] === '') {
		Conf::setMsgError("Informe o CPF ou CNPJ.");
		$page->setTpl("conf-update", array(
			"conf"=>$_POST,
			"error"=>Conf::getMsgError(),
			"success"=>""	
		));	
		exit;		
	}

	if (!isset($_POST['desphone']) || $_POST['desphone'] === '') {
		Conf::setMsgError("Informe o número do telefone.");
		$page->setTpl("conf-update", array(
			"conf"=>$_POST,
			"error"=>Conf::getMsgError(),
			"success"=>""	
		));	
		exit;		
	}

	if (!isset($_POST['desemail']) || $_POST['desemail'] === '') {
		Conf::setMsgError("Informe o email comercial.");
		$page->setTpl("conf-update", array(
			"conf"=>$_POST,
			"error"=>Conf::getMsgError(),
			"success"=>""	
		));	
		exit;		
	}

	if (!isset($_POST['deszipcode']) || $_POST['deszipcode'] === '') {
		Conf::setMsgError("Informe o seu cep.");
		$page->setTpl("conf-update", array(
			"conf"=>$_POST,
			"error"=>Conf::getMsgError(),
			"success"=>""	
		));	
		exit;		
	}

	if (!isset($_POST['descountry']) || $_POST['descountry'] === '') {
		Conf::setMsgError("Informe o país.");
		$page->setTpl("conf-update", array(
			"conf"=>$_POST,
			"error"=>Conf::getMsgError(),
			"success"=>""	
		));	
		exit;		
	}

	if (!isset($_POST['desstate']) || $_POST['desstate'] === '') {
		Conf::setMsgError("Informe o estado.");
		$page->setTpl("conf-update", array(
			"conf"=>$_POST,
			"error"=>Conf::getMsgError(),
			"success"=>""	
		));	
		exit;		
	}

	if (!isset($_POST['descity']) || $_POST['descity'] === '') {
		Conf::setMsgError("Informe a cidade.");
		$page->setTpl("conf-update", array(
			"conf"=>$_POST,
			"error"=>Conf::getMsgError(),
			"success"=>""	
		));	
		exit;
	}

	if (!isset($_POST['desaddress']) || $_POST['desaddress'] === '') {
		Conf::setMsgError("Informe o endereço.");
		$page->setTpl("conf-update", array(
			"conf"=>$_POST,
			"error"=>Conf::getMsgError(),
			"success"=>""	
		));	
		exit;		
	}

	if (!isset($_POST['desnumber']) || $_POST['desnumber'] === '') {
		Conf::setMsgError("Informe o número.");
		$page->setTpl("conf-update", array(
			"conf"=>$_POST,
			"error"=>Conf::getMsgError(),
			"success"=>""	
		));	
		exit;		
	}

	Conf::setMsgSuccess("Dados Salvo com Sucesso.");
	
	$conf->setData($_POST);

	$conf->update();

	$page->setTpl("conf-update", array(
		"conf"=>$_POST,
		"error"=>Conf::getMsgError(),
		"success"=>Conf::getMsgSuccess()	
	));	
	exit;

});
?>