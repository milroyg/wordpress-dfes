<?php

namespace MasterAddons\Inc\Classes\Base;
use \Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

abstract class Master_Widget extends Widget_Base {

    // Wether or not we are in edit mode
    public $_is_edit_mode = false;

    // Dynamic Loop
    private $jltma_loop_dynamic_settings = [];

    public function get_categories() {
		return [ 'master-addons' ];
	}

    /**
     * Widget base constructor.
     *
     * @param  array  $data
     * @param  [type] $args
     */
	public function __construct( $data = [], $args = null ) {

		parent::__construct( $data, $args );

		// Set edit mode
		$this->_is_edit_mode = \Elementor\Plugin::instance()->editor->is_edit_mode();
	}

    /**
     * Method for adding editor helper attributes
     *
     * @param  [type] $key
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
     * @param  [type] $args
     *
     * @return void
     */
    public function render_placeholder( $args ) {

		if ( ! $this->_is_edit_mode )
			return;

		$defaults = [
			'title_tag' => 'h4',
			'title' => $this->get_title(),
			'body' 	=> __( 'This is a placeholder for this widget and will not shown on the page.', 'master-addons' ),
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

		?><div <?php echo $this->get_render_attribute_string( 'jltma-placeholder' ); ?>>
			<<?php echo $args['title_tag']; ?> <?php echo $this->get_render_attribute_string( 'jltma-placeholder-title' ); ?>>
				<?php echo $args['title']; ?>
			</<?php echo $args['title_tag']; ?>>
			<div <?php echo $this->get_render_attribute_string( 'jltma-placeholder-body' ); ?>><?php echo $args['body']; ?></div>
		</div><?php
	}

    /**
     * Method for setting widget dependancy on Elementor Pro plugin
     * When returning true it doesn't allow the widget to be registered
     * @return void
     */
    public static function requires_elementor_pro() {
		return false;
	}

    /**
	 * Get skin setting
	 *
	 * Retrieves the current skin setting
     */
	protected function get_skin_setting( $setting_key ) {
		if ( ! $setting_key )
			return false;

		return $this->get_current_skin()->get_instance_value( $setting_key );
	}

    /**
     * Set Loop Dynamic Settings
     *
     * @param  [type] $query
     *
     * @return void
     */
    protected function set_settings_for_loop( $query ) {

		global $wp_query;

		// Temporarily force a query for the template and set it as the currenty query
		$old_query 	= $wp_query;
		$wp_query 	= $query;

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
     * @param  [type] $post_id
     *
     * @return void
     */
	protected function set_settings_for_post( $post_id ) {
		if ( ! $post_id ) {
			return;
		}

		$settings 		= $this->get_settings_for_display();
		$all_settings 	= $this->get_settings();
		$controls 		= $this->get_controls();

		$this->jltma_loop_dynamic_settings[ $post_id ] = [];

		foreach ( $controls as $control ) {
			$control_name = $control['name'];
			$control_obj = \Elementor\Plugin::$instance->controls_manager->get_control( $control['type'] );

			if ( empty( $control['dynamic'] ) ) {
				continue;
			}

			$dynamic_settings = array_merge( $control_obj->get_settings( 'dynamic' ), $control['dynamic'] );
			$parsed_value = '';

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
     * @param  bool $post_id
     *
     * @return void
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
     * @return void
     */
    public function get_id_for_loop() {
		global $post;

		if ( ! $post ) {
			return $this->get_id();
		}

		return implode( '_', [ $this->get_id(), $post->ID ] );
	}

}
