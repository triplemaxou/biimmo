<?php

//ini_set('display_errors', '1');
require_once(plugin_dir_path(__FILE__) . "../config.php");
unset($_REQUEST["action"]);

$cu = wp_get_current_user();
if ($cu->ID) {
    $ser = serialize($_REQUEST);
	$rep = usermeta::add($cu->ID, "requete_sauvegardee", $ser, false);
	if (function_exists("rocket_clean_user")) {
		rocket_clean_user($cu->ID);
	}
    echo "Recherche sauvegardée";
} else {
    
    if (isset($_REQUEST['save_email']) && filter_var($_REQUEST['save_email'], FILTER_VALIDATE_EMAIL)) {
        $tel = !empty($_REQUEST['save_telephone']) ? $_REQUEST['save_telephone'] : '';
        $nom = !empty($_REQUEST['save_nom']) ? $_REQUEST['save_nom'] : '';
        $prenom = !empty($_REQUEST['save_prenom']) ? $_REQUEST['save_prenom'] : '';
        
        $idUser = users::add('newsletter', $_REQUEST['save_email'], $tel, $nom, $prenom);
        if ($idUser != false) {
            unset($_REQUEST['save_email']);
            unset($_REQUEST['save_telephone']);
            unset($_REQUEST['save_nom']);
            unset($_REQUEST['save_prenom']);
            unset($_REQUEST['g-recaptcha-response']);
            $ser = serialize($_REQUEST);
            $rep = usermeta::add($idUser, "requete_sauvegardee", $ser, false);
            echo "Recherche sauvegardée";
        } else {
            echo "Impossible de créer l'utilisateur. Cette adresse email existe déjà surement.";
        }
    }
}