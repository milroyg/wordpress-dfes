<?php
/**
 * Class: Site_Stats
 *
 * @return affiche la valeur d'une variable interne du site
 * @since 1.6.0
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Controls_Manager;
use Elementor\TemplateLibrary\Source_Local;

class Site_Stats extends Tag {

	public function get_name(): string {
		return 'eac-addon-post-stats';
	}

	public function get_title(): string {
		return esc_html__( 'Statistics', 'eac-components' );
	}

	public function get_group(): array {
		return array( 'eac-site-groupe' );
	}

	public function get_categories(): array {
		return array(
			TagsModule::TEXT_CATEGORY,
		);
	}

	public function get_panel_template_setting_key(): string {
		return 'select_stats';
	}

	protected function register_controls(): void {

		$this->add_control(
			'select_stats',
			array(
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					''                => esc_html__( 'Select...', 'eac-components' ),
					'wpv'             => esc_html( 'WordPress version' ),
					'phpv'            => esc_html( 'PHP version' ),
					'eacv'            => esc_html( 'EAC version' ),
					'woov'            => esc_html( 'WooCommerce version' ),
					'acfv'            => esc_html( 'ACF version' ),
					'siteurl'         => esc_html__( 'Site url', 'eac-components' ),
					'language'        => esc_html__( 'Language', 'eac-components' ),
					'timezone'        => esc_html__( 'Timezone', 'eac-components' ),
					'dateformat'      => esc_html__( 'Date format', 'eac-components' ),
					'user'            => esc_html__( 'Registered users', 'eac-components' ),
					'post'            => esc_html__( 'Posts', 'eac-components' ),
					'page'            => esc_html__( 'Pages', 'eac-components' ),
					'cpt'             => esc_html__( 'Custom Post Types', 'eac-components' ),  // Nombre de types d'articles personnalisés
					'countcpt'        => esc_html__( 'Custom Post Type count', 'eac-components' ), // Nombre d'articles de types personnalisés
					'attachment'      => esc_html__( 'Medias', 'eac-components' ),
					'comment'         => esc_html__( 'Comments', 'eac-components' ),
					'comment_pending' => esc_html__( 'Comments pending', 'eac-components' ),
					'category'        => esc_html__( 'Categories', 'eac-components' ),
					'post_tag'        => esc_html__( 'Tags', 'eac-components' ),
					'elem_vers'       => esc_html( 'Elementor version' ),
					'elem_lib'        => esc_html__( 'Elementor templates', 'eac-components' ),
					'elem_category'   => esc_html__( 'Elementor categories', 'eac-components' ),
					'plugins'         => esc_html__( 'Active plugins', 'eac-components' ),
					'spams'           => esc_html( 'Spams' ),
				),
			)
		);
	}

	public function render(): void {
		global $wpdb;
		$stats = 0;

		if ( 'wpv' === $this->get_settings( 'select_stats' ) ) {
			$stats = get_bloginfo( 'version' );
		} elseif ( 'phpv' === $this->get_settings( 'select_stats' ) ) {
			$stats = phpversion();
		} elseif ( 'eacv' === $this->get_settings( 'select_stats' ) ) {
			$stats = EAC_PLUGIN_VERSION;
		} elseif ( 'woov' === $this->get_settings( 'select_stats' ) ) {
			$stats = defined( 'WC_VERSION' ) ? WC_VERSION : 'x.x.x';
		} elseif ( 'acfv' === $this->get_settings( 'select_stats' ) ) {
			$stats = defined( 'ACF_VERSION' ) ? ACF_VERSION : 'x.x.x';
		} elseif ( 'siteurl' === $this->get_settings( 'select_stats' ) ) {
			$stats = get_site_url();
		} elseif ( 'language' === $this->get_settings( 'select_stats' ) ) {
			$stats = get_bloginfo( 'language' );
		} elseif ( 'timezone' === $this->get_settings( 'select_stats' ) ) {
			$timezone = get_option( 'timezone_string' );
			if ( ! $timezone ) {
				$timezone = get_option( 'gmt_offset' );
			}
			$stats = $timezone;
		} elseif ( 'dateformat' === $this->get_settings( 'select_stats' ) ) {
			$stats = get_option( 'date_format' );
		} elseif ( 'user' === $this->get_settings( 'select_stats' ) ) {
			$stats = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->users" );
		} elseif ( 'post' === $this->get_settings( 'select_stats' ) ) {
			$stats = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish'" );
		} elseif ( 'page' === $this->get_settings( 'select_stats' ) ) {
			$stats = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'page' AND post_status = 'publish'" );
		} elseif ( 'cpt' === $this->get_settings( 'select_stats' ) ) {
			$stats = count( get_post_types( array( '_builtin' => false ) ) );
		} elseif ( 'countcpt' === $this->get_settings( 'select_stats' ) ) {
			$post_types = get_post_types( array( '_builtin' => false ), 'objects' );
			foreach ( $post_types as $post_type ) {
				$stats += wp_count_posts( $post_type->name )->publish;
			}
		} elseif ( 'attachment' === $this->get_settings( 'select_stats' ) ) {
			$stats = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'attachment' AND post_status = 'inherit'" );
		} elseif ( 'comment' === $this->get_settings( 'select_stats' ) ) {
			$stats = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '1'" );
		} elseif ( 'comment_pending' === $this->get_settings( 'select_stats' ) ) {
			$stats = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '0'" );
		} elseif ( 'category' === $this->get_settings( 'select_stats' ) ) {
			$stats = wp_count_terms( 'category' );
		} elseif ( 'post_tag' === $this->get_settings( 'select_stats' ) ) {
			$stats = wp_count_terms( 'post_tag' );
		} elseif ( 'elem_vers' === $this->get_settings( 'select_stats' ) ) {
			$stats = defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : 'x.x.x';
		} elseif ( 'elem_lib' === $this->get_settings( 'select_stats' ) ) {
			$stats = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'elementor_library' AND post_status = 'publish'" );
		} elseif ( 'elem_category' === $this->get_settings( 'select_stats' ) ) {
			$stats = wp_count_terms( Source_Local::TAXONOMY_CATEGORY_SLUG );
		} elseif ( 'plugins' === $this->get_settings( 'select_stats' ) ) {
			$stats = 0;
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			$active_plugins    = get_option( 'active_plugins' );
			$all_plugins       = get_plugins();
			$activated_plugins = array();

			foreach ( $active_plugins as $plugin ) {
				if ( isset( $all_plugins[ $plugin ] ) ) {
					array_push( $activated_plugins, $all_plugins[ $plugin ] );
				}
			}
			$stats = count( $activated_plugins );
		} elseif ( 'spams' === $this->get_settings( 'select_stats' ) ) {
			if ( get_option( 'eac_options_honeypot_comment' ) ) {
				$spam = get_option( 'eac_options_honeypot_comment' );
				$stats = $spam['count'];
			} else {
				$stats = '0';
			}
		}

		echo wp_kses_post( $stats );
	}
}
