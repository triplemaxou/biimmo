<?php
ini_set('display_errors', '1');
require_once(plugin_dir_path(__FILE__) . "../config.php");


if (isset($_REQUEST["id"])) {
	$id = $_REQUEST["id"];
	$annonce = new annonce($id);
//	pre($annonce);
	$annonce->purgeImages();
} else {
	?><p class="warning">id seems to be uninitialized</p><?php
}
