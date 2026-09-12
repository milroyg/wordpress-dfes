<?php

namespace MasterAddons\Inc\Admin\Templates\Includes\Sources;

use MasterAddons\Inc\Admin\Templates;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

class Api extends Base
{

	private $_object_cache = array();

	public function get_slug()
	{
		return 'master-api';
	}

	public function get_version()
	{

		$key     = 'jltma_' . $this->get_slug() . '_version';
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
		if (class_exists('MasterAddons\Inc\Classes\Template_Library_Cache')) {
			$cache_manager = \MasterAddons\Inc\Classes\Template_Library_Cache::get_instance();
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

	/**
	 * Fetch a single page of templates from the remote library.
	 *
	 * The library popup shows one screenful at a time and pages as the user
	 * scrolls, so it asks for one page rather than the whole type. Nothing is
	 * cached locally: the remote response is small (under 2 KB) and is served
	 * from the edge, which is cheaper than keeping a copy of it on every
	 * client site.
	 *
	 * @return array|false ['templates' => array, 'pagination' => array] or false.
	 */
	public function get_items_page($tab, $page = 1, $per_page = 0, $category = '', $search = '')
	{
		if (!$tab) {
			return false;
		}

		$api_url = Templates\master_addons_templates()->api->api_url('templates');

		if (!$api_url) {
			return false;
		}

		$query = array('page' => max(1, (int) $page));
		if ($per_page > 0) {
			$query['per_page'] = (int) $per_page;
		}

		// Narrowing is done by the API. Filtering used to mean fetching the
		// whole type and cutting it down here, which pulled hundreds of
		// kilobytes just to show a handful of cards.
		if ('' !== $category && 'all' !== $category) {
			$query['category'] = $category;
		}
		if ('' !== $search) {
			$query['search'] = $search;
		}

		$response = wp_remote_get(
			add_query_arg($query, $api_url . $tab),
			array(
				'timeout'   => 20,
				'sslverify' => false,
			)
		);

		if (is_wp_error($response)) {
			return false;
		}

		$body = json_decode(wp_remote_retrieve_body($response), true);

		if (!is_array($body) || empty($body['success']) || !isset($body['templates'])) {
			return false;
		}

		$templates = $this->expand_paged_items($body);

		return array(
			'templates'  => $templates,
			'pagination' => array(
				'current_page' => isset($body['page']) ? (int) $body['page'] : (int) $page,
				'total_pages'  => isset($body['pages']) ? (int) $body['pages'] : 1,
				'total_items'  => isset($body['total']) ? (int) $body['total'] : count($templates),
				'per_page'     => isset($body['per_page']) ? (int) $body['per_page'] : count($templates),
				'has_more'     => isset($body['page'], $body['pages']) ? ((int) $body['page'] < (int) $body['pages']) : false,
			),
		);
	}

	/**
	 * Paged responses send the uploads URL once and relative thumbnail paths
	 * per row, to stay inside the size budget. The views want absolute URLs
	 * and the same keys the unpaged response uses, so restore both here.
	 */
	private function expand_paged_items(array $body)
	{
		$base = isset($body['thumb_base']) ? $body['thumb_base'] : '';
		$out  = array();

		foreach ($body['templates'] as $template) {
			$thumbnail = isset($template['thumbnail']) ? $template['thumbnail'] : '';

			if ('' !== $thumbnail && '' !== $base && !preg_match('#^https?://#i', $thumbnail)) {
				$thumbnail = $base . ltrim($thumbnail, '/');
			}

			$template['thumbnail'] = $thumbnail;

			// The template views read 'preview' for the thumbnail image and
			// treat an empty 'url' as "no preview link", so both are filled in
			// from what the paged response carries.
			if (!isset($template['preview'])) {
				$template['preview'] = $thumbnail;
			}
			if (!isset($template['url'])) {
				$template['url'] = '';
			}
			if (!isset($template['dependencies'])) {
				$template['dependencies'] = array();
			}
			if (!isset($template['source'])) {
				$template['source'] = $this->get_slug();
			}
			// The detail pane reads this straight off the model; leaving it unset
			// gave the view `undefined` where it expected a string.
			if (!isset($template['notice'])) {
				$template['notice'] = '';
			}

			$out[] = $template;
		}

		return $out;
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
			if (class_exists('Template_Kit_Cache')) {
				$cache_manager = Template_Kit_Cache::get_instance();
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
		if (class_exists('MasterAddons\Inc\Classes\Template_Library_Cache')) {
			$cache_manager = \MasterAddons\Inc\Classes\Template_Library_Cache::get_instance();
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
		if (class_exists('MasterAddons\Inc\Classes\Template_Library_Cache')) {
			$cache_manager = \MasterAddons\Inc\Classes\Template_Library_Cache::get_instance();
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
			$tab = isset($_REQUEST['tab']) ? sanitize_key($_REQUEST['tab']) : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only tab parameter for template display
		}

		// Try enhanced file-based cache first
		if (class_exists('MasterAddons\Inc\Classes\Template_Library_Cache')) {
			$cache_manager = \MasterAddons\Inc\Classes\Template_Library_Cache::get_instance();
			$cached_template = $cache_manager->get_cached_template($id, $tab);

			// An entry with no content is a licence refusal an older build
			// wrote before it knew not to. Serving it back would keep the
			// template locked on a site that has since been licensed, so
			// refetch instead.
			$is_empty_refusal = is_array($cached_template) && empty($cached_template['content']);

			if ($cached_template !== false && !$is_empty_refusal) {
				return $cached_template;
			}
		}

		$license_key = \MasterAddons\Inc\Classes\Helper::jltma_template_license_key();

		$api_url = Templates\master_addons_templates()->api->api_url('template');


		if (!$api_url) {
			wp_send_json_success(array(
				'licenseError' => true,
			));
		}

		$query = array(
			'license' => $license_key,
			'url'     => urlencode(home_url('/')),
		);

		// A licensed site must say so, or the library answers with no content
		// and the import ends at "Activate your licence" on a site that has
		// one. The licence key alone is not enough to say it: a site licensed
		// through Freemius may hold no key this side can read, so send the
		// same flag the kit importer sends.
		if (\MasterAddons\Inc\Classes\Helper::jltma_can_use_pro_templates()) {
			$query['pro_enabled'] = 'true';
		}

		$request =  add_query_arg($query, $api_url . $id);

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
			// The library answers a pro request from an unlicensed site with an
			// empty body and these two flags. Dropping them left the importer
			// unable to tell "this needs a licence" from "this template is
			// missing", which is why an unlicensed import ran the whole way
			// through and then reported "Template content not found".
			'is_pro'           => !empty($body['is_pro']),
			'license_required' => !empty($body['license_required']),
			'content'          => $content
		);

		// Cache the successful result. A licence refusal is not one: caching it
		// would keep the template empty here even after the licence is entered.
		if (!empty($content) && class_exists('MasterAddons\Inc\Classes\Template_Library_Cache')) {
			$cache_manager = \MasterAddons\Inc\Classes\Template_Library_Cache::get_instance();
			$cache_manager->cache_template_data($id, $tab, $result);
		}

		return $result;
	}

	public function transient_lifetime()
	{
		return DAY_IN_SECONDS;
	}
}
