<?php

namespace MasterAddons\Admin\Dashboard\Extensions;

use MasterAddons\Master_Elementor_Addons;
use MasterAddons\Admin\Dashboard\Addons\Extensions\JLTMA_Addon_Extensions;
use MasterAddons\Inc\Helper\Master_Addons_Helper;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 9/5/19
 */
?>

<div class="jltma-master-addons-tab-panel" id="jltma-master-addons-extensions" style="display: none;">

	<div class="jltma-master-addons-features">

		<div class="jltma-tab-dashboard-wrapper">

			<form action="" method="POST" id="jltma-addons-extensions-settings" class="jltma-addons-tab-settings" name="jltma-addons-extensions-settings">

				<?php wp_nonce_field('jltma_extensions_settings_nonce_action'); ?>

				<div class="jltma-addons-dashboard-tabs-wrapper">

					<div id="jltma-addons-extensions" class="jltma-addons-dashboard-header-left">

						<div class="jltma-master-addons-features-list">

							<div class="jltma-master-addons-dashboard-filter float-right">

								<div class="jltma-filter-right">
									<button class="jltma-addons-enable-all">
										<?php echo esc_html__('Enable All', 'master-addons'); ?>
									</button>
									<button class="jltma-addons-disable-all">
										<?php echo esc_html__('Disable All', 'master-addons'); ?>
									</button>

									<div class="jltma-tab-dashboard-header-wrapper inline-block">
										<div class="jltma-tab-dashboard-header-right">
											<button type="submit" class="jltma-button jltma-tab-element-save-setting">
												<?php _e('Save Settings', 'master-addons'); ?>
											</button>
										</div>
									</div>
								</div>
							</div><!-- /.jltma_master_addons-dashboard-filter -->

							<!-- Master Addons Extensions -->
							<h3 class="mt-0"><?php echo esc_html__('Extensions', 'master-addons'); ?></h3>

							<div class="jltma-master-addons-features-container mt-0 is-flex">
								<?php foreach (JLTMA_Addon_Extensions::$jltma_extensions['jltma-extensions']['extension'] as $key => $extension) :
									// Check if Elementor Pro is active and this is a conflicting extension
									// $is_elementor_pro_active = defined('ELEMENTOR_PRO_VERSION');
									// $is_dynamic_tags = ($extension['key'] === 'dynamic-tags');
									// $is_custom_css = ($extension['key'] === 'custom-css');
									// $is_disabled_due_to_pro = ($is_elementor_pro_active && ($is_dynamic_tags || $is_custom_css));
									$is_disabled_due_to_pro = false;
								?>

									<div class="jltma-master-addons-dashboard-checkbox">
										<div class="jltma-master-addons-dashboard-checkbox-content">

											<div class="jltma-master-addons-features-ribbon">
												<?php echo apply_filters('master_addons/addons/pro_ribbon', !empty($extension['is_pro']) ? '<span class="jltma-pro-ribbon">Pro</span>' : '', $extension); ?>
												<?php if ($is_disabled_due_to_pro) {
													echo '<span class="jltma-pro-ribbon" style="background: #ff6b6b;">Disabled</span>';
												} ?>
											</div>

											<div class="jltma-master-addons-content-inner">
												<div class="jltma-master-addons-features-title">
													<?php echo esc_html__($extension['title']); ?>
													<?php if ($is_disabled_due_to_pro) : ?>
														<small style="display: block; color: #ff6b6b; font-size: 11px; margin-top: 5px;">
															<?php
															if ($is_dynamic_tags) {
																echo esc_html__('Disabled: Elementor Pro Dynamic Tags is active', 'master-addons');
															} elseif ($is_custom_css) {
																echo esc_html__('Disabled: Elementor Pro Custom CSS is active', 'master-addons');
															}
															?>
														</small>
													<?php endif; ?>
												</div> <!-- jltma_master_addons-features-title -->
												<div class="jltma-addons-tooltip inline-block">
													<?php
													Master_Addons_Helper::jltma_admin_tooltip_info('Demo', $extension['demo_url'], 'eicon-device-desktop');
													Master_Addons_Helper::jltma_admin_tooltip_info('Documentation', $extension['docs_url'], 'eicon-info-circle-o');
													Master_Addons_Helper::jltma_admin_tooltip_info('Video Tutorial', $extension['tuts_url'], 'eicon-video-camera');
													?>
												</div>
											</div> <!-- .jltma_master_addons-content-inner -->


											<div class="jltma-master-addons_feature-switchbox <?php echo apply_filters('master_addons/addons/pro_switchbox_class', '', $extension); ?>">
												<label for="<?php echo esc_attr($extension['key']); ?>" class="switch switch-text switch-primary switch-pill <?php echo apply_filters('master_addons/addons/pro_label_class', '', $extension); ?>">
													<?php
													$is_disabled = $is_disabled_due_to_pro || (!empty($extension['is_pro']));
													echo apply_filters(
														'master_addons/addons/pro_checkbox_render',
														'<input type="checkbox" id="' . esc_attr($extension['key']) . '" class="jltma-switch-input" name="' . esc_attr($extension['key']) . '" ' .
															($is_disabled ? ' disabled' : checked(1, $this->jltma_get_extension_settings[$extension['key']], false)) . '>',
														$extension,
														$this->jltma_get_extension_settings[$extension['key']]
													);
													?>
													<span data-on="On" data-off="Off" class="jltma-switch-label"></span>
													<span class="jltma-switch-handle"></span>
												</label>
											</div>
										</div>

									</div>

								<?php endforeach; ?>
							</div>


							<?php include_once JLTMA_PATH . 'inc/admin/welcome/third-party-plugins.php'; ?>

						</div> <!--  .master_addons_extensions-->

					</div>
				</div> <!-- .master-addons-el-dashboard-tabs-wrapper-->
			</form>
		</div>
	</div>
</div>
