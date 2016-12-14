<?php

class annonce extends bii_items {

	protected $id;
	protected $id_post;
	protected $is_archive;
	protected $nom_agence;
	protected $negociateur_nom;
	protected $reference; //302473 reference; //
	protected $num_mandat; // num_mandat; //
	protected $dateCreation; //2016-01-27 12:09:09 dateCreation; //
	protected $secteurWeb; //Goderville secteurWeb; //
	protected $situation; //CAMPAGNE - OUEST situation; //
	protected $coupdecoeur; //1 coupdecoeur; //
	protected $nouveaute; //1 nouveaute; //
	protected $nom_pays; //France nom_pays; //
	protected $code_postal; //76110 code_postal; //
	protected $ville; //BREAUTE ville; //
	protected $nom_secteur; //CAMPAGNE - OUEST nom_secteur; //
	protected $proximite; // proximite; //
	protected $description_magazine; //EXCLUSIVITE, 30 MIN DU HAVRE, AXE BREAUTE/ST ROMAIN, CHARMANTE CAUCHOISE colombage SH 150m², bel environnement. Séjour, salon cheminée, 5 chambres dont 1 au RDC, SDB, SDD. Garage, dépendances, cour plantée de 3 000m². Le calme est au RDV.  description_magazine; //
	protected $description_internet; //EXCLUSIVITE, 30 MIN DU HAVRE, AXE BREAUTE/ST ROMAIN, CHARMANTE CAUCHOISE colombage SH 150m², bel environnement. Séjour, salon cheminée, 5 chambres dont 1 au RDC, SDB, SDD. Garage, dépendances, cour plantée de 3 000m². Le calme est au RDV. Réf: JL. Veuillez contacter l'agence de GODERVILLE 02 35 10 20 00. description_internet; //
	protected $type_transaction; //vente type_transaction; //
	protected $type_bien; //maison type_bien; //
	protected $prestige; // prestige; //
	protected $nb_piece; //6 nb_piece; //
	protected $nb_chambre; //5 nb_chambre; //
	protected $nb_parking_interieur; //0 nb_parking_interieur; //
	protected $nb_parking_exterieur; //0 nb_parking_exterieur; //
	protected $etage; //1 etage; //
	protected $nb_etage; //0 nb_etage; //
	protected $mitoyennete; //Indépendant mitoyennete; //
	protected $surface_habitable; //160 surface_habitable; //
	protected $surface_jardin; //3030 surface_jardin; //
	protected $prix; //202000 prix; //
	protected $montant_frais_agence; //12000 montant_frais_agence; //
	protected $visiteVirtuelle; // visiteVirtuelle; //
	protected $ascenseur; // ascenseur; //
	protected $nbdebalcons; // nbdebalcons; //
	protected $nbterrasses; // nbterrasses; //
	protected $exposition; //Sud exposition; //
	protected $interphone; // interphone; //
	protected $naturechauffage; //Fuel naturechauffage; //
	protected $modechauffage; //Radiateur modechauffage; //
	protected $typechauffage; // typechauffage; // 
	protected $nbparkint; // nbparkint; //        
	protected $nbparkext; // nbparkext; // 
	protected $nbgarage; // nbgarage; //
	protected $longitude; // taxefonciere; //
	protected $latitude; // taxefonciere; //
	protected $code_insee; // taxefonciere; //
	protected $taxefonciere; // taxefonciere; //
	protected $coprocharges; // coprocharges; //                      
	protected $fai; //12000 fai; //
	protected $bilan_energie; // bilan_energie; //
	protected $valeur_energie; //0.00 valeur_energie; //
	protected $bilan_ges; // bilan_ges; //
	protected $valeur_ges; //0.00 valeur_ges; //
	protected $date_maj;

	public function visiteVirtuelle() {
		
	}

	public static function fromPost($post) {
		$where = "id_post = $post";
		$liste = static::all_id($where);
		foreach ($liste as $id) {
			$item = new static($id);
		}
		if (!(bool) $liste) {
			return new static(0);
		}
		return $item;
	}

	public function biens_similaires($ecart_prix = 0.1, $ecartville = 5, $liste = array()) {
//		pre($ecart_prix." ".$ecartville,"green");
//		pre($liste,"blue");
		$ville = villes_france::fromCodeInsee($this->code_insee());
		if ($ville->id()) {
			$villes_alentour = $ville->insee_autour($ecartville);
			$prix = $this->prix();
			$prix_min = $prix - ($prix * $ecart_prix);
			$prix_max = $prix + ($prix * $ecart_prix);
			$pieces_min = $this->nb_piece();

			$strvilles = "(";
			$sep = "";
			foreach ($villes_alentour as $insee) {
				$strvilles .= $sep . "'" . $insee . "'";
				$sep = ",";
			}
			$strvilles .= ")";
			$where = "is_archive = 0 AND code_insee in $strvilles AND prix > $prix_min AND prix < $prix_max and nb_piece >= $pieces_min and id <> $this->id";
//		consoleLog($where);
			$liste = array_unique(array_merge($liste, static::all_id($where)));
			$nb = count($liste);
			if ($nb < 3) {
				$liste = array_unique(array_merge($liste, $this->biens_similaires($ecart_prix * 1.2, $ecartville + 2.5, $liste)));
//			pre($liste,"red");
			}
			return $liste;
		} else {
			return [];
		}
	}

	public static function trad_rs($rs) {
		switch ($rs) {
			case "keyword":
				return "code_insee";
			case "property-id":
				return "reference";
			case "type":
				return "type_bien";
			case "bathrooms":
				return "nb_piece";
			case "max-price":
			case "min-price":
				return "prix";
			case "max-area":
			case "min-area":
				return "surface_habitable";
			case "balcon":
				return "nbdebalcons";
			case "type-de-chauffage":
				return "typechauffage";
			case "plain-pied-de-vie":
				return "description_internet";

			default:
				return $rs;
		}
	}

	static function filters_form_arguments($array_selected = array()) {
		?>
		<option class="nb" value="id" data-oldval="id" >Id</option>
		<option class="nb" value="reference" data-oldval="reference" >reference</option>
		<option class="nb" value="id_post" data-oldval="id_post" >Id du post</option>
		<option class="nb" value="is_archive" data-oldval="is_archive" >Archivée</option>
		<option class="text" value="ville" data-oldval="ville" >Ville</option>
		<?php
	}

	public static function display_pagination() {
		return true;
	}

	public static function display_filter() {
		return true;
	}

	public function idGlobal() {
		return $this->reference;
	}

	public function prix() {
		return $this->prix;
	}

	public function montant_frais_agence() {
		return $this->montant_frais_agence;
	}

	public function is_FAI() {
		if ($this->montant_frais_agence) {
			return true;
		}
		return false;
	}

	public function balcon() { //Je sais c'est moche mais c'est plus simple comme ça
		$nb = $this->nbdebalcons();
		if ($nb) {
			return true;
		}
		return false;
	}

	public function is_interphone() {
		$nb = $this->interphone();
		if ($nb) {
			return true;
		}
		return false;
	}

