<?php

namespace MasterAddons\Inc\Admin\Templates\Includes\Types;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 9/8/19
 */

if (!defined('ABSPATH')) exit; // No access of directly access

if (!class_exists(__NAMESPACE__ . '\\Manager')) {

	class Manager
	{

		private $types = null;

		public function __construct()
		{

			$this->register_types();
		}

		public function register_types()
		{
			$types = array(
				Section::class,
			);

			foreach ($types as $class) {
				$this->register_type($class);
			}

			do_action('master-addons-templates/types/register', $this);
		}


		public function register_type($class)
		{

			$instance = new $class;

			$this->types[$instance->get_id()] = $instance;

			if (true === $instance->is_location()) {

				register_structure()->locations->register_location($instance->location_name(), $instance);
			}
		}

		public function get_types()
		{

			return $this->types;
		}

		public function get_type($id)
		{

			return isset($this->types[$id]) ? $this->types[$id] : false;
		}

		public function get_types_for_popup()
		{
			// Check if we're in popup builder context
			$current_post_type = get_post_type();
			$is_popup_builder = false;
			// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only display routing, no state change
			$get_action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
			$get_tab    = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : '';
			$get_post   = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
			$get_page   = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
			// phpcs:enable WordPress.Security.NonceVerification.Recommended
			if ( $get_action === 'jltma_get_templates' && $get_tab === 'master_popups' ) {
				$is_popup_builder = true;
			}else{
				// Check if we're editing a popup post type or if the post type is related to popups
				if ( $current_post_type === 'jltma_popup' || $current_post_type === 'popupbuilder' ||
					( $get_post && get_post_type( $get_post ) === 'jltma_popup' ) ||
					( $get_post && get_post_type( $get_post ) === 'popupbuilder' ) ||
					( $get_page && strpos( $get_page, 'popup' ) !== false ) ) {
					$is_popup_builder = true;
				}

				// Also check if we're in Elementor editor and editing a popup
				if ( $get_action === 'elementor' && $get_post &&
					( get_post_type( $get_post ) === 'ma_popup' || get_post_type( $get_post ) === 'jltma_popup' || get_post_type( $get_post ) === 'popupbuilder' ) ) {
					$is_popup_builder = true;
				}
			}
	
			// If we're in popup builder context, only show the Popups tab
			if ($is_popup_builder) {
				$result = array(
					'master_popups' => array(
						'title' => __('Popups', 'master-addons'),
						'data' => [],
						'sources' => array('master-addons', 'master-api'),
						'settings' => array(
							'show_title' => true,
							'show_keywords' => true
						)
					)
				);
			} else {
				// Show normal tabs for other contexts
				$result = array(
					'master_pages' => array(
						'title' => __('Ready Pages', 'master-addons' ),
						'data' => [],
						'sources' => array('master-addons', 'master-api'),
						'settings' => array(
							'show_title' => true,
							'show_keywords' => true
						)
					),
					'master_headers' => array(
						'title' => __('Headers', 'master-addons' ),
						'data' => [],
						'sources' => array('master-addons', 'master-api'),
						'settings' => array(
							'show_title' => true,
							'show_keywords' => true
						)
					),
					'master_footers' => array(
						'title' => __('Footers', 'master-addons' ),
						'data' => [],
						'sources' => array('master-addons', 'master-api'),
						'settings' => array(
							'show_title' => true,
							'show_keywords' => true
						)
					),
				);
			}

			foreach ($this->types as $id => $structure) {
				$result[$id] = array(
					'title'    => $structure->get_plural_label(),
					'data'     => array(),
					'sources'  => $structure->get_sources(),
					'settings' => $structure->library_settings(),
				);
			}
			if( $is_popup_builder ){
				unset($result['master_section']);
			}
			return $result;
		}
	}
}
