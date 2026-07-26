<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<ul class="hh-breadcrumbs">
	<li><a href="<?php echo esc_url(get_admin_url()); ?>options-general.php?page=http-headers"><?php esc_html_e('Dashboard', 'http-headers'); ?></a></li>
	<?php 
	if (isset($_GET['category']))
	{
		?><li><?php echo isset($http_headers_categories[$_GET['category']]) ? esc_html($http_headers_categories[sanitize_text_field(wp_unslash($_GET['category']))]) : 'Unknown'; ?></li><?php
	} elseif (isset($_GET['header'])) {
	    if (isset($http_headers_headers[$_GET['header']][2]))
        {
            ?><li><a href="<?php echo esc_url(get_admin_url()); ?>options-general.php?page=http-headers&amp;category=<?php echo esc_attr($http_headers_headers[sanitize_text_field(wp_unslash($_GET['header']))][2]); ?>"><?php echo isset($http_headers_categories[$http_headers_headers[$_GET['header']][2]]) ? esc_html($http_headers_categories[$http_headers_headers[sanitize_text_field(wp_unslash($_GET['header']))][2]]) : 'Unknown'; ?></a></li><?php
            ?><li><?php echo esc_html($http_headers_headers[sanitize_text_field(wp_unslash($_GET['header']))][0]); ?></li><?php
        }
	} elseif (isset($_GET['tab']) && $_GET['tab'] == 'advanced') {
		?><li><?php esc_html_e('Advanced settings', 'http-headers'); ?></li><?php
	} elseif (isset($_GET['tab']) && $_GET['tab'] == 'manual') {
	    ?><li><?php esc_html_e('Manual setup', 'http-headers'); ?></li><?php
	} elseif (isset($_GET['tab']) && $_GET['tab'] == 'inspect') {
		?><li><?php esc_html_e('Inspect headers', 'http-headers'); ?></li><?php
	}
	?>
</ul>