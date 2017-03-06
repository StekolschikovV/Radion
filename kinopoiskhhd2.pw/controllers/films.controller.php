<?php

class Films extends DB{
	#OPT
	public function getFilmsCatgory($category,$catid){
        DB::query("SELECT `id`,`link`,`name` FROM `serial_movie` where `category_id`='".$catid."' ORDER BY `serial_movie`.`weight` ASC LIMIT 12");
        $articles = array();
        while($row = DB::fetch_array()){
            $articles[] = $row;
        }
        return $articles;
	}

	#OPT
	public function getTitleCategory($category){
		DB::query("SELECT `id`,`name` FROM `serial_category` where `link`='$category' limit 1");
		$cat = DB::fetch_array();
		return $cat;
	}




	public function getCount($category){
		DB::query("SELECT * FROM `serial_category` where `link`='$category'");
		$cat = DB::fetch_array();


		DB::query("SELECT count(1) FROM `serial_item` where `category_id`='".$cat["id"]."' group by `movie_id`");
        $row = DB::fetch_array();
        return $row[0];
	}

}
?>