<?php

ini_set('display_errors', '1');
require_once("/web/clients/lemdev/www.lemaistre-immo.fr/wp-content/plugins/biimmo/config.php");
//annonce_image::autoTable(true);



$logs = bii_items::fromXML("", $_REQUEST["from"], $_REQUEST["to"], $liste_identifiant1);
$nb_err = $logs["errors"];
$nb_add = $logs["added"];
$nb_edit = $logs["edit"];
$nb_arch = $logs["archive"];
$log = $logs["log"];

$subject = utf8_decode(get_bloginfo("name") . " import des données e:$nb_err a:$nb_add m:$nb_edit a:$nb_arch");
$message = "$log";
mail("t.lecrosnier@hubb.fr", $subject, $message);
mail("t.poisson@hubb.fr", $subject, $message);

