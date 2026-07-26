<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<tr valign="top">
    <th scope="row">Feature-Policy
    	<p class="description"><?php esc_html_e('With Feature Policy, you opt-in to a set of policies for the browser to enforce on specific features used throughout your site. These policies restrict what APIs the site can access or modify the browser\'s default behavior for certain features.', 'http-headers'); ?></p>
        <hr>
        <p class="description"><?php esc_html_e('Read more at', 'http-headers'); ?>
            <a target="_blank" href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Feature-Policy"><?php esc_html_e('MDN Web Docs', 'http-headers'); ?></a>
        </p>
    </th>
	<td>
   		<fieldset>
    		<legend class="screen-reader-text">Feature-Policy</legend>
        <?php
        $http_headers_feature_policy = get_option('hh_feature_policy', 0);
        foreach ($http_headers_bools as $http_headers_k => $http_headers_v)
        {
        	?><p><label><input type="radio" class="http-header" name="hh_feature_policy" value="<?php echo esc_attr($http_headers_k); ?>"<?php checked($http_headers_feature_policy, $http_headers_k, true); ?> /> <?php echo esc_html($http_headers_v); ?></label></p><?php
        }
        ?>
    	</fieldset>
    </td>
	<td>
    	<?php settings_fields( 'http-headers-fp' ); ?>
    	<?php do_settings_sections( 'http-headers-fp' ); ?>
		<table>
			<tbody>
			<?php 
			$http_headers_features = array(
			    'accelerometer',
			    'ambient-light-sensor',
			    'autoplay',
			    'camera',
			    'cookie',
			    'docwrite',
			    'domain',
			    'encrypted-media',
			    'fullscreen',
			    'geolocation',
			    'gyroscope',
			    'magnetometer',
			    'microphone',
			    'midi',
			    'payment',
			    'picture-in-picture',
			    'speaker',
			    'sync-script',
			    'sync-xhr',
			    'unsized-media',
			    'usb',
			    'vertical-scroll',
			    'vibrate',
			    'vr',
			);
			$http_headers_origins = array("'self'", "'none'", '*', 'origin(s)');
			
			$http_headers_feature_policy_value = get_option('hh_feature_policy_value');
			$http_headers_feature_policy_feature = get_option('hh_feature_policy_feature');
			$http_headers_feature_policy_origin = get_option('hh_feature_policy_origin');
			if (!$http_headers_feature_policy_value)
			{
			    $http_headers_feature_policy_value = array();
			}
			if (!$http_headers_feature_policy_feature)
			{
			    $http_headers_feature_policy_feature = array();
			}
			if (!$http_headers_feature_policy_origin)
			{
			    $http_headers_feature_policy_origin = array();
			}
			
			foreach ($http_headers_features as $http_headers_feature)
			{
				?>
				<tr>
					<td><input type="checkbox" name="hh_feature_policy_feature[<?php echo esc_attr($http_headers_feature); ?>]" class="http-header-value"
						value="1"<?php echo !is_array($http_headers_feature_policy_feature) || !array_key_exists($http_headers_feature, $http_headers_feature_policy_feature) ? NULL : ' checked'; ?><?php echo $http_headers_feature_policy == 1 ? NULL : ' readonly'; ?>></td>
        			<td><?php echo esc_html($http_headers_feature); ?></td>
        			<td>
        				<select name="hh_feature_policy_value[<?php echo esc_attr($http_headers_feature); ?>]"
        					class="http-header-value"<?php echo $http_headers_feature_policy == 1 ? NULL : ' readonly'; ?>>
        				<?php 
        				foreach ($http_headers_origins as $http_headers_origin)
        				{
        				    ?><option value="<?php echo esc_attr($http_headers_origin); ?>"<?php isset($http_headers_feature_policy_value[$http_headers_feature]) ? selected($http_headers_feature_policy_value[$http_headers_feature], $http_headers_origin) : NULL; ?>><?php echo esc_html($http_headers_origin); ?></option><?php
        				}
        				?>
        				</select>
        				<input type="text" name="hh_feature_policy_origin[<?php echo esc_attr($http_headers_feature); ?>]"
                               value="<?php echo isset($http_headers_feature_policy_origin[$http_headers_feature]) ? esc_attr($http_headers_feature_policy_origin[$http_headers_feature]) : NULL; ?>"
                               size="30"<?php echo isset($http_headers_feature_policy_value[$http_headers_feature]) && in_array($http_headers_feature_policy_value[$http_headers_feature], array('origin(s)', "'self'")) ? NULL : ' style="display: none"'; ?>
                               class="http-header-value"<?php echo $http_headers_feature_policy == 1 ? NULL : ' readonly'; ?>>
        			</td>
        		</tr>
				<?php
			}
			?>
        	</tbody>
		</table>
	</td>
</tr>