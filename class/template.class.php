<?php

class template {

	protected $plugin_slug;
	private static $instance;
	protected $templates;

	public static function get_instance() {
		if (null == self::$instance) {
			self::$instance = new template();
		}
		return self::$instance;
	}

	private function __construct() {

		$this->templates = array();
		add_filter(
			'page_attributes_dropdown_pages_args', array($this, 'register_project_templates')
		);

		add_filter(
			'wp_insert_post_data', array($this, 'register_project_templates')
		);

		add_filter(
			'template_include', array($this, 'view_project_template')
		);

//		// Produit .js.
//		wp_enqueue_script('kompromis-script', plugins_url("vazard") . '/js/produit.js', array('jquery'), false, true);
//		// Loads bootstrap.
//		wp_enqueue_style('bootstrap', plugins_url("vazard") . '/css/bootstrap.css', array(), false);
//		wp_enqueue_style('bootstrap', plugins_url("vazard") . '/css/bootstrap-1.css', array(), false);
//		// Loads font awesome.
//		wp_enqueue_style('font-awesome', plugins_url("wem-immo") . '/css/font-awesome.min.css', array(), false);
//		// main stylesheet.
//		wp_enqueue_style('vazard-style', plugins_url("vazard") . '/front/css/style.css', array(), false);


//		$this->templates = array(
//			'../front/liste-sous-categories.php' => "Liste sous catégories",
//			'../front/liste-produits.php' => "Liste Produits",
//			'../front/produit.php' => "Produit détail",
//			'../front/liste-produits-style.php' => "Liste de produits par style",
//			'../front/liste-produits-collection.php' => "Liste de produits par collection",
//			'../front/liste-produits-aubaines.php' => "Liste des produits soldés",
//		);
	}

	public function register_project_templates($atts) {
		$cache_key = 'page_templates-' . md5(get_theme_root() . '/' . get_stylesheet());

		$templates = wp_get_theme()->get_page_templates();
		if (empty($templates)) {
			$templates = array();
		}

		wp_cache_delete($cache_key, 'themes');
		$templates = array_merge($templates, $this->templates);
		wp_cache_add($cache_key, $templates, 'themes', 1800);

		return $atts;
	}

	public function view_project_template($template) {
		global $post;
		if (!isset($this->templates[get_post_meta(
					$post->ID, '_wp_page_template', true
			)])) {
			return $template;
		}

		$file = plugin_dir_path(__FILE__) . get_post_meta(
				$post->ID, '_wp_page_template', true
		);

		if (file_exists($file)) {
			return $file;
		} else {
			echo $file;
		}

		return $template;
	}

}

add_action('plugins_loaded', array('template', 'get_instance'));
