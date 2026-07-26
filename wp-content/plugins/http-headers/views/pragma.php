<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<tr valign="top">
	<th scope="row">Pragma
		<p class="description"><?php esc_html_e('The Pragma HTTP/1.0 general header is an implementation-specific header that may have various effects along the request-response chain. It is used for backwards compatibility with HTTP/1.0 caches where the Cache-Control HTTP/1.1 header is not yet present.', 'http-headers'); ?></p>
        <hr>
        <p class="description"><?php esc_html_e('Read more at', 'http-headers'); ?>
            <a target="_blank" href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Pragma"><?php esc_html_e('MDN Web Docs', 'http-headers'); ?></a>
        </p>
	</th>
	<td>
		<fieldset>
			<legend class="screen-reader-text">Pragma</legend>
			<?php
			$http_headers_pragma = get_option('hh_pragma', 0);
	        foreach ($http_headers_bools as $http_headers_k => $http_headers_v)
	        {
	        	?><p><label><input type="radio" class="http-header" name="hh_pragma" value="<?php echo esc_attr($http_headers_k); ?>"<?php checked($http_headers_pragma, $http_headers_k); ?> /> <?php echo esc_html($http_headers_v); ?></label></p><?php
	        }
	        ?>
        	</fieldset>
	</td>
	<td>
		<?php settings_fields( 'http-headers-pra' ); ?>
		<?php do_settings_sections( 'http-headers-pra' ); ?>
		<select name="hh_pragma_value" class="http-header-value"<?php echo $http_headers_pragma == 1 ? NULL : ' readonly'; ?>>
		<?php
		$http_headers_items = array('no-cache');
		$http_headers_pragma_value = get_option('hh_pragma_value');
		foreach ($http_headers_items as $http_headers_item) {
			?><option value="<?php echo esc_attr($http_headers_item); ?>"<?php selected($http_headers_pragma_value, $http_headers_item); ?>><?php echo esc_html($http_headers_item); ?></option><?php
		}
		?>
		</select>
	</td>
</tr>