<?php
ini_set('display_errors', '1');
require_once(plugin_dir_path(__FILE__) . "../config.php");

if (isset($_REQUEST["nom_classe"])) {
	$nom_classe = $_REQUEST["nom_classe"];
	if (isset($_REQUEST["id"])) {
		$id = $_REQUEST["id"];
		if ($nom_classe::supprimable_ajax($id)) {
			$nom_classe::deleteStatic($id);
			?><p class="success">Entrée suprimmée avec succès</p><?php
			if(function_exists("rocket_clean_user")){
				rocket_clean_user( get_current_user_id());
			}
		} else {
			$item = new $nom_classe($id);
			$identifiant = $nom_classe::identifiant();
//			pre($item);
			if ($item->$identifiant() != $id) {
				?><p class="warning">Élément déjà supprimmé</p><?php
			} else {
				?><p class="warning">Ca ne marchera pas comme ça :)</p><?php
			}
		}
	} else {
		?><p class="warning">Erreur</p><?php
	}
} else {
	?><p class="warning">Erreur</p><?php
}