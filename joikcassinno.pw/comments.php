<?php

/*
	This is Ajax Comments BOT 
	Development OnLineHack 
*/

session_start(); 

// set header charset
header("Content-Type: text/html; charset=utf8");

// connect connection settings
require_once("core/Connection.php");
$DB = new DB;

require_once("controllers/main.controller.php");
$Main = new Main;

//function parsing user comment for answer
function testWord($str, $keys_list) {
		foreach ($keys_list as $key){
			if(strpos($str ,$key['keyword'])!==false) {
				if($key['subid']>0)
					return $key['subid'];
				else
					return $key['id'];
			}
		}
	return 2;
}

// parsing comment
function parseComment($txt, $movie){
		return str_replace(
			array(
				'%filmname%',
			),
			array(
				$movie['title'],
			),
			$txt
		);
	}

switch($_GET['action']){
	case 'load-comment':
		$DB->query("select * from `serial_item` where `id` = '".$_GET['movieid']."'");
		$film = $DB->fetch_array();
		$question = 0;
		if($_POST['text'] ) {
			$text_in = strip_tags($_POST['text']);
			$text = strip_tags($_POST['text']);
			$text = str_replace("<", '', $text);
			$text = str_replace(">", '', $text);
			$text = str_replace("'", '', $text);
			$text = str_replace("\"", '', $text);
			$text = str_replace("&", '', $text);
			
			function GetRealIp() {
				 if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				   	$ip = $_SERVER['HTTP_CLIENT_IP'];
				 } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				 	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				 } else {
				   	$ip=$_SERVER['REMOTE_ADDR'];
				 }
				 return $ip;
			}
			$ip = GetRealIp();
			
			$query = "Insert into `serial_comments_users` set `comment` = '".$text."', `ip` = '".$ip."', `date` = '".time()."'";
			$DB ->query($query);
			
			$text = str_replace(".", '', $text);
			$text = str_replace(",", '', $text);
			$text = str_replace("?", '', $text);
			$text = str_replace("!", '', $text);
			
			$text_explode = explode(" ", $text);
			
			$DB->query('Select * from serial_keywords');
			while($keys = $DB->fetch_array()){
				$keys_list[] = $keys["keyword"];
			}
			$where = '';
			$question = array();
			foreach($text_explode as $keyword_ids){
				$keyword_ids = mb_strtolower($keyword_ids, 'UTF-8');
				if(in_array($keyword_ids, $keys_list)){
					$DB->query("select * from `serial_keywords` where `keyword`='$keyword_ids'");
					$keywordRows = $DB->fetch_array();
					$question[] = $keywordRows["id"];
					if(isset($_SESSION['count_key'])) {
						$countsArray = array_count_values($_SESSION['count_key']);
						//print_r($countsArray);
						$insertSession = $_SESSION['count_key'];
						$insertSession[] = $keywordRows["id"];
						if($countsArray[$keywordRows["id"]] >= 4){
							$keyMassive = array_search($keywordRows["id"], $question);
							if ($keyMassive !== false){
								unset($question[$keyMassive]);
							}
						}
					}else{
						$countsArray = 0;
						$insertSession = array($keywordRows["id"]);
					}
					$_SESSION['count_key'] = $insertSession;
				}
			}
			//print_r($question);
			if(count($question) != 0){
				$j = implode(',',$question);
				//print_r($j);
				$isession = '';
				if(isset($_SESSION['myans'])){
					$keyfordel = array_search('',$_SESSION['myans']);
					if ($keyfordel !== false){
						 unset($_SESSION['myans'][$keyfordel]);
					}
					$k = implode(',',$_SESSION['myans']);
					if($k != ""){
						$isession = "and NOT ( `id` IN (".$k."))";
					}
				}
				$where .= "(`keyword_id` IN (".$j.")) ";
				//echo "Select * from `comments_answers` where ". $where . " ".$isession." group by `keyword_id` order by RAND() limit 1";
				$DB -> query("Select * from `serial_comments_answers` where ". $where . " ".$isession." group by `keyword_id` order by RAND()");
				while($c01 = $DB -> fetch_array()){
					$c0[] = $c01;
					if(isset($_SESSION['myans'])) {
						$set = $_SESSION['myans'];
						$set[] = $c01["id"];
					}else{
						$set = array($c01["id"]);
					}
					$_SESSION['myans'] = $set;
				}
			} else {
				$question = 1;
				$issession = '';
				if(isset($_SESSION['myans_random'])){
					$keyfordel = array_search('',$_SESSION['myans_random']);
					if ($keyfordel !== false){
						 unset($_SESSION['myans_random'][$keyfordel]);
					}
					$isession = "where";
					$k = implode(',',$_SESSION['myans_random']);
					if($k != ""){
						$isession .= " NOT ( `id` IN (".$k."))";
					}
				}
				$DB -> query("Select * from `serial_comments_random` ".$isession."  order by RAND()");
				while($c01 = $DB -> fetch_array()){
					$c0[] = $c01;
					if(isset($_SESSION['myans_random'])) {
						$set = $_SESSION['myans_random'];
						$set[] = $c01["id"];
					}else{
						$set = array($c01["id"]);
					}
					$_SESSION['myans_random'] = $set;
				}
				
			}	
			//sleep(20);
			//print_r($_SESSION);
			//print_r($_SESSION["count_key"]);
			//unset($_SESSION['count_key']);
			//unset($_SESSION['myans']);
			//unset($_SESSION['myans_random']);
		}	
			
		if(isset($_GET['unreg'])){
			$row = array(
				'comment' =>  $text_in,
				'author' => 'Незарегистрированный пользователь',
				'avatar_url' => '/templates/images/NoAvatar.png',
			);
		}else{
			$DB->query("select count(*) from `serial_comments` where `movie_id` in ('0','".$_GET['movieid']."')");
			$count = $DB -> fetch_array();
			$comments_num = $count[0];
			
			$new_val = ($_SESSION["commentaries"] + 1) % $comments_num;
			$_SESSION["commentaries"] = $new_val;
			
			if($_SESSION["check_id_film"] == $_GET["movieid"]){
				$ctf = ($_SESSION["commentaries"] + 9) % $comments_num;
			} else {
				$ctf = ($_SESSION["commentaries"] + 29) % $comments_num;
				unset($_SESSION["check_id_film"]);
			}
			
			if($ctf + 1 > $comments_num){
				$ctf = 10;
			}
			
			$DB->query("select * from `serial_comments` where `movie_id` in ('0','".$_GET['movieid']."') order by `id` asc limit ".$ctf.", 1");
			$row = $DB -> fetch_array();

		}
			
		
        
			if($question) {
				$Main->SaveArrayCommentsAnswer($_GET["ucomment"],$_GET["session"],$_GET["film_id"],'question','0');
				//$DB->query("select * from `comments_setting` where `id` ='1'");
				//$enable_answers = $DB->fetch_array();
				//if($enable_answers["auto_answers"] == 1){
				//sleep(20);
					$i=1;
						foreach ($c0 as $answer){
							if($answer["user_id"] == 0){
								$param = 2;
							} else {
								$param = 1;
							}
							
							$Main->SaveArrayCommentsAnswer($answer["id"],$_GET["session"],$_GET["film_id"],'answer',$param);


							/*echo '
							<div class="userComment">';
							if($answer["user_id"] == 0){
									echo '<img src="/templates/images/NoAvatar.png" />';
								} else {
									$getUser = mysql_query("select * from `serial_comments` where `id`='".$answer["user_id"]."'");
									$user = mysql_fetch_array($getUser);
									echo '<img src="http://imagess.pw/i1' . $user['avatar_url'] . '" alt="' . htmlspecialchars($user['author']) . '" />';
								}
							echo '
			                        <div class="userText">
			                          <div class="userName">';
			                          if($answer["user_id"] == 0){
									echo 'Незарегистрированный пользователь';
								} else {
									echo  htmlspecialchars($user['author']);
								}
			                          echo '</div>
			                          <div class="userDate">' . date('H:i:s',mktime(date("H"), date("i"),date("s")+mt_rand(5*$i,10*$i), date("m"), date("d"),   date("Y"))) . '</div>
			                        	<div class="userText">';
			                        	$DB->query("select * from `serial_item` where `id`='".$_GET['movieid']."'");
									$row_film_1 = $DB->fetch_array();
									//nl2br($answer['comment'])
									$comment_film_1 = str_replace("%FILM_ID%", $row_film_1["title"], $answer['comment']);
									
									
									echo '<pre class="typing_mess" style="padding: 5px;">';
									/*if($answer["user_id"] == 0){
										echo '<b>Незарегистрированный пользователь</b>';
									} else {
										echo '<b>' . htmlspecialchars($user['author']) . '</b>';
									}*/
									/*echo 'печатает Вам сообщение <img src="/templates/images/typing.gif" style="width:14px"/>';
									echo '</pre>';

									echo '<b class="typing_mess_view" style="display:none;font-weight: normal;">';
									echo nl2br($comment_film_1);
			                        	echo '</div>
			                       	</div>
		                      </div>
							';*/
							echo '
							<div class="message-line comment" style="border-bottom:1px solid #212121">
					            <div class="line-one">
					                <div class="time">('.date('H:i:s',mktime(date("H"), date("i"),date("s")+mt_rand(5*$i,10*$i), date("m"), date("d"),   date("Y"))).')</div>';
					                if($answer["user_id"] == 0){
											
									} else {
										$getUser = mysql_query("select * from `comments` where `id`='".$answer["user_id"]."'");
										$user = mysql_fetch_array($getUser);
									}

					                if($answer["user_id"] == 0){
										$autor = 'Незарегистрированный пользователь';
									} else {
										$autor = htmlspecialchars($user['author']);
									}

									$DB->query("select * from `movies` where `id`='".$_GET['movieid']."'");
									$row_film_1 = $DB->fetch_array();
									$comment_film_1 = str_replace("%FILM_ID%", $row_film_1["title"], $answer['comment']);

					                echo '<div class="name">' . $autor . ':</div></div><div class="line-two">';

					                $DB->query("select * from `movies` where `id`='".$_GET['movieid']."'");
									$row_film = $DB->fetch_array();
									$comment_film = str_replace("%FILM_ID%", $row_film["title"], $row['comment']);

									echo '<i class="typing_mess" style="color:green">';
									echo '<br /> печатает Вам сообщение <img src="/templates/old/images/typing.gif" style="width:17px; height: 16px;"/></i>';
					                echo '<div class="messag typing_mess_view" style="display:none;"> ' . nl2br($comment_film_1) .'</div></div></div>';

								
						$i++;
						}
			//}
		} else {

			/*echo '

			<div class="userComment">';
			if(isset($_GET['unreg'])){
				echo '<img src="' . $row['avatar_url'] . '" alt="' . htmlspecialchars($row['author']) . '"> ';
			} else {
				echo '<img src="http://imagess.pw/i1' . $row['avatar_url'] . '" alt="' . htmlspecialchars($row['author']) . '"> ';
			}

			echo '
					                        <div class="userText">
					                          <div class="userName">' . htmlspecialchars($row['author']) . '</div>
					                          <div class="userDate">' . date('H:i:s') . '</div>
					                          <div class="userText">';
					                          $DB->query("select * from `serial_item` where `id`='".$_GET['movieid']."'");
			$row_film = $DB->fetch_array();
			$comment_film = str_replace("%FILM_ID%", $row_film["title"], $row['comment']);
			echo nl2br(htmlspecialchars(parseComment($comment_film, $film)));
			echo '</div>
					                        </div>
					                    </div>
			';*/
			echo '
			<div class="message-line comment" style="border-bottom:1px solid #212121">
	                <div class="line-one">
	                  <div class="time">(' . date('H:i:s') . ')</div>
	                  <div class="name">' . htmlspecialchars($row['author']) . ':</div>
	                </div>
	                <div class="line-two">';

	                $DB->query("select * from `movies` where `id`='".$_GET['movieid']."'");
					$row_film = $DB->fetch_array();
					$comment_film = str_replace("%FILM_ID%", $row_film["title"], $row['comment']);

	                echo '<div class="messag"> ' . nl2br(htmlspecialchars(parseComment($comment_film, $film))) .'</div>
	                </div>
	            </div>
			';




			/*echo '<li class="commenti">' . "\n";                
			if(isset($_GET['unreg'])){
				echo '<img class="commenti__avatar" src="' . $row['avatar_url'] . '" alt="' . htmlspecialchars($row['author']) . '"> ';
			} else {
				echo '<img class="commenti__avatar" src="http://imagess.pw/i1' . $row['avatar_url'] . '" alt="' . htmlspecialchars($row['author']) . '"> ';
			}
			echo '<p class="commenti__author" itemprop="author"><b>' . htmlspecialchars($row['author']) . '</b></p>' . "\n";
			$DB->query("select * from `serial_item` where `id`='".$_GET['movieid']."'");
			$row_film = $DB->fetch_array();
			$comment_film = str_replace("%FILM_ID%", $row_film["title"], $row['comment']);
			echo '<time class="commenti__date" itemprop="datePublished">' . date('H:i:s') . '</time> ';
			echo '<div class="commenti__message" itemprop="reviewBody">' . nl2br(htmlspecialchars(parseComment($comment_film, $film))) . '</div>';
			echo '</li>';*/

			$Main->SaveArrayCommentsAnswer($row['id'],$_GET["session"],$_GET["movieid"],'additional','0');
		}
	break;
	
	case 'send-sms':
		if(isset($_POST['phone'])) {
			echo 'ok';
		} else {
			echo 'wrong number';
		}
	break;
}

?>