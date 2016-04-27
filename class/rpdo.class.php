<?php

class wpdbExtended extends wpdb {

	public function __construct(wpdb $wpdbItem) {
		$dbuser = $wpdbItem->dbuser;
		$dbpassword = $wpdbItem->dbpassword;
		$dbname = $wpdbItem->dbname;
		$dbhost = $wpdbItem->dbhost;

		parent::__construct($dbuser, $dbpassword, $dbname, $dbhost);
	}

	public function connexionArray() {
		$connexion = array();
		$connexion["host"] = $this->dbhost;
		$connexion["name"] = $this->dbname;
		$connexion["user"] = $this->dbuser;
		$connexion["pwd"] = $this->dbpassword;
		return $connexion;
	}

}

class rpdo {

	private static $instance = null;

	public static function getInstance() {
		//singleton
		if (!self::$instance) {

			global $wpdb;
			$wpextended = new wpdbExtended($wpdb);
			$connexionArray = $wpextended->connexionArray();

			$rpdo_host = $connexionArray["host"];
			$rpdo_name = $connexionArray["name"];
			$rpdo_user = $connexionArray["user"];
			$rpdo_pwd = $connexionArray["pwd"];

			$db = new PDO('mysql:host=' . $rpdo_host . ';dbname=' . $rpdo_name, $rpdo_user, $rpdo_pwd);
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

			self::$instance = $db;
		}
		return self::$instance;
	}

	public static function getDBCommunes(){
		
		$rpdo_host = "localhost";
		$rpdo_name = "jador2";
		$rpdo_user = "jador";
		$rpdo_pwd = "GJqcTCp4";


		$db = new PDO('mysql:host=' . $rpdo_host . ';dbname=' . $rpdo_name, $rpdo_user, $rpdo_pwd);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
		
		return $db;
	}
}
