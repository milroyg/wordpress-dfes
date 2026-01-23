<?php
namespace MasterHeaderFooter;
use MasterHeaderFooter\Master_Header_Footer;


defined( 'ABSPATH' ) || exit;

class JLTMA_HF_Activator {
    public static $instance = null;

    protected $templates;
    public $templates_template;
    public $header_template;
    public $footer_template;
    public $comment_template;
    public $popup_template;

    protected $current_theme;
    protected $current_template;
    public $jltma_plugin_path;
    protected $post_type = 'master_template';

    public function __construct() {
        $this->jltma_plugin_path = JLTMA_PATH;
        $this->jltma_include_theme_support_files();

        add_action( 'wp', array( $this, 'jltma_hooks' ) );

        // Add template override for full page templates (Search, 404, Single, Archive, etc.)
        // Use priority 1 to be absolutely first
        add_filter( 'template_include', array( $this, 'jltma_template_include' ), 1 );

        // Also try template_redirect as a backup
        add_action( 'template_redirect', array( $this, 'jltma_template_redirect' ), 1 );
    }

    public function jltma_include_theme_support_files(){
        include $this->jltma_plugin_path . 'inc/admin/theme-builder/inc/theme-hooks/theme-support.php';
        include $this->jltma_plugin_path . 'inc/admin/theme-builder/inc/theme-hooks/hello-elementor.php';
        include $this->jltma_plugin_path . 'inc/admin/theme-builder/inc/theme-hooks/storefront.php';
        include $this->jltma_plugin_path . 'inc/admin/theme-builder/inc/theme-hooks/astra.php';
        include $this->jltma_plugin_path . 'inc/admin/theme-builder/inc/theme-hooks/bbtheme.php';
        include $this->jltma_plugin_path . 'inc/admin/theme-builder/inc/theme-hooks/generatepress.php';
        include $this->jltma_plugin_path . 'inc/admin/theme-builder/inc/theme-hooks/genesis.php';
        include $this->jltma_plugin_path . 'inc/admin/theme-builder/inc/theme-hooks/my-listing.php';
        include $this->jltma_plugin_path . 'inc/admin/theme-builder/inc/theme-hooks/oceanwp.php';
        include $this->jltma_plugin_path . 'inc/admin/theme-builder/inc/theme-hooks/twenty-nineteen.php';
    }


    public function jltma_hooks(){
        $this->current_template = basename(get_page_template_slug());


        if($this->current_template == 'elementor_canvas'){
            return;
        }

        $this->current_theme = get_template();


        switch($this->current_theme){

            case 'hello-elementor':  case 'hello-elementor-child':
                new Theme_Hooks\Hello_Elementor(self::template_ids());
                break;

            case 'astra':
                new Theme_Hooks\Astra(self::template_ids());
                break;

            case 'storefront':  case 'storefront-child':
                new Theme_Hooks\Storefront(self::template_ids());
                break;

            case 'generatepress':  case 'generatepress-child':
                new Theme_Hooks\Generatepress(self::template_ids());
                break;

            case 'oceanwp': case 'oceanwp-child':
                new Theme_Hooks\Oceanwp(self::template_ids());
                break;

            case 'bb-theme':  case 'bb-theme-child':
                new Theme_Hooks\Bbtheme(self::template_ids());
                break;

            case 'genesis':  case 'genesis-child':
                new Theme_Hooks\Genesis(self::template_ids());
                break;

            case 'twentynineteen':
                new Theme_Hooks\TwentyNineteen(self::template_ids());
                break;

            case 'my-listing': case 'my-listing-child':
                new Theme_Hooks\MyListing(self::template_ids());
                break;

            default:
                $template_ids = self::template_ids();
                new Theme_Hooks\Theme_Support($template_ids);
                return;

        }


    }

    /**
     * Override template for full page templates (Search, 404, Single, Archive, etc.)
     *
     * @param string $template
     * @return string
     */
    public function jltma_template_include( $template ) {
        // Only run on frontend
        if ( is_admin() ) {
            return $template;
        }

        // Get current page location
        $location = $this->get_current_location();

        if ( ! $location ) {
            return $template;
        }

        // Find specific template for current location
        $template_id = $this->find_template_for_location( $location );

        if ( $template_id ) {
            // Store template ID for template to use
            global $wp_query;
            $wp_query->set( 'master_template_id', $template_id );

            // Get our template path
            $ma_template = $this->get_master_addons_template_path();

            if ( $ma_template && file_exists( $ma_template ) ) {
                return $ma_template;
            }
        }
        return $template;
    }

