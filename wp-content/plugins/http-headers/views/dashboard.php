<?php 
if (!defined('ABSPATH')) {
    exit;
}
include dirname(__FILE__) . '/includes/config.inc.php';
?>
<div class="hh-wrapper">
	<div class="hh-categories">
	<?php
	$http_headers_tmp = array();
	foreach ($http_headers_headers as $http_headers_item)
	{
		if (!isset($http_headers_tmp[$http_headers_item[2]]))
		{
			$http_headers_tmp[$http_headers_item[2]] = array('total' => 0, 'on' => 0);
		}
		$http_headers_tmp[$http_headers_item[2]]['total'] += 1;
		if (get_option($http_headers_item[1]) == 1)
		{
			$http_headers_tmp[$http_headers_item[2]]['on'] += 1;
		}
	}
	foreach ($http_headers_categories as $http_headers_key => $http_headers_val)
	{
		?>
		<a href="<?php echo esc_url(get_admin_url()); ?>options-general.php?page=http-headers&amp;category=<?php echo esc_attr($http_headers_key); ?>" class="hh-category">
			<i></i>
    		<span><?php echo esc_html($http_headers_key[0]); ?></span>
			<strong><?php echo esc_html($http_headers_val); ?></strong><?php
            if (isset($http_headers_tmp[$http_headers_key]))
            {
                printf('(%u/%u)', esc_html($http_headers_tmp[$http_headers_key]['on']), esc_html($http_headers_tmp[$http_headers_key]['total']));
            }
            ?></a>
		<?php 
	}
	?>
    </div>

	<div class="hh-sidebar">
		<div class="hh-sidebar-inner">
			<h3><?php esc_html_e('Rate us', 'http-headers'); ?></h3>
			<p><?php esc_html_e('Tell us what you think about this plugin', 'http-headers'); ?> <a href="https://wordpress.org/support/plugin/http-headers/reviews/?rate=5#new-post"><?php esc_html_e('writing a review', 'http-headers'); ?></a>.</p>
			<h3><?php esc_html_e('Contribution', 'http-headers'); ?></h3>
			<p><?php esc_html_e('Help us to continue developing this plugin with a small donation.', 'http-headers'); ?></p>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
            	<input type="hidden" name="cmd" value="_xclick">
            	<input type="hidden" name="business" value="biggie@abv.bg">
            	<input type="hidden" name="item_name" value="HTTP Headers Donation">
            	<input type="hidden" name="no_shipping" value="1">
            	<input type="hidden" name="lc" value="US">
            	<input type="hidden" name="currency_code" value="USD">
            	<input type="hidden" name="item_number" value="">
            	$ <input type="text" name="amount" value="5" size="3">
            	<button type="submit" class="button"><?php esc_html_e('Donate', 'http-headers'); ?></button>
            </form>
		</div>
	</div>
</div>