	public function codeInsee() {
		if (!$this->code_insee) {
			$ville = $this->villeAAfficher();
			$item = villes_france::fromNom($ville);
			try {
				$insee = $item->codeInsee();
				$this->updateChamps($insee, "code_insee");
				return $insee;
			} catch (Exception $e) {
				throw new Exception("Erreur Nom de commune : $ville n'existe pas");
			}
		} else {
			return $this->code_insee;
		}
	}

	public function is_mitoyen() {
		switch ($this->mitoyennete) {
			case "":
			case null:
			case 0:
			case "Indépendant":
				$is_mitoyen = false;
				break;
			default:
				$is_mitoyen = true;
				break;
		}
		return $is_mitoyen;
	}

	public function is_mitoyen_fr() {
		$ret = "non";
		if ($this->is_mitoyen()) {
			$ret = "oui";
		}
		return $ret;
	}

	public function prixTotal() {
		if ($this->type_transaction() == "vente") {
			return $this->prix()/* + $this->montant_frais_agence() */;
		} else {
			return $this->prix();
		}
	}

	public function type_bien() {
		return $this->type_bien;
	}

	public function article_type_bien(&$defini = "ce", &$indefini = "un") {
		$tb = strtolower($this->type_bien);
		if ($tb == "appartement" || $tb == "immeuble") {
			$defini = "cet";
		}
		if ($tb == "maison") {
			$defini = "cette";
			$indefini = "une";
		}
	}

	public function surface() {
		return $this->surface_habitable();
	}

	public function type_transaction() {
		return $this->type_transaction;
	}

	public function villeAAfficher() {
		$ville = utf8_encode($this->ville);

		return $ville;
	}

	public function villeAAffichernorm() {
		$ville = $this->villeAAfficher();
		return ucwords(strtolower($ville));
	}

	public function villeAAfficherAvecCP() {
		$ville = strtoupper($this->villeAAfficher());
		$cp = $this->code_postal();
		$string = $ville;
		if ($cp) {
			$string = "$ville ($cp)";
		}

		return $string;
	}

	public function dateDisponibiliteOuLiberation() {
		$date = $this->dateCreation;

		return $date;
	}

	public function dateCreation() {
		$date = $this->dateCreation;

		return $date;
	}

	public function nb_piece() {
		return $this->nb_piece;
	}

	public function chauffage() {
		return utf8_encode($this->naturechauffage);
	}

	public function typechauffage() {
		return utf8_encode($this->typechauffage);
	}

	public function etage() {
		return $this->etage;
	}

	public function etage_string() {
		$etage = $this->etage();
		if ($etage == 0) {
			$etage = "RDC";
		} else {
			if (is_int($etage)) {
				$cardinal = "ème";
				if ($etage == 1) {
					$cardinal = "er";
				}
				$etage = "$etage<sup>$cardinal</sup> étage";
			}
		}
		return $etage;
	}

	public function classeDPE() {
		return $this->bilan_energie;
	}

	public function classeGES() {
		return $this->bilan_ges;
	}

	public function GES() {
		return $this->valeur_ges;
	}

	public function DPE() {
		return $this->valeur_energie;
	}

	public function garage() {
		return $this->nb_parking_exterieur + $this->nb_parking_interieur;
	}

	public function lienVisiteVirtuelle() {
		$lien = "";
		if ($this->visiteVirtuelle) {
			if (strpos($this->visiteVirtuelle, "tour.previsite.com")) {
				$lien = $this->visiteVirtuelle;
			} else {
				$lien = "http://viewer.previsite.net/default.asp?Key=CJT64S&Portal=COMMNET&Ref=$this->visiteVirtuelle&TB_iframe=trueamp;height=305&width=291";
			}
		}
		return $lien;
	}

	public function nb_string($method, $string = "", $upper = false, $no_s = false) {
		if ($string == "") {
			$string = str_replace("nb_", "", $method);
		}
		if ($upper) {
			$string = ucfirst($string);
		}
		$s = "s";
		$nb = 0;
		if (method_exists($this, $method)) {
			$nb = $this->$method();
		}
		if ($nb < 2 || $no_s) {
			$s = "";
		}
		return "$nb $string$s";
	}

	public function exposition_trad() {
		$expo = strtoupper($this->exposition);
		$expo = str_replace("/", "-", $expo);
		$expo = str_replace(" ", "-", $expo);
		switch ($expo) {
			case "S":
				$expo = "Sud";
				break;
			case "O":
				$expo = "Ouest";
				break;
			case "E":
				$expo = "Est";
				break;
			case "N":
				$expo = "Nord";
				break;
			case "OUEST-EST":
			case "OE":
			case "EO":
				$expo = "Est-ouest";
				break;
			case "NS":
				$expo = "Nord-sud";
				break;
			case "SO":
				$expo = "Sud-ouest";
				break;
			case "SE":
			case "EST/SUD":
				$expo = "Sud-est";
				break;
			case "NO":
				$expo = "Nord-ouest";
				break;
			case "NE":
				$expo = "Nord-est";
				break;
			case "O.E.S":
				$expo = "Sud-ouest-est";
				break;
			default:
				$expo = ucfirst(strtolower($expo));
				break;
		}
		return trim($expo);
	}

	public function have($methode, $prefix = "", $suffix = "", $replaceValue = "", $upper = true, $notDisplayValues = ["none"]) {
		$value = $methode;
		if ($replaceValue) {
			$value = $replaceValue;
		}
		if (is_callable(array($this, $methode)) && $this->$methode() && !in_array($this->$methode(), $notDisplayValues)) {

			$retour = $value;
			if ($upper) {
				$retour = ucfirst($value);
			}
			return "$prefix$retour$suffix";
		}
		return "";
	}

	public function nb_piece_string($upper = true) {
		return $this->nb_string("nb_piece", "pièce", $upper);
	}

	public function surface_string() {
		return $this->nb_string("surface", "m²", false, true);
	}

	public function nb_chambre() {
		return $this->nb_chambre;
	}

	function texte() {
		$texte = str_replace("€", "&euro;", $this->description_internet);
		$texte = str_replace("&#8364;", "&euro;", $texte);
		return $texte;
	}

	public function adresse() {
		$pays = $this->nom_pays();
		$ville = $this->villeAAfficher();
		$code = $this->code_postal();
		return "$ville $code, $pays";
	}

	function encodetexte() {

		return utf8_encode($this->texte());
	}

	static function getListeProprietes() {
		$array = array(
			"id" => "id",
			"idGlobal" => "identifiant",
			"id_post" => "Lien vers le post",
			"titre" => "titre",
//			"type_transaction" => "Type de transaction",
			"type_bien" => "Type de bien",
			"ville" => "Ville",
			"code_insee" => "Insee",
			"dateCreation" => "Créée le",
//			"dateModification" => "Modifiée le",
//			"ascenseur" => "ascenseur",
			"taxonomy_features" => "Taxonomies",
			"is_archive" => "archivée",
			"purge_image" => "purger les images",
		);
		return $array;
	}

	static function getListeProprietesFormEdit() {
		$item = new static();
		$liste = array();
		foreach ($item->tabPropValeurs() as $prop => $value) {
			$liste[$prop] = $prop;
		}
		return $liste;
	}

