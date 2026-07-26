<?php
if (!defined('ABSPATH')) {
	exit;
}
?>
<tr valign="top">
	<th scope="row">X-Robots-Tag
		<p class="description"><?php esc_html_e('The X-Robots-Tag HTTP header is used to indicate how a web page is to be indexed within public search engine results. The header is effectively equivalent to <code>&lt;meta name="robots" content="..."&gt;</code>.', 'http-headers'); ?></p>
		<hr>
		<p class="description"><?php esc_html_e('Read more at', 'http-headers'); ?>
			<a target="_blank" href="https://developers.google.com/search/docs/advanced/robots/robots_meta_tag"><?php esc_html_e('Google Search Central', 'http-headers'); ?></a>
		</p>
	</th>
	<td>
		<fieldset>
			<legend class="screen-reader-text">X-Robots-Tag</legend>
			<?php
			$http_headers_x_robots_tag = get_option('hh_x_robots_tag', 0);
			foreach ($http_headers_bools as $http_headers_k => $http_headers_v)
			{
				?><p><label><input type="radio" class="http-header" name="hh_x_robots_tag" value="<?php echo esc_attr($http_headers_k); ?>"<?php checked($http_headers_x_robots_tag, $http_headers_k); ?> /> <?php echo esc_html($http_headers_v); ?></label></p><?php
			}
			?>
		</fieldset>
	</td>
	<td>
		<?php settings_fields( 'http-headers-rob' ); ?>
		<?php do_settings_sections( 'http-headers-rob' ); ?>
		<?php
		$http_headers_items = array(
			'all' => 'bool',
			'noindex' => 'bool',
			'nofollow' => 'bool',
			'none' => 'bool',
			'noarchive' => 'bool',
			'nosnippet' => 'bool',
			'max-snippet' => 'number',
			'max-image-preview' => 'setting',
			'max-video-preview' => 'number',
			'notranslate' => 'bool',
			'noimageindex' => 'bool',
			'unavailable_after' => 'datetime',
		);
		?>
		<table>
			<?php
			$http_headers_x_robots_tag_value = get_option('hh_x_robots_tag_value');
			if (!$http_headers_x_robots_tag_value)
			{
				$http_headers_x_robots_tag_value = array();
			}
			foreach ($http_headers_items as $http_headers_item => $http_headers_type)
			{
				?>
				<tr>
					<td><label for="hh_x_robots_tag_value_<?php echo esc_attr($http_headers_item); ?>"><?php echo esc_html($http_headers_item); ?></label></td>
					<td><?php
						switch ($http_headers_type) {
							case 'bool':
								?><input type="checkbox" class="http-header-value" name="hh_x_robots_tag_value[<?php echo esc_attr($http_headers_item); ?>]"
								         id="hh_x_robots_tag_value_<?php echo esc_attr($http_headers_item); ?>"<?php echo $http_headers_x_robots_tag == 1 ? NULL : ' readonly'; ?>
								         value="1"<?php checked(array_key_exists($http_headers_item, $http_headers_x_robots_tag_value), 1, true); ?>><?php
								break;
							case 'number':
								?><input type="number" class="http-header-value" name="hh_x_robots_tag_value[<?php echo esc_attr($http_headers_item); ?>]"
								         id="hh_x_robots_tag_value_<?php echo esc_attr($http_headers_item); ?>"
								         size="6" min="-1" step="1"<?php echo $http_headers_x_robots_tag == 1 ? NULL : ' readonly'; ?>
								         value="<?php echo array_key_exists($http_headers_item, $http_headers_x_robots_tag_value) && strlen($http_headers_x_robots_tag_value[$http_headers_item]) > 0 ? (int) $http_headers_x_robots_tag_value[$http_headers_item] : NULL; ?>"><?php
								break;
							case 'setting':
								?><select class="http-header-value" name="hh_x_robots_tag_value[<?php echo esc_attr($http_headers_item); ?>]"
								          id="hh_x_robots_tag_value_<?php echo esc_attr($http_headers_item); ?>"<?php echo $http_headers_x_robots_tag == 1 ? NULL : ' readonly'; ?>>
									<option value="">---</option>
									<?php
									foreach (array('none', 'standard', 'large') as $http_headers_k)
									{
										?><option value="<?php echo esc_attr($http_headers_k); ?>"<?php echo array_key_exists($http_headers_item, $http_headers_x_robots_tag_value) && $http_headers_k == $http_headers_x_robots_tag_value[$http_headers_item] ? ' selected="selected"' : NULL; ?>><?php echo esc_html($http_headers_k); ?></option><?php
									}
									?>
								</select><?php
								break;
							case 'datetime':
								?><input type="date" class="http-header-value" name="hh_x_robots_tag_value[<?php echo esc_attr($http_headers_item); ?>]"
								         id="hh_x_robots_tag_value_<?php echo esc_attr($http_headers_item); ?>"<?php echo $http_headers_x_robots_tag == 1 ? NULL : ' readonly'; ?>
								         value="<?php echo array_key_exists($http_headers_item, $http_headers_x_robots_tag_value) && strlen($http_headers_x_robots_tag_value[$http_headers_item]) > 0 ? esc_attr($http_headers_x_robots_tag_value[$http_headers_item]) : NULL; ?>"><?php
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