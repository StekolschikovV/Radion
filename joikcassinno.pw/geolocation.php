<?php

// Подключаемся к базе данных
require_once("core/Connection.php");
$DB = new DB;

class geolocation extends DB{

	public function getCountry($ip){

		DB::query("select `type` from `geolocation_status` where `id`='1'");
		$type = DB::fetch_array();

		if($type["type"] == 0){
			#USE API
			$urlsend = "https://api.sypexgeo.net/3j8Ur/json/".$ip;
			$cr = curl_init($urlsend);
			curl_setopt($cr,CURLOPT_RETURNTRANSFER,true); 
			$content = curl_exec($cr);
			curl_close($cr);
			$data = json_decode($content, true);
			$_SESSION["COUNTRU_CODE"] = $data["country"]["iso"];
			return $data["country"]["name_ru"];
		}
		if($type["type"] == 1){
			#USE DAT
			//header('Content-type: text/plain; charset=utf8');
			include("SxGeo.php");
			$SxGeo = new SxGeo('SxGeo.dat', SXGEO_BATCH | SXGEO_MEMORY); 
			$_SESSION["COUNTRU_CODE"] = $SxGeo->getCountry($ip);
			DB::query("select `name_ru` from `net_country` where `code`='".$SxGeo->getCountry($ip)."'");
			$row = DB::fetch_array();
			if($row){ 
				return $row["name_ru"]; 
			} else { 
				return 'Россия';
			}
		}
		if($type["type"] == 2){
			#USE MYSQL
			$int = self::ip2int($ip);
			DB::query("select count(1) from (select * from net_euro where begin_ip<=$int order by begin_ip desc limit 1) as t where end_ip>=$int");
			$getCount = DB::fetch_array();
			if ($getCount[0] == 0) {
				DB::query("select count(1) from (select * from net_country_ip where begin_ip<=$int order by begin_ip desc limit 1) as t where end_ip>=$int");
				$getSecondCount = DB::fetch_array();
			}
			if ($getSecondCount[0] != 0) {
				DB::query("select * from (select * from net_country_ip where begin_ip<=$int order by begin_ip desc limit 1) as t where end_ip>=$int");
				$row = DB::fetch_array();
				$country_id = $row['country_id'];
				DB::query("select count(1) from net_country where id='".$country_id."'");
				$countResult = DB::fetch_array();
				if ($countResult[0] != 0) {
					DB::query("select * from net_country where id='".$country_id."'");
					$rows = DB::fetch_array();
					$country = $rows['name_ru'];
					return $country;
				}
			}
		}
	}

	public function ip2int($ip){
		$part = explode(".", $ip);
		$int = 0;
		if (count($part) == 4) {
			$int = $part[3] + 256 * ($part[2] + 256 * ($part[1] + 256 * $part[0]));
		}
		return $int;
	}

}


?>