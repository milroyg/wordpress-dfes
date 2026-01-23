<?php
namespace MasterHeaderFooter;

defined( 'ABSPATH' ) || exit;

if( !class_exists('Master_Header_Footer') ){

	class Master_Header_Footer{

		public $dir;

		public $url;

		private static $plugin_path;

		private static $plugin_url;

		private static $_instance = null;

		public $jltma_plugin_path;

		const MINIMUM_PHP_VERSION = '5.6';

	    const MINIMUM_ELEMENTOR_VERSION = '3.5.0';

		private static $plugin_name = 'Master Header Footer & Comment Form Builder';

	    public function __construct(){
			$this->jltma_plugin_path = JLTMA_PATH;
			$this->jltma_include_files();

	        add_action('admin_footer', [$this, 'jltma_header_footer_modal_view']);
    	}

		public function jltma_include_files(){
			include $this->jltma_plugin_path . 'inc/admin/theme-builder/inc/cpt.php';
			include $this->jltma_plugin_path . 'inc/admin/theme-builder/inc/api/rest-api.php';
			include $this->jltma_plugin_path . 'inc/admin/theme-builder/inc/api/cpt-api.php';
			include $this->jltma_plugin_path . 'inc/admin/theme-builder/inc/cpt-hooks.php';
			include $this->jltma_plugin_path . 'inc/admin/theme-builder/inc/jltma-activator.php';
			include $this->jltma_plugin_path . 'inc/admin/theme-builder/inc/theme-builder-assets.php';
			include $this->jltma_plugin_path . 'inc/admin/theme-builder/inc/api/handler-api.php';
			include $this->jltma_plugin_path . 'inc/admin/theme-builder/inc/api/select2-api.php';
			include $this->jltma_plugin_path . 'inc/admin/theme-builder/inc/comments/class-comments-builder.php';
			
		}

		public function jltma_header_footer_modal_view(){
			$screen = get_current_screen();
			if($screen->id == 'edit-master_template'){
				include_once $this->jltma_plugin_path . 'inc/admin/theme-builder/inc/view/modal-options.php';
			}
		}


	    public static function render_elementor_content_css($content_id){
	        if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
	            $css_file = new \Elementor\Core\Files\CSS\Post( $content_id );
	            $css_file->enqueue();
	        }
	    }

		public static function render_elementor_content($content_id){
			$elementor_instance = \Elementor\Plugin::instance();
			return $elementor_instance->frontend->get_builder_content_for_display( $content_id , true);
		}

	    public static function get_instance() {
	        if ( is_null( self::$_instance ) ) {
	            self::$_instance = new self();
	        }
	        return self::$_instance;
	    }
	}
}

\MasterHeaderFooter\Master_Header_Footer::get_instance();
