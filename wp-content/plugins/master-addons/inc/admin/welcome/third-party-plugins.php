<?php

namespace MasterAddons\Admin\Dashboard\Extensions;

use MasterAddons\Master_Elementor_Addons;
use MasterAddons\Inc\Helper\Master_Addons_Helper;
use MasterAddons\Admin\Dashboard\Addons\Extensions\JLTMA_Third_Party_Extensions;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 9/5/19
 */
?>

<h3><?php echo esc_html__('Third Party Plugins', 'master-addons'); ?></h3>

<div class="jltma-master-addons-features-container is-flex">
	<!-- Third Party Plugins -->
	<?php foreach (JLTMA_Third_Party_Extensions::$jltma_third_party_plugins['jltma-plugins']['plugin'] as $key => $jltma_plugins) {

		$plugin_file = $jltma_plugins['plugin_file'];
		$plugin_slug = $jltma_plugins['wp_slug'];
	?>

		<div class="jltma-master-addons-dashboard-checkbox">
			<div class="jltma-master-addons-dashboard-checkbox-content">

				<div class="jltma-master-addons-features-ribbon">
					<?php echo apply_filters('master_addons/addons/pro_ribbon', !empty($jltma_plugins['is_pro']) ? '<span class="jltma-pro-ribbon">Pro</span>' : '', $jltma_plugins); ?>
				</div>

				<div class="jltma-master-addons-content-inner">
					<div class="jltma-master-addons-features-title">
						<?php echo esc_html($jltma_plugins['title']); ?>
					</div> <!-- jltma_master_addons-features-title -->
					<div class="jltma-addons-tooltip inline-block">
						<?php
						if ($plugin_slug and $plugin_file) {
							if (Master_Addons_Helper::is_plugin_installed($plugin_slug, $plugin_file)) {
								if (!current_user_can('install_plugins')) {
									return;
								}
								if (!jltma_is_plugin_active($plugin_file)) {
									$activation_url = wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $plugin_file . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin_file);
									$html = '<a class="jltma-external-plugin-download jltma-external-plugin-download-active" href="' . $activation_url . '" ><span class="jltma-external-plugin-download jltma-external-plugin-download-active pr-1">' . esc_html__('Activate', 'master-addons') . '</span><i class="dashicons dashicons-yes-alt"></i></a>';
								} else {
									$html = '';
								}
							} else {

								$install_url = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . $plugin_slug), 'install-plugin_' . $plugin_slug);
								$html = '<a class="jltma-external-plugin-download" href="' . $install_url . '"><span class="jltma-external-plugin-download-text">' . esc_html__('Download', 'master-addons') . '</span><i class="dashicons dashicons-download"></i></a>';

								activate_plugin($plugin_file);
							}
							echo wp_kses_post($html);
						}
						?>
					</div>
				</div> <!-- .master-addons-el-title -->


				<div class="jltma-master-addons_feature-switchbox <?php echo apply_filters('master_addons/addons/pro_switchbox_class', '', $jltma_plugins); ?>">
					<label for="<?php echo esc_attr($jltma_plugins['key']); ?>" class="switch switch-text switch-primary switch-pill <?php echo apply_filters('master_addons/addons/pro_label_class', '', $jltma_plugins); ?>">
						<?php
						echo apply_filters(
							'master_addons/addons/pro_checkbox_render',
							'<input type="checkbox" id="' . esc_attr($jltma_plugins['key']) . '" class="jltma-switch-input" name="' . esc_attr($jltma_plugins['key']) . '" ' .
								(!empty($jltma_plugins['is_pro']) ? ' disabled' : checked(1, $this->jltma_get_third_party_plugins_settings[$jltma_plugins['key']], false)) . '>',
							$jltma_plugins,
							$this->jltma_get_third_party_plugins_settings[$jltma_plugins['key']]
						);
						?>
						<span data-on="On" data-off="Off" class="jltma-switch-label"></span>
						<span class="jltma-switch-handle"></span>
					</label>
				</div>
			</div>
		</div>
	<?php } ?>
</div>
