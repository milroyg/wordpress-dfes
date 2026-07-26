<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<tr>
	<th scope="row">Clear-Site-Data
		<p class="description"><?php esc_html_e('The Clear-Site-Data header clears browsing data (cookies, storage, cache) associated with the requesting website. It allows web developers to have more control over the data stored locally by a browser for their origins.', 'http-headers'); ?></p>
        <hr>
        <p class="description"><?php esc_html_e('Read more at', 'http-headers'); ?>
            <a target="_blank" href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Clear-Site-Data"><?php esc_html_e('MDN Web Docs', 'http-headers'); ?></a>
        </p>
	</th>
	<td>
        <fieldset>
        	<legend class="screen-reader-text">Clear-Site-Data</legend>
	    <?php
        $http_headers_clear_site_data = get_option('hh_clear_site_data', 0);
        foreach ($http_headers_bools as $http_headers_k => $http_headers_v)
        {
        	?><p><label><input type="radio" class="http-header" name="hh_clear_site_data" value="<?php echo esc_attr($http_headers_k); ?>"<?php checked($http_headers_clear_site_data, $http_headers_k); ?> /> <?php echo esc_html($http_headers_v); ?></label></p><?php
        }
        ?>
		</fieldset>
	</td>
	<td>
		<?php settings_fields( 'http-headers-csd' ); ?>
		<?php do_settings_sections( 'http-headers-csd' ); ?>
		<?php 
		$http_headers_items = array(
			'cache' => 'bool',
			'clientHints' => 'bool',
			'cookies' => 'bool',
			'storage' => 'bool',
			'executionContexts' => 'bool',
			'*' => 'bool',
		);
		?>
		<table>
		<?php 
		$http_headers_clear_site_data_value = get_option('hh_clear_site_data_value');
		if (!$http_headers_clear_site_data_value)
		{
			$http_headers_clear_site_data_value = array();
		}
		foreach ($http_headers_items as $http_headers_item => $http_headers_type)
		{
			?>
			<tr>
				<td><label for="hh_clear_site_data_value_<?php echo esc_attr($http_headers_item); ?>">"<?php echo esc_html($http_headers_item); ?>"</label></td>
				<td><?php
				switch ($http_headers_type) {
					case 'bool':
						?><input type="checkbox" class="http-header-value" name="hh_clear_site_data_value[<?php echo esc_attr($http_headers_item); ?>]" id="hh_clear_site_data_value_<?php echo esc_attr($http_headers_item); ?>" value="1"<?php checked(array_key_exists($http_headers_item, $http_headers_clear_site_data_value), 1, true); ?>><?php
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