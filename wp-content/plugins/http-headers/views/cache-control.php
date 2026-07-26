<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<tr>
	<th scope="row">Cache-Control
		<p class="description"><?php esc_html_e('The Cache-Control general-header field is used to specify directives for caching mechanisms in both, requests and responses. Caching directives are unidirectional, meaning that a given directive in a request is not implying that the same directive is to be given in the response.', 'http-headers'); ?></p>
        <hr>
        <p class="description"><?php esc_html_e('Read more at', 'http-headers'); ?>
            <a target="_blank" href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control"><?php esc_html_e('MDN Web Docs', 'http-headers'); ?></a>
        </p>
	</th>
	<td>
        <fieldset>
        	<legend class="screen-reader-text">Cache-Control</legend>
	    <?php
        $http_headers_cache_control = get_option('hh_cache_control', 0);
        foreach ($http_headers_bools as $http_headers_k => $http_headers_v)
        {
        	?><p><label><input type="radio" class="http-header" name="hh_cache_control" value="<?php echo esc_attr($http_headers_k); ?>"<?php checked($http_headers_cache_control, $http_headers_k); ?> /> <?php echo esc_html($http_headers_v); ?></label></p><?php
        }
        ?>
		</fieldset>
	</td>
	<td>
		<?php settings_fields( 'http-headers-cc' ); ?>
		<?php do_settings_sections( 'http-headers-cc' ); ?>
		<?php 
		$http_headers_items = array(
			'must-revalidate' => 'bool',
			'no-cache' => 'bool',
			'no-store' => 'bool',
			'no-transform' => 'bool',
			'public' => 'bool',
			'private' => 'bool',
			'proxy-revalidate' => 'bool',
			'max-age' => 'int',
			's-maxage' => 'int',
            'immutable' => 'bool',
            'stale-while-revalidate' => 'int',
            'stale-if-error' => 'int',
		);
		?>
		<table>
		<?php 
		$http_headers_cache_control_value = get_option('hh_cache_control_value');
		if (!$http_headers_cache_control_value)
		{
			$http_headers_cache_control_value = array();
		}
		foreach ($http_headers_items as $http_headers_item => $http_headers_type)
		{
			?>
			<tr>
				<td><label for="hh_cache_control_value_<?php echo esc_attr($http_headers_item); ?>"><?php echo esc_html($http_headers_item); ?></label></td>
				<td><?php
				switch ($http_headers_type) {
					case 'bool':
						?><input type="checkbox" class="http-header-value" name="hh_cache_control_value[<?php echo esc_attr($http_headers_item); ?>]" id="hh_cache_control_value_<?php echo esc_attr($http_headers_item); ?>" value="1"<?php checked(array_key_exists($http_headers_item, $http_headers_cache_control_value), 1, true); ?>><?php
						break;
					case 'int':
						?><input type="text" class="http-header-value" name="hh_cache_control_value[<?php echo esc_attr($http_headers_item); ?>]" id="hh_cache_control_value_<?php echo esc_attr($http_headers_item); ?>" size="6" value="<?php echo array_key_exists($http_headers_item, $http_headers_cache_control_value) && strlen($http_headers_cache_control_value[$http_headers_item]) > 0 ? (int) $http_headers_cache_control_value[$http_headers_item] : NULL; ?>"> <?php esc_html_e('seconds', 'http-headers');
						break;
				}
				?>	
				</td>
			</tr>
			<?php 
		}
		?>
		</table>
	</td>
</tr>