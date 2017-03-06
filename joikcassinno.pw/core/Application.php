<?php
/*
 * General class | General functions
 * This class have general functions for start engine
 */
 
//session_start();
require_once("Connection.php");

class Core extends DB {
        
        public function __construct() {
            parent::__construct();
        }
    	
    	# OPT
		public function Run(){
			$count = 0;

			//unset($_SESSION["checkIn"]);

		    if(!isset($_SESSION["checkIn"])){
		    	$_SESSION["checkIn"] = "true";
		    	$lRequest = explode("/", $_SERVER["REQUEST_URI"]);
			    $e = explode("&", $lRequest[3]);
			    if($e[1] != ""){
			    	$m = explode("ref=", $e[1]);
			    	$_COOKIE["ref"] = $m[0];
			    }
			    if(isset($_GET["ref"])){
			    	$_COOKIE["ref"] = $_GET["ref"];
			    }
			    DB::query("select * from `w_wap_settings` where `id`='1'");
			    $Is_enable_wap = DB::fetch_array();
			    $setWap = $Is_enable_wap["is_enable"]; // проверка включено или нет

			    $urlExplode = explode("-", $e[0]); $thisUrl = $urlExplode[0];
				$_SESSION["CURRLINKS"] = $thisUrl;

				#CHECK DODOPAY
				$checkDodopay = 0;

				require_once('MobileDetect.php'); 
				$detect = new Mobile_Detect;
				#CHECKPHONE
				if ($detect->isMobile()) {
				    DB::query("select `is_enable` from `dodopay_setting` where `id`='1'");
				    $dodopay = DB::fetch_array();
				    #IF DODOPAY ENABLE
				    if($dodopay["is_enable"] == 1){
				    	#IF GEOLOCATION = DODOPAY COUNTY
				    	if($_SESSION["geolocation"] == "Украина" || $_SESSION["geolocation"] == "Беларусь" || $_SESSION["geolocation"] == "Казахстан" || $_SESSION["geolocation"] == "Азербайджан"){
				    		if($_SESSION["geolocation"] == "Украина"){ $countryPay = "UA";}
				    		if($_SESSION["geolocation"] == "Беларусь"){ $countryPay = "BY";}
				    		if($_SESSION["geolocation"] == "Казахстан"){ $countryPay = "KZ";}
				    		if($_SESSION["geolocation"] == "Азербайджан"){ $countryPay = "AZ";}
				    		# FOUND IF ENABLE FOR COUNTRY PARAMETER
				    		$currPlatform = 20; 
				    		DB::query("select `".$countryPay."` from `dodopay_setting` where `id`='".$currPlatform."'");
				    		$dodopayParamEnable = DB::fetch_array();
				    		if($dodopayParamEnable[0] == 1){
				    			if($_GET["ref"] != "" && $_GET["ref"] != 0){ $_COOKIE["ref"] = $_GET["ref"]; }
						    	$getWapURL = self::isWapDodopay($thisUrl,$_COOKIE["ref"]);
						    	if($getWapURL == "error" || $getWapURL == ""){
						    		$checkDodopay = 0;
						    		$statusWap = "NOT_OK";
						    	} else {
						    		$checkDodopay = 1;
						    		$statusWap = "REDIRECT";
						    	}
				    		} else {
				    			$checkDodopay = 0;
				    		}
				    	} else {
				    		$checkDodopay = 0;
				    	}
				    }
				} else {
					$checkDodopay = 0;
				}

			    if($checkDodopay == 0){
					# OTHER WAP SETTING
				    if($setWap == 1){
					    if($Is_enable_wap["type_bill"] == 0){
					    	if($_GET["ref"] != "" && $_GET["ref"] != 0){ $_COOKIE["ref"] = $_GET["ref"]; }
					    	$getWapURL = self::isWapAbonent($thisUrl,$_COOKIE["ref"]);
					    } 

					    if($Is_enable_wap["type_bill"] == 1){
					    	if($_GET["ref"] != "" && $_GET["ref"] != 0){ $_COOKIE["ref"] = $_GET["ref"]; }
					    	$getWapURL = self::isWapAbonentPayStream($thisUrl,$_COOKIE["ref"]);
					    }

					    if($Is_enable_wap["type_bill"] == 2){
					    	# WAPEZ
					    	if($_GET["ref"] != "" && $_GET["ref"] != 0){ $_COOKIE["ref"] = $_GET["ref"]; }
					    	$getWapURL = self::isWapAbonentWapez($thisUrl,$_COOKIE["ref"]);
					    }
					    
				    	if($getWapURL == "error" || $getWapURL == ""){
				    		$statusWap = "NOT_OK";
				    	} else {
				    		$statusWap = "REDIRECT";
				    	}
				    }
				}

			   	if($statusWap == "NOT_OK"){
					require_once("templates/index.phtml");
				} else {;
					header('Location: '.$getWapURL);
				}
			} else {
				require_once("templates/index.phtml");
			}
		}

