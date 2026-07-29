<?php
/**
 * Class: Memory_Usage_Widget
 * Slug: memory-usage
 * Name: eac-addon-memory-usage
 *
 * Description: Affichage des informations relatives à l'utilisation de la mémoire
 *
 * @since 2.2.9
 */

namespace EACCustomWidgets\Includes\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

use EACCustomWidgets\Core\Eac_Load_Config;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

class Memory_Usage_Widget extends Widget_Base {

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'memory-usage';

	/**
	 * Retrieve widget name.
	 *
	 * @access public
	 *
	 * @return string widget name.
	 */
	public function get_name(): string {
		return Eac_Load_Config::get_widget_name( $this->slug );
	}

	/**
	 * Retrieve widget title.
	 *
	 * @access public
	 *
	 * @return string widget title.
	 */
	public function get_title(): string {
		return Eac_Load_Config::get_widget_title( $this->slug );
	}

	/**
	 * Retrieve widget icon.
	 *
	 * @access public
	 *
	 * @return string widget icon.
	 */
	public function get_icon(): string {
		return Eac_Load_Config::get_widget_icon( $this->slug );
	}

	/**
	 * Affecte le composant à la catégorie définie dans plugin.php
	 *
	 * @access public
	 *
	 * @return array widget category.
	 */
	public function get_categories(): array {
		return Eac_Load_Config::get_widget_categories( $this->slug );
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords(): array {
		return Eac_Load_Config::get_widget_keywords( $this->slug );
	}

	/**
	 * Get help widget get_custom_help_url.
	 *
	 * @access public
	 *
	 * @return string URL help center
	 */
	public function get_custom_help_url(): string {
		return Eac_Load_Config::get_widget_help_url( $this->slug );
	}

	/**
	 * has_widget_inner_wrapper
	 *
	 * @return bool
	 */
	public function has_widget_inner_wrapper(): bool {
		return false;
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls(): void {

		$this->start_controls_section(
			'mu_settings',
			array(
				'label' => esc_html__( 'Settings', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'mu_show_labels',
				array(
					'label'     => esc_html__( 'Display labels', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'yes' => array(
							'title' => esc_html__( 'Yes', 'eac-components' ),
							'icon'  => 'eicon-check',
						),
						'no'    => array(
							'title' => esc_html__( 'No', 'eac-components' ),
							'icon'  => 'eicon-ban',
						),
					),
					'default'   => 'yes',
					'toggle'    => false,
				)
			);

			$this->add_control(
				'mu_peak',
				array(
					'label'     => esc_html__( 'Peak memory usage', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'yes' => array(
							'title' => esc_html__( 'Yes', 'eac-components' ),
							'icon'  => 'eicon-check',
						),
						'no'  => array(
							'title' => esc_html__( 'No', 'eac-components' ),
							'icon'  => 'eicon-ban',
						),
					),
					'default'   => 'yes',
					'toggle'    => false,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'mu_limit',
				array(
					'label'     => esc_html__( 'PHP memory limit', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'yes' => array(
							'title' => esc_html__( 'Yes', 'eac-components' ),
							'icon'  => 'eicon-check',
						),
						'no'  => array(
							'title' => esc_html__( 'No', 'eac-components' ),
							'icon'  => 'eicon-ban',
						),
					),
					'default'   => 'yes',
					'toggle'    => false,
				)
			);

			$this->add_control(
				'mu_percent',
				array(
					'label'     => esc_html__( 'Percentage', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'yes' => array(
							'title' => esc_html__( 'Yes', 'eac-components' ),
							'icon'  => 'eicon-check',
						),
						'no'  => array(
							'title' => esc_html__( 'No', 'eac-components' ),
							'icon'  => 'eicon-ban',
						),
					),
					'default'   => 'yes',
					'toggle'    => false,
				)
			);

			$this->add_control(
				'mu_usage',
				array(
					'label'     => esc_html__( 'Memory used', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'yes' => array(
							'title' => esc_html__( 'Yes', 'eac-components' ),
							'icon'  => 'eicon-check',
						),
						'no'  => array(
							'title' => esc_html__( 'No', 'eac-components' ),
							'icon'  => 'eicon-ban',
						),
					),
					'default'   => 'yes',
					'toggle'    => false,
				)
			);

			$this->add_control(
				'mu_req',
				array(
					'label'     => esc_html__( 'Count of queries', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'yes' => array(
							'title' => esc_html__( 'Yes', 'eac-components' ),
							'icon'  => 'eicon-check',
						),
						'no'  => array(
							'title' => esc_html__( 'No', 'eac-components' ),
							'icon'  => 'eicon-ban',
						),
					),
					'default'   => 'yes',
					'toggle'    => false,
				)
			);

			$this->add_control(
				'mu_time',
				array(
					'label'     => esc_html__( 'Loading time', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'description' => esc_html__( 'Time elapsed between the start of page loading and the call to this function', 'eac-components' ),
					'options'   => array(
						'yes' => array(
							'title' => esc_html__( 'Yes', 'eac-components' ),
							'icon'  => 'eicon-check',
						),
						'no'  => array(
							'title' => esc_html__( 'No', 'eac-components' ),
							'icon'  => 'eicon-ban',
						),
					),
					'default'   => 'yes',
					'toggle'    => false,
				)
			);

			$this->add_control(
				'mu_ip',
				array(
					'label'     => esc_html__( 'IP address', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'yes' => array(
							'title' => esc_html__( 'Yes', 'eac-components' ),
							'icon'  => 'eicon-check',
						),
						'no'  => array(
							'title' => esc_html__( 'No', 'eac-components' ),
							'icon'  => 'eicon-ban',
						),
					),
					'default'   => 'yes',
					'toggle'    => false,
				)
			);

		$this->end_controls_section();

		/**
		 * Generale Style Section
		 */
		$this->start_controls_section(
			'site_logo_style',
			array(
				'label' => esc_html__( 'General', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$start = is_rtl() ? 'right' : 'left';
			$end   = is_rtl() ? 'left' : 'right';
			$this->add_responsive_control(
				'mu_alignment',
				array(
					'label'     => esc_html__( 'Alignment', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'left'   => array(
							'title' => is_rtl() ? esc_html__( 'Right', 'eac-components' ) : esc_html__( 'Left', 'eac-components' ),
							'icon'  => "eicon-h-align-{$start}",
						),
						'center' => array(
							'title' => esc_html__( 'Center', 'eac-components' ),
							'icon'  => 'eicon-h-align-center',
						),
						'right'  => array(
							'title' => is_rtl() ? esc_html__( 'Left', 'eac-components' ) : esc_html__( 'Right', 'eac-components' ),
							'icon'  => "eicon-h-align-{$end}",
						),
					),
					'selectors_dictionary' => array(
						'left'  => 'start',
						'right' => 'end',
					),
					'default'   => 'center',
					'toggle'    => false,
					'selectors' => array( '{{WRAPPER}} .memory-usage__wrapper' => 'text-align: {{VALUE}};' ),
				)
			);

			$this->add_control(
				'mu_color',
				array(
					'label'     => esc_html__( 'Color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_TEXT,
					),
					'selectors' => array(
						'{{WRAPPER}} .memory-usage__wrapper' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'sc_typography',
					'label'    => esc_html__( 'Typography', 'eac-components' ),
					'selector' => '{{WRAPPER}} .memory-usage__wrapper',
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$prefix = 'yes' === $settings['mu_show_labels'] ? true : false;
		?>
		<div class='eac-memory-usage'>
			<div class='memory-usage__wrapper' dir='ltr'>
				<?php
				if ( 'yes' === $settings['mu_peak'] ) { ?>
					<span class='memory-peak'><?php echo wp_kses_post( $this->get_memory_usage_peak( true, $prefix, $settings['mu_limit'] ) ); ?></span>
				<?php }
				if ( 'yes' === $settings['mu_limit'] ) { ?>
					<span class='memory-limit'><?php echo wp_kses_post( $this->get_memory_limit( true, $prefix, $settings['mu_peak'] ) ); ?></span>
				<?php }
				if ( 'yes' === $settings['mu_percent'] ) { ?>
					<span class='memory-percent'><?php echo wp_kses_post( $this->get_memory_percent( $prefix ) ); ?></span>
				<?php }
				if ( 'yes' === $settings['mu_usage'] ) { ?>
					<span class='memory-usage'><?php echo wp_kses_post( $this->get_memory_usage( true, $prefix ) ); ?></span>
				<?php }
				if ( 'yes' === $settings['mu_req'] ) { ?>
					<span class='memory-req'><?php echo wp_kses_post( $this->get_request_count( $prefix ) ); ?></span>
				<?php }
				if ( 'yes' === $settings['mu_time'] ) { ?>
					<span class='memory-time'><?php echo wp_kses_post( $this->get_execution_time( $prefix ) ); ?></span>
				<?php }
				if ( 'yes' === $settings['mu_ip'] ) { ?>
					<span class='memory-ip'><?php echo wp_kses_post( $this->get_serveur_ip( $prefix ) ); ?></span>
				<?php } ?>
			</div>
		</div>
		<style>
			.memory-usage__wrapper {
				position: relative;
			}
			span[class^='memory-'] {
				display: inline-block;
			}
		</style>
		<?php
	}

	/**
	 * get_memory_limit
	 *
	 * @param bool $formate
	 * @param bool $prefix
	 * @param string $peak
	 *
	 * @return mixed
	 */
	public function get_memory_limit( bool $formate = false, bool $prefix = false, string $peak = 'yes' ) {
		$memory = '0MB';
		if ( function_exists( 'ini_get' ) ) {
			$label = $prefix && 'no' === $peak ? esc_html__( 'Limit', 'eac-components' ) : '';
			$memory = $formate ? sprintf( '%s %.0fMB', $label, ini_get( 'memory_limit' ) ) : filter_var( ini_get( 'memory_limit' ), FILTER_SANITIZE_NUMBER_INT );
		}
		return $memory;
	}

	/**
	 * get_memory_usage
	 *
	 * @param bool $formate
	 * @param bool $prefix
	 *
	 * @return mixed
	 */
	public function get_memory_usage( bool $formate = false, bool $prefix = false ) {
		$label = $prefix ? esc_html__( 'Used', 'eac-components' ) : '';
		$memory = $formate ? sprintf( '%s %.1fMB', $label, memory_get_usage() / 1024 / 1024 ) : round( memory_get_usage() / 1024 / 1024, 0 );
		return $memory;
	}

	/**
	 * get_memory_usage_peak
	 *
	 * @param bool $formate
	 * @param bool $prefix
	 * @param string $limit
	 *
	 * @return mixed
	 */
	public function get_memory_usage_peak( bool $formate = false, bool $prefix = false, string $limit = 'yes' ) {
		$label = $prefix ? esc_html__( 'Peak', 'eac-components' ) : '';
		$of    = 'yes' === $limit ? esc_html__( 'of', 'eac-components' ) : '';
		$memory = $formate ? sprintf( '%s %.1fMB %s', $label, memory_get_peak_usage() / 1024 / 1024, $of ) : round( memory_get_peak_usage() / 1024 / 1024, 0 );
		return $memory;
	}

	/**
	 * get_memory_percent
	 *
	 * @param bool $prefix
	 *
	 * @return string
	 */
	public function get_memory_percent( bool $prefix = false ): string {
		$label = $prefix ? esc_html__( 'Percent', 'eac-components' ) : '';
		$limit = $this->get_memory_limit();
		$peak = $this->get_memory_usage_peak();
		$percent = sprintf( '%s %.1f%%', $label, ( $peak / $limit ) * 100 );
		return $percent;
	}

	/**
	 * get_request_count
	 *
	 * @param bool $prefix
	 *
	 * @return string
	 */
	public function get_request_count( bool $prefix = false ): string {
		$label = $prefix ? esc_html__( 'Queries', 'eac-components' ) : '';
		$req = sprintf( '%s %d', $label, get_num_queries() );
		return $req;
	}

	/**
	 * get_execution_time
	 *
	 * @param bool $prefix
	 *
	 * @return string
	 */
	public function get_execution_time( bool $prefix = false ): string {
		$label = $prefix ? esc_html__( 'Time', 'eac-components' ) : '';
		$time = sprintf( '%s %s%s', $label, timer_stop( 0, 2 ), esc_html( 's' ) );
		return $time;
	}

	/**
	 * get_serveur_ip
	 *
	 * @param bool $prefix
	 *
	 * @return string
	 */
	public function get_serveur_ip( bool $prefix = false ): string {
		$label = $prefix ? esc_html( 'IP' ) : '';
		$server_ip_address = ! empty( $_SERVER['SERVER_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_ADDR'] ) ) : '';
		if ( empty( $server_ip_address ) ) {
			$server_ip_address = ! empty( $_SERVER['LOCAL_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['LOCAL_ADDR'] ) ) : '';
		}
		$ip = sprintf( '%s %s', esc_attr( $label ), esc_attr( $server_ip_address ) );
		return $ip;
	}

	protected function content_template(): void {}
}
