<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use MasterAddons\Inc\Classes\Importer\JLTMA_Templates_Importer;
use MasterAddons\Inc\Helper\Master_Addons_Helper;
use Elementor\Plugin;

/**
 * Template Kits Import Functionality
 */

// Register import AJAX actions
add_action('wp_ajax_jltma_activate_required_theme', 'jltma_activate_required_theme');
add_action('wp_ajax_jltma_fix_compatibility', 'jltma_fix_compatibility');
add_action('wp_ajax_jltma_reset_previous_import', 'jltma_reset_previous_import');
add_action('wp_ajax_jltma_import_templates_kit', 'jltma_import_templates_kit');
add_action('wp_ajax_jltma_final_settings_setup', 'jltma_final_settings_setup');
add_action('wp_ajax_jltma_search_query_results', 'jltma_search_query_results');
add_action('wp_ajax_jltma_predownload_kit', 'jltma_predownload_kit');
add_action('wp_ajax_jltma_download_all_kits', 'jltma_download_all_kits');
add_action('wp_ajax_jltma_install_required_plugin', 'jltma_install_required_plugin');
add_action('wp_ajax_jltma_activate_required_plugin', 'jltma_activate_required_plugin');
add_action('wp_ajax_jltma_upload_template_kit', 'jltma_upload_template_kit');
add_action('init', 'jltma_disable_default_woo_pages_creation', 2);

// Set Image Timeout
if (version_compare(get_bloginfo('version'), '5.1.0', '>=')) {
    add_filter('http_request_timeout', 'jltma_set_image_request_timeout', 10, 2);
}

/**
** Get Theme Status
*/
function jltma_get_theme_status() {
    $theme = wp_get_theme();

    // Theme installed and activate.
    if ('Hello Elementor' === $theme->name || 'Hello Elementor' === $theme->parent_theme) {
        return 'req-theme-active';
    }

    // Theme installed but not activate.
    foreach ((array) wp_get_themes() as $theme_dir => $theme) {
        if ('Hello Elementor' === $theme->name || 'Hello Elementor' === $theme->parent_theme) {
            return 'req-theme-inactive';
        }
    }

    return 'req-theme-not-installed';
}

/**
** Install/Activate Required Theme
*/
function jltma_activate_required_theme() {
    $nonce = $_POST['nonce'];

    if (!wp_verify_nonce($nonce, 'jltma-templates-kit-js') || !current_user_can('manage_options')) {
        exit; // Get out of here, the nonce is rotten!
    }

    // Get Current Theme
    $theme = get_option('stylesheet');

    // No default theme activation - themes should be specified in manifest
}


/**
** Fix Compatibility
*/
function jltma_fix_compatibility() {
    $nonce = $_POST['nonce'];

    if (!wp_verify_nonce($nonce, 'jltma-templates-kit-js') || !current_user_can('manage_options')) {
        exit; // Get out of here, the nonce is rotten!
    }

    // Get currently active plugins
    $active_plugins = (array) get_option('active_plugins', array());
    $active_plugins = array_values($active_plugins);
    // Required plugins should come from manifest
    $required_plugins = [];

    // Keep only required plugins active during import
    foreach ($active_plugins as $key => $value) {
        if (!in_array($value, $required_plugins)) {
            $active_key = array_search($value, $active_plugins);
            unset($active_plugins[$active_key]);
        }
    }

    // Set Active Plugins
    update_option('active_plugins', array_values($active_plugins));

    // Get Current Theme
    $theme = get_option('stylesheet');

    // No default theme activation - themes should be specified in manifest
}

