<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<tr>
	<th scope="row">Access-Control-Expose-Headers
		<p class="description"><?php esc_html_e('The Access-Control-Expose-Headers response header brings information about headers that browsers could allow accessing.', 'http-headers'); ?></p>
        <hr>
        <p class="description"><?php esc_html_e('Read more at', 'http-headers'); ?>
            <a target="_blank" href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Expose-Headers"><?php esc_html_e('MDN Web Docs', 'http-headers'); ?></a>
        </p>
	</th>
	<td>
		<fieldset>
			<legend class="screen-reader-text">Access-Control-Expose-Headers</legend>
			<?php
	        $http_headers_access_control_expose_headers = get_option('hh_access_control_expose_headers', 0);
	        foreach ($http_headers_bools as $http_headers_k => $http_headers_v)
	        {
	        	?><p><label><input type="radio" class="http-header" name="hh_access_control_expose_headers" value="<?php echo esc_attr($http_headers_k); ?>"<?php checked($http_headers_access_control_expose_headers, $http_headers_k); ?> /> <?php echo esc_html($http_headers_v); ?></label></p><?php
	        }
	        ?>
    	</fieldset>
	</td>
	<td>
		<?php settings_fields( 'http-headers-aceh' ); ?>
		<?php do_settings_sections( 'http-headers-aceh' ); ?>

		<?php
		$http_headers_access_control_expose_headers_value = get_option('hh_access_control_expose_headers_value');
		if (!$http_headers_access_control_expose_headers_value)
		{
			$http_headers_access_control_expose_headers_value = array();
		}
		?>
        <table><tbody><tr>
		<?php
		$http_headers_i = 0;
		array_unshift($http_headers_list, '*');
		foreach ($http_headers_list as $http_headers_item) {
            if (in_array($http_headers_item, $http_headers_cors_safe_response_headers) || in_array($http_headers_item, $http_headers_cors_safe_request_headers))
            {
                continue;
            }
			if ($http_headers_i % 3 === 0) {
				?></tr><tr><?php
			}
			?><td><label><input type="checkbox" class="http-header-value" name="hh_access_control_expose_headers_value[<?php echo esc_attr($http_headers_item); ?>]" value="1"<?php echo !array_key_exists($http_headers_item, $http_headers_access_control_expose_headers_value) ? NULL : ' checked'; ?><?php echo $http_headers_access_control_expose_headers == 1 ? NULL : ' readonly'; ?> /> <?php echo esc_html($http_headers_item); ?></label></td><?php
            $http_headers_i += 1;
		}
		?>
		</tr>
        </tbody></table>
        <table><tbody>
            <?php
            $http_headers_access_control_expose_headers_custom = get_option('hh_access_control_expose_headers_custom');
            if (is_array($http_headers_access_control_expose_headers_custom))
            {
                foreach ($http_headers_access_control_expose_headers_custom as $http_headers_header)
                {
                    ?>
                    <tr>
                        <td><input type="text" name="hh_access_control_expose_headers_custom[]" class="http-header-value" size="35" value="<?php echo esc_attr($http_headers_header); ?>"<?php echo $http_headers_access_control_expose_headers == 1 ? NULL : ' readonly'; ?> /></td>
                        <td><button type="button" class="button button-small hh-btn-delete-ac" title="<?php esc_attr_e('Delete', 'http-headers'); ?>">x</button></td>
                    </tr>
                    <?php
                }
            }
            ?>
            <tr>
                <td colspan="2">
                    <button type="button" class="button hh-btn-add-ac" data-name="hh_access_control_expose_headers_custom[]">+ <?php esc_html_e('Add header', 'http-headers'); ?></button>
                </td>
            </tr>
        </tbody></table>
	</td>
</tr>