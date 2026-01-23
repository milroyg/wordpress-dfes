<?php

namespace MasterAddons\Modules\DynamicTags\Tags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class JLTMA_Post_Content extends Tag
{
	public function get_name()
	{
		return 'jltma-post-content';
	}

	public function get_title()
	{
		return esc_html__('Post Content', 'master-addons' );
	}

	public function get_group()
	{
		return 'post';
	}

	public function get_categories()
	{
		return [TagsModule::TEXT_CATEGORY];
	}

	public function render()
	{
		// Get the post object
		$post = get_post();

		// Return empty if no post or no content
		if (!$post || empty($post->post_content)) {
			return;
		}

		// Get the content
		$content = $post->post_content;

		// If content contains Elementor data, show a placeholder message
		if (false !== strpos($content, '[elementor-template') || false !== strpos($content, 'data-elementor-type')) {
			echo esc_html__('This post is built with Elementor. Post content is not available in text format.', 'master-addons');
			return;
		}

		// Apply the_content filter to process shortcodes, embeds, etc.
		$content = apply_filters('the_content', $content);
		$content = str_replace(']]>', ']]&gt;', $content);

		echo wp_kses_post($content);
	}
}
