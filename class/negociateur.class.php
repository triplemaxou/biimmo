<?php

class negociateur extends bii_items {

	protected $id;

	protected $nom;
	protected $tel;
	protected $mail;

	public static function nom_classe_admin() {
		return "négociateur";
	}
	
	public function option_value() {
		return $this->nom();
	}

//	static function getListeProprietes() {
//		$array = array(
//			"id" => "id",
//			"nom" => "nom",
//			"adresse" => "adresse",
//			"url_image" => "Image",
//		);
//		return $array;
//	}

	public static function display_pagination() {
		return true;
	}

	public static function display_filter() {
		return true;
	}
	
	public static function nomXml() {
		return "negociateur";
	}

	/*
	  static function filters_form_arguments() {
	  ?>

	  <option class="text" value="nom">Nom</option>
	  <option class="nb" value="id">Id</option>


	  <?php
	  }

	  public function url_image_ligneIA() {
	  ?>
	  <td class="url_image">
	  <?php if ($this->url_image()) { ?>
	  <img alt="fournisseur-<?php echo $this->id; ?>" src="<?php echo $this->url_image(); ?>" />
	  <?php } ?>
	  </td>
	  <?php
	  }
	 */

	public static function fromAnnonceXML($annonceXML) {
		$vars = get_object_vars($annonceXML);
		$nom_xml = static::nomXml();
		
		$values = [];
		$nom = "";
		$identifiant = "nom";
		foreach ($vars as $key => $val) {
			if (strpos($key, $nom_xml) !== false) {
				$key = str_replace($nom_xml."_", "", $key);
				if (property_exists(static::nom_classe_bdd(), $key)) {
					if ($identifiant == $key) {
						$nom = $val;
					}else{
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
