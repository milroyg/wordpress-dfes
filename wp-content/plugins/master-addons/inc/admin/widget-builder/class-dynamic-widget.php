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

    /** @var Widget_Template_Engine|null Built lazily on first render. */
    private $jltma_template_engine = null;

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
        
        // Fallback to default values if no data is found or if the data is not an array.
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

        // Premium-gated and normalised; free builds resolve to an empty set.
        $data['includes'] = Widget_Builder_Init::get_widget_includes($this->jltma_post_id);

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
        return wp_list_pluck($this->jltma_data['includes']['css_libraries'], 'handle');
    }

    public function get_script_depends() {
        return wp_list_pluck($this->jltma_data['includes']['js_libraries'], 'handle');
    }

    /**
     * Opt out of Elementor's static element cache.
     *
     * A cached element is stored as finished HTML and its render() is never run
     * again, which would silently drop the custom JS this widget queues for the
     * footer. Reporting the widget as dynamic makes Elementor cache it as a
     * re-rendered [elementor-element] placeholder instead, so render() runs on
     * every request — the same treatment core gives Counter, Tabs and the other
     * stateful widgets.
     *
     * Frontend only. Document::print_elements() turns the placeholder path on
     * for the editor and preview too, but nothing expands the shortcode there,
     * so the widget would render as an empty canvas while editing.
     *
     * @return bool
     */
    protected function is_dynamic_content(): bool {
        return !Widget_Builder_Init::is_editor_context();
    }

    /**
     * Register the external CSS/JS libraries declared via the widget's includes.
     *
     * The list is already premium-gated, normalised and URL-validated by
     * Widget_Builder_Init::get_widget_includes(), so free builds register
     * nothing here. The widget's own CSS/JS is emitted by render(), not from a
     * file.
     */
    private function jltma_register_external_libraries() {
        foreach ($this->jltma_data['includes']['css_libraries'] as $lib) {
            wp_register_style($lib['handle'], $lib['src'], $lib['dependencies'], '1.0.0');
        }
        foreach ($this->jltma_data['includes']['js_libraries'] as $lib) {
            wp_register_script($lib['handle'], $lib['src'], $lib['dependencies'], '1.0.0', true);
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
        $locked     = $this->control_manager->is_locked_type($type);

        // Locked TABS: the container itself holds no value, but its children do,
        // and they consume control keys from the same counter. Walk them in the
        // same order so every later control keeps the key its value is stored
        // under — just hidden instead of editable.
        if ($locked && 'TABS' === $type) {
            $this->jltma_register_locked_tabs($field_id, $field, $tab, $tab_prefix);
            return;
        }

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
            // Locked: no popover to open, so the children are registered hidden
            // (keys are derived from the toggle's key, not the shared counter).
            if ($locked) {
                $this->jltma_apply_control($this->control_manager->build_locked_control_config($control_key, $field, $type));
                $this->jltma_register_locked_popover_fields($control_key, $field);
                return;
            }
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

    /**
     * Register a locked TABS control: one notice, then every child as a hidden
     * control, walked in the same order jltma_register_tabs() would use so the
     * shared key counter advances identically.
     */
    private function jltma_register_locked_tabs($field_id, $field, $tab, $tab_prefix) {
        $notice_key = $tab_prefix . 'jltma_pro_locked_' . $this->jltma_sanitize_key((string) $field_id) . '_' . $this->jltma_post_id;
        $descriptor = $this->control_manager->build_locked_control_config($notice_key, $field, 'TABS');

        if (!empty($descriptor['notice'])) {
            $this->add_control($descriptor['notice']['key'], $descriptor['notice']['args']);
        }

        foreach ($this->jltma_extract_tabs($field) as $tab_def) {
            foreach (($tab_def['controls'] ?? []) as $child) {
                if (empty($child['type']) || empty($child['name'])) {
                    continue;
                }
                $label = !empty($child['label']) ? $child['label'] : $child['name'];
                $key   = $this->jltma_make_control_key($label, $tab_prefix);
                $this->jltma_apply_control(
                    $this->control_manager->build_locked_control_config($key, $child, strtoupper($child['type']), false)
                );
            }
        }
    }

    /** Register a locked popover's child fields as hidden value carriers. */
    private function jltma_register_locked_popover_fields($control_key, $field) {
        if (empty($field['popover_fields']) || !is_array($field['popover_fields'])) {
            return;
        }

        foreach ($field['popover_fields'] as $pf) {
            if (empty($pf['name']) || empty($pf['type'])) {
                continue;
            }
            $pf_key = $control_key . '_' . $this->jltma_sanitize_key($pf['name']);
            $this->jltma_apply_control(
                $this->control_manager->build_locked_control_config($pf_key, $pf, strtoupper($pf['type']), false)
            );
        }
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

        // Locked premium control: the "(Pro)" lock badge, then a hidden control
        // that keeps the stored value flowing to render().
        if (!empty($descriptor['method']) && 'locked' === $descriptor['method']) {
            if (!empty($descriptor['notice'])) {
                $notice = $descriptor['notice'];
                $method = !empty($notice['responsive']) ? 'add_responsive_control' : 'add_control';
                $this->{$method}($notice['key'], $notice['args']);
            }
            $this->add_control($descriptor['key'], $descriptor['args']);
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

        // Inline CSS. The stored body is PHP-/tag-stripped on save; placeholder
        // substitution is escaped per output by the template renderer, so only
        // the <style> wrapper is added here.
        $css_rendered = ('' !== trim($css)) ? $this->jltma_render_template($css, $context) : '';
        if ('' !== trim($css_rendered)) {
            echo '<style>' . $css_rendered . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- plugin-sanitized CSS body; value substitution escaped per-output
        }

        // HTML body.
        echo $this->jltma_render_template($html, $context); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- value substitution escaped per-output; HTML body is plugin-sanitized data

        // Custom JS. Raw JS is only ever persisted for users with unfiltered_html
        // — see REST_Controller::save_widget_data().
        //
        // On the frontend it is queued for the footer rather than echoed here: an
        // inline <script> in this position sits inside the_content, where core's
        // convert_chars() rewrites every `&` to `&#038;` (it does not skip script
        // blocks), which breaks any `&&` on parse.
        //
        // The editor canvas needs the opposite: it swaps a widget's markup on
        // every edit without reloading the document, so a footer script would run
        // once and never again — leaving anything the script reveals invisible.
        // Editor output is not run through the_content, so inline is safe there.
        $js_rendered = ('' !== trim($js)) ? $this->jltma_render_template($js, $context) : '';
        if ('' !== trim($js_rendered)) {
            if (Widget_Builder_Init::is_editor_context()) {
                echo '<script>' . $js_rendered . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- plugin-sanitized JS body; value substitution escaped per-output
            } else {
                Widget_Builder_Init::enqueue_inline_js($js_rendered, $this->jltma_post_id);
            }
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
     * Template rendering — delegated to Widget_Template_Engine so the editor
     * preview and the front end run the same Twig-subset engine.
     * ------------------------------------------------------------------ */

    /** Render a template string against the variable context. */
    private function jltma_render_template($template, $context) {
        if (null === $this->jltma_template_engine) {
            $this->jltma_template_engine = new Widget_Template_Engine(
                $this->jltma_collect_control_types()
            );
        }

        return $this->jltma_template_engine->render($template, $context);
    }

    /** Map every control name to its type, for the engine's output escaping. */
    private function jltma_collect_control_types() {
        $types = [];

        if (empty($this->jltma_data['sections']) || !is_array($this->jltma_data['sections'])) {
            return $types;
        }

        foreach ($this->jltma_data['sections'] as $section) {
            if (!is_array($section)) {
                continue;
            }

            $controls = !empty($section['controls'])
                ? $section['controls']
                : (!empty($section['fields']) ? $section['fields'] : []);

            foreach ((array) $controls as $control) {
                if (empty($control['name'])) {
                    continue;
                }

                $types[$control['name']] = $control['type'] ?? 'text';

                if (!empty($control['popover_fields']) && is_array($control['popover_fields'])) {
                    foreach ($control['popover_fields'] as $pf) {
                        if (!empty($pf['name'])) {
                            $types[$pf['name']] = $pf['type'] ?? 'text';
                        }
                    }
                }
            }
        }

        return $types;
    }
}
}