	protected static function do_not_display_array() {
		return array("id");
	}

	function getPost($autoinsert = true) {
		$ip = 0;
		if ($autoinsert) {
			if ($this->id_post()) {
				$ip = $this->id_post();
			}
		} else {
			if ($this->id_post) {
				$ip = $this->id_post;
			}
		}
		return new posts($ip);
	}

	function getPhotos($type = "postString") {
		$where = "id_annonce = '" . $this->idGlobal() . "'";
		$liste_id = annonce_image::all_id($where);
		$string_id = $string_idpost = $string_url = "";
		$liste_item = [];
		$onlyid = false;
		if ($type == "idArray") {
			return $liste_id;
		}
		if ($type == "idString") {
			$onlyid = true;
		}
		$sep = "";
		foreach ($liste_id as $id) {
			$string_id .= "$sep$id";
			if (!$onlyid) {
				$ai = new annonce_image($id);
//				var_dump($ai);
				$liste_item[] = $ai;
				$string_idpost .= $sep . $ai->attach_id();
				if ($type == "urlString") {
					$string_url .= $sep . $ai->urlPhoto();
				}
			}
			$sep = ",";
		}
		if ($type == "itemArray") {
			return $liste_item;
		}
		if ($type == "idString") {
			return $string_id;
		}
		if ($type == "urlString") {
			return $string_url;
		}
		if ($type == "postString") {

			return $string_idpost;
		}
	}

	protected function arrayPost($id_post = 0) {
		$name = static::themename();
		$method_name = "arrayPost_$name";
		return $this->$method_name($id_post);
	}

	private function arrayPost_realhomes($id_post = 0) {
		$array = $this->arrayPost_shandora($id_post);
		$array["post_type"] = "property";
		return $array;
	}

	public function buildlink() {
		$link = str_replace("²", "-carres", $this->titre());
//		$link = str_replace("²", "-carres", $this->titre());

		$link = stripAccentsLiens($link);
		$link = str_replace("-0-m-carres", "", $link);
		return $link;
	}

	private function arrayPost_shandora($id_post = 0) {
		$post = array(
			'ID' => $id_post // Are you updating an existing post?
			, 'post_content' => $this->encodetexte() // The full text of the post.
			, 'post_name' => $this->buildlink() // The name (slug) for your post
			, 'post_title' => $this->titre() // The title of your post.
			, 'post_status' => "publish" // Default 'draft'.
			, 'post_type' => "listing" // Default 'post'.
			, 'post_author' => 1 // The user ID number of the author. Default is the current user ID.
			, 'ping_status' => "closed" // Pingbacks or trackbacks allowed. Default is the option 'default_ping_status'.
			, 'post_parent' => 0 // Sets the parent of the new post, if any. Default 0.
			, 'menu_order' => 0 // If new post is a page, sets the order in which it should appear in supported menus. Default 0.
			, 'to_ping' => '' // Space or carriage return-separated list of URLs to ping. Default empty string.
			, 'pinged' => ''// Space or carriage return-separated list of URLs that have been pinged. Default empty string.
			, 'post_password' => '' // Password for post, if any. Default empty string.
//		, 'guid' => // Skip this and let Wordpress handle it, usually.
//		, 'post_content_filtered' => // Skip this and let Wordpress handle it, usually.
//		, 'post_excerpt' => [<string>] // For all your post excerpt needs.
			, 'post_date' => $this->dateCreation() // The time post was made.
			, 'post_date_gmt' => $this->dateCreation()
			, 'comment_status' => 'closed' // Default is the option 'default_comment_status', or 'closed'.
//		, 'post_category' => "" // Default empty.
//		, 'tags_input' => ['<tag>, <tag>, ...' | array] // Default empty.
//		, 'tax_input' => [array( <taxonomy> => <array | string>, <taxonomy_other> => <array | string> )] // For custom taxonomies. Default empty.
//		, 'page_template' => [<string>] // Requires name of template file, eg template.php. Default empty.
		);
		if (0 == $id_post) {
			unset($post["ID"]);
		}
//		var_dump($post["post_content"]);
		return $post;
	}

	public function insertPost() {
		if (!$this->id_post) {
			$values = $this->arrayPost();
			$id_post = wp_insert_post($values);
			$this->updateChamps($id_post, "id_post");
			echo " new post : $id_post <br />";
//			var_dump($values);
		} else {
			$values = $this->arrayPost($this->id_post);
			wp_insert_post($values);

			echo " post updated : $id_post <br />";
		}
		return $id_post;
	}

	public function insertAttachementPhotos() {
		$liste = $this->getPhotos("itemArray");

		foreach ($liste as $photo) {
			$photo->addAttachement($this->id_post);
		}
	}

	public function id_post() {
		$id = $this->id_post;
		if (!$this->id_post) {
			$id = $this->insertPost();
		}
		return $id;
	}

	public function traduireStatut() {
		switch ($this->categorieOffre) {
			case "Location": case "location": return "for-rent";
			case "Vente": case "vente": return "for-sale";
			default:return "none";
		}
	}

	public function prixCC() {
		switch ($this->categorieOffre) {
			case "Location":case "location": return $this->montant + $this->charges;
			case "Vente": case "vente": return $this->montant + $this->honoraires;
			default:return "none";
		}
	}

	public function departement() {
		$insee = $this->code_insee;
		$dept = "";
		if ($insee) {
			$dept = $insee[0] . $insee[1];
			if ($dept == '97') {
				$dept .= $insee[2];
			}
		}
		return $dept;
	}

	public function departement_nom(&$article = "le") {
		$nom = "";
		$dept = $this->departement();
		if ($dept) {
			if ($dept == 14) {
				$dept = "Calvados ($dept)";
			}
			if ($dept == 27) {
				$dept = "Eure ($dept)";
				$article = "l'";
			}
			if ($dept == 76) {
				$dept = "Seine-Maritime ($dept)";
				$article = "la";
			}
		}
		return $dept;
	}

	public function titlefirst() {
		$article = "le";
		$type_bien = "un bien";
		if ($this->type_bien()) {
			$type_bien = $this->type_bien();
		}
		$surface = "";
		if ($this->surface()) {
			$surface = " de " . $this->surface_string();
		}
		$type_trans = "";
		if ($this->type_transaction()) {
			$type_trans = " en " . $this->type_transaction();
		}
		$ville = "";
		if ($this->ville) {
			$ville = $this->villeAAffichernorm();
		}

		$departement = "";
		if ($this->departement_nom()) {
			$dept = $this->departement_nom($article);
			$departement = " dans $article $dept";
		}
		$titl = "$ville : $type_bien$type_trans$surface";
		return $titl;
	}

	public function altfirst() {
		$type_bien = "Immobilier";
		if ($this->type_bien()) {
			$type_bien = ucfirst($this->type_bien());
		}
		$ville = "";
		if ($this->ville) {
			$ville = $this->villeAAffichernorm();
		}
		$dept = "";
		if ($this->departement()) {
			$dept = " (" . $this->departement() . ")";
		}
		return "$type_bien $ville$dept";
	}

