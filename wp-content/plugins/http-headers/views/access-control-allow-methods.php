<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<tr>
	<th scope="row">Access-Control-Allow-Methods
		<p class="description"><?php esc_html_e('The Access-Control-Allow-Methods header is returned by the server in a response to a preflight request and informs the browser about the HTTP methods that can be used in the actual request.', 'http-headers'); ?></p>
        <hr>
        <p class="description"><?php esc_html_e('Read more at', 'http-headers'); ?>
            <a target="_blank" href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Allow-Methods"><?php esc_html_e('MDN Web Docs', 'http-headers'); ?></a>
        </p>
	</th>
	<td>
		<fieldset>
			<legend class="screen-reader-text">Access-Control-Allow-Methods</legend>
        <?php
        $http_headers_access_control_allow_methods = get_option('hh_access_control_allow_methods', 0);
        foreach ($http_headers_bools as $http_headers_k => $http_headers_v)
        {
        	?><p><label><input type="radio" class="http-header" name="hh_access_control_allow_methods" value="<?php echo esc_attr($http_headers_k); ?>"<?php checked($http_headers_access_control_allow_methods, $http_headers_k); ?> /> <?php echo esc_html($http_headers_v); ?></label></p><?php
		}
		?>
		</fieldset>
	</td>
	<td>
	<?php settings_fields( 'http-headers-acam' ); ?>
	<?php do_settings_sections( 'http-headers-acam' ); ?>
	<?php
	$http_headers_items = array('*', 'GET', 'POST', 'OPTIONS', 'HEAD', 'PUT', 'DELETE', 'TRACE', 'CONNECT', 'PATCH');
	$http_headers_access_control_allow_methods_value = get_option('hh_access_control_allow_methods_value');
	if (!$http_headers_access_control_allow_methods_value)
	{
		$http_headers_access_control_allow_methods_value = array();
	}
	foreach ($http_headers_items as $http_headers_item)
	{
		?><p><label><input type="checkbox" class="http-header-value" name="hh_access_control_allow_methods_value[<?php echo esc_attr($http_headers_item); ?>]" value="1"<?php echo !array_key_exists($http_headers_item, $http_headers_access_control_allow_methods_value) ? NULL : ' checked'; ?><?php echo $http_headers_access_control_allow_methods == 1 ? NULL : ' readonly'; ?> /> <?php echo esc_html($http_headers_item); ?></label></p><?php
	}
	?>
	</td>
</tr>