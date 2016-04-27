<?php
ini_set('display_errors', '1');
require_once(plugin_dir_path(__FILE__)."../config.php");

if (isset($_REQUEST["nom_classe"])) {
	$nom_classe = $_REQUEST["nom_classe"];
	if (isset($_REQUEST["id"])) {
		$id = $_REQUEST["id"];
		if (isset($_REQUEST["method"])) {
			$method = $_REQUEST["method"];

			if (isset($_REQUEST["value"])) {
				$value = $_REQUEST["value"];

				$item = new $nom_classe($id);
				$return = $item->$method($value);
				?><p class="success" data-return="<?= $return; ?>">updated</p><?php
			}else{
				?><p class="warning">value seems to be uninitialized</p><?php
			}
		} else {
			?><p class="warning">bool seems to be uninitialized</p><?php
		}
	} else {
		?><p class="warning">id seems to be uninitialized</p><?php
	}
} else {
	?><p class="warning">class_name seems to be uninitialized</p><?php
}