/**
** Import Template Kit
*/
function jltma_import_templates_kit() {
    if (!wp_verify_nonce($_POST['_wpnonce'], 'jltma_template_kits_nonce_action') || !current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Permission denied']);
        return;
    }

    // Include the importer class if not already loaded
    if (!class_exists('MasterAddons\Inc\Classes\Importer\JLTMA_Templates_Importer')) {
        require_once JLTMA_PATH . 'inc/classes/importer/class-jltma-templates-importer.php';
    }
    
    // Get cache manager instance
    $cache_manager = null;
    if (class_exists('MasterAddons\Inc\Templates\Classes\JLTMA_Plugin_Template_Kit_Cache')) {
        $cache_manager = \MasterAddons\Inc\Templates\Classes\JLTMA_Plugin_Template_Kit_Cache::get_instance();
    }

    /**
    ** Add .xml and .svg files as supported format in the uploader.
    */
    function jltma_custom_upload_mimes($mimes) {
        // Allow SVG files.
        $mimes['svg']  = 'image/svg+xml';
        $mimes['svgz'] = 'image/svg+xml';

        // Allow XML files.
        $mimes['xml'] = 'text/xml';

        // Allow JSON files.
        $mimes['json'] = 'application/json';

        return $mimes;
    }

    add_filter('upload_mimes', 'jltma_custom_upload_mimes', 99);

    /**
    * Sanitize SVG files on upload.
    */
    function jltma_sanitize_svg_on_upload($file) {
        if ($file['type'] === 'image/svg+xml') {
            $file_content = file_get_contents($file['tmp_name']);
            $sanitized_content = jltma_sanitize_svg($file_content);
            file_put_contents($file['tmp_name'], $sanitized_content);
        }
        return $file;
    }

    add_filter('wp_handle_upload_prefilter', 'jltma_sanitize_svg_on_upload');

    /**
    * Sanitize SVG content.
    */
    function jltma_sanitize_svg($svg_content) {
        $dom = new DOMDocument();
        $dom->loadXML($svg_content, LIBXML_NOENT | LIBXML_DTDLOAD);

        // Remove scripts
        $scripts = $dom->getElementsByTagName('script');
        while ($scripts->length > 0) {
            $scripts->item(0)->parentNode->removeChild($scripts->item(0));
        }

        return $dom->saveXML();
    }

    // Temp Define Importers
    if (!defined('WP_LOAD_IMPORTERS')) {
        define('WP_LOAD_IMPORTERS', true);
    }

    // Include if Class Does NOT Exist
    if (!class_exists('WP_Import')) {
        $class_wp_importer = JLTMA_PATH .'inc/classes/importer/class-wordpress-importer.php';
        if (file_exists($class_wp_importer)) {
            require $class_wp_importer;
        }
    }

    // Get template kit ID
    $kit = isset($_POST['jltma_templates_kit']) ? sanitize_file_name(wp_unslash($_POST['jltma_templates_kit'])) : '';
    if( !$kit && isset($_POST['parentTemplate'])){
        $kit = $_POST['parentTemplate'];
    }
    // Handle template import
    if(isset($_POST['template'])){
        $template_data = wp_unslash($_POST['template']);
        $decoded = json_decode($template_data, true);
        $kit_id = $decoded['template_id'];
        $kit_name = $decoded['title'];

        // Check if we have both parentTemplate and template_id for cached templates
        if(isset($_POST['parentTemplate']) && isset($decoded['template_id'])){
            $parent_template = sanitize_text_field($_POST['parentTemplate']);
            $template_id = sanitize_text_field($decoded['template_id']);

            $upload_dir = wp_upload_dir();

            // First check cached templates directory
            $cached_base = $upload_dir['basedir'] . '/master_addons/templates_kits/kits/';
            $manifest_path = $cached_base . $parent_template . '/manifest.json';

            // Check if manifest exists in cached location
            if(file_exists($manifest_path)){
                // Read manifest to verify template exists
                $manifest_content = file_get_contents($manifest_path);
                $manifest = json_decode($manifest_content, true);

                // Find the template in manifest
                $template_found = false;
                $template_source = null;
                if(isset($manifest['templates']) && is_array($manifest['templates'])){
                    foreach($manifest['templates'] as $tpl){
                        // Check by template_id or other identifying fields
                        if((isset($tpl['template_id']) && $tpl['template_id'] == $template_id) ||
                           (isset($tpl['name']) && $tpl['name'] == $kit_name)){
                            $template_found = true;
                            $template_source = isset($tpl['source']) ? $tpl['source'] : null;
                            break;
                        }
                    }
                }

                // If template found in manifest, load from local file
                if($template_found && $template_source){
                    $json_file_path = $cached_base . $parent_template . '/' . $template_source;

                    if(file_exists($json_file_path)){
                        $json_content = file_get_contents($json_file_path);
                        $local_template = json_decode($json_content, true);

                        if($local_template){
                            // Use the local template data instead of frontend data
                            $decoded = $local_template;
                            // Preserve some fields from frontend if needed
                            $decoded['template_id'] = $template_id;
                            $decoded['title'] = $kit_name;
                        }
                    }
                }
                // If not found by manifest check, try direct file lookup for global.json
                else if($kit_name === 'Global Kit Styles' || strpos(strtolower($kit_name), 'global') !== false){
                    $global_path = $cached_base . $parent_template . '/templates/global.json';
                    if(file_exists($global_path)){
                        $json_content = file_get_contents($global_path);
                        $local_template = json_decode($json_content, true);

                        if($local_template){
                            // Use the local template data instead of frontend data
                            $decoded = $local_template;
                            // Preserve template_id if needed
                            if(!isset($decoded['template_id'])){
                                $decoded['template_id'] = $template_id;
                            }
                            $decoded['title'] = $kit_name;
                        }
                    }
                }
            }
            // Fall back to purchased kits if not found in cached
            else if(is_null($decoded['content'])){
                $cache_base = $upload_dir['basedir'] . '/master_addons/purchased_kits/kits/';
                $manifest_data = json_decode(file_get_contents($cache_base . 'kit_' . $parent_template . '/manifest.json'));
                $thisTemplate = null;
                foreach($manifest_data->templates as $template_key => $template){
                    if( $template->name === $kit_name ){
                        $thisTemplate = $template;
                    }
                }
                $template_slug = ($thisTemplate)? $thisTemplate->source : str_replace(' ', '-', $kit_name) . '.json';
                if($thisTemplate ){
                    $json_file_path = $cache_base . 'kit_' . $parent_template . '/' . $template_slug;

                }else{
                    $json_file_path = $cache_base . 'kit_' . $parent_template . '/templates/' . $template_slug;
                }

            
            // If direct path doesn't work, try to find the file with similar name
            if (!file_exists($json_file_path)) {
                $possible_names = [
                    $template_slug,
                    strtolower($template_slug),
                    str_replace('-', '_', $template_slug),
                    str_replace(' ', '_', $kit_name) . '.json',
                    str_replace([' ', '_'], '-', $kit_name) . '.json',
                    str_replace(['–', '—'], '-', $template_slug),
                    str_replace(['–', '—'], '-', str_replace(' ', '-', $kit_name)) . '.json',
                    // Handle various quote types
                    str_replace(['\'', '\'', '\"', '\"'], '', $template_slug),
                    // URL encode special characters
                    urlencode(str_replace(' ', '-', $kit_name)) . '.json',
                    // Remove all non-alphanumeric except hyphens
                    preg_replace('/[^a-zA-Z0-9\-]/', '-', str_replace(' ', '-', $kit_name)) . '.json'
                    // Extra check, can be removed for standerd items ends here
                ];

                // Try each possible name
                foreach ($possible_names as $possible_name) {
                    $test_path = $cache_base . 'kit_' . $parent_template . '/templates/' . $possible_name;
                    if (file_exists($test_path)) {
                        $json_file_path = $test_path;
                        break;
                    }
                }

                // If still not found, scan the directory for a matching file
                if (!file_exists($json_file_path)) {
                    $templates_dir = $cache_base . 'kit_' . $parent_template . '/templates/';
                    if (is_dir($templates_dir)) {
                        $files = scandir($templates_dir);

                        // Normalize the kit name for comparison
                        $normalized_kit_name = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $kit_name));

                        foreach ($files as $file) {
                            if (pathinfo($file, PATHINFO_EXTENSION) === 'json') {
                                // Normalize the filename for comparison
                                $normalized_filename = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', pathinfo($file, PATHINFO_FILENAME)));

                                // Check if the normalized names match
                                if ($normalized_filename === $normalized_kit_name) {
                                    $json_file_path = $templates_dir . $file;
                                    break;
                                }

                                // Also check if the kit name is contained in the filename
                                if (strpos($normalized_filename, $normalized_kit_name) !== false) {
                                    $json_file_path = $templates_dir . $file;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
            // Read the JSON file
            if (file_exists($json_file_path)) {
                $json_content = file_get_contents($json_file_path);
                $template_json = json_decode($json_content, true);

                // Use the content from the JSON file
                if($template_json && isset($template_json['content'])){
                    $decoded['content'] = $template_json['content'];
                    // Also merge other properties if they exist
                    
                    if(isset($template_json['type'])){
                        if( $template_json['type'] == 'wp-post') $decoded['type'] = $template_json['metadata']['template_type'];
                        else{
                            $decoded['type'] = $template_json['type'];
                        }
                    }
                    
                    if(isset($template_json['version'])){
                        $decoded['version'] = $template_json['version'];
                    }
                    if(isset($template_json['metadata'])){
                        $decoded['metadata'] = $template_json['metadata'];
                    }
                } else {
                    // If the entire file is the template data
                    $decoded = array_merge($decoded, $template_json);
                }

            } else {
                wp_send_json_error(array(
                    'message' => 'Template file not found: ' . $template_slug . ' in kit_' . $parent_template
                ));
                exit;
            }
            } // Close the else-if for purchased kits
        } // Close the if for parentTemplate check

        // Continue with the import process
        $import_start_time = microtime(true);
        $decoded['content'] = jltma_import_images_to_media($decoded['content'], $kit_id, $kit_name);
        $import_time = microtime(true) - $import_start_time;

        $template_type = $decoded['type'] ?? 'page';

        // Check if this is a global kit style template
        $is_global_kit = false;
        if (isset($decoded['metadata']['template_type']) && $decoded['metadata']['template_type'] === 'global-styles') {
            $is_global_kit = true;
            $template_type = 'kit';
            // if (isset($decoded['template_id'])) {
            //     unset($decoded['template_id']);
            // }
        }

        // Prepare meta input - don't include complex arrays here
        $meta_input = array(
            '_elementor_edit_mode'        => 'builder',
            '_elementor_template_type'    => $template_type,
            '_elementor_version'          => ELEMENTOR_VERSION,
            '_elementor_data'             => wp_slash(json_encode($decoded['content'])),
            '_jltma_demo_import_item'     => true,
            '_jltma_import_kit_id'        => $kit_id,
            '_jltma_import_date'          => current_time('mysql'),
            '_jltma_original_template_id' => $kit_id,
            '_thumbnail_id'               => 0 // You can import thumbnail separately if needed
        );

        $template_id = wp_insert_post(array(
            'post_title'    => $kit_name,
            'post_type'     => 'elementor_library',
            'post_status'   => 'publish',
            'post_content'  => '',
            'meta_input'    => $meta_input
        ));

        if (!is_wp_error($template_id) && $is_global_kit && isset($decoded['page_settings'])) {
            update_post_meta($template_id, '_elementor_page_settings', $decoded['page_settings']);
        }

        // Handle global kit styles activation
        if (!is_wp_error($template_id) && $is_global_kit) {
            // For global kit styles, we need to activate it as the active kit
            update_option('elementor_active_kit', $template_id);

            // Clear Elementor cache to apply the new kit
            if (class_exists('\Elementor\Plugin')) {
                \Elementor\Plugin::$instance->files_manager->clear_cache();
            }
        } elseif (!is_wp_error($template_id) && $template_type === 'kit') {
            // Handle regular kit templates (non-global styles)
            $auto_activate = apply_filters('jltma_auto_activate_kit_template', true, $template_id, $kit_name, $decoded);

            if ($auto_activate) {
                // Update Elementor's active kit option
                update_option('elementor_active_kit', $template_id);

                // Import global settings from the kit if available
                if (isset($decoded['settings']) && is_array($decoded['settings'])) {
                    $kit_instance = \Elementor\Plugin::$instance->documents->get($template_id);
                    if ($kit_instance) {
                        $kit_instance->save(['settings' => $decoded['settings']]);
                    }
                }

                // Clear Elementor cache to apply the new kit
                if (class_exists('\Elementor\Plugin')) {
                    \Elementor\Plugin::$instance->files_manager->clear_cache();
                }
            }
        }
        
        if (!is_wp_error($template_id)) {
            $created_page_id = null;
            $page_edit_url = null;
            
            // Define which template types need actual pages vs library entries
            $page_template_types = ['page', 'wp-page', 'single-page', 'landing-page', 'single-404', 'archive-blog'];
            $library_template_types = ['section-header', 'section-footer', 'single-post'];

            // Check if this template type needs a page created
            if (in_array($template_type, $page_template_types)) {
                $page_prefix = 'New';

                if (isset($_POST['parentTemplate'])) {
                    $parent_template = sanitize_text_field($_POST['parentTemplate']);

                    $upload_dir = wp_upload_dir();

                    // Check for purchased kit manifest first
                    $manifest_path = $upload_dir['basedir'] . '/master_addons/purchased_kits/kits/kit_' . $parent_template . '/manifest.json';

                    // If not found in purchased kits, check in cached kits
                    if (!file_exists($manifest_path)) {
                        // Try cached kits location
                        $manifest_path = $upload_dir['basedir'] . '/master_addons/templates_kits/kits/kit_' . $parent_template . '.json';

                        // Also try without kit_ prefix for cached kits
                        if (!file_exists($manifest_path)) {
                            $manifest_path = $upload_dir['basedir'] . '/master_addons/templates_kits/kits/' . $parent_template . '.json';
                        }
                    }

                    if (file_exists($manifest_path)) {
                        $manifest_content = file_get_contents($manifest_path);
                        $manifest = json_decode($manifest_content, true);

                        if ($manifest && isset($manifest['title'])) {
                            $kit_title = $manifest['title'];
                            if (strpos($kit_title, '-') !== false) {
                                $parts = explode('-', $kit_title);
                                $page_prefix = trim($parts[0]);
                            } else {
                                $page_prefix = $kit_title;
                            }
                        } elseif ($manifest && isset($manifest['kit_name'])) {
                            // Some manifests might use kit_name instead of title
                            $kit_title = $manifest['kit_name'];
                            if (strpos($kit_title, '-') !== false) {
                                $parts = explode('-', $kit_title);
                                $page_prefix = trim($parts[0]);
                            } else {
                                $page_prefix = $kit_title;
                            }
                        }
                    } else {
                        // If no manifest file found, try to get kit info from cache manager
                        if (class_exists('MasterAddons\Inc\Templates\Classes\JLTMA_Plugin_Template_Kit_Cache')) {
                            $cache_manager = \MasterAddons\Inc\Templates\Classes\JLTMA_Plugin_Template_Kit_Cache::get_instance();

                            // Try to get kit manifest
                            if (method_exists($cache_manager, 'get_kit_manifest')) {
                                $manifest = $cache_manager->get_kit_manifest($parent_template);
                                if ($manifest) {
                                    $kit_title = $manifest['title'] ?? $manifest['kit_name'] ?? '';
                                    if (!empty($kit_title)) {
                                        if (strpos($kit_title, '-') !== false) {
                                            $parts = explode('-', $kit_title);
                                            $page_prefix = trim($parts[0]);
                                        } else {
                                            $page_prefix = $kit_title;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $page_prefix = apply_filters('jltma_new_page_prefix', $page_prefix, $kit_name, $decoded);

                $separator = apply_filters('jltma_new_page_separator', ' - ', $page_prefix, $kit_name);

                // Special handling for different template types
                if (in_array($template_type, ['single-404', 'archive-blog'])) {
                    // Don't add prefix for special pages
                    $page_title = $kit_name;
                    $page_slug = sanitize_title($kit_name);
                } else {
                    // Add prefix for regular pages
                    $page_title = !empty($page_prefix) ? $page_prefix . $separator . $kit_name : $kit_name;
                    $page_slug = sanitize_title(!empty($page_prefix) ? $page_prefix . '-' . $kit_name : $kit_name);
                }
                $slug_check = 0;
                $original_slug = $page_slug;
                while (get_page_by_path($page_slug)) {
                    $slug_check++;
                    $page_slug = $original_slug . '-' . $slug_check;
                }
                
                /**
                 * Filter the post status for newly created pages from templates
                 * 
                 * @param string $status Default status ('publish')
                 * @param string $kit_name Original template name
                 * @param array $decoded Full template data
                 */
                $post_status = apply_filters('jltma_new_page_status', 'publish', $kit_name, $decoded);
                
                // Create the new page with Elementor data
                $new_page_args = array(
                    'post_title'    => $page_title,
                    'post_name'     => $page_slug,
                    'post_type'     => 'page',
                    'post_status'   => $post_status,
                    'post_content'  => '', // Elementor handles the content
                    'meta_input'    => array(
                        '_elementor_edit_mode'        => 'builder',
                        '_elementor_template_type'    => 'wp-page',
                        '_elementor_version'          => ELEMENTOR_VERSION,
                        '_elementor_data'             => wp_slash(json_encode($decoded['content'])), // Already has imported images
                        '_jltma_demo_import_item'     => true,
                        '_jltma_import_kit_id'        => $kit_id,
                        '_jltma_import_date'          => current_time('mysql'),
                        '_jltma_original_template_id' => $kit_id,
                        '_jltma_from_template_id'     => $template_id // Reference to the template
                    )
                );
                
                $created_page_id = wp_insert_post($new_page_args);
                
                if (!is_wp_error($created_page_id)) {
                    // Set page template if needed
                    update_post_meta($created_page_id, '_wp_page_template', 'elementor_header_footer');

                    // Handle special template types
                    if ($template_type === 'landing-page') {
                        // Set this page as the homepage
                        update_option('show_on_front', 'page');
                        update_option('page_on_front', $created_page_id);
                    } elseif ($template_type === 'single-404') {
                        // Store as 404 page option for theme to use
                        update_option('jltma_404_page_id', $created_page_id);

                        // If Elementor Pro is active, set it as the 404 page
                        if (defined('ELEMENTOR_PRO_VERSION')) {
                            $kit_id = get_option('elementor_active_kit');
                            if ($kit_id) {
                                $kit_settings = get_post_meta($kit_id, '_elementor_page_settings', true);
                                if (!is_array($kit_settings)) {
                                    $kit_settings = [];
                                }
                                $kit_settings['404_page_id'] = $created_page_id;
                                update_post_meta($kit_id, '_elementor_page_settings', $kit_settings);
                            }
                        }
                    } elseif ($template_type === 'archive-blog') {
                        // Set as blog posts page
                        update_option('show_on_front', 'page');
                        update_option('page_for_posts', $created_page_id);
                    }

                    // Get edit URL for the new page
                    $page_edit_url = admin_url('post.php?post=' . $created_page_id . '&action=elementor');
                }
            } elseif (in_array($template_type, $library_template_types)) {
                // For library templates (headers, footers, single post), the template is already saved
                // No additional page needs to be created, just keep the template in the library
                // The template was already created above with correct template_type
            }
            
            // Clear Elementor cache
            if (class_exists('\Elementor\Plugin')) {
                \Elementor\Plugin::$instance->files_manager->clear_cache();
            }
            
            // Prepare response
            $response = array(
                'message' => 'Template imported successfully!',
                'template_id' => $template_id,
                'template_edit_url' => admin_url('post.php?post=' . $template_id . '&action=elementor')
            );

            // Add specific information for global kit styles
            if ($is_global_kit) {
                $response['is_global_kit'] = true;
                $response['message'] = 'Global kit styles imported and activated successfully!';
                $response['edit_url'] = admin_url('admin.php?page=elementor#/kit-library');
                $response['active_kit_id'] = $template_id;
            }
            // Add page information if a page was created
            elseif ($created_page_id && !is_wp_error($created_page_id)) {
                $response['page_created'] = true;
                $response['page_id'] = $created_page_id;
                $response['page_title'] = $page_title;
                $response['page_slug'] = $page_slug;
                $response['page_edit_url'] = $page_edit_url;
                $response['edit_url'] = $page_edit_url; // Primary edit URL points to the page
                $response['view_url'] = get_permalink($created_page_id); // Frontend view URL
                $response['message'] = 'Template imported and page created successfully!';
            } else {
                $response['page_created'] = false;
                $response['edit_url'] = $response['template_edit_url']; // Primary edit URL points to the template
                $response['view_url'] = get_permalink($template_id); // Frontend view URL for templates
            }
            
            wp_send_json_success($response);
            exit;
        } else {
            wp_send_json_error(array(
                'message' => 'Failed to save template: ' . $template_id->get_error_message()
            ));
            exit;
        }
    }

    if (empty($kit)) {
        wp_send_json_error(array(
            'message' => 'Template kit ID is required.'
        ));
        return;
    }

    // Store kit ID temporarily
    update_option('jltma-import-kit-id', $kit);

    try {
        // Try REST API with caching first, fallback to local JSON
        $import_result = jltma_import_kit_via_api($kit);

        if (!$import_result) {
            // Fallback to local JSON files
            $importer = new JLTMA_Templates_Importer($kit);
            $import_result = $importer->import_kit();
        }

        if ($import_result && $import_result['success']) {
            // Return serialized result similar to Royal Addons
            echo esc_html(serialize(array(
                'success' => true,
                'imported_pages' => $import_result['imported_pages'] ?? array(),
                'message' => $import_result['message'] ?? 'Import completed successfully!'
            )));
        } else {
            wp_send_json_error(array(
                'message' => $import_result['message'] ?? 'Import failed.'
            ));
        }

    } catch (Exception $e) {
        wp_send_json_error(array(
            'message' => $e->getMessage()
        ));
    }
}

/**
** Import Template Kit via REST API with caching
*/
function jltma_import_kit_via_api($kit) {
    // Get cache manager instance
    $cache_manager = null;
    if (class_exists('MasterAddons\Inc\Templates\Classes\JLTMA_Plugin_Template_Kit_Cache')) {
        $cache_manager = \MasterAddons\Inc\Templates\Classes\JLTMA_Plugin_Template_Kit_Cache::get_instance();
    }

    if (!$cache_manager) {
        return false;
    }

    // Try to download and cache the kit
    $kit_data = $cache_manager->download_and_cache_kit($kit);

    if (!$kit_data || !$kit_data['success']) {
        // Fallback to direct API call if caching fails
        return jltma_import_kit_via_api_direct($kit);
    }

    // Get manifest from cached data
    $manifest_data = $kit_data['manifest'];
    if (!$manifest_data || !isset($manifest_data['pages'])) {
        // Try to read manifest from kit directory
        $manifest_data = $cache_manager->get_kit_manifest($kit);
        if (!$manifest_data || !isset($manifest_data['pages'])) {
            return false;
        }
    }

    // Import each page from cached templates
    $imported_pages = array();
    foreach ($manifest_data['pages'] as $page_slug) {
        // Try to get template from cache first
        $template_data = $cache_manager->get_kit_template($kit, $page_slug);

        if ($template_data) {
            $page_id = jltma_import_template_from_data($template_data, $kit);
        } else {
            // Fallback to API if template not in cache
            $page_id = jltma_import_single_template_from_api($kit, $page_slug);
        }

        if ($page_id) {
            $imported_pages[$page_slug] = $page_id;
        }
    }

    // Import global settings if available
    if (isset($manifest_data['global_settings'])) {
        jltma_import_global_settings_from_api($manifest_data['global_settings']);
    }

    // Set home page if exists
    if (isset($imported_pages['home'])) {
        update_option('show_on_front', 'page');
        update_option('page_on_front', $imported_pages['home']);
    }

    return array(
        'success' => true,
        'imported_pages' => $imported_pages,
        'message' => 'Template kit imported successfully!'
    );
}

/**
** Fallback: Import Template Kit directly via API (without caching)
*/
function jltma_import_kit_via_api_direct($kit) {
    // Primary API endpoint
    $config = null;
    if (function_exists('MasterAddons\Inc\Templates\master_addons_templates')) {
        $templates_instance = \MasterAddons\Inc\Templates\master_addons_templates();
        if ($templates_instance && isset($templates_instance->config)) {
            $config = $templates_instance->config->get('api');
        }
    }

    $api_url = $config['base'] . $config['path'] . '/templates-kit/' . $kit . '/manifest.json';

    // Add pro_enabled parameter if pro is enabled
    if (Master_Addons_Helper::jltma_premium()) {
        $api_url = add_query_arg('pro_enabled', 'true', $api_url);
    }

    $response = wp_remote_get($api_url, [
        'timeout' => 60,
        'sslverify' => false,
        'headers' => [
            'User-Agent' => 'Master Addons Template Kit Importer/' . JLTMA_VER
        ]
    ]);

    // Check for API errors
    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
        return false;
    }

    $manifest_data = json_decode(wp_remote_retrieve_body($response), true);
    if (!$manifest_data || !isset($manifest_data['pages'])) {
        return false;
    }

    // Import each page from API
    $imported_pages = array();
    foreach ($manifest_data['pages'] as $page_slug) {
        $page_id = jltma_import_single_template_from_api($kit, $page_slug);
        if ($page_id) {
            $imported_pages[$page_slug] = $page_id;
        }
    }

    // Import global settings if available
    if (isset($manifest_data['global_settings'])) {
        jltma_import_global_settings_from_api($manifest_data['global_settings']);
    }

    // Set home page if exists
    if (isset($imported_pages['home'])) {
        update_option('show_on_front', 'page');
        update_option('page_on_front', $imported_pages['home']);
    }

    return array(
        'success' => true,
        'imported_pages' => $imported_pages,
        'message' => 'Template kit imported successfully via API!'
    );
}

/**
** Import Single Template from API
*/
function jltma_import_single_template_from_api($kit, $template_slug) {
    // First try to get from cache
    $cache_manager = null;
    if (class_exists('MasterAddons\Inc\Templates\Classes\JLTMA_Plugin_Template_Kit_Cache')) {
        $cache_manager = \MasterAddons\Inc\Templates\Classes\JLTMA_Plugin_Template_Kit_Cache::get_instance();
    }

    if ($cache_manager) {
        $template_data = $cache_manager->get_kit_template($kit, $template_slug);
        if ($template_data) {
            return jltma_import_template_from_data($template_data, $kit);
        }
    }

    // Fallback to direct API call
    $config = null;
    if (function_exists('MasterAddons\Inc\Templates\master_addons_templates')) {
        $templates_instance = \MasterAddons\Inc\Templates\master_addons_templates();
        if ($templates_instance && isset($templates_instance->config)) {
            $config = $templates_instance->config->get('api');
        }
    }

    $template_url = $config['base'] . $config['path'] . '/templates-kit/' . $kit . '/' . $template_slug . '.json';

    // Add pro_enabled parameter if pro is enabled
    if (Master_Addons_Helper::jltma_premium()) {
        $template_url = add_query_arg('pro_enabled', 'true', $template_url);
    }

    $response = wp_remote_get($template_url, array(
        'timeout' => 30,
        'headers' => array(
            'Accept' => 'application/json',
            'User-Agent' => 'Master-Addons/' . JLTMA_VER
        )
    ));

    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
        return false;
    }

    $template_data = json_decode(wp_remote_retrieve_body($response), true);
    if (!$template_data) {
        return false;
    }

    return jltma_import_template_from_data($template_data, $kit);
}

/**
** Import template from data array
*/
function jltma_import_template_from_data($template_data, $kit) {
    if (!$template_data || !isset($template_data['page_settings'])) {
        return false;
    }

    // Create WordPress page (publish by default, not draft)
    $page_id = wp_insert_post(array(
        'post_title' => $template_data['page_settings']['post_title'],
        'post_type' => $template_data['page_settings']['post_type'],
        'post_status' => 'publish',
        'post_content' => '',
        'meta_input' => array(
            '_elementor_edit_mode' => 'builder',
            '_elementor_template_type' => 'wp-page',
            '_elementor_version' => $template_data['version'],
            '_elementor_data' => wp_slash(json_encode($template_data['content'])),
            '_jltma_demo_import_item' => true,
            '_jltma_import_kit_id' => $kit,
            '_jltma_import_date' => current_time('mysql')
        )
    ));

    return is_wp_error($page_id) ? false : $page_id;
}

/**
** Import Global Settings from API
*/
function jltma_import_global_settings_from_api($global_settings) {
    // Import Elementor global colors
    if (isset($global_settings['colors'])) {
        $elementor_scheme = get_option('elementor_scheme_color', array());

        $color_mapping = array(
            'primary' => '1',
            'secondary' => '2',
            'accent' => '3'
        );

        foreach ($global_settings['colors'] as $color_name => $color_value) {
            if (isset($color_mapping[$color_name])) {
                $elementor_scheme[$color_mapping[$color_name]] = $color_value;
            }
        }

        update_option('elementor_scheme_color', $elementor_scheme);
    }

    // Import Elementor global fonts
    if (isset($global_settings['typography'])) {
        $elementor_scheme = get_option('elementor_scheme_typography', array());

        if (isset($global_settings['typography']['primary_font'])) {
            $elementor_scheme['1'] = array(
                'font_family' => $global_settings['typography']['primary_font'],
                'font_weight' => '400'
            );
        }

        if (isset($global_settings['typography']['secondary_font'])) {
            $elementor_scheme['2'] = array(
                'font_family' => $global_settings['typography']['secondary_font'],
                'font_weight' => '400'
            );
        }

        update_option('elementor_scheme_typography', $elementor_scheme);
    }
}

/**
** Reset Previous Import
*/
function jltma_reset_previous_import() {
    $nonce = $_POST['nonce'];

    if (!wp_verify_nonce($nonce, 'jltma-templates-kit-js') || !current_user_can('manage_options')) {
        exit; // Get out of here, the nonce is rotten!
    }

    $args = [
        'post_type' => [
            'page',
            'post',
            'product',
            'elementor_library',
            'attachment',
        ],
        'post_status' => 'any',
        'posts_per_page' => '-1',
        'meta_key' => '_jltma_demo_import_item'
    ];

    $imported_items = new WP_Query($args);

    if ($imported_items->have_posts()) {
        while ($imported_items->have_posts()) {
            $imported_items->the_post();

            // Dont Delete Elementor Kit
            if ('Default Kit' == get_the_title()) {
                continue;
            }

            // Delete Posts
            wp_delete_post(get_the_ID(), true);
        }

        // Reset
        wp_reset_query();

        $imported_terms = get_terms([
            'meta_key' => '_jltma_demo_import_item',
            'posts_per_page' => -1,
            'hide_empty' => false,
        ]);

        if (!empty($imported_terms)) {
            foreach($imported_terms as $imported_term) {
                // Delete Terms
                wp_delete_term($imported_term->term_id, $imported_term->taxonomy);
            }
        }

        wp_send_json_success(esc_html__('Previous Import Files have been successfully Reset.', 'master-addons'));
    } else {
        wp_send_json_success(esc_html__('There is no Data for Reset.', 'master-addons'));
    }
}

/**
** Final Settings Setup
*/
function jltma_final_settings_setup() {
    $nonce = $_POST['nonce'];

    if (!wp_verify_nonce($nonce, 'jltma-templates-kit-js') || !current_user_can('manage_options')) {
        exit; // Get out of here, the nonce is rotten!
    }

    $kit = !empty(get_option('jltma-import-kit-id')) ? esc_html(get_option('jltma-import-kit-id')) : '';

    // Import Elementor Site Settings
    jltma_import_elementor_site_settings($kit);

    // Setup Templates
    jltma_setup_templates($kit);

    // Fix Elementor Images
    jltma_fix_elementor_images();

    // Clear DB
    delete_option('jltma-import-kit-id');

    // Delete Hello World Post
    $post = get_page_by_path('hello-world', OBJECT, 'post');
    if ($post) {
        wp_delete_post($post->ID, true);
    }

    wp_send_json_success();
}

/**
** Activate Template Kit as Global Kit
*/
function jltma_activate_template_as_global_kit($template_id) {
    if (!$template_id || is_wp_error($template_id)) {
        return false;
    }
    
    // Check if this is actually a kit template
    $template_type = get_post_meta($template_id, '_elementor_template_type', true);
    if ($template_type !== 'kit') {
        return false;
    }
    
    // Set as active kit
    update_option('elementor_active_kit', $template_id);
    
    // Clear Elementor cache
    if (class_exists('\Elementor\Plugin')) {
        \Elementor\Plugin::$instance->files_manager->clear_cache();
    }
    
    return true;
}

/**
** Import Elementor Site Settings
*/
function jltma_import_elementor_site_settings($kit) {
    update_option('elementor_experiment-e_local_google_fonts', 'inactive');

    // Get Remote File
    $site_settings = @file_get_contents('https://master-addons.com/templates-kit/'. $kit .'/site-settings.json');

    if (false !== $site_settings) {
        $site_settings = json_decode($site_settings, true);

        if (!empty($site_settings['settings'])) {
            $default_kit = \Elementor\Plugin::$instance->documents->get_doc_for_frontend(get_option('elementor_active_kit'));

            $kit_settings = $default_kit->get_settings();
            $new_settings = $site_settings['settings'];
            $settings = array_merge($kit_settings, $new_settings);

            $default_kit->save(['settings' => $settings]);
        }
    }
}

/**
** Setup Templates
*/
function jltma_setup_templates($kit) {
    // Set Home & Blog Pages
    $home_page = get_page_by_path('home-'. $kit);
    $blog_page = get_page_by_path('blog-'. $kit);

    if ($home_page) {
        update_option('show_on_front', 'page');
        update_option('page_on_front', $home_page->ID);

        if ($blog_page) {
            update_option('page_for_posts', $blog_page->ID);
        }
    }
}

/**
** Fix Elementor Images
*/
function jltma_fix_elementor_images() {
    $args = array(
        'post_type' => ['page', 'elementor_library'],
        'posts_per_page' => '-1',
        'meta_key' => '_elementor_version'
    );
    $elementor_pages = new WP_Query($args);

    // Check that we have query results.
    if ($elementor_pages->have_posts()) {
        // Start looping over the query results.
        while ($elementor_pages->have_posts()) {
            $elementor_pages->the_post();

            // Replace Demo with Current
            $site_url = get_site_url();
            $site_url = str_replace('/', '\/', $site_url);
            $demo_site_url = 'https://demosites.master-addons.com/'. get_option('jltma-import-kit-id');
            $demo_site_url = str_replace('/', '\/', $demo_site_url);

            // Elementor Data
            $data = get_post_meta(get_the_ID(), '_elementor_data', true);

            if (!empty($data)) {
                // Handle both string and array cases
                if (is_string($data)) {
                    $data = preg_replace('/\\\{1}\/sites\\\{1}\/\d+/', '', $data);
                    $data = str_replace($demo_site_url, $site_url, $data);
                    $data = json_decode($data, true);
                } elseif (is_array($data)) {
                    // Data is already an array, process it directly
                    $data_string = wp_json_encode($data);
                    $data_string = preg_replace('/\\\{1}\/sites\\\{1}\/\d+/', '', $data_string);
                    $data_string = str_replace($demo_site_url, $site_url, $data_string);
                    $data = json_decode($data_string, true);
                }
            }

            // Sanitize Elementor data to prevent warnings
            if (is_array($data)) {
                $data = jltma_sanitize_elementor_data($data);
            }

            update_metadata('post', get_the_ID(), '_elementor_data', $data);

            // Elementor Page Settings
            $page_settings = get_post_meta(get_the_ID(), '_elementor_page_settings', true);
            $page_settings = json_encode($page_settings);

            if (!empty($page_settings)) {
                $page_settings = preg_replace('/\\\{1}\/sites\\\{1}\/\d+/', '', $page_settings);
                $page_settings = str_replace($demo_site_url, $site_url, $page_settings);
                $page_settings = json_decode($page_settings, true);
            }

            update_metadata('post', get_the_ID(), '_elementor_page_settings', $page_settings);
        }
    }

    // Clear Elementor Cache
    Plugin::$instance->files_manager->clear_cache();
}

/**
** Set Timeout for Image Request
*/
function jltma_set_image_request_timeout($timeout_value, $url) {
    if (strpos($url, 'https://master-addons.com/') === false) {
        return $timeout_value;
    }

    $valid_ext = preg_match('/^((https?:\/\/)|(www\.))([a-z0-9-].?)+(:[0-9]+)?\/[\w\-\@]+\.(jpg|png|gif|jpeg|svg)\/?$/i', $url);

    if ($valid_ext) {
        $timeout_value = 300;
    }

    return $timeout_value;
}

/**
** Prevent WooCommerce creating default pages
*/
function jltma_disable_default_woo_pages_creation() {
    add_filter('woocommerce_create_pages', '__return_empty_array');
}

/**
** Sanitize Elementor Data to prevent warnings
*/
function jltma_sanitize_elementor_data($data) {
    if (!is_array($data)) {
        return $data;
    }

    // Recursively process elements
    foreach ($data as &$element) {
        if (isset($element['elType']) && $element['elType'] === 'column') {
            // Ensure column has _column_size setting
            if (!isset($element['settings']['_column_size'])) {
                $element['settings']['_column_size'] = 100; // Default to full width
            }
        }

        // Sanitize element settings if they exist
        if (isset($element['settings']) && is_array($element['settings'])) {
            $element['settings'] = jltma_sanitize_element_settings($element['settings']);
        }

        // Recursively process nested elements
        if (isset($element['elements']) && is_array($element['elements'])) {
            $element['elements'] = jltma_sanitize_elementor_data($element['elements']);
        }
    }

    return $data;
}

/**
** Sanitize Element Settings (handles images and other settings)
*/
function jltma_sanitize_element_settings($settings) {
    if (!is_array($settings)) {
        return $settings;
    }

    // List of common image-related settings in Elementor
    $image_settings = [
        'image', 'background_image', 'hover_image', 'bg_image', 'icon_image',
        'gallery', 'images', 'slide_image', 'background_overlay_image',
        'testimonial_image', 'team_image', 'portfolio_image'
    ];

    foreach ($settings as $key => &$value) {
        // Handle image settings
        if (in_array($key, $image_settings) && is_array($value)) {
            // Ensure image array has required 'id' field
            if (!isset($value['id']) || empty($value['id'])) {
                $value['id'] = 0; // Use 0 as fallback for missing images
            }

            // Ensure other common image fields exist
            if (!isset($value['url'])) {
                $value['url'] = '';
            }
        }

        // Handle gallery settings (array of images)
        if ($key === 'gallery' && is_array($value)) {
            foreach ($value as &$gallery_item) {
                if (is_array($gallery_item) && !isset($gallery_item['id'])) {
                    $gallery_item['id'] = 0;
                }
                if (is_array($gallery_item) && !isset($gallery_item['url'])) {
                    $gallery_item['url'] = '';
                }
            }
        }

        // Recursively handle nested arrays
        if (is_array($value) && !in_array($key, $image_settings)) {
            $value = jltma_sanitize_element_settings($value);
        }
    }

    return $settings;
}

/**
** Search Query Results
*/
function jltma_search_query_results() {
    $search_query = isset($_POST['search_query']) ? sanitize_text_field(wp_unslash($_POST['search_query'])) : '';

    // Log search queries for analytics (optional)
    if (!empty($search_query)) {
    }
}

if (version_compare(get_bloginfo('version'), '5.1.0', '>=')) {
    add_filter('wp_check_filetype_and_ext', 'jltma_real_mime_types_5_1_0', 10, 5, 99);
} else {
    add_filter('wp_check_filetype_and_ext', 'jltma_real_mime_types', 10, 4);
}

function jltma_real_mime_types_5_1_0($defaults, $file, $filename, $mimes, $real_mime) {
    return jltma_real_mimes($defaults, $filename);
}

function jltma_real_mime_types($defaults, $file, $filename, $mimes) {
    return jltma_real_mimes($defaults, $filename);
}

function jltma_real_mimes($defaults, $filename) {
    if (strpos($filename, 'main') !== false) {
        $defaults['ext']  = 'xml';
        $defaults['type'] = 'text/xml';
    }

    return $defaults;
}

/**
** Import images to media library and replace URLs with attachment IDs
*/
function jltma_import_images_to_media($content, $kit_id, $kit_name = '') {
    if (is_string($content)) {
        $content = json_decode($content, true);
    }
    
    if (!is_array($content)) {
        return $content;
    }
    
    // Track imported images to avoid duplicates
    static $imported_images = [];
    
    // Recursively process elements
    foreach ($content as &$element) {
        if (isset($element['settings'])) {
            $element['settings'] = jltma_import_settings_images($element['settings'], $kit_id, $kit_name, $imported_images);
        }
        
        if (isset($element['elements']) && is_array($element['elements'])) {
            $element['elements'] = jltma_import_images_to_media($element['elements'], $kit_id, $kit_name);
        }
    }
    
    return $content;
}

/**
** Replace image URLs with cached versions (deprecated - kept for compatibility)
*/
function jltma_replace_with_cached_images($content, $kit_id, $cache_manager) {
    // Now redirects to the new media import function
    return jltma_import_images_to_media($content, $kit_id);
}

/**
** Import images in element settings to media library
*/
function jltma_import_settings_images($settings, $kit_id, $kit_name = '', &$imported_images = []) {
    if (!is_array($settings)) {
        return $settings;
    }
    
    // Image-related settings to check
    $image_settings = [
        'image', 'background_image', 'hover_image', 'bg_image', 'icon_image',
        'gallery', 'images', 'slide_image', 'background_overlay_image',
        'testimonial_image', 'team_image', 'portfolio_image', 'logo_image',
        'before_image', 'after_image', 'author_image', 'product_image',
        'featured_image', 'fallback_image', 'mobile_image', 'tablet_image'
    ];
    
    foreach ($settings as $key => &$value) {
        // Handle single image settings
        if (in_array($key, $image_settings) && is_array($value) && isset($value['url'])) {
            $attachment_data = jltma_import_single_image($value['url'], $kit_id, $kit_name, $imported_images);
            if ($attachment_data) {
                $value['id'] = $attachment_data['id'];
                $value['url'] = $attachment_data['url'];
            }
        }
        
        // Handle gallery settings (array of images)
        if ($key === 'gallery' && is_array($value)) {
            foreach ($value as &$gallery_item) {
                if (is_array($gallery_item) && isset($gallery_item['url'])) {
                    $attachment_data = jltma_import_single_image($gallery_item['url'], $kit_id, $kit_name, $imported_images);
                    if ($attachment_data) {
                        $gallery_item['id'] = $attachment_data['id'];
                        $gallery_item['url'] = $attachment_data['url'];
                    }
                }
            }
        }
        
        // Handle repeater fields that might contain images
        if (is_array($value) && !in_array($key, $image_settings)) {
            foreach ($value as &$repeater_item) {
                if (is_array($repeater_item)) {
                    $repeater_item = jltma_import_settings_images($repeater_item, $kit_id, $kit_name, $imported_images);
                }
            }
        }
        
        // Handle CSS strings that might contain background images
        if (is_string($value) && (strpos($key, 'css') !== false || strpos($key, 'style') !== false)) {
            $value = jltma_import_css_images($value, $kit_id, $kit_name, $imported_images);
        }
    }
    
    return $settings;
}

/**
** Import images from CSS strings
*/
function jltma_import_css_images($css_string, $kit_id, $kit_name = '', &$imported_images = []) {
    if (empty($css_string)) {
        return $css_string;
    }
    
    // Find all URLs in CSS
    preg_match_all('/url\([\'"]?([^\'")]+)[\'"]?\)/i', $css_string, $matches);
    
    if (!empty($matches[1])) {
        foreach ($matches[1] as $url) {
            // Skip data URIs and relative paths
            if (strpos($url, 'data:') === 0 || strpos($url, 'http') !== 0) {
                continue;
            }
            
            // Check if it's an image
            if (preg_match('/\.(jpg|jpeg|png|gif|svg|webp)$/i', $url)) {
                $attachment_data = jltma_import_single_image($url, $kit_id, $kit_name, $imported_images);
                if ($attachment_data) {
                    // Replace the URL in CSS
                    $css_string = str_replace($url, $attachment_data['url'], $css_string);
                }
            }
        }
    }
    
    return $css_string;
}

/**
** Import a single image to media library
*/
function jltma_import_single_image($image_url, $kit_id, $kit_name = '', &$imported_images = []) {
    if (empty($image_url) || !filter_var($image_url, FILTER_VALIDATE_URL)) {
        return false;
    }
    
    // Check if we've already imported this image
    if (isset($imported_images[$image_url])) {
        return $imported_images[$image_url];
    }
    
    // Check if image already exists in media library by URL
    $existing_attachment = jltma_get_attachment_by_url($image_url);
    if ($existing_attachment) {
        $attachment_data = [
            'id' => $existing_attachment,
            'url' => wp_get_attachment_url($existing_attachment)
        ];
        $imported_images[$image_url] = $attachment_data;
        return $attachment_data;
    }
    
    // Download and import the image
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    
    // Download image to temp file with error handling
    $tmp = download_url($image_url, 300); // 5 minutes timeout
    
    if (is_wp_error($tmp)) {
        // Try alternative download method for problematic URLs
        $response = wp_remote_get($image_url, [
            'timeout' => 300,
            'sslverify' => false
        ]);
        
        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return false;
        }
        
        $image_data = wp_remote_retrieve_body($response);
        $tmp = wp_tempnam();
        file_put_contents($tmp, $image_data);
        
        if (!file_exists($tmp) || filesize($tmp) === 0) {
            @unlink($tmp);
            return false;
        }
    }
    
    // Get filename from URL
    $file_array = [];
    $file_array['name'] = basename($image_url);
    
    // If filename doesn't have extension, try to determine it
    if (!pathinfo($file_array['name'], PATHINFO_EXTENSION)) {
        $file_info = wp_check_filetype($tmp);
        if ($file_info['ext']) {
            $file_array['name'] = 'image-' . uniqid() . '.' . $file_info['ext'];
        }
    }
    
    // Add kit name prefix to filename for organization
    if (!empty($kit_name)) {
        $file_array['name'] = sanitize_title($kit_name) . '-' . $file_array['name'];
    }
    
    $file_array['tmp_name'] = $tmp;
    
    // Upload the image to media library
    $attachment_id = media_handle_sideload($file_array, 0, null, [
        'post_title' => !empty($kit_name) ? $kit_name . ' - ' . pathinfo($file_array['name'], PATHINFO_FILENAME) : pathinfo($file_array['name'], PATHINFO_FILENAME),
        'post_content' => '',
        'post_status' => 'inherit'
    ]);
    
    // Clean up temp file
    @unlink($tmp);
    
    if (is_wp_error($attachment_id)) {
        return false;
    }
    
    // Add metadata to track this is an imported template image
    update_post_meta($attachment_id, '_jltma_imported_from_kit', $kit_id);
    update_post_meta($attachment_id, '_jltma_original_url', $image_url);
    update_post_meta($attachment_id, '_jltma_import_date', current_time('mysql'));
    if (!empty($kit_name)) {
        update_post_meta($attachment_id, '_jltma_kit_name', $kit_name);
    }
    
    // Set alt text if not present
    $alt_text = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
    if (empty($alt_text)) {
        update_post_meta($attachment_id, '_wp_attachment_image_alt', $kit_name ?: 'Template Image');
    }
    
    $attachment_data = [
        'id' => $attachment_id,
        'url' => wp_get_attachment_url($attachment_id)
    ];
    
    // Cache the result
    $imported_images[$image_url] = $attachment_data;
    
    return $attachment_data;
}

/**
** Get attachment ID by URL
*/
function jltma_get_attachment_by_url($url) {
    global $wpdb;
    
    // Clean the URL for comparison
    $clean_url = str_replace(['https://', 'http://'], '', $url);
    
    // First, try to find by exact URL match
    $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s' AND post_type='attachment';", $url));
    if (!empty($attachment)) {
        return $attachment[0];
    }
    
    // Try without protocol
    $attachment = $wpdb->get_col($wpdb->prepare(
        "SELECT ID FROM $wpdb->posts WHERE (REPLACE(REPLACE(guid, 'https://', ''), 'http://', '') = '%s') AND post_type='attachment' LIMIT 1;",
        $clean_url
    ));
    if (!empty($attachment)) {
        return $attachment[0];
    }
    
    // Try to find by original URL meta
    $attachment = $wpdb->get_col($wpdb->prepare(
        "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_jltma_original_url' AND meta_value='%s' LIMIT 1;",
        $url
    ));
    if (!empty($attachment)) {
        return $attachment[0];
    }
    
    return false;
}

/**
** Clean up orphaned template images
*/
add_action('wp_ajax_jltma_cleanup_template_images', 'jltma_cleanup_template_images');
function jltma_cleanup_template_images() {
    if (!wp_verify_nonce($_POST['_wpnonce'], 'jltma_template_kits_nonce_action') || !current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Permission denied']);
        return;
    }
    
    global $wpdb;
    
    // Find all attachments imported from templates
    $template_attachments = $wpdb->get_col(
        "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_jltma_imported_from_kit'"
    );
    
    $deleted_count = 0;
    $kept_count = 0;
    
    foreach ($template_attachments as $attachment_id) {
        // Check if this attachment is used anywhere
        $is_used = false;
        
        // Check in post content
        $used_in_content = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $wpdb->posts WHERE post_content LIKE '%wp-image-%d%' OR post_content LIKE '%attachment_%d%'",
            $attachment_id, $attachment_id
        ));
        
        if ($used_in_content > 0) {
            $is_used = true;
        }
        
        // Check in Elementor data
        if (!$is_used) {
            $attachment_url = wp_get_attachment_url($attachment_id);
            $used_in_elementor = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $wpdb->postmeta WHERE meta_key='_elementor_data' AND meta_value LIKE %s",
                '%"id":' . $attachment_id . '%'
            ));
            
            if ($used_in_elementor > 0) {
                $is_used = true;
            }
        }
        
        if (!$is_used) {
            wp_delete_attachment($attachment_id, true);
            $deleted_count++;
        } else {
            $kept_count++;
        }
    }
    
    wp_send_json_success([
        'message' => sprintf('Cleanup complete: %d orphaned images deleted, %d in use kept', $deleted_count, $kept_count),
        'deleted' => $deleted_count,
        'kept' => $kept_count
    ]);
}

/**
** Pre-import all images for a template kit to media library
*/
add_action('wp_ajax_jltma_preimport_kit_images', 'jltma_preimport_kit_images');
function jltma_preimport_kit_images() {
    if (!wp_verify_nonce($_POST['_wpnonce'], 'jltma_template_kits_nonce_action') || !current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Permission denied']);
        return;
    }
    
    $kit_id = isset($_POST['kit_id']) ? sanitize_text_field($_POST['kit_id']) : '';
    $template_data = isset($_POST['template_data']) ? wp_unslash($_POST['template_data']) : '';
    
    if (empty($kit_id)) {
        wp_send_json_error(['message' => 'Kit ID is required']);
        return;
    }
    
    if (!empty($template_data)) {
        $decoded = json_decode($template_data, true);
        if ($decoded && isset($decoded['content'])) {
            // Extract all image URLs from the template
            $image_urls = jltma_extract_all_image_urls($decoded['content']);
            $imported_count = 0;
            $imported_images = [];
            
            foreach ($image_urls as $url) {
                $result = jltma_import_single_image($url, $kit_id, $decoded['title'] ?? '', $imported_images);
                if ($result) {
                    $imported_count++;
                }
            }
            
            wp_send_json_success([
                'message' => sprintf('Successfully imported %d images to media library', $imported_count),
                'image_count' => $imported_count,
                'imported_images' => $imported_images
            ]);
        }
    }
    
    wp_send_json_error(['message' => 'Invalid template data']);
}

/**
** Extract all image URLs from Elementor content
*/
function jltma_extract_all_image_urls($content, &$urls = []) {
    if (is_string($content)) {
        $content = json_decode($content, true);
    }
    
    if (!is_array($content)) {
        return $urls;
    }
    
    foreach ($content as $element) {
        if (isset($element['settings']) && is_array($element['settings'])) {
            jltma_extract_urls_from_settings($element['settings'], $urls);
        }
        
        if (isset($element['elements']) && is_array($element['elements'])) {
            jltma_extract_all_image_urls($element['elements'], $urls);
        }
    }
    
    return array_unique($urls);
}

/**
** Extract image URLs from element settings
*/
function jltma_extract_urls_from_settings($settings, &$urls) {
    if (!is_array($settings)) {
        return;
    }
    
    foreach ($settings as $key => $value) {
        if (is_array($value)) {
            // Check if it's an image array with URL
            if (isset($value['url']) && filter_var($value['url'], FILTER_VALIDATE_URL)) {
                $urls[] = $value['url'];
            }
            // Recursively check nested arrays
            jltma_extract_urls_from_settings($value, $urls);
        } elseif (is_string($value)) {
            // Check if the string contains image URLs
            if (preg_match_all('/(https?:\/\/[^\s"]+\.(?:jpg|jpeg|png|gif|svg|webp))/i', $value, $matches)) {
                $urls = array_merge($urls, $matches[1]);
            }
        }
    }
}

/**
** Pre-cache all images for a template kit (deprecated - kept for compatibility)
*/
add_action('wp_ajax_jltma_precache_kit_images', 'jltma_precache_kit_images');
function jltma_precache_kit_images() {
    // Redirect to new preimport function
    jltma_preimport_kit_images();
}

/**
** Pre-download template kit for faster import
*/
function jltma_predownload_kit() {
    if (!wp_verify_nonce($_POST['_wpnonce'], 'jltma_template_kits_nonce_action') || !current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Permission denied']);
        return;
    }

    $kit_id = isset($_POST['kit_id']) ? sanitize_text_field($_POST['kit_id']) : '';

    if (empty($kit_id)) {
        wp_send_json_error(['message' => 'Kit ID is required']);
        return;
    }

    // Get cache manager instance
    $cache_manager = null;
    if (class_exists('MasterAddons\Inc\Templates\Classes\JLTMA_Plugin_Template_Kit_Cache')) {
        $cache_manager = \MasterAddons\Inc\Templates\Classes\JLTMA_Plugin_Template_Kit_Cache::get_instance();
    }

    if (!$cache_manager) {
        wp_send_json_error(['message' => 'Cache manager not available']);
        return;
    }

    // Download and cache the kit
    $result = $cache_manager->download_and_cache_kit($kit_id);

    if ($result && $result['success']) {
        // Count templates in manifest
        $template_count = 0;
        if (isset($result['manifest']['pages']) && is_array($result['manifest']['pages'])) {
            $template_count = count($result['manifest']['pages']);
        }

        wp_send_json_success([
            'message' => sprintf('Kit pre-downloaded successfully with %d templates', $template_count),
            'kit_id' => $kit_id,
            'template_count' => $template_count,
            'cached' => true
        ]);
    } else {
        wp_send_json_error(['message' => 'Failed to download kit']);
    }
}

/**
** Download all template kits for offline use
*/
function jltma_download_all_kits() {
    if (!wp_verify_nonce($_POST['_wpnonce'], 'jltma_template_kits_nonce_action') || !current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Permission denied']);
        return;
    }

    // Get cache manager instance
    $cache_manager = null;
    if (class_exists('MasterAddons\Inc\Templates\Classes\JLTMA_Plugin_Template_Kit_Cache')) {
        $cache_manager = \MasterAddons\Inc\Templates\Classes\JLTMA_Plugin_Template_Kit_Cache::get_instance();
    }

    if (!$cache_manager) {
        wp_send_json_error(['message' => 'Cache manager not available']);
        return;
    }

    // Download all kits
    $stats = $cache_manager->download_all_kits();

    wp_send_json_success([
        'message' => sprintf(
            'Download complete: %d downloaded, %d skipped, %d failed out of %d total kits',
            $stats['downloaded'],
            $stats['skipped'],
            $stats['failed'],
            $stats['total']
        ),
        'stats' => $stats
    ]);
}

/**
** Install required plugin for template kit
*/
function jltma_install_required_plugin() {
    // Accept both template kits and template library nonces
    $valid_nonce = wp_verify_nonce($_POST['_wpnonce'], 'jltma_template_kits_nonce_action') ||
                   wp_verify_nonce($_POST['_wpnonce'], 'jltma_template_library_nonce');

    if (!$valid_nonce || !current_user_can('install_plugins')) {
        wp_send_json_error(['message' => 'Permission denied']);
        return;
    }

    // $plugin_slug = isset($_POST['plugin_slug']) ? sanitize_text_field($_POST['plugin_slug']) : '';
    $plugin_name = isset($_POST['plugin_name']) ? sanitize_text_field($_POST['plugin_name']) : '';
    $plugin_file = isset($_POST['plugin_file']) ? sanitize_text_field($_POST['plugin_file']) : '';
    $plugin_slug = get_plugin_slug($plugin_file);
    
    if (empty($plugin_slug)) {
        wp_send_json_error(['message' => 'Plugin slug is required']);
        return;
    }
    
    // Include necessary files
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/misc.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
    
    if (empty($plugin_file)) {
        $plugin_file = $plugin_slug . '/' . $plugin_slug . '.php';
    }
    
    // Check if plugin is already installed
    $installed_plugins = get_plugins();
    if (isset($installed_plugins[$plugin_file])) {
        // Plugin is already installed, try to activate it
        $activate_result = activate_plugin($plugin_file);

        if (is_wp_error($activate_result)) {
            wp_send_json_error([
                'message' => 'Plugin installed but activation failed: ' . $activate_result->get_error_message()
            ]);
        } else {
            wp_send_json_success([
                'message' => sprintf('%s has been activated successfully!', $plugin_name ?: $plugin_slug),
                'is_active' => true
            ]);
        }
        return;
    }

    $api = plugins_api('plugin_information', [
        'slug' => $plugin_slug,
        'fields' => [
            'sections' => false,
            'tags' => false
        ]
    ]);

    if (is_wp_error($api)) {
        if( $api->get_error_message() === "Plugin not found."){
            $plugin_file = explode('/', $plugin_file);
            if($plugin_file[0] !== $plugin_file[1]){
                $plugin_slug = $plugin_file[0];
            }
        }
        $new_api = plugins_api('plugin_information', [
            'slug' => $plugin_slug,
            'fields' => [
                'sections' => false,
                'tags' => false
            ]
        ]);
        if ( is_wp_error($new_api)) {
            wp_send_json_error([
                'message' => 'Failed to get plugin information: ',
                'details' => $api->get_error_message()
            ]);
            return;
        }else{
            $api = $new_api;
        }
        
    }

    // Set up the plugin upgrader
    $skin = new WP_Ajax_Upgrader_Skin();
    $upgrader = new Plugin_Upgrader($skin);

    // Install the plugin
    $result = $upgrader->install($api->download_link);

    if (is_wp_error($result)) {
        wp_send_json_error([
            'message' => 'Installation failed: ' . $result->get_error_message()
        ]);
        return;
    }

    if (!$result) {
        wp_send_json_error([
            'message' => 'Installation failed for unknown reason'
        ]);
        return;
    }

    // Get the installed plugin file
    $installed_plugins = get_plugins();
    $plugin_installed = false;

    foreach ($installed_plugins as $file => $plugin) {
        if (strpos($file, $plugin_slug . '/') === 0 || $file === $plugin_file) {
            $plugin_file = $file;
            $plugin_installed = true;
            break;
        }
    }

    if (!$plugin_installed) {
        wp_send_json_error([
            'message' => 'Plugin installed but could not be found'
        ]);
        return;
    }

    // Try to activate the plugin
    $activate_result = activate_plugin($plugin_file);

    if (is_wp_error($activate_result)) {
        wp_send_json_success([
            'message' => sprintf('%s has been installed successfully but needs manual activation.', $plugin_name ?: $plugin_slug),
            'is_active' => false
        ]);
    } else {
        wp_send_json_success([
            'message' => sprintf('%s has been installed and activated successfully!', $plugin_name ?: $plugin_slug),
            'is_active' => true
        ]);
    }
}

/**
** Activate required plugin for template kit
*/
function jltma_activate_required_plugin() {
    // Accept both template kits and template library nonces
    $valid_nonce = wp_verify_nonce($_POST['_wpnonce'], 'jltma_template_kits_nonce_action') ||
                   wp_verify_nonce($_POST['_wpnonce'], 'jltma_template_library_nonce');

    if (!$valid_nonce || !current_user_can('activate_plugins')) {
        wp_send_json_error(['message' => 'Permission denied']);
        return;
    }

    $plugin_slug = isset($_POST['plugin_slug']) ? sanitize_text_field($_POST['plugin_slug']) : '';
    $plugin_file = isset($_POST['plugin_file']) ? sanitize_text_field($_POST['plugin_file']) : '';
    if (empty($plugin_slug) && empty( $plugin_file)) {
        wp_send_json_error(['message' => 'Plugin slug is required']);
        return;
    }
    
    if( ! is_string($plugin_slug) ){
        $file_name = explode('/', $plugin_file)[1];
        
        $plugin_slug = explode('.', $file_name)[0];
    }
    // Include necessary files
    require_once ABSPATH . 'wp-admin/includes/plugin.php';

    // If plugin_file is not provided and it's not a theme, use default structure
    if (empty($plugin_file) && $plugin_file !== 'theme') {
        $plugin_file = $plugin_slug . '/' . $plugin_slug . '.php';
    }

    // Special handling for themes
    if ($plugin_file === 'theme') {
        // This is a theme, not a plugin
        $theme = wp_get_theme($plugin_slug);

        if (!$theme->exists()) {
            wp_send_json_error(['message' => 'Theme not found. Please install it first.']);
            return;
        }

        // Switch to the theme
        switch_theme($plugin_slug);

        wp_send_json_success([
            'message' => ucfirst(str_replace('-', ' ', $plugin_slug)) . ' theme has been activated successfully!',
            'is_active' => true
        ]);
        return;
    }

    // Check if plugin exists
    $installed_plugins = get_plugins();
    if (!isset($installed_plugins[$plugin_file])) {
        // Try to find the plugin with a different file structure
        $plugin_found = false;
        foreach ($installed_plugins as $file => $plugin) {
            if (strpos($file, $plugin_slug . '/') === 0) {
                $plugin_file = $file;
                $plugin_found = true;
                break;
            }
        }

        if (!$plugin_found) {
            wp_send_json_error(['message' => 'Plugin not found. Please install it first.']);
            return;
        }
    }

    // Check if plugin is already active
    if (is_plugin_active($plugin_file)) {
        wp_send_json_success([
            'message' => 'Plugin is already active!',
            'is_active' => true
        ]);
        return;
    }

    // Try to activate the plugin
    $result = activate_plugin($plugin_file);

    if (is_wp_error($result)) {
        wp_send_json_error([
            'message' => 'Activation failed: ' . $result->get_error_message()
        ]);
    } else {
        wp_send_json_success([
            'message' => 'Plugin activated successfully!',
            'is_active' => true
        ]);
    }
}

function get_plugin_slug($plugin_file) {
    $without_extension = pathinfo($plugin_file, PATHINFO_FILENAME);

    $parts = explode('/', $without_extension);
    $filename = end($parts);

    return $filename;
}

/**
** Handle Template Kit Upload
*/
function jltma_upload_template_kit() {
    $valid_nonce = wp_verify_nonce($_POST['_wpnonce'], 'jltma_template_kits_nonce_action') ||
                   wp_verify_nonce($_POST['_wpnonce'], 'jltma_template_library_nonce');
    if (!$valid_nonce || !current_user_can('upload_files')) {
        wp_send_json_error(['message' => 'Permission denied']);
        return;
    }

    // Check if file was uploaded
    if (!isset($_FILES['kit_file']) || $_FILES['kit_file']['error'] !== UPLOAD_ERR_OK) {
        wp_send_json_error(['message' => 'No file uploaded or upload failed']);
        return;
    }

    $uploaded_file = $_FILES['kit_file'];

    // Validate file type
    $file_type = wp_check_filetype($uploaded_file['name']);
    if ($file_type['ext'] !== 'zip') {
        wp_send_json_error(['message' => 'Please upload a valid ZIP file']);
        return;
    }

    // Get cache manager instance
    $cache_manager = null;
    if (class_exists('MasterAddons\Inc\Templates\Classes\JLTMA_Plugin_Template_Kit_Cache')) {
        $cache_manager = \MasterAddons\Inc\Templates\Classes\JLTMA_Plugin_Template_Kit_Cache::get_instance();
    }

    if (!$cache_manager) {
        wp_send_json_error(['message' => 'Cache manager not available']);
        return;
    }

    $file_name_without_ext = pathinfo($uploaded_file['name'], PATHINFO_FILENAME);

    if (preg_match('/^kit[_-]?(\d+)$/i', $file_name_without_ext, $matches)) {
        $kit_id = $matches[1];
    } else {
        $kit_id = (string)(9000 + time() % 1000);
    }

    global $wp_filesystem;
    if (!function_exists('WP_Filesystem')) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }
    WP_Filesystem();

    $upload_dir = wp_upload_dir();
    $kits_dir = $upload_dir['basedir'] . '/master_addons/purchased_kits/kits/kit_' . $kit_id;

    if (!$wp_filesystem->exists($kits_dir)) {
        $wp_filesystem->mkdir($kits_dir, 0755, true);
    }

    // Extract ZIP file
    $unzip_result = unzip_file($uploaded_file['tmp_name'], $kits_dir);

    if (is_wp_error($unzip_result)) {
        // Clean up on error
        $wp_filesystem->delete($kits_dir, true);
        wp_send_json_error(['message' => 'Failed to extract ZIP file: ' . $unzip_result->get_error_message()]);
        return;
    }

    // Look for manifest.json in the extracted files
    $manifest_path = $kits_dir . '/manifest.json';
    $manifest = null;

    if ($wp_filesystem->exists($manifest_path)) {
        $manifest_content = $wp_filesystem->get_contents($manifest_path);
        $manifest = json_decode($manifest_content, true);
    } else {
        // Check if files are in a subdirectory
        $files = $wp_filesystem->dirlist($kits_dir);
        foreach ($files as $file) {
            if ($file['type'] === 'd') {
                $sub_manifest = $kits_dir . '/' . $file['name'] . '/manifest.json';
                if ($wp_filesystem->exists($sub_manifest)) {
                    // Move contents up one level
                    $sub_dir = $kits_dir . '/' . $file['name'];
                    $sub_files = $wp_filesystem->dirlist($sub_dir);
                    foreach ($sub_files as $sub_file) {
                        $wp_filesystem->move($sub_dir . '/' . $sub_file['name'], $kits_dir . '/' . $sub_file['name']);
                    }
                    $wp_filesystem->delete($sub_dir, true);

                    $manifest_content = $wp_filesystem->get_contents($manifest_path);
                    $manifest = json_decode($manifest_content, true);
                    break;
                }
            }
        }
    }

    if (!$manifest) {
        // Clean up if no manifest found
        $wp_filesystem->delete($kits_dir, true);
        wp_send_json_error(['message' => 'Invalid template kit: manifest.json not found']);
        return;
    }

    // Validate required manifest fields
    $required_manifest_fields = ['manifest_version', 'title', 'kit_version', 'templates'];
    $missing_fields = [];

    foreach ($required_manifest_fields as $field) {
        if (!isset($manifest[$field])) {
            $missing_fields[] = $field;
        }
    }

    if (!empty($missing_fields)) {
        $wp_filesystem->delete($kits_dir, true);
        wp_send_json_error(['message' => 'Invalid manifest.json. Missing required fields: ' . implode(', ', $missing_fields)]);
        return;
    }

    // Validate templates array
    if (!is_array($manifest['templates']) || empty($manifest['templates'])) {
        $wp_filesystem->delete($kits_dir, true);
        wp_send_json_error(['message' => 'Invalid template kit: templates array is empty or invalid']);
        return;
    }

    // Check for required folders
    $screenshots_dir = $kits_dir . '/screenshots';
    $templates_dir = $kits_dir . '/templates';

    // Create folders info for validation message
    $missing_folders = [];

    if (!$wp_filesystem->exists($screenshots_dir)) {
        $missing_folders[] = 'screenshots';
    }

    if (!$wp_filesystem->exists($templates_dir)) {
        $missing_folders[] = 'templates';
    }

    if (!empty($missing_folders)) {
        $wp_filesystem->delete($kits_dir, true);
        wp_send_json_error(['message' => 'Invalid template kit structure. Missing required folders: ' . implode(', ', $missing_folders)]);
        return;
    }

    // Count templates and process pages
    $template_count = 0;
    $templates = [];

    // Use templates from manifest (already validated above)
    $manifest_templates = $manifest['templates'];
    $template_count = count($manifest_templates);

    // Process each template in the manifest
    foreach ($manifest_templates as $page) {
        // Get the slug or filename
        $slug = $page['slug'] ?? $page['fileName'] ?? $page['name'] ?? '';

        // Read the template JSON file from templates folder
        $template_file = $templates_dir . '/' . $slug . '.json';
        if (!$wp_filesystem->exists($template_file)) {
            // Try without .json extension if slug already includes it
            $template_file = $templates_dir . '/' . $slug;
        }

        if ($wp_filesystem->exists($template_file)) {
            $template_content = $wp_filesystem->get_contents($template_file);
            $template_data = json_decode($template_content, true);

            // Get thumbnail from screenshots folder if available
            $thumbnail = '';
            if (isset($page['thumbnail']) && !empty($page['thumbnail'])) {
                // Check if it's a relative path to screenshots folder
                if (strpos($page['thumbnail'], 'http') !== 0) {
                    $thumbnail_file = $screenshots_dir . '/' . basename($page['thumbnail']);
                    if ($wp_filesystem->exists($thumbnail_file)) {
                        // Store reference to the file location
                        $thumbnail = 'screenshots/' . basename($page['thumbnail']);
                    } else {
                        $thumbnail = $page['thumbnail'];
                    }
                } else {
                    $thumbnail = $page['thumbnail'];
                }
            }

            $templates[] = [
                'id' => $page['id'] ?? $page['slug'] ?? $slug,
                'slug' => $slug,
                'title' => $page['title'] ?? $page['name'] ?? $slug,
                'thumbnail' => $thumbnail,
                'content' => $template_data['content'] ?? $template_data,
                'page_settings' => $template_data['page_settings'] ?? [],
                'type' => $page['type'] ?? 'page',
                'template_id' => $page['template_id'] ?? $page['id'] ?? ''
            ];
        }
    }

    // Get the thumbnail from manifest
    $kit_thumbnail = '';
    if (isset($manifest['thumbnail_url']) && !empty($manifest['thumbnail_url'])) {
        $kit_thumbnail = $manifest['thumbnail_url'];
    } elseif (isset($manifest['thumbnail']) && !empty($manifest['thumbnail'])) {
        // Handle relative thumbnail path
        if (strpos($manifest['thumbnail'], 'http') !== 0) {
            // It's a relative path, try to find the image in the kit directory
            $thumbnail_path = str_replace('screenshots/', '', $manifest['thumbnail']);
            if ($wp_filesystem->exists($kits_dir . '/' . $thumbnail_path)) {
                $kit_thumbnail = $manifest['thumbnail'];
            }
        } else {
            $kit_thumbnail = $manifest['thumbnail'];
        }
    } elseif (!empty($templates) && isset($templates[0]['thumbnail'])) {
        // Fallback to first template thumbnail
        $kit_thumbnail = $templates[0]['thumbnail'];
    }

    // Get the actual kit name from manifest - use title field which is what the manifest has
    $kit_name = isset($manifest['title']) && !empty($manifest['title']) ? $manifest['title'] : (
        isset($manifest['name']) && !empty($manifest['name']) ? $manifest['name'] : 'Kit ' . $kit_id
    );

    // Process categories - convert string to array if needed
    $categories = ['purchased']; // Always include 'purchased'
    if (isset($manifest['categories'])) {
        if (is_array($manifest['categories'])) {
            $categories = array_merge($categories, $manifest['categories']);
        } elseif (is_string($manifest['categories']) && !empty($manifest['categories'])) {
            if (strpos($manifest['categories'], ',') !== false) {
                $cats = array_map('trim', explode(',', $manifest['categories']));
                $categories = array_merge($categories, $cats);
            } else {
                $categories[] = trim($manifest['categories']);
            }
        }
    }
    $categories = array_unique($categories);

    // Process keywords - convert string to array if needed
    $keywords = [];
    if (isset($manifest['keywords'])) {
        if (is_array($manifest['keywords'])) {
            $keywords = $manifest['keywords'];
        } elseif (is_string($manifest['keywords']) && !empty($manifest['keywords'])) {
            // Split by comma
            $keywords = array_map('trim', explode(',', $manifest['keywords']));
            $keywords = array_filter($keywords); // Remove empty values
        }
    }

    // Extract required plugins from manifest
    $required_plugins = [];
    if (isset($manifest['required_plugins']) && is_array($manifest['required_plugins'])) {
        $required_plugins = $manifest['required_plugins'];
    } elseif (isset($manifest['requirements']) && is_array($manifest['requirements'])) {
        // Some manifests might use 'requirements' key
        $required_plugins = $manifest['requirements'];
    }

    // Prepare kit data for purchased storage with same structure as cached kits
    $kit_data = [
        'kit_id' => $kit_id, 
        'template_id' => $kit_id,
        'kit_name' => $kit_name,
        'title' => $kit_name,
        'descriptions' => isset($manifest['description']) ? $manifest['description'] : 'Uploaded template kit',
        'description' => isset($manifest['description']) ? $manifest['description'] : 'Uploaded template kit',
        'author' => isset($manifest['author']) ? $manifest['author'] : 'Unknown',
        'categories' => $categories,
        'keywords' => $keywords,
        'thumbnail' => $kit_thumbnail,
        'preview_url' => isset($manifest['preview_url']) ? $manifest['preview_url'] : '',
        'is_purchased' => true,
        'purchasable' => false,
        'downloadable' => true, 
        'is_pro' => false, 
        'downloads' => isset($manifest['downloads']) ? $manifest['downloads'] : 0,
        'purchase_url' => '', 
        'uploaded_date' => current_time('mysql'),
        'template_count' => isset($manifest['template_count']) ? $manifest['template_count'] : $template_count,
        'templates' => $templates,
        'required_plugins' => $required_plugins, 
        'manifest' => $manifest,
        'kit_path' => $kits_dir 
    ];

    // Store as purchased kit using cache manager
    $stored = $cache_manager->store_purchased_kit($kit_data);
    if (!$stored) {
        $wp_filesystem->delete($kits_dir, true);
        wp_send_json_error(['message' => 'Failed to store template kit']);
        return;
    }

    // Create a metadata file for tracking purposes
    $meta_data = [
        'kit_id' => $kit_id,
        'uploaded_date' => $kit_data['uploaded_date'],
        'original_filename' => $uploaded_file['name'],
        'is_purchased' => true
    ];

    $wp_filesystem->put_contents(
        $kits_dir . '/meta.json',
        wp_json_encode($meta_data, JSON_PRETTY_PRINT)
    );

    wp_send_json_success([
        'kit_id' => $kit_id,
        'name' => $kit_data['kit_name'],
        'template_count' => $template_count,
        'is_purchased' => true,
        'message' => sprintf('Template kit "%s" uploaded successfully with %d templates', $kit_data['kit_name'], $template_count)
    ]);
}