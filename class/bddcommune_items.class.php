<?php

class bddcommune_items extends global_class {

	static function getPDO() {
		
		return rpdo::getDBCommunes();
	}
	
	public static function prefix_bdd() {
		return "";
	}

	public static function editable(){
		return false;
	}
	public static function supprimable(){
		return false;
	}
	

}
