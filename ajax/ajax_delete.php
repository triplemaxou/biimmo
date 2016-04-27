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
		} else {
			?><p class="warning">Ca ne marchera pas comme ça :)</p><?php
		}
	} else {
		?><p class="warning">Erreur</p><?php
	}
} else {
	?><p class="warning">Erreur</p><?php
}