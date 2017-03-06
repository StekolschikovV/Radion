<?php
header("Content-type: text/html; charset=utf8");
$search = $_POST['search'];
$search = addslashes($search);
$search = htmlspecialchars($search);
$search = stripslashes($search);
    if($search == ''){
       exit("Начните вводить запрос");
    }
require_once("core/Connection.php");
$DB = new DB;

require_once("core/Application.php");
$Core = new Core;

function cutString($string, $maxlen) {
     $len = (mb_strlen($string) > $maxlen)
         ? mb_strripos(mb_substr($string, 0, $maxlen), ' ')
         : $maxlen
     ;
     $cutStr = mb_substr($string, 0, $len);
     return (mb_strlen($string) > $maxlen)
         ? '' . $cutStr . '...'
         : '' . $cutStr . ''
     ;
 }

$DB->query("SELECT count(1) FROM `serial_movie` WHERE `name` like '%$search%'");
$count = $DB->fetch_array();
if($count[0] > 0){
	//echo '<h3>Быстрый поиск:</h3>';
    //$DB->query("SELECT * FROM `serial_movie` JOIN `serial_item` ON `serial_item`.`movie_id` = `serial_movie`.`id` WHERE `serial_movie`.`name` like '%".$search."%' group by `movie_id`");
    $DB->query("SELECT `id`,`link`,`name` FROM `serial_movie` WHERE `name` like '%".$search."%' ORDER BY `serial_movie`.`weight` ASC");
	while($rows = $DB->fetch_array()){
		$q = mysql_query("select `id`,`poster_url`,`description` from `serial_item` where `movie_id`='".$rows["id"]."' and `show` != 1 order by `id` desc limit 1");
		$r = mysql_fetch_array($q);

		echo '
        <a href="/serial/view/'.$r[0].'-'.$rows["link"].'.html" target="_blank">
		<div class="search-block-el">
								<img src="http://imagees2.pw/i2/'.$r["id"].'/'.$r["poster_url"].'">
								<div class="title">'.$rows["name"].'</div>
								<div class="text">'.$r["description"].'</div>
							</div></a>';
	}
	echo '<div class="search-block-el"><a class="showAllResults" href="javascript:void(0)" onclick="$(\'.search-block\').submit()">Все результаты</a></div>';
 }else{
    echo '<div class="search-block-el"><a class="showAllResults" href="javascript:void(0)" onclick="$(\'.searchResults\').html(\'\'); $(\'#search\').val(\'\');$(\'.searchResults\').removeClass(\'opened\');">Поиск не дал резултатов</a></div>';
 }
?>

