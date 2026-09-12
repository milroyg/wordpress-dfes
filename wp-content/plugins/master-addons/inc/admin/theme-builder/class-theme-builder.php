<?php
namespace MasterAddons\Inc\Admin\Theme_Builder;

defined( 'ABSPATH' ) || exit;

if( !class_exists(__NAMESPACE__ . '\Theme_Builder') ){

	class Theme_Builder{

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
			$this->jltma_plugin_path = \JLTMA_PATH;
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
	            self::ensure_elementor_frontend_registered();
	            $css_file = new \Elementor\Core\Files\CSS\Post( $content_id );
	            $css_file->enqueue();
	        }
	    }

		public static function render_elementor_content($content_id){
			$elementor_instance = \Elementor\Plugin::instance();
			self::ensure_elementor_frontend_registered();
			return $elementor_instance->frontend->get_builder_content_for_display( $content_id , true);
		}

		/**
		 * Register Elementor's base frontend styles so the elementor-post-{id}
		 * handle enqueued for a theme-builder template has its elementor-frontend
		 * dependency satisfied.
		 *
		 * A theme-builder header/footer can render on a page (or in the editor)
		 * whose main query is not Elementor content, in which case Elementor never
		 * ran register_styles() and elementor-frontend is unregistered -- enqueuing
		 * the post CSS against it triggers a WP 6.9.1+ doing_it_wrong notice.
		 * Guarded by wp_style_is() so it is a no-op once Elementor has registered.
		 *
		 * @return void
		 */
		public static function ensure_elementor_frontend_registered(){
			if ( ! class_exists( '\Elementor\Plugin' ) ) {
				return;
			}

			$frontend = \Elementor\Plugin::instance()->frontend;

			if ( ! wp_style_is( 'elementor-frontend', 'registered' ) ) {
				$frontend->register_styles();
			}
			if ( ! wp_style_is( 'elementor-frontend', 'enqueued' ) ) {
				wp_enqueue_style( 'elementor-frontend' );
			}
		}

	    public static function get_instance() {
	        if ( is_null( self::$_instance ) ) {
	            self::$_instance = new self();
	        }
	        return self::$_instance;
	    }
	}
}
