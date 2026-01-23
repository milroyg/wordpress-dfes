<?php
namespace MasterAddons\Inc\Classes\Importer;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Master Addons Demo Importer
 * Complete standalone demo import system
 * No external plugin dependencies
 *
 * @package MasterAddons
 * @subpackage Importer
 * @since 2.0.0
 */
class JLTMA_Demo_Importer {
  private static $instance = null;
  public $demo_path;
  public $demo_url;
  public $has_variations = false;
  public $current_variation = null;
  public $manifest_data = array();

  public function __construct(){
    add_action( 'after_setup_theme', [$this, 'init'], 20 );
  }

  public static function get_instance() {
    if (self::$instance === null) {
        self::$instance = new self();
    }
    return self::$instance;
}

  public function init(){
    if ( ! apply_filters( 'jltma_demo_importer_enabled', false ) ||  ! current_user_can( 'manage_options' )  ) {
        return;
    }

    $this->set_demo_path();
    add_action( 'admin_menu', array(&$this, 'register_sub_menu') );

    // // AJAX handlers for demo import
    add_action( 'wp_ajax_jltma_mark_imported_template', array($this, 'ajax_mark_imported_template') );

    add_action( 'wp_ajax_jltma_import_demo_widgets', array($this, 'ajax_import_widgets') );

    // AJAX handler for importing templates (similar to widgets)
    add_action( 'wp_ajax_jltma_import_demo_template', array($this, 'ajax_import_template') );

    // AJAX handler for fetching templates
    add_action( 'wp_ajax_jltma_get_demo_templates', array($this, 'ajax_get_templates') );

    // AJAX handler for fetching variations
    add_action( 'wp_ajax_jltma_get_demo_variations', array($this, 'ajax_get_variations') );

    // AJAX handler for importing a full variation
    add_action( 'wp_ajax_jltma_import_full_variation', array($this, 'ajax_import_full_variation') );

    // Enqueue scripts
    add_action( 'admin_enqueue_scripts', array($this, 'enqueue_scripts') );
  }

  public function set_demo_path(){
    // Get manifest data from filter
    $this->manifest_data = apply_filters( 'jltma_demo_importer_variations', array() );

    // Set demo path from manifest or use default
    if (!empty($this->manifest_data['demo_directory'])) {
        $this->demo_path = $this->manifest_data['demo_directory'];
        $this->demo_url = str_replace(ABSPATH, site_url('/'), $this->demo_path);
        $this->has_variations = true;
    } else {
        // Fallback to old structure
        $theme_demo_folder = 'jltma_demo';
        $this->demo_path = get_stylesheet_directory( ) . '/' .  $theme_demo_folder;
        $this->demo_url = get_stylesheet_directory_uri( ) . '/' .  $theme_demo_folder;
        $this->has_variations = false;
    }
  }


  public function register_sub_menu(){
    $menu_args = [
        'parent_slug' => 'master-addons-settings',
        'page_title' => esc_html__('Demo Importer', 'master-addons'),
        'menu_title' => esc_html__('Demo Import', 'master-addons'),
        'capability' => 'manage_options',
        'menu_slug' => 'jltma-demo-importer',
        'position' => 70
    ];
    $menu_args = apply_filters( 'jltma_import_demo_submenu_page', $menu_args );
    add_submenu_page(
        $menu_args['parent_slug'],
        $menu_args['page_title'],
        $menu_args['menu_title'],
        $menu_args['capability'],
        $menu_args['menu_slug'],
        [$this, 'jltma_render_demo_importer_page']
    );

  }


  public function jltma_render_demo_importer_page(){
    
    ?>
    <div id="jltma-demo-importer-root"></div>

    <?php
  }


  /**
   * Enqueue scripts for demo importer
   */
  public function enqueue_scripts( $hook ) {
    if ( !str_ends_with( $hook, '_page_jltma-demo-importer' ) ) {
      return ;
    }

    // Enqueue Widget Builder admin script
    $widget_admin_url = JLTMA_URL . '/inc/admin/widget-builder/assets/js/widget-admin.js';
    wp_enqueue_script(
        'jltma-widget-admin',
        $widget_admin_url,
        array('jquery'),
        JLTMA_VER,
        true
    );

    // Enqueue demo importer specific script
    $importer_admin_url = JLTMA_URL . '/assets/js/admin/demo-importer-app.js';
    
    wp_enqueue_script(
        'jltma-demo-importer',
        $importer_admin_url,
        array('wp-element', 'wp-i18n', 'jquery', 'jltma-widget-admin'),
        JLTMA_VER,
        true
    );


    $current_theme = wp_get_theme();
    $theme_name = $current_theme->get( 'Name' );
    $theme_version = $current_theme->get( 'Version' );

    
    $templates_dir = $this->demo_path . '/templates';
    $images_dir = $this->demo_path . '/images';
    $images_url = $this->demo_url . '/images';

    // Localize script for demo importer
    wp_localize_script('jltma-demo-importer', 'jltmaDemoImporter', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('jltma_demo_import'),
        'themeName' => $theme_name,
        'themeLogo' => $this->demo_url . '/' . $this->manifest_data['theme_logo'],
        'themeVersion' => $theme_version,
        'demoPath' => $this->demo_path,
        'demoUrl' => $this->demo_url,
        'templatesDir' => $templates_dir,
        'imagesDir' => $images_dir,
        'imagesUrl' => $images_url,
        'hasVariations' => $this->has_variations,
        'template_nonce' => wp_create_nonce('jltma_template_library_nonce') // Use template library nonce
    ));