	public function titre() {
//		$ville = $this->villeAAfficher();
		$type = ucfirst($this->type_bien);
		$action = $this->action_bien();
		if ($this->type_bien() != "garage" && $this->type_bien() != "terrain") {
			$pieces = $this->nb_piece_string();
			$surface = "";
			if ($this->surface()) {
				$surface = " - " . $this->surface_string();
			}
//			$titre = "$ville - $type $pieces $action - $surface";
			$titre = "$type $pieces $action$surface";
		} else {
//			$titre = "$ville - $type $action";
			$titre = "$type $action";
		}
		return $titre;
	}

	public function metaTitle() {
		$name = get_bloginfo("name");

		$article = "le";
		$type_bien = "un bien";
		if ($this->type_bien()) {
			$type_bien = $this->type_bien();
		}
		$surface = "";
		if ($this->surface()) {
			$surface = " de " . $this->surface_string();
		}
		$type_trans = "";
		if ($this->type_transaction()) {
			$type_trans = " en " . $this->type_transaction();
		}
		$ville = "";
		if ($this->ville) {
			$ville = $this->villeAAffichernorm();
		}

		$departement = "";
		if ($this->departement_nom()) {
			$dept = $this->departement_nom($article);
			$departement = " dans $article $dept";
		}
		$titl = "$name $ville : $type_bien$type_trans$surface";
		return $titl;
	}

	public function metaDescription() {
		$name = get_bloginfo("name");
		$ce = "ce";
		$article = "le";
		$type_bien = "un bien";
		if ($this->type_bien()) {
			$type_bien = $this->type_bien();
			$this->article_type_bien($ce);
			$type_bien = "$ce $type_bien";
		}
		$surface = "";
		if ($this->surface()) {
			$surface = " de " . $this->surface_string();
		}
		$type_trans = "";
		if ($this->type_transaction()) {
			$type_trans = " en " . $this->type_transaction();
		}
		$departement = "";
		if ($this->departement_nom()) {
			$dept = $this->departement_nom($article);
			$departement = " dans $article $dept";
		}
		if ($this->ville) {
			$ville = " à " . $this->villeAAffichernorm();
		}
		$desc = "$name vous propose $type_bien$surface$type_trans$ville$departement";
		return $desc;
	}

	public function map_postMeta() {
		$name = static::themename();
		$method_name = "map_postMeta_$name";
		$array = $this->$method_name();
		$array["_yoast_wpseo_metadesc"] = $this->metaDescription();
		$array["_yoast_wpseo_title"] = $this->metaTitle();
		$array["alt_for_first_image"] = $this->altfirst();
		$array["title_for_first_image"] = $this->titlefirst();
		return $array;
	}

	public function additionals_details() {
		$tb = $this->type_bien();
		$arr = [];

		if ($this->prestige()) {
			$arr["Prestige"] = "Oui";
		}
		if ($this->etage() != "" && $tb == "appartement") {
			$arr["Étage"] = $this->etage_string();
		}
		if ($this->surface_jardin()) {
			$arr["Surface du terrain"] = $this->surface_jardin() . " m²";
		}
		if ($this->proximite()) {
			$arr["Proximité"] = $this->proximite();
		}
		if ($this->typechauffage()) {
			$arr["Type de Chauffage"] = $this->typechauffage();
		}
		if ($this->modechauffage()) {
			$arr["Mode de Chauffage"] = $this->modechauffage();
		}
		if ($this->nb_etage()) {
			$arr["Nombre d'étages"] = $this->nb_etage() . " étages";
		}
		if ($this->exposition()) {
			$arr["Exposition"] = $this->exposition();
		}
		if ($this->is_plein_pied_fr() != "non") {
			$arr["Plain Pied"] = $this->is_plein_pied_fr();
		}
		if ($this->DPE()) {
			$arr["DPE"] = "Classe " . $this->classeDPE() . " (" . $this->DPE() . " kWh<sub>EP</sub>/m².an)";
		}
		if ($this->GES()) {
			$arr["GES"] = "Classe " . $this->classeGES() . " (" . $this->GES() . " kgeqCO2/m2/an)";
		}
		if ($this->taxefonciere()) {
			$arr["Taxe Foncière"] = $this->taxefonciere() . " &euro;";
		}
		if ($this->coprocharges()) {
			$arr["Charges de Copropriété"] = $this->coprocharges() . " &euro;";
		}
		if ($this->lienVisiteVirtuelle()) {
			$lien = $this->lienVisiteVirtuelle();
			$arr["Visite Virtuelle"] = "<a href='$lien' target='_blank'>Voir la visite virtuelle</a>";
		}
		return serialize($arr);
	}

	public function agent_id() {
		$agent = strtolower($this->nom_agence());
		$id = 988; //Agences du havre
		if (strpos($agent, "oderville") !== false) {
			$id = 995;
		} elseif (strpos($agent, "olbosc") !== false) {
			$id = 993;
		} elseif (strpos($agent, "sneval") !== false) {
			$id = 991;
		} elseif (strpos($agent, "ontivilliers") !== false) {
			$id = 7963;
		}
		return $id;
	}

	private function map_postMeta_realhomes() {
		$array = [
			"REAL_HOMES_property_price" => $this->prixTotal(),
			"REAL_HOMES_property_size" => $this->surface(),
			"REAL_HOMES_property_price_postfix" => "&euro;",
			"REAL_HOMES_property_size_postfix" => "m²",
			"REAL_HOMES_property_bedrooms" => $this->nb_chambre(),
			"REAL_HOMES_property_garage" => $this->nbgarage(),
			"REAL_HOMES_property_bathrooms" => $this->nb_piece(), //attention ! bathrooms est utilisé pour les pièces
			"REAL_HOMES_property_id" => $this->idGlobal(),
			"REAL_HOMES_property_map" => "0",
			"REAL_HOMES_property_address" => $this->adresse(),
			"REAL_HOMES_property_city" => $this->ville(),
//			"REAL_HOMES_gallery_slider_type" => $this->idGlobal(),
//			"REAL_HOMES_agent_display_option" => $this->idGlobal(),
//			"REAL_HOMES_agents" => $this->idGlobal(),
//			"REAL_HOMES_featured" => $this->idGlobal(),
//			"REAL_HOMES_add_in_slider" => $this->idGlobal(),
			"REAL_HOMES_additional_details" => $this->additionals_details(),
			"REAL_HOMES_agents" => $this->agent_id(),
			"REAL_HOMES_featured" => $this->coupdecoeur(),
			"REAL_HOMES_add_in_slider" => $this->nouveaute(),
			"REAL_HOMES_property_insee" => $this->codeInsee(),
			"REAL_HOMES_property_virtual" => $this->lienVisiteVirtuelle(),
		];


		return $array;
	}

