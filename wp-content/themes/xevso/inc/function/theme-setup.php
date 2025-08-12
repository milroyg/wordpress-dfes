<?php 

if ( ! function_exists( 'xevso_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function xevso_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on xevso, use a find and replace
		 * to change 'xevso' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'xevso', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );
		add_image_size( 'xevso-project-image', 615, 450, true);
		add_image_size( 'xevso-project-full', 1170, 400, array( 'center', 'center' ) );
		add_image_size( 'xevso-project-image2', 360, 500, array( 'center', 'center' ) );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'main-menu' => esc_html__( 'Main Menu', 'xevso' ),
			'footer-menu' => esc_html__( 'Footer Menu', 'xevso' ),
		) );
		function xevso_add_editor_styles_sub_dir() {
			add_editor_style( trailingslashit( get_template_directory_uri() ) . 'assets/css/editor-style.css' );
		  }
		  add_action( 'after_setup_theme', 'xevso_add_editor_styles_sub_dir' );
		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );
		add_theme_support( 'post-formats', array(
			'gallery',
			'quote',
			'video',
			'image',
			'link',
			'audio',
		) );
		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background' );
		add_theme_support( 'custom-background', apply_filters( 'xevso_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		) ) );

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );
		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support( 'custom-logo', array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		) );
	}
endif;
add_action( 'after_setup_theme', 'xevso_setup' );


/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function xevso_content_width() {
	// This variable is intended to be overruled from themes.
	// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$GLOBALS['content_width'] = apply_filters( 'xevso_content_width', 640 );
}
add_action( 'after_setup_theme', 'xevso_content_width', 0 );