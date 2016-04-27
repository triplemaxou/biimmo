<?php
/*
  Plugin Name: Biimmo
  Description: Gestion du carnet de biens d'une agence immobilière.
  Version: 1.1
  Author: Biilink Agency
  Author URI: http://biilink.com/
  License: GPL2
 */

function bii_enqueueCSS() {
	wp_enqueue_style('bootstrap', plugins_url('css/bootstrap.css', __FILE__));
//	wp_enqueue_style('bootstrap-theme', plugins_url('css/bootstrap-theme.css', __FILE__));
	wp_enqueue_style('font-awesome', plugins_url('css/font-awesome.min.css', __FILE__));
	wp_enqueue_style('stylepage', plugins_url('css/style.css', __FILE__));
}

bii_enqueueCSS();
function bii_enqueueJS() {
	wp_enqueue_script('util', plugins_url('js/util.js', __FILE__), array('jquery'), false, true);
	wp_enqueue_script('seoscript', plugins_url('js/seo.js', __FILE__), array('jquery','util'), false, true);
	wp_enqueue_script('lazyload2', plugins_url('js/lazyload.js', __FILE__), array('jquery'), false, true);
	wp_enqueue_script('manual-lazyload', plugins_url('js/manual-lazyload.js', __FILE__), array('jquery','lazyload2','util'), false, true);
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
	include('admin/dashboard.php');
}

function bii_ajax_dezip() {
	include("ajax/ajax_dezip.php");
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

function bii_showlogs() {
	?>
	<script type="text/javascript" src="http://l2.io/ip.js?var=myip"></script>
	<script type="text/javascript">
		var ajaxurl = '<?= admin_url('admin-ajax.php'); ?>';
		var bloginfourl = '<?= get_bloginfo("url") ?>';
		var bii_showlogs = false;
		var ip_client = myip;
		if (ip_client == "77.154.194.84") {
			bii_showlogs = true;
		}
	</script>
	<?php
}

add_action('wp_head', 'bii_showlogs');


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

/* Retirer emojis */

remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('admin_print_styles', 'print_emoji_styles');
