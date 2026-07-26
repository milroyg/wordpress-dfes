<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<tr>
	<th scope="row">Vary
		<p class="description"><?php esc_html_e('The Vary HTTP response header determines how to match future request headers to decide whether a cached response can be used rather than requesting a fresh one from the origin server. It is used by the server to indicate which headers it used when selecting a representation of a resource in a content negotiation algorithm.', 'http-headers'); ?></p>
        <hr>
        <p class="description"><?php esc_html_e('Read more at', 'http-headers'); ?>
            <a target="_blank" href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Vary"><?php esc_html_e('MDN Web Docs', 'http-headers'); ?></a>
        </p>
	</th>
	<td>
		<fieldset>
			<legend class="screen-reader-text">Vary</legend>
			<?php
			$http_headers_vary = get_option('hh_vary', 0);
			foreach ($http_headers_bools as $http_headers_k => $http_headers_v)
			{
				?><p><label><input type="radio" class="http-header" name="hh_vary" value="<?php echo esc_attr($http_headers_k); ?>"<?php checked($http_headers_vary, $http_headers_k); ?> /> <?php echo esc_html($http_headers_v); ?></label></p><?php
			}
			?>
		</fieldset>
	</td>
	<td>
		<?php settings_fields( 'http-headers-vary' ); ?>
		<?php do_settings_sections( 'http-headers-vary' ); ?>
		<table>
            <tbody>
                <tr>
                    <td>
                    <?php
                    $http_headers_items = array(
                        '*', 'Accept-Encoding', 'User-Agent', 'Referer', 'Cookie',
                    );
                    $http_headers_vary_value = get_option('hh_vary_value');
                    if (!$http_headers_vary_value) {
                        $http_headers_vary_value = array();
                    }
                    foreach ($http_headers_items as $http_headers_item)
                    {
                        ?><p><label><input type="checkbox" class="http-header-value" name="hh_vary_value[<?php echo esc_attr($http_headers_item); ?>]" value="1"<?php echo !array_key_exists($http_headers_item, $http_headers_vary_value) ? NULL : ' checked'; ?><?php echo $http_headers_vary == 1 ? NULL : ' readonly'; ?> /> <?php echo esc_html($http_headers_item); ?></label></p><?php
                    }
                    ?>
                    </td>
                </tr>
            </tbody>
        </table>
	</td>
</tr>