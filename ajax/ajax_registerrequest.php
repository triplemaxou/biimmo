<?php

ini_set('display_errors', '1');
require_once(plugin_dir_path(__FILE__) . "../config.php");
unset($_REQUEST["action"]);
$ser = serialize($_REQUEST);


$cu = wp_get_current_user();
if ($cu->ID) {
	$rep = usermeta::add($cu->ID, "requete_sauvegardee", $ser, false);
	echo $rep;
	if (function_exists("rocket_clean_user")) {
		rocket_clean_user($cu->ID);
	}
} else {
	echo "Vous n'êtes pas connecté";
}