	private function map_postMeta_shandora() {
		$array = [
			"shandora_listing_mls" => $this->idGlobal(),
			"shandora_listing_status" => $this->traduireStatut(),
			"shandora_listing_period" => "per-month",
			"shandora_listing_show_period" => "no",
			"shandora_listing_address" => $this->villeAAfficher(),
			"shandora_listing_price" => $this->prixCC(),
			"shandora_listing_bed" => $this->nb_chambre(),
			"shandora_listing_bath" => $this->nbSallesDEau,
			"shandora_listing_garage" => $this->nbGarages,
			"shandora_listing_basement" => $this->cave,
			"shandora_listing_floor" => $this->etage,
			"shandora_listing_totalroom" => $this->pieces,
			"shandora_listing_buildingsize" => $this->surface,
			"shandora_listing_furnishing" => "any",
			"shandora_listing_mortgage" => "any",
			"shandora_listing_dateavail" => $this->dateDisponibiliteOuLiberation(),
			"shandora_listing_yearbuild" => $this->anneeConstruction,
			"shandora_listing_dpe" => $this->consommationenergie,
			"shandora_listing_ges" => $this->emissionges,
//			"shandora_listing_maplatitude",
//			"shandora_listing_maplongitude",
			"shandora_listing_featured" => 1,
			"shandora_videoself" => 0,
			"shandora_videoembed" => 0,
			"shandora_videocover" => 0,
			"shandora_videocover" => 0,
			"shandora_listing_gallery" => $this->getPhotos("postString"),
		];

		return $array;
	}

	public function insertPostMeta() {
//		if ($this->reference == "302721") {
		$map = $this->map_postMeta();
		$id_post = $this->id_post();
		$where = "post_id = '" . $id_post . "'";
		postmeta::deleteWhere($where);
		foreach ($map as $key => $value) {
			postmeta::add($id_post, $key, $value);
		}
//		}
	}

	private function action_bien() {
		$action = "";
		$ca = strtolower($this->type_transaction());
		if ("location" == $ca) {
			$action = "à louer";
		}
		if ("vente" == $ca) {
			$action = "à vendre";
		}
		return $action;
	}

	private function action_bien_nom() {
		$action = "";
		$ca = strtolower($this->type_transaction());
		if ("location" == $ca) {
			$action = "en location";
		}
		if ("vente" == $ca) {
			$action = "en vente";
		}
		return $action;
	}

	private function taxonomyTypeBien() {
		$tb = strtolower($this->type_bien());
		$ab = $this->action_bien_nom();

		$array_s = ["maison", "appartement", "garage", "commerce"];
		if (in_array($tb, $array_s)) {
			$tb.="s";
		}
		$string = "$tb $ab";
		return trim($string);
	}

	private function taxonomyActionBien() {
		$ab = $this->action_bien_nom();
		$string = "$ab";
		return trim($string);
	}

	public function plain_pied() {
		if (stripos($this->description_internet(), "PLAIN PIED") !== false) {
			return 1;
		} else {
			return 0;
		}
	}

	public function plain_pied_de_vie() {
		if ($this->plain_pied()) {
			if (stripos($this->description_internet(), "DE VIE") !== false) {
				return 1;
			}
		}
		return 0;
	}

	public function is_plein_pied_fr() {
		$ret = "non";
		if ($this->plain_pied()) {
			$ret = "oui";
			if ($this->plain_pied_de_vie()) {
				$ret = "de vie";
			}
		}

		return $ret;
	}

	public function taxonomy_features() {
		$arr = [];
		$liste_features = [
			"chauffage" => ["prefix" => "", "suffix" => " " . $this->chauffage(), "replaceValue" => "", "upper" => true, "notDisplayValues" => ["Radiateur fonte", "Fuel", "Autres"]],
			"typechauffage" => ["prefix" => "", "suffix" => " " . $this->typechauffage(), "replaceValue" => "Type de Chauffage", "upper" => true, "notDisplayValues" => ["Aucun"]],
			"exposition" => ["prefix" => "", "suffix" => " " . $this->exposition_trad(), "replaceValue" => "", "upper" => true, "notDisplayValues" => []],
			"is_plein_pied_fr" => ["prefix" => "", "suffix" => " " . $this->is_plein_pied_fr(), "replaceValue" => "Plain Pied", "upper" => true, "notDisplayValues" => ["non"]],
			"is_FAI" => ["prefix" => "", "suffix" => "", "replaceValue" => "honoraires inclus", "upper" => true, "notDisplayValues" => []],
			"mitoyennete" => ["prefix" => "", "suffix" => " " . $this->is_mitoyen_fr(), "replaceValue" => "", "upper" => true, "notDisplayValues" => []],
			"ascenseur" => ["prefix" => "", "suffix" => "", "replaceValue" => "", "upper" => true, "notDisplayValues" => []],
			"balcon" => ["prefix" => "", "suffix" => "", "replaceValue" => "", "upper" => true, "notDisplayValues" => []],
			"interphone" => ["prefix" => "", "suffix" => "", "replaceValue" => "", "upper" => true, "notDisplayValues" => []],
		];
		if ($this->prestige()) {
			$liste_features["prestige"] = ["prefix" => "", "suffix" => " ", "replaceValue" => "", "upper" => true, "notDisplayValues" => []];
		}

		foreach ($liste_features as $name => $feature) {
			$have = $this->have($name, $feature["prefix"], $feature["suffix"], $feature["replaceValue"], $feature["upper"], $feature["notDisplayValues"]);
			if (trim($have)) {
				$arr[] = $have;
			}
		}
//		echo "<br />array have";
//		var_dump($arr);
//		echo "<br />";
		return $arr;
	}

	public function insertTaxonomy() {
		$name = static::themename();
		$method_name = "insertTaxonomy_$name";
		return $this->$method_name();
	}

	private function insertTaxonomy_realhomes() {

//		echo "<br /> taxonomies ";
		$id_post = $this->id_post;
		$taxonomies = [
			'property-city' => utf8_encode($this->villeAAfficherAvecCP()) . "",
			'property-status' => [ucwords($this->taxonomyTypeBien()), ucwords($this->taxonomyActionBien())],
			'property-feature' => $this->taxonomy_features(),
			'property-type' => ucwords($this->type_bien()),
		];
		foreach ($taxonomies as $id => $value) {
			$ok = "";
//			echo "<p><strong>$id_post $value $id</strong></p>";
			if ((bool) $value) {
				$ok = wp_set_object_terms($id_post, $value, $id);
				if (is_wp_error($ok)) {
//				echo " $id:erreur ";
				} else {
//				echo " $id:ok ";
				}
			}
		}
	}

	private function insertTaxonomy_shandora() {
		$ville = strtoupper($this->villeAAfficher());
//		$idville = terms::addVille($ville);
		$tb = $this->taxonomyTypeBien();
//		$idtype = terms::addType($tb);

		$ok1 = wp_set_object_terms($this->id_post, $ville, 'property-location');
		$ok2 = wp_set_object_terms($this->id_post, $tb, 'property-type');
		if (is_wp_error($ok1) || is_wp_error($ok2)) {
			echo " erreur taxonomies";
		} else {
			echo " 2 taxonomies insérées";
		}
	}

