<?php

/*
  Plugin Name: Biimmo
  Description: Gestion du carnet de biens d'une agence immobilière.
  Version: 1.2
  Author: Biilink Agency
  Author URI: http://biilink.com/
  License: GPL2
 */

define('Biimmo_version', '1.2');

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

add_action('wp_ajax_bii_dezip', 'bii_ajax_dezip');
add_action('wp_ajax_nopriv_bii_dezip', 'bii_ajax_dezip');

add_action('wp_ajax_bii_import', 'bii_ajax_import');
add_action('wp_ajax_nopriv_bii_import', 'bii_ajax_import');

add_action('wp_ajax_bii_change_value', 'bii_ajax_change_value');
add_action('wp_ajax_nopriv_bii_change_value', 'bii_ajax_change_value');

add_action('wp_ajax_bii_register_request', 'bii_register_request');
add_action('wp_ajax_nopriv_bii_register_request', 'bii_register_request');

add_action('wp_ajax_bii_delete', 'bii_ajax_delete');
add_action('wp_ajax_nopriv_bii_delete', 'bii_ajax_delete');

add_action('wp_ajax_bii_ajax_purge_pictures', 'bii_ajax_purge_pictures');
add_action('wp_ajax_nopriv_bii_ajax_purge_pictures', 'bii_ajax_purge_pictures');
