<?php

require_once("core/Connection.php");
$DB = new DB;

$url = explode("/", $_SERVER["REQUEST_URI"]);
$link_movie = $url[2];

function file_force_download($file) {
    header('X-SendFile: ' . realpath($file));
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($file));
    exit;
}

$DB->query("select * from `movies`");
while($rows = $DB->fetch_array()){
	if(md5($rows["name_url"]) == $link_movie){
		$link_download = "/media/".$rows["real_preview_url"];
	}
}

file_force_download($link_download);
?>