	public static function checkIdentifiants($listeID) {
		$array = [];
		$listeIDToCheck = static::allIdentifiants("is_archive = 0");
		foreach ($listeIDToCheck as $identifiant) {
			$item = static::fromIdentifiant($identifiant);
			if (!in_array($identifiant, $listeID) && $item->reference() != 0) {
				$item->change_archive(1);
				$array[] = $item->id;
			}
		}
		return $array;
	}

	public function listeEtags() {
		return annonce_image::listeEtag("id_annonce = " . $this->id());
	}

	public static function fromAnnonceXML($annonceXML) {
		$return = " erreur";
		$vars = get_object_vars($annonceXML);
//		$id_xml = $annonceXML["@attributes"]["id"];
		unset($annonceXML);
		$values = [];
		$photos = [];
		$id = 0;

		$identifiant = "reference";
		$hasImages = false;
		foreach ($vars as $key => $val) {
			if (property_exists(static::nom_classe_bdd(), $key)) {
				if ($identifiant == $key) {
					$id = $val;
				}
				echo " Memory " . (memory_get_peak_usage() / 1024 / 1024) . " MB ";
				$values[$key] = $val;
			}
			if ("images" == $key) {
//				var_dump($val);


				foreach ($val as $photo_ext) {
					$hasImages = true;
					$out = get_headers($photo_ext);
					foreach ($out as $v) {
						if (strstr($v, 'ETag:')) {
							$etag = substr($v, 7);
							$etag = substr($etag, 0, -1);
						}
					}
					$photos[] = ["photo" => $photo_ext, "etag" => $etag];
				}
//				sort($photos);
//				echo " Memory ".(memory_get_peak_usage() / 1024 / 1024)." MB ";
//				bii_custom_log($photos, "Photos insérées");
			}
		}
		unset($vars);
		echo "<br /> ";
		if (!static::exists($id)) {
//			var_dump($values);
			$item = new static();
			$item->insert();
			$item->updateChamps(0, "is_archive");
			$item->updateChamps(time(), "date_maj");
			$item->updateChamps($values);

			$lettreLog = "N";
			$motecho = "ajouté";
		} else {
			$item = static::fromIdentifiant($id);
//			annonce_image::deleteFromReference($id);
			$lettreLog = "E";
			$motecho = "existante";
			$messup = "";
			if ($item->is_archive) {
				$item->updateChamps(0, "is_archive");
				$item->updateChamps(time(), "date_maj");
			}
		}
		$item->updateChamps($values);
		$item->insertPost();
		$id = $item->id;
//		echo " Memory ".(memory_get_peak_usage() / 1024 / 1024)." MB ";
		bii_custom_log("Bien $id id_post $item->id_post ", "Bien inséré");
		try {
			$item->insertPostMeta();
		} catch (Exception $e) {
			$messup = " Erreur : " . $item->ville . " n'existe pas";
		}
		echo "<br /> " . static::nom_classe_admin() . " $motecho : $item->id id du post : $item->id_post" . $messup;
		$return = " $lettreLog($item->id id du post : $item->id_post)$messup \n\r";

		$item->insertTaxonomy();
		$alt = $item->titre();
		$countphotos = count($photos);
		if ((bool) $photos) {
			$attid1 = 1183;
			$i = 1;
			$etags = [];
			foreach ($photos as $phetag) {
				$photo = $phetag["photo"];
				$etag = $phetag["etag"];
				$etags[] = $etag;
//				pre($phetag);

				$nbetag = annonce_image::etagExists($etag);
				bii_write_log($photo . " " . $nbetag);
				if ($nbetag == 1) {
					$ai = annonce_image::fromEtag($etag);
				} else {
					if ($nbetag > 1) {
						annonce_image::deleteFromEtag($etag);
					}
					$ai = new annonce_image();
					$ai->insert();
					$year = date("Y");
					$month = date("m");
					$ai->updateChamps(["id_annonce" => $id, "photo" => $photo, "alt" => "$alt photo $i", "year" => $year, "month" => $month, "etag" => $etag]);
				}
				$attid = $ai->addAttachement($item->id_post);
				postmeta::add($item->id_post, "REAL_HOMES_property_images", $attid);

				if (strpos($photo, "-1") !== false && strpos($photo, "-10") === false) {
					$ai1 = $ai;
					$attid1 = $ai1->attach_id();
				}
				++$i;
			}
			$liste_etagsbien = $item->listeEtags();
			foreach ($liste_etagsbien as $etcheck) {
				if (!in_array($etcheck, $etags)) {
					$photo = annonce_image::fromEtag($etcheck);
					$photo->purge();
					$photo->delete();
				}
			}

			delete_post_thumbnail($item->id_post);
			set_post_thumbnail($item->id_post, $attid1);
			if ($item->nouveaute) {
				postmeta::add($item->id_post, "REAL_HOMES_slider_image", $attid1);
			}
			if ($item->coupdecoeur) {
				postmeta::add($item->id_post, "REAL_HOMES_attachments", $attid1);
			}
			unset($ai);
			unset($ai1);
			bii_custom_log("Bien $id nb photos $countphotos", "Photos insérées");
		}

		unset($item);

		return $return;
	}

	public static function fromIdentifiant($id) {
		$where = "reference = '" . $id . "'";
		bii_custom_log("fromIdentifiant $id");
		$ids = static::all_id($where);
		$nb = count($ids);
		if ($nb) {
			return new static($ids[0]);
		}

		return new static(0);
	}

	public static function toDoDoublons($meth = "count") {
		$req = "reference IN (SELECT reference FROM wp_987abc_bii_annonce GROUP BY reference HAVING COUNT(*) > 1)";
		$ids = static::all_id($req);

		$refprec = 0;
		if ($meth == "count") {
			return count($ids);
		}
		if ($meth == "print") {
			$liste_ref = [];
			foreach ($ids as $id) {
				$item = new static($id);
				$liste_ref[$id] = $item->reference;
			}
			pre($liste_ref);
		}
		if ($meth == "return") {
			$liste_ref = [];
			foreach ($ids as $id) {
				$item = new static($id);
				$liste_ref[$id] = $item->reference;
			}
			return $liste_ref;
		}
		if ($meth == "desarchive") {
			$liste_ref = [];
			foreach ($ids as $id) {
				$item = new static($id);
				$item->change_archive(0);
			}
			pre($liste_ref);
		}

		if ($meth == "delete" || $meth == "archive") {
			foreach ($ids as $id) {
				$item = new static($id);
				$refcurrent = $item->reference;
				if ($refprec == $refcurrent) {
					$item->change_archive(1);
					if ($meth == "delete") {
						wp_delete_post($item->id_post, 1);
						static::deleteStatic($id);
					}
				}
				$refprec = $refcurrent;
			}
		}
		posts::deleteWhere('post_type = "property" AND `post_content` = "" AND ID not IN (select distinct id_post from `wp_987abc_bii_annonce`');
	}

	public static function exists($id) {
		$where = "reference = '" . $id . "'";
		$nb = static::nb($where);
		if ($nb) {
			return true;
		}
		return false;
	}

