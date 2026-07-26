<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<tr>
	<th scope="row">Expires
		<p class="description"><?php esc_html_e('The Expires header contains the date/time after which the response is considered stale.', 'http-headers'); ?></p>
		<p class="description"><?php esc_html_e('Invalid dates, like the value 0, represent a date in the past and mean that the resource is already expired.', 'http-headers'); ?></p>
	    <p class="description"><?php esc_html_e("If there is a Cache-Control header with the 'max-age' or 's-max-age' directive in the response, the Expires header is ignored.", 'http-headers'); ?></p>
		<p class="description"><?php esc_html_e('* Works only in Apache mode', 'http-headers'); ?></p>

        <hr>
        <p class="description"><?php esc_html_e('Read more at', 'http-headers'); ?>
            <a target="_blank" href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Expires"><?php esc_html_e('MDN Web Docs', 'http-headers'); ?></a>
        </p>
	</th>
	<td>
        <fieldset>
        	<legend class="screen-reader-text">Expires</legend>
	    <?php
        $http_headers_expires = get_option('hh_expires', 0);
        foreach ($http_headers_bools as $http_headers_k => $http_headers_v)
        {
        	?><p><label><input type="radio" class="http-header" name="hh_expires" value="<?php echo esc_attr($http_headers_k); ?>"<?php checked($http_headers_expires, $http_headers_k); ?> /> <?php echo esc_html($http_headers_v); ?></label></p><?php
        }
        ?>
		</fieldset>
	</td>
	<td>
		<?php settings_fields( 'http-headers-exp' ); ?>
		<?php do_settings_sections( 'http-headers-exp' ); ?>
		<table>
		<?php 
		$http_headers_types = array(
			'default',
			'text/css',
			'text/javascript',
			'text/plain',
			'image/gif',
			'image/png',
			'image/jpeg',
			'image/x-icon',
			'application/x-javascript',
			'application/javascript',
			'application/x-icon',
		);
		$http_headers_items = array(
			'invalid_0_date' => '0 (invalid date)',
			'access_1_hour' => 'Access +1 hour',
			'access_6_hours' => 'Access +6 hours',
			'access_12_hours' => 'Access +12 hours',
			'access_1_day' => 'Access +1 day',
			'access_3_days' => 'Access +3 days',
			'access_1_week' => 'Access +1 week',
			'access_2_weeks' => 'Access +2 weeks',
			'access_1_month' => 'Access +1 month',
			'access_3_months' => 'Access +3 months',
			'access_6_months' => 'Access +6 months',
			'access_1_year' => 'Access +1 year',
			'modification_1_hour' => 'Modification +1 hour',
			'modification_6_hours' => 'Modification +6 hours',
			'modification_12_hours' => 'Modification +12 hours',
			'modification_1_day' => 'Modification +1 day',
			'modification_3_days' => 'Modification +3 days',
			'modification_1_week' => 'Modification +1 week',
			'modification_2_weeks' => 'Modification +2 weeks',
			'modification_1_month' => 'Modification +1 month',
			'modification_3_months' => 'Modification +3 months',
			'modification_6_months' => 'Modification +6 months',
			'modification_1_year' => 'Modification +1 year',
		);
		$http_headers_expires_value = get_option('hh_expires_value');
		$http_headers_expires_type = get_option('hh_expires_type');
		if (!$http_headers_expires_value)
		{
			$http_headers_expires_value = array();
		}
		if (!$http_headers_expires_type)
		{
			$http_headers_expires_type = array();
		}
		foreach ($http_headers_types as $http_headers_type) {
			?>
			<tr>
				<td><input type="checkbox" class="http-header-value" name="hh_expires_type[<?php echo esc_attr($http_headers_type); ?>]" value="1"<?php echo !is_array($http_headers_expires_type) || !array_key_exists($http_headers_type, $http_headers_expires_type) ? NULL : ' checked'; ?><?php echo $http_headers_expires == 1 ? NULL : ' readonly'; ?>></td>
				<td><?php echo esc_html($http_headers_type); ?></td>
				<td>
					<select class="http-header-value" name="hh_expires_value[<?php echo esc_attr($http_headers_type); ?>]"<?php echo $http_headers_expires == 1 ? NULL : ' readonly'; ?>>
					<?php 
					foreach ($http_headers_items as $http_headers_k => $http_headers_v) {
					    $http_headers_val_type = !empty($http_headers_expires_value[$http_headers_type]) ? $http_headers_expires_value[$http_headers_type] : '';
					    ?><option value="<?php echo esc_attr($http_headers_k); ?>"<?php selected($http_headers_val_type, $http_headers_k); ?>><?php echo esc_html($http_headers_v); ?></option><?php
					}
					?>
					</select>
				</td>
			</tr>
			<?php 
		}
		?>
		</table>
	</td>
</tr>