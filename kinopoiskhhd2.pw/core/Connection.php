<?php

/*
 * Conncetion class | DataBase Connect
 * This class connected to database
 */
 
 
class DB {
	# private vars
	private $mysql = false;
    private $query = false;
	private $DB;

	# main setting for connect
	public function __construct(){
		$host = "213.202.233.41";
		$user = "ruser";
		$db = "platforms";
		$pass = "BpsHVryT1985";
		$this->mysql = mysql_connect($host, $user, $pass);
		mysql_select_db($db, $this->mysql);
		mysql_query("SET NAMES `utf8`");
	}
	
	# init query 
	public function query($sql) {
		if(!$this->mysql) return false;
		$this->query = mysql_query($sql, $this->mysql);
		#mysql_close ($this->query);
	}
	
	# init fetching arrays()
	public function fetch_array(){
		if(!$this->query) return false;
		return mysql_fetch_array($this->query);
		mysql_close ($this->query);
	}
     
	# init fetching rows
    public function fetch_row(){
		if(!$this->query) return false;
		return mysql_fetch_row($this->query);
		mysql_close ($this->query);
	}
	

}
?>