	public static function allIdentifiants() {
		$pdo = static::getPDO();
		$class_name = static::prefix_bdd() . static::nom_classe_bdd();
		$req = "select reference from " . $class_name;

		$select = $pdo->query($req);
		$liste = array();
		while ($row = $select->fetch()) {
			array_push($liste, $row["reference"]);
		}
		$pdo = null;
		return $liste;
	}

	public static function tradDate($datestring) {
		if (strpos($datestring, "/")) { // date en 17/12/2015
			$date_actu_expl = explode("/", $datestring);
			$date_actu_expl_jour = $date_actu_expl[0];
			$date_actu_expl_mois = $date_actu_expl[1];
			$date_actu_expl_anne = $date_actu_expl[2];
//			$val = mktime(9, 0, 0, $date_actu_expl_mois, $date_actu_expl_jour, $date_actu_expl_anne);
			return "$date_actu_expl_anne-$date_actu_expl_mois-$date_actu_expl_jour 09:00:00";
		}
		if (strpos($datestring, "-")) { //date en  2015-02-17 08:38:27
			$date_actu_expl = explode("-", $datestring);
			$date_actu_expl_jour = substr($date_actu_expl[2], 0, 2);
			$date_actu_expl_mois = $date_actu_expl[1];
			$date_actu_expl_anne = $date_actu_expl[0];
			return "$date_actu_expl_jour/$date_actu_expl_mois/$date_actu_expl_anne";
		}
	}

	public static function archive_where($where = "") {
		$nom_table = static::prefix_bdd() . static::nom_classe_bdd();
		$req = "UPDATE $nom_table set is_archive = 1 where $where";
		bii_custom_log($req, "Request_archive");
		$pdo = static::getPDO();
		$nb = $pdo->exec($req);
		bii_custom_log($nb, "Post Archive");
		return $nb;
	}

	public function change_archive($value) {
//		echo $value;
		$this->updateChamps($value, "is_archive");
		if ($value == 1) {
			$this->getPost(false)->unpublish(true);
		} else {
			$this->getPost(false)->publish(true);
			$this->updateChamps(time(), "date_maj");
		}
	}

	public function purgeImages() {
		$where = "id_annonce = '" . $this->id . "'";
		$liste_id = annonce_image::all_id($where);
		pre($liste_id);
		foreach ($liste_id as $id_image) {
			$image = new annonce_image($id_image);
			$image->purge();
		}
		annonce_image::deleteFromAnnonce($this->id);
		$this->insertPostMeta();
	}

	public function categorieOffre_ligneIA() {
		$categorieOffre = $this->categorieOffre;
		?>
		<td class="categorieOffre">						
			<?= $categorieOffre ?>				
		</td>
		<?php
	}

	public function typeBien_ligneIA() {
		$typeBien = $this->typeBien;
		?>
		<td class="typeBien">						
			<?= $typeBien ?>
		</td>
		<?php
	}

	public function dateCreation_ligneIA() {
		$dateCreation = $this->dateCreation;
		?>
		<td class="dateCreation">						
			<?= $dateCreation ?>
		</td>
		<?php
	}

	public function dateModification_ligneIA() {
		$dateModification = $this->dateModification;
		?>
		<td class="dateModification">						
			<?= $dateModification ?>
		</td>
		<?php
	}

	public function id_post_ligneIA() {
		$id_post = $this->id_post;
		?>
		<td class="id_post">			
			<a class="btn btn-success" target="_blank" data-id="<?php echo $this->id; ?>" href="/wp-admin/post.php?post=<?= $id_post ?>&action=edit" >
				<?= $id_post ?>
			</a>		
		</td>
		<?php
	}

	public function titre_ligneIA() {
		$titre = $this->titre();
		?>
		<td class="titre">			

			<?= $titre ?>

		</td>
		<?php
	}

	public function is_archive_ligneIA() {

		$radios = array("Non" => "success", "Oui" => "danger");
		$is_archive = $this->is_archive;
		$val = "Non";
		if ($is_archive == 1) {
			$val = "Oui";
		}
		?>
		<td class="statut"> 
			<?php
			foreach ($radios as $value => $color) {
				$checked = "";
				if ($val == $value) {
					$checked = " <i class='fa fa-check-square-o'></i>";
					$color = "$color go-$color";
				} else {
					$color = "default go-$color";
				}
				?>
				<button class="btn btn-<?php echo $color; ?> change-statut" data-id="<?php echo $this->id; ?>" ><?php echo ucfirst($value . $checked); ?></button>

				<?php
			}
//			echo $this->have("ascenseur");
			?>
		</td>
		<?php
	}

	public function purge_image() {
		
	}

	public function purge_image_ligneIA() {
		?>
		<td class="statut"> 
			<button class="btn btn-warning purgeimages" data-id="<?php echo $this->id; ?>" >
				<span class="fa-stack">
					<i class="fa fa-picture-o fa-stack-1x"></i>
					<i class="fa fa-ban fa-stack-2x text-danger"></i>
				</span>
			</button>
		</td>
		<?php
	}

	public function taxonomy_features_ligneIA() {
		?>
		<td class="taxonomy_features"> 
			<?php
			$tf = $this->taxonomy_features();
			$sep = "";
			foreach ($tf as $f) {
				echo "$sep$f";
				$sep = ", ";
			}
			$this->insertTaxonomy();
			?>
		</td>
		<?php
	}

	public static function maxPrix() {
		$prix = "1000000";
		$prefix = static::prefix_bdd();
		$class_name = static::nom_classe_bdd();
		$req = "select max(prix) as maximum from $prefix$class_name WHERE is_archive = 0";
//		consoleLog($req);
		$pdo = static::getPDO();
		$select = $pdo->query($req);
		while ($row = $select->fetch()) {
			$prix = $row["maximum"];
		}
		$pdo = null;
		return $prix;
	}

	public static function maxSurf() {
		$surf = "1600";
		$prefix = static::prefix_bdd();
		$class_name = static::nom_classe_bdd();
		$req = "select max(surface_habitable + surface_jardin) as maximum from $prefix$class_name WHERE is_archive = 0";
//		consoleLog($req);
		$pdo = static::getPDO();
		$select = $pdo->query($req);
		while ($row = $select->fetch()) {
			$surf = $row["maximum"];
		}
		$pdo = null;
		return $surf;
	}

	public function list_usertosent() {
		$list = users::users_alert();
		$listtosend = [];
		foreach ($list as $user_id => $list_rs) {
			$send = false;
			foreach ($list_rs as $rs) {
				if ($this->is_inrs($rs)) {
					$send = true;
				}
			}
			if ($send) {
				$listtosend = [$user_id];
			}
		}
		return $listtosend;
	}

	public function is_inrs($rs) {
		//a:10:{
		//	s:7:"keyword";s:8:"LE HAVRE";
		//	s:11:"property-id";s:0:"";
		//	s:4:"type";s:11:"appartement";
		//	s:9:"bathrooms";s:1:"1";
		//	s:9:"max-price";s:6:"200000";
		//	s:9:"min-price";s:1:"0";
		//	s:13:"max-price-all";s:7:"1050000";
		//	s:8:"min-area";s:2:"80";
		//	s:8:"max-area";s:3:"100";
		//	s:8:"features";a:2:{
		//		i:0;s:10:"interphone";
		//		i:1;s:6:"balcon";}
		//	}
	}

