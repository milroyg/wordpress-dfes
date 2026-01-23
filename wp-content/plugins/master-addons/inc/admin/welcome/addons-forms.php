<?php

namespace MasterAddons\Admin\Dashboard\Addons;

use MasterAddons\Master_Elementor_Addons;
use MasterAddons\Admin\Dashboard\Addons\Elements\JLTMA_Addon_Forms;
use MasterAddons\Inc\Helper\Master_Addons_Helper;

// File is already included in dashboard-settings.php based on pro/free version
// include_once JLTMA_PATH . 'inc/admin/jltma-elements/ma-forms.php';
?>

<div class="jltma-master-addons-features-list">

	<h3><?php echo esc_html__('Form Addons', 'master-addons'); ?></h3>

	<div class="jltma-master-addons-features-container is-flex">
		<?php foreach (JLTMA_Addon_Forms::$jltma_forms['jltma-forms']['elements'] as $key => $widget) : ?>

			<div class="jltma-master-addons-dashboard-checkbox">
				<div class="jltma-master-addons-dashboard-checkbox-content">

					<div class="jltma-master-addons-features-ribbon">
						<?php echo apply_filters('master_addons/addons/pro_ribbon', !empty($widget['is_pro']) ? '<span class="jltma-pro-ribbon">Pro</span>' : '', $widget); ?>
					</div>

					<div class="jltma-master-addons-content-inner">
						<div class="jltma-master-addons-features-title">
							<?php echo esc_html__($widget['title']); ?>
						</div> <!-- master-addons-el-title-content -->


						<div class="jltma-addons-tooltip inline-block">

							<?php
							Master_Addons_Helper::jltma_admin_tooltip_info('Demo', $widget['demo_url'], 'eicon-device-desktop');
							Master_Addons_Helper::jltma_admin_tooltip_info('Documentation', $widget['docs_url'], 'eicon-info-circle-o');
							Master_Addons_Helper::jltma_admin_tooltip_info('Video Tutorial', $widget['tuts_url'], 'eicon-video-camera');
							?>

						</div>

					</div> <!-- .master-addons-el-title -->

					<div class="jltma-master-addons_feature-switchbox <?php echo apply_filters('master_addons/addons/pro_switchbox_class', '', $widget); ?>">
						<label for="<?php echo esc_attr($widget['key']); ?>" class="switch switch-text switch-primary switch-pill <?php echo apply_filters('master_addons/addons/pro_label_class', '', $widget); ?>">
							<?php
							echo apply_filters(
								'master_addons/addons/pro_checkbox_render',
								'<input type="checkbox" id="' . esc_attr($widget['key']) . '" class="jltma-switch-input" name="' . esc_attr($widget['key']) . '" ' .
									(!empty($widget['is_pro']) ? ' disabled' : checked(1, $this->jltma_get_element_settings[$widget['key']], false)) . '>',
								$widget,
								$this->jltma_get_element_settings[$widget['key']]
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

</div> <!--  .master_addons_feature-->
