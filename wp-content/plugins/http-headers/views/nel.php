<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<tr valign="top">
    <th scope="row">NEL
    	<p class="description"><?php esc_html_e('Network Error Logging is a mechanism that can be configured via the NEL HTTP response header. This experimental header allows web sites and applications to opt-in to receive reports about failed (and, if desired, successful) network fetches from supporting browsers.', 'http-headers'); ?></p>
    	<hr>
        <p class="description"><?php esc_html_e('Read more at', 'http-headers'); ?>
            <a target="_blank" href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Network_Error_Logging"><?php esc_html_e('MDN Web Docs', 'http-headers'); ?></a>
        </p>
    </th>
	<td>
   		<fieldset>
    		<legend class="screen-reader-text">NEL</legend>
        <?php
        $http_headers_nel = get_option('hh_nel', 0);
        foreach ($http_headers_bools as $http_headers_k => $http_headers_v)
        {
        	?><p><label><input type="radio" class="http-header" name="hh_nel" value="<?php echo esc_attr($http_headers_k); ?>"<?php checked($http_headers_nel, $http_headers_k, true); ?> /> <?php echo esc_html($http_headers_v); ?></label></p><?php
        }
        ?>
    	</fieldset>
    </td>
	<td>
	<?php settings_fields( 'http-headers-nel' ); ?>
	<?php do_settings_sections( 'http-headers-nel' ); ?>
	<?php 
	$http_headers_nel_value = get_option('hh_nel_value', array());
	
	$http_headers_report_to = isset($http_headers_nel_value['report_to']) ? $http_headers_nel_value['report_to'] : NULL;
	$http_headers_max_age = isset($http_headers_nel_value['max_age']) ? $http_headers_nel_value['max_age'] : NULL;
	$http_headers_include_subdomains = isset($http_headers_nel_value['include_subdomains']) ? $http_headers_nel_value['include_subdomains'] : NULL;
	$http_headers_success_fraction = isset($http_headers_nel_value['success_fraction']) ? $http_headers_nel_value['success_fraction'] : NULL;
	$http_headers_failure_fraction = isset($http_headers_nel_value['failure_fraction']) ? $http_headers_nel_value['failure_fraction'] : NULL;
	$http_headers_request_headers = isset($http_headers_nel_value['request_headers']) ? $http_headers_nel_value['request_headers'] : NULL;
	$http_headers_response_headers = isset($http_headers_nel_value['response_headers']) ? $http_headers_nel_value['response_headers'] : NULL;
	?>
		<table>
			<tr>
				<td>report_to:</td>
				<td><input type="text" class="http-header-value" name="hh_nel_value[report_to]" value="<?php echo esc_attr($http_headers_report_to); ?>"<?php echo $http_headers_nel == 1 ? NULL : ' readonly'; ?>></td>
			</tr>
			<tr>
				<td>max_age:</td>
				<td><select name="hh_nel_value[max_age]" class="http-header-value"<?php echo $http_headers_nel == 1 ? NULL : ' readonly'; ?>>
				<?php
				$http_headers_items = array('3600' => '1 hour', '86400' => '1 day', '604800' => '7 days', '2592000' => '30 days', '5184000' => '60 days', '7776000' => '90 days', '31536000' => '1 year');
				foreach ($http_headers_items as $http_headers_key => $http_headers_item) {
				    ?><option value="<?php echo esc_attr($http_headers_key); ?>"<?php selected($http_headers_max_age, $http_headers_key); ?>><?php echo esc_html($http_headers_item); ?></option><?php
				}
				?>
				</select></td>
			</tr>
			<tr>
				<td>include_subdomains:</td>
				<td><input type="checkbox" class="http-header-value" name="hh_nel_value[include_subdomains]" value="1"<?php checked($http_headers_include_subdomains, 1, true); ?><?php echo $http_headers_nel == 1 ? NULL : ' readonly'; ?>></td>
			</tr>
			<tr>
				<td>success_fraction:</td>
				<td><input type="number" class="http-header-value" name="hh_nel_value[success_fraction]" value="<?php echo esc_attr($http_headers_success_fraction); ?>"<?php echo $http_headers_nel == 1 ? NULL : ' readonly'; ?> min="0.0" max="1.0" step="0.1"></td>
			</tr>
			<tr>
				<td>failure_fraction:</td>
				<td><input type="number" class="http-header-value" name="hh_nel_value[failure_fraction]" value="<?php echo esc_attr($http_headers_failure_fraction); ?>"<?php echo $http_headers_nel == 1 ? NULL : ' readonly'; ?> min="0.0" max="1.0" step="0.1"></td>
			</tr>
			<tr>
				<td>request_headers:</td>
				<td><input type="text" class="http-header-value" name="hh_nel_value[request_headers]" value="<?php echo esc_attr($http_headers_request_headers); ?>"<?php echo $http_headers_nel == 1 ? NULL : ' readonly'; ?>></td>
			</tr>
			<tr>
				<td>response_headers:</td>
				<td><input type="text" class="http-header-value" name="hh_nel_value[response_headers]" value="<?php echo esc_attr($http_headers_response_headers); ?>"<?php echo $http_headers_nel == 1 ? NULL : ' readonly'; ?>></td>
			</tr>
		</table>
	</td>
</tr>