	public function lien() {
		$lien = get_permalink($this->id_post());
		return $lien;
	}

	public static function mailFromListe($liste, $limit = 0) {
		pre($liste, "green");
		ob_start();
		static::headermail();
		$i = 1;
		foreach ($liste as $id) {
			if ($limit != 0 && $i <= $limit) {
				$annonce = new annonce($id);
				$annonce->displayAnnonceMail($i);
			}
			++$i;
		}
		static::footermail();
		$email_body = ob_get_contents();
		ob_end_clean();
		return $email_body;
	}

	public static function headermail() {
		?>
		<!doctype html>
		<html xmlns="http://www.w3.org/1999/xhtml">
			<head></head>
			<body>
				<div class="liste_annonce" style="max-width:600px;width:100%;">
					<table><tbody>
							<tr>
								<?php
							}

							public static function footermail() {
								?>
							</tr>
						</tbody></table>
				</div>
			</body>
		</html>
		<?php
	}

	public static function headermailMjml() {
		?>

		<mj-body>
			<mj-section>
				<?php
			}

			public static function footermailMjml() {
				?>

			</mj-section>
		</mj-body>
		<?php
	}

	public function displayAnnonceMail($i) {
		$classArt = "";
		$fatitle = "";
		if ($this->coupdecoeur()) {
			$classArt .= "coupdecoeur";
			$fatitle = '<i class="fa fa-heart"></i>';
		}
		if ($this->nouveaute()) {
			$classArt .= "nouveau";
		}
		$lien = $this->lien();
		$titre = $this->titre();
		$srcthumb = get_the_post_thumbnail($this->id_post);
//		pre($srcthumb,"red");
		$type_bien = $this->type_bien();
//		$type_trans = $this->type_transaction();
//		$text_caption = ucfirst("$type_bien en $type_trans");
		$prix = $this->prix();
		$liendesc = utf8_decode("<a href='$lien' class='more-details'>+ de détails</a>");
		$description = utf8_encode(static::tronquer($this->description_internet(), 100, $liendesc));
		$surface = $this->surface_string();
		$chambres = $this->nb_string("nb_chambre", "chambre");
		$pieces = $this->nb_string("nb_piece", "pièce");
		if ($i % 2 == 1 && $i != 1) {
			?></tr><tr><?php
		}
		?>
			<td>
				<div style="width:200px;display:inline-block;">
					<article class="property-item" style="background-color: #ffffff;border: 1px solid #dedede;
							 margin-bottom: 5px !important;margin-top: 5px !important;padding: 15px 19px 0;
							 background-color: #fff;text-align: left;">
						<h4 style="height:38px;"><?= $fatitle; ?><a href="<?= $lien; ?>"><?= $titre; ?></a></h4>

						<figure>
							<a href="<?= $lien; ?>"><?= $srcthumb; ?></a>

						</figure>

						<div class="detail" style="height:130px;">
							<h5 class="price" ><span style="color:#cd1719;">
									<?= $prix; ?> €</span><small> - <?= $type_bien; ?></small>            </h5>
									<?= $description; ?>

						</div>

						<table class="property-meta" style="width:200px;display:table;margin-bottom:10px;border: 1px solid #dedede;">
							<tr>
								<td style="display:table-cell;border: 1px solid #dedede;"><?= $surface; ?></td>
								<td style="display:table-cell;border: 1px solid #dedede;"><?= $chambres; ?></td>
								<td style="display:table-cell;border: 1px solid #dedede;"><i class="icon-sofa"></i><?= $pieces; ?></td>
							</tr>
						</table>
					</article>
				</div>
			</td>
			<?php
		}

		public function displayAnnonceMailMjml($i) {

			$lien = $this->lien();
			$titre = $this->titre();
			$srcthumb = wp_get_attachment_image_src(get_post_thumbnail_id($this->id_post))[0];
//		pre($srcthumb,"red");
			$type_bien = $this->type_bien();
			$type_trans = $this->type_transaction();
			$text_caption = ucfirst("$type_bien en $type_trans");
			$prix = $this->prix();
			$liendesc = "";
			$description = utf8_encode(static::tronquer($this->description_internet(), 100, $liendesc));
			$surface = $this->surface_string();
			$chambres = $this->nb_string("nb_chambre", "chambre");
			$pieces = $this->nb_string("nb_piece", "pièce");
			if ($i % 2 == 1 && $i != 1) {
				?></mj-section><mj-section><?php
			}
			?>

			<mj-column background-color="#fafafa">
				<mj-text font-size="24px"  font-family="helvetica" color="#92A1BB">
					<?= $titre; ?>
				</mj-text>
				<mj-image href="<?= $lien; ?>" align="center" width="150" height="150" src="<?= $srcthumb ?>"></mj-image>

				<mj-divider border-color="#F45E43"></mj-divider>
				<mj-section background-color="#fafafa">
					<mj-column background-color="#fafafa">
						<mj-text font-size="15px" color="#cd1719" font-family="helvetica" align="center">
							<?= $prix; ?> €
						</mj-text>
					</mj-column>
					<mj-column background-color="#fafafa">
						<mj-text font-size="12px" color="#000" font-family="helvetica" align="center">
							<?= $text_caption; ?>
						</mj-text>
					</mj-column>
				</mj-section>
				<mj-section background-color="#fafafa">
					<mj-text font-size="20px"  font-family="helvetica">
						<?= $description; ?>
					</mj-text>
					<mj-button href="<?= $lien; ?>">Voir +</mj-button>
				</mj-section>
				<mj-section background-color="#fafafa">
					<mj-column  background-color="#F1F1F1" font-family="helvetica">
						<mj-text font-size="20px" align="center" font-family="helvetica"><?= $surface; ?></mj-text>
					</mj-column>
					<mj-column  background-color="#F1F1F1">
						<mj-text font-size="20px" align="center" font-family="helvetica"><?= $chambres; ?></mj-text>
					</mj-column>
					<mj-column  background-color="#F1F1F1">
						<mj-text font-size="20px" align="center" font-family="helvetica"><?= $pieces; ?></mj-text>
					</mj-column>
				</mj-section>
			</mj-column>


			<?php
		}

		public static function liste_reload() {
			$req = 'ID NOT IN '
				. '( '
				. 'select meta.post_id from wp_987abc_posts as post '
				. 'right join wp_987abc_postmeta as meta on post.ID = meta.post_id '
				. 'where post_type = "property" '
				. 'AND post_status = "publish" '
				. 'AND meta_key = "_thumbnail_id"'
				. ') '
				. 'AND LENGTH(post_content) > 3 '
				. 'AND post_status = "publish" AND post_type = "property" '
				. 'ORDER BY ID ASC';
			$liste = posts::all_id($req);
			$liste_annonce = [];
			foreach ($liste as $id_post) {
				$liste_annonce[] = static::fromPost($id_post);
			}
			return $liste_annonce;
		}

		public static function nb_annonces($where = "1=1") {
			return static::nb("is_archive = 0 AND $where");
		}

	}
	