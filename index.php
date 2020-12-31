<?php 
session_start();
//ini_set('display_errors',true);
require_once("vendor/autoload.php");

//echo '<pre>' , var_dump($_SESSION) , '</pre>';

use \Slim\Slim;

$app = new Slim();

$app->config('debug', true);

require_once("site.php");
//require_once("site-cart.php");
//require_once("site-login.php");
//require_once("site-profile.php");
require_once("site-pagseguro.php");
require_once("site-checkout.php");
require_once("admin.php");
require_once("admin-google.php");
require_once("admin-conf.php");
require_once("admin-orders.php");
require_once("admin-users.php");
require_once("admin-categories.php");
require_once("admin-products.php");
require_once("admin-methods-payment.php");
require_once("admin-methods-shipping.php");
require_once("functions.php");

$app->run();

 ?>