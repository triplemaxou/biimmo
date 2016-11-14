<?php

if(!isset($from)){
	$from = $_REQUEST["from"];
}
if(!isset($to)){
	$to = $_REQUEST["to"];
}

ini_set('display_errors', '1');
//require_once("/web/clients/lemdev/www.lemaistre-immo.fr/wp-content/plugins/biimmo/config.php");
//annonce_image::autoTable(true);
$nb = rand();
$prefix = "ajax_import id:$nb";

bii_custom_log("Import des données ".$from." à ".$to,$prefix);


$logs = bii_items::fromXML("", $from, $to, $liste_identifiant1);
$nb_err = $logs["errors"];
$nb_add = $logs["added"];
$nb_edit = $logs["edit"];
$nb_arch = $logs["archive"];
$log = $logs["log"];

$subject = utf8_decode(get_bloginfo("name") . " import des données e:$nb_err a:$nb_add m:$nb_edit a:$nb_arch");
$message = "$log";
update_option("bii_last_paserelle",time());
update_option("bii_last_paserelle_".$from."_".$to,time());
//mail("t.lecrosnier@hubb.fr", $subject, $message);
mail("t.poisson@hubb.fr", $subject, $message);
bii_custom_log("Fin import",$prefix);