    // Localize script for widget admin if not already done
    wp_localize_script('jltma-widget-admin', 'jltmaWidgetAdmin', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'admin_url' => admin_url(),
        'rest_url' => get_rest_url(null, 'jltma/v1'),
        'widget_nonce' => wp_create_nonce('jltma_widget_nonce'),
        'rest_nonce' => wp_create_nonce('wp_rest'),
        'strings' => array(
            'saving' => __('Saving...', 'master-addons'),
            'saved' => __('Widget saved successfully!', 'master-addons'),
            'error' => __('An error occurred. Please try again.', 'master-addons'),
            'confirm_delete' => __('Are you sure you want to delete this widget?', 'master-addons'),
            'copied' => __('Copied!', 'master-addons'),
            'widget_title_required' => __('Widget title is required', 'master-addons'),
            'category_added' => __('Category added successfully!', 'master-addons')
        )
    ));
  }


  /**
   * AJAX handler to import widgets for a template
   */
  public function ajax_import_widgets() {
    // Check nonce
    if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( $_POST['_nonce'], 'jltma_demo_import' ) ) {
        wp_send_json_error( array( 'message' => 'Security check failed', 'debug' => 'Nonce verification failed' ) );
    }

    // Check permissions
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Insufficient permissions', 'debug' => 'User lacks manage_options capability' ) );
    }

    // Get all widget files
    $widgets_dir = $this->demo_path . '/jltma_widget';


    if (!file_exists($widgets_dir)) {
        wp_send_json_error( array(
            'message' => 'Widgets directory not found at: ' . $widgets_dir,
            'debug' => array(
                'theme_dir' => get_stylesheet_directory(),
                'expected_path' => $widgets_dir
            )
        ));
    }

    $widget_files = glob($widgets_dir . '/*.json');

    if (empty($widget_files)) {
        wp_send_json_error( array(
            'message' => 'No widget JSON files found in directory',
            'debug' => 'Directory exists but contains no .json files'
        ));
    }

    $imported_count = 0;
    $skipped_count = 0;
    $errors = array();
    $imported_widgets = array();

    foreach ($widget_files as $widget_file) {
        $filename = basename($widget_file);

        $json_content = file_get_contents($widget_file);
        $widget_data = json_decode($json_content, true);

        if (!$widget_data || !isset($widget_data['widget'])) {
            $errors[] = $filename . ' - Invalid JSON structure';
            continue;
        }

        // Import widget using the existing widget builder functionality
        $widget_to_import = $widget_data['widget'];

        // Check if widget with same title already exists
        $existing_widget = get_page_by_title($widget_to_import['title'], OBJECT, 'jltma_widget');
        if ($existing_widget) {
            $skipped_count++;
            $imported_widgets[] = array(
                'title' => $widget_to_import['title'],
                'status' => 'skipped',
                'message' => 'Already exists'
            );
            continue;
        }

        // Prepare widget data - ensure all required fields are present
        $widget_post_data = array(
            'post_title' => $widget_to_import['title'],
            'post_type' => 'jltma_widget',
            'post_status' => 'publish',
            'post_content' => '', // Empty content since we use meta fields
        );

        // Create widget post
        $widget_id = wp_insert_post($widget_post_data);

        if ($widget_id && !is_wp_error($widget_id)) {
            // Add widget meta data separately for better control
            update_post_meta($widget_id, '_jltma_widget_data', wp_json_encode($widget_to_import));
            update_post_meta($widget_id, '_jltma_widget_category', isset($widget_to_import['category']) ? $widget_to_import['category'] : 'general');
            update_post_meta($widget_id, '_jltma_widget_html', isset($widget_to_import['html_code']) ? $widget_to_import['html_code'] : '');
            update_post_meta($widget_id, '_jltma_widget_css', isset($widget_to_import['css_code']) ? $widget_to_import['css_code'] : '');
            update_post_meta($widget_id, '_jltma_widget_js', isset($widget_to_import['js_code']) ? $widget_to_import['js_code'] : '');

            // Store sections data if available
            if (isset($widget_to_import['sections'])) {
                update_post_meta($widget_id, '_jltma_widget_sections', wp_json_encode($widget_to_import['sections']));
            }

            $imported_count++;
            $imported_widgets[] = array(
                'title' => $widget_to_import['title'],
                'status' => 'imported',
                'id' => $widget_id
            );
        } else {
            $error_message = is_wp_error($widget_id) ? $widget_id->get_error_message() : 'Unknown error';
            $errors[] = $filename . ' - Failed to create: ' . $error_message;
        }
    }

    // Prepare response
    $total_processed = count($widget_files);
    $message = '';

    if ($imported_count > 0) {
        $message = $imported_count . ' widget(s) imported successfully. ';
    }
    if ($skipped_count > 0) {
        $message .= $skipped_count . ' widget(s) skipped (already exist). ';
    }
    if (!empty($errors)) {
        $message .= count($errors) . ' error(s) occurred.';
    }

    if ($imported_count > 0 || $skipped_count > 0) {
        wp_send_json_success( array(
            'message' => trim($message),
            'imported' => $imported_count,
            'skipped' => $skipped_count,
            'total' => $total_processed,
            'errors' => $errors,
            'widgets' => $imported_widgets,
            'debug' => 'Check browser console and PHP error log for details'
        ));
    } else {
        wp_send_json_error( array(
            'message' => 'No widgets imported. ' . (!empty($errors) ? 'Errors: ' . implode(', ', $errors) : ''),
            'errors' => $errors,
            'debug' => 'Total files found: ' . $total_processed
        ));
    }
  }

  /**
   * AJAX handler to import a template from demo folder only
   */
  public function ajax_import_template() {
    // Check nonce - accept both demo import and template library nonces
    $valid_nonce = (isset($_POST['_nonce']) && wp_verify_nonce($_POST['_nonce'], 'jltma_demo_import')) ||
                   (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'jltma_template_library_nonce')) ||
                   (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'jltma_template_kits_nonce_action'));

    if (!$valid_nonce) {
        wp_send_json_error(array('message' => 'Security check failed'));
        return;
    }

    // Check permissions
    if (!current_user_can('import')) {
        wp_send_json_error(array('message' => 'Insufficient permissions'));
        return;
    }

    // Get parameters
    $template_id = isset($_POST['template_id']) ? sanitize_text_field($_POST['template_id']) : '';
    $page_name = isset($_POST['page_name']) ? sanitize_text_field($_POST['page_name']) : null;
    $is_reimport = isset($_POST['is_reimport']) && $_POST['is_reimport'] === '1';

    if (empty($template_id)) {
        wp_send_json_error(array('message' => 'Template ID is required'));
        return;
    }

    // Import from local demo folder only
    $result = $this->import_demo_template($template_id, $page_name, $is_reimport);

    if ($result && !is_wp_error($result)) {
        wp_send_json_success(array(
            'message' => $result['message'] ?? 'Template imported successfully',
            'page_id' => $result['page_id'] ?? 0,
            'edit_url' => $result['edit_url'] ?? '',
            'view_url' => $result['view_url'] ?? ''
        ));
    } else {
        $error_message = is_wp_error($result) ? $result->get_error_message() : 'Failed to import template';
        wp_send_json_error(array('message' => $error_message));
    }
  }

  /**
   * Import demo template from local files
   */
  private function import_demo_template($template_id, $page_name = null, $is_reimport = false) {
    // First, import required dependencies (only once)
    $this->import_demo_dependencies();

    // Get template file from local demo directory
    $pages_dir = $this->demo_path . '/pages';
    $template_files = glob($pages_dir . '/*.json');

    $template_data = null;
    foreach ($template_files as $template_file) {
        $json_content = file_get_contents($template_file);
        $decoded = json_decode($json_content, true);

        if ($decoded && isset($decoded['template_id']) && $decoded['template_id'] == $template_id) {
            $template_data = $decoded;
            break;
        }
    }

    if (!$template_data) {
        return new \WP_Error('template_not_found', 'Demo template not found');
    }

    // Ensure Elementor is loaded
    if (!did_action('elementor/loaded')) {
        return new \WP_Error('elementor_not_loaded', 'Elementor is not loaded');
    }

    // Prepare the page title and slug
    $page_title = $page_name ? $page_name : (isset($template_data['title']) ? $template_data['title'] : 'Demo Page ' . $template_id);
    $page_slug = isset($template_data['slug']) ? $template_data['slug'] : sanitize_title($page_title);
    $template_type = isset($template_data['type']) ? $template_data['type'] : 'page';

    // Handle re-import by adding suffix
    if ($is_reimport) {
        $page_title = $this->get_unique_page_title($page_title);
        $page_slug = $page_slug . '-' . time();
    }

    // Prepare the page data
    $page_data = array(
        'post_title' => $page_title,
        'post_name' => $page_slug,
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_content' => '', // Elementor will handle the content
        'meta_input' => array(
            '_elementor_edit_mode' => 'builder',
            '_elementor_template_type' => $template_type,
            '_wp_page_template' => 'elementor_header_footer',
            '_jltma_demo_import_item' => true,
            '_jltma_import_date' => current_time('mysql'),
            '_jltma_original_template_id' => $template_id
        )
    );

    // Create the page
    $page_id = wp_insert_post($page_data);

    if (is_wp_error($page_id)) {
        return $page_id;
    }

    // Store the page mapping for menu imports
    $this->store_page_mapping($page_slug, $page_id);

    // Import Elementor data if available
    if (isset($template_data['content']) && !empty($template_data['content'])) {
        // Process the content for image imports if needed
        $processed_content = $this->process_demo_content($template_data['content']);

        // Save Elementor data
        update_post_meta($page_id, '_elementor_data', wp_json_encode($processed_content));
        update_post_meta($page_id, '_elementor_version', ELEMENTOR_VERSION);

        // Clear Elementor cache
        if (class_exists('\Elementor\Plugin')) {
            \Elementor\Plugin::$instance->files_manager->clear_cache();
        }
    }

    // Mark template as imported (optional)
    $current_theme = wp_get_theme();
    $theme_name = $current_theme->get('Name');
    $theme_version = $current_theme->get('Version');
    $template_signature = $theme_name . '_' . $theme_version . '_' . $template_id;
    update_option($template_signature, true);

    // Return success with page details
    return array(
        'page_id' => $page_id,
        'edit_url' => admin_url('post.php?post=' . $page_id . '&action=elementor'),
        'view_url' => get_permalink($page_id),
        'message' => 'Demo template imported successfully'
    );
  }

  /**
   * Get unique page title by adding suffix if needed
   */
  private function get_unique_page_title($base_title) {
    // Find existing pages with this title
    $existing_pages = get_posts(array(
        'post_type' => 'page',
        'post_status' => 'any',
        's' => $base_title,
        'numberposts' => -1
    ));

    // Calculate suffix number
    $suffix = 2;

    // Check for existing numbered versions
    foreach ($existing_pages as $page) {
        if (preg_match('/(.+)\s+(\d+)$/', $page->post_title, $matches)) {
            if ($matches[1] === $base_title) {
                $num = intval($matches[2]);
                if ($num >= $suffix) {
                    $suffix = $num + 1;
                }
            }
        }
    }

    // Check if base title exists without number
    $base_exists = false;
    foreach ($existing_pages as $page) {
        if ($page->post_title === $base_title) {
            $base_exists = true;
            break;
        }
    }

    // Return title with suffix if base exists
    return $base_exists ? $base_title . ' ' . $suffix : $base_title;
  }

  /**
   * Process demo content for imports (handle images, etc.)
   */
  private function process_demo_content($content) {
    // If content is a string, decode it
    if (is_string($content)) {
        $content = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $content;
        }
    }

    // If content is not an array, return as-is
    if (!is_array($content)) {
        return $content;
    }

    // Process content recursively to find and replace image URLs
    $content = $this->process_content_images($content);

    return $content;
  }

  /**
   * Process content recursively to find and replace image URLs with imported attachments
   */
  private function process_content_images($content) {
    if (!is_array($content)) {
        return $content;
    }

    foreach ($content as $key => &$value) {
        // Check if this is an image setting
        if ($key === 'image' && is_array($value) && isset($value['url'])) {
            // Process the image URL
            $value = $this->process_image_field($value);
        }
        // Check for background images
        elseif (($key === 'background_image' || $key === '_background_image' ||
                 $key === 'background_overlay_image' || $key === '_background_overlay_image') &&
                 is_array($value) && isset($value['url'])) {
            // Process background image URL
            $value = $this->process_image_field($value);
        }
        // Recursively process arrays
        elseif (is_array($value)) {
            $value = $this->process_content_images($value);
        }
        // Process image URLs in strings (for inline images in content)
        elseif (is_string($value) && (strpos($value, '.jpg') !== false ||
                strpos($value, '.jpeg') !== false ||
                strpos($value, '.png') !== false ||
                strpos($value, '.gif') !== false ||
                strpos($value, '.svg') !== false)) {
            $value = $this->process_string_with_images($value);
        }
    }

    return $content;
  }

  /**
   * Process an image field and replace with imported attachment
   */
  private function process_image_field($image_data) {
    if (!isset($image_data['url'])) {
        return $image_data;
    }

    $original_url = $image_data['url'];

    // Check if this is just a filename (not a full URL)
    if (!filter_var($original_url, FILTER_VALIDATE_URL)) {
        // This is a filename from the demo folder
        $attachment_id = $this->import_demo_image($original_url);

        if ($attachment_id && !is_wp_error($attachment_id)) {
            // Get the new attachment URL and metadata
            $attachment_url = wp_get_attachment_url($attachment_id);
            $attachment_metadata = wp_get_attachment_metadata($attachment_id);

            // Update the image data
            $image_data['url'] = $attachment_url;
            $image_data['id'] = $attachment_id;

            // Update source to library since it's now in Media Library
            if (isset($image_data['source'])) {
                $image_data['source'] = 'library';
            }

            // Add width and height if available
            if ($attachment_metadata) {
                if (isset($attachment_metadata['width'])) {
                    $image_data['width'] = $attachment_metadata['width'];
                }
                if (isset($attachment_metadata['height'])) {
                    $image_data['height'] = $attachment_metadata['height'];
                }
            }
        }
    }

    return $image_data;
  }

  /**
   * Process strings that might contain image references
   */
  private function process_string_with_images($content) {
    // Pattern to match image filenames (including complex names with multiple hyphens, underscores, etc.)
    $pattern = '/([a-zA-Z0-9_\-\.]+\.(?:jpg|jpeg|png|gif|svg))/i';

    return preg_replace_callback($pattern, function($matches) {
        $filename = $matches[1];

        // Try to import this image
        $attachment_id = $this->import_demo_image($filename);

        if ($attachment_id && !is_wp_error($attachment_id)) {
            // Get the new URL
            $attachment_url = wp_get_attachment_url($attachment_id);
            return $attachment_url;
        }

        // Return original if import failed
        return $filename;
    }, $content);
  }

  /**
   * Import a demo image and create WordPress attachment
   */
  private function import_demo_image($filename) {
    // Check if we've already imported this image
    $cache_key = 'jltma_imported_image_' . md5($filename);
    $cached_id = get_transient($cache_key);

    if ($cached_id) {
        // Verify the attachment still exists
        if (get_post($cached_id)) {
            return $cached_id;
        }
    }

    // Build the full path to the image
    $images_dir = $this->demo_path . '/images';
    $image_path = $images_dir . '/' . $filename;

    // Check if the file exists
    if (!file_exists($image_path)) {
        return false;
    }

    // Check file type
    $filetype = wp_check_filetype($filename, null);
    if (!$filetype['type']) {
        return false;
    }

    // Prepare upload directory
    $upload_dir = wp_upload_dir();
    $upload_path = $upload_dir['path'] . '/' . $filename;

    // Check if file already exists in uploads
    if (file_exists($upload_path)) {
        // Try to find existing attachment
        global $wpdb;
        $attachment_id = $wpdb->get_var($wpdb->prepare(
            "SELECT ID FROM $wpdb->posts WHERE guid LIKE %s AND post_type = 'attachment'",
            '%/' . $filename
        ));

        if ($attachment_id) {
            // Cache the result
            set_transient($cache_key, $attachment_id, DAY_IN_SECONDS);
            return $attachment_id;
        }
    }

    // Copy file to uploads directory
    if (!copy($image_path, $upload_path)) {
        return false;
    }

    // Create attachment
    $attachment = array(
        'guid' => $upload_dir['url'] . '/' . $filename,
        'post_mime_type' => $filetype['type'],
        'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
        'post_content' => '',
        'post_status' => 'inherit'
    );

    // Insert the attachment
    $attachment_id = wp_insert_attachment($attachment, $upload_path);

    if (!is_wp_error($attachment_id)) {
        // Generate attachment metadata
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attachment_metadata = wp_generate_attachment_metadata($attachment_id, $upload_path);
        wp_update_attachment_metadata($attachment_id, $attachment_metadata);

        // Mark as imported from demo
        update_post_meta($attachment_id, '_jltma_demo_import_image', true);
        update_post_meta($attachment_id, '_jltma_original_filename', $filename);

        // Cache the result for 1 day
        set_transient($cache_key, $attachment_id, DAY_IN_SECONDS);

        return $attachment_id;
    }

    return false;
  }


  /**
   * AJAX handler to get templates list
   */
  public function ajax_get_templates() {
    // Check nonce
    if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( $_POST['_nonce'], 'jltma_demo_import' ) ) {
        wp_send_json_error( array( 'message' => 'Security check failed' ) );
    }

    // Check permissions
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Insufficient permissions' ) );
    }
    
    $pages = $this->demo_path . '/pages';
    $images_dir = $this->demo_path . '/images';

    if (!file_exists($pages)) {
        wp_send_json_error( array(
            'message' => 'Templates directory not found',
            'path' => $pages
        ));
        return;
    }

    // Get all JSON template files
    $template_files = glob($pages . '/*.json');
    $templates = array();
    $current_theme = wp_get_theme();
    $theme_name = $current_theme->get( 'Name' );
    $theme_version = $current_theme->get( 'Version' );


    foreach ($template_files as $template_file) {
        // Read and parse the JSON file
        $json_content = file_get_contents($template_file);
        $template_data = json_decode($json_content, true);
        // Skip if JSON decode failed
        if (!$template_data) {
          continue;
        }
        
        // Skip global.json file if it exists
        if (isset($template_data['title']) && $template_data['title'] === 'global') {
          continue;
        }

        // Check if template is already imported
        $template_id = isset($template_data['template_id']) ? $template_data['template_id'] : '';
        $template_signature = $theme_name . '_' . $theme_version . '_' . $template_id;
        $imported = get_option( $template_signature, false );
        $template_data['imported'] = $imported;

        // Add to templates array
        $templates[] = $template_data;
    }

    wp_send_json_success( $templates );
  }


  /**
   * AJAX handler to get demo variations
   */
  public function ajax_get_variations() {
    // Check nonce
    if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( $_POST['_nonce'], 'jltma_demo_import' ) ) {
        wp_send_json_error( array( 'message' => 'Security check failed' ) );
    }

    // Check permissions
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Insufficient permissions' ) );
    }

    $manifest_data = $this->manifest_data;
    if (empty($manifest_data) || empty($manifest_data['demos'])) {
        wp_send_json_error( array(
            'message' => 'No demo variations found',
            'legacy' => true
        ));
        return;
    }

    // Get selected category from POST (case-insensitive)
    $selected_category = 'all';
    if ( isset( $_POST['category'] ) && ! empty( $_POST['category'] ) ) {
        $selected_category = strtolower( sanitize_text_field( $_POST['category'] ) );
    }

    // Build categories list for tabs
    $categories = array_unique(array_column($manifest_data['demos'], 'category'));
    $tabs = array(
        array('label' => 'All')
    );

    foreach ($categories as $cat) {
        $tabs[] = array('label' => $cat);
    }

    // Process demos and format them as variations
    $variations = array();
    $demo_directory = $manifest_data['demo_directory'];
    $demo_url = str_replace(ABSPATH, site_url('/'), $demo_directory);

    foreach ($manifest_data['demos'] as $demo) {
        // Filter by category (case-insensitive comparison)
        if ( $selected_category !== 'all' ) {
            $demo_category = isset( $demo['category'] ) ? strtolower( $demo['category'] ) : '';
            if ( $demo_category !== $selected_category ) {
                continue; // Skip this demo if it doesn't match the selected category
            }
        }

        $variation = array(
            'name' => $demo['name'],
            'folder' => $demo['import_folder'],
            'imported' => get_option('jltma_variation_imported_' . $demo['import_folder'], false)
        );
        // Handle thumbnail
        if (!empty($demo['thumbnail'])) {
            $variation['thumbnail'] = $this->demo_url . '/'. $demo['import_folder'] . '/'. $demo['thumbnail'];
        } else {
            $variation['thumbnail'] = 'https://placehold.co/600x400?text=' . urlencode($demo['name']);
        }

        // Add summary counts for the variation
        $variation['summary'] = $this->get_variation_summary($demo_directory . '/' . $demo['import_folder']);

        // Store menu assignments and homepage settings for later use
        if (!empty($demo['menu_assignments'])) {
            $variation['menu_assignments'] = $demo['menu_assignments'];
        }
        if (!empty($demo['homepage_settings'])) {
            $variation['homepage_settings'] = $demo['homepage_settings'];
        }

        $variations[] = $variation;
    }

    wp_send_json_success(array(
        'theme' => get_stylesheet(),
        'theme_name' => wp_get_theme()->get('Name'),
        'variations' => $variations,
        'categories' => $tabs
    ));
  }

  /**
   * Get summary counts for a variation
   */
  private function get_variation_summary($variation_path) {
      $summary = array(
          'page' => 0,
          'jltma_widget' => 0,
          'elementor_library' => 0,
          'menus' => 0
      );

      // Count pages
      $pages_dir = $variation_path . '/pages';
      if (is_dir($pages_dir)) {
          $pages = glob($pages_dir . '/*.json');
          $summary['page'] = count($pages);
      }

      // Count widgets
      $widgets_dir = $variation_path . '/jltma_widget';
      if (is_dir($widgets_dir)) {
          $widgets = glob($widgets_dir . '/*.json');
          $summary['jltma_widget'] = count($widgets);
      }

      // Count elementor library templates
      $library_dir = $variation_path . '/elementor_library';
      if (is_dir($library_dir)) {
          $templates = glob($library_dir . '/*.json');
          $summary['elementor_library'] = count($templates);
      }

      // Count menus (menus are in the demo root/menus folder)
      $menus_file = $this->demo_path . '/menus/menus.json';
      if (file_exists($menus_file)) {
          $menus_data = json_decode(file_get_contents($menus_file), true);
          if (is_array($menus_data)) {
              $summary['menus'] = count($menus_data);
          }
      }

      return $summary;
  }

  /**
   * AJAX handler to import a full variation
   */
  public function ajax_import_full_variation() {
    // Check nonce
    if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( $_POST['_nonce'], 'jltma_demo_import' ) ) {
        wp_send_json_error( array( 'message' => 'Security check failed' ) );
    }

    // Check permissions
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Insufficient permissions' ) );
    }

    $variation_folder = isset($_POST['variation']) ? sanitize_text_field($_POST['variation']) : '';

    if (empty($variation_folder)) {
        wp_send_json_error( array( 'message' => 'Variation folder is required' ) );
        return;
    }

    // Get the manifest data to find variation settings
    $manifest_data = apply_filters( 'jltma_demo_importer_variations', array() );
    $current_demo = null;

    if (!empty($manifest_data['demos'])) {
        foreach ($manifest_data['demos'] as $demo) {
            if ($demo['import_folder'] === $variation_folder) {
                $current_demo = $demo;
                break;
            }
        }
    }

    // Set the current variation path
    $this->current_variation = $variation_folder;
    $variation_path = $this->demo_path . '/' . $variation_folder;

    if (!file_exists($variation_path)) {
        wp_send_json_error( array(
            'message' => 'Variation folder not found: ' . $variation_folder
        ));
        return;
    }

    // Start the import process
    $import_result = array(
        'widgets' => 0,
        'templates' => 0,
        'library_templates' => 0,
        'menus' => 0,
        'settings' => false,
        'errors' => array()
    );

    // Temporarily update demo path to the variation path
    $original_path = $this->demo_path;
    $original_url = $this->demo_url;
    $this->demo_path = $variation_path;
    $this->demo_url = $this->demo_url . '/' . $variation_folder;

    // Clear page mappings for new import
    $this->clear_page_mappings();

    // Import dependencies (Elementor templates, settings - but NOT menus yet)
    $this->import_demo_dependencies();

    // Import widgets
    $widgets_result = $this->import_variation_widgets();
    $import_result['widgets'] = $widgets_result['imported'] ?? 0;

    // Import all templates/pages FIRST
    $templates_result = $this->import_all_variation_templates();
    $import_result['templates'] = $templates_result['imported'] ?? 0;

    // NOW import menus after pages have been imported
    // Restore original path temporarily for menu import (menus are in demo root)
    $temp_path = $this->demo_path;
    $this->demo_path = $original_path;
    $created_menus = $this->import_demo_menus();
    $this->demo_path = $temp_path;

    // Assign menus to locations if specified in manifest
    if ($current_demo && !empty($current_demo['menu_assignments']) && $created_menus) {
        $this->assign_menus_to_locations($current_demo['menu_assignments'], $created_menus);
    }

    // Set homepage and posts page if specified (using slugs from manifest)
    if ($current_demo) {
        $homepage_settings = array();
        if (!empty($current_demo['page_on_front'])) {
            $homepage_settings['page_on_front'] = $current_demo['page_on_front'];
        }
        if (!empty($current_demo['page_for_posts'])) {
            $homepage_settings['page_for_posts'] = $current_demo['page_for_posts'];
        }
        if (!empty($homepage_settings)) {
            $this->set_homepage_settings($homepage_settings);
        }
    }

    // Count imported library templates
    $library_dir = $variation_path . '/elementor_library';
    if (file_exists($library_dir)) {
        $library_files = glob($library_dir . '/*.json');
        $import_result['library_templates'] = count($library_files) - 1; 
    }

    // Count imported menus (menus are in demo root/menus folder)
    $menus_file = $original_path . '/menus/menus.json';
    if (file_exists($menus_file)) {
        $menus_content = file_get_contents($menus_file);
        $menus_data = json_decode($menus_content, true);
        if ($menus_data) {
            $import_result['menus'] = count($menus_data);
        }
    }

    // Settings folder no longer exists
    $import_result['settings'] = false;

    // Restore original paths
    $this->demo_path = $original_path;
    $this->demo_url = $original_url;

    // Mark variation as imported
    update_option('jltma_variation_imported_' . $variation_folder, true);

    // Clear the dependencies cache to allow re-import of other variations
    delete_transient('jltma_demo_dependencies_imported');

    wp_send_json_success( array(
        'message' => 'Variation imported successfully',
        'variation' => $variation_folder,
        'result' => $import_result
    ));
  }

  /**
   * Import all widgets for a variation
   */
  private function import_variation_widgets() {
    $widgets_dir = $this->demo_path . '/jltma_widget';

    if (!file_exists($widgets_dir)) {
        return array('imported' => 0, 'skipped' => 0);
    }

    $widget_files = glob($widgets_dir . '/*.json');
    $imported_count = 0;
    $skipped_count = 0;

    foreach ($widget_files as $widget_file) {
        $json_content = file_get_contents($widget_file);
        $widget_data = json_decode($json_content, true);

        if (!$widget_data || !isset($widget_data['widget'])) {
            continue;
        }

        $widget_to_import = $widget_data['widget'];

        // Check if widget already exists
        $existing_widget = get_page_by_title($widget_to_import['title'], OBJECT, 'jltma_widget');
        if ($existing_widget) {
            $skipped_count++;
            continue;
        }

        // Create widget post
        $widget_post_data = array(
            'post_title' => $widget_to_import['title'],
            'post_type' => 'jltma_widget',
            'post_status' => 'publish',
            'post_content' => '',
        );

        $widget_id = wp_insert_post($widget_post_data);

        if ($widget_id && !is_wp_error($widget_id)) {
            // Add widget meta data
            update_post_meta($widget_id, '_jltma_widget_data', wp_json_encode($widget_to_import));
            update_post_meta($widget_id, '_jltma_widget_category', $widget_to_import['category'] ?? 'general');
            update_post_meta($widget_id, '_jltma_widget_html', $widget_to_import['html_code'] ?? '');
            update_post_meta($widget_id, '_jltma_widget_css', $widget_to_import['css_code'] ?? '');
            update_post_meta($widget_id, '_jltma_widget_js', $widget_to_import['js_code'] ?? '');

            if (isset($widget_to_import['sections'])) {
                update_post_meta($widget_id, '_jltma_widget_sections', wp_json_encode($widget_to_import['sections']));
            }

            $imported_count++;
        }
    }

    return array('imported' => $imported_count, 'skipped' => $skipped_count);
  }

  /**
   * Import all templates for a variation
   */
  private function import_all_variation_templates() {
    $pages_dir = $this->demo_path . '/pages';

    if (!file_exists($pages_dir)) {
        return array('imported' => 0, 'errors' => array());
    }

    $template_files = glob($pages_dir . '/*.json');
    $imported_count = 0;
    $errors = array();

    foreach ($template_files as $template_file) {
        $json_content = file_get_contents($template_file);
        $template_data = json_decode($json_content, true);

        if (!$template_data) {
            continue;
        }

        // Skip global.json if exists
        if (isset($template_data['title']) && $template_data['title'] === 'global') {
            continue;
        }

        $template_id = $template_data['template_id'] ?? basename($template_file, '.json');

        // Import the template
        $result = $this->import_demo_template($template_id, null, false);

        if ($result && !is_wp_error($result)) {
            $imported_count++;

            // Store page mapping if slug is available
            if (isset($template_data['slug']) && isset($result['page_id'])) {
                $this->store_page_mapping($template_data['slug'], $result['page_id']);
            }
        } else {
            $error_message = is_wp_error($result) ? $result->get_error_message() : 'Unknown error';
            $errors[] = $template_id . ': ' . $error_message;
        }
    }

    return array('imported' => $imported_count, 'errors' => $errors);
  }

  public function ajax_mark_imported_template(){
    if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( $_POST['_nonce'], 'jltma_demo_import' ) ) {
        wp_send_json_error( array( 'message' => 'Security check failed', 'debug' => 'Nonce verification failed' ) );
    }

    // Check permissions
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Insufficient permissions', 'debug' => 'User lacks manage_options capability' ) );
    }

    $template_id = isset($_POST['template_id']) ? sanitize_text_field($_POST['template_id']) : '';
    $theme_name = isset($_POST['theme_name']) ? sanitize_text_field($_POST['theme_name']) : '';
    $theme_version = isset($_POST['theme_version']) ? sanitize_text_field($_POST['theme_version']) : '';

    if (empty($template_id)) {
        wp_send_json_error( array( 'message' => 'Template ID is required' ) );
        return;
    }

    $template_signature = $theme_name . '_' . $theme_version . '_' . $template_id;
    update_option($template_signature, true);

    wp_send_json_success( array(
        'message' => 'Template marked as imported',
        'template_id' => $template_id
    ));
  }

  /**
   * Store page mapping for menu imports
   */
  private function store_page_mapping($slug, $page_id) {
    $mappings = get_transient('jltma_import_page_mappings') ?: array();
    $mappings[$slug] = $page_id;
    set_transient('jltma_import_page_mappings', $mappings, HOUR_IN_SECONDS);
  }

  /**
   * Get page mapping
   */
  private function get_page_mapping($slug) {
    $mappings = get_transient('jltma_import_page_mappings') ?: array();
    return isset($mappings[$slug]) ? $mappings[$slug] : null;
  }

  /**
   * Clear page mappings
   */
  private function clear_page_mappings() {
    delete_transient('jltma_import_page_mappings');
  }

  /**
   * Assign imported menus to their specified locations
   */
  private function assign_menus_to_locations($menu_assignments, $created_menus = null) {
    if (empty($menu_assignments) || !is_array($menu_assignments)) {
        return;
    }

    $locations = get_theme_mod('nav_menu_locations', array());

    foreach ($menu_assignments as $location => $menu_name) {
        // Skip empty assignments
        if (empty($menu_name)) {
            continue;
        }

        // If we have created menus map, use it first
        if ($created_menus && isset($created_menus[$menu_name])) {
            $locations[$location] = $created_menus[$menu_name];
        } else {
            // Fallback: Find the menu by name from all menus
            $menu = get_term_by('name', $menu_name, 'nav_menu');
            if ($menu) {
                $locations[$location] = $menu->term_id;
            }
        }
    }

    set_theme_mod('nav_menu_locations', $locations);
  }

  /**
   * Set homepage and posts page settings using page slugs
   */
  private function set_homepage_settings($homepage_settings) {
    if (empty($homepage_settings) || !is_array($homepage_settings)) {
        return;
    }

    // Get page mappings to find imported pages
    $page_mappings = get_transient('jltma_import_page_mappings');
    if (!$page_mappings) {
        $page_mappings = array();
    }

    // Set the homepage using slug
    if (!empty($homepage_settings['page_on_front'])) {
        $home_page_slug = $homepage_settings['page_on_front'];

        // Find the imported page with this slug
        $home_page = get_page_by_path($home_page_slug);

        // If not found by slug, try mappings (in case slug was modified)
        if (!$home_page && !empty($page_mappings)) {
            foreach ($page_mappings as $original_slug => $page_id) {
                if ($original_slug === $home_page_slug) {
                    $home_page = get_post($page_id);
                    break;
                }
            }
        }

        if ($home_page && $home_page->post_type === 'page') {
            update_option('page_on_front', $home_page->ID);
            update_option('show_on_front', 'page');
        }
    }

    // Set the posts page (blog page) using slug
    if (!empty($homepage_settings['page_for_posts'])) {
        $posts_page_slug = $homepage_settings['page_for_posts'];

        // Find the imported page with this slug
        $posts_page = get_page_by_path($posts_page_slug);

        // If not found by slug, try mappings (in case slug was modified)
        if (!$posts_page && !empty($page_mappings)) {
            foreach ($page_mappings as $original_slug => $page_id) {
                if ($original_slug === $posts_page_slug) {
                    $posts_page = get_post($page_id);
                    break;
                }
            }
        }

        if ($posts_page && $posts_page->post_type === 'page') {
            update_option('page_for_posts', $posts_page->ID);
            update_option('show_on_front', 'page');
        }
    }
  }

  /**
   * Import demo dependencies (Elementor templates, menus, settings)
   * This runs only once per session to avoid duplicates
   */
  private function import_demo_dependencies() {
    // Check if dependencies already imported in this session
    $dependencies_imported = get_transient('jltma_demo_dependencies_imported');
    if ($dependencies_imported) {
        return true;
    }

    // Clear any previous page mappings
    $this->clear_page_mappings();

    // Pre-import commonly used images (optional optimization)
    $this->preload_demo_images();

    // Import Elementor library templates
    $this->import_elementor_library_templates();

    // Import and set global kit styles
    $this->import_and_set_global_kit();

    // Import menus - moved to after pages are imported
    // $this->import_demo_menus();

    // Settings folder no longer exists, skip settings import

    // Mark dependencies as imported for 1 hour
    set_transient('jltma_demo_dependencies_imported', true, HOUR_IN_SECONDS);

    return true;
  }

  /**
   * Preload common demo images for better performance
   */
  private function preload_demo_images() {
    $images_dir = $this->demo_path . '/images';

    if (!file_exists($images_dir)) {
        return false;
    }

    // Get a few common image types to preload
    $common_extensions = array('jpg', 'jpeg', 'png', 'gif', 'svg');
    $preloaded_count = 0;
    $max_preload = 10; // Limit to prevent timeout

    foreach ($common_extensions as $ext) {
        $pattern = $images_dir . '/*.' . $ext;
        $files = glob($pattern);

        if ($files) {
            foreach ($files as $file_path) {
                if ($preloaded_count >= $max_preload) {
                    break 2;
                }

                $filename = basename($file_path);
                // This will cache the image ID for faster access later
                $this->import_demo_image($filename);
                $preloaded_count++;
            }
        }
    }

    return $preloaded_count;
  }

  /**
   * Import Elementor library templates (header, footer, etc.)
   */
  private function import_elementor_library_templates() {
    $library_dir = $this->demo_path . '/elementor_library';

    if (!file_exists($library_dir)) {
        return false;
    }

    $template_files = glob($library_dir . '/*.json');
    $imported_count = 0;

    foreach ($template_files as $template_file) {
        $filename = basename($template_file, '.json');

        $json_content = file_get_contents($template_file);
        $template_data = json_decode($json_content, true);

        if (!$template_data) {
            continue;
        }

        // Skip kit files as they're handled separately
        if (isset($template_data['metadata']['template_type']) &&
            $template_data['metadata']['template_type'] === 'kit') {
            continue;
        }

        // Check if template already exists
        $existing_template = get_posts(array(
            'post_type' => 'elementor_library',
            'title' => $template_data['title'] ?? $filename,
            'post_status' => 'publish',
            'numberposts' => 1
        ));

        if (!empty($existing_template)) {
            continue; // Skip if already exists
        }

        // Determine template type
        $template_type = 'section'; // default
        if (stripos($filename, 'header') !== false) {
            $template_type = 'header';
        } elseif (stripos($filename, 'footer') !== false) {
            $template_type = 'footer';
        } elseif (stripos($filename, 'post') !== false || stripos($filename, 'single') !== false) {
            $template_type = 'single';
        } elseif (stripos($filename, 'archive') !== false) {
            $template_type = 'archive';
        } elseif (stripos($filename, 'menu') !== false || stripos($filename, 'nav') !== false) {
            $template_type = 'section';
        }

        // Process content for images before saving
        $processed_content = $this->process_demo_content($template_data['content'] ?? []);

        // Create the Elementor library post
        $post_data = array(
            'post_title' => $template_data['title'] ?? ucfirst($filename),
            'post_type' => 'elementor_library',
            'post_status' => 'publish',
            'post_content' => '',
            'meta_input' => array(
                '_elementor_edit_mode' => 'builder',
                '_elementor_template_type' => $template_type,
                '_elementor_data' => wp_json_encode($processed_content),
                '_jltma_demo_import_item' => true,
                '_jltma_import_source' => 'demo_library'
            )
        );

        $post_id = wp_insert_post($post_data);

        if ($post_id && !is_wp_error($post_id)) {
            $imported_count++;

            // Store the imported template ID for reference
            update_option('jltma_imported_' . $filename . '_id', $post_id);
        }
    }

    return $imported_count;
  }

  /**
   * Import and set global kit styles
   */
  private function import_and_set_global_kit() {
    $library_dir = $this->demo_path . '/elementor_library';

    if (!file_exists($library_dir)) {
        return false;
    }

    // Check if kit already imported
    $kit_imported = get_option('jltma_demo_kit_imported');
    if ($kit_imported) {
        return true;
    }

    // Look for any file with kit metadata
    $library_files = glob($library_dir . '/*.json');
    $kit_file = null;
    $kit_data = null;

    foreach ($library_files as $file) {
        $json_content = file_get_contents($file);
        $template_data = json_decode($json_content, true);

        // Check if this is a kit by looking at metadata
        if ($template_data && isset($template_data['metadata']['template_type']) &&
            $template_data['metadata']['template_type'] === 'kit') {
            $kit_file = $file;
            $kit_data = $template_data;
            break;
        }
    }

    if (!$kit_file || !$kit_data) {
        return false;
    }

    // Create the kit post
    $kit_post = array(
        'post_title' => $kit_data['title'] ?? 'Global Kit Styles',
        'post_type' => 'elementor_library',
        'post_status' => 'publish',
        'meta_input' => array(
            '_elementor_edit_mode' => 'builder',
            '_elementor_template_type' => 'kit',
            '_elementor_data' => wp_json_encode($kit_data['content'] ?? [])
        )
    );

    $kit_id = wp_insert_post($kit_post);

    if ($kit_id && !is_wp_error($kit_id)) {
        // Set this kit as the active kit in Elementor
        update_option('elementor_active_kit', $kit_id);
        update_option('jltma_demo_kit_imported', $kit_id);

        return true;
    }

    return false;
  }

  /**
   * Import demo menus - only import menus used in current variation
   */
  private function import_demo_menus() {
    // Get the current demo configuration to know which menus to import
    $manifest_data = apply_filters('jltma_demo_importer_variations', array());
    $current_demo = null;

    if (!empty($manifest_data['demos']) && $this->current_variation) {
        foreach ($manifest_data['demos'] as $demo) {
            if ($demo['import_folder'] === $this->current_variation) {
                $current_demo = $demo;
                break;
            }
        }
    }

    // If no current demo or no menu assignments, skip
    if (!$current_demo || empty($current_demo['menu_assignments'])) {
        return false;
    }

    // Menus are in demo root/menus folder
    $menus_file = $this->demo_path . '/menus/menus.json';

    if (!file_exists($menus_file)) {
        return false;
    }

    // Check if menus already imported (use variation-specific key)
    $import_key = 'jltma_demo_menus_imported_' . $this->current_variation;
    $menus_imported = get_option($import_key);
    if ($menus_imported) {
        return true;
    }

    $json_content = file_get_contents($menus_file);
    $menus_data = json_decode($json_content, true);

    if (!$menus_data) {
        return false;
    }

    // Get list of menu names that need to be imported for this variation
    $menus_to_import = array_filter(array_unique(array_values($current_demo['menu_assignments'])));
    if (empty($menus_to_import)) {
        return false;
    }

    $created_menus = array();

    // Only import menus that are assigned in this variation
    foreach ($menus_data as $menu_data) {
        // New format (object with name, slug, items)
        if (is_array($menu_data) && isset($menu_data['name'])) {
            $menu_name = $menu_data['name'];

            // Skip this menu if it's not used in current variation
            if (!in_array($menu_name, $menus_to_import)) {
                continue;
            }

            $menu_slug = $menu_data['slug'] ?? sanitize_title($menu_name);
            $menu_items = $menu_data['items'] ?? array();

            // Check if menu already exists by name
            $menu_exists = get_term_by('name', $menu_name, 'nav_menu');

            if (!$menu_exists) {
                // Create the menu
                $menu_id = wp_create_nav_menu($menu_name);

                if (!is_wp_error($menu_id)) {
                    // Add menu items
                    $this->add_menu_items($menu_id, $menu_items);
                    $created_menus[$menu_name] = $menu_id; // Store by name for assignment
                }
            } else {
                $created_menus[$menu_name] = $menu_exists->term_id;
            }
        }
    }

    update_option($import_key, true);
    return $created_menus; // Return created menus for assignment
  }

  /**
   * Helper function to add menu items recursively
   */
  private function add_menu_items($menu_id, $items, $parent_id = 0) {
    if (!is_array($items) || empty($items)) {
        return;
    }

    // Build a map of old IDs to new IDs for parent-child relationships
    $id_map = array();

    foreach ($items as $item) {
        $menu_item_data = array(
            'menu-item-title' => $item['title'] ?? $item['post_title'] ?? '',
            'menu-item-url' => $item['url'] ?? '#',
            'menu-item-status' => 'publish',
            'menu-item-parent-id' => isset($item['menu_item_parent']) && $item['menu_item_parent'] != '0' ?
                                    (isset($id_map[$item['menu_item_parent']]) ? $id_map[$item['menu_item_parent']] : 0) :
                                    0,
            'menu-item-type' => $item['type'] ?? 'custom',
        );

        // Handle different menu item types
        if ($item['type'] === 'post_type') {
            $object_type = $item['object'] ?? 'page';
            $post = null;

            // First try to find by imported page mapping
            if (isset($item['slug'])) {
                $mapped_id = $this->get_page_mapping($item['slug']);
                if ($mapped_id) {
                    $post = get_post($mapped_id);
                }
            }

            // If not found in mapping, try to find by slug
            if (!$post && isset($item['slug'])) {
                $post = get_page_by_path($item['slug'], OBJECT, $object_type);
            }

            // If still not found, try to find by title as fallback
            if (!$post) {
                $post_title = $item['title'] ?? $item['post_title'] ?? '';
                // Remove any prefix like "Flowerry – " from the title
                $post_title = preg_replace('/^[^–]+–\s*/', '', $post_title);

                $posts = get_posts(array(
                    'post_type' => $object_type,
                    'title' => $post_title,
                    'post_status' => 'publish',
                    'numberposts' => 1
                ));
                $post = !empty($posts) ? $posts[0] : null;

                // Also try with the full title
                if (!$post) {
                    $posts = get_posts(array(
                        'post_type' => $object_type,
                        's' => $item['title'] ?? $item['post_title'] ?? '',
                        'post_status' => 'publish',
                        'numberposts' => 1
                    ));
                    $post = !empty($posts) ? $posts[0] : null;
                }
            }

            if ($post) {
                $menu_item_data['menu-item-object-id'] = $post->ID;
                $menu_item_data['menu-item-object'] = $object_type;
                // Update URL to the actual post URL
                $menu_item_data['menu-item-url'] = get_permalink($post->ID);
            } else {
                // Don't create menu items for pages that don't exist
                continue;
            }
        } elseif ($item['type'] === 'taxonomy') {
            // Handle taxonomy items (categories, tags, etc.)
            $taxonomy = $item['object'] ?? 'category';

            if (isset($item['slug'])) {
                $term = get_term_by('slug', $item['slug'], $taxonomy);
            } else {
                // Try to find by name as fallback
                $term = get_term_by('name', $item['title'] ?? '', $taxonomy);
            }

            if ($term) {
                $menu_item_data['menu-item-object-id'] = $term->term_id;
                $menu_item_data['menu-item-object'] = $taxonomy;
            }
        } elseif ($item['type'] === 'custom') {
            // Custom links don't need object IDs
            $menu_item_data['menu-item-url'] = $item['url'] ?? '#';
        }

        $new_item_id = wp_update_nav_menu_item($menu_id, 0, $menu_item_data);

        // Store the ID mapping for parent-child relationships
        if (!is_wp_error($new_item_id) && isset($item['ID'])) {
            $id_map[$item['ID']] = $new_item_id;
        }
    }
  }

  /**
   * Import demo settings (customizer and site options)
   */
  private function import_demo_settings() {
    // Check if settings already imported
    $settings_imported = get_option('jltma_demo_settings_imported');
    if ($settings_imported) {
        return true;
    }

    // Import customizer settings
    $this->import_customizer_settings();

    // Import site options
    $this->import_site_options();

    update_option('jltma_demo_settings_imported', true);
    return true;
  }

  /**
   * Import customizer settings
   */
  private function import_customizer_settings() {
    $customizer_file = $this->demo_path . '/settings/customizer.json';

    if (!file_exists($customizer_file)) {
        return false;
    }

    $json_content = file_get_contents($customizer_file);
    $customizer_data = json_decode($json_content, true);

    if (!$customizer_data) {
        return false;
    }

    // Process nav_menu_locations to map slugs to new IDs
    if (isset($customizer_data['nav_menu_locations']) && is_array($customizer_data['nav_menu_locations'])) {
        $new_locations = array();
        foreach ($customizer_data['nav_menu_locations'] as $location => $menu_slug) {
            // Find the menu by slug
            $menu = get_term_by('slug', $menu_slug, 'nav_menu');
            if ($menu) {
                $new_locations[$location] = $menu->term_id;
            }
        }
        set_theme_mod('nav_menu_locations', $new_locations);
        unset($customizer_data['nav_menu_locations']);
    }

    // Process custom_logo if it's a filename
    if (isset($customizer_data['custom_logo'])) {
        $logo_filename = $customizer_data['custom_logo'];
        // Import the logo image and get the attachment ID
        $attachment_id = $this->import_demo_image($logo_filename);
        if ($attachment_id && !is_wp_error($attachment_id)) {
            set_theme_mod('custom_logo', $attachment_id);
        }
        unset($customizer_data['custom_logo']);
    }

    // Process custom CSS post slug
    if (isset($customizer_data['custom_css_post_slug'])) {
        $css_post_slug = $customizer_data['custom_css_post_slug'];
        // Find the custom CSS post by slug
        $css_post = get_page_by_path($css_post_slug, OBJECT, 'custom_css');
        if ($css_post) {
            // Store the ID in a way WordPress expects
            // Note: WordPress typically handles custom CSS differently, but we'll store for reference
            set_theme_mod('custom_css_post_id', $css_post->ID);
        }
        unset($customizer_data['custom_css_post_slug']);
    }

    // Process any image fields that might be filenames
    foreach ($customizer_data as $option_name => &$option_value) {
        // Skip certain sensitive options
        if (in_array($option_name, array('admin_email', 'users_can_register'))) {
            continue;
        }

        // Check if this looks like an image filename
        if (is_string($option_value) && preg_match('/\.(jpg|jpeg|png|gif|svg)$/i', $option_value)) {
            // Try to import as image
            $attachment_id = $this->import_demo_image($option_value);
            if ($attachment_id && !is_wp_error($attachment_id)) {
                $option_value = $attachment_id;
            }
        }

        set_theme_mod($option_name, $option_value);
    }

    return true;
  }

  /**
   * Import site options
   */
  private function import_site_options() {
    $options_file = $this->demo_path . '/settings/site_options.json';

    if (!file_exists($options_file)) {
        return false;
    }

    $json_content = file_get_contents($options_file);
    $options_data = json_decode($json_content, true);

    if (!$options_data) {
        return false;
    }

    // Handle variation-specific landing and blog pages
    // Check for slug-based fields first (new format)
    if (isset($options_data['page_on_front_slug']) && !empty($options_data['page_on_front_slug'])) {
        // First check page mappings from import
        $mapped_id = $this->get_page_mapping($options_data['page_on_front_slug']);
        if ($mapped_id) {
            $landing_page = get_post($mapped_id);
        } else {
            // Fall back to finding by slug
            $landing_page = get_page_by_path($options_data['page_on_front_slug']);
        }

        if ($landing_page) {
            update_option('page_on_front', $landing_page->ID);
            update_option('show_on_front', 'page');
        }
    }
    // Legacy support for old format
    elseif (isset($options_data['landing_page']) && !empty($options_data['landing_page'])) {
        $landing_page = get_page_by_path($options_data['landing_page']);
        if ($landing_page) {
            update_option('page_on_front', $landing_page->ID);
            update_option('show_on_front', 'page');
        }
    }

    // Check for blog page slug (new format)
    if (isset($options_data['page_for_posts_slug']) && !empty($options_data['page_for_posts_slug'])) {
        // First check page mappings from import
        $mapped_id = $this->get_page_mapping($options_data['page_for_posts_slug']);
        if ($mapped_id) {
            $blog_page = get_post($mapped_id);
        } else {
            // Fall back to finding by slug
            $blog_page = get_page_by_path($options_data['page_for_posts_slug']);
        }

        if ($blog_page) {
            update_option('page_for_posts', $blog_page->ID);
        }
    }
    // Legacy support for old format
    elseif (isset($options_data['blog_page']) && !empty($options_data['blog_page'])) {
        $blog_page = get_page_by_path($options_data['blog_page']);
        if ($blog_page) {
            update_option('page_for_posts', $blog_page->ID);
        }
    }

    // Set show_on_front if specified
    if (isset($options_data['show_on_front'])) {
        update_option('show_on_front', $options_data['show_on_front']);
    }

    // Handle posts per page
    if (isset($options_data['posts_per_page'])) {
        update_option('posts_per_page', intval($options_data['posts_per_page']));
    }

    // Process other options
    foreach ($options_data as $option_name => $option_value) {
        // Skip certain critical options and already processed ones
        $skip_options = array(
            'siteurl',
            'home',
            'admin_email',
            'users_can_register',
            'default_role',
            'landing_page',
            'blog_page',
            'posts_per_page'
        );

        if (in_array($option_name, $skip_options)) {
            continue;
        }

        update_option($option_name, $option_value);
    }

    return true;
  }

}

// Initialize
JLTMA_Demo_Importer::get_instance();