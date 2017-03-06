<?php
session_start();

require_once("core/Connection.php");
$DB = new DB;

require_once("core/Application.php");
$Core = new Core;

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
			$_SESSION["answered"] = $dataArray;

		}
	}

}



echo $number;
?>
