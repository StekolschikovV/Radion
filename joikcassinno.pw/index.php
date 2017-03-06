<?php

//if($_SERVER["REMOTE_ADDR"] == "109.87.225.20"){
	#ini_set('display_errors', 1);
	#error_reporting(E_ALL);
//}
#КЕШИРОВАНИЕ В СЕКУНДАХ
header("X-Accel-Expires: 1800");
session_start();
/*if (!empty($_COOKIE[session_name()]) || $_SERVER['REQUEST_METHOD'] == 'POST') {
	session_id() || session_start();
}*/

//$start = microtime(true);

session_set_cookie_params(10800);
define("TEMPLATE_ROOT", "templates/");

require_once("core/Application.php");
require_once("core/Connection.php");
require_once("geolocation.php");

$DB = new DB;
$Core = new Core;


$Core->Run();
			

#КОНТРОЛЬНОЕ ВРЕМЯ
//echo "<font color='white'>Время выполнения скрипта: ".(microtime(true) - $start)."</font>";
?>