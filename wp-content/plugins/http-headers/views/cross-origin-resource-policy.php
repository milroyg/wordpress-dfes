<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<tr>
	<th scope="row">Cross-Origin-Resource-Policy
		<p class="description"><?php esc_html_e('The HTTP Cross-Origin-Resource-Policy response header conveys a desire that the browser blocks no-cors cross-origin/cross-site requests to the given resource.', 'http-headers'); ?></p>
        <hr>
        <p class="description"><?php esc_html_e('Read more at', 'http-headers'); ?>
            <a target="_blank" href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cross-Origin-Resource-Policy"><?php esc_html_e('MDN Web Docs', 'http-headers'); ?></a>
        </p>
	</th>
	<td>
		<fieldset>
			<legend class="screen-reader-text">Cross-Origin-Resource-Policy</legend>
			<?php
            $http_headers_cross_origin_resource_policy = get_option('hh_cross_origin_resource_policy', 0);
			foreach ($http_headers_bools as $http_headers_k => $http_headers_v)
			{
				?><p><label><input type="radio" class="http-header" name="hh_cross_origin_resource_policy" value="<?php echo esc_attr($http_headers_k); ?>"<?php checked($http_headers_cross_origin_resource_policy, $http_headers_k); ?> /> <?php echo esc_html($http_headers_v); ?></label></p><?php
			}
			?>
		</fieldset>
	</td>
	<td>
		<?php settings_fields( 'http-headers-corp' ); ?>
		<?php do_settings_sections( 'http-headers-corp' ); ?>
        <select name="hh_cross_origin_resource_policy_value" class="http-header-value"<?php echo $http_headers_cross_origin_resource_policy == 1 ? NULL : ' readonly'; ?>>
            <?php
            $http_headers_items = array('same-site', 'same-origin', 'cross-origin');
            $http_headers_cross_origin_resource_policy_value = get_option('hh_cross_origin_resource_policy_value');
            foreach ($http_headers_items as $http_headers_item) {
                ?><option value="<?php echo esc_attr($http_headers_item); ?>"<?php selected($http_headers_cross_origin_resource_policy_value, $http_headers_item); ?>><?php echo esc_html($http_headers_item); ?></option><?php
            }
            ?>
        </select>
	</td>
</tr>