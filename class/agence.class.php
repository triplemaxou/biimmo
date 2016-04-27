<?php

class agence extends bii_items {

	protected $id;
	protected $nom; //Lemaistre Immobilier - Goderville 76110 nom_agence; //
	protected $telephone; //02.35.10.20.00 telephone_agence; //
	protected $fax; //02.35.10.20.01 fax_agence; //
	protected $email; // email_agence; //
	protected $url; //http://www.lemaistre-immo.com url_agence; //
	protected $nom_pays; //fr nom_pays_agence; //
	protected $ville; //Goderville ville_agence; //
	protected $code_postal; //76110 code_postal_agence; //
	protected $adresse; //27, Place Godard des Vaux adresse_agence; //

	function nom() {
		return $this->nom;
	}

	public static function feminin() {
		return true;
	}

	public function option_value() {
		return $this->nom();
	}

	public static function nomXml() {
		return "agence";
	}

	public static function display_pagination() {
		return true;
	}

	public static function display_filter() {
		return true;
	}

	public static function fromAnnonceXML($annonceXML) {
		$vars = get_object_vars($annonceXML);
		$nom_xml = static::nomXml();

		$values = [];
		$nom = "";
		$identifiant = "nom";
		foreach ($vars as $key => $val) {
			if (strpos($key, $nom_xml) !== false) {
				$key = str_replace("_" . $nom_xml, "", $key);
				if (property_exists(static::nom_classe_bdd(), $key)) {
					if ($identifiant == $key) {
						$nom = $val;
					} else {
						$values[$key] = $val;
					}
				}
			}
		}
//		echo "<br /> ";		
		if (!static::exists($nom)) {
			$item = new static();
			$item->updateChamps($values);
		}
	}

	public static function exists($nom) {
		$where = "nom = '$nom'";
		$nb = static::nb($where);
		if ($nb) {
			return true;
		}
		return false;
	}

}
