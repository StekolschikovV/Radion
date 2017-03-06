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
				<div class="film-block" onclick="location.href='/serial/view/<?=$r[0]?>-<?=$article["link"]?>.html'">
                      <div class="top-block">
                        <img src="http://imagees2.pw/i2/<?=$r["id"];?>/<?=$r["poster_url"];?>">
                        <div class="link-block" style="width:90px">
                          <i aria-hidden="true" class="fa fa-play-circle-o"></i>
                          <i aria-hidden="true" class="fa fa-download"></i>
                          <i aria-hidden="true" class="fa fa-comments"></i>
                        </div>
                      </div>
                      <div class="title-film-block"><div class="title-film"><?=$article["name"]?></div></div>
                    </div>
				<?	
			}
		}
	}
	
	
}
?>