<?php

namespace MasterAddons\Addons;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 8/18/19
 */

// Elementor Classes
use MasterAddons\Inc\Classes\Base\Master_Widget;
use \Elementor\Controls_Manager;
use \Elementor\Repeater;
use \Elementor\Group_Control_Typography;
use \Elementor\Core\Kits\Documents\Tabs\Global_Typography;

use MasterAddons\Inc\Admin\Config;

if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

class Changelogs extends Master_Widget
{
	use \MasterAddons\Inc\Traits\Widget_Notice;
	use \MasterAddons\Inc\Traits\Widget_Assets_Trait;

	public function get_name()
	{
		return 'ma-changelog';
	}
	public function get_title()
	{
		return esc_html__('Changelog', 'master-addons' );
	}

	public function get_icon()
	{
		return 'jltma-icon ' . Config::get_addon_icon($this->get_name());
	}


	protected function register_controls()
	{

		/**
		 * Master Headlines Content Section
		 */
		$this->start_controls_section(
			'ma_el_changelog_content_section',
			[
				'label' => esc_html__('Changelog Content', 'master-addons' ),
			]
		);

		$this->add_control(
			'ma_el_changelog_heading',
			[
				'label' => esc_html__('Heading', 'master-addons' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => esc_html__('1.1.1 [18th August 2019]', 'master-addons' ),
			]
		);

		$this->add_control(
			'ma_el_changelog_main_title',
			[
				'label'   => esc_html__('Main Title', 'master-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'Added',
				'options' => [
					'Added'  => esc_html__('Added', 'master-addons' ),
					'Fixed' => esc_html__('Fixed', 'master-addons' ),
					'Updated' => esc_html__('Updated', 'master-addons' ),
					'Removed' => esc_html__('Removed', 'master-addons' ),
					'Changed' => esc_html__('Changed', 'master-addons' ),
					'Note' => esc_html__('Note', 'master-addons' ),
					'Info' => esc_html__('Info', 'master-addons' ),
					'Language' => esc_html__('Language', 'master-addons' ),
				]
			]
		);
		$repeater = new Repeater();

		$repeater->add_control(
			'ma_el_changelog_title',
			[
				'label'   => esc_html__('Title', 'master-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'Fixed',
				'options' => [
					'Added'  => esc_html__('Added', 'master-addons' ),
					'Fixed' => esc_html__('Fixed', 'master-addons' ),
					'Updated' => esc_html__('Updated', 'master-addons' ),
					'Removed' => esc_html__('Removed', 'master-addons' ),
					'Changed' => esc_html__('Changed', 'master-addons' ),
					'Note' => esc_html__('Note', 'master-addons' ),
					'Info' => esc_html__('Info', 'master-addons' ),
					'Language' => esc_html__('Language', 'master-addons' ),
				]
			]
		);

		$repeater->add_control(
			'ma_el_changelog_content',
			[
				'label'                 => __('Content', 'master-addons' ),
				'type'                  => Controls_Manager::TEXTAREA,
				'default'               => __(
					'Changelog Contents. If you want to link them, enable option below.',
					'master-addons'
				),
				'dynamic'               => [
					'active'   => true,
				],
			]
		);
		//
		//			$repeater->add_control(
		//				'ma_changelog_content_link',
		//				[
		//					'label'       => esc_html__( 'Content Link URL', 'master-addons' ),
		//					'type'        => Controls_Manager::URL,
		//					'label_block' => true,
		//					'default'     => [
		//						'url'         => '#',
		//						'is_external' => true,
		//					],
		//					'show_external' => true,
		//				]
		//			);

		$this->add_control(
			'changelog_tabs',
			[
				'type'                  => Controls_Manager::REPEATER,
				'default'               => [
					['ma_el_changelog_title' => esc_html__('Added', 'master-addons' )],
					['ma_el_changelog_title' => esc_html__('Fixed', 'master-addons' )],
				],
				'fields' 				=> $repeater->get_controls(),
				'title_field'           => '{{ma_el_changelog_title}}',
			]
		);

		$this->end_controls_section();

		/**
		 * Style Tab: Heading
		 */
		$this->start_controls_section(
			'ma_el_changelog_heading_style',
			[
				'label' => esc_html__('Heading', 'master-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ma_el_changelog_heading_color',
			[
				'label'     => esc_html__('Color', 'master-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jltma-changelog .jltma-changelog-heading' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ma_el_changelog_heading_typography',
				'selector' => '{{WRAPPER}} .jltma-changelog .jltma-changelog-heading',
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Style Tab: Title
		 */
		$this->start_controls_section(
			'ma_el_changelog_title_style',
			[
				'label' => esc_html__('Title', 'master-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ma_el_changelog_title_color',
			[
				'label'     => esc_html__('Color', 'master-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jltma-changelog .jltma-changelog-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ma_el_changelog_title_typography',
				'selector' => '{{WRAPPER}} .jltma-changelog .jltma-changelog-title',
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Style Tab: Content
		 */
		$this->start_controls_section(
			'ma_el_changelog_content_style',
			[
				'label' => esc_html__('Content', 'master-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ma_el_changelog_content_color',
			[
				'label'     => esc_html__('Color', 'master-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .jltma-changelog ul li' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ma_el_changelog_content_typography',
				'selector' => '{{WRAPPER}} .jltma-changelog ul li',
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->end_controls_section();

		// Help Docs section (links from config.php)
		$this->jltma_help_docs();

		$this->upgrade_to_pro_message();

	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();
?>

		<div id="jltma-changelog-<?php echo esc_attr($this->get_id()); ?>" class="jltma-changelog">
			<?php if ($settings['ma_el_changelog_heading']) { ?>
				<h2 class="jltma-changelog-heading">
					<?php echo esc_html($this->parse_text_editor($settings['ma_el_changelog_heading'])); ?>
				</h2>
			<?php } ?>

			<?php if ($settings['ma_el_changelog_main_title']) { ?>
				<h3 class="jltma-changelog-title">
					<?php echo esc_html($this->parse_text_editor($settings['ma_el_changelog_main_title'])); ?>
				</h3>
			<?php } ?>

			<?php foreach ($settings['changelog_tabs'] as $index => $tab) { ?>
				<ul>
					<li>
						<span class="jltma-label jltma-<?php echo esc_attr( strtolower($tab['ma_el_changelog_title']) ); ?>">
							<?php echo wp_kses_post( $this->parse_text_editor($tab['ma_el_changelog_title']) ); ?>
						</span>
						<?php echo wp_kses_post( $this->parse_text_editor($tab['ma_el_changelog_content']) ); ?>
					</li>
				</ul>

			<?php } ?>

		</div>

<?php
	}
}
