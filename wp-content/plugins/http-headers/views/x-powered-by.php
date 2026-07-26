<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<tr valign="top">
	<th scope="row">X-Powered-By
		<p class="description"><?php esc_html_e('Specifies the technology (e.g. ASP.NET, PHP, JBoss, Express) supporting the web application, i.e. the scripting language. It is recommended to remove it or provide misleading information to throw off hackers that might target a particular technology/version.', 'http-headers'); ?></p>
	</th>
	<td>
		<fieldset>
			<legend class="screen-reader-text">X-Powered-By</legend>
			<?php
			$http_headers_x_powered_by = get_option ( 'hh_x_powered_by', 0 );
			foreach ( $http_headers_bools as $http_headers_k => $http_headers_v ) {
				?><p>
					<label><input type="radio" class="http-header" name="hh_x_powered_by" value="<?php echo esc_attr($http_headers_k); ?>" <?php checked($http_headers_x_powered_by, $http_headers_k, true); ?> /> <?php echo esc_html($http_headers_v); ?></label>
				</p><?php
			}
			?>
		</fieldset>
	</td>
	<td>
		<?php settings_fields( 'http-headers-xpb' ); ?>
		<?php do_settings_sections( 'http-headers-xpb' ); ?>
		<select name="hh_x_powered_by_option" class="http-header-value"<?php echo $http_headers_x_powered_by == 1 ? NULL : ' readonly'; ?>>
		<?php
		$http_headers_items = array (
			'unset' => 'Unset',
			'set' => 'Set',
		);
		$http_headers_x_powered_by_option = get_option ( 'hh_x_powered_by_option' );
		foreach ( $http_headers_items as $http_headers_k => $http_headers_v ) {
			?><option value="<?php echo esc_attr($http_headers_k); ?>" <?php selected($http_headers_x_powered_by_option, $http_headers_k); ?>><?php echo esc_html($http_headers_v); ?></option><?php
		}
		?>		
		</select>
		<input type="text" name="hh_x_powered_by_value" class="http-header-value" placeholder="PHP/<?php echo esc_attr(PHP_VERSION); ?>" value="<?php echo esc_attr(get_option('hh_x_powered_by_value')); ?>"
		<?php echo $http_headers_x_powered_by == 1 && $http_headers_x_powered_by_option == 'set' ? NULL : ' style="display: none" readonly'; ?> />
	</td>
</tr>