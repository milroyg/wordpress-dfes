<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<tr valign="top">
	<th scope="row">Referrer-Policy
		<p class="description"><?php esc_html_e('The Referrer-Policy HTTP header governs which referrer information, sent in the Referer header, should be included with requests made.', 'http-headers'); ?></p>
        <hr>
        <p class="description"><?php esc_html_e('Read more at', 'http-headers'); ?>
            <a target="_blank" href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Referrer-Policy"><?php esc_html_e('MDN Web Docs', 'http-headers'); ?></a>
        </p>
	</th>
	<td>
   		<fieldset>
    		<legend class="screen-reader-text">Referrer-Policy</legend>
        <?php
        $http_headers_referrer_policy = get_option('hh_referrer_policy', 0);
        foreach ($http_headers_bools as $http_headers_k => $http_headers_v)
        {
        	?><p><label><input type="radio" class="http-header" name="hh_referrer_policy" value="<?php echo esc_attr($http_headers_k); ?>"<?php checked($http_headers_referrer_policy, $http_headers_k, true); ?> /> <?php echo esc_html($http_headers_v); ?></label></p><?php
        }
        ?>
    	</fieldset>
    </td>
	<td>
    	<?php settings_fields( 'http-headers-rp' ); ?>
		<?php do_settings_sections( 'http-headers-rp' ); ?>
		<select name="hh_referrer_policy_value" class="http-header-value"<?php echo $http_headers_referrer_policy == 1 ? NULL : ' readonly'; ?>>
		<?php
		$http_headers_items = array("", "no-referrer", "no-referrer-when-downgrade", "same-origin", "origin", "strict-origin", "origin-when-cross-origin", "strict-origin-when-cross-origin", "unsafe-url");
		$http_headers_referrer_policy_value = get_option('hh_referrer_policy_value');
		foreach ($http_headers_items as $http_headers_item) {
			?><option value="<?php echo esc_attr($http_headers_item); ?>"<?php selected($http_headers_referrer_policy_value, $http_headers_item); ?>><?php echo !empty($http_headers_item) ? esc_html($http_headers_item) : '(empty string)'; ?></option><?php
		}
		?>		
		</select>
	</td>
</tr>