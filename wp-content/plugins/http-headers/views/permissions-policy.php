<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<tr valign="top">
    <th scope="row">Permissions-Policy
    	<p class="description"><?php esc_html_e('Permissions Policy is a web platform API which gives a website the ability to allow or block the use of browser features in its own frame or in iframes that it embeds.', 'http-headers'); ?></p>
        <hr>
        <p class="description"><?php esc_html_e('Read more at', 'http-headers'); ?>
            <a target="_blank" href="https://www.w3.org/TR/permissions-policy-1/"><?php esc_html_e('W3C Working Draft', 'http-headers'); ?></a>
        </p>
    </th>
	<td>
   		<fieldset>
    		<legend class="screen-reader-text">Permissions-Policy</legend>
        <?php
        $http_headers_permissions_policy = get_option('hh_permissions_policy', 0);
        foreach ($http_headers_bools as $http_headers_k => $http_headers_v)
        {
        	?><p><label><input type="radio" class="http-header" name="hh_permissions_policy" value="<?php echo esc_attr($http_headers_k); ?>"<?php checked($http_headers_permissions_policy, $http_headers_k, true); ?> /> <?php echo esc_html($http_headers_v); ?></label></p><?php
        }
        ?>
    	</fieldset>
    </td>
	<td>
    	<?php settings_fields( 'http-headers-pp' ); ?>
    	<?php do_settings_sections( 'http-headers-pp' ); ?>
		<table>
			<tbody>
			<?php 
			# https://github.com/w3c/webappsec-permissions-policy/blob/master/features.md
			$http_headers_features = array(
			    'accelerometer',
			    'ambient-light-sensor',
			    'autoplay',
			    'battery',
			    'camera',
			    'cross-origin-isolated',
			    'display-capture',
			    'document-domain',
			    'encrypted-media',
			    'execution-while-not-rendered',
			    'execution-while-out-of-viewport',
			    'fullscreen',
			    'geolocation',
			    'gyroscope',
			    'interest-cohort',
                'layout-animations',
                'legacy-image-formats',
			    'magnetometer',
			    'microphone',
			    'midi',
			    'navigation-override',
                'oversized-images',
			    'payment',
			    'picture-in-picture',
			    'publickey-credentials-get',
			    'screen-wake-lock',
			    'sync-script',
			    'sync-xhr',
			    'usb',
			    'vertical-scroll',
			    'web-share',
			    'wake-lock',
			    'xr-spatial-tracking',
			);
			$http_headers_origins = array('none', 'self', '*', 'origin(s)');
			
			$http_headers_permissions_policy_value = get_option('hh_permissions_policy_value');
			$http_headers_permissions_policy_feature = get_option('hh_permissions_policy_feature');
			$http_headers_permissions_policy_origin = get_option('hh_permissions_policy_origin');
			if (!$http_headers_permissions_policy_value)
			{
			    $http_headers_permissions_policy_value = array();
			}
			if (!$http_headers_permissions_policy_feature)
			{
			    $http_headers_permissions_policy_feature = array();
			}
			if (!$http_headers_permissions_policy_origin)
			{
			    $http_headers_permissions_policy_origin = array();
			}
			
			foreach ($http_headers_features as $http_headers_feature)
			{
				?>
				<tr>
					<td><input type="checkbox" name="hh_permissions_policy_feature[<?php echo esc_attr($http_headers_feature); ?>]" class="http-header-value"
						value="1"<?php echo !is_array($http_headers_permissions_policy_feature) || !array_key_exists($http_headers_feature, $http_headers_permissions_policy_feature) ? NULL : ' checked'; ?><?php echo $http_headers_permissions_policy == 1 ? NULL : ' readonly'; ?>></td>
        			<td><?php echo esc_html($http_headers_feature); ?></td>
        			<td>
        				<select name="hh_permissions_policy_value[<?php echo esc_attr($http_headers_feature); ?>]"
        					class="http-header-value"<?php echo $http_headers_permissions_policy == 1 ? NULL : ' readonly'; ?>>
        				<?php 
        				foreach ($http_headers_origins as $http_headers_origin)
        				{
        				    ?><option value="<?php echo esc_attr($http_headers_origin); ?>"<?php isset($http_headers_permissions_policy_value[$http_headers_feature]) ? selected($http_headers_permissions_policy_value[$http_headers_feature], $http_headers_origin) : NULL; ?>><?php echo esc_html($http_headers_origin); ?></option><?php
        				}
        				?>
        				</select>
        				<input type="text" name="hh_permissions_policy_origin[<?php echo esc_attr($http_headers_feature); ?>]"
        					value="<?php echo isset($http_headers_permissions_policy_origin[$http_headers_feature]) ? esc_attr( $http_headers_permissions_policy_origin[$http_headers_feature] ) : NULL; ?>" size="30"<?php echo isset($http_headers_permissions_policy_value[$http_headers_feature]) && in_array($http_headers_permissions_policy_value[$http_headers_feature], array('origin(s)', 'self')) ? NULL : ' style="display: none"'; ?>
        					class="http-header-value"<?php echo $http_headers_permissions_policy == 1 ? NULL : ' readonly'; ?>>
        			</td>
        		</tr>
				<?php
			}
			?>
        	</tbody>
		</table>
	</td>
	</td>
</tr>