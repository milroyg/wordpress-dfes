<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<tr valign="top">
	<th scope="row">Connection
		<p class="description"><?php esc_html_e('The Connection general header controls whether or not the network connection stays open after the current transaction finishes. If the value sent is keep-alive, the connection is persistent and not closed, allowing for subsequent requests to the same server to be done.', 'http-headers'); ?></p>
        <hr>
        <p class="description"><?php esc_html_e('Read more at', 'http-headers'); ?>
            <a target="_blank" href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Connection"><?php esc_html_e('MDN Web Docs', 'http-headers'); ?></a>
        </p>
	</th>
	<td>
		<fieldset>
			<legend class="screen-reader-text">Connection</legend>
			<?php
			$http_headers_connection = get_option('hh_connection', 0);
	        foreach ($http_headers_bools as $http_headers_k => $http_headers_v)
	        {
	        	?><p><label><input type="radio" class="http-header" name="hh_connection" value="<?php echo esc_attr($http_headers_k); ?>"<?php checked($http_headers_connection, $http_headers_k); ?> /> <?php echo esc_html($http_headers_v); ?></label></p><?php
	        }
	        ?>
        	</fieldset>
	</td>
	<td>
		<?php settings_fields( 'http-headers-con' ); ?>
		<?php do_settings_sections( 'http-headers-con' ); ?>
		<select name="hh_connection_value" class="http-header-value"<?php echo $http_headers_connection == 1 ? NULL : ' readonly'; ?>>
		<?php
		$http_headers_items = array('keep-alive', 'close');
		$http_headers_connection_value = get_option('hh_connection_value');
		foreach ($http_headers_items as $http_headers_item) {
			?><option value="<?php echo esc_attr($http_headers_item); ?>"<?php selected($http_headers_connection_value, $http_headers_item); ?>><?php echo esc_html($http_headers_item); ?></option><?php
		}
		?>
		</select>
	</td>
</tr>