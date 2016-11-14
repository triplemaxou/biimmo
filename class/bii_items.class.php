<?php

class bii_items extends global_class {

	public static function prefix_bdd() {
		$prefix = parent::prefix_bdd();
		return $prefix . "bii_";
	}

	public static function nomXml() {
		return ucfirst(static::nom_classe_bdd());
	}

	public static function is_externe() {
		return true;
	}

	public static function themename() {
		return "realhomes";
	}

	public function XML_exists($xml) {
		if (static::is_externe()) {
			return url_exists($xml);
		} else {
			return file_exists($xml);
		}
	}

	public static function autoTable($is_autoinserted = false) {
		$class_name = static::nom_classe_bdd();
		$prefix = static::prefix_bdd();
		$item = new static();
		$tab = $item->tabPropValeurs();
		$scriptSQL = "CREATE TABLE IF NOT EXISTS `$prefix$class_name` (";
		$virg = "";
		$identifiant = static::identifiant();
		foreach ($tab as $prop => $val) {
			$scriptSQL.= $virg;
			if ($prop == $identifiant) {
				$scriptSQL.= "`$identifiant` int(11) NOT NULL";
			} else {
				$scriptSQL .= "`$prop` varchar(255) NULL";
			}
			$virg = ",";
		}
		$scriptSQL .= ") ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
		$scriptSQL .= "ALTER TABLE `$prefix$class_name` ADD PRIMARY KEY (`$identifiant`);";
		$scriptSQL .= "ALTER TABLE `$prefix$class_name` MODIFY `$identifiant` int(11) NOT NULL AUTO_INCREMENT;";
		if ($is_autoinserted) {
			$pdo = static::getPDO();
			$pdo->query($scriptSQL);
			bii_custom_log($scriptSQL, "auto Table");
		}
		return $scriptSQL;
	}

	public static function fromXML($path = "", $start = 0, $stop = 1500) {
		$return = "";
		$nb_new = 0;
		$nb_edit = 0;
		$nb_errors = 0;
		$nb_archive = 0;
		if (!$path) {
			$path = "http://lemaistre.bbimmo.pro/ftp/lemaistre/32/siteinternet/siteinternet.xml";
		}
		if (static::XML_exists($path)) {
			$xml = simplexml_load_file($path);
//			var_dump($xml);
			$liste_annonces = $xml->bien;
			unset($xml);
			$count = count($liste_annonces);
			echo "<h2>Nombre d'annonces au total : $count</h2> "
//				. "Memory ".(memory_get_peak_usage() / 1024 / 1024)." MB"
				. "";
			$i = 0;
			$liste_identifiant = [];
			$arrayClasses = ["annonce"];
//			$liste_annonce = array_reverse($liste_annonce);
			foreach ($liste_annonces as $item) {
//				echo " index $i Memory ".(memory_get_peak_usage() / 1024 / 1024)." MB";
				$liste_identifiant[] = $item->reference;
				$id_xml = get_object_vars($item)["@attributes"]["id"];
//				if($item->reference == "302721"){
				if ($i >= $start && $i < $stop) {
					foreach ($arrayClasses as $classe) {
//						echo "<h2>Insert $classe</h2>";
						try {
							$log = "id_xml $id_xml ";
							bii_custom_log("ID xml $id_xml","Début insertion bien");
							$log .= $classe::fromAnnonceXML($item);

							$return .= $log;
							if (stripos($log, "erreur")) {
								++$nb_errors;
							} elseif (stripos($log, "ajoutée")) {
								++$nb_new;
							} elseif (stripos($log, "existante")) {
								++$nb_edit;
							}
						} catch (Exception $e) {
							$message = $e->getMessage();
							echo "<br/>$message</br>";
							++$nb_errors;
						}
					}
				}
				unset($item);
				++$i;
			}
			
//			
			$where = "reference NOT IN(".implode(",",$liste_identifiant).") AND is_archive = 0";
			$liste_archive = annonce::all_id($where);
			$nb_archive = count($liste_archive);
			if($nb_archive){
				foreach($liste_archive as $id_arch){
					$annarch = new annonce($id_arch);
					$annarch->change_archive(1);
				}
			}
		} else {
			echo "Echec lors de l'ouverture du fichier $path";
		}

		$ret = ["errors" => $nb_errors, "added" => $nb_new, "edit" => $nb_edit, "log" => $return, "archive" => $nb_archive];
		return $ret;
	}

	public static function fromAnnonceXML($annonceXML) {
		$vars = get_object_vars($annonceXML);
		unset($annonceXML);
		$nom_xml = static::nomXml();

		$values = [];
		$id = 0;
		$identifiant = static::identifiant();
		foreach ($vars as $key => $val) {
			if (strpos($key, $nom_xml) !== false) {
				$key = str_replace($nom_xml, "", $key);
				if (property_exists(static::nom_classe_bdd(), $key)) {
					if ($identifiant == $key) {
						$id = $val;
					} else {
						$values[$key] = $val;
					}
				}
			}
		}


		if (!static::exists($id)) {
			$nom_class = static::nom_classe_bdd();
			$prefix = static::prefix_bdd();
			$pdo = static::getPDO();
			$sql = "insert into $prefix$nom_class (id) VALUES ($id)";
			$pdo->query($sql);
//			var_dump($values);
			$item = new static($id);
			$item->updateChamps($values);
//			echo "<br /> " . static::nom_classe_admin() . " ajouté : $item->id";
			unset($item);
		}
		return "";
	}

	public static function dezip() {
		$cheminArchive = "/web/clients/heuzimmo/import/connectimmo.zip";
		if (file_exists($cheminArchive)) {
			$destination = "/web/clients/heuzimmo/www.heuze-immo.fr/wp-content/plugins/biimmo/export";
			if (is_dir($destination)) {
				$zip = new ZipArchive;
				$zip->open($cheminArchive);
				$file = $zip->extractTo($destination);

				if ($file) {
					return 'Successfully unzipped the file!';
				} else {
					return 'There was an error unzipping the file.';
				}
			} else {
				return "Directory does not exists $destination";
			}
		} else {
			return "File does not exists $cheminArchive";
		}
	}

	public static function exists($id) {
		$where = static::identifiant() . " = '" . $id . "'";
		$nb = static::nb($where);
		if ($nb) {
			return true;
		}
		return false;
	}

}
