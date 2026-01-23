<?php

/**
 * Templates Loader View with Master Addons Logo
 */
?>
<div id="ma-el-modal-loading">
	<div class="elementor-loader-wrapper">
		<div class="ma-el-logo-container">
			<img src="<?php echo esc_url(JLTMA_URL . '/assets/images/logo.svg'); ?>" alt="Master Addons" class="ma-el-loading-logo">
		</div>
		<div class="elementor-loading-title"><?php echo esc_html__('Loading', 'master-addons'); ?></div>
	</div>
</div>
