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
	echo '<h3>Быстрый поиск:</h3>';
    $DB->query("SELECT * FROM `serial_movie` JOIN `serial_item` ON `serial_item`.`movie_id` = `serial_movie`.`id` WHERE `serial_movie`.`name` like '%".$search."%' group by `movie_id`");
	while($rows = $DB->fetch_array()){
		$q = mysql_query("select `id` from `serial_item` where `movie_id`='".$rows["movie_id"]."' and `show` != 1 order by `id` desc limit 1");
		$r = mysql_fetch_array($q);

		echo "
		<table id='table_result' style='padding:5px' >
			<tr style='cursor: pointer;' onclick='window.open(\"/serial/view/".$r[0]."-".$rows["link"].".html\",\"_blank\"); return false;'>
				<td valign='top' style='padding:5px'><img src='http://imagees2.pw/i2/".$rows["id"]."/".$rows["poster_url"]."' style='width:100px;max-width: 100px;'></td>
				<td valign='top' style='padding:5px'>
					<a href='javascript: void(0)' target='_blank' style='color: #00b0eb; font-weight: bold;text-decoration: none;'>".$rows["name"]."</a>
					<p class='hover-search-text-film'>".$rows["description"]."</p>
				</td>
			</tr>
		</table>
		
		";
	}
	echo '<center><a href="javascript: void(0);" onclick="$(\'.searchRes\').addClass(\'displayNone\'); $(\'#live_search\').val(\'\');" style="color: red; font-weight: bold; text-decoration: none;"><i class="fa fa-times"></i> Очистить результат</a></center>';
 }else{
    echo "<div style='padding:10px; border:1px solid 00b0eb'><i class='fa fa-warning'></i> Поиск не дал результатов. Попробуйте изменить параметры поиска...</div>";
 }
?>