    /**
     * Template redirect fallback - force include our template if found
     */
    public function jltma_template_redirect() {
        // Only run on frontend
        if ( is_admin() ) {
            return;
        }

        // Get current page location
        $location = $this->get_current_location();

        if ( ! $location ) {
            return;
        }

        // Find specific template for current location
        $template_id = $this->find_template_for_location( $location );

        if ( $template_id ) {
            // Store template ID for template to use
            global $wp_query;
            $wp_query->set( 'master_template_id', $template_id );

            // Get our template path
            $ma_template = $this->get_master_addons_template_path();

            if ( $ma_template && file_exists( $ma_template ) ) {
                // Force include our template
                include( $ma_template );
                exit;
            }
        }
    }

    /**
     * Find template for specific location
     *
     * @param string $location
     * @return int|false
     */
    public function find_template_for_location( $location ) {
        $args = [
            'posts_per_page'   => -1,  // Get all templates to check
            'orderby'          => 'id',
            'order'            => 'DESC',
            'post_status'      => 'publish',
            'post_type'        => $this->post_type,
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key'     => 'master_template_activation',
                    'value'   => 'yes',
                    'compare' => '=',
                ]
            ],
        ];

        // Check for specific template type first (Search, 404, Single, Archive, etc.)
        $type_location_map = [
            'search' => 'search',
            '404' => '404',
            'single' => 'single',
            'archive' => 'archive',
            'category' => 'category',
            'tag' => 'tag',
            'author' => 'author',
            'date' => 'date',
            'page_single' => 'page_single',
            'product' => 'product',
            'product_archive' => 'product_archive',
        ];

        if ( isset($type_location_map[$location]) ) {
            $args['meta_query'][] = [
                'key'     => 'master_template_type',
                'value'   => $type_location_map[$location],
                'compare' => '=',
            ];

            $templates = get_posts($args);

            if ( !empty($templates) ) {
                foreach ($templates as $template_post) {
                    $template = $this->get_full_data($template_post);

                    // Check new conditions data first
                    if (!empty($template['conditions_data']) && is_array($template['conditions_data'])) {
                        if ( $this->check_new_conditions($template['conditions_data']) ) {
                            return $template['ID'];
                        }
                    } else {
                        // Legacy check for backward compatibility
                        return $template['ID'];
                    }
                }
            }
        }

        // Check templates with type 'templates' that have conditions matching current location
        $args['meta_query'] = [
            'relation' => 'AND',
            [
                'key'     => 'master_template_activation',
                'value'   => 'yes',
                'compare' => '=',
            ],
            [
                'key'     => 'master_template_type',
                'value'   => 'templates',
                'compare' => '=',
            ]
        ];

        $templates = get_posts($args);
        foreach ($templates as $template_post) {
            $template = $this->get_full_data($template_post);

            // Check new conditions data first
            if (!empty($template['conditions_data']) && is_array($template['conditions_data'])) {
                if ( $this->check_new_conditions($template['conditions_data']) ) {
                    return $template['ID'];
                }
            }
        }

        return false;
    }

    /**
     * Get current page location type
     *
     * @return string|false
     */
    public function get_current_location() {
        if ( is_404() ) {
            return '404';
        } elseif ( is_search() ) {
            return 'search';
        } elseif ( function_exists('is_shop') && (is_shop() || is_product_category() || is_product_tag()) ) {
            return 'product_archive';
        } elseif ( function_exists('is_product') && is_product() ) {
            return 'product';
        } elseif ( is_category() ) {
            return 'category';
        } elseif ( is_tag() ) {
            return 'tag';
        } elseif ( is_author() ) {
            return 'author';
        } elseif ( is_date() ) {
            return 'date';
        } elseif ( is_singular('page') ) {
            return 'page_single';
        } elseif ( is_singular() ) {
            return 'single';
        } elseif ( is_archive() || is_home() || is_tax() ) {
            return 'archive';
        }

        return false;
    }

    /**
     * Get Master Addons template path
     *
     * @return string
     */
    private function get_master_addons_template_path() {
        // Use absolute path to template files - current file is in /inc/admin/theme-builder/inc/
        $theme_builder_dir = dirname( __FILE__ ); // /inc/admin/theme-builder/inc/
        $theme_builder_dir = dirname( $theme_builder_dir ); // /inc/admin/theme-builder/
        $template_path = $theme_builder_dir . '/templates/views/ma-template.php';

        if ( file_exists( $template_path ) ) {
            return $template_path;
        }

        // Fallback to canvas if template file doesn't exist
        $canvas_path = $theme_builder_dir . '/templates/views/ma-canvas.php';

        if ( file_exists( $canvas_path ) ) {
            return $canvas_path;
        }

        return false;
    }

    public static function template_ids(){
        $cached = wp_cache_get( 'master_template_ids' );

		if ( false !== $cached ) {
			return $cached;
        }

        $instance = self::instance();
        $instance->the_filter();

        $ids = [
            $instance->header_template,
            $instance->footer_template,
            $instance->comment_template,
            $instance->templates_template,
            $instance->popup_template,
        ];

        if($instance->templates_template != null){
            Master_Header_Footer::render_elementor_content_css($instance->templates_template);
        }

        if($instance->popup_template != null){
            Master_Header_Footer::render_elementor_content_css($instance->popup_template);
        }

        if($instance->header_template != null){
            Master_Header_Footer::render_elementor_content_css($instance->header_template);
        }

        if($instance->footer_template != null){
            Master_Header_Footer::render_elementor_content_css($instance->footer_template);
        }

        if($instance->comment_template != null){
            Master_Header_Footer::render_elementor_content_css($instance->comment_template);
        }

        wp_cache_set( 'master_template_ids', $ids );

        return $ids;
    }

    protected function the_filter() {
        // Get all active templates
        $arg = [
            'posts_per_page'   => -1,
            'orderby'          => 'id',
            'order'            => 'DESC',
            'post_status'      => 'publish',
            'post_type'        => $this->post_type,
            'meta_query' => [
                [
                    'key'     => 'master_template_activation',
                    'value'   => 'yes',
                    'compare' => '=',
                ],
            ],
        ];
        $this->templates = get_posts($arg);


        // Process all templates using the new conditions system
        if(!is_admin() && $this->templates){
            $this->get_header_footer([]);
        }
    }

    protected function get_header_footer($filters){
        $template_id = array();

        if($this->templates != null){
            foreach($this->templates as $template){
                $template = $this->get_full_data($template);

                $match_found = false;

                // WPML Language Check
                if ( defined( 'ICL_LANGUAGE_CODE' ) ):
                    $current_lang = apply_filters( 'wpml_post_language_details', NULL, $template['ID'] );

                    if ( !empty($current_lang) && !$current_lang['different_language'] && ($current_lang['language_code'] == ICL_LANGUAGE_CODE) ):
                        $template_id[ $template['type'] ] = $template['ID'];
                    endif;
                endif;

                // Check new conditions data first
                if (!empty($template['conditions_data']) && is_array($template['conditions_data'])) {
                    $match_found = $this->check_new_conditions($template['conditions_data']);
                } else {
                    // Fallback to legacy condition checking
                    $match_found = $this->check_legacy_conditions($template, $filters);
                }

                if($match_found == true){

                    if($template['type'] == 'templates'){
                        $this->templates_template = isset( $template_id['templates'] ) ? $template_id['templates'] : $template['ID'];
                    }

                    if($template['type'] == 'popup'){
                        $this->popup_template = isset( $template_id['popup'] ) ? $template_id['popup'] : $template['ID'];
                    }

                    if($template['type'] == 'header'){
                        $this->header_template = isset( $template_id['header'] ) ? $template_id['header'] : $template['ID'];
                    }

                    if($template['type'] == 'footer'){
                        $this->footer_template = isset( $template_id['footer'] ) ? $template_id['footer'] : $template['ID'];
                    }

                    if($template['type'] == 'comment'){
                        $this->comment_template = isset( $template_id['comment'] ) ? $template_id['comment'] : $template['ID'];
                    }

                    // Handle new template types (Search, 404, Single, Archive, Category, Tag, Author, Date, Page Single, Product)
                    if(in_array($template['type'], ['search', 'single', 'archive', '404', 'category', 'tag', 'author', 'date', 'page_single', 'product', 'product_archive'])){
                        $this->templates_template = isset( $template_id['templates'] ) ? $template_id['templates'] : $template['ID'];
                    }
                } else {
                }
            }
        }
    }

    /**
     * Check new repeater-based conditions with Include/Exclude logic
     *
     * @param array $conditions_data
     * @return bool
     */
    protected function check_new_conditions($conditions_data) {
        // If no conditions are set, don't display
        if (empty($conditions_data) || !is_array($conditions_data)) {
            return false;
        }

        $include_matches = [];
        $exclude_matches = [];
        $has_includes = false;
        $has_excludes = false;

        // Process all conditions
        foreach ($conditions_data as $condition) {
            // Skip empty conditions
            if (empty($condition['rule'])) {
                continue;
            }

            $rule_matches = $this->check_condition_rule($condition['rule'], $condition['specific'] ?? '', $condition);

            if ($condition['type'] === 'include') {
                $has_includes = true;
                $include_matches[] = $rule_matches;
            } else if ($condition['type'] === 'exclude') {
                $has_excludes = true;
                $exclude_matches[] = $rule_matches;
            }
        }

        // Template matches if:
        // 1. If there are includes, at least one must match
        // 2. If there are excludes, none must match
        // 3. If there are no includes and no excludes, don't display

        if (!$has_includes && !$has_excludes) {
            return false; // No valid conditions set
        }

        $include_match = !$has_includes || in_array(true, $include_matches);
        $exclude_match = $has_excludes && in_array(true, $exclude_matches);

        return $include_match && !$exclude_match;
    }

    /**
     * Check individual condition rule against current page context
     *
     * @param string $rule
     * @param string $specific
     * @param array $condition Full condition data including posts
     * @return bool
     */
    protected function check_condition_rule($rule, $specific = '', $condition = []) {
        switch ($rule) {
            case 'entire_site':
                return true;

            case 'front_page':
                return is_front_page();

            case 'search':
                return is_search();

            case '404':
                return is_404();

            case 'archive':
                if (empty($specific)) {
                    return is_archive();
                } else {
                    // Handle specific archive types
                    switch ($specific) {
                        case 'author':
                            return is_author();
                        case 'date':
                            return is_date();
                        case 'category':
                            return is_category();
                        case 'post_tag':
                        case 'tag':
                            return is_tag();
                        case 'product_cat':
                            return function_exists('is_product_category') && is_product_category();
                        case 'product_tag':
                            return function_exists('is_product_tag') && is_product_tag();
                        default:
                            // Handle custom taxonomy archives
                            return is_tax($specific);
                    }
                }

            case 'singular':
                if (empty($specific)) {
                    // If no specific post type is set, match all singular pages
                    return is_singular();
                } else {
                    // Handle specific post type
                    if (!is_singular($specific)) {
                        return false;
                    }

                    // If specific posts are defined, check if current post is in that list
                    if (!empty($condition['posts']) && is_array($condition['posts'])) {
                        $current_post_id = get_the_ID();
                        // Filter out empty values and convert to integers
                        $selected_posts = array_filter(array_map('intval', $condition['posts']));

                        // If posts array is not empty, check if current post is in the list
                        if (!empty($selected_posts)) {
                            return in_array($current_post_id, $selected_posts);
                        }
                    }

                    // If no specific posts defined, match any post of this type
                    return true;
                }

            case 'product':
                return function_exists('is_product') && is_product();

            case 'product_archive':
                return function_exists('is_shop') && (is_shop() || is_product_category() || is_product_tag());

            default:
                return false;
        }
    }

    /**
     * Legacy condition checking for backward compatibility
     *
     * @param array $template
     * @param array $filters
     * @return bool
     */
    protected function check_legacy_conditions($template, $filters) {
        // If no filters are provided, check the template's stored conditions
        if (empty($filters)) {
            $condition = $template['jltma_hf_conditions'] ?? '';

            switch ($condition) {
                case 'entire_site':
                    return true;
                case 'front_page':
                    return is_front_page();
                case 'singular':
                    // Check if specific posts are selected
                    $specific_ids = $template['jltma_hfc_singular_id'] ?? '';
                    $current_id = get_the_ID();

                    if (!empty($specific_ids)) {
                        $ids = array_map('trim', explode(',', $specific_ids));
                        return in_array($current_id, $ids);
                    }

                    return is_singular();
                default:
                    return false;
            }
        }

        $match_found = true;

        foreach($filters as $filter){
            if($filter['key'] == 'jltma_hfc_singular_id'){
                $ids = explode(',', $template[$filter['key']]);
                if(!in_array($filter['value'], $ids)){
                    $match_found = false;
                }
            } elseif($filter['key'] == 'jltma_hfc_post_types_id'){
                $current_post_type = get_post_type(get_the_ID());
                $selected_post_types = array_map('trim', explode(',', $template[$filter['key']]));
                if(!in_array($current_post_type, $selected_post_types)){
                    $match_found = false;
                }
            } elseif($template[$filter['key']] != $filter['value']){
                $match_found = false;
            }
            if( $filter['key'] == 'jltma_hf_conditions' && $template[$filter['key']] == 'singular' && count($filters) < 2){
                $match_found = false;
            }
        }

        return $match_found;
    }

    protected function get_full_data($post){
        if($post != null){
            return array_merge((array)$post, [
                'type'                    => get_post_meta($post->ID, 'master_template_type', true),
                'jltma_hf_conditions'     => get_post_meta($post->ID, 'master_template_jltma_hf_conditions', true),
                'jltma_hfc_singular'      => get_post_meta($post->ID, 'master_template_jltma_hfc_singular', true),
                'jltma_hfc_singular_id'   => get_post_meta($post->ID, 'master_template_jltma_hfc_singular_id', true),
                'jltma_hfc_post_types_id' => get_post_meta($post->ID, 'master_template_jltma_hfc_post_types_id', true),
                'conditions_data'         => get_post_meta($post->ID, 'master_template_conditions_data', true),
            ]);
        }
    }

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

}

JLTMA_HF_Activator::instance();
