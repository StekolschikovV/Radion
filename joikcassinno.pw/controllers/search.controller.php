<?php
session_start();

class Search extends DB{
	
	#OPT
	public function activateSearch($query){
		DB::query("SELECT `id`,`link`,`name` FROM `serial_movie` WHERE `name` like '%".$query."%' ORDER BY `serial_movie`.`weight` ASC");
		//DB::query("SELECT * FROM `serial_movie` JOIN `serial_item` ON `serial_item`.`movie_id` = `serial_movie`.`id` where `serial_movie`.`name` like '%".$query."%'  GROUP BY `serial_item`.`movie_id` ORDER BY `serial_movie`.`weight` ASC");
		while($array = DB::fetch_array()){
			$arrays[] = $array;
		}
		if(!$arrays) {
			echo '<div class="alert alert-error"><b>Ваш запрос не дал результатов! Попобуйте изменить параметры поиска</b></div>';
		} else {
			foreach($arrays as $article){
				$q = mysql_query("select `id`,`poster_url` from `serial_item` where `movie_id`='".$article["id"]."' and `show` != 1 order by `id` desc limit 1");
				$r = mysql_fetch_array($q);
				?>
				<a href="/serial/view/<?=$r["id"]?>-<?=$article["link"]?>.html" class="available-to-view-film-block">
                  <div class="available-to-view-film-img">
                    <img src="http://imagees2.pw/i2/<?=$r["id"];?>/<?=$r["poster_url"];?>" alt="<?=$article["name"]?>">
                  </div>
                  <div class="available-to-view-film-title"><?=$article["name"]?></div>
                </a>
				<?	
			}
		}
	}
	
	
}
?>

