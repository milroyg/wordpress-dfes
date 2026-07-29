<?php
/*
* Master Addons : Welcome Screen by Jewel Theme
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Check if PRO version is active and set constants accordingly
if (\MasterAddons\Inc\Classes\Helper::jltma_premium() && defined('JLTMA_PRO') && defined('JLTMA_PRO_VER') && defined('JLTMA_PRO_PATH')) {
	$jltma_prefix = JLTMA_PRO;
	$jltma_ver = JLTMA_PRO_VER;
	$jltma_path = JLTMA_PRO_PATH;
} else {
	$jltma_prefix = JLTMA;
	$jltma_ver = JLTMA_VER;
	$jltma_path = JLTMA_PATH;
}
// JLTMA_IMAGE_DIR stays the same for both versions
$jltma_image_dir = JLTMA_IMAGE_DIR;

$jltma_white_label_setting 	= \MasterAddons\Inc\Classes\Utils::get_options('jltma_white_label_settings') ?? [];
if ( empty($jltma_white_label_setting) ) {
	$jltma_white_label_setting = apply_filters('jltma_white_label_default_options', array());
}

$jltma_hide_welcome 		     = \MasterAddons\Inc\Classes\Utils::check_options($jltma_white_label_setting['jltma_wl_plugin_tab_welcome'] ?? false);
$jltma_hide_addons 			     = \MasterAddons\Inc\Classes\Utils::check_options($jltma_white_label_setting['jltma_wl_plugin_tab_addons'] ?? false);
$jltma_hide_extensions 		  = \MasterAddons\Inc\Classes\Utils::check_options($jltma_white_label_setting['jltma_wl_plugin_tab_extensions'] ?? false);
$jltma_hide_icons_library 	= \MasterAddons\Inc\Classes\Utils::check_options($jltma_white_label_setting['jltma_wl_plugin_tab_icons_library'] ?? false);
$jltma_hide_api 			        = \MasterAddons\Inc\Classes\Utils::check_options($jltma_white_label_setting['jltma_wl_plugin_tab_api'] ?? false);
$jltma_hide_performance 	  = \MasterAddons\Inc\Classes\Utils::check_options($jltma_white_label_setting['jltma_wl_plugin_tab_performance'] ?? false);
$jltma_hide_white_label 	  = \MasterAddons\Inc\Classes\Utils::check_options($jltma_white_label_setting['jltma_wl_plugin_tab_white_label'] ?? false);
$jltma_hide_version 		     = \MasterAddons\Inc\Classes\Utils::check_options($jltma_white_label_setting['jltma_wl_plugin_tab_version'] ?? false);
// $jltma_hide_changelogs 		  = \MasterAddons\Inc\Classes\Utils::check_options($jltma_white_label_setting['jltma_wl_plugin_tab_changelogs'] ?? false);
$jltma_hide_system_info 	  = \MasterAddons\Inc\Classes\Utils::check_options($jltma_white_label_setting['jltma_wl_plugin_tab_system_info'] ?? false);

// Image Optimizer tab - only show if premium and extension is enabled
$jltma_hide_optimize = true; // Default hidden
$is_premium = \MasterAddons\Inc\Classes\Helper::jltma_premium();
// Allow local dev to see Pro features
if (defined('WP_DEBUG') && WP_DEBUG && (strpos(home_url() ?? '', '.local') !== false || strpos(home_url() ?? '', 'localhost') !== false)) {
	$is_premium = true;
}
if ($is_premium) {
	$extension_settings = \MasterAddons\Inc\Admin\Settings\Settings::get_extensions();
	if (!empty($extension_settings['image-optimizer'])) {
		$jltma_hide_optimize = \MasterAddons\Inc\Classes\Utils::check_options($jltma_white_label_setting['jltma_wl_plugin_tab_optimize'] ?? false);
	}
}
?>
<div class="jltma-master-addons-admin">
	<div class="jltma-master-addons-wrap">

		<header class="jltma-master-addons-header is-flex">
			<a class="jltma-master-addons-panel-logo" href="https://master-addons.com/?utm_source=dashboard&utm_medium=settings_header&utm_id=admin_dashboard" target="_blank">
				<?php
					$jltma_logo_image = apply_filters('master_addons/white_label/menu_logo', $jltma_image_dir . 'logo.svg');
					?>
				<img src="<?php echo esc_url($jltma_logo_image ); ?>" />
			</a>

			<h1 class="jltma-master-addons-title">
				<?php if (!empty($jltma_white_label_setting['jltma_wl_plugin_menu_label'])) {
					/* translators: 1: plugin name, 2: plugin version */
					printf( esc_html__('%1$s v %2$s', 'master-addons'), esc_html( $jltma_white_label_setting['jltma_wl_plugin_menu_label'] ), esc_html( $jltma_ver ) );
				} else {
					/* translators: 1: plugin name, 2: plugin version */
					printf( esc_html__('%1$s v %2$s', 'master-addons'), esc_html( $jltma_prefix ), esc_html( $jltma_ver ) );
				}
				?>
			</h1>

			<div class="jltma-master-addons-header-text"></div>
		</header>

		<?php require_once $jltma_path . 'inc/admin/welcome/navigation.php'; ?>

		<div class="jltma-master-addons-tab-contents">
			<?php
			if (isset($jltma_hide_welcome) && !$jltma_hide_welcome) {
				require $jltma_path . 'inc/admin/welcome/supports.php';
			}

			if (isset($jltma_hide_addons) && !$jltma_hide_addons) {
				require $jltma_path . 'inc/admin/welcome/addons.php';
			}

			if (isset($jltma_hide_extensions) && !$jltma_hide_extensions) {
				require $jltma_path . 'inc/admin/welcome/extensions.php';
			}

			if (isset($jltma_hide_icons_library) && !$jltma_hide_icons_library) {
				require $jltma_path . 'inc/admin/welcome/icons-library.php';
			}

			if (isset($jltma_hide_optimize) && !$jltma_hide_optimize) {
				require $jltma_path . 'inc/admin/welcome/optimize.php';
			}

			if (isset($jltma_hide_api) && !$jltma_hide_api) {
				require $jltma_path . 'inc/admin/welcome/api-keys.php';
			}

			if (isset($jltma_hide_performance) && !$jltma_hide_performance) {
				require $jltma_path . 'inc/admin/welcome/performance.php';
			}

			if (isset($jltma_hide_version) && !$jltma_hide_version) {
				require $jltma_path . 'inc/admin/welcome/version-control.php';
			}

			// if (isset($jltma_hide_changelogs) && !$jltma_hide_changelogs) {
			// 	require $jltma_path . 'inc/admin/welcome/changelogs.php';
			// }

			if (isset($jltma_hide_white_label) && !$jltma_hide_white_label) {
				require $jltma_path . 'inc/admin/welcome/white-label.php';
			}

			if (isset($jltma_hide_system_info) && !$jltma_hide_system_info) {
				require $jltma_path . 'inc/admin/welcome/system-info.php';
			}
			?>
		</div>

	</div>
</div>
