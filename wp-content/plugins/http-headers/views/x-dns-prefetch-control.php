<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<tr valign="top">
    <th scope="row">X-DNS-Prefetch-Control
    	<p class="description"><?php esc_html_e('The X-DNS-Prefetch-Control HTTP response header controls DNS prefetching, a feature by which browsers proactively perform domain name resolution on both links that the user may choose to follow as well as URLs for items referenced by the document, including images, CSS, JavaScript, and so forth.', 'http-headers'); ?></p>
		<p class="description"><?php esc_html_e('This prefetching is performed in the background, so that the DNS is likely to have been resolved by the time the referenced items are needed. This reduces latency when the user clicks a link.', 'http-headers'); ?></p>
        <hr>
        <p class="description"><?php esc_html_e('Read more at', 'http-headers'); ?>
            <a target="_blank" href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-DNS-Prefetch-Control"><?php esc_html_e('MDN Web Docs', 'http-headers'); ?></a>
        </p>
    </th>
    <td>
   		<fieldset>
    		<legend class="screen-reader-text">X-DNS-Prefetch-Control</legend>
        <?php
        $http_headers_x_dns_prefetch_control = get_option('hh_x_dns_prefetch_control', 0);
        foreach ($http_headers_bools as $http_headers_k => $http_headers_v)
        {
        	?><p><label><input type="radio" class="http-header" name="hh_x_dns_prefetch_control" value="<?php echo esc_attr($http_headers_k); ?>"<?php checked($http_headers_x_dns_prefetch_control, $http_headers_k); ?> /> <?php echo esc_html($http_headers_v); ?></label></p><?php
        }
        ?>
    	</fieldset>
    </td>
	<td>
		<?php settings_fields( 'http-headers-xdpc' ); ?>
		<?php do_settings_sections( 'http-headers-xdpc' ); ?>
		<select name="hh_x_dns_prefetch_control_value" class="http-header-value"<?php echo $http_headers_x_dns_prefetch_control == 1 ? NULL : ' readonly'; ?>>
		<?php
		$http_headers_items = array('on', 'off');
		$http_headers_x_dns_prefetch_control_value = get_option('hh_x_dns_prefetch_control_value');
		foreach ($http_headers_items as $http_headers_item) {
			?><option value="<?php echo esc_attr($http_headers_item); ?>"<?php selected($http_headers_x_dns_prefetch_control_value, $http_headers_item); ?>><?php echo esc_html($http_headers_item); ?></option><?php
		}
		?>
		</select>
	</td>
</tr>