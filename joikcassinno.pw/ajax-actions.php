<?php
header("Content-type: text/html; charset=utf8");
include 'core/Connection.php';
$DB = new DB;

if($_POST["comment"]){
	$DB->query("insert into `serial_website_reviews` (`author`,`comment`,`active`,`ip`) values ('".$_POST["author"]."','".$_POST["comment"]."','1','".$_POST["ip"]."')");
	echo 0;
	
}