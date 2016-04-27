<?php

class registred_dates extends bii_items {

	protected $id;
	protected $metakey;
	protected $date;

	
	function date() {
		$retour = "";
		if ($this->date != 0) {
			$retour = date("d/m/Y", $this->date);
		}
		return $retour;
	}

	function date_complete() {
		$retour = "";
		if ($this->date != 0) {
			$retour = date("d/m/Y H:i", $this->date);
		}
		return $retour;
	}

	function date_tmstp() {
		return $this->date;
	}
	
	function nom() {
		return $this->metakey;
	}

	public static function feminin() {
		return true;
	}

	public function option_value() {
		return $this->nom();
	}

	public static function nomXml() {
		return "date";
	}

	public static function display_pagination() {
		return false;
	}

	public static function display_filter() {
		return false;
	}

	public static function exists($key) {
		$where = "metakey = '$key'";
		
		$nb = static::nb($where);
		if ($nb) {
			return true;
		}
		return false;
	}

	public static function fromkey($key) {
		$where = "metakey = '$key'";
		$ids = static::all_id($where);
		foreach ($ids as $id) {
			$item = new static($id);
		}
		return $item;
	}

	public static function insertorupdate($key, $value = 0) {
		if ($value * 1 == 0) {
			$value = time();
		}
		if (static::exists($key)) {
			$item = static::fromkey($key);
		} else {
			$item = new static();
			$item->insert();
			$item->updateChamps($key, "metakey");
		}
		$item->updateChamps($value, "date");
		return $item;
	}
}
