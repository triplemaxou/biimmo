<?php

require_once(plugin_dir_path(__FILE__) . "../config.php");

$liste_reload = annonce::liste_reload();
pre($liste_reload,"green");
foreach ($liste_reload as $item) {
	$id = $item->id();
	
	$photos =annonce_image::all_id("id_annonce = $id");
	pre($photos,"blue");
	$attid1 = 1183;
	$idpost = $item->id_post();
	foreach ($photos as $id_photo) {
		$ai = new annonce_image($id_photo);
		$attid = $ai->addAttachement($idpost);
		postmeta::add($idpost, "REAL_HOMES_property_images", $attid);
		
		$photo_url = $ai->photo();
		pre($photo_url,"orange");
		if (strpos($photo_url, "-1") !== false && strpos($photo_url, "-10") === false) {
			pre($photo_url,"green");
			$ai1 = $ai;
			$attid1 = $ai1->attach_id();
		}
		
	}
	delete_post_thumbnail($idpost);
	set_post_thumbnail($idpost, $attid1);
	if ($item->nouveaute()) {
		postmeta::add($idpost, "REAL_HOMES_slider_image", $attid1);
	}
	if ($item->coupdecoeur()) {
		postmeta::add($idpost, "REAL_HOMES_attachments", $attid1);
	}
}