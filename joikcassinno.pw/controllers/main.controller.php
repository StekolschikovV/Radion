<?php

/*
	This is Main controller 
	Default public functions for page
	Viewing video content. Development OnLineHack 
*/

class Main extends DB{
	
	/************* END INDEX ****************/

	# OPT
	public function GetRealIp(){
		// Initialize real IP address user guest
		 if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
		   	$ip = $_SERVER['HTTP_X_REAL_IP'];
		 } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		 	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		 } 
		 return $ip;
	}
	
	# OPT
	public function UniqueIp($ip, $date, $cookie) {
		//DB::query("delete from `ip` where `date` != '".date("Y-m-d")."'");
		DB::query("Select `ip` from `ip` where `ip` = '$ip' and `date`='$date' and `refferal`='$cookie' limit 1");
		$count = DB::fetch_array();
		if(!$count){
			DB::query("insert into `ip` (`ip`, `date`, `refferal`) values ('$ip', '$date' , '$cookie')");
			return 1;
		} else {
			return 0;
		}
	}

	#OPT
	public function checkConnectCountry($code) {
		$countries = array('RU', 'UA', 'KZ', 'KG', 'TJ', 'AM', 'BY', 'AZ', 'GE', 'MD');
		foreach ($countries as $country) {
			if ($code == $country) {return $country;}
		}
		return 'other';
	}

	#OPT
	public function RefferalLink($today,$link,$ck){
        DB::query("Select `id` from `referer` where `date` = '$today' AND `ref_id` = '$ck' AND `link` = '$link'");
        $row = DB::fetch_array();
        if ($row) {
            DB::query("Update `referer` set `counter` = `counter` + 1 where `id` = '".$row["id"]."'");
        } else {
			DB::query("Insert into `referer` set `date` = CURDATE(), `ref_id` = '$ck', `counter` = '1', `link` = '". $link . "'");
        }
	}

	#OPT
	public function InitCountry(){
		$connected_country = self::checkConnectCountry($_SESSION["COUNTRU_CODE"]);
		return $connected_country;
	}

	#OPT
	public function RefferalFunction($date, $unic, $from, $ref_id){
		DB::query("Select `id` from `transitions` where `date` = '$date' AND `ref_id` = '$ref_id' limit 1");
		$count = DB::fetch_array();
		if ($count) {
			if ($unic == 1) {
				DB::query("update `transitions` set `counter` = counter + 1, `$from` = `$from` + 1, `unique` = `unique` + 1 where `ref_id` = '$ref_id' AND `date` = CURDATE()");
			} else {
				DB::query("Update `transitions` set `counter` = `counter` + 1,  `$from` = `$from` + 1 where `ref_id` = '$ref_id' AND `date` = CURDATE()");
			}
		} else {
			if ($unic == 1) {
				DB::query("Insert into `transitions` set `date` = CURDATE(), `ref_id` = '$ref_id', `counter` = '1', `unique` = '1', `$from` = '1'");
			} else {
				DB::query("Insert into `transitions` set `date` = CURDATE(), `ref_id` = '$ref_id', `counter` = '1', `$from` = '1'");
			}
		}
	}

	/************* END INDEX ****************/

	/************* PAGES FUNCTION ***********/
	#OPT
	public function getSerialMovies($id,$bgr){
		if($bgr !=""){
			return 'style="background-image: url(http://imagees2.pw/i2/'.$id.'/'.$bgr.');background-size: cover;background-position: 50% 0;background-attachment: fixed;background-repeat: no-repeat"';
			//return 'http://images1.pw/i2/'.$id.'/'.$bgr;	
		} else {
			return 'noimage';
		}
    }
	
    #OPT >>> NEED TO CHECK
    public function getMovieFrames($id){
		$frames=array();
		DB::query("Select * from `serial_stills` where `movie_id` = '".$id."' and `active` = '1' order by `weight`");
		while($stills = DB::fetch_array()){
			$frames[] = $stills;
		}
		return $frames;
	}

	#OPT >>> NEED TO CHECK
	public function getDuration($film_duration){
		// Duration video file for player
		$film_duration = trim($film_duration);
		if(preg_match('/^\d\d:\d\d:\d\d$/', $film_duration)){
			$movie_duration = ((int)$film_duration[0] * 10 + (int)$film_duration) * 3600;
			$movie_duration += ((int)$film_duration[3] * 10 + (int)$film_duration[4]) * 60;
			$movie_duration += ((int)$film_duration[6] * 10 + (int)$film_duration[7]);
		}
		return $movie_duration;
	}



















	
	function ip2int($ip){
		$part = explode(".", $ip);
		$int = 0;
		if (count($part) == 4) {
			$int = $part[3] + 256 * ($part[2] + 256 * ($part[1] + 256 * $part[0]));
		}
		return $int;
	}
	
	




	public function getPlatformSetting($plat_id, $ref_id){
		DB::query("SELECT * FROM `platform_setting` WHERE `ref_id` = '". $ref_id."' and `platform_id`='".$plat_id."'");
		$row = DB::fetch_array();
		return $row["setting"];
	}
	
	public function getPlatformSettingCount($plat_id, $ref_id){
		DB::query("SELECT count(1) FROM `platform_setting` WHERE `ref_id` = '". $ref_id."' and `platform_id`='".$plat_id."'");
		$count = DB::fetch_array();
		return $count[0];
	}
	
	public function getSettingsComments(){
		DB::query("select * from `comments_setting` where `id`='1'");
		$rows = DB::fetch_array();
		return $rows;
	}
	
	public function Reviews(){
		/*DB::query("select * from `website_reviews_settings` where `id`='1'");
		$setting = DB::fetch_array();
		return $setting["active"];*/
		$q = mysql_query("select * from `website_reviews_settings` where `id`='1'");
		$rows = mysql_fetch_array($q);
		return $rows;
	}
	
	public function UpdateViews($id){
		// Update count views video
		DB::query("update `serial_item` set `viewers` = `viewers` + 1 where `id` = '$id'");
	}
	
	public function getMovies($id){
		// Get video from ID
		//DB::query("select * from `serial_item` where `id` = '$id'");
		DB::query("select * from `serial_item` where `id` = '$id'");
		$rows = DB::fetch_array();
		return $rows;
	}
	
	public function getFilmLists($id){

			/*DB::query("select max(`id`) FROM `serial_movie` JOIN `serial_item` ON `serial_item`.`movie_id` = `serial_movie`.`id` order by `serial_movie`.`weight` asc");
			$countMovies = DB::fetch_array();
			$LastMovies = $countMovies[0] - 5;*/

			/*DB::query('SELECT * FROM `serial_movie` JOIN `serial_item` ON `serial_item`.`movie_id` = `serial_movie`.`id` where  
				`serial_item`.`id` != ' . $id . ' group by `serial_item`.`movie_id` order by `serial_movie`.`weight` asc limit 6');*/
			DB::query("SELECT `id`,`link`,`name` FROM `serial_movie` WHERE `id` != " . $id . " ORDER BY `weight` ASC LIMIT 20");
			$movies = array();
			while($getList = DB::fetch_array()){
				$movies[] = $getList;
			}
			return $movies;

	}
	
	public function getFilmListsTwo($id){
			/*DB::query('SELECT * FROM `serial_movie` JOIN `serial_item` ON `serial_item`.`movie_id` = `serial_movie`.`id` 
				where `serial_item`.`id` != ' . $id . ' group by `serial_item`.`movie_id` order by rand() limit 4');*/
			//DB::query("SELECT `id`,`link`,`name` FROM `serial_movie` ORDER BY RAND() ASC LIMIT 4");
			DB::query("SELECT `id`,`link`,`name` FROM `serial_movie` ORDER BY `weight` asc LIMIT 4");
			$movies = array();
			while($getList = DB::fetch_array()){
				$movies[] = $getList;
			}
			return $movies;

	}

	public function ListSeasons($movie_id){
		$season=array();
		DB::query("Select `season_id` from `serial_item` where `movie_id` = '".$movie_id."' group by `season_id`");
		while($stills = DB::fetch_array()){
			$season[] = $stills;
		}
		return $season;
	}

	public function ListSeasonsSeries($movie_id,$season_id){
		$season=array();
		DB::query("SELECT * FROM `serial_movie` JOIN `serial_item` ON `serial_item`.`movie_id` = `serial_movie`.`id` where `serial_item`.`movie_id` = '".$movie_id."' and `serial_item`.`season_id`='".$season_id."'");
		while($stills = DB::fetch_array()){
			$season[] = $stills;
		}
		return $season;
	}
	
	
	
	public function getCats($id){
		// Get categories from ID on item
		DB::query("Select * from `category_films` where `id` = '".$id."' ");
		$stills = DB::fetch_array();
		return $stills["link"];
	}
	
	public function getPreviewPlayer($id, $frame){
		// Get preview image for video player
		$HostPath = 'http://imagees2.pw';
		$CatchCurrentFile = "http://imagees2.pw/i2/".$id."/". $frame;
		if($f=@fopen($CatchCurrentFile, 'rb')){
			$FileExist = 1;
			fclose($f);
		} else { $FileExist = 0;}
		unset($foto);
		unset($f);

		if($FileExist == 1){
			$previewimage = "content/".$id."/".$frame;
		}else{
			$max = 0;
			foreach(self::getMovieFrames($id) as $k => $item){
				if(is_file("i2/".$id."/frames/".$item['image_url'])){
					$s = getimagesize("i2/".$id."/frames/".$item['image_url']);
					if($s[0] > $s[1] && $s[0] > $max){
						$max = $s[0];
						$previewimage = "i2/".$id."/frames/".$item['image_url'];
					}
				}
			}
		}
		return $previewimage;
	}
	
	
	
	public function getTimePopup($id,$show_real,$real_movie_url,$time_reg, $real_preview,$player,$preview_time,$filename){
		// Default function for player
		if ($show_real==1 && !empty($real_movie_url)){
			$movie_preview_url = $real_movie_url;	
			if (!empty($time_reg)){
				$movie_time_to_popup = $time_reg;
			}else {
				$movie_time_to_popup = 180;
			}
		}else  if(trim($real_preview) != ''){
				$movie_preview_url = $real_preview;
				if(!empty($player)){
					$movie_time_to_popup = $player;
				}else 
					$movie_time_to_popup = $preview_time;
			} else{
			$movie_preview_url ="/serial/content/".$id."/previews/" . $filename;
			$movie_time_to_popup = $preview_time;
		}
		if ($movie_time_to_popup<1){
			$movie_time_to_popup = 59;
		}
		return array('0'=>$movie_preview_url,'1'=>$movie_time_to_popup);
	}
	
	public function parseComment($txt, $movie){
		// Parsing comments
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
	
	public function getComments($id,$cookie){
		DB::query("select count(1) from `serial_comments` where `movie_id` in ('0','".$id."')");
		$count = DB::fetch_array();
		if($cookie + 1 > $count[0]){
			$cookie = 0;
		}
	
		DB::query("select * from `serial_comments` where `movie_id` in ('0','".$id."') order by `id` asc limit ".$cookie.", 12");
		$comments = array();
		while($getComments = DB::fetch_array()){
			$comments[] = $getComments;
		}
		return $comments;
		
	}
	
	public function MovieNums(){
		// Numeric comments
		DB::query('Select * from serial_item where site_id = 1 order by id');
		while($all_movies_f = DB::fetch_array()){
			$all_movies[] = $all_movies_f;
		}
		$this_movie_num = 0;
		$id = 1;
		for($i = 0, $s = sizeof($all_movies); $i < $s; $i++){
			if($all_movies[$i]['id'] == $id){
				$this_movie_num = $i;
				break;
			}
		}
		return $this_movie_num;
	}
	
	public function FindContent($url){
		DB::query("Select count(1) from `serial_item` where  `id` = '".$url."'");
		$count = DB::fetch_array();
		return $count[0];
	}
	
	public function SettingPages($param){
		DB::query("select * from `setting_pages` where `name`='$param'");
		$rows = DB::fetch_array();
		return $rows["text"];
	}


	#COM
	public function newComments($id){
		//DB::query("select * from `new_comments_serial` where `movie_id` = '".$id."' order by `weight` asc");
		DB::query("select `id`,`avatar_url`,`author`,`comment` from `new_comments_serial` where `movie_id` = '".$id."' order by `weight` asc");
		$comments = array();
		while($getComments = DB::fetch_array()){
			$comments[] = $getComments;
		}
		return $comments;
	}
	
	public function SaveArrayComments($array,$session,$film){
		DB::query("delete from `auto_comments_serial` where `date` != '".date("d.m.Y")."'");
		DB::query("insert into `auto_comments_serial` (`session_id`,`array`,`film`,`type`,`date`)
			values('".$session."','".serialize($array)."','".$film."','main','".date('d.m.Y')."')");
	}

	public function SaveArrayCommentsAnswer($array,$session,$film,$type,$param){
		DB::query("insert into `auto_comments_serial` (`session_id`,`array`,`film`,`type`,`date`,`is_answer_type`)
			values('".$session."','".$array."','".$film."','".$type."','".date('d.m.Y')."','".$param."')");
	}

	public function GetCommentsExist($session,$film){
		DB::query("select * from `auto_comments_serial` where `film`='".$film."' and `session_id`='".$session."' and `type`='main'");
		while($row = DB::fetch_array()){
			$rows[] = $row;
		}
		return $rows;
	}

	public function GetListMainComments($string){
		DB::query("select * from `serial_comments` where (`id`='00000' ".$string.") order by `id` desc");
		while($row = DB::fetch_array()){
			$rows[] = $row;
		}
		return $rows;
	}

	public function GetListAdditionalComments($string){
		DB::query("select * from `new_comments_serial` where (`id`='00000' ".$string.") order by `id` desc");
		while($row = DB::fetch_array()){
			$rows[] = $row;
		}
		return $rows;
	}

	public function GetAddComments($id,$session){
		DB::query("select * from `auto_comments_serial` where `film`='".$id."' and `session_id`='".$session."' and `type`!='main' order by `id` desc");
		while($row = DB::fetch_array()){
			$rows[] = $row;
		}
		return $rows;
	}

	public function AnswerCommentData($id){
		DB::query("select `avatar_url`,`author` from `serial_comments` where `id`='".$id."'");
		$row = DB::fetch_array();
		return $row;
	}

	public function AnswerComment($id,$type){
			if($type == 0){
				//echo "select * from `serial_comments` where `id`='".$id."'";
				DB::query("select * from `serial_comments` where `id`='".$id."'");
				$row = DB::fetch_array();
			}
			if($type == 1){
				//echo "select * from `serial_comments_answers` where `id`='".$id."'";
				DB::query("select * from `serial_comments_answers` where `id`='".$id."'");
				$row = DB::fetch_array();
			}
			if($type == 2){
				//echo "select * from `serial_comments_random` where `id`='".$id."'";
				DB::query("select * from `serial_comments_random` where `id`='".$id."'");
				$row = DB::fetch_array();
			}
			//echo "select * from `serial_comments` where `id`='".$id."'";
			/*DB::query("select * from `serial_comments` where `id`='".$id."'");
			$row = DB::fetch_array();
			if(!$row){
				DB::query("select * from `serial_comments_answers` where `id`='".$id."'");
				$row = DB::fetch_array();
				if(!$row){
					DB::query("select * from `serial_comments_random` where `id`='".$id."'");
					$row = DB::fetch_array();
				}
			}*/
		//}
		return $row;
	}

}
?>