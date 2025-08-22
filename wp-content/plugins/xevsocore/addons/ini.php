<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
final class xevso_extension {
	const VERSION = '1.0.0';

	const MINIMUM_ELEMENTOR_VERSION = '2.0.0';

	const MINIMUM_PHP_VERSION = '7.0';

	private static $_instance = null;

	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	public function __construct() {

		add_action( 'init', [ $this, 'i18n' ] );
		add_action( 'plugins_loaded', [ $this, 'init' ] );

	}
	

	public function i18n() {
		load_plugin_textdomain( 'xevsocore' );
	}
	public function init() {
		require_once( __DIR__ . '/control/check-notice.php' );
		require_once( __DIR__ . '/control/css-load.php' );
	}
	public function admin_notice_missing_main_plugin() {
		require_once( __DIR__ . '/control/messing-notice.php' );
	}
	public function admin_notice_minimum_elementor_version() {
		require_once( __DIR__ . '/control/mimv-notice.php' );
	}
	public function admin_notice_minimum_php_version() {
		require_once( __DIR__ . '/control/mimvp-notice.php' );
	}
	public function init_widgets() {
		// Include Widget files
		require_once( __DIR__ . '/widgets/clients.php' );
		require_once( __DIR__ . '/widgets/about.php' );
		require_once( __DIR__ . '/widgets/title.php' );
		require_once( __DIR__ . '/widgets/service.php' );
		require_once( __DIR__ . '/widgets/progress.php' );
		require_once( __DIR__ . '/widgets/pricing.php' );
		require_once( __DIR__ . '/widgets/pricing-two.php' );
		require_once( __DIR__ . '/widgets/contact-box.php' );
		require_once( __DIR__ . '/widgets/project.php' );
		require_once( __DIR__ . '/widgets/project-two.php' );
		require_once( __DIR__ . '/widgets/buttons.php' );
		require_once( __DIR__ . '/widgets/team.php' );
		require_once( __DIR__ . '/widgets/team-two.php' );
		require_once( __DIR__ . '/widgets/testimonial.php' );
		require_once( __DIR__ . '/widgets/blog.php' );
		require_once( __DIR__ . '/widgets/contact-message.php' );
		require_once( __DIR__ . '/widgets/counter.php' );
		require_once( __DIR__ . '/widgets/counter-two.php' );
		require_once( __DIR__ . '/widgets/support.php' );
		require_once( __DIR__ . '/widgets/promo-content.php' );
		require_once( __DIR__ . '/widgets/contact-info.php' );
		require_once( __DIR__ . '/widgets/slider.php' );
		require_once( __DIR__ . '/widgets/icon-box.php' );
		require_once( __DIR__ . '/widgets/flipbox.php' );
		require_once( __DIR__ . '/widgets/shape.php' );
		require_once( __DIR__ . '/widgets/dot-shape.php' );
		require_once( __DIR__ . '/widgets/animatebtn.php' );
		require_once( __DIR__ . '/widgets/team_three.php' );
		require_once( __DIR__ . '/widgets/faq.php' );
		require_once( __DIR__ . '/widgets/service_new.php' );
		require_once( __DIR__ . '/widgets/featre_new.php' );
		require_once( __DIR__ . '/widgets/title_animation.php' );
		require_once( __DIR__ . '/widgets/magic.php' );
		require_once( __DIR__ . '/widgets/screenshot.php' );
		require_once( __DIR__ . '/widgets/blog-two.php' );
		require_once( __DIR__ . '/widgets/watch.php' );
		// Register widget
		
	}
	public function widget_styles() {
		require_once( __DIR__ . '/control/xevso-widget-style.php' );
	}
}
xevso_extension::instance();
