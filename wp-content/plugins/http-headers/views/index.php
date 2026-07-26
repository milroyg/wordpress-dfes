<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
	<h1>HTTP Headers</h1>
	<?php 
	$http_headers_check = http_headers_check_web_server_requirements();
	if ($http_headers_check !== true) {
	    ?>
	    <div class="notice notice-error">
	    	<h2><?php esc_html_e('Error!', 'http-headers'); ?></h2>
	    	<?php 
	    	if ($http_headers_check == -1) {
	    	    ?><p><?php esc_html_e('The following file was not found. Please make sure the file exists and has write permissions:', 'http-headers'); ?> <code><?php echo esc_html(http_headers_get_web_server_filename()); ?></code></p><?php
	    	} elseif ($http_headers_check == -2) {
	    	    ?><p><?php esc_html_e('Please make sure the following file has write permissions:', 'http-headers'); ?> <code><?php echo esc_html(http_headers_get_web_server_filename()); ?></code></p><?php
	    	}
	    	?>
	    </div>
	    <?php
	}
	$http_headers_check = http_headers_check_php_requirements();
	if ($http_headers_check !== true) {
	    ?>
	    <div class="notice notice-warning">
	    	<h2><?php esc_html_e('Warning!', 'http-headers'); ?></h2>
	    	<?php 
	    	if ($http_headers_check == -1) {
	    	    ?><p><?php esc_html_e('The following file was not found. Please make sure the file exists and has write permissions:', 'http-headers'); ?> <code><?php echo esc_html(http_headers_get_user_ini_filename()); ?></code></p><?php
	    	} elseif ($http_headers_check == -2) {
	    	    ?><p><?php esc_html_e('Please make sure the following file has write permissions:', 'http-headers'); ?> <code><?php echo esc_html(http_headers_get_user_ini_filename()); ?></code></p><?php
	    	}
	    	?>
	    </div>
	    <?php
	}
	?>
	<p><?php esc_html_e('Quick links', 'http-headers'); ?>:
		<a href="<?php echo esc_url(get_admin_url()); ?>options-general.php?page=http-headers&amp;tab=advanced"><?php esc_html_e('Advanced settings', 'http-headers'); ?></a>,
		<a href="<?php echo esc_url(get_admin_url()); ?>options-general.php?page=http-headers&amp;tab=manual"><?php esc_html_e('Manual setup', 'http-headers'); ?></a>,
		<a href="<?php echo esc_url(get_admin_url()); ?>options-general.php?page=http-headers&amp;tab=inspect"><?php esc_html_e('Inspect headers', 'http-headers'); ?></a>
	</p>
	<?php 
	if (isset($_GET['header']) && !empty($_GET['header']))
	{
		include dirname(__FILE__) . '/header.php';
	} elseif (isset($_GET['tab']) && $_GET['tab'] == 'advanced') {
		include dirname(__FILE__) . '/advanced.php';
	} elseif (isset($_GET['tab']) && $_GET['tab'] == 'manual') {
		include dirname(__FILE__) . '/manual.php';
	} elseif (isset($_GET['tab']) && $_GET['tab'] == 'inspect') {
		include dirname(__FILE__) . '/inspect.php';
	} elseif (isset($_GET['category'])) {
		include dirname(__FILE__) . '/category.php';
	} else {
		include dirname(__FILE__) . '/dashboard.php';
	}
	?>
</div>