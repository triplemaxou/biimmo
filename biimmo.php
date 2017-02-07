<?php

/*
  Plugin Name: Biimmo
  Description: Gestion du carnet de biens d'une agence immobilière.
  Version: 1.8
  Author: Biilink Agency
  Author URI: http://biilink.com/
  License: GPL2
 */

define('Biimmo_version', '1.7');
define('bii_immo_path', plugin_dir_path(__FILE__));
define('bii_immo_url', plugin_dir_url(__FILE__));

//Plugin biidebug, ajout de fonctions
require_once(plugin_dir_path(__FILE__) . "/plugins/biidebug/biidebug.php");
//Plugin biibdd, ajout de fonctions bases de données
require_once(plugin_dir_path(__FILE__) . "/plugins/bii_bdd/bii_bdd.php");
//Plugin biiadvanced admin, ajout de fonctionnalités ajax sur l'interface d'admin
require_once(plugin_dir_path(__FILE__) . "/plugins/biiadvanced-admin/biiadvanced-admin.php");
//Plugin biicheckseo, ajout de scripts permettant de vérifier la SEO des pages parcourues
require_once(plugin_dir_path(__FILE__) . "/plugins/biicheckseo/biicheckseo.php");
//Plugin bii_advanced_shortcodes, ajout de shortcodes
require_once(plugin_dir_path(__FILE__) . "/plugins/biiadvanced_shortcodes/biiadvanced_shortcodes.php");
//Plugin bii_cron
require_once(plugin_dir_path(__FILE__) . "/plugins/bii_cron/bii_cron.php");

function bii_enqueueCSS() {
	wp_enqueue_style('bootstrap', plugins_url('css/bootstrap.css', __FILE__));
//	wp_enqueue_style('bootstrap-theme', plugins_url('css/bootstrap-theme.css', __FILE__));
	wp_enqueue_style('font-awesome', plugins_url('css/font-awesome.min.css', __FILE__));
	wp_enqueue_style('stylepage', plugins_url('css/style.css', __FILE__));
}

bii_enqueueCSS();

function bii_enqueueJS() {
//	wp_enqueue_script('util', plugins_url('js/util.js', __FILE__), array('jquery'), false, true);
	if (!get_option("bii_hideseo")) {
		update_option("bii_hideseo", 0);
		wp_enqueue_script('seoscript', plugins_url('plugins/biicheckseo/js/seo.js', __FILE__), array('jquery', 'util'), false, true);
	}
//	wp_enqueue_script('lazyload2', plugins_url('js/lazyload.js', __FILE__), array('jquery'), false, true);
//	wp_enqueue_script('manual-lazyload', plugins_url('js/manual-lazyload.js', __FILE__), array('jquery', 'lazyload2', 'util'), false, true);
}

bii_enqueueJS();
require_once(plugin_dir_path(__FILE__) . "config.php");

function bii_menu() {
	add_menu_page(__(global_class::wp_slug_menu()), __(global_class::wp_titre_menu()), global_class::wp_min_role(), global_class::wp_nom_menu(), global_class::wp_dashboard_page(), global_class::wp_dashicon_menu());

//	negociateur::displaySousMenu();
//	agence::displaySousMenu();
	annonce::displaySousMenu();
	annonce_image::displaySousMenu();
	villes_france::displaySousMenu();
}

add_action('admin_menu', 'bii_menu');

function bii_dashboard() {
	wp_enqueue_script('admin-init', plugins_url('/admin/js/dashboard.js', __FILE__), array('jquery'), null, true);
	wp_enqueue_style('bii-admin-css', plugins_url('/admin/css/admin.css', __FILE__));
	include('admin/dashboard.php');
}

function bii_ajax_dezip() {
	include("ajax/ajax_dezip.php");
	die();
}

function bii_ajax_purge_pictures() {
	include("ajax/ajax_purge_pictures.php");
	die();
}

function bii_ajax_import() {
	include("ajax/ajax_import.php");
	die();
}

function bii_ajax_import_test() {
	do_action('bii_import', 0, 1);
	do_action('bii_import', 2, 3);
	die();
}

function bii_ajax_import_wparams($from, $to) {
	bii_custom_log("Crons Import des données " . $from . " à " . $to);
	update_option("bii_passerelle_ids", "");
    
	$logs = bii_items::fromXML("", $from, $to);
	$nb_err = $logs["errors"];
	$nb_add = $logs["added"];
	$nb_edit = $logs["edit"];
	$nb_arch = $logs["archive"];
	$log = $logs["log"];

	$subject = utf8_decode(get_bloginfo("name") . " import des données $from à $to : e:$nb_err a:$nb_add m:$nb_edit a:$nb_arch");
	$message = utf8_decode("$log");
	update_option("bii_last_paserelle", time());
	update_option("bii_last_paserelle_" . $from . "_" . $to, time());
	mail("web@groupejador.fr", $subject, $message);
    bii_custom_log("Fin import ".date('Y-m-d H:i:s'));
}

function bii_ajax_change_value() {
	include("ajax/ajax_value.php");
	die();
}

function bii_register_request() {
	include("ajax/ajax_registerrequest.php");
	die();
}

