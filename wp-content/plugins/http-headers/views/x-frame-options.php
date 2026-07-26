<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<tr valign="top">
	<th scope="row">X-Frame-Options
		<p class="description"><?php esc_html_e('This header can be used to indicate whether or not a browser should be allowed to render a page in a &lt;frame&gt;, &lt;iframe&gt; or &lt;object&gt;. Use this to avoid clickjacking attacks.', 'http-headers'); ?></p>
        <hr>
        <p class="description"><?php esc_html_e('Read more at', 'http-headers'); ?>
            <a target="_blank" href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Frame-Options"><?php esc_html_e('MDN Web Docs', 'http-headers'); ?></a>
        </p>
	</th>
	<td>
		<fieldset>
			<legend class="screen-reader-text">X-Frame-Options</legend>
			<?php
			$http_headers_x_frame_options = get_option('hh_x_frame_options', 0);
			foreach ($http_headers_bools as $http_headers_k => $http_headers_v)
			{
				?><p><label><input type="radio" class="http-header" name="hh_x_frame_options" value="<?php echo esc_attr($http_headers_k); ?>"<?php checked($http_headers_x_frame_options, $http_headers_k, true); ?> /> <?php echo esc_html($http_headers_v); ?></label></p><?php
			}
			?>
		</fieldset>
	</td>
	<td>
		<?php settings_fields( 'http-headers-xfo' ); ?>
		<?php do_settings_sections( 'http-headers-xfo' ); ?>
		<select name="hh_x_frame_options_value" class="http-header-value"<?php echo $http_headers_x_frame_options == 1 ? NULL : ' readonly'; ?>>
		<?php
		$http_headers_items = array('deny', 'sameorigin', 'allow-from');
		$http_headers_x_frame_options_value = get_option('hh_x_frame_options_value');
		foreach ($http_headers_items as $http_headers_item)
		{
			?><option value="<?php echo esc_attr($http_headers_item); ?>"<?php selected($http_headers_x_frame_options_value, $http_headers_item); ?>><?php echo esc_html(strtoupper($http_headers_item)); ?></option><?php
		}
		?>		
		</select>
		<input type="text" name="hh_x_frame_options_domain" class="http-header-value" placeholder="Domain" value="<?php echo esc_attr(get_option('hh_x_frame_options_domain')); ?>"<?php echo $http_headers_x_frame_options == 1 && $http_headers_x_frame_options_value == 'allow-from' ? NULL : ' style="display: none" readonly'; ?> />
	</td>
</tr>