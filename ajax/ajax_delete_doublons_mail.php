<?php

ini_set('display_errors', '1');
require_once(plugin_dir_path(__FILE__) . "../config.php");
$count = annonce::toDoDoublons("count");
$liste = annonce::toDoDoublons("return");

$subject = utf8_decode(get_bloginfo("name") . " Supression des doublons");
$message = "$count doublons \n\r";
$refprec = 0;
foreach($liste as $id=>$ref){
	$message.= "\n\r$id $ref";
	if($refprec == $ref){
		$message.= " Supprimmé";
	}
	$refprec = $ref;
}
annonce::toDoDoublons("delete");

mail("t.lecrosnier@hubb.fr", $subject, $message);
mail("t.poisson@hubb.fr", $subject, $message);


