<?php
/**
 * Templates Content View
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Get banner configuration
$banner_config = null;
if (class_exists('MasterAddons\\Inc\\Admin\\Templates\\Includes\\Classes\\Config')) {
    $config_instance = new \MasterAddons\Inc\Admin\Templates\Includes\Classes\Config();
    $banner_config = $config_instance->get('banner');
}
?>
<?php //if (isset($banner_config) && $banner_config['enabled']): ?>
<!-- <div class="ma-el-template-library-banner">
	<a href="<?php //echo esc_url($banner_config['url']); ?>" class="ma-el-template-library--banner" target="<?php //echo esc_attr($banner_config['target']); ?>">
		<img src="<?php //echo esc_url($banner_config['image']); ?>" alt="<?php //echo esc_attr($banner_config['alt']); ?>">
	</a>
</div> -->
<?php //endif; ?>

<div class="ma-el-template-filters-row">
	<div class="ma-el-filters-list"></div>
	<div class="ma-el-keywords-list"></div>
	<div class="ma-el-template-search-bar">
		<div class="ma-el-search-container">
			<input type="text" id="ma-el-template-search-input" placeholder="SEARCH TEMPLATES" class="ma-el-search-input">
			<i class="eicon-search ma-el-search-icon"></i>
		</div>
	</div>
</div>
<div class="ma-el-modal-templates-wrap">
	<div class="ma-el-templates-list"></div>
</div>
