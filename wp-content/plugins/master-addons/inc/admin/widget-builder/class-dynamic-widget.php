<?php
namespace MasterAddons\Inc\Admin\WidgetBuilder;

use MasterAddons\Inc\Classes\Base\Master_Widget;

defined('ABSPATH') || exit;

/**
 * Dynamic Widget (runtime renderer)
 *
 * Replaces the legacy "generate a widget.php file and require_once it" approach.
 * One shipped class, instantiated per widget post, that reads the stored schema
 * from post meta and:
 *   - registers Elementor controls at runtime (no generated PHP), and
 *   - renders the HTML/CSS/JS template with escaped {{placeholder}} substitution.
 *
 * No user input is ever written to or executed as PHP.
 *
 * @package MasterAddons
 * @subpackage WidgetBuilder
 */
if (!class_exists('MasterAddons\Inc\Admin\WidgetBuilder\Dynamic_Widget')) {
class Dynamic_Widget extends Master_Widget {

    /** @var int */
    private $jltma_post_id;

    /** @var array */
    private $jltma_data;

    /** @var array Tracks used control keys for deterministic uniqueness (parity with generator). */
    private $jltma_used_keys = [];

    /** @var \MasterAddons\Inc\Admin\WidgetBuilder\Control_Manager */
    private $control_manager;

    /**
     * Elementor instantiates widgets as `new Class($data, $args)`, so the
     * signature MUST match. The widget post id is derived from the registration
     * args (type instance) or from the element's widgetType `jltma_wb_{id}`
     * (rendered element) — never from a positional id argument.
     *
     * @param array      $data Elementor element data.
     * @param array|null $args Elementor element/registration args.
     */
    public function __construct($data = [], $args = null) {
        $this->jltma_post_id = $this->jltma_resolve_post_id($data, $args);
        $this->jltma_load_data();

        require_once __DIR__ . '/class-control-manager.php';
        $this->control_manager = Control_Manager::get_instance();

        // Register external libraries declared in the widget's includes.
        $this->jltma_register_external_libraries();

        parent::__construct($data, $args);
    }

    /**
     * Resolve the widget post id from the registration args (jltma_post_id) or
     * from the element's widgetType name (jltma_wb_{id}).
     */
    private function jltma_resolve_post_id($data, $args) {
        if (is_array($args) && !empty($args['jltma_post_id'])) {
            return absint($args['jltma_post_id']);
        }
        if (is_array($data) && !empty($data['widgetType']) && preg_match('/^jltma_wb_(\d+)$/', $data['widgetType'], $m)) {
            return absint($m[1]);
        }
        return 0;
    }

    /* ------------------------------------------------------------------ *
     * Data loading
     * ------------------------------------------------------------------ */

    private function jltma_load_data() {
        $data = get_post_meta($this->jltma_post_id, '_jltma_widget_data', true);
        if (empty($data) || !is_array($data)) {
            $data = [
                'title'     => get_the_title($this->jltma_post_id),
                'icon'      => 'eicon-code',
                'category'  => get_post_meta($this->jltma_post_id, '_jltma_widget_category', true) ?: 'master-addons',
                'html_code' => '',
                'css_code'  => '',
                'js_code'   => '',
            ];
        }

        $sections = get_post_meta($this->jltma_post_id, '_jltma_widget_sections', true);
        $data['sections'] = (!empty($sections) && is_array($sections)) ? $sections : [];

        $includes = get_post_meta($this->jltma_post_id, '_jltma_widget_includes', true);
        $data['includes'] = (!empty($includes) && is_array($includes))
            ? $includes
            : ['css_libraries' => [], 'js_libraries' => []];

        $this->jltma_data = $data;
    }

    /* ------------------------------------------------------------------ *
     * Widget identity
     * ------------------------------------------------------------------ */

    public function get_name() {
        return 'jltma_wb_' . $this->jltma_post_id;
    }

    public function get_title() {
        $title = !empty($this->jltma_data['title']) ? $this->jltma_data['title'] : 'Custom Widget';
        // translators: dynamic user-defined widget title.
        return esc_html($title);
    }

    public function get_icon() {
        $icon = !empty($this->jltma_data['icon']) ? $this->jltma_data['icon'] : 'eicon-code';
        return sanitize_text_field($icon);
    }

    public function get_categories() {
        $category = !empty($this->jltma_data['category']) ? $this->jltma_data['category'] : 'master-addons';
        return [sanitize_text_field($category)];
    }

    public function get_style_depends() {
        $handles = [];
        foreach (($this->jltma_data['includes']['css_libraries'] ?? []) as $lib) {
            if (!empty($lib['handle'])) {
                $handles[] = sanitize_text_field($lib['handle']);
            }
        }
        return $handles;
    }

    public function get_script_depends() {
        $handles = [];
        foreach (($this->jltma_data['includes']['js_libraries'] ?? []) as $lib) {
            if (!empty($lib['handle'])) {
                $handles[] = sanitize_text_field($lib['handle']);
            }
        }
        return $handles;
    }

    /**
     * Register external CSS/JS libraries declared via the widget's includes.
     * Only URL sources are registered; the widget's own CSS/JS is emitted inline
     * in render() (no files written).
     */
    private function jltma_register_external_libraries() {
        foreach (($this->jltma_data['includes']['css_libraries'] ?? []) as $lib) {
            if (!empty($lib['handle']) && !empty($lib['src']) && filter_var($lib['src'], FILTER_VALIDATE_URL)) {
                $deps = (!empty($lib['dependencies']) && is_array($lib['dependencies'])) ? array_map('sanitize_text_field', $lib['dependencies']) : [];
                wp_register_style(sanitize_text_field($lib['handle']), esc_url_raw($lib['src']), $deps, '1.0.0');
            }
        }
        foreach (($this->jltma_data['includes']['js_libraries'] ?? []) as $lib) {
            if (!empty($lib['handle']) && !empty($lib['src']) && filter_var($lib['src'], FILTER_VALIDATE_URL)) {
                $deps = (!empty($lib['dependencies']) && is_array($lib['dependencies'])) ? array_map('sanitize_text_field', $lib['dependencies']) : [];
                wp_register_script(sanitize_text_field($lib['handle']), esc_url_raw($lib['src']), $deps, '1.0.0', true);
            }
        }
    }

    /* ------------------------------------------------------------------ *
     * Controls (runtime; parity with Widget_Generator::build_register_controls)
     * ------------------------------------------------------------------ */

    protected function register_controls() {
        $this->jltma_used_keys = [];

        if (empty($this->jltma_data['sections']) || !is_array($this->jltma_data['sections'])) {
            return;
        }

        foreach ($this->jltma_sort_sections($this->jltma_data['sections']) as $section_id => $section) {
            if (is_array($section)) {
                $this->jltma_register_section($section_id, $section);
            }
        }
    }

    /** Order: content, style, advanced (matches generator). */
    private function jltma_sort_sections($sections) {
        $content = $style = $advanced = [];
        foreach ($sections as $id => $section) {
            if (!is_array($section)) {
                continue;
            }
            $tab = !empty($section['tab']) ? $section['tab'] : 'content';
            if ('style' === $tab) {
                $style[$id] = $section;
            } elseif ('advanced' === $tab) {
                $advanced[$id] = $section;
            } else {
                $content[$id] = $section;
            }
        }
        return $content + $style + $advanced;
    }

    private function jltma_register_section($section_id, $section) {
        $label = !empty($section['title']) ? $section['title'] : (!empty($section['label']) ? $section['label'] : 'Section');
        $tab   = !empty($section['tab']) ? $section['tab'] : 'content';

        $tab_prefix = 'jltma_content_';
        $tab_const  = \Elementor\Controls_Manager::TAB_CONTENT;
        if ('style' === $tab) {
            $tab_prefix = 'jltma_style_';
            $tab_const  = \Elementor\Controls_Manager::TAB_STYLE;
        } elseif ('advanced' === $tab) {
            $tab_prefix = 'jltma_advanced_';
            $tab_const  = \Elementor\Controls_Manager::TAB_ADVANCED;
        }

        $section_key = $tab_prefix . $this->jltma_sanitize_key($label) . '_' . $section_id . '_' . $this->jltma_post_id;

        $this->start_controls_section($section_key, [
            // translators: dynamic user-defined section label.
            'label' => esc_html($label),
            'tab'   => $tab_const,
        ]);

        $controls = !empty($section['controls']) ? $section['controls'] : (!empty($section['fields']) ? $section['fields'] : []);
        if (!empty($controls) && is_array($controls)) {
            foreach ($controls as $field_id => $field) {
                $this->jltma_register_control($field_id, $field, $tab);
            }
        }

        $this->end_controls_section();
    }

    private function jltma_register_control($field_id, $field, $tab) {
        $type       = !empty($field['type']) ? strtoupper($field['type']) : 'TEXT';
        $tab_prefix = $this->jltma_tab_prefix($tab);

        // TABS: structural container keyed by its own name (matches generator).
        if ('TABS' === $type) {
            $tabs_key = !empty($field['name']) ? $field['name'] : 'tabs_' . $field_id;
            $this->jltma_register_tabs($tabs_key, $field, $tab, $tab_prefix);
            return;
        }

        $label       = !empty($field['label']) ? $field['label'] : 'Control';
        $control_key = $this->jltma_make_control_key($label, $tab_prefix);
        $field       = $this->jltma_inject_context($field, $tab, $tab_prefix, true);

        // POPOVER_TOGGLE: a normal toggle control followed by a popover of child fields.
        if ('POPOVER_TOGGLE' === $type) {
            $this->jltma_apply_control($this->control_manager->build_control_config($control_key, $field, $type));
            if (!empty($field['popover_fields']) && is_array($field['popover_fields'])) {
                $this->jltma_register_popover_fields($control_key, $field, $tab, $tab_prefix);
            }
            return;
        }

        // REPEATER: descriptor carries sub-controls; jltma_apply_control builds the Repeater.
        if ('REPEATER' === $type) {
            $this->jltma_apply_control($this->control_manager->build_control_config($control_key, $field, $type));
            return;
        }

        if ('DATE_TIME' === $type) {
            $field = $this->jltma_preprocess_date_time($field);
        }

        $this->jltma_apply_control($this->control_manager->build_control_config($control_key, $field, $type));
    }

    private function jltma_tab_prefix($tab) {
        if ('style' === $tab) {
            return 'jltma_style_';
        }
        if ('advanced' === $tab) {
            return 'jltma_advanced_';
        }
        return 'jltma_content_';
    }

    /** Deterministic unique control key (matches generator's tab-prefix + slug + counter). */
    private function jltma_make_control_key($label, $tab_prefix) {
        $slug = $this->jltma_sanitize_key($label);
        $key  = $tab_prefix . $slug . '_' . $this->jltma_post_id;
        $c    = 1;
        while (in_array($key, $this->jltma_used_keys, true)) {
            $key = $tab_prefix . $slug . '_' . $c . '_' . $this->jltma_post_id;
            $c++;
        }
        $this->jltma_used_keys[] = $key;
        return $key;
    }

    /** Inject the context the control builders use for condition-key conversion. */
    private function jltma_inject_context($field, $tab, $tab_prefix, $with_sections = true) {
        $field['_tab']        = $tab;
        $field['_widget_id']  = $this->jltma_post_id;
        $field['_tab_prefix'] = $tab_prefix;
        if ($with_sections) {
            $field['_sections_data'] = $this->jltma_data['sections'] ?? [];
        }
        return $field;
    }

    /**
     * Normalize a TABS control descriptor into a list of tabs, each:
     *   ['name' => string, 'label' => string, 'controls' => [ <child field defs> ]].
     * Accepts the processed shape ('tabs' array) or the raw UI shape
     * ('fields' + 'tab_fields'). Used by both registration and context building
     * so the two stay in lockstep.
     */
    private function jltma_extract_tabs($field) {
        if (!empty($field['tabs']) && is_array($field['tabs'])) {
            return $field['tabs'];
        }
        $tabs = [];
        if (!empty($field['fields']) && is_array($field['fields'])) {
            $tab_fields = (!empty($field['tab_fields']) && is_array($field['tab_fields'])) ? $field['tab_fields'] : [];
            foreach ($field['fields'] as $td) {
                if (empty($td['name'])) {
                    continue;
                }
                $tn     = $td['name'];
                $tabs[] = [
                    'name'     => $tn,
                    'label'    => !empty($td['label']) ? $td['label'] : ucfirst($tn),
                    'controls' => (!empty($tab_fields[$tn]) && is_array($tab_fields[$tn])) ? $tab_fields[$tn] : [],
                ];
            }
        }
        return $tabs;
    }

    /** Register a TABS structural control (ports Tabs::build to runtime calls). */
    private function jltma_register_tabs($tabs_key, $field, $tab, $tab_prefix) {
        $tabs = $this->jltma_extract_tabs($field);

        if (empty($tabs)) {
            return;
        }

        $this->start_controls_tabs($tabs_key);

        foreach ($tabs as $tab_index => $tabdef) {
            if (empty($tabdef['name']) || empty($tabdef['label'])) {
                continue;
            }
            $tab_key   = $tabs_key . '_tab_' . $tabdef['name'];
            $tab_label = !empty($tabdef['label']) ? $tabdef['label'] : 'Tab ' . ($tab_index + 1);

            $tab_args = [
                // translators: dynamic user-defined tab label.
                'label' => esc_html($tab_label),
            ];
            if (!empty($tabdef['condition']) && is_array($tabdef['condition'])) {
                $tab_args['condition'] = $tabdef['condition'];
            }

            $this->start_controls_tab($tab_key, $tab_args);

            if (!empty($tabdef['controls']) && is_array($tabdef['controls'])) {
                foreach ($tabdef['controls'] as $child) {
                    $this->jltma_register_child_control($child, $tab, $tab_prefix);
                }
            }

            $this->end_controls_tab();
        }

        $this->end_controls_tabs();
    }

    /** Register a control nested inside a tab (no section context, no date_time preprocess; matches generator). */
    private function jltma_register_child_control($child, $tab, $tab_prefix) {
        if (empty($child['type']) || empty($child['name'])) {
            return;
        }
        $label = !empty($child['label']) ? $child['label'] : $child['name'];
        $key   = $this->jltma_make_control_key($label, $tab_prefix);
        $child = $this->jltma_inject_context($child, $tab, $tab_prefix, false);
        $this->jltma_apply_control($this->control_manager->build_control_config($key, $child, strtoupper($child['type'])));
    }

    /** Register popover child fields (start_popover / children / end_popover). */
    private function jltma_register_popover_fields($control_key, $field, $tab, $tab_prefix) {
        $this->start_popover();
        foreach ($field['popover_fields'] as $pf) {
            if (empty($pf['name']) || empty($pf['type'])) {
                continue;
            }
            $pf_key = $control_key . '_' . $this->jltma_sanitize_key($pf['name']);
            $pf     = $this->jltma_inject_context($pf, $tab, $tab_prefix, false);
            if (empty($pf['label'])) {
                $pf['label'] = ucfirst($pf['name']);
            }
            $this->jltma_apply_control($this->control_manager->build_control_config($pf_key, $pf, strtoupper($pf['type'])));
        }
        $this->end_popover();
    }


    /**
     * Apply a control descriptor returned by Control_Manager::build_control_config().
     * Descriptor: ['key' => string, 'responsive' => bool, 'args' => array,
     *              optional 'method' => 'add_group_control', 'group_type' => string].
     */
    private function jltma_apply_control($descriptor) {
        if (empty($descriptor) || empty($descriptor['key']) || !isset($descriptor['args'])) {
            return;
        }

        // Group controls (pro) signal a different registration method.
        if (!empty($descriptor['method']) && 'add_group_control' === $descriptor['method'] && !empty($descriptor['group_type'])) {
            $this->add_group_control($descriptor['group_type'], $descriptor['args']);
            return;
        }

        // Repeater: build an \Elementor\Repeater, add its sub-controls, then register.
        if (!empty($descriptor['method']) && 'repeater' === $descriptor['method']) {
            $repeater = new \Elementor\Repeater();
            foreach (($descriptor['sub_controls'] ?? []) as $sub) {
                if (!empty($sub['name'])) {
                    $repeater->add_control($sub['name'], $sub['args']);
                }
            }
            $args           = $descriptor['args'];
            $args['fields'] = $repeater->get_controls();
            $this->add_control($descriptor['key'], $args);
            return;
        }

        $method = !empty($descriptor['responsive']) ? 'add_responsive_control' : 'add_control';
        $this->{$method}($descriptor['key'], $descriptor['args']);
    }

    private function jltma_preprocess_date_time($field) {
        $picker = [];
        $enable_time = isset($field['enable_time']) ? (bool) $field['enable_time'] : false;
        if (isset($field['enable_time'])) {
            $picker['enableTime'] = $enable_time;
        }
        $picker['dateFormat'] = $enable_time ? 'Y-m-d H:i' : 'Y-m-d';
        $picker['time_24hr']  = true;
        if (!empty($field['minute_increment'])) {
            $picker['minuteIncrement'] = intval($field['minute_increment']);
        }
        if (!empty($field['picker_options']) && is_array($field['picker_options'])) {
            $picker = array_merge($field['picker_options'], $picker);
        }
        if (!empty($picker)) {
            $field['picker_options'] = $picker;
        }
        return $field;
    }

    /** Matches Control_Base::sanitize_key()/generator (spaces -> underscores). */
    private function jltma_sanitize_key($label) {
        $key = strtolower($label);
        $key = str_replace(' ', '_', $key);
        $key = preg_replace('/[^a-z0-9_]/', '', $key);
        $key = preg_replace('/_+/', '_', $key);
        return trim($key, '_');
    }

    /* ------------------------------------------------------------------ *
     * Render (runtime; parity with Widget_Generator::build_render value output)
     * ------------------------------------------------------------------ */

    protected function render() {
        $settings = $this->get_settings_for_display();
        if (!is_array($settings)) {
            $settings = [];
        }
        $mapping = $this->jltma_build_control_mapping();
        $context = $this->jltma_build_context($settings, $mapping);

        // TABS values: nested so templates read {{ <tabs>.<tab>.<field> }}
        // e.g. {{ tabs.tab_1.person }}. Overwrites the scalar placeholder the
        // TABS control name would otherwise hold (a structural control has no value).
        foreach ($this->jltma_build_tabs_data($settings) as $tabs_key => $tabs_value) {
            $context[$tabs_key] = $tabs_value;
        }

        $html = isset($this->jltma_data['html_code']) ? (string) $this->jltma_data['html_code'] : '';
        $css  = isset($this->jltma_data['css_code']) ? (string) $this->jltma_data['css_code'] : '';
        $js   = isset($this->jltma_data['js_code']) ? (string) $this->jltma_data['js_code'] : '';

        // Emitting custom CSS/JS is a premium-only capability. The free build
        // outputs the HTML body only; the Pro build returns the <style>/<script>
        // markup via these filters — see MasterAddons\Pro\Classes\Pro_Modules.
        // Values are pre-rendered here (placeholder substitution, escaped per
        // output) but the wrapping markup is emitted only by Pro, so no inline
        // CSS/JS ships in the free plugin.
        $css_rendered = ('' !== trim($css)) ? $this->jltma_render_template($css, $context) : '';
        $css_output   = apply_filters('master_addons/widget_builder/render_css', '', $css_rendered, $context);
        if ('' !== $css_output) {
            echo $css_output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- premium-rendered markup; value substitution escaped per-output
        }

        // HTML body.
        echo $this->jltma_render_template($html, $context); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- value substitution escaped per-output; HTML body is plugin-sanitized data

        // Inline JS.
        $js_rendered = ('' !== trim($js)) ? $this->jltma_render_template($js, $context) : '';
        $js_output   = apply_filters('master_addons/widget_builder/render_js', '', $js_rendered, $context);
        if ('' !== $js_output) {
            echo $js_output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- premium-rendered markup; value substitution escaped per-output
        }
    }

    /** Build the template variable context: control name => resolved value. */
    private function jltma_build_context($settings, $mapping) {
        $context = [];
        foreach ($mapping as $name => $key) {
            $context[$name] = array_key_exists($key, $settings) ? $settings[$key] : '';
        }
        return $context;
    }

    /** placeholder (control name) => full control key. Mirrors generator. */
    private function jltma_build_control_mapping() {
        $mapping = [];
        if (empty($this->jltma_data['sections']) || !is_array($this->jltma_data['sections'])) {
            return $mapping;
        }
        $tab_prefix_map = ['content' => 'jltma_content_', 'style' => 'jltma_style_', 'advanced' => 'jltma_advanced_'];

        foreach ($this->jltma_data['sections'] as $section) {
            if (!is_array($section)) {
                continue;
            }
            $tab        = !empty($section['tab']) ? $section['tab'] : 'content';
            $tab_prefix = $tab_prefix_map[$tab] ?? 'jltma_content_';
            $controls   = !empty($section['controls']) ? $section['controls'] : (!empty($section['fields']) ? $section['fields'] : []);

            foreach ($controls as $control) {
                if (empty($control['name'])) {
                    continue;
                }
                $control_key = $tab_prefix . $this->jltma_sanitize_key($control['label'] ?? $control['name']) . '_' . $this->jltma_post_id;
                $mapping[$control['name']] = $control_key;

                if (!empty($control['type']) && 'POPOVER_TOGGLE' === strtoupper($control['type']) && !empty($control['popover_fields']) && is_array($control['popover_fields'])) {
                    foreach ($control['popover_fields'] as $pf) {
                        if (empty($pf['name'])) {
                            continue;
                        }
                        $mapping[$control['name'] . '_' . $pf['name']] = $control_key . '_' . $this->jltma_sanitize_key($pf['name']);
                    }
                }
            }
        }
        return $mapping;
    }

    /**
     * Build the nested TABS context, keyed by tabs-control name:
     *   [ <tabs_name> => [ <tab_name> => [ <field_name> => value ] ] ]
     * Templates read a single value with {{ <tabs_name>.<tab_name>.<field_name> }}
     * (e.g. {{ tabs.tab_1.person }}) — no looping required.
     *
     * Tab-child control keys are counter-deduped at registration time
     * (jltma_make_control_key), so two tabs that reuse a field name — e.g. the
     * default "field" — resolve to ..._field_ and ..._field_1_. We replay that
     * exact counter, walking controls in register_controls() order, so the keys
     * we read from $settings match the ones that were registered.
     */
    private function jltma_build_tabs_data($settings) {
        $data = [];
        if (empty($this->jltma_data['sections']) || !is_array($this->jltma_data['sections'])) {
            return $data;
        }

        // Mirror of jltma_make_control_key()'s dedup, with its own counter state.
        $used     = [];
        $make_key = function ($label, $tab_prefix) use (&$used) {
            $slug = $this->jltma_sanitize_key($label);
            $key  = $tab_prefix . $slug . '_' . $this->jltma_post_id;
            $c    = 1;
            while (in_array($key, $used, true)) {
                $key = $tab_prefix . $slug . '_' . $c . '_' . $this->jltma_post_id;
                $c++;
            }
            $used[] = $key;
            return $key;
        };

        foreach ($this->jltma_sort_sections($this->jltma_data['sections']) as $section) {
            if (!is_array($section)) {
                continue;
            }
            $tab        = !empty($section['tab']) ? $section['tab'] : 'content';
            $tab_prefix = $this->jltma_tab_prefix($tab);
            $controls   = !empty($section['controls']) ? $section['controls'] : (!empty($section['fields']) ? $section['fields'] : []);

            foreach ($controls as $field_id => $control) {
                if (empty($control['type'])) {
                    continue;
                }
                $type = strtoupper($control['type']);

                if ('TABS' === $type) {
                    $tabs_key = !empty($control['name']) ? $control['name'] : 'tabs_' . $field_id;
                    $entry    = [];
                    foreach ($this->jltma_extract_tabs($control) as $tabdef) {
                        // Same skip as jltma_register_tabs(): unregistered tabs
                        // must not advance the shared dedup counter.
                        if (empty($tabdef['name']) || empty($tabdef['label'])) {
                            continue;
                        }
                        $fields = [];
                        if (!empty($tabdef['controls']) && is_array($tabdef['controls'])) {
                            foreach ($tabdef['controls'] as $child) {
                                if (empty($child['name']) || empty($child['type'])) {
                                    continue;
                                }
                                // Mirrors jltma_register_child_control()'s label fallback + key.
                                $child_label             = !empty($child['label']) ? $child['label'] : $child['name'];
                                $child_key               = $make_key($child_label, $tab_prefix);
                                $fields[$child['name']]  = array_key_exists($child_key, $settings) ? $settings[$child_key] : '';
                            }
                        }
                        $entry[$tabdef['name']] = $fields;
                    }
                    $data[$tabs_key] = $entry;
                    continue;
                }

                // Non-tab control: advance the counter to mirror registration order
                // (jltma_register_control() uses a 'Control' label fallback).
                $label = !empty($control['label']) ? $control['label'] : 'Control';
                $make_key($label, $tab_prefix);
            }
        }
        return $data;
    }

    /**
     * Resolve a tab-child field type from a dotted path (<tabs>.<tab>.<field>),
     * so {{ tabs.tab_1.person }} escapes per the child's own control type.
     * Returns null when the path is not a known tab-child.
     */
    private function jltma_tab_field_type($path) {
        $parts = explode('.', $path);
        if (count($parts) < 3) {
            return null;
        }
        list($tabs_name, $tab_name, $field_name) = [$parts[0], $parts[1], $parts[2]];
        if (empty($this->jltma_data['sections']) || !is_array($this->jltma_data['sections'])) {
            return null;
        }
        foreach ($this->jltma_data['sections'] as $section) {
            if (!is_array($section)) {
                continue;
            }
            $controls = !empty($section['controls']) ? $section['controls'] : (!empty($section['fields']) ? $section['fields'] : []);
            foreach ($controls as $control) {
                if (empty($control['type']) || 'TABS' !== strtoupper($control['type']) || ($control['name'] ?? '') !== $tabs_name) {
                    continue;
                }
                foreach ($this->jltma_extract_tabs($control) as $tabdef) {
                    if (($tabdef['name'] ?? '') !== $tab_name) {
                        continue;
                    }
                    foreach (($tabdef['controls'] ?? []) as $child) {
                        if (($child['name'] ?? '') === $field_name) {
                            return $child['type'] ?? 'text';
                        }
                    }
                }
            }
        }
        return null;
    }

    private function jltma_control_type($control_name) {
        // Tab-child path (<tabs>.<tab>.<field>) resolves to the child's own type.
        if (false !== strpos($control_name, '.')) {
            $tab_type = $this->jltma_tab_field_type($control_name);
            if (null !== $tab_type) {
                return $tab_type;
            }
            $control_name = explode('.', $control_name)[0];
        }
        if (empty($this->jltma_data['sections']) || !is_array($this->jltma_data['sections'])) {
            return 'text';
        }
        foreach ($this->jltma_data['sections'] as $section) {
            if (!is_array($section)) {
                continue;
            }
            $controls = !empty($section['controls']) ? $section['controls'] : (!empty($section['fields']) ? $section['fields'] : []);
            foreach ($controls as $control) {
                if (!empty($control['name']) && $control['name'] === $control_name) {
                    return $control['type'] ?? 'text';
                }
                if (!empty($control['type']) && 'POPOVER_TOGGLE' === strtoupper($control['type']) && !empty($control['popover_fields'])) {
                    foreach ($control['popover_fields'] as $pf) {
                        if (!empty($pf['name']) && $control_name === $control['name'] . '_' . $pf['name']) {
                            return $pf['type'] ?? 'text';
                        }
                    }
                }
            }
        }
        return 'text';
    }

    /* ------------------------------------------------------------------ *
     * Twig-syntax template engine (safe subset; no eval, no compiled PHP).
     * Supports:  {{ var }}  {{ var.prop }}  {{ var|raw }}  {{ var|upper }}
     *            {% if expr %} {% elseif expr %} {% else %} {% endif %}
     *            {% for item in list %} ... {% endfor %}
     * Conditions:  ==  !=  >  <  >=  <=   and  or  not   plus bare truthiness.
     * All output is escaped per control type unless the |raw filter is used.
     * ------------------------------------------------------------------ */

    /** Render a template string against the variable context. */
    private function jltma_render_template($template, $context) {
        $template = (string) $template;
        if ('' === $template) {
            return '';
        }
        $tokens = $this->jltma_tokenize_template($template);
        $pos    = 0;
        $ast    = $this->jltma_parse_template($tokens, $pos, []);
        return $this->jltma_eval_nodes($ast, $context);
    }

    /** Split a template into text / {{ output }} / {% tag %} tokens. */
    private function jltma_tokenize_template($template) {
        $parts  = preg_split('/(\{%.*?%\}|\{\{.*?\}\})/s', $template, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $tokens = [];
        foreach ($parts as $part) {
            if (preg_match('/^\{%\s*(.*?)\s*%\}$/s', $part, $m)) {
                $inner   = trim($m[1]);
                $space   = strpos($inner, ' ');
                $keyword = (false === $space) ? $inner : substr($inner, 0, $space);
                $expr    = (false === $space) ? '' : trim(substr($inner, $space + 1));
                $tokens[] = ['type' => 'tag', 'kw' => $keyword, 'expr' => $expr];
            } elseif (preg_match('/^\{\{\s*(.*?)\s*\}\}$/s', $part, $m)) {
                $tokens[] = ['type' => 'out', 'expr' => trim($m[1])];
            } else {
                $tokens[] = ['type' => 'text', 'value' => $part];
            }
        }
        return $tokens;
    }

    /** Recursive-descent parse into an AST. Stops (without consuming) on a $stops keyword. */
    private function jltma_parse_template($tokens, &$pos, $stops) {
        $nodes = [];
        $count = count($tokens);
        while ($pos < $count) {
            $tok = $tokens[$pos];
            if ('text' === $tok['type']) {
                $nodes[] = ['text', $tok['value']];
                $pos++;
                continue;
            }
            if ('out' === $tok['type']) {
                $nodes[] = ['out', $tok['expr']];
                $pos++;
                continue;
            }
            // tag
            $kw = $tok['kw'];
            if (in_array($kw, $stops, true)) {
                return $nodes; // leave $pos on the stop tag for the caller
            }
            if ('if' === $kw) {
                $pos++;
                $branches = [];
                $cond     = $tok['expr'];
                while (true) {
                    $body       = $this->jltma_parse_template($tokens, $pos, ['elseif', 'else', 'endif']);
                    $branches[] = [$cond, $body];
                    if ($pos >= $count) {
                        break;
                    }
                    $next = $tokens[$pos];
                    if ('endif' === $next['kw']) {
                        $pos++;
                        break;
                    }
                    if ('elseif' === $next['kw']) {
                        $cond = $next['expr'];
                        $pos++;
                        continue;
                    }
                    if ('else' === $next['kw']) {
                        $cond = '__else__';
                        $pos++;
                        continue;
                    }
                    break;
                }
                $nodes[] = ['if', $branches];
                continue;
            }
            if ('for' === $kw) {
                $pos++;
                $body = $this->jltma_parse_template($tokens, $pos, ['endfor']);
                if ($pos < $count && 'endfor' === $tokens[$pos]['kw']) {
                    $pos++;
                }
                $nodes[] = ['for', $tok['expr'], $body];
                continue;
            }
            // stray close/else with no opener -> skip
            $pos++;
        }
        return $nodes;
    }

    /** Evaluate an AST node list to a string. */
    private function jltma_eval_nodes($nodes, $context) {
        $out = '';
        foreach ($nodes as $node) {
            switch ($node[0]) {
                case 'text':
                    $out .= $node[1];
                    break;
                case 'out':
                    $out .= $this->jltma_render_output($node[1], $context);
                    break;
                case 'if':
                    foreach ($node[1] as $branch) {
                        if ('__else__' === $branch[0] || $this->jltma_eval_condition($branch[0], $context)) {
                            $out .= $this->jltma_eval_nodes($branch[1], $context);
                            break;
                        }
                    }
                    break;
                case 'for':
                    if (preg_match('/^(\w+)\s+in\s+(.+)$/s', trim($node[1]), $m)) {
                        $list = $this->jltma_resolve_value(trim($m[2]), $context);
                        if (is_array($list)) {
                            foreach ($list as $row) {
                                $scope        = $context;
                                $scope[$m[1]] = $row;
                                $out         .= $this->jltma_eval_nodes($node[2], $scope);
                            }
                        }
                    }
                    break;
            }
        }
        return $out;
    }

    /** Resolve an expression to its raw value: literal, number, bool, or dotted var path. */
    private function jltma_resolve_value($expr, $context) {
        $expr = trim($expr);
        if ('' === $expr) {
            return null;
        }
        $first = $expr[0];
        $last  = substr($expr, -1);
        if (('"' === $first && '"' === $last) || ("'" === $first && "'" === $last)) {
            return substr($expr, 1, -1);
        }
        if (is_numeric($expr)) {
            return $expr + 0;
        }
        if ('true' === $expr) {
            return true;
        }
        if ('false' === $expr) {
            return false;
        }
        if ('null' === $expr) {
            return null;
        }
        $value = $context;
        foreach (explode('.', $expr) as $part) {
            if (is_array($value) && array_key_exists($part, $value)) {
                $value = $value[$part];
            } else {
                return null;
            }
        }
        return $value;
    }

    /** Evaluate a boolean condition (or / and / not / comparison / truthiness). */
    private function jltma_eval_condition($expr, $context) {
        $expr = trim($expr);
        if ('__else__' === $expr || 'true' === $expr) {
            return true;
        }
        if ('' === $expr || 'false' === $expr) {
            return false;
        }
        // or (lowest precedence)
        $parts = preg_split('/\s+or\s+/', $expr);
        if (count($parts) > 1) {
            foreach ($parts as $part) {
                if ($this->jltma_eval_condition($part, $context)) {
                    return true;
                }
            }
            return false;
        }
        // and
        $parts = preg_split('/\s+and\s+/', $expr);
        if (count($parts) > 1) {
            foreach ($parts as $part) {
                if (!$this->jltma_eval_condition($part, $context)) {
                    return false;
                }
            }
            return true;
        }
        // not
        if (preg_match('/^not\s+(.+)$/s', $expr, $m)) {
            return !$this->jltma_eval_condition($m[1], $context);
        }
        // comparison (longest operators tried first via alternation order)
        if (preg_match('/^(.+?)\s*(==|!=|>=|<=|>|<)\s*(.+)$/s', $expr, $m)) {
            return $this->jltma_compare(
                $this->jltma_resolve_value($m[1], $context),
                $this->jltma_resolve_value($m[3], $context),
                $m[2]
            );
        }
        // bare truthiness
        return $this->jltma_truthy($this->jltma_resolve_value($expr, $context));
    }

    /** Compare two resolved values; numeric when both numeric, else string. */
    private function jltma_compare($a, $b, $op) {
        if (is_numeric($a) && is_numeric($b)) {
            $a += 0;
            $b += 0;
        } else {
            $a = (string) $a;
            $b = (string) $b;
        }
        switch ($op) {
            case '==':
                return $a == $b; // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison -- template equality is intentionally loose
            case '!=':
                return $a != $b; // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison -- template inequality is intentionally loose
            case '>':
                return $a > $b;
            case '<':
                return $a < $b;
            case '>=':
                return $a >= $b;
            case '<=':
                return $a <= $b;
        }
        return false;
    }

    /** Twig/Handlebars truthiness: '', '0', 0, null, false, [] are falsy. */
    private function jltma_truthy($value) {
        if (null === $value || false === $value) {
            return false;
        }
        if (is_array($value)) {
            return !empty($value);
        }
        $string = (string) $value;
        return '' !== $string && '0' !== $string;
    }

    /** Render a {{ output }} expression: resolve, apply filters, escape per type. */
    private function jltma_render_output($expr, $context) {
        $segments = array_map('trim', explode('|', trim($expr)));
        $base     = array_shift($segments);
        $value    = $this->jltma_resolve_value($base, $context);

        if (is_array($value)) {
            $value = isset($value['url']) ? $value['url'] : '';
        }
        $value = (string) $value;

        $raw = false;
        foreach ($segments as $filter) {
            switch ($filter) {
                case 'raw':
                    $raw = true;
                    break;
                case 'e':
                case 'escape':
                    $raw = false;
                    break;
                case 'upper':
                    $value = strtoupper($value);
                    break;
                case 'lower':
                    $value = strtolower($value);
                    break;
                case 'trim':
                    $value = trim($value);
                    break;
            }
        }
        if ($raw) {
            return $value;
        }
        $type = strtolower($this->jltma_control_type($base));
        return $this->jltma_escape_value($value, $type);
    }

    private function jltma_escape_value($value, $type) {
        switch ($type) {
            case 'wysiwyg':
            case 'code':
                return wp_kses_post($value);
            case 'url':
                return esc_url($value);
            default:
                return esc_html($value);
        }
    }
}
}
