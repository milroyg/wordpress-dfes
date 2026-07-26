<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<tr>
	<th scope="row">Custom headers
		<p class="description"><?php esc_html_e('Common non-standard response fields:', 'http-headers'); ?>
			<br>X-Pingback
			<br>X-Cache
			<br>X-Edge-Location
			<br>X-HTTP-Method-Override
			<br>X-Csrf-Token
			<br>X-Request-ID
			<br>X-Correlation-ID
			<br>X-Content-Duration
		</p>
	</th>
	<td>
		<fieldset>
			<legend class="screen-reader-text">Custom headers</legend>
        <?php
        $http_headers_custom_headers = get_option('hh_custom_headers', 0);
        foreach ($http_headers_bools as $http_headers_k => $http_headers_v)
        {
        	?><p><label><input type="radio" class="http-header" name="hh_custom_headers" value="<?php echo esc_attr($http_headers_k); ?>"<?php checked($http_headers_custom_headers, $http_headers_k); ?> /> <?php echo esc_html($http_headers_v); ?></label></p><?php
		}
		?>
		</fieldset>
	</td>
	<td>
	<?php settings_fields( 'http-headers-che' ); ?>
	<?php do_settings_sections( 'http-headers-che' ); ?>
	<?php
	$http_headers_custom_headers_value = get_option('hh_custom_headers_value');
	if (!is_array($http_headers_custom_headers_value)) {
		$http_headers_custom_headers_value = array();
	}
	?>
		<table>
			<thead>
				<tr>
					<th><?php esc_html_e('Header', 'http-headers'); ?></th>
					<th><?php esc_html_e('Value', 'http-headers'); ?></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
			<?php 
			if (empty($http_headers_custom_headers_value))
			{
				?>
				<tr>
					<td><input type="text" name="hh_custom_headers_value[name][]" class="http-header-value" placeholder="X-Custom-Name"></td>
					<td><input type="text" name="hh_custom_headers_value[value][]" class="http-header-value" placeholder="<?php esc_attr_e('Value', 'http-headers'); ?>"></td>
					<td></td>
				</tr>
				<?php 
			} else {
				foreach ($http_headers_custom_headers_value['name'] as $http_headers_key => $http_headers_name)
				{
					if (empty($http_headers_name) || empty($http_headers_custom_headers_value['value'][$http_headers_key]))
					{
						continue;
					}
					?>
					<tr>
						<td><input type="text" name="hh_custom_headers_value[name][]" class="http-header-value" placeholder="X-Custom-Name" value="<?php echo esc_attr($http_headers_name); ?>"<?php echo $http_headers_custom_headers == 1 ? NULL : ' readonly'; ?>></td>
						<td><input type="text" name="hh_custom_headers_value[value][]" class="http-header-value" placeholder="<?php esc_attr_e('Value', 'http-headers'); ?>" value="<?php echo esc_attr($http_headers_custom_headers_value['value'][$http_headers_key]); ?>"<?php echo $http_headers_custom_headers == 1 ? NULL : ' readonly'; ?>></td>
						<td><button type="button" class="button button-small hh-btn-delete-header" title="<?php esc_attr_e('Delete', 'http-headers'); ?>">x</button></td>
					</tr>
					<?php
				}
			}
			?>
				<tr>
					<td colspan="3"><button type="button" class="button" id="hh-btn-add-header">+ <?php esc_html_e('Add header', 'http-headers'); ?></button></td>
				</tr>
			</tbody>
		</table>
	</td>
</tr>