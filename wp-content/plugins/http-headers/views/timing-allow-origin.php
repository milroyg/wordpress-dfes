<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<tr>
	<th scope="row">Timing-Allow-Origin
		<p class="description"><?php esc_html_e('The Timing-Allow-Origin header indicates whether a resource provides the complete timing information. SEO tools use the Resource Timing API to analyze the speed and weight of your web page resources.', 'http-headers'); ?></p>
        <hr>
        <p class="description"><?php esc_html_e('Read more at', 'http-headers'); ?>
            <a target="_blank" href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Timing-Allow-Origin"><?php esc_html_e('MDN Web Docs', 'http-headers'); ?></a>
        </p>
	</th>
	<td>
	    <fieldset>
	    	<legend class="screen-reader-text">Timing-Allow-Origin</legend>
	        <?php
	        $http_headers_timing_allow_origin = get_option('hh_timing_allow_origin', 0);
	        foreach ($http_headers_bools as $http_headers_k => $http_headers_v)
	        {
	        	?><p><label><input type="radio" class="http-header" name="hh_timing_allow_origin" value="<?php echo esc_attr($http_headers_k); ?>"<?php checked($http_headers_timing_allow_origin, $http_headers_k); ?> /> <?php echo esc_html($http_headers_v); ?></label></p><?php
	        }
	        ?>
	    </fieldset>
	</td>
	<td>
		<?php settings_fields( 'http-headers-tao' ); ?>
		<?php do_settings_sections( 'http-headers-tao' ); ?>
		<select name="hh_timing_allow_origin_value" class="http-header-value"<?php echo $http_headers_timing_allow_origin == 1 ? NULL : ' readonly'; ?>>
		<?php
		$http_headers_items = array('*', 'origin');
		$http_headers_timing_allow_origin_value = get_option('hh_timing_allow_origin_value');
		foreach ($http_headers_items as $http_headers_item) {
			?><option value="<?php echo esc_attr($http_headers_item); ?>"<?php selected($http_headers_timing_allow_origin_value, $http_headers_item); ?>><?php echo esc_html($http_headers_item); ?></option><?php
		}
		?>
		</select>
		<input type="text" name="hh_timing_allow_origin_url" class="http-header-value" placeholder="http://domain.com" value="<?php echo esc_attr(get_option('hh_timing_allow_origin_url')); ?>" size="35"<?php echo $http_headers_timing_allow_origin == 1 && $http_headers_timing_allow_origin_value == 'origin' ? NULL : ' style="display: none" readonly'; ?> />
	</td>
</tr>