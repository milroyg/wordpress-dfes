<?php

namespace MasterAddons\Inc\Classes;

use MasterAddons\Master_Elementor_Addons;

class Master_Addons_Assets
{
    private static $instance = null;

    public function __construct()
    {
        add_action('elementor/init', [$this, 'jltma_on_elementor_init'], 0);

        // Enqueue Styles and Scripts
        add_action('wp_enqueue_scripts', [$this, 'jltma_enqueue_scripts'], 100);
    }

    public function jltma_on_elementor_init()
    {
        // Elementor hooks
        $this->add_actions();
    }

    public function add_actions()
    {
        // Elementor Frontend: Register Styles/Scripts
        add_action('elementor/frontend/after_register_styles', [$this, 'jltma_register_frontend_styles']);
        add_action('elementor/frontend/after_register_scripts', [$this, 'jltma_register_frontend_scripts']);
        
        // Elementor Frontend: Enqueue Styles/Scripts 
        add_action('elementor/frontend/after_enqueue_scripts', [$this, 'jltma_enqueue_scripts']);
        // add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'jltma_editor_scripts_enqueue_js' ]);

        // Elementor Editor: Enqueue Styles/Scripts
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'jltma_editor_scripts_js'], 100);
        add_action('elementor/editor/after_enqueue_styles', [$this, 'jltma_enqueue_preview_scripts'], 100);

        // Elementor Preview: Enqueue Styles/Scripts
        add_action('elementor/preview/enqueue_styles', [$this, 'jltma_enqueue_preview_scripts'], 100);
        add_action('elementor/preview/enqueue_scripts', [$this, 'jltma_enqueue_preview_scripts'], 100);
    }


    /** Enqueue Elementor Editor Styles */

    public function jltma_editor_scripts_js()
    {
        wp_enqueue_style('master-addons-editor', JLTMA_ASSETS . 'css/master-addons-editor.css', [], JLTMA_VER);
        wp_enqueue_script( 'jltma-macy', JLTMA_ASSETS . 'vendor/macy/macy.js', ['jquery'], JLTMA_VER, true );
        wp_enqueue_script('master-addons-editor', JLTMA_ADMIN_ASSETS . 'js/editor.js', ['jquery', 'jltma-macy'], JLTMA_VER, true);
        // wp_enqueue_script('master-addons-editor', JLTMA_ADMIN_ASSETS . 'js/editor.js', ['jquery'], JLTMA_VER, true);

    }

    // Enqueue Preview Scripts
    public function jltma_enqueue_preview_scripts()
    {
        // wp_enqueue_style('ma-creative-buttons');
        wp_enqueue_script('jltma-timeline');

        // Enqueue TippyJS for tooltips in preview/editor
        wp_enqueue_style('jltma-tippy');
        wp_enqueue_script('jltma-popper');
        wp_enqueue_script('jltma-tippy');
    }


    // Register Frontend Styles
    public function jltma_register_frontend_styles()
    {
        $jltma_vendor_dir = JLTMA_ASSETS . 'vendor/';

        wp_register_style('gridder', JLTMA_ASSETS . 'vendor/gridder/css/jquery.gridder.min.css');
        wp_register_style('fancybox', JLTMA_ASSETS . 'vendor/fancybox/jquery.fancybox.min.css', ['master-addons-main-style']);
        wp_register_style('twentytwenty', JLTMA_ASSETS . 'vendor/image-comparison/css/twentytwenty.css');

        // Allow Pro to register styles after free styles
        do_action('master_addons/assets/register_styles');
    }


    // Enqueue Preview Scripts
    public function jltma_register_frontend_scripts()
    {

        $suffix           = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
        $jltma_vendor_dir = JLTMA_ASSETS . 'vendor/';

        // wp_register_script('ma-swiper', $jltma_vendor_dir . 'swiper/js/swiper-bundle.min.js', ['jquery'], JLTMA_VER, true);

        wp_register_script('ma-animated-headlines', JLTMA_ASSETS . 'js/animated-main.js', ['jquery'], JLTMA_VER, true);

        wp_register_script('master-addons-progressbar', JLTMA_ASSETS . 'js/loading-bar.js', ['jquery'], JLTMA_VER, true);

        wp_register_script('jquery-stats', JLTMA_ASSETS . 'js/jquery.stats.js', ['jquery'], JLTMA_VER, true);

        wp_register_script('jltma-owl-carousel', JLTMA_ASSETS . 'vendor/owlcarousel/owl.carousel.min.js', ['jquery'], JLTMA_VER, true);

        wp_register_script('gridder', JLTMA_ASSETS . 'vendor/gridder/js/jquery.gridder.min.js', ['jquery'], JLTMA_VER, true);

        wp_register_script('isotope', JLTMA_ASSETS . 'js/isotope.js', ['jquery'], JLTMA_VER, true);


        wp_register_script('jquery-rss', JLTMA_ASSETS . 'vendor/newsticker/js/jquery.rss.min.js', ['jquery'], JLTMA_VER, true);

        wp_register_script('ma-counter-up', JLTMA_ASSETS . 'js/counterup.min.js', ['jquery', 'master-addons-scripts'], JLTMA_VER, true);

        wp_register_script('ma-countdown', JLTMA_ASSETS . 'vendor/countdown/jquery.countdown.js', ['jquery'], JLTMA_VER, true);

        wp_register_script('jltma-table-of-content', JLTMA_ASSETS . 'vendor/jltma-table-of-content/jltma-table-of-content.js', ['jquery'], JLTMA_VER, true);

        wp_register_script('fancybox', JLTMA_ASSETS . 'vendor/fancybox/jquery.fancybox.min.js', ['jquery'], JLTMA_VER, true);

        wp_register_script('jltma-timeline', JLTMA_ASSETS . 'js/timeline.js', ['jquery'], JLTMA_VER, true);

        wp_register_script('jltma-tilt', JLTMA_ASSETS . 'vendor/tilt/tilt.jquery.min.js', ['jquery'], JLTMA_VER, true);

        // Swiper
        // wp_register_style('ma-swiper', $jltma_vendor_dir . 'swiper/css/swiper.min.css');

        // Tippy JS
        wp_register_style('jltma-tippy', $jltma_vendor_dir . 'tippyjs/css/tippy.css');
        wp_register_script('jltma-popper', $jltma_vendor_dir . 'popper.min.js', ['jquery'], JLTMA_VER, true);
        wp_register_script('jltma-tippy', $jltma_vendor_dir . 'tippyjs/js/tippy.min.js', ['jquery'], JLTMA_VER, true);
        wp_register_script('jltma-section-tooltip', JLTMA_ASSETS . 'js/extensions/ma-tooltips.js', ['jquery'], JLTMA_VER, true);

        // Particles
        wp_register_script('master-addons-particles', JLTMA_ASSETS . 'js/particles.min.js', ['jquery'], JLTMA_VER, true);

        // Vegas Background Slider
        wp_register_style('master-addons-vegas', JLTMA_ASSETS . 'vendor/vegas/vegas.min.css', [], JLTMA_VER);
        wp_register_script('master-addons-vegas', JLTMA_ASSETS . 'vendor/vegas/vegas.min.js', ['jquery'], JLTMA_VER, true);

        // Image Comparison
        wp_register_script('jquery-event-move', JLTMA_ASSETS . 'vendor/image-comparison/js/jquery.event.move.js', ['jquery'], JLTMA_VER, true);
        wp_register_script('twentytwenty', JLTMA_ASSETS . 'vendor/image-comparison/js/jquery.twentytwenty.js', ['jquery'], JLTMA_VER, true);

        // Toggle Content
        wp_register_script('jltma-toggle-content', JLTMA_ASSETS . 'vendor/toggle-content/toggle-content.js', ['jquery'], JLTMA_VER, true);


        // Advanced Animations
        // wp_register_script('jltma-floating-effects', JLTMA_URL . '/assets/vendor/floating-effects/floating-effects.js', array('ma-el-anime-lib', 'jquery'), JLTMA_VER);


        // Data Tables
        wp_register_script('jltma-data-table',  $jltma_vendor_dir . 'datatable/table.min.js', ['jquery'], JLTMA_VER, true);


        // iPhone Inline Video
        wp_register_script( 'iphone-inline-video', JLTMA_URL . $suffix . '.js', [], JLTMA_VER, true );
        wp_register_script('jltma-nav-menu', JLTMA_ASSETS . 'js/addons/jltma-nav-menu.js', ['jquery', 'elementor-frontend-modules'], JLTMA_VER, true);

        wp_register_script( 'jltma-macy', JLTMA_ASSETS . 'vendor/macy/macy.js', ['jquery'], JLTMA_VER, true );

        // Allow Pro to register scripts after free scripts
        do_action('master_addons/assets/register_scripts');
    }


    /**
     * Enqueue Plugin Styles and Scripts
     *
     */
    public function jltma_enqueue_scripts()
    {
        // Register Styles

        //Reveal
        wp_register_script('ma-el-reveal-lib', JLTMA_ASSETS . 'vendor/reveal/revealFx.js', ['jquery'], JLTMA_VER, true);
        wp_register_script('ma-el-anime-lib', JLTMA_ASSETS . 'vendor/anime/anime.min.js', ['jquery'], JLTMA_VER, true);

        //Rellax
        wp_register_script('ma-el-rellaxjs-lib', JLTMA_ASSETS . 'vendor/rellax/rellax.min.js', ['jquery'], JLTMA_VER, true);


        // Enqueue Styles
        wp_enqueue_style('master-addons-main-style', JLTMA_ASSETS . 'css/master-addons-styles.css');

        // Enqueue Scripts
        wp_enqueue_script('master-addons-plugins', JLTMA_ASSETS . 'js/plugins.js', ['jquery'], JLTMA_VER, true);
        wp_enqueue_script('master-addons-scripts', JLTMA_ASSETS . 'js/master-addons-scripts.js', ['jquery'], JLTMA_VER, true);


        $localize_data = array(
            'plugin_url'    => JLTMA_URL,
            'ajaxurl'       => admin_url('admin-ajax.php'),
            'nonce'           => 'master-addons-elementor',
        );
        wp_localize_script('master-addons-scripts', 'jltma_scripts', $localize_data);


        // Data Table localization
        $jltma_data_table_param = array(
            "lengthMenu"        => esc_html__('Display _MENU_ records per page', 'master-addons'),
            "zeroRecords"       => esc_html__('Nothing found - sorry', 'master-addons'),
            "info"              => esc_html__('Showing page _PAGE_ of _PAGES_', 'master-addons'),
            "infoEmpty"         => esc_html__('No records available', 'master-addons'),
            "infoFiltered"      => esc_html__('(filtered from _MAX_ total records)', 'master-addons'),
            "searchPlaceholder" => esc_html__('Search...', 'master-addons'),
            "processing"        => esc_html__('Processing...', 'master-addons'),
            "csvHtml5"          => esc_html__('CSV', 'master-addons'),
            "excelHtml5"        => esc_html__('Excel', 'master-addons'),
            "pdfHtml5"          => esc_html__('PDF', 'master-addons'),
            "print"             => esc_html__('Print', 'master-addons')
        );
        wp_localize_script('master-addons-scripts', 'jltma_data_table_vars', $jltma_data_table_param);

        // Allow Pro to enqueue scripts/styles after free
        do_action('master_addons/assets/enqueue_scripts');
    }


    public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }
}
Master_Addons_Assets::get_instance();