function bii_ajax_delete() {
	include("ajax/ajax_delete.php");
	die();
}

function bii_ajax_count_doublons() {
	include("ajax/ajax_count_doublons.php");
	die();
}

function bii_ajax_delete_doublons() {
	include("ajax/ajax_delete_doublons_mail.php");
	die();
}

function bii_purge_archive() {
    annonce::purgeArchive();
}

function bii_ajax_delete_doublons_mail() {
	$count = annonce::toDoDoublons("count");
	$liste = annonce::toDoDoublons("return");

	$subject = utf8_decode(get_bloginfo("name") . " Supression des doublons");
	$message = "$count doublons \n\r";
	$refprec = 0;
	foreach ($liste as $id => $ref) {
		$message.= "\n\r$id $ref";
		if ($refprec == $ref) {
			$message.= " Supprimmé";
		}
		$refprec = $ref;
	}
	annonce::toDoDoublons("delete");

	mail("web@groupejador.fr", $subject, $message);
}

function bii_ajax_reload() {
	include("ajax/ajax_reload_pictures.php");
	die();
}

function bii_ajax_reload_pictures() {
    bii_custom_log("[INFO BII_CRON] Reload pictures start");
    $subject = utf8_decode(get_bloginfo("name") . " Reload pictures");
	$message = "";
    $liste_reload = annonce::liste_reload();
    
    foreach ($liste_reload as $item) {
        $id = $item->id();
        
        $photos =annonce_image::all_id("id_annonce = $id");
        $message .= "Annonce : $id : ".count($photos)." photos \n\r";
        $attid1 = 1183;
        $idpost = $item->id_post();
        foreach ($photos as $id_photo) {
            $ai = new annonce_image($id_photo);
            $attid = $ai->addAttachement($idpost);
            postmeta::add($idpost, "REAL_HOMES_property_images", $attid);

            $photo_url = $ai->photo();
    
            if (strpos($photo_url, "-1") !== false && strpos($photo_url, "-10") === false) {
    
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
    mail("web@groupejador.fr", $subject, $message);
    bii_custom_log("[INFO BII_CRON] Reload pictures end");
}

add_action('wp_ajax_bii_dezip', 'bii_ajax_dezip');
add_action('wp_ajax_nopriv_bii_dezip', 'bii_ajax_dezip');


add_action('bii_import', 'bii_ajax_import_wparams', 10, 2);
add_action('bii_delete_doublons_mail', 'bii_ajax_delete_doublons_mail', 10, 2);
add_action('wp_ajax_bii_import-test', 'bii_ajax_import_test');
add_action('wp_ajax_bii_import', 'bii_ajax_import');
add_action('wp_ajax_nopriv_bii_import', 'bii_ajax_import');

add_action('wp_ajax_bii_change_value', 'bii_ajax_change_value');
add_action('wp_ajax_nopriv_bii_change_value', 'bii_ajax_change_value');

add_action('wp_ajax_bii_register_request', 'bii_register_request');
add_action('wp_ajax_nopriv_bii_register_request', 'bii_register_request');

add_action('wp_ajax_bii_count_doublons', 'bii_ajax_count_doublons');
add_action('wp_ajax_nopriv_bii_count_doublons', 'bii_ajax_count_doublons');

add_action('wp_ajax_bii_delete_doublons', 'bii_ajax_delete_doublons');
add_action('wp_ajax_nopriv_bii_delete_doublons', 'bii_ajax_delete_doublons');

add_action('wp_ajax_bii_delete', 'bii_ajax_delete');
add_action('wp_ajax_nopriv_bii_delete', 'bii_ajax_delete');

add_action('wp_ajax_bii_ajax_purge_pictures', 'bii_ajax_purge_pictures');
add_action('wp_ajax_nopriv_bii_ajax_purge_pictures', 'bii_ajax_purge_pictures');

add_action('wp_ajax_bii_purge_archive', 'bii_purge_archive');
add_action('bii_purge_archive', 'bii_purge_archive');

add_action('wp_ajax_bii_ajax_reload', 'bii_ajax_reload');
add_action('wp_ajax_nopriv_bii_ajax_reload', 'bii_ajax_reload');
add_action('bii_reload_pictures', 'bii_ajax_reload_pictures', 10, 2);

register_deactivation_hook(__FILE__, 'bii_cron');
register_activation_hook(__FILE__, 'bii_cron');
register_activation_hook(__FILE__, 'bii_4daily_event');

register_deactivation_hook(__FILE__, 'bii_cron_2');
register_activation_hook(__FILE__, 'bii_cron_2');

register_deactivation_hook(__FILE__, 'bii_cron_3');
register_activation_hook(__FILE__, 'bii_cron_3');

register_deactivation_hook(__FILE__, 'bii_cron_4');
register_activation_hook(__FILE__, 'bii_cron_4');

register_deactivation_hook(__FILE__, 'bii_cron_5');
register_activation_hook(__FILE__, 'bii_cron_5');

register_deactivation_hook(__FILE__, 'bii_cron_delete_doublons');
register_activation_hook(__FILE__, 'bii_cron_delete_doublons');
