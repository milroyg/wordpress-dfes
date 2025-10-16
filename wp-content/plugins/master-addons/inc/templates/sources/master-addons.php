<?php

namespace MasterAddons\Inc\Templates\Sources;

use MasterAddons\Inc\Templates;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

class Master_Addons_Templates_Source_Local extends Master_Addons_Templates_Source_Base
{

	private $_object_cache = array();

	public function get_slug()
	{
		return 'master-addons';
	}

	public function get_version()
	{
		return '1.0.0';
	}

	public function get_items($tab = null)
	{
		if (!$tab) {
			return array();
		}

		$cached = $this->get_templates_cache();

		if (!empty($cached[$tab])) {
			return array_values($cached[$tab]);
		}

		$templates = $this->get_local_templates($tab);

		if (!$templates) {
			return array();
		}

		if (empty($cached)) {
			$cached = array();
		}

		$cached[$tab] = $templates;

		$this->set_templates_cache($cached);

		return $templates;
	}

	public function get_local_templates($tab)
	{
		// Get local templates from WordPress database
		$args = array(
			'post_type' => 'elementor_library',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'meta_query' => array(
				array(
					'key' => '_elementor_template_type',
					'value' => $this->map_tab_to_type($tab),
					'compare' => '='
				)
			)
		);

		$query = new \WP_Query($args);
		$templates = array();

		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
				$post_id = \get_the_ID();

				$templates[] = array(
					'template_id' => $this->id_prefix() . $post_id,
					'title' => \get_the_title(),
					'type' => \get_post_meta($post_id, '_elementor_template_type', true),
					'source' => $this->get_slug(),
					'thumbnail' => \get_the_post_thumbnail_url($post_id, 'medium'),
					'date' => \get_the_date(),
					'author' => \get_the_author()
				);
			}
		}

		\wp_reset_postdata();

		return $templates;
	}

	public function map_tab_to_type($tab)
	{
		// Map tab names to Elementor template types
		$mapping = array(
			'master_pages' => 'section',
			'master_headers' => 'header',
			'master_footers' => 'footer'
		);

		return isset($mapping[$tab]) ? $mapping[$tab] : 'section';
	}

	public function get_categories($tab = null)
	{
		if (!$tab) {
			return array();
		}

		$cached = $this->get_categories_cache();

		if (!empty($cached[$tab])) {
			return $this->prepare_categories($cached[$tab]);
		}

		$categories = $this->get_local_categories($tab);

		if (!$categories) {
			return array();
		}

		if (empty($cached)) {
			$cached = array();
		}

		$cached[$tab] = $categories;

		$this->set_categories_cache($cached);

		return $this->prepare_categories($categories);
	}

	public function get_local_categories($tab)
	{
		// Get categories from local templates
		$args = array(
			'post_type' => 'elementor_library',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'meta_query' => array(
				array(
					'key' => '_elementor_template_type',
					'value' => $this->map_tab_to_type($tab),
					'compare' => '='
				)
			)
		);

		$query = new \WP_Query($args);
		$categories = array();

		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
				$post_id = \get_the_ID();

				$template_categories = \wp_get_post_terms($post_id, 'elementor_library_category', array('fields' => 'all'));

				if (is_array($template_categories) && !is_wp_error($template_categories)) {
					foreach ($template_categories as $category) {
						if (is_object($category) && isset($category->slug) && isset($category->name)) {
							if (!isset($categories[$category->slug])) {
								$categories[$category->slug] = $category->name;
							}
						}
					}
				}
			}
		}

		\wp_reset_postdata();

		return $categories;
	}

	public function prepare_categories($categories)
	{
		$result = array();

		foreach ($categories as $slug => $title) {
			$result[] = array(
				'slug'  => $slug,
				'title' => $title,
			);
		}

		return $result;
	}

	public function get_keywords($tab = null)
	{
		if (!$tab) {
			return array();
		}

		$cached = $this->get_keywords_cache();

		if (!empty($cached[$tab])) {
			return $cached[$tab];
		}

		$keywords = $this->get_local_keywords($tab);

		if (!$keywords) {
			return array();
		}

		if (empty($cached)) {
			$cached = array();
		}

		$cached[$tab] = $keywords;

		$this->set_keywords_cache($cached);

		return $keywords;
	}

	public function get_local_keywords($tab)
	{
		// Get keywords from local templates
		$args = array(
			'post_type' => 'elementor_library',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'meta_query' => array(
				array(
					'key' => '_elementor_template_type',
					'value' => $this->map_tab_to_type($tab),
					'compare' => '='
				)
			)
		);

		$query = new \WP_Query($args);
		$keywords = array();

		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
				$post_id = \get_the_ID();

				$template_keywords = \wp_get_post_terms($post_id, 'elementor_library_tag', array('fields' => 'all'));

				if (is_array($template_keywords) && !is_wp_error($template_keywords)) {
					foreach ($template_keywords as $keyword) {
						if (is_object($keyword) && isset($keyword->name)) {
							if (!in_array($keyword->name, $keywords)) {
								$keywords[] = $keyword->name;
							}
						}
					}
				}
			}
		}

		\wp_reset_postdata();

		return $keywords;
	}

	public function get_item($template_id, $tab = false)
	{
		$id = str_replace($this->id_prefix(), '', $template_id);

		if (!$tab) {
			$tab = isset($_REQUEST['tab']) ? sanitize_key($_REQUEST['tab']) : false;
		}

		$post = \get_post($id);

		if (!$post || $post->post_type !== 'elementor_library') {
			return false;
		}

		$elementor_data = \get_post_meta($id, '_elementor_data', true);
		$template_type = \get_post_meta($id, '_elementor_template_type', true);

		if (empty($elementor_data)) {
			return false;
		}

		$content = json_decode($elementor_data, true);

		if (!empty($content)) {
			$content = $this->replace_elements_ids($content);
			$content = $this->process_export_import_content($content, 'on_import');
		}

		return array(
			'page_settings' => array(),
			'type' => $template_type,
			'license' => 'local',
			'content' => $content
		);
	}

	// Required methods from base class
	public function replace_elements_ids($content)
	{
		// Simple implementation - can be enhanced later
		return $content;
	}

	public function process_export_import_content($content, $method)
	{
		// Simple implementation - can be enhanced later
		return $content;
	}

	public function id_prefix()
	{
		return 'local_';
	}

	public function transient_lifetime()
	{
		return HOUR_IN_SECONDS; // Shorter cache for local templates
	}
}
