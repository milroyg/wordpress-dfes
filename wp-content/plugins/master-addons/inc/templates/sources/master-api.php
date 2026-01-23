<?php

namespace MasterAddons\Inc\Templates\Sources;

use MasterAddons\Inc\Templates;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

class Master_Addons_Templates_Source_Api extends Master_Addons_Templates_Source_Base
{

	private $_object_cache = array();

	public function get_slug()
	{
		return 'master-api';
	}

	public function get_version()
	{

		$key     = $this->get_slug() . '_version';
		$version = get_transient($key);
		$version = false;

		if (!$version) {
			$version = Templates\master_addons_templates()->api->get_info('api_version');
			set_transient($key, $version, DAY_IN_SECONDS);
		}

		return $version;
	}

	public function get_items($tab = null)
	{

		if (!$tab) {

			return array();
		}

		// Try enhanced file-based cache first
		if (class_exists('MasterAddons\Inc\Templates\Classes\Master_Addons_Templates_Cache_Manager')) {
			$cache_manager = \MasterAddons\Inc\Templates\Classes\Master_Addons_Templates_Cache_Manager::get_instance();
			$cached_templates = $cache_manager->get_cached_templates($tab);
			
			if ($cached_templates !== false) {
				return array_values($cached_templates);
			}
		}

		// Fallback to existing transient cache
		$cached = $this->get_templates_cache();

		if (!empty($cached[$tab])) {

			return array_values($cached[$tab]);
		}

		$templates = $this->remote_get_templates($tab);

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

	public function prepare_items_tab($tab = '')
	{

		if (!empty($this->_object_cache[$tab])) {
			return $this->_object_cache[$tab];
		}

		$result = array(
			'templates'  => array(),
			'categories' => array(),
			'keywords'   => array(),
		);

		$templates_cache  = $this->get_templates_cache();
		$categories_cache = $this->get_categories_cache();
		$keywords_cache   = $this->get_keywords_cache();

		if (empty($templates_cache)) {
			$templates_cache = array();
		}

		if (empty($categories_cache)) {
			$categories_cache = array();
		}

		if (empty($keywords_cache)) {
			$keywords_cache = array();
		}

		$result['templates'] = $this->remote_get_templates($tab);
		$result['templates'] = $this->remote_get_categories($tab);
		$result['templates'] = $this->remote_get_keywords($tab);

		$templates_cache[$tab]  = $result['templates'];
		$categories_cache[$tab] = $result['categories'];
		$keywords_cache[$tab]   = $result['keywords'];

		$this->set_templates_cache($templates_cache);
		$this->set_categories_cache($categories_cache);
		$this->set_keywords_cache($keywords_cache);

		$this->_object_cache[$tab] = $result;

		return $result;
	}

	public function remote_get_templates($tab)
	{

		$api_url = Templates\master_addons_templates()->api->api_url('templates');

		if (!$api_url) {
			return false;
		}

		$response = wp_remote_get($api_url . $tab, array(
			'timeout'   => 60,
			'sslverify' => false
		));

		$body = wp_remote_retrieve_body($response);

		if (!$body) {
			return false;
		}

		$body = json_decode($body, true);

		if (!isset($body['success']) || true !== $body['success']) {
			return false;
		}

		if (empty($body['templates'])) {
			return false;
		}

		// Update thumbnail URLs to use cache folder first, then remote fallback
		foreach ($body['templates'] as &$template) {
			if (class_exists('JLTMA_Template_Kit_Cache')) {
				$cache_manager = JLTMA_Template_Kit_Cache::get_instance();
				$cached_thumbnail = $cache_manager->get_kit_thumbnail_url('', $template['title'], $template['thumbnail']);
				if ($cached_thumbnail) {
					$template['thumbnail'] = $cached_thumbnail;
				}
			}
		}

		return $body['templates'];
	}

	public function remote_get_categories($tab)
	{

		$api_url = Templates\master_addons_templates()->api->api_url('categories');

		if (!$api_url) {
			return false;
		}

		$response = wp_remote_get($api_url . $tab, array(
			'timeout'   => 60,
			'sslverify' => false
		));

		$body = wp_remote_retrieve_body($response);

		if (!$body) {
			return false;
		}

		$body = json_decode($body, true);

		if (!isset($body['success']) || true !== $body['success']) {
			return false;
		}

		if (empty($body['terms'])) {
			return false;
		}

		return $body['terms'];
	}

	public function remote_get_keywords($tab)
	{

		$api_url = Templates\master_addons_templates()->api->api_url('keywords');

		if (!$api_url) {
			return false;
		}

		$response = wp_remote_get($api_url . $tab, array(
			'timeout'   => 60,
			'sslverify' => false
		));

		$body = wp_remote_retrieve_body($response);

		if (!$body) {
			return false;
		}

		$body = json_decode($body, true);

		if (!isset($body['success']) || true !== $body['success']) {
			return false;
		}

		if (empty($body['terms'])) {
			return false;
		}

		return $body['terms'];
	}

	public function get_categories($tab = null)
	{

		if (!$tab) {
			return array();
		}

		// Try enhanced file-based cache first
		if (class_exists('MasterAddons\Inc\Templates\Classes\Master_Addons_Templates_Cache_Manager')) {
			$cache_manager = \MasterAddons\Inc\Templates\Classes\Master_Addons_Templates_Cache_Manager::get_instance();
			$cached_categories = $cache_manager->get_cached_categories($tab);
			
			if ($cached_categories !== false) {
				return $this->prepare_categories($cached_categories);
			}
		}

		// Fallback to existing transient cache
		$cached = $this->get_categories_cache();

		if (!empty($cached[$tab])) {
			return $this->prepare_categories($cached[$tab]);
		}

		$categories = $this->remote_get_categories($tab);

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

		// Try enhanced file-based cache first
		if (class_exists('MasterAddons\Inc\Templates\Classes\Master_Addons_Templates_Cache_Manager')) {
			$cache_manager = \MasterAddons\Inc\Templates\Classes\Master_Addons_Templates_Cache_Manager::get_instance();
			$cached_keywords = $cache_manager->get_cached_keywords($tab);
			
			if ($cached_keywords !== false) {
				return $cached_keywords;
			}
		}

		// Fallback to existing transient cache
		$cached = $this->get_keywords_cache();

		if (!empty($cached[$tab])) {
			return $cached[$tab];
		}

		$keywords = $this->remote_get_keywords($tab);

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

	public function get_item($template_id, $tab = false)
	{

		$id  = str_replace($this->id_prefix(), '', $template_id);

		if (!$tab) {
			$tab = isset($_REQUEST['tab']) ? sanitize_key($_REQUEST['tab']) : false;
		}

		// Try enhanced file-based cache first
		if (class_exists('MasterAddons\Inc\Templates\Classes\Master_Addons_Templates_Cache_Manager')) {
			$cache_manager = \MasterAddons\Inc\Templates\Classes\Master_Addons_Templates_Cache_Manager::get_instance();
			$cached_template = $cache_manager->get_cached_template($id, $tab);
			
			if ($cached_template !== false) {
				return $cached_template;
			}
		}

		$license_key = Templates\master_addons_templates()->config->get('key');

		$api_url = Templates\master_addons_templates()->api->api_url('template');


		if (!$api_url) {
			wp_send_json_success(array(
				'licenseError' => true,
			));
		}

		$request =  add_query_arg(
			array(
				'license' => $license_key,
				'url'     => urlencode(home_url('/')),
			),
			$api_url . $id
		);

		$response = wp_remote_get($request, array(
			'timeout'   => 60,
			'sslverify' => false
		));

		$body = wp_remote_retrieve_body($response);
		
		if (empty($body)) {
			wp_send_json_error(array(
				'message' => 'Empty API response',
			));
		}
		
		$body = json_decode($body, true);

		if (!$body || !isset($body['success'])) {
			wp_send_json_error(array(
				'message' => 'Invalid API response format',
				'response' => $body,
				'request_url' => $request
			));
		}

		$content = isset($body['content']) ? $body['content'] : '';
		// $content = isset($body['content']) ? sanitize_text_field($body['content']) : ''; // @not_sure

		$type    = isset($body['type']) ? sanitize_text_field($body['type']) : '';
		$license = isset($body['license']) ? sanitize_text_field($body['license']) : '';

		if (!empty($content)) {
			$content = $this->replace_elements_ids($content);
			$content = $this->process_export_import_content($content, 'on_import');
		}

		$result = array(
			'page_settings' => array(),
			'type'          => $type,
			'license'       => $license,
			'content'       => $content
		);

		// Cache the successful result
		if (class_exists('MasterAddons\Inc\Templates\Classes\Master_Addons_Templates_Cache_Manager')) {
			$cache_manager = \MasterAddons\Inc\Templates\Classes\Master_Addons_Templates_Cache_Manager::get_instance();
			$cache_manager->cache_template_data($id, $tab, $result);
		}

		return $result;
	}

	public function transient_lifetime()
	{
		return DAY_IN_SECONDS;
	}
}
