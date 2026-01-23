<?php
namespace MasterAddons\Admin\WidgetBuilder;

defined('ABSPATH') || exit;

/**
 * Widget File Generator
 * Generates widget PHP files from widget builder data
 *
 * @package MasterAddons
 * @subpackage WidgetBuilder
 */
if (!class_exists('MasterAddons\Admin\WidgetBuilder\JLTMA_Widget_Generator')) {
class JLTMA_Widget_Generator {

    private $post_id;
    private $widget_data;
    private $widget_slug;
    private $widget_class;
    private $upload_dir;
    private $upload_url;
    private $widget_dir;
    private $widget_url;
    private $control_manager;

    // Prefixes
    private $name_prefix = 'jltma_wb_';
    private $class_prefix = 'JLTMA_WB_';

    /**
     * Constructor
     *
     * @param int $post_id Widget post ID
     */
    public function __construct($post_id) {
        $this->post_id = absint($post_id);
        $this->widget_slug = $this->name_prefix . $this->post_id;
        $this->widget_class = $this->class_prefix . $this->post_id;

        $this->init_directories();
        $this->load_widget_data();

        // Initialize control manager
        require_once __DIR__ . '/class-control-manager.php';
        $this->control_manager = Control_Manager::get_instance();
    }

    /**
     * Initialize upload directories
     */
    private function init_directories() {
        $upload = wp_upload_dir();

        $this->upload_dir = $upload['basedir'] . '/master_addons/widgets';
        $this->upload_url = $upload['baseurl'] . '/master_addons/widgets';

        $this->widget_dir = $this->upload_dir . '/' . $this->post_id;
        $this->widget_url = $this->upload_url . '/' . $this->post_id;
    }

    /**
     * Load widget data from post meta
     */
    private function load_widget_data() {
        $this->widget_data = get_post_meta($this->post_id, '_jltma_widget_data', true);

        if (empty($this->widget_data)) {
            $this->widget_data = $this->get_default_data();
        }

        // Load sections data separately
        $sections = get_post_meta($this->post_id, '_jltma_widget_sections', true);
        if (!empty($sections) && is_array($sections)) {
            $this->widget_data['sections'] = $sections;
        } else {
            $this->widget_data['sections'] = [];
        }

        // Load includes data separately (CSS/JS libraries)
        $includes = get_post_meta($this->post_id, '_jltma_widget_includes', true);
        if (!empty($includes) && is_array($includes)) {
            $this->widget_data['includes'] = $includes;
        } else {
            $this->widget_data['includes'] = [
                'css_libraries' => [],
                'js_libraries' => []
            ];
        }
    }

    /**
     * Get default widget data structure
     *
     * @return array
     */
    private function get_default_data() {
        return [
            'title' => get_the_title($this->post_id),
            'icon' => 'eicon-code',
            'category' => get_post_meta($this->post_id, '_jltma_widget_category', true) ?: 'master-addons',
            'sections' => [],
            'html_code' => '',
            'css_code' => '',
            'js_code' => ''
        ];
    }

    /**
     * Generate all widget files
     *
     * @return bool|WP_Error
     */
    public function generate() {
        // Create widget directory
        if (!$this->create_directory()) {
            return new \WP_Error('directory_failed', 'Failed to create widget directory');
        }

        // Generate PHP widget file
        $php_result = $this->generate_php_file();
        if (is_wp_error($php_result)) {
            return $php_result;
        }

        // Generate CSS file if code exists
        if (!empty($this->widget_data['css_code'])) {
            $css_result = $this->generate_css_file();
            if (is_wp_error($css_result)) {
                return $css_result;
            }
        }

        // Generate JS file if code exists
        if (!empty($this->widget_data['js_code'])) {
            $js_result = $this->generate_js_file();
            if (is_wp_error($js_result)) {
                return $js_result;
            }
        }

        return true;
    }

    /**
     * Create widget directory
     *
     * @return bool
     */
    private function create_directory() {
        if (!file_exists($this->widget_dir)) {
            return wp_mkdir_p($this->widget_dir);
        }
        return true;
    }

    /**
     * Generate PHP widget file
     *
     * @return bool|WP_Error
     */
    private function generate_php_file() {
        $content = $this->build_php_content();

        $file_path = $this->widget_dir . '/widget.php';

        // Use WP_Filesystem
        global $wp_filesystem;
        if (empty($wp_filesystem)) {
            require_once(ABSPATH . '/wp-admin/includes/file.php');
            WP_Filesystem();
        }

        $result = $wp_filesystem->put_contents($file_path, $content, FS_CHMOD_FILE);

        if (!$result) {
            return new \WP_Error('file_write_failed', 'Failed to write widget.php file');
        }

        return true;
    }

    /**
     * Build PHP widget file content
     *
     * @return string
     */
    private function build_php_content() {
        $content = "<?php\n";
        $content .= "namespace MasterAddons\\Addons;\n\n";
        $content .= "use \\Elementor\\Widget_Base;\n";
        $content .= "use \\Elementor\\Controls_Manager;\n";
        $content .= "use \\Elementor\\Core\\Kits\\Documents\\Tabs\\Global_Typography;\n\n";
        $content .= "if (!defined('ABSPATH')) exit;\n\n";
        $content .= "/**\n";
        $content .= " * " . esc_html($this->widget_data['title']) . "\n";
        $content .= " * Generated by Master Addons Widget Builder\n";
        $content .= " * Widget ID: " . $this->post_id . "\n";
        $content .= " */\n";
        $content .= "class " . $this->widget_class . " extends Widget_Base {\n\n";

        // Constructor if CSS/JS files exist
        if (!empty($this->widget_data['css_code']) || !empty($this->widget_data['js_code'])) {
            $content .= $this->build_constructor();
        }

        // Widget name
        $content .= $this->build_get_name();

        // Widget title
        $content .= $this->build_get_title();

        // Widget icon
        $content .= $this->build_get_icon();

        // Widget categories
        $content .= $this->build_get_categories();

        // Register controls
        $content .= $this->build_register_controls();

        // Add tabs data helper method if there are tab controls
        $tabs_helper = $this->build_get_tabs_data_helper();
        if (!empty($tabs_helper)) {
            $content .= $tabs_helper;
        }

        // Render method
        $content .= $this->build_render();

        $content .= "}\n";

        return $content;
    }

    /**
     * Build constructor method
     *
     * @return string
     */
    private function build_constructor() {
        $handle = 'jltma-wb-' . $this->post_id;

        $content = "\tpublic function __construct(\$data = [], \$args = null) {\n";
        $content .= "\t\tparent::__construct(\$data, \$args);\n\n";

        // Register external CSS libraries
        if (!empty($this->widget_data['includes']['css_libraries'])) {
            foreach ($this->widget_data['includes']['css_libraries'] as $css_lib) {
                if (!empty($css_lib['handle']) && !empty($css_lib['src'])) {
                    $deps = !empty($css_lib['dependencies']) && is_array($css_lib['dependencies'])
                        ? $css_lib['dependencies']
                        : [];
                    $deps_string = $this->build_deps_array($deps);

                    // Only register if src is a URL (external library)
                    if (filter_var($css_lib['src'], FILTER_VALIDATE_URL)) {
                        $content .= "\t\twp_register_style('{$css_lib['handle']}', '{$css_lib['src']}', {$deps_string}, '1.0.0');\n";
                    }
                }
            }
        }

        // Register external JS libraries
        if (!empty($this->widget_data['includes']['js_libraries'])) {
            foreach ($this->widget_data['includes']['js_libraries'] as $js_lib) {
                if (!empty($js_lib['handle']) && !empty($js_lib['src'])) {
                    $deps = !empty($js_lib['dependencies']) && is_array($js_lib['dependencies'])
                        ? $js_lib['dependencies']
                        : [];
                    $deps_string = $this->build_deps_array($deps);

                    // Only register if src is a URL (external library)
                    if (filter_var($js_lib['src'], FILTER_VALIDATE_URL)) {
                        $content .= "\t\twp_register_script('{$js_lib['handle']}', '{$js_lib['src']}', {$deps_string}, '1.0.0', true);\n";
                    }
                }
            }
        }

        // Register widget's own CSS
        if (!empty($this->widget_data['css_code'])) {
            $content .= "\t\twp_register_style('{$handle}-style', '{$this->widget_url}/style.css', [], '1.0.0');\n";
        }

        // Register widget's own JS
        if (!empty($this->widget_data['js_code'])) {
            $content .= "\t\twp_register_script('{$handle}-script', '{$this->widget_url}/script.js', ['elementor-frontend'], '1.0.0', true);\n";
        }

        $content .= "\t}\n\n";

        // Build get_style_depends method
        $style_deps = [];

        // Add external CSS library handles
        if (!empty($this->widget_data['includes']['css_libraries'])) {
            foreach ($this->widget_data['includes']['css_libraries'] as $css_lib) {
                if (!empty($css_lib['handle'])) {
                    $style_deps[] = $css_lib['handle'];
                }
            }
        }

        // Add widget's own CSS
        if (!empty($this->widget_data['css_code'])) {
            $style_deps[] = "{$handle}-style";
        }

        if (!empty($style_deps)) {
            $content .= "\tpublic function get_style_depends() {\n";
            $content .= "\t\treturn " . $this->build_deps_array($style_deps) . ";\n";
            $content .= "\t}\n\n";
        }

        // Build get_script_depends method
        $script_deps = [];

        // Add external JS library handles
        if (!empty($this->widget_data['includes']['js_libraries'])) {
            foreach ($this->widget_data['includes']['js_libraries'] as $js_lib) {
                if (!empty($js_lib['handle'])) {
                    $script_deps[] = $js_lib['handle'];
                }
            }
        }

        // Add widget's own JS
        if (!empty($this->widget_data['js_code'])) {
            $script_deps[] = "{$handle}-script";
        }

        if (!empty($script_deps)) {
            $content .= "\tpublic function get_script_depends() {\n";
            $content .= "\t\treturn " . $this->build_deps_array($script_deps) . ";\n";
            $content .= "\t}\n\n";
        }

        return $content;
    }

    /**
     * Build dependencies array string for PHP code
     *
     * @param array $deps
     * @return string
     */
    private function build_deps_array($deps) {
        if (empty($deps)) {
            return '[]';
        }

        $quoted_deps = array_map(function($dep) {
            return "'" . esc_attr($dep) . "'";
        }, $deps);

        return '[' . implode(', ', $quoted_deps) . ']';
    }

    /**
     * Build get_name method
     *
     * @return string
     */
    private function build_get_name() {
        $content = "\tpublic function get_name() {\n";
        $content .= "\t\treturn '{$this->widget_slug}';\n";
        $content .= "\t}\n\n";

        return $content;
    }

    /**
     * Build get_title method
     *
     * @return string
     */
    private function build_get_title() {
        $title = !empty($this->widget_data['title']) ? esc_html($this->widget_data['title']) : 'Custom Widget';

        $content = "\tpublic function get_title() {\n";
        $content .= "\t\treturn esc_html__('{$title}', 'master-addons');\n";
        $content .= "\t}\n\n";

        return $content;
    }

    /**
     * Build get_icon method
     *
     * @return string
     */
    private function build_get_icon() {
        $icon = !empty($this->widget_data['icon']) ? $this->widget_data['icon'] : 'eicon-code';

        $content = "\tpublic function get_icon() {\n";
        $content .= "\t\treturn '{$icon}';\n";
        $content .= "\t}\n\n";

        return $content;
    }

    /**
     * Build get_categories method
     *
     * @return string
     */
    private function build_get_categories() {
        $category = !empty($this->widget_data['category']) ? $this->widget_data['category'] : 'master-addons';

        $content = "\tpublic function get_categories() {\n";
        $content .= "\t\treturn ['{$category}'];\n";
        $content .= "\t}\n\n";

        return $content;
    }

    /**
     * Build register_controls method
     *
     * @return string
     */
    private function build_register_controls() {
        $content = "\tprotected function register_controls() {\n";

        if (!empty($this->widget_data['sections']) && is_array($this->widget_data['sections'])) {
            // Sort sections by tab order: content, style, advanced
            $sorted_sections = $this->sort_sections_by_tab($this->widget_data['sections']);

            foreach ($sorted_sections as $section_id => $section) {
                // Ensure section has proper structure
                if (is_array($section)) {
                    $content .= $this->build_section($section_id, $section);
                }
            }
        }

        $content .= "\t}\n\n";

        return $content;
    }

    /**
     * Sort sections by tab order: content, style, advanced
     *
     * @param array $sections
     * @return array
     */
    private function sort_sections_by_tab($sections) {
        $content_sections = [];
        $style_sections = [];
        $advanced_sections = [];

        foreach ($sections as $section_id => $section) {
            if (!is_array($section)) {
                continue;
            }

            $tab = !empty($section['tab']) ? $section['tab'] : 'content';

            if ($tab === 'style') {
                $style_sections[$section_id] = $section;
            } elseif ($tab === 'advanced') {
                $advanced_sections[$section_id] = $section;
            } else {
                $content_sections[$section_id] = $section;
            }
        }

        // Merge in correct order: content, style, advanced
        return array_merge($content_sections, $style_sections, $advanced_sections);
    }

    /**
     * Build a control section
     *
     * @param string $section_id
     * @param array $section
     * @return string
     */
    private function build_section($section_id, $section) {
        // Try 'title' first (used by Widget Builder), fall back to 'label', then default to 'Section'
        $label = !empty($section['title']) ? esc_html($section['title']) : (!empty($section['label']) ? esc_html($section['label']) : 'Section');
        $tab = !empty($section['tab']) ? $section['tab'] : 'content';

        // Generate section key with proper prefix based on tab
        $tab_prefix = '';
        $tab_const = 'Controls_Manager::TAB_CONTENT';

        if ($tab === 'style') {
            $tab_prefix = 'jltma_style_';
            $tab_const = 'Controls_Manager::TAB_STYLE';
        } elseif ($tab === 'advanced') {
            $tab_prefix = 'jltma_advanced_';
            $tab_const = 'Controls_Manager::TAB_ADVANCED';
        } else {
            $tab_prefix = 'jltma_content_';
        }
        
        // Create sanitized section slug from label and add post ID
        $section_slug = $this->sanitize_key($label);
        $section_key = $tab_prefix . $section_slug . '_' . $section_id.  '_'  . $this->post_id;

        $content = "\n\t\t\$this->start_controls_section(\n";
        $content .= "\t\t\t'{$section_key}',\n";
        $content .= "\t\t\t[\n";
        $content .= "\t\t\t\t'label' => esc_html__('{$label}', 'master-addons'),\n";
        $content .= "\t\t\t\t'tab' => {$tab_const},\n";
        $content .= "\t\t\t]\n";
        $content .= "\t\t);\n\n";
        
        // Add controls (check both 'fields' and 'controls' for backwards compatibility)
        $controls = !empty($section['controls']) ? $section['controls'] : (!empty($section['fields']) ? $section['fields'] : []);
        
        
        if (!empty($controls) && is_array($controls)) {
            foreach ($controls as $field_id => $field) {
                $content .= $this->build_control($field_id, $field, $tab);
            }
        }

        $content .= "\t\t\$this->end_controls_section();\n";
        return $content;
    }

    /**
     * Build a control
     *
     * @param string $field_id
     * @param array $field
     * @param string $tab Current tab (content/style/advanced)
     * @return string
     */
    private function build_control($field_id, $field, $tab = 'content') {
        $label = !empty($field['label']) ? esc_html($field['label']) : 'Control';
        $type = !empty($field['type']) ? $field['type'] : 'TEXT';

        // Special handling for TABS control - it's a structural element, not a regular control
        // TABS control requires the full field data with 'tabs' array and should use field['name'] as key
        if (strtoupper($type) === 'TABS') {
            // For TABS, use the control name directly as the key (not label-based)
            $control_name = !empty($field['name']) ? $field['name'] : 'tabs_' . $field_id;

            // Pass tab and widget_id context
            $field['_tab'] = $tab;
            $field['_widget_id'] = $this->post_id;

            // Call TABS control builder directly with the name as key
            return $this->control_manager->build_control($control_name, $field, $type);
        }

        // Generate control key with proper prefix based on tab
        $tab_prefix = '';
        if ($tab === 'style') {
            $tab_prefix = 'jltma_style_';
        } elseif ($tab === 'advanced') {
            $tab_prefix = 'jltma_advanced_';
        } else {
            $tab_prefix = 'jltma_content_';
        }

        // Create sanitized control slug from label and add post ID
        $control_slug = $this->sanitize_key($label);
        $control_key = $tab_prefix . $control_slug . '_' . $this->post_id;

        // Pass tab and widget_id context to control manager for condition key conversion
        $field['_tab'] = $tab;
        $field['_widget_id'] = $this->post_id;
        $field['_tab_prefix'] = $tab_prefix;

        // Preprocess date_time controls - convert UI settings to picker_options
        if ($type === 'date_time') {
            $picker_options = [];

            // Convert enable_time to picker_options
            $enable_time = isset($field['enable_time']) ? (bool) $field['enable_time'] : false;
            if (isset($field['enable_time'])) {
                $picker_options['enableTime'] = $enable_time;
            }

            // Set dateFormat based on enableTime
            $picker_options['dateFormat'] = $enable_time ? 'Y-m-d H:i' : 'Y-m-d';

            // Always use 24-hour format
            $picker_options['time_24hr'] = true;

            // Convert minute_increment to picker_options
            if (isset($field['minute_increment']) && !empty($field['minute_increment'])) {
                $picker_options['minuteIncrement'] = intval($field['minute_increment']);
            }

            // Merge with existing picker_options if any
            if (!empty($field['picker_options']) && is_array($field['picker_options'])) {
                $picker_options = array_merge($field['picker_options'], $picker_options);
            }

            // Set picker_options
            if (!empty($picker_options)) {
                $field['picker_options'] = $picker_options;
            }
        }

        // Use Control Manager to build control
        return $this->control_manager->build_control($control_key, $field, $type);
    }

    /**
     * Get tab data structures from widget sections
     * Returns array of tab control names with their field mappings
     *
     * @return array ['tab_control_name' => ['name' => 'tab_name', 'tabs' => [...]]]
     */
    private function get_tab_structures() {
        $tab_structures = [];

        if (empty($this->widget_data['sections']) || !is_array($this->widget_data['sections'])) {
            return $tab_structures;
        }

        foreach ($this->widget_data['sections'] as $section) {
            if (!is_array($section) || empty($section['controls'])) {
                continue;
            }

            foreach ($section['controls'] as $control) {
                if (empty($control['type']) || strtoupper($control['type']) !== 'TABS') {
                    continue;
                }

                if (empty($control['name'])) {
                    continue;
                }

                // Get tab info - check for both 'tabs' array (processed) and 'fields'+'tab_fields' (raw from UI)
                $tabs = [];

                if (!empty($control['tabs']) && is_array($control['tabs'])) {
                    // Already processed tabs structure
                    $tabs = $control['tabs'];
                } elseif (!empty($control['fields']) && is_array($control['fields'])) {
                    // Raw structure from UI - build tabs array
                    $tab_fields = !empty($control['tab_fields']) && is_array($control['tab_fields']) ? $control['tab_fields'] : [];

                    foreach ($control['fields'] as $tab_definition) {
                        if (empty($tab_definition['name'])) {
                            continue;
                        }

                        $tab_name = $tab_definition['name'];
                        $tab = [
                            'name' => $tab_name,
                            'label' => !empty($tab_definition['label']) ? $tab_definition['label'] : ucfirst($tab_name),
                            'controls' => []
                        ];

                        // Add controls for this tab if they exist
                        if (!empty($tab_fields[$tab_name]) && is_array($tab_fields[$tab_name])) {
                            $tab['controls'] = $tab_fields[$tab_name];
                        }

                        $tabs[] = $tab;
                    }
                }

                if (!empty($tabs)) {
                    $tab_control_name = $control['name'];
                    $tab_structures[$tab_control_name] = [
                        'name' => $tab_control_name,
                        'tabs' => $tabs
                    ];
                }
            }
        }

        return $tab_structures;
    }

    /**
     * Build get_tabs_data helper method
     * Creates a helper method that organizes tab control data into accessible array structure
     *
     * @return string
     */
    private function build_get_tabs_data_helper() {
        $tab_structures = $this->get_tab_structures();

        if (empty($tab_structures)) {
            return '';
        }

        $content = "\t/**\n";
        $content .= "\t * Get tabs data organized by tab control\n";
        $content .= "\t * Helper method to access tab data as arrays\n";
        $content .= "\t *\n";
        $content .= "\t * @param array \$settings Widget settings\n";
        $content .= "\t * @return array Organized tab data\n";
        $content .= "\t */\n";
        $content .= "\tprivate function get_tabs_data(\$settings) {\n";
        $content .= "\t\t\$tabs_data = [];\n\n";

        // Build data structure for each tab control
        foreach ($tab_structures as $tab_control_name => $tab_info) {
            $content .= "\t\t// Tab control: {$tab_control_name}\n";
            $content .= "\t\t\$tabs_data['{$tab_control_name}'] = [\n";
            $content .= "\t\t\t'tabs' => [],\n";
            $content .= "\t\t];\n\n";

            foreach ($tab_info['tabs'] as $tab_index => $tab) {
                $tab_name = $tab['name'];
                $tab_label = $tab['label'] ?? ucfirst($tab_name);

                $content .= "\t\t// Tab: {$tab_label}\n";
                $content .= "\t\t\$tabs_data['{$tab_control_name}']['tabs']['{$tab_name}'] = [\n";
                $content .= "\t\t\t'name' => '{$tab_name}',\n";
                $content .= "\t\t\t'label' => '{$tab_label}',\n";
                $content .= "\t\t\t'content' => [],\n";
                $content .= "\t\t];\n\n";

                // Map controls from this tab
                if (!empty($tab['controls']) && is_array($tab['controls'])) {
                    foreach ($tab['controls'] as $control) {
                        if (empty($control['name']) || empty($control['label'])) {
                            continue;
                        }

                        // Get the actual control key in settings
                        $control_label = $control['label'];
                        $control_slug = $this->sanitize_key($control_label);

                        // Get tab context from widget data
                        $tab_context = $this->get_tab_context_for_control($control['name']);
                        $tab_prefix = $this->get_tab_prefix($tab_context);
                        $control_key = $tab_prefix . $control_slug . '_' . $this->post_id;

                        $control_name = $control['name'];

                        $content .= "\t\t\$tabs_data['{$tab_control_name}']['tabs']['{$tab_name}']['content']['{$control_name}'] = \$settings['{$control_key}'] ?? '';\n";
                    }
                }

                $content .= "\n";
            }
        }

        $content .= "\t\treturn \$tabs_data;\n";
        $content .= "\t}\n\n";

        return $content;
    }

    /**
     * Get tab context (content/style/advanced) for a control
     *
     * @param string $control_name Control name to search for
     * @return string Tab context (content/style/advanced)
     */
    private function get_tab_context_for_control($control_name) {
        if (empty($this->widget_data['sections']) || !is_array($this->widget_data['sections'])) {
            return 'content';
        }

        foreach ($this->widget_data['sections'] as $section) {
            if (!is_array($section) || empty($section['controls'])) {
                continue;
            }

            foreach ($section['controls'] as $control) {
                if (empty($control['type']) || strtoupper($control['type']) !== 'TABS') {
                    continue;
                }

                if (!empty($control['tabs']) && is_array($control['tabs'])) {
                    foreach ($control['tabs'] as $tab) {
                        if (!empty($tab['controls']) && is_array($tab['controls'])) {
                            foreach ($tab['controls'] as $tab_control) {
                                if (isset($tab_control['name']) && $tab_control['name'] === $control_name) {
                                    // Found the control, return the section's tab context
                                    return $section['tab'] ?? 'content';
                                }
                            }
                        }
                    }
                }
            }
        }

        return 'content';
    }
    
    /**
     * Get tab prefix based on tab context
     *
     * @param string $tab Tab context (content/style/advanced)
     * @return string Prefix for control keys
     */
    private function get_tab_prefix($tab) {
        if ($tab === 'style') {
            return 'jltma_style_';
        } elseif ($tab === 'advanced') {
            return 'jltma_advanced_';
        } else {
            return 'jltma_content_';
        }
    }

    /**
     * Sanitize label to create control/section key
     * Converts spaces to underscores instead of hyphens
     *
     * @param string $label Label to sanitize
     * @return string Sanitized key with underscores
     */
    private function sanitize_key($label) {
        // Convert to lowercase
        $key = strtolower($label);

        // Replace spaces with underscores
        $key = str_replace(' ', '_', $key);

        // Remove special characters, keeping only alphanumeric and underscores
        $key = preg_replace('/[^a-z0-9_]/', '', $key);

        // Remove multiple consecutive underscores
        $key = preg_replace('/_+/', '_', $key);

        // Trim underscores from beginning and end
        $key = trim($key, '_');

        return $key;
    }

    /**
     * Build render method
     *
     * @return string
     */
    private function build_render() {
        $html = !empty($this->widget_data['html_code']) ? $this->widget_data['html_code'] : '';
        
        $html = $this->prepare_html_for_render($html);

        $content = "\tprotected function render() {\n";
        $content .= "\t\t\$settings = \$this->get_settings_for_display();\n";
        $content .= "\t\t\$this->render_widget_content(\$settings);\n";
        $content .= "\t}\n\n";

        // Add render_shortcode method for shortcode support
        $content .= "\t/**\n";
        $content .= "\t * Render widget as shortcode\n";
        $content .= "\t * \n";
        $content .= "\t * @param array \$settings Settings array from shortcode attributes\n";
        $content .= "\t */\n";
        $content .= "\tpublic function render_shortcode(\$settings = []) {\n";
        $content .= "\t\t// Merge with defaults\n";
        $content .= "\t\tif (empty(\$settings)) {\n";
        $content .= "\t\t\t\$settings = \$this->get_settings_for_display();\n";
        $content .= "\t\t}\n";
        $content .= "\t\t\$this->render_widget_content(\$settings);\n";
        $content .= "\t}\n\n";

        // Add render_widget_content method - shared between Elementor and shortcode
        $content .= "\t/**\n";
        $content .= "\t * Render widget HTML content\n";
        $content .= "\t * Shared method for both Elementor widget and shortcode output\n";
        $content .= "\t * \n";
        $content .= "\t * @param array \$settings Widget settings\n";
        $content .= "\t */\n";
        $content .= "\tprotected function render_widget_content(\$settings) {\n";

        // Check if we have tab controls and add tabs data
        $tab_structures = $this->get_tab_structures();
        if (!empty($tab_structures)) {
            $content .= "\t\t// Get organized tab data\n";
            $content .= "\t\t\$tabs_data = \$this->get_tabs_data(\$settings);\n\n";
        }

        // Build control mapping for placeholder replacement
        $control_mapping = $this->build_control_mapping();

        // Get tab structures for tab data access
        $tab_structures = $this->get_tab_structures();

        // Replace placeholders in HTML with PHP code
        // Two-pass approach:
        // 1. First pass: Replace placeholders INSIDE PHP tags with just variable references
        // 2. Second pass: Replace placeholders OUTSIDE PHP tags with full <?php echo ...  tags
        if (!empty($control_mapping)) {
            foreach($control_mapping as $control => $param){
                // $content .= "\t \$$control = \$settings[\'$param\'];\n";
                $content .= "\t \$$control = \$settings['$param'];\n";
            }
            // Pass 1: Handle placeholders inside PHP tags
            $html = $this->replace_placeholders_in_php_context($html, $control_mapping, $tab_structures);

            // Pass 2: Handle placeholders outside PHP tags
            $html = $this->replace_placeholders_outside_php_context($html, $control_mapping, $tab_structures);
        }

        // Process CSS code if it contains template strings
        $css_code = !empty($this->widget_data['css_code']) ? $this->widget_data['css_code'] : '';
        $has_css_templates = !empty($css_code) && preg_match('/\{\{[^}]+\}\}/', $css_code);

        // Process JS code if it contains template strings
        $js_code = !empty($this->widget_data['js_code']) ? $this->widget_data['js_code'] : '';
        $has_js_templates = !empty($js_code) && preg_match('/\{\{[^}]+\}\}/', $js_code);

        // Output the processed HTML
        $content .= "\t\t?" . ">\n";

        // Output dynamic CSS if it contains template strings
        if ($has_css_templates && !empty($control_mapping)) {
            $content .= "\t\t<style>\n";
            $content .= "\t\t\t<" . "?php\n";
            $content .= "\t\t\t" . $this->build_dynamic_css_output($css_code, $control_mapping, $tab_structures);
            $content .= "\t\t\t?" . ">\n";
            $content .= "\t\t</style>\n";
        }

        if (!empty($html)) {
            $content .= $html . "\n";
        }

        // Output dynamic JS if it contains template strings
        if ($has_js_templates && !empty($control_mapping)) {
            $content .= "\t\t<script>\n";
            $content .= "\t\t\t<" . "?php\n";
            $content .= "\t\t\t" . $this->build_dynamic_js_output($js_code, $control_mapping, $tab_structures);
            $content .= "\t\t\t?" . ">\n";
            $content .= "\t\t</script>\n";
        }

        $content .= "\t\t<" . "?php\n";
        $content .= "\t}\n\n";

        return $content;
    }

    /**
     * Replace placeholders inside PHP context
     * Replaces {{placeholder}} with just variable references, no PHP tags
     *
     * @param string $html HTML code with placeholders
     * @param array $control_mapping Mapping of control names to keys
     * @param array $tab_structures Tab control structures
     * @return string HTML with placeholders inside PHP replaced
     */
    private function replace_placeholders_in_php_context($html, $control_mapping, $tab_structures) {
        // Match PHP blocks and replace placeholders within them
        $php_open = '<' . '?php';
        $php_close = '?' . '>';
        $pattern = '/' . preg_quote($php_open, '/') . '(.*?)' . preg_quote($php_close, '/') . '/s';

        return preg_replace_callback(
            $pattern,
            function($matches) use ($control_mapping, $tab_structures, $php_open, $php_close) {
                $php_code = $matches[1];

                // Replace placeholders within this PHP block
                $php_code = preg_replace_callback(
                    '/\{\{([^}]+)\}\}/',
                    function($inner_matches) use ($control_mapping, $tab_structures) {
                        return $this->get_variable_reference($inner_matches[1], $control_mapping, $tab_structures);
                    },
                    $php_code
                );

                return $php_open . $php_code . $php_close;
            },
            $html
        );
    }

    /**
     * Replace placeholders outside PHP context
     * Replaces {{placeholder}} with full PHP echo statements
     *
     * @param string $html HTML code with placeholders
     * @param array $control_mapping Mapping of control names to keys
     * @param array $tab_structures Tab control structures
     * @return string HTML with remaining placeholders replaced
     */
    private function replace_placeholders_outside_php_context($html, $control_mapping, $tab_structures) {
        return preg_replace_callback(
            '/\{\{([^}]+)\}\}/',
            function($matches) use ($control_mapping, $tab_structures) {
                $placeholder = trim($matches[1]);

                // Get the variable reference
                $var_ref = $this->get_variable_reference($placeholder, $control_mapping, $tab_structures);

                // If it returned just a variable (not a full echo statement), wrap it in echo
                if ($var_ref !== $matches[0] && strpos($var_ref, '<' . '?php') === false) {
                    // Determine appropriate escaping based on control type
                    $parts = explode('.', $placeholder);
                    $field_name = $parts[0];
                    $control_info = $this->get_control_info($field_name);
                    $control_type = $control_info['type'] ?? 'text';

                    // For simple variable references, wrap in echo with appropriate escaping
                    if (in_array(strtolower($control_type), ['wysiwyg', 'code'])) {
                        return '<' . '?php echo ' . $var_ref . '; ?' . '>';
                    } else {
                        return '<' . '?php echo esc_html(' . $var_ref . '); ?' . '>';
                    }
                }

                return $var_ref;
            },
            $html
        );
    }

    /**
     * Get variable reference for a placeholder
     * Returns just the PHP variable access code without PHP tags or echo
     *
     * @param string $placeholder Placeholder string (without {{ }})
     * @param array $control_mapping Mapping of control names to keys
     * @param array $tab_structures Tab control structures
     * @return string Variable reference or original placeholder if not found
     */
    private function get_variable_reference($placeholder, $control_mapping, $tab_structures) {
        $placeholder = trim($placeholder);

        // Check if this is a tab data access pattern (e.g., abc_tabs.tabs)
        if (!empty($tab_structures)) {
            foreach ($tab_structures as $tab_control_name => $tab_info) {
                // Check for exact match: tab_name.tabs
                if ($placeholder === $tab_control_name . '.tabs') {
                    return "\$tabs_data['{$tab_control_name}']['tabs']";
                }
                // Also support just the tab control name to get entire tab data
                if ($placeholder === $tab_control_name) {
                    return "\$tabs_data['{$tab_control_name}']";
                }
            }
        }

        // Parse placeholder for nested properties (e.g., url.url, url.target, icons.value)
        $parts = explode('.', $placeholder);
        $field_name = $parts[0];
        $property = isset($parts[1]) ? $parts[1] : null;

        if (isset($control_mapping[$field_name])) {
            $control_key = $control_mapping[$field_name];
            $control_info = $this->get_control_info($field_name);
            $control_type = $control_info['type'] ?? 'text';

            // Return just the variable reference for use in PHP context
            // For simple controls, return the settings value
            // For complex controls with properties, return the nested array access
            if ($property) {
                // Handle nested properties
                switch (strtolower($control_type)) {
                    case 'url':
                    case 'media':
                    case 'image':
                    case 'icons':
                    case 'icon':
                    case 'slider':
                    case 'dimensions':
                        return "\$settings['{$control_key}']['{$property}']";
                    default:
                        return "\$settings['{$control_key}']";
                }
            } else {
                // No property, just return the setting value
                return "\$settings['{$control_key}']";
            }
        }

        // If placeholder not found in mapping, return as-is
        return '{{' . $placeholder . '}}';
    }

    /**
     * Build control mapping for placeholder replacement
     * Maps control names (placeholders) to their full control keys
     *
     * @return array Associative array: placeholder => control_key
     */
    private function build_control_mapping() {
        $mapping = [];

        // Check if sections exist
        if (empty($this->widget_data['sections']) || !is_array($this->widget_data['sections'])) {
            return $mapping;
        }

        // Map tab names to prefixes
        $tab_prefix_map = [
            'content' => 'jltma_content_',
            'style' => 'jltma_style_',
            'advanced' => 'jltma_advanced_',
        ];

        // Iterate through all sections
        foreach ($this->widget_data['sections'] as $section_id => $section) {
            if (!is_array($section)) {
                continue;
            }

            // Get tab name (default to content)
            $tab = !empty($section['tab']) ? $section['tab'] : 'content';
            $tab_prefix = $tab_prefix_map[$tab] ?? 'jltma_content_';

            // Check both 'controls' and 'fields' keys for backwards compatibility
            $controls = !empty($section['controls']) ? $section['controls'] : (!empty($section['fields']) ? $section['fields'] : []);

            // Iterate through controls
            foreach ($controls as $control) {
                if (empty($control['name'])) {
                    continue;
                }

                // Control name as used in HTML (placeholder)
                $control_name = $control['name'];

                // Full control key as used in Elementor
                $control_slug = $this->sanitize_key($control['label'] ?? $control_name);
                $control_key = $tab_prefix . $control_slug . '_' . $this->post_id;

                // Map placeholder to control key
                $mapping[$control_name] = $control_key;

                // Handle popover_toggle child fields
                if (!empty($control['type']) && strtoupper($control['type']) === 'POPOVER_TOGGLE') {
                    if (!empty($control['popover_fields']) && is_array($control['popover_fields'])) {
                        foreach ($control['popover_fields'] as $popover_field) {
                            if (empty($popover_field['name'])) {
                                continue;
                            }

                            // Child field name
                            $child_field_name = $popover_field['name'];

                            // Placeholder pattern: parent_name_child_name (e.g., popover_toggle_color)
                            $placeholder = $control_name . '_' . $child_field_name;

                            // Actual control key pattern: parent_control_key_child_name
                            // (e.g., jltma_content_popover_toggle_381_color)
                            $child_field_slug = $this->sanitize_key($child_field_name);
                            $child_control_key = $control_key . '_' . $child_field_slug;

                            // Map placeholder to control key
                            $mapping[$placeholder] = $child_control_key;
                        }
                    }
                }
            }
        }


        return $mapping;
    }

    /**
     * Get control info by control name
     * Returns control type and other metadata
     *
     * @param string $control_name
     * @return array Control info with 'type' and other properties
     */
    private function get_control_info($control_name) {
        // Check if sections exist
        if (empty($this->widget_data['sections']) || !is_array($this->widget_data['sections'])) {
            return ['type' => 'text'];
        }

        // Search through all sections
        foreach ($this->widget_data['sections'] as $section_id => $section) {
            if (!is_array($section)) {
                continue;
            }

            // Check both 'controls' and 'fields' keys for backwards compatibility
            $controls = !empty($section['controls']) ? $section['controls'] : (!empty($section['fields']) ? $section['fields'] : []);

            // Search for the control by name
            foreach ($controls as $control) {
                if (!empty($control['name']) && $control['name'] === $control_name) {
                    return [
                        'type' => $control['type'] ?? 'text',
                        'label' => $control['label'] ?? '',
                        'default' => $control['default'] ?? '',
                        'responsive' => $control['responsive'] ?? false,
                    ];
                }

                // Check if this is a popover_toggle child field pattern (parent_name_child_name)
                if (!empty($control['type']) && strtoupper($control['type']) === 'POPOVER_TOGGLE') {
                    if (!empty($control['popover_fields']) && is_array($control['popover_fields'])) {
                        $parent_name = $control['name'];
                        foreach ($control['popover_fields'] as $popover_field) {
                            if (empty($popover_field['name'])) {
                                continue;
                            }

                            // Check if control_name matches pattern: parent_name_child_name
                            $expected_pattern = $parent_name . '_' . $popover_field['name'];
                            if ($control_name === $expected_pattern) {
                                return [
                                    'type' => $popover_field['type'] ?? 'text',
                                    'label' => $popover_field['label'] ?? '',
                                    'default' => $popover_field['default'] ?? '',
                                    'responsive' => $popover_field['responsive'] ?? false,
                                    'parent' => $parent_name,
                                ];
                            }
                        }
                    }
                }
            }
        }

        // Default if not found
        return ['type' => 'text'];
    }

    /**
     * Prepare HTML code for render method
     * Ensures the HTML doesn't break PHP context
     *
     * @param string $html
     * @return string
     */
    private function prepare_html_for_render($html) {
        if (empty($html)) {
            return '';
        }

        // Ensure the HTML doesn't contain closing PHP tags that would break the context
        // This is a security measure to prevent breaking out of the render method
        // We replace any standalone PHP tag boundaries
        $html = str_replace('?' . '><' . '?php', '<!-- php-boundary -->', $html);

        // Ensure the html does not contain {{  }} template string

        return $html;
    }

    /**
     * Build dynamic CSS output with template replacement
     * Replaces {{placeholder}} with PHP echo statements for CSS values
     *
     * @param string $css_code CSS code with placeholders
     * @param array $control_mapping Mapping of control names to keys
     * @param array $tab_structures Tab control structures
     * @return string PHP code that echoes CSS with replaced placeholders
     */
    private function build_dynamic_css_output($css_code, $control_mapping, $tab_structures) {
        // Split CSS by template string patterns to build echo statements
        $pattern = '/\{\{([^}]+)\}\}/';
        $parts = preg_split($pattern, $css_code, -1, PREG_SPLIT_DELIM_CAPTURE);

        $output = "echo \"";

        for ($i = 0; $i < count($parts); $i++) {
            if ($i % 2 === 0) {
                // This is regular CSS content (not a placeholder)
                // Escape for PHP string
                $escaped = str_replace('"', '\\"', $parts[$i]);
                $escaped = str_replace("\n", "\\n", $escaped);
                $output .= $escaped;
            } else {
                // This is a placeholder - close the string and add PHP code
                $placeholder = trim($parts[$i]);
                $var_ref = $this->get_variable_reference($placeholder, $control_mapping, $tab_structures);

                // Check if we got a valid replacement
                if ($var_ref !== '{{' . $placeholder . '}}') {
                    $output .= "\" . esc_attr(" . $var_ref . ") . \"";
                } else {
                    // Placeholder not found, keep as-is
                    $output .= "{{" . $placeholder . "}}";
                }
            }
        }

        $output .= "\";\n";
        return $output;
    }

    /**
     * Build dynamic JS output with template replacement
     * Replaces {{placeholder}} with PHP echo statements for JS values
     *
     * @param string $js_code JavaScript code with placeholders
     * @param array $control_mapping Mapping of control names to keys
     * @param array $tab_structures Tab control structures
     * @return string PHP code that echoes JS with replaced placeholders
     */
    private function build_dynamic_js_output($js_code, $control_mapping, $tab_structures) {
        // Split JS by template string patterns to build echo statements
        $pattern = '/\{\{([^}]+)\}\}/';
        $parts = preg_split($pattern, $js_code, -1, PREG_SPLIT_DELIM_CAPTURE);

        $output = "echo \"";

        for ($i = 0; $i < count($parts); $i++) {
            if ($i % 2 === 0) {
                // This is regular JS content (not a placeholder)
                // Escape for PHP string
                $escaped = str_replace('"', '\\"', $parts[$i]);
                $escaped = str_replace("\n", "\\n", $escaped);
                $output .= $escaped;
            } else {
                // This is a placeholder - close the string and add PHP code
                $placeholder = trim($parts[$i]);
                $var_ref = $this->get_variable_reference($placeholder, $control_mapping, $tab_structures);

                // Check if we got a valid replacement
                if ($var_ref !== '{{' . $placeholder . '}}') {
                    $output .= "\" . esc_js(" . $var_ref . ") . \"";
                } else {
                    // Placeholder not found, keep as-is
                    $output .= "{{" . $placeholder . "}}";
                }
            }
        }

        $output .= "\";\n";
        return $output;
    }

    /**
     * Generate CSS file
     *
     * @return bool|WP_Error
     */
    private function generate_css_file() {
        global $wp_filesystem;
        if (empty($wp_filesystem)) {
            require_once(ABSPATH . '/wp-admin/includes/file.php');
            WP_Filesystem();
        }

        $file_path = $this->widget_dir . '/style.css';

        // Sanitize and validate CSS code
        $content = $this->sanitize_css_code($this->widget_data['css_code']);

        // Add file header comment
        $header = "/**\n * Widget Styles\n * Generated by Master Addons Widget Builder\n * Widget ID: {$this->post_id}\n */\n\n";
        $content = $header . $content;

        $result = $wp_filesystem->put_contents($file_path, $content, FS_CHMOD_FILE);

        if (!$result) {
            return new \WP_Error('css_write_failed', 'Failed to write style.css file');
        }

        return true;
    }

    /**
     * Generate JS file
     *
     * @return bool|WP_Error
     */
    private function generate_js_file() {
        global $wp_filesystem;
        if (empty($wp_filesystem)) {
            require_once(ABSPATH . '/wp-admin/includes/file.php');
            WP_Filesystem();
        }

        $file_path = $this->widget_dir . '/script.js';

        // Sanitize and validate JavaScript code
        $content = $this->sanitize_js_code($this->widget_data['js_code']);

        // Add file header comment
        $header = "/**\n * Widget Scripts\n * Generated by Master Addons Widget Builder\n * Widget ID: {$this->post_id}\n */\n\n";
        $content = $header . $content;

        $result = $wp_filesystem->put_contents($file_path, $content, FS_CHMOD_FILE);

        if (!$result) {
            return new \WP_Error('js_write_failed', 'Failed to write script.js file');
        }

        return true;
    }

    /**
     * Sanitize CSS code
     * Removes potentially dangerous code while preserving valid CSS
     *
     * @param string $css
     * @return string
     */
    private function sanitize_css_code($css) {
        if (empty($css)) {
            return '';
        }

        $css = trim($css);

        // Remove any PHP tags
        $css = preg_replace('/<\?php.*?\?>/s', '', $css);
        $css = preg_replace('/<\?.*?\?>/s', '', $css);

        // Remove any HTML script tags
        $css = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $css);

        // Remove any HTML tags
        $css = preg_replace('/<[^>]*>/', '', $css);

        // Remove any JavaScript event handlers
        $css = preg_replace('/on\w+\s*=\s*["\'].*?["\']/i', '', $css);

        // Remove null bytes
        $css = str_replace(chr(0), '', $css);

        return $css;
    }

    /**
     * Sanitize JavaScript code
     * Basic validation to prevent obvious security issues
     *
     * @param string $js
     * @return string
     */
    private function sanitize_js_code($js) {
        if (empty($js)) {
            return '';
        }

        $js = trim($js);

        // Remove any PHP tags
        $js = preg_replace('/<\?php.*?\?>/s', '', $js);
        $js = preg_replace('/<\?.*?\?>/s', '', $js);

        // Remove null bytes
        $js = str_replace(chr(0), '', $js);

        // Note: We don't strip HTML tags from JS as they might be part of string literals
        // The responsibility is on the admin user to write secure code

        return $js;
    }

    /**
     * Delete widget files
     *
     * @param int $post_id
     * @return bool
     */
    public static function delete_widget_files($post_id) {
        global $wp_filesystem;
        if (empty($wp_filesystem)) {
            require_once(ABSPATH . '/wp-admin/includes/file.php');
            WP_Filesystem();
        }

        $upload = wp_upload_dir();
        $widget_dir = $upload['basedir'] . '/master_addons/widgets/' . $post_id;

        if (file_exists($widget_dir)) {
            return $wp_filesystem->delete($widget_dir, true);
        }

        return true;
    }

    /**
     * Get widget file path
     *
     * @return string
     */
    public function get_widget_file_path() {
        return $this->widget_dir . '/widget.php';
    }

    /**
     * Get widget class name
     *
     * @return string
     */
    public function get_widget_class_name() {
        return $this->widget_class;
    }
}
}