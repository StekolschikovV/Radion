<?php
session_start();

class Info extends DB{
	#OPT
	public function getArticles($url){
		DB::query("select * from `information` where `url`='$url'");
		$content = DB::fetch_array();
		return $content;
	}

	#OPT
	public function ListArticles(){
		DB::query("select * from `information");
		$articles = array();
            while($row = DB::fetch_array()){
                $articles[] = $row;
            }
            return $articles;
	}
}
?>