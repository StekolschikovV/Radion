<?php
include 'core/Connection.php';
$DB = new DB;
$startFrom = $_POST['startFrom'];
$Category = $_POST['categorypage'];

$q =mysql_query("select * from `serial_category` where `link`='".$Category."'");
$category_id = mysql_fetch_array($q);


$count = mysql_query("SELECT count(1) FROM `serial_item` where `category_id`='".$category_id["id"]."'");
$countRows = mysql_fetch_array($count);
//echo $startFrom. " > " .$countRows[0];

if($startFrom >= $countRows[0]){
	echo json_encode("error");
} else {
	//$res = mysql_query("SELECT * FROM `serial_item` where `category_id`='".$category_id["id"]."' ORDER BY `id` DESC LIMIT {$startFrom}, 8");
	/*$res = mysql_query("SELECT * FROM `serial_movie` JOIN `serial_item` ON `serial_item`.`movie_id` = `serial_movie`.`id` WHERE 
		`serial_movie`.`category_id`='".$category_id["id"]."' AND `serial_item`.`show` != 1 GROUP BY `serial_item`.`movie_id` ORDER BY `serial_movie`.`weight` ASC LIMIT {$startFrom}, 12");*/
	$res = mysql_query("SELECT `id`,`link`,`name` FROM `serial_movie` where `category_id`='".$category_id["id"]."' ORDER BY `serial_movie`.`weight` ASC LIMIT {$startFrom}, 12");
	$articles = array();
	while ($row = mysql_fetch_array($res))
	{
		$q3 = mysql_query("select `id`,`poster_url` from `serial_item` where `movie_id`='".$row["id"]."' and `show` != 1 order by `id` desc limit 1");
		$r2 = mysql_fetch_array($q3);
		$row["findId"] = $r2[0];
		$row["last_poster"] = $r2[1];
				
		array_push($row, $row["last_poster"]);
		array_push($row, $row["findId"]);

		$articles[] = $row;
	}
	echo json_encode($articles);
}