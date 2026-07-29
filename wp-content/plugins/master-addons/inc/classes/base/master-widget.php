<?php

namespace MasterAddons\Inc\Classes\Base;

use \Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

abstract class Master_Widget extends Widget_Base {

    // Whether or not we are in edit mode
    public $_is_edit_mode = false;

    // Dynamic Loop
    private $jltma_loop_dynamic_settings = [];

    /**
     * Widget base constructor.
     *
     * @param  array  $data
     * @param  mixed  $args
     */
    public function __construct( $data = [], $args = null ) {
        parent::__construct( $data, $args );

        // Set edit mode
        $this->_is_edit_mode = \Elementor\Plugin::instance()->editor->is_edit_mode();
    }

    /**
     * Get widget categories.
     *
     * @return array Widget categories.
     */
    public function get_categories() {
        return [ 'master-addons' ];
    }

    /**
     * Whether the widget has dynamic content.
     * Default is false for better editor performance.
     * Override and return true in widgets that need dynamic content (e.g., blog, posts).
     *
     * @return bool
     */
    protected function is_dynamic_content(): bool {
        return false;
    }

    /**
     * Normalize FA4 icon values to FA5 format.
     *
     * Converts legacy Font Awesome 4 icon arrays (e.g. 'fa fa-plus')
     * to Font Awesome 5 format (e.g. 'fas fa-plus') so icons render
     * correctly even when Elementor's "Load Font Awesome 4 Support" is disabled.
     *
     * @param array $icon Icon array with 'value' and 'library' keys.
     * @return array Normalized icon array.
     */
    protected function normalize_fa_icon( $icon ) {
        if ( empty( $icon['value'] ) || ! is_string( $icon['value'] ) ) {
            return $icon;
        }

        // Only convert FA4 single-prefix format: "fa fa-xxx"
        if ( preg_match( '/^fa fa-(.+)$/', $icon['value'], $matches ) ) {
            $icon_name = $matches[1];

            // FA4 outline icons (-o suffix) map to FA5 "regular" style
            if ( substr( $icon_name, -2 ) === '-o' ) {
                $icon['value']   = 'far fa-' . substr( $icon_name, 0, -2 );
                $icon['library'] = 'regular';
            } else {
                $icon['value']   = 'fas fa-' . $icon_name;
                $icon['library'] = 'solid';
            }
        }

        return $icon;
    }

    /**
     * Render a Font Awesome icon with proper CSS enqueue.
     *
     * Normalizes FA4→FA5 format AND ensures the correct Font Awesome CSS
     * is loaded on the frontend. Without this, icons break when Elementor's
     * "Load Font Awesome 4 Support" setting is disabled because Elementor
     * only loads FA CSS globally when that shim is active.
     *
     * @param array  $icon       Icon array with 'value' and 'library' keys.
     * @param array  $attributes HTML attributes for the icon element.
     * @param string $tag        HTML tag to use (default 'i').
     * @return bool Whether the icon was rendered.
     */
    protected function render_icon( $icon, $attributes = [], $tag = 'i' ) {
        $icon = $this->normalize_fa_icon( $icon );

        if ( empty( $icon['value'] ) || empty( $icon['library'] ) ) {
            return false;
        }

        // Ensure the Font Awesome CSS is enqueued for this icon's library.
        // wp_enqueue_style() alone is too late during render (head already printed),
        // so wp_print_styles() forces the <link> tag to print inline in the body.
        // WordPress tracks printed handles and won't duplicate them.
        $fa_css_map = [
            'solid'      => 'elementor-icons-fa-solid',
            'fa-solid'   => 'elementor-icons-fa-solid',
            'regular'    => 'elementor-icons-fa-regular',
            'fa-regular' => 'elementor-icons-fa-regular',
            'brands'     => 'elementor-icons-fa-brands',
            'fa-brands'  => 'elementor-icons-fa-brands',
        ];

        if ( isset( $fa_css_map[ $icon['library'] ] ) && ! $this->_is_edit_mode ) {
            $handle = $fa_css_map[ $icon['library'] ];
            if ( ! wp_style_is( $handle, 'done' ) ) {
                wp_enqueue_style( $handle );
                wp_print_styles( [ $handle ] );
            }
        }

        return \Elementor\Icons_Manager::render_icon( $icon, $attributes, $tag );
    }

