<?php
/**
 * Class: News_Ticker_Widget
 * Name: .News ticker
 *
 * Slug: eac-addon-news-ticker
 *
 * Description: Bandeau déroulant les nouvelles des journaux par leurs flux RSS.
 *
 * @since 1.9.2
 */

namespace EACCustomWidgets\Includes\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Core\Eac_Load_Config;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Repeater;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class News_Ticker_Widget extends Widget_Base {

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'news-ticker';

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
	 * Load dependent libraries
	 *
	 * @access public
	 *
	 * @return libraries Array.
	 */
	public function get_script_depends(): array {
		return array( 'eac-news-ticker' );
	}

	/**
	 * Load dependent styles
	 * Les styles sont chargés dans le footer
	 *
	 * @access public
	 *
	 * @return CSS Array.
	 */
	public function get_style_depends(): array {
		return array( 'eac-news-ticker' );
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
			'news_ticker_settings',
			array(
				'label' => esc_html__( 'RSS feeds', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$repeater = new Repeater();

			$repeater->add_control(
				'news_item_title',
				array(
					'label' => esc_html__( 'Title', 'eac-components' ),
					'type'  => Controls_Manager::TEXT,
					'ai'    => array( 'active' => false ),
				)
			);

			$repeater->add_control(
				'news_item_url',
				array(
					'label'       => esc_html( 'URL' ),
					'type'        => Controls_Manager::URL,
					'placeholder' => 'http://your-link.com/index.xml/',
				)
			);

			$this->add_control(
				'news_image_list',
				array(
					'type'        => Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => array(
						array(
							'news_item_title' => 'Euronews',
							'news_item_url'   => array( 'url' => 'https://www.youtube.com/feeds/videos.xml?channel_id=UCW2QcKZiU8aUGg4yxCIditg' ),
						),
						array(
							'news_item_title' => 'EAC feed',
							'news_item_url'   => array( 'url' => 'https://elementor-addon-components.com/feed/' ),
						),
						array(
							'news_item_title' => 'WPTavern feed',
							'news_item_url'   => array( 'url' => 'https://wptavern.com/feed/' ),
						),
						array(
							'news_item_title' => 'Le Monde',
							'news_item_url'   => array( 'url' => 'https://www.lemonde.fr/rss/en_continu.xml' ),
						),
						array(
							'news_item_title' => 'Courrier International',
							'news_item_url'   => array( 'url' => 'https://www.courrierinternational.com/feed/all/rss.xml' ),
						),
						array(
							'news_item_title' => 'Huffington',
							'news_item_url'   => array( 'url' => 'https://www.huffingtonpost.fr/feeds/index.xml' ),
						),
						array(
							'news_item_title' => 'BBC News',
							'news_item_url'   => array( 'url' => 'https://feeds.bbci.co.uk/news/world/rss.xml' ),
						),
						array(
							'news_item_title' => 'Die Welt',
							'news_item_url'   => array( 'url' => 'https://www.welt.de/feeds/latest.rss' ),
						),
						array(
							'news_item_title' => 'Corriere della Sera',
							'news_item_url'   => array( 'url' => 'https://xml2.corriereobjects.it/rss/homepage.xml' ),
						),
					),
					'title_field' => '{{{ news_item_title }}}',
					'button_text' => esc_html__( 'Add feed', 'eac-components' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'news_items_settings',
			array(
				'label' => esc_html__( 'Settings', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'news_item_nombre',
				array(
					'label'   => esc_html__( 'Post count', 'eac-components' ),
					'type'    => Controls_Manager::NUMBER,
					'min'     => 5,
					'max'     => 50,
					'step'    => 5,
					'default' => 20,
				)
			);

			$this->add_control(
				'news_item_speed',
				array(
					'label'       => esc_html__( 'Scroll speed (seconds)', 'eac-components' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => array( 'px' ),
					'default'     => array(
						'unit' => 'px',
						'size' => 60,
					),
					'range'       => array(
						'px' => array(
							'min'  => 10,
							'max'  => 180,
							'step' => 10,
						),
					),
					'label_block' => true,
				)
			);

			/**
			$this->add_control('news_item_speed',
				[
					'label' => esc_html__('Scroll speed (seconds)', 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'default'   => array(
					'sizes' => array(
						'start' => 0,
						'end'   => 100,
						),
						'unit'  => '%',
					),
					'handles'   => 'range',
					'scales'    => 1,
					'labels'    => array(
						__( 'Bottom', 'eac-components' ),
						__( 'Top', 'eac-components' ),
					),
					'label_block' => true,
				]
			);
			*/

			$this->add_control(
				'news_item_loop',
				array(
					'label'       => esc_html__( 'Count of iterations', 'eac-components' ),
					'description' => esc_html__( 'Number of iterations of the contents of each feed before loading the next feed. 0 = infinite', 'eac-components' ),
					'type'        => Controls_Manager::NUMBER,
					'min'         => 0,
					'max'         => 20,
					'step'        => 1,
					'default'     => 1,
				)
			);

			$this->add_responsive_control(
				'news_item_height',
				array(
					'label'          => esc_html__( 'Height (px)', 'eac-components' ),
					'type'           => Controls_Manager::SLIDER,
					'size_units'     => array( 'px' ),
					'default'        => array(
						'unit' => 'px',
						'size' => 45,
					),
					'tablet_default' => array(
						'unit' => 'px',
						'size' => 45,
					),
					'mobile_default' => array(
						'unit' => 'px',
						'size' => 35,
					),
					'range'          => array(
						'px' => array(
							'min'  => 30,
							'max'  => 100,
							'step' => 5,
						),
					),
					'selectors'      => array( '{{WRAPPER}} .news-ticker_wrapper, {{WRAPPER}} .news-ticker_wrapper-content' => 'block-size: {{SIZE}}px;' ),
				)
			);

			$order_end   = is_rtl() ? 'end' : 'start';
			$order_start = is_rtl() ? 'start' : 'end';
			$this->add_control(
				'news_item_scroll',
				array(
					'label'        => esc_html__( 'Display direction', 'eac-components' ),
					'type'         => Controls_Manager::CHOOSE,
					'options'      => array(
						'left'  => array(
							'title' => is_rtl() ? esc_html__( 'Right', 'eac-components' ) : esc_html__( 'Left', 'eac-components' ),
							'icon'  => "eicon-order-{$order_end}",
						),
						'right' => array(
							'title' => is_rtl() ? esc_html__( 'Left', 'eac-components' ) : esc_html__( 'Right', 'eac-components' ),
							'icon'  => "eicon-order-{$order_start}",
						),
					),
					'default'      => 'right',
					'prefix_class' => 'news-ticker_orientation-',
					'render_type'  => 'template',
					'toggle'       => false,
					'separator'    => 'before',
				)
			);

			$this->add_control(
				'news_item_date',
				array(
					'label'   => esc_html__( 'Publishing date', 'eac-components' ),
					'type'    => Controls_Manager::CHOOSE,
					'options' => array(
						'yes' => array(
							'title' => esc_html__( 'Yes', 'eac-components' ),
							'icon'  => 'eicon-check',
						),
						'no'  => array(
							'title' => esc_html__( 'No', 'eac-components' ),
							'icon'  => 'eicon-ban',
						),
					),
					'default' => 'no',
					'toggle'  => false,
				)
			);

			$this->add_control(
				'news_item_read_auto',
				array(
					'label'   => esc_html__( 'Autoplay', 'eac-components' ),
					'type'    => Controls_Manager::CHOOSE,
					'options' => array(
						'yes' => array(
							'title' => esc_html__( 'Yes', 'eac-components' ),
							'icon'  => 'eicon-check',
						),
						'no'  => array(
							'title' => esc_html__( 'No', 'eac-components' ),
							'icon'  => 'eicon-ban',
						),
					),
					'default' => 'no',
					'toggle'  => false,
				)
			);

			$this->add_control(
				'news_item_controls',
				array(
					'label'   => esc_html__( 'Forward/Backward buttons', 'eac-components' ),
					'type'    => Controls_Manager::CHOOSE,
					'options' => array(
						'yes' => array(
							'title' => esc_html__( 'Yes', 'eac-components' ),
							'icon'  => 'eicon-check',
						),
						'no'  => array(
							'title' => esc_html__( 'No', 'eac-components' ),
							'icon'  => 'eicon-ban',
						),
					),
					'default' => 'yes',
					'toggle'  => false,
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'news_general_style',
			array(
				'label' => esc_html__( 'General', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'news_wrapper_style',
				array(
					'label'        => esc_html__( 'Style', 'eac-components' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => 'style-0',
					'options'      => array(
						'style-0'  => esc_html__( 'Default', 'eac-components' ),
						'style-1'  => 'Style 1',
						'style-2'  => 'Style 2',
						'style-3'  => 'Style 3',
						'style-4'  => 'Style 4',
						'style-5'  => 'Style 5',
						'style-6'  => 'Style 6',
						'style-7'  => 'Style 7',
						'style-8'  => 'Style 8',
						'style-9'  => 'Style 9',
						'style-10' => 'Style 10',
					),
					'prefix_class' => 'news-ticker_wrapper-',
				)
			);

			$this->add_control(
				'news_wrapper_style_warning',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'raw'             => esc_html__( "Don't forget to enable autoplay.", 'eac-components' ),
					'condition'       => array( 'news_wrapper_style' => 'style-10' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'news_title_style',
			array(
				'label' => esc_html__( 'Title', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'news_title_color',
				array(
					'label'     => esc_html__( 'Color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .news-ticker_wrapper-title a, {{WRAPPER}} .news-ticker_wrapper-control' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'news_title_typography',
					'label'    => esc_html__( 'Typography', 'eac-components' ),
					'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'selector' => '{{WRAPPER}} .news-ticker_wrapper-title',
				)
			);

			$this->add_control(
				'news_title_bgcolor',
				array(
					'label'     => esc_html__( 'Background color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .news-ticker_wrapper-title, {{WRAPPER}} .news-ticker_wrapper-control' => 'background-color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'      => 'news_title_border',
					'selector'  => '{{WRAPPER}} .news-ticker_wrapper-title',
				)
			);

			$this->add_control(
				'news_title_radius',
				array(
					'label'              => esc_html__( 'Border radius', 'eac-components' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => array( 'px', '%' ),
					'allowed_dimensions' => array( 'top', 'right', 'bottom', 'left' ),
					'default'            => array(
						'left'     => 0,  // border-top-left-radius : border-start-start-radius
						'right'    => 24, // border-top-right-radius : border-start-end-radius
						'top'      => 0,  // border-bottom-left-radius : border-end-start-radius
						'bottom'   => 24, // border-bottom-right-radius : border-end-end-radius
						'unit'     => 'px',
						'isLinked' => true,
					),
					'selectors'          => array(
						'{{WRAPPER}} .news-ticker_wrapper-title' => 'border-start-start-radius: {{LEFT}}{{UNIT}}; border-start-end-radius: {{RIGHT}}{{UNIT}}; border-end-start-radius: {{TOP}}{{UNIT}}; border-end-end-radius: {{BOTTOM}}{{UNIT}};',
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'news_picto_style',
			array(
				'label' => esc_html__( 'Pictograms', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'news_picto_color',
				array(
					'label'     => esc_html__( 'Color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array( '{{WRAPPER}} .left, {{WRAPPER}} .play, {{WRAPPER}} .pause, {{WRAPPER}} .right' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'news_picto_typography',
					'label'    => esc_html__( 'Typography', 'eac-components' ),
					'selector' => '{{WRAPPER}} .left, {{WRAPPER}} .play, {{WRAPPER}} .pause, {{WRAPPER}} .right',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'news_content_style',
			array(
				'label' => esc_html__( 'Content', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'news_content_color',
				array(
					'label'     => esc_html__( 'Color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .news-ticker_wrapper-content .animationHorizontal .news' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'           => 'news_content_typography',
					'label'          => esc_html__( 'Typography', 'eac-components' ),
					'global'         => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'fields_options' => array(
						'font_size' => array(
							'default'        => array(
								'size' => 1.1,
								'unit' => 'em',
							),
							'tablet_default' => array(
								'size' => 1.1,
								'unit' => 'em',
							),
							'mobile_default' => array(
								'size' => .8,
								'unit' => 'em',
							),
						),
					),
					'selector'       => '{{WRAPPER}} .news-ticker_wrapper-content .animationHorizontal .date,
									{{WRAPPER}} .news-ticker_wrapper-content .animationHorizontal .news',
				)
			);

			$this->add_control(
				'news_content_bgcolor',
				array(
					'label'     => esc_html__( 'Background color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .news-ticker_wrapper-content' => 'background-color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'      => 'news_content_border',
					'selector'  => '{{WRAPPER}} .news-ticker_wrapper',
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'     => 'news_content_shadow',
					'label'    => esc_html__( 'Shadow', 'eac-components' ),
					'selector' => '{{WRAPPER}} .news-ticker_wrapper',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'news_date_style',
			array(
				'label'     => esc_html__( 'Date', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'news_item_date' => 'yes' ),
			)
		);

			$this->add_control(
				'news_date_color',
				array(
					'label'     => esc_html__( 'Color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .news-ticker_wrapper-content .animationHorizontal .date' => 'color: {{VALUE}};' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'news_sep_style',
			array(
				'label' => esc_html__( 'Separator', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'news_sep_color',
				array(
					'label'     => esc_html__( 'Color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .news-ticker_wrapper-content .animationHorizontal .separator' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'news_sep_typography',
					'label'    => esc_html__( 'Typography', 'eac-components' ),
					'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'selector' => '{{WRAPPER}} .news-ticker_wrapper-content .animationHorizontal .separator',
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
		if ( ! $settings['news_image_list'] ) {
			return;
		}
		$this->add_render_attribute( 'news_ticker', 'class', 'news-ticker_wrapper' );
		$this->add_render_attribute( 'news_ticker', 'data-settings', $this->get_settings_json() );
		?>
		<div class='eac-news-ticker'>
			<input type='hidden' id='news_nonce' name='news_nonce' value="<?php echo wp_create_nonce( 'eac_rss_feed_' . esc_attr( $this->get_id() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" />
			<div <?php $this->print_render_attribute_string( 'news_ticker' ); ?>>
				<div class='news-ticker_wrapper-title'>Breaking News: Taglines</div>
				<div id='news-ticker_wrapper-content' class='news-ticker_wrapper-content' tabindex='-1'>
					<div class='animationPause animationHorizontal'></div>
				</div>
				<div class='news-ticker_wrapper-control'>
					<?php if ( 'yes' === $settings['news_item_controls'] ) : ?>
						<span class='left eac-icon-svg' role='button' tabindex='0' aria-label="<?php esc_html_e( 'Previous feed', 'eac-components' ); ?>" aria-controls='news-ticker_wrapper-content' title="<?php esc_html_e( 'Previous feed', 'eac-components' ); ?>">
							<?php
							Icons_Manager::render_icon(
								array(
									'value'   => 'fas fa-caret-square-left',
									'library' => 'fa-regular',
								),
								array( 'aria-hidden' => 'true' )
							);
							?>
						</span>
					<?php endif; ?>
					<span class='play eac-icon-svg' role='button' tabindex='0' aria-label="<?php esc_html_e( 'Start reading', 'eac-components' ); ?>" aria-controls='news-ticker_wrapper-content' title="<?php esc_html_e( 'Start reading', 'eac-components' ); ?>">
						<?php
						Icons_Manager::render_icon(
							array(
								'value'   => 'fas fa-play-circle',
								'library' => 'fa-regular',
							),
							array( 'aria-hidden' => 'true' )
						); ?>
					</span>
					<span class='pause eac-icon-svg' role='button' tabindex='0' aria-label="<?php esc_html_e( 'Stop reading', 'eac-components' ); ?>" aria-controls='news-ticker_wrapper-content' title="<?php esc_html_e( 'Stop reading', 'eac-components' ); ?>">
						<?php
						Icons_Manager::render_icon(
							array(
								'value'   => 'fas fa-pause-circle',
								'library' => 'fa-regular',
							),
							array( 'aria-hidden' => 'true' )
						); ?>
					</span>
					<?php if ( 'yes' === $settings['news_item_controls'] ) : ?>
						<span class='right eac-icon-svg' role='button' tabindex='0' aria-label="<?php esc_html_e( 'Next feed', 'eac-components' ); ?>" aria-controls='news-ticker_wrapper-content' title="<?php esc_html_e( 'Next feed', 'eac-components' ); ?>">
							<?php
							Icons_Manager::render_icon(
								array(
									'value'   => 'fas fa-caret-square-right',
									'library' => 'fa-regular',
								),
								array( 'aria-hidden' => 'true' )
							);
							?>
						</span>
					<?php endif; ?>
				</div>
			</div>
			<?php $this->render_news(); ?>
		</div>
		<?php
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_news(): void {
		$settings = $this->get_settings_for_display(); ?>
		<div class='news-ticker_item-list'>
			<?php
			foreach ( $settings['news_image_list'] as $item ) {
				if ( ! empty( $item['news_item_url']['url'] ) ) : ?>
					<span class='news-ticker_item'><?php echo esc_html( $item['news_item_title'] ) . '::' . esc_url( $item['news_item_url']['url'] ); ?></span>
				<?php endif;
			} ?>
		</div>
		<?php
	}

	/**
	 * get_settings_json()
	 *
	 * Retrieve fields values to pass at the widget container
	 * Convert on JSON format
	 *
	 * @uses      wp_json_encode()
	 *
	 * @return    JSON oject
	 *
	 * @access    protected
	 */

	protected function get_settings_json(): string {
		$module_settings = $this->get_settings_for_display();

		$settings = array(
			'data_id'     => esc_attr( $this->get_id() ),
			'data_nombre' => ! empty( $module_settings['news_item_nombre'] ) ? absint( $module_settings['news_item_nombre'] ) : 20,
			'data_date'   => 'yes' === $module_settings['news_item_date'] ? true : false,
			'data_speed'  => ! empty( $module_settings['news_item_speed'] ) ? absint( $module_settings['news_item_speed']['size'] ) : 60,
			'data_loop'   => ! empty( $module_settings['news_item_loop'] ) ? absint( $module_settings['news_item_loop'] ) : 1,
			'data_auto'   => 'yes' === $module_settings['news_item_read_auto'] ? true : false,
			'data_rtl'    => $module_settings['news_item_scroll'],
		);

		return wp_json_encode( $settings );
	}

	protected function content_template(): void {}
}
