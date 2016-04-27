<?php
/*
  Plugin Name: BiiDebug
  Description: Ajoute des fonctions de débug, invisibles pour le public
  Version: 2.0.3
  Author: Biilink Agency
  Author URI: http://biilink.com/
  License: GPL2
 */
define('bii_debug_version', '2.0.3');

include_once("functions.php");

function biidebug_enqueueJS() {
	wp_enqueue_script('util', plugins_url('js/util.js', __FILE__), array('jquery'), false, true);

	wp_enqueue_script('lazyload2', plugins_url('js/lazyload.js', __FILE__), array('jquery'), false, true);
	wp_enqueue_script('manual-lazyload', plugins_url('js/manual-lazyload.js', __FILE__), array('jquery', 'lazyload2', 'util'), false, true);
}

biidebug_enqueueJS();
if (!(get_option("bii_medium_width"))) {
	update_option("bii_medium_width", 1050);
}
if (!(get_option("bii_small_width"))) {
	update_option("bii_small_width", 985);
}
if (!(get_option("bii_xsmall_width"))) {
	update_option("bii_xsmall_width", 767);
}
if (!(get_option("bii_xxsmall_width"))) {
	update_option("bii_xxsmall_width", 479);
}
if (!(get_option("bii_ipallowed"))) {
	update_option("bii_ipallowed", "77.154.194.84");
}
if (get_option("bii_disallow_emojis") === false) {
	update_option("bii_disallow_emojis", "1");
}

function bii_showlogs() {
	?>
	<script type="text/javascript" src="http://l2.io/ip.js?var=myip"></script>
	<script type="text/javascript">
		var ajaxurl = '<?= admin_url('admin-ajax.php'); ?>';
		var bloginfourl = '<?= get_bloginfo("url") ?>';
		var bii_showlogs = false;
		var ip_client = myip;
		if (ip_client == "<?= get_option("bii_ipallowed"); ?>") {
			bii_showlogs = true;
		}
		var bii_medium = "(max-width: <?= get_option("bii_medium_width"); ?>px";
		var bii_small = "(max-width: <?= get_option("bii_small_width"); ?>px";
		var bii_xsmall = "(max-width: <?= get_option("bii_xsmall_width"); ?>px";
		var bii_xxsmall = "(max-width: <?= get_option("bii_xxsmall_width"); ?>px";
	</script>
	<?php
}

add_action('wp_head', 'bii_showlogs');
add_action('admin_head', 'bii_showlogs');

add_action("bii_informations", function() {
	?>
	<tr><td>Les emojis sont  </td><td><?= bii_makebutton("bii_disallow_emojis", 1, 0, true); ?></td></tr>
	<?php
});

function bii_canshow_debug() {
	return $_SERVER["REMOTE_ADDR"] == get_option("bii_ipallowed");
}

/* Retirer emojis */
if (get_option("bii_disallow_emojis")) {
	remove_action('wp_head', 'print_emoji_detection_script', 7);
	remove_action('wp_print_styles', 'print_emoji_styles');

	remove_action('admin_print_scripts', 'print_emoji_detection_script');
	remove_action('admin_print_styles', 'print_emoji_styles');
}
add_action("bii_options_title", function() {
	?>
	<li role="presentation" class="hide-relative active hide-publier" data-relative="pl-Informations"><i class="fa fa-info"></i> Informations</li>
	<li role="presentation" class="hide-relative " data-relative="pl-Biidebug"><i class="fa fa-cogs"></i> Biidebug</li>
	<li role="presentation" class="hide-relative hide-publier" data-relative="pl-Shortcodes"><i class="fa fa-cog"></i> Shortcodes</li>
	<?php
}, 1);

add_action("bii_options", function() {
	?>
	<div class="col-xxs-12 pl-Informations bii_option">
		<table>
			<?php do_action("bii_informations"); ?>				
		</table>
	</div>
	<div class="col-xxs-12 pl-Biidebug bii_option hidden">
		<?php
		bii_makestuffbox("bii_medium_width", "Pixels maximum md", "number", "col-xxs-12 col-sm-6 col-md-3");
		bii_makestuffbox("bii_small_width", "Pixels maximum sm", "number", "col-xxs-12 col-sm-6 col-md-3");
		bii_makestuffbox("bii_xsmall_width", "Pixels maximum xs", "number", "col-xxs-12 col-sm-6 col-md-3");
		bii_makestuffbox("bii_xxsmall_width", "Pixels maximum xxs", "number", "col-xxs-12 col-sm-6 col-md-3");
		bii_makestuffbox("bii_ipallowed", "Adresse IP de débug", "text", "col-xxs-12 col-sm-6 col-md-3");
		?>
	</div>
	<div class="col-xxs-12 pl-Shortcodes bii_option hidden">
		<div class="col-xxs-12">
			<h3>Base</h3>
			<table>
				<?php do_action("bii_base_shortcodes"); ?>						
			</table>
		</div>
		<div class="col-xxs-12">
			<h3>Ignition Desk</h3>
			<table>
				<?php do_action("bii_specific_shortcodes"); ?>						
			</table>
		</div>
	</div>
	<?php
}, 1);

add_action("bii_options_submit", function() {
	$tableaucheck = ["bii_medium_width", "bii_small_width", "bii_xsmall_width", "bii_xxsmall_width"];
	foreach ($tableaucheck as $itemtocheck) {
		if (isset($_POST[$itemtocheck])) {
			update_option($itemtocheck, $_POST[$itemtocheck]);
		}
	}
}, 5);
if (bii_canshow_debug()) {
	add_action("bii_options_title", function() {
		?>
		<li role="presentation" class="hide-relative hide-publier" data-relative="pl-zdt"><i class="fa fa-wrench"></i> Zone de test</li>
		<?php
	}, 99);
}