    /**
     * Check if widget has inner wrapper.
     * Handles Elementor's optimized markup experiment.
     *
     * @return bool
     */
    public function has_widget_inner_wrapper(): bool {
        return ! \Elementor\Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
    }

    /**
     * Override from addon to add custom wrapper class.
     *
     * @return string
     */
    protected function get_custom_wrapper_class() {
        return '';
    }

    /**
     * Overriding default function to add custom html class.
     *
     * @return string
     */
    public function get_html_wrapper_class() {
        $html_class = parent::get_html_wrapper_class();
        $html_class .= ' jltma-addon';
        $html_class .= ' ' . $this->get_name();
        $html_class .= ' ' . $this->get_custom_wrapper_class();
        return rtrim( $html_class );
    }

    /**
     * Method for adding editor helper attributes
     *
     * @param  string $key
     * @param  string $name
     *
     * @return void
     */
    public function add_helper_render_attribute( $key, $name = '' ) {
        if ( ! $this->_is_edit_mode )
            return;

        $this->add_render_attribute( $key, [
            'data-jltma-helper' => $name,
            'class'             => 'jltma-editor-helper',
        ] );
    }

    /**
     * Method for adding a placeholder for the widget in the preview area
     *
     * @param  array $args
     *
     * @return string|void
     */
    public function render_placeholder( $args ) {
        if ( ! $this->_is_edit_mode )
            return '';

        $defaults = [
            'title_tag' => 'h4',
            'title'     => $this->get_title(),
            'body'      => __( 'This is a placeholder for this widget and will not shown on the page.', 'master-addons' ),
        ];

        $args = wp_parse_args( $args, $defaults );

        $this->add_render_attribute([
            'jltma-placeholder' => [
                'class' => 'jltma-editor-placeholder',
            ],
            'jltma-placeholder-title' => [
                'class' => 'jltma-editor-placeholder__title',
            ],
            'jltma-placeholder-body' => [
                'class' => 'jltma-editor-placeholder__body',
            ],
        ]);

        ob_start();
        ?><div <?php echo $this->get_render_attribute_string( 'jltma-placeholder' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor safe HTML ?>>
            <<?php echo esc_html( $args['title_tag'] ); ?> <?php echo $this->get_render_attribute_string( 'jltma-placeholder-title' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor safe HTML ?>>
                <?php echo esc_html( $args['title'] ); ?>
            </<?php echo esc_html( $args['title_tag'] ); ?>>
            <div <?php echo $this->get_render_attribute_string( 'jltma-placeholder-body' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor safe HTML ?>><?php echo esc_html( $args['body'] ); ?></div>
        </div><?php
        return ob_get_clean();
    }

    /**
     * Add inline editing attributes.
     *
     * @param string $key         Element key.
     * @param string $toolbar     Toolbar type. Accepted values are `advanced`, `basic` or `none`. Default is `basic`.
     * @param string $setting_key Additional settings key in case $key != $setting_key
     */
    public function add_inline_editing_attributes( $key, $toolbar = 'basic', $setting_key = '' ) {
        if ( ! \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
            return;
        }

        if ( empty( $setting_key ) ) {
            $setting_key = $key;
        }

        $this->add_render_attribute( $key, [
            'class'                        => 'elementor-inline-editing',
            'data-elementor-setting-key'   => $setting_key,
        ] );

        if ( 'basic' !== $toolbar ) {
            $this->add_render_attribute( $key, [
                'data-elementor-inline-editing-toolbar' => $toolbar,
            ] );
        }
    }

    /**
     * Add link render attributes with backward compatibility.
     *
     * @param array|string $element     The HTML element.
     * @param array        $url_control Array of link settings.
     * @param bool         $overwrite   Whether to overwrite existing attribute.
     *
     * @return $this
     */
    public function add_link_attributes( $element, array $url_control, $overwrite = false ) {
        // add_link_attributes is available from Elementor 2.8.0
        if ( version_compare( ELEMENTOR_VERSION, '2.8.0', '>=' ) ) {
            return parent::add_link_attributes( $element, $url_control, $overwrite );
        }

        $attributes = [];

        if ( ! empty( $url_control['url'] ) ) {
            $attributes['href'] = $url_control['url'];
        }

        if ( ! empty( $url_control['is_external'] ) ) {
            $attributes['target'] = '_blank';
        }

        if ( ! empty( $url_control['nofollow'] ) ) {
            $attributes['rel'] = 'nofollow';
        }

        if ( ! empty( $url_control['custom_attributes'] ) ) {
            $custom_attributes = explode( ',', $url_control['custom_attributes'] );
            $blacklist = [ 'onclick', 'onfocus', 'onblur', 'onchange', 'onresize', 'onmouseover', 'onmouseout', 'onkeydown', 'onkeyup' ];

            foreach ( $custom_attributes as $attribute ) {
                list( $attr_key, $attr_value ) = explode( '|', $attribute );
                $attr_key = trim( $attr_key );
                $attr_value = trim( $attr_value );

                if ( ! in_array( strtolower( $attr_key ), $blacklist, true ) ) {
                    $attributes[ $attr_key ] = $attr_value;
                }
            }
        }

        if ( $attributes ) {
            $this->add_render_attribute( $element, $attributes, $overwrite );
        }

        return $this;
    }

    /**
     * Method for setting widget dependency on Elementor Pro plugin
     * When returning true it doesn't allow the widget to be registered
     *
     * @return bool
     */
    public static function requires_elementor_pro() {
        return false;
    }

    /**
     * Get skin setting
     *
     * Retrieves the current skin setting
     *
     * @param string $setting_key
     * @return mixed
     */
    protected function get_skin_setting( $setting_key ) {
        if ( ! $setting_key )
            return false;

        return $this->get_current_skin()->get_instance_value( $setting_key );
    }

    /**
     * Set Loop Dynamic Settings
     *
     * @param  \WP_Query $query
     *
     * @return void
     */
    protected function set_settings_for_loop( $query ) {
        global $wp_query;

        // Temporarily force a query for the template and set it as the current query
        $old_query = $wp_query;
        $wp_query  = $query;

        while ( $query->have_posts() ) {
            $query->the_post();
            $this->set_settings_for_post( get_the_ID() );
        }

        // Revert to the initial query
        $wp_query = $old_query;

        wp_reset_postdata();
    }

    /**
     * Set Post Dynamic Settings
     *
     * @param  int $post_id
     *
     * @return void
     */
    protected function set_settings_for_post( $post_id ) {
        if ( ! $post_id ) {
            return;
        }

        $settings     = $this->get_settings_for_display();
        $all_settings = $this->get_settings();
        $controls     = $this->get_controls();

        $this->jltma_loop_dynamic_settings[ $post_id ] = [];

        foreach ( $controls as $control ) {
            $control_name = $control['name'];
            $control_obj  = \Elementor\Plugin::$instance->controls_manager->get_control( $control['type'] );

            if ( empty( $control['dynamic'] ) ) {
                continue;
            }

            $dynamic_settings = array_merge( $control_obj->get_settings( 'dynamic' ), $control['dynamic'] );
            $parsed_value     = '';

            if ( ! isset( $all_settings[ '__dynamic__' ][ $control_name ] ) || empty( $control['dynamic']['loop'] ) ) {
                $parsed_value = $all_settings[ $control_name ];
            } else {
                $parsed_value = $control_obj->parse_tags( $settings[ '__dynamic__' ][ $control_name ], $dynamic_settings );
            }

            $this->jltma_loop_dynamic_settings[ $post_id ][ $control_name ] = $parsed_value;
        }
    }

    /**
     * Get Loop Dynamic Settings
     *
     * @param  int|bool $post_id
     *
     * @return array
     */
    protected function get_settings_for_loop_display( $post_id = false ) {
        if ( $post_id ) {
            if ( array_key_exists( $post_id, $this->jltma_loop_dynamic_settings ) ) {
                return $this->jltma_loop_dynamic_settings[ $post_id ];
            }
        }

        return $this->jltma_loop_dynamic_settings;
    }

    /**
     * Get ID for Loop
     *
     * @return string
     */
    public function get_id_for_loop() {
        global $post;

        if ( ! $post ) {
            return $this->get_id();
        }

        return implode( '_', [ $this->get_id(), $post->ID ] );
    }

}
