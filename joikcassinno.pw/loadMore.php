<?php
session_start();
include 'core/Connection.php';
$DB = new DB;
$startFrom = $_POST["startFrom"];
$countRows= 0;
$count = mysql_query("SELECT count(1) FROM `serial_movie` JOIN `serial_item` ON `serial_item`.`movie_id` = `serial_movie`.`id` group by `serial_item`.`movie_id`");
while($c = mysql_fetch_array($count)){
	$countRows++;
}

if(isset($_SESSION['mobile_id'])){
	$Q = mysql_query("select * from `xbill_abonents` where `id`='".$_SESSION['mobile_id']."'");
	$account=mysql_fetch_array($Q);
									
	$Q3 = mysql_query("select count(1) from `serial_item`");
	$count_movies=mysql_fetch_array($Q3);
	if($account["qty_sms"] == "0"){
		$limitCount = "0";
	}
	if($account["qty_sms"] == "1"){
		$limitCount = "16";
	}
	if($account["qty_sms"] == "2"){
		$limitCountExp = $count_movies[0] / 2;
		$limitCountTurn = explode(".", $limitCountExp);
		$limitCount = $limitCountTurn[0] - 4;	
	}
	if($account["qty_sms"] >= "3"){
		$limitCount = $count_movies[0] - 4;
	}
}

if($startFrom > $countRows){
	echo json_encode("error");
} else {
	//$res = mysql_query("SELECT * FROM `serial_movie` JOIN `serial_item` ON `serial_item`.`movie_id` = `serial_movie`.`id` WHERE `serial_item`.`show` != 1 GROUP BY `serial_item`.`movie_id` ORDER BY `serial_movie`.`weight` ASC LIMIT {$startFrom}, 12");
	$res = mysql_query("SELECT `id`,`link`,`name` FROM `serial_movie` ORDER BY `serial_movie`.`weight` ASC LIMIT {$startFrom}, 12");
	$articles = array();
	while ($row = mysql_fetch_assoc($res)){
			if(isset($_SESSION['mobile_id'])){
				
				$q3 = mysql_query("select `id`,`poster_url` from `serial_item` where `movie_id`='".$row["id"]."' and `show` != 1 order by `id` desc limit 1");
				$r2 = mysql_fetch_array($q3);
				$row["last_id"] = $r2[0];
				$row["last_poster"] = $r2[1];
				
				array_push($row, $row["last_id"]);
				array_push($row, $row["last_poster"]);

				$articles[] = $row;
			} else {
				$q3 = mysql_query("select `id`,`poster_url` from `serial_item` where `movie_id`='".$row["id"]."' and `show` != 1 order by `id` desc limit 1");
				$r2 = mysql_fetch_array($q3);
				$row["last_id"] = $r2[0];
				$row["last_poster"] = $r2[1];
				
				array_push($row, $row["last_id"]);
				array_push($row, $row["last_poster"]);

				$articles[] = $row;
			}
	}
	echo json_encode($articles);
 
}