		/********* WAPEZ ***************/
		public function isWapAbonentWapez($url,$ref_id){
			if (empty($_SERVER["HTTP_X_FORWARDED_FOR"])){ 
		        $ip = $_SERVER["HTTP_X_REAL_IP"];
		    } else {
		    	$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		    }
		    $platform = 20;

		    $infoRequest = self::CheckWapIps($ip);
		    if($_GET["ref"] != "" && $_GET["ref"] != 0){ $_COOKIE["ref"] = $_GET["ref"]; }
    		if($infoRequest != "error"){

    			DB::query("select * from `platform_setting` where `ref_id`='".$_COOKIE['ref']."' and `platform_id`='".$platform."'");
				$RowAutoSell = DB::fetch_array();
				if($RowAutoSell["autosell"] == 1){
					$refferal = 100;
				} else {
					$refferal = $_COOKIE['ref'];
				}
				$platform = 20;
				$enter_url = $url;

				$unic_hash = md5(date("d.m.Y.h.i.s"));
				DB::query("insert into `waperz_abonents` (`hash`,`from`) values ('".$unic_hash."','".$_SERVER["HTTP_REFERER"]."')");
				DB::query("select `id` from `waperz_abonents` where `hash`='".$unic_hash."'");
				$data = DB::fetch_array();

    			DB::query("select `url` from `waperz_setting` where `id`='1'");
				$waperz = DB::fetch_array();
    			if($infoRequest == "megafon"){ 
    				$returnUrl = $waperz["url"].'&subid='.$data["id"].'&salt='.$unic_hash; 

    				DB::query("insert into `wap_transitions` (`ref_id`,`date`,`url`,`platform`,`operator`,`from`) 
    					values ('".$_COOKIE['ref']."','".date("Y-m-d")."','".$enter_url."','".$platform."','MEGAFON','".$_SERVER['HTTP_REFERER']."')");
    			}
				if($infoRequest == "beeline"){ 
					$returnUrl = $waperz["url"].'&subid='.$data["id"].'&salt='.$unic_hash; 
					DB::query("insert into `wap_transitions` (`ref_id`,`date`,`url`,`platform`,`operator`,`from`) 
    					values ('".$_COOKIE['ref']."','".date("Y-m-d")."','".$enter_url."','".$platform."','BEELINE','".$_SERVER['HTTP_REFERER']."')");
				}
				if($infoRequest == "mts"){ 
					$returnUrl = $waperz["url"].'&subid='.$data["id"].'&salt='.$unic_hash;
					DB::query("insert into `wap_transitions` (`ref_id`,`date`,`url`,`platform`,`operator`,`from`) 
    					values ('".$_COOKIE['ref']."','".date("Y-m-d")."','".$enter_url."','".$platform."','MTS','".$_SERVER['HTTP_REFERER']."')");
				}
				if($infoRequest == "tele2"){ 
					$returnUrl = $waperz["url"].'&subid='.$data["id"].'&salt='.$unic_hash;
					DB::query("insert into `wap_transitions` (`ref_id`,`date`,`url`,`platform`,`operator`,`from`) 
    					values ('".$_COOKIE['ref']."','".date("Y-m-d")."','".$enter_url."','".$platform."','TELE2','".$_SERVER['HTTP_REFERER']."')");
				}
				if($infoRequest == ""){ $infoRequest = "error"; $returnUrl = "error";}

				DB::query("update `waperz_abonents` set `ref_id`='".$ref_id."',`origin_ref`='".$refferal."',`movie_id`='".$enter_url."',`operator`='".$infoRequest."',`date`='".date("Y-m-d")."',`plat_id`='".$platform."'
					where `hash`='".$unic_hash."'");
				return $returnUrl;
    		} else {
				return "error";
			}
		}

		/********* WAPEZ ***************/

		/****************** DODOPAY START *******************/
		public function generationSignature($params, $salt){
			uksort($params, 'strcmp');
			$query = str_replace(
				array('+', '%7E'), 
				array('%20', '~'), 
					http_build_query($params, '', '&')
			);

			$parts = array (
				$query
			);
			$parts = array_map('rawurlencode', $parts);
			return md5(implode('&', $parts).$salt);
		}



		public function isWapDodopay($url,$ref_id){
			if (empty($_SERVER["HTTP_X_FORWARDED_FOR"])){ 
		        $ip = $_SERVER["HTTP_X_REAL_IP"];
		    } else {
		    	$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		    }
		    $platform = 20;
		    $infoRequest = self::CheckWapIpsDodopay($ip);
    		if($_GET["ref"] != "" && $_GET["ref"] != 0){ $_COOKIE["ref"] = $_GET["ref"]; }
    		if($infoRequest != "error"){
    			//Проверяем включена ли автоподписка
				DB::query("select * from `platform_setting` where `ref_id`='".$_COOKIE['ref']."' and `platform_id`='".$platform."'");
				$RowAutoSell = DB::fetch_array();
				if($RowAutoSell["autosell"] == 1){
					$refferal = 100;
				} else {
					$refferal = $_COOKIE['ref'];
				}
				$enter_url = $url;
				
				DB::query("select * from `dodopay_setting` where `plat_id`='".$platform."'");
				$params_data = DB::fetch_array();

				$unic_hash = md5(date("d.m.Y.h.i.s"));
				DB::query("insert into `dodopay_abonents` (`hash`,`from`) values ('".$unic_hash."','".$_SERVER["HTTP_REFERER"]."')");
				DB::query("select `id` from `dodopay_abonents` where `hash`='".$unic_hash."'");
				$data = DB::fetch_array();

				if($_SESSION["geolocation"] == "Украина"){
					if(isset($_SESSION["UK_LIFE"])){
						$id = $params_data["UA_L_idland"];
						$streamid = $params_data["UA_L_idpotok"];
						$bubId = $data["id"];
						$salt = $params_data["UA_L_salt"];
						$params = array('streamId' => $streamid,'id' => $id, 'subId' => $bubId);
						$hash = self::generationSignature($params,$salt);
						$params['hash'] = $hash;
						$url = 'http://dodopay.ru/tds/direct?' . http_build_query($params);
						$returnUrl = $url;
					} else {
						$id = $params_data["UA_idland"];
						$streamid = $params_data["UA_idpotok"];
						$bubId = $data["id"];
						$salt = $params_data["UA_salt"];
						$params = array('streamId' => $streamid,'id' => $id, 'subId' => $bubId);
						$hash = self::generationSignature($params,$salt);
						$params['hash'] = $hash;
						$url = 'http://dodopay.ru/tds/direct?' . http_build_query($params);
						$returnUrl = $url;
					}
					DB::query("insert into `wap_transitions` (`ref_id`,`date`,`url`,`platform`,`operator`,`from`) 
    					values ('".$_COOKIE['ref']."','".date("Y-m-d")."','".$enter_url."','".$platform."','WAP','".$_SERVER['HTTP_REFERER']."')");
				}

				if($_SESSION["geolocation"] == "Казахстан"){
					$id = $params_data["KZ_idland"];
					$streamid = $params_data["KZ_idpotok"];
					$bubId = $data["id"];
					$salt = $params_data["KZ_salt"];
					$params = array('streamId' => $streamid,'id' => $id, 'subId' => $bubId);
					$hash = self::generationSignature($params,$salt);
					$params['hash'] = $hash;
					$url = 'http://dodopay.ru/tds/direct?' . http_build_query($params);
					$returnUrl = $url;
					DB::query("insert into `wap_transitions` (`ref_id`,`date`,`url`,`platform`,`operator`,`from`) 
    					values ('".$_COOKIE['ref']."','".date("Y-m-d")."','".$enter_url."','".$platform."','WAP','".$_SERVER['HTTP_REFERER']."')");
				}

				if($_SESSION["geolocation"] == "Беларусь"){
					$id = $params_data["BY_idland"];
					$streamid = $params_data["BY_idpotok"];
					$bubId = $data["id"];
					$salt = $params_data["BY_salt"];
					$params = array('streamId' => $streamid,'id' => $id, 'subId' => $bubId);
					$hash = self::generationSignature($params,$salt);
					$params['hash'] = $hash;
					$url = 'http://dodopay.ru/tds/direct?' . http_build_query($params);
					$returnUrl = $url;
					DB::query("insert into `wap_transitions` (`ref_id`,`date`,`url`,`platform`,`operator`,`from`) 
    					values ('".$_COOKIE['ref']."','".date("Y-m-d")."','".$enter_url."','".$platform."','WAP','".$_SERVER['HTTP_REFERER']."')");
				}

				if($_SESSION["geolocation"] == "Азербайджан"){
					$id = $params_data["AZ_idland"];
					$streamid = $params_data["AZ_idpotok"];
					$bubId = $data["id"];
					$salt = $params_data["AZ_salt"];
					$params = array('streamId' => $streamid,'id' => $id, 'subId' => $bubId);
					$hash = self::generationSignature($params,$salt);
					$params['hash'] = $hash;
					$url = 'http://dodopay.ru/tds/direct?' . http_build_query($params);
					$returnUrl = $url;
					DB::query("insert into `wap_transitions` (`ref_id`,`date`,`url`,`platform`,`operator`,`from`) 
    					values ('".$_COOKIE['ref']."','".date("Y-m-d")."','".$enter_url."','".$platform."','WAP','".$_SERVER['HTTP_REFERER']."')");
				}

				DB::query("update `dodopay_abonents` set `ref_id`='".$ref_id."',`origin_ref`='".$refferal."',`movie_id`='".$enter_url."',`url`='".$url."',`date`='".date("Y-m-d")."',`plat_id`='".$platform."'
					where `hash`='".$unic_hash."'");

				return $returnUrl;
			} else {
				return "error";
			}
		}

		/****************** DODOPAY END *********************/

		/****************** PAYSTREAM START ******************/
		# OPT
		public function isWapAbonentPayStream($url,$ref_id){
			if (empty($_SERVER["HTTP_X_FORWARDED_FOR"])){ 
		        $ip = $_SERVER["HTTP_X_REAL_IP"];
		    } else {
		    	$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		    }
    		$infoRequest = self::CheckWapIps($ip);
    		if($_GET["ref"] != "" && $_GET["ref"] != 0){ $_COOKIE["ref"] = $_GET["ref"]; }
    		if($infoRequest != "error"){
				//Проверяем включена ли автоподписка
				DB::query("select * from `platform_setting` where `ref_id`='".$_COOKIE['ref']."' and `platform_id`='20'");
				$RowAutoSell = DB::fetch_array();
				if($RowAutoSell["autosell"] == 1){
					$refferal = 100;
				} else {
					$refferal = $_COOKIE['ref'];
				}
				$platform = 20;
				$enter_url = $url;
				if($infoRequest == "beeline"){
					#####################BEELINE######################
					/*$key = md5(date("d.m.Y"));
					$verify = md5("1473" . "2501" . $key . 'kino777');
					$href = 'http://partner.vepar.clickmania.org/reg_user/?service_id=2501&partner_id=1473&key='.$key.'&verify='.$verify;
					$cr = curl_init($href);
					curl_setopt($cr,CURLOPT_RETURNTRANSFER,true); 
					$content = curl_exec($cr);
					curl_close($cr);
					$x = new SimpleXMLElement($content);
					$request_id = $x->request_id;
					$returnUrl = $x->url;
					DB::query("insert into `wap_transitions` (`ref_id`,`date`,`url`,`platform`,`operator`,`from`) values ('".$_COOKIE['ref']."','".date("Y-m-d")."','".$enter_url."','".$platform."','BEELINE','".$_SERVER['HTTP_REFERER']."')");
					*/#####################BEELINE######################
					$returnUrl = "error";
				}

				if($infoRequest == "megafon"){
					#####################MEGAFON######################
					$key = md5(date("d.m.Y"));
					$verify = md5("1473" . "2500" . $key . 'kino777');
					$href = 'http://partner.astaroth.paystream.ru/reg_user/?service_id=2500&partner_id=1473&key='.$key.'&verify='.$verify.'&ip='.$ip;
					$cr = curl_init($href);
					curl_setopt($cr,CURLOPT_RETURNTRANSFER,true); 
					$content = curl_exec($cr);
					curl_close($cr);
					$x = new SimpleXMLElement($content);
					$request_id = $x->request_id;
					$returnUrl = $x->url;
					DB::query("insert into `wap_transitions` (`ref_id`,`date`,`url`,`platform`,`operator`,`from`) values ('".$_COOKIE['ref']."','".date("Y-m-d")."','".$enter_url."','".$platform."','MEGAFON','".$_SERVER['HTTP_REFERER']."')");
					#####################MEGAFON######################
				}

				if($infoRequest == "mts"){
					#####################MTS######################
					$key = md5(date("d.m.Y"));
					$verify = md5("1473" . "2502" . $key . 'kino777');
					$href = 'http://partner.azazel.paystream.ru/reg_user/?service_id=2502&partner_id=1473&key='.$key.'&verify='.$verify.'&ip='.$ip.'&return_url=http://videobazza.ru/return.php&ua='.$_SERVER["HTTP_USER_AGENT"];
					$cr = curl_init($href);
					curl_setopt($cr,CURLOPT_RETURNTRANSFER,true); 
					$content = curl_exec($cr);
					curl_close($cr);
					$x = new SimpleXMLElement($content);
					$request_id = $x->request_id;
					$returnUrl = $x->url;
					DB::query("insert into `wap_transitions` (`ref_id`,`date`,`url`,`platform`,`operator`,`from`) values ('".$_COOKIE['ref']."','".date("Y-m-d")."','".$enter_url."','".$platform."','MTS','".$_SERVER['HTTP_REFERER']."')");
					#####################MTS######################
				}

				if($infoRequest == "tele2"){
					#################### TELE2 #######################
					/*$key = md5(date("d.m.Y"));
					$verify = md5("1473" . "2503" . $key . 'kino777');
					$href = 'http://partner.baal.paystream.ru/reg_user/?service_id=2503&partner_id=1473&key='.$key.'&verify='.$verify;
					$cr = curl_init($href);
					curl_setopt($cr,CURLOPT_RETURNTRANSFER,true); 
					$content = curl_exec($cr);
					curl_close($cr);
					$x = new SimpleXMLElement($content);
					$request_id = $x->request_id;
					$returnUrl = $x->url;
					DB::query("insert into `wap_transitions` (`ref_id`,`date`,`url`,`platform`,`operator`,`from`) values ('".$_COOKIE['ref']."','".date("Y-m-d")."','".$enter_url."','".$platform."','TELE2','".$_SERVER['HTTP_REFERER']."')");
					*/#################### TELE2 #######################
					$returnUrl = "error";
				}
				DB::query("insert into `paystream_abonents` (`request_id`,`ref_id`,`origin_ref`,`movie_id`,`date`) 
					values ('".$request_id."','".$ref_id."','".$refferal."','".$enter_url."','".date("Y-m-d")."')");
				return $returnUrl;
			} else {
				return "error";
			}
		}

		#OPT
		public function CheckWapIps($user_ip){
			$operator = "error";
			DB::query("select `range`,`operator` from `paystream_ips` where `range` != ''");
			while($r = DB::fetch_array()){
				$r["range"] = str_replace(" ", "", $r["range"]);
				$ex = explode("-",$r["range"]);
				if(self::checkIP($user_ip, $ex[0], $ex[1])){
					$operator = $r["operator"];
				}
			}
			if($operator == "error"){
				DB::query("select `mask`,`operator` from `paystream_ips` where `mask` != ''");
				while($r = DB::fetch_array()){
					$test_ip=explode(".",$user_ip); 
					$range=$r["mask"];             // Маска подсети 
					$chk=self::range_parser($range); 
					if(self::chk_ips($test_ip,$chk[0],$chk[1]) == 1){
						$operator = $r["operator"];
					}

				}
			}
			return $operator;
		}

		#OPT
		public function CheckWapIpsDodopay($user_ip){
			if(isset($_SESSION["UK_LIFE"])){ unset($_SESSION["UK_LIFE"]); }
			$country = "error";
			DB::query("select `range`,`country`,`is_life` from `dodopay_ips` where `range` != ''");
			while($r = DB::fetch_array()){
				$r["range"] = str_replace(" ", "", $r["range"]);
				$ex = explode("-",$r["range"]);
				if(self::checkIP($user_ip, $ex[0], $ex[1])){
					if($r["is_life"] == 1) { $_SESSION["UK_LIFE"] = "true"; }
					$country = $r["country"];
				}
			}
			if($country == "error"){
				DB::query("select `mask`,`country`,`is_life` from `dodopay_ips` where `mask` != ''");
				while($r = DB::fetch_array()){
					$test_ip=explode(".",$user_ip); 
					$range=$r["mask"];             // Маска подсети 
					$chk=self::range_parser($range); 
					if(self::chk_ips($test_ip,$chk[0],$chk[1]) == 1){
						if($r["is_life"] == 1) { $_SESSION["UK_LIFE"] = "true"; }
						$country = $r["country"];
					}

				}
			}
			return $country;
		}

		#OPT
		public function checkIP ($user_ip, $ip_begin, $ip_end) {
		 	return (ip2long($user_ip)>=ip2long($ip_begin) && ip2long($user_ip)<=ip2long($ip_end));
		}

		#OPT
		public function range_parser($range) { 
			  $range=trim($range); 
			  if (strpos($range,"-")) { 
			    $tmp=explode("-",$range); 
			    $ip_start=explode(".",trim($tmp[0])); 
			    $ip_end=explode(".",trim($tmp[1])); 
			  } 
			  elseif (strpos($range,"/")) { 
			    $tmp=explode("/",$range); 
			    $ip_start=explode(".",$tmp[0]); 
			    $ip_end=self::addip($ip_start,$tmp[1]); 
			  } 
			  else { 
			    $ip_start=$ip_end=explode(".",$range); 
			  } 
			  if (count($ip_start)==4 && count($ip_end)==4) { 
			    return array($ip_start, $ip_end); 
			  } 
			  else { 
			    return false; 
			  } 
		} 

		#OPT
		public function chk_ips($ip,$ip_start,$ip_end) { 
			  for ($i=0; $i<4; $i++) { 
			    if ($ip_start[$i]=='*') { $ip_start[$i]='0'; } 
			    if ($ip_end[$i]=='*') { $ip_end[$i]='255'; } 
			  } 
			  $ip_num=ip2long(join('.',$ip)); 
			  if ($ip_num>=ip2long(join('.',$ip_start))  
			      && $ip_num<=ip2long(join('.',$ip_end))) { 
			    return true; 
			  } 
			  else { 
			    return false; 
			  } 
		} 

		#OPT
		public function addip($ip,$mask) { 
			  $ip_count=Array(32=>0, 31=>1, 30=>3, 29=>7, 28=>15, 27=>31, 26=>63, 
			                25=>127, 24=>255, 23=>511, 22=>1023, 21=>2047, 20=>4095, 
			                19=>8191, 18=>16383, 17=>32767, 16=>65535, 15=>131071, 
			                14=>262143, 13=>524287, 12=>1048575, 11=>2097151, 
			                10=>4194303, 9=>8388607, 8=>16777215, 7=>33554431, 
			                6=>67108863, 5=>134217727, 4=>268435455, 3=>536870911, 
			                2=>1073741823); 
			  $x=Array(); 
			  $ips=$ip_count[$mask]; 
			  $x[0]=$ip[0]+intval($ips/(256*256*256)); 
			  $ips=($ips%(256*256*256)); 
			  $x[1]=$ip[1]+intval($ips/(256*256)); 
			  $ips=($ips%(256*256)); 
			  $x[2]=$ip[2]+intval($ips/(256)); 
			  $ips=($ips%256); 
			  $x[3]=$ip[3]+$ips; 
			  return ($x); 
		} 

		/****************** PAYSTREAM END ******************/


		/****************** XBILL WAP START ******************/
		#OPT
		public function isWapAbonent($url,$ref_id){
			$platformId = "20";
			$refferalToWap = 100;
			if (empty($_SERVER["HTTP_X_FORWARDED_FOR"])){ 
		        $_SERVER["HTTP_X_FORWARDED_FOR"] = $_SERVER["HTTP_X_REAL_IP"];
		    }
		    $platform = $platformId;
			$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
			$service_id = "3";
			$action = "NEW";
			$key = "kinovh7777";
			$hash = md5($key.$action.$service_id);
			$urlsend = "http://sub-bill.ru/http/subscr_pl/wapclick/sub_api.php?service_id=".$service_id."&action=NEW&user_ip=".$ip."&hash=".$hash;
			$cr = curl_init($urlsend);
			curl_setopt($cr,CURLOPT_RETURNTRANSFER,true); 
			$content = curl_exec($cr);
			curl_close($cr);
			$decoding = json_decode($content);
			if($decoding->status == "error"){
				//DB::query("insert into `w_wap_error` (`date`,`ip`,`error`) values ('".date("Y-m-d")."','".$ip."','".$decoding->error."')");
				return "error";
			} else {
				if($decoding->status == "ok"){
					$enter_url = $url;
					$enter_url = $_SESSION["GETTING_L"];
					if($enter_url == "" || $enter_url == 0){
						$enter_url = $_SESSION["CURRLINKS"];
					}
				
					DB::query("select `id` from `entered_numbers` where `ref_id`='".$ref_id."' and `date`='".date("Y-m-d")."' limit 1");
					$count = DB::fetch_array();
					if(!$count){
						DB::query("insert into `entered_numbers` (`date`,`ref_id`,`wap`) values ('".date("Y-m-d")."', '".$ref_id."','1')");
					} else {
						DB::query("update `entered_numbers` set `wap` = `wap` + 1 where `ref_id`='".$ref_id."' and `date`='".date("Y-m-d")."'");
					}

					/*DB::query("select `id` from `entered_numbers` where `ref_id`='100' and `date`='".date("Y-m-d")."' limit 1");
					$count = DB::fetch_array();
					if($count[0] == 0){
						DB::query("insert into `entered_numbers` (`date`,`ref_id`,`wap`) values ('".date("Y-m-d")."', '100','1')");
					} else {
						DB::query("update `entered_numbers` set `wap` = `wap` + 1 where `ref_id`='100' and `date`='".date("Y-m-d")."'");
					}*/
					DB::query("insert into `w_wap_subscribe` (`ref_id`,`date`,`create_id`,`movie_id`,`server`,`plat`, `orig_ref`) values
							('".$refferalToWap."','".date("Y-m-d")."','".$decoding->create_id."','".$enter_url."','".$_SERVER["SERVER_NAME"]."','".$platform."','".$ref_id."')");

					return  $decoding->url;
				}
			}
		}

		/****************** XBILL WAP END ******************/


		/***************** WEB FUNCTIONS START *******************/
		#OPT
		public function setTitle($url){
			$ids_go = explode("-",$url);
			DB::query("SELECT * FROM `serial_movie` JOIN `serial_item` ON `serial_item`.`movie_id` = `serial_movie`.`id` WHERE `serial_item`.`id`='".$ids_go[0]."'");
            $film = DB::fetch_array();
			return $film;
		}

		#OPT
		public function GetBackground(){
        	DB::query("select `background` from `serial_comments_setting` where `id`='1'");
        	$r = DB::fetch_array();
        	if($r[0] != ""){
        		return 'style="background-image: url(http://images2.pw/bg/'.$r[0].');background-size: cover;background-position: 50% 0;background-attachment: fixed;background-repeat: no-repeat"';
        	} else {
        		return 'noimage';
        	}
        } 

        #OPT
        public function getCategories(){
            DB::query("SELECT `link`,`name` FROM `serial_category` where (`link` != 'news' and `link` != 'real_tv' and `link` != 'sprting' and `link` != 'tok_show') order by `id` desc");$rows = array();
            while($row = DB::fetch_array()){
				$rows[] = $row;
            }
            return $rows;
        }

        #OPT
        public function newPopularSerial($id){
			DB::query("SELECT * FROM `serial_recomended` where `plat_id`='1'");
        	$row = DB::fetch_array();

        	/*DB::query("SELECT `serial_movie`.`id`,`serial_item`.`poster_url`,`serial_movie`.`link` FROM `serial_movie` JOIN 
        	`serial_item` ON `serial_item`.`movie_id` = `serial_movie`.`id` 
        	WHERE `serial_movie`.`id`='".$row["poster1"]."' or `serial_movie`.`id`='".$row["poster2"]."' 
        	or `serial_movie`.`id`='".$row["poster3"]."' or `serial_movie`.`id`='".$row["poster4"]."' 
        	or `serial_movie`.`id`='".$row["poster5"]."' or `serial_movie`.`id`='".$row["poster6"]."' order by `serial_item`.`id` desc");*/

        	DB::query("SELECT `id`,`link` FROM `serial_movie` where `id`='".$row["poster1"]."' or `id`='".$row["poster2"]."' or `id`='".$row["poster3"]."'
        		or `id`='".$row["poster4"]."' or `id`='".$row["poster5"]."' or `id`='".$row["poster6"]."' or `id`='".$row["poster7"]."' or `id`='".$row["poster8"]."' limit 8");
        	while($posters = DB::fetch_array()){
        		$rowses[] = $posters;
        	}
        	return $rowses;
		}

		#OPT
		public function ShowMessageModal(){
        	$account = self::InitMessageModal();
        	if($account["qty_sms"] == "0"){
				echo '<b>Идет обработка запроса. Подождите 7-10 минут затем обновите текущую страницу</b>';

			}
			if($account["qty_sms"] == "1"){
				echo "Доступ к 20 видео. <a href='#PluginRegistration' class='modal popup initializeaudio'>Отправте еще смс</a> чтобы получить доступ к половине видеоконтента. ";
				echo 'Фильмы помеченные как <img src="/templates/old/images/lock.png" /> - закрыты для Вашего просмотра, <img src="/templates/old/images/Unlock.png" /> - доступны для Вашего просмотра. ';
				echo 'Перйти к <a href="/access/">Списку доступных</a> фильмов по Вашему аккаунту';
			}
			if($account["qty_sms"] == "2"){
				echo "Доступ к половине видео. Что бы получить доступ ко всему видеоконтенту <a href='#PluginRegistration' class='modal popup initializeaudio'>повторите регистрацию</a>";
				echo 'Фильмы помеченные как <img src="/templates/old/images/lock.png" /> - закрыты для Вашего просмотра, <img src="/templates/old/images/Unlock.png" /> - доступны для Вашего просмотра';
				echo '<b>Перйти к <a href="/access/">Списку доступных</a> фильмов по Вашему аккаунту</b>';
			}
			if($account["qty_sms"] >= "3"){
				echo "Доступ ко всему видеоконтенту. <a href='javascript:logout();' >Выход</a>";
			}
		}

		#OPT
		public function InitMessageModal(){
        	DB::query("select `qty_sms` from `xbill_abonents` where `id`='".$_SESSION['mobile_id']."'");
        	$rows = DB::fetch_array();
        	return $rows;
        }

        #OPT
        public function FooterTexts(){
			echo '
			<ul>
              <li>+18 </li>
              <li> <a href="/">Главная </a></li>
              <li> <a href="/info/read/stoimost_podpiski.html">Стоимость услуги </a></li>
              <li> <a href="/info/read/pravila-predostavlenii-podpiski.html">Правила предоставления Подписки на Контент </a></li>
              <li> <a href="/info/read/upravlenie_podpiskoi.html">Управление Подпиской на Контент </a></li>
            </ul>
            
            <div class="text-info">'.file_get_contents('http://api.platinote.biz/offero_footer/35033dd868be4cb8848184b873971324/hl_30').'</div>
			';
        }

        #OPT
        public function ListMainPosters(){
			$limitCount = 12;
			# ДЖОИН ЗАПРОС ДОЛЬШЕ В ОТРАБОТКЕ ПОЧЕМУТО
			//DB::query("SELECT * FROM `serial_movie` JOIN `serial_item` ON `serial_item`.`movie_id` = `serial_movie`.`id` GROUP BY `serial_item`.`movie_id` ORDER BY `serial_movie`.`weight` ASC LIMIT $limitCount");
            DB::query("SELECT `id`,`link`,`name` FROM `serial_movie` ORDER BY `serial_movie`.`weight` ASC LIMIT $limitCount");
            $articles = array();
            while($row = DB::fetch_array()){
				$articles[] = $row;
            }
            return $articles;
        }

        #OPT
		public function LastSeasonSeries($movie_id){
			# ДЖОИН ЗАПРОС ДОЛЬШЕ В ОТРАБОТКЕ ПОЧЕМУТО
			/*DB::query("select `id` from `serial_item` where `movie_id`='$movie_id' and `show` != 1 order by `id` desc limit 1");
			$r = DB::fetch_array();
			return $r[0];*/
			# ПРОСТЫМ ЗАПРОСОМ
			DB::query("select `id`,`poster_url`,`description` from `serial_item` where `movie_id`='$movie_id' and `show` != 1 order by `id` desc limit 1");
			$rData = DB::fetch_array();
			return $rData;
		}

        #OPT
        public function GetMainPosters(){
			DB::query("select `pos1`,`pos2`,`pos3`,`pos4`,`pos5`,`pos6`,`pos7`,`pos8` from `setting_mainpage` where `is_active` = '1' and `plat`='20' and `ref_id`='".$_COOKIE["ref"]."'");
			$row = DB::fetch_array();
			if($row){
				$string = "";
				$string .= " or `serial_movie`.`id` = '".$row["pos1"]."' ";
				$string .= " or `serial_movie`.`id` = '".$row["pos2"]."' ";
				$string .= " or `serial_movie`.`id` = '".$row["pos3"]."' ";
				$string .= " or `serial_movie`.`id` = '".$row["pos4"]."' ";
				$string .= " or `serial_movie`.`id` = '".$row["pos5"]."' ";
				$string .= " or `serial_movie`.`id` = '".$row["pos6"]."' ";
				$string .= " or `serial_movie`.`id` = '".$row["pos7"]."' ";
				$string .= " or `serial_movie`.`id` = '".$row["pos8"]."' ";

				# ДОЛГИЙ ДЖОЙИН
				//DB::query("SELECT * FROM `serial_movie` JOIN `serial_item` ON `serial_item`.`movie_id` = `serial_movie`.`id` WHERE ( `serial_movie`.`id` = '000000' ".$string.") GROUP BY `serial_item`.`movie_id`");
            	
            	DB::query("SELECT `id`,`link`,`name` FROM `serial_movie` WHERE ( `serial_movie`.`id` = '000000' ".$string.") ORDER BY `weight` ASC LIMIT 8");
            	while($rows = DB::fetch_array()){
            		$data[] = $rows;
            	}
            	return $data;
			} else {
				return false;
			}
		}

		#OPT
		public function ListMainPostersAccess(){
			DB::query("select * from `xbill_abonents` where `id`='".$_SESSION['mobile_id']."'");
			$account = DB::fetch_array();
											
			DB::query("select count(1) from `movies`");
			$count_movies = DB::fetch_array();
			if($account["qty_sms"] == "0"){
				$limitCount = "0";
			}
			if($account["qty_sms"] == "1"){
				$limitCount = "23";
			}
			if($account["qty_sms"] == "2"){
				$limitCountExp = $count_movies[0] / 2;
				$limitCountTurn = explode(".", $limitCountExp);
				$limitCount = $limitCountTurn[0] - 4;	
			}
			if($account["qty_sms"] >= "3"){
				$limitCount = $count_movies[0] - 4;
			}
			
			DB::query("SELECT `id`,`link`,`name` FROM `serial_movie` ORDER BY `serial_movie`.`weight` ASC LIMIT $limitCount");
            $articles = array();
            while($row = DB::fetch_array()){
				$articles[] = $row;
            }
            return $articles;
        }

        #OPT
        public function NameOperator(){
			DB::query("select * from `support_operator` order by rand() limit 1");
			$row = DB::fetch_array();
			return $row["name"];
		}

		#OPT
		public function getStatusServer(){
	        DB::query("select `weight` from `settings` where `id`='16'");
	        $row = DB::fetch_array();
	        return $row[0]; 
	    }

	    # OPTIMIZED
		public function LikeRequest(){
			if (!mb_detect_encoding($_REQUEST['like'], array('UTF-8', 'Windows-1251'))=='UTF-8'){
				$request = iconv('UTF-8', 'windows-1251', $_REQUEST["like"]);
			}else {
				$request = $_REQUEST["like"];
			}
			DB::query("SELECT  `id`,`link`,`name` FROM `serial_movie` WHERE `serial_movie`.`name` like '%".$request."%'");
            $row = DB::fetch_array();
            return $row;
		}

	    #OPT
	    public function GetAccess(){
        	/*		 
			DB::query("select * from `xbill_abonents` where `id`='".$_SESSION['mobile_id']."'");
			$account=DB::fetch_array();
								
			DB::query("select count(1) from `serial_movie`");
			$count_movies = DB::fetch_array();

			# Проверка количества смс у пользователя вывод нужного сообщения
			if($account["qty_sms"] == "0"){
				//echo '<b>Идет обработка запроса. Подождите 7-10 минут затем обновите текущую страницу</b>';
				$limitCount = "0";
			}
			# Проверка количества смс у пользователя вывод нужного сообщения
			if($account["qty_sms"] == "1"){
				//echo "К 20 последним видео. Что бы получить доступ к половине видеоконтенту <a href='#modtest' class='modal popup initializeaudio'>повторите регистрацию</a>";
				//echo '<p style="color: white;"><b>Перйти к <a href="/access/">Списку доступных</a> фильмов по Вашему аккаунту</b></p>';
				$limitCount = "23";
			}
			# Проверка количества смс у пользователя вывод нужного сообщения
			if($account["qty_sms"] == "2"){
				//echo "К половине видео. Что бы получить доступ ко всему видеоконтенту <a href='#modtest' class='modal popup initializeaudio'>повторите регистрацию</a>";
				//echo '<p style="color: white;"><b>Перйти к <a href="/access/">Списку доступных</a> фильмов по Вашему аккаунту</b></p>';
									
				$limitCountExp = $count_movies[0] / 2;
				$limitCountTurn = explode(".", $limitCountExp);
				$limitCount = $limitCountTurn[0];					
			}
			# Проверка количества смс у пользователя вывод нужного сообщения
			if($account["qty_sms"] >= "3"){
				//echo "Ко всему видеоконтенту";
				$limitCount = $count_movies[0];
			}	
			return $limitCount;			
			*/
        }



























		

		

		public function cidr_2_mask($mask){
		    return long2ip(pow(2,32) - pow(2, (32-$mask)));
		}

		public function mask_2_cidr($mask){
		    $a=strpos(decbin(ip2long($mask)),"0");
		    if (!$a){$a=32;}
		    return $a;
		}

		public function ipCIDRcheck($ip, $cidr) {
		  list($net, $mask) = explode('/', $cidr);
		  return ( ip2long($ip) & (-1<<(32-$mask)) ) == ip2long($net);
		}

		

		

		/****/

		
	
		#OPTIMIZED
		public function LimitLinks(){
            //DB::query("SELECT * FROM `serial_category` limit ".$start.",".$end);
            DB::query("SELECT * FROM `serial_category`");
            $rows = array();
            while($row = DB::fetch_array()){
				$rows[] = $row;
            }
            return $rows;
        }

        public function FoundParking($name, $plat){
        	//echo "select `ref_id` from `parking_domains` where `plat_id`='".$plat."' and `domain`='".$name."'";
			DB::query("select `ref_id` from `parking_domains` where `plat_id`='".$plat."' and `domain`='".$name."'");
			$row = DB::fetch_array();
			return $row;
		}

        public function LimitCat($start,$end){
            DB::query("SELECT * FROM `serial_category` limit ".$start.",".$end);
            $rows = array();
            while($row = DB::fetch_array()){
				$rows[] = $row;
            }
            return $rows;
        }

        public function GetTrailerLink(){
        	DB::query("select * from `serial_comments_setting` where `id`='1'");
	        $r = DB::fetch_array();
	        return $r["new_link"];
        }

        # OPTIMIZED
        

        

        
        
		public function cidr_conv($cidr_address) {
		  $first = substr($cidr_address, 0, strpos($cidr_address, "/"));
		  $netmask = substr(strstr($cidr_address, "/"), 1);
		  $first_bin = str_pad(decbin(ip2long($first)), 32, "0", STR_PAD_LEFT);
		  $netmask_bin = str_pad(str_repeat("1", (integer)$netmask), 32, "0", STR_PAD_RIGHT);
		  for ($i = 0; $i < 32; $i++) {
		    if ($netmask_bin[$i] == "1") 
		      @$last_bin .= $first_bin[$i];
		    else
		      $last_bin .= "1";
		  }
		  $last = long2ip(bindec($last_bin));
		  return "$first-$last";
		}

        public function IsInArray($array , $findme){
            foreach ($array as $key => $value){
                if (strpos(strtolower($value), strtolower($findme)) !== false )
                return $key;
            }
            return false;
        }
        
        public function getCategoryLink($id){
			DB::query("Select * from `category_films` where `id` = '".$id."' ");
			$stills = DB::fetch_array();
			return $stills["link"];
		}	
		
        public function setDescription(){
            $title = 'Description pages';
            return $title;
        }
        
        public function InitController($controller){
            $controller = trim($controller, "/");
            $controller = explode("/", $controller);
            if($controller[0] == "serial"){
            	$controller[0] = "films";
            }
            $CheckExist =  'controllers/'.$controller[0].'.controller.php';
            if (file_exists($CheckExist)) {
                $pathController =  $CheckExist;
                include $pathController;
            } else {
                include 'templates/404.phtml';
            }
        }


        

		


		
		public function ListMainBackground(){
			//DB::query("SELECT * FROM `movies` ORDER BY `id` DESC LIMIT 8");
			DB::query("SELECT * FROM `movies` ORDER BY `weight` ASC LIMIT 8");
            $row = DB::fetch_array();
			return $row;
		}
		


		public function ListMainPostersPopular(){
			DB::query("SELECT * FROM `serial_item` ORDER BY `id` DESC LIMIT 8");
			 $articles = array();
            while($row = DB::fetch_array()){
				$articles[] = $row;
            }
            return $articles;
		}


		

		#OPTIMIZED
		
		

		


		public function GettingLinks($id){
			DB::query("select * from `serial_movie` where `id`='$id'");
			$r = DB::fetch_array();
			return $r;
		}


		# OPTIMIZED
        

        public function genNameSerialLink($id){
        	DB::query("SELECT `link` FROM `serial_movie` where `id`='$id'");
        	$r = DB::fetch_array();
        	return $r[0];
        }
        public function genNameSerial($id){
        	DB::query("SELECT `name` FROM `serial_movie` where `id`='$id'");
        	$r = DB::fetch_array();
        	return $r[0];
        }
		
		
		
        
		
		// OPTIMIZED
		
		
		public function getBillingPathSetting(){
			DB::query("select * from `billing_type` where `status`='1' and `name`='pseudo' and `payer_id`='1'");
			$rows = DB::fetch_array();
			return $rows["prefix_path"];
		}

		public function TrimText($text, $max_text){ 
		  $text = str_replace("  ", " ", $text); 
		  $string = explode(" ", $text); 
		  $count = 100;
		  $trimed = "";
		  for ($wordCounter = 0; $wordCounter <= $count;$wordCounter++) { 
		  $trimed .= $string[$wordCounter]; 
		  if(strlen($trimed) >= $max_text) break;
		    if($wordCounter < $count){
		      $trimed .= " ";
		    } 
		  }
		  $trimed = trim($trimed);
		  return $trimed; 
		} 

		
}
?>