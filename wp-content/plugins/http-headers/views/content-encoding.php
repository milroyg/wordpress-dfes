<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<tr>
	<th scope="row">Content-Encoding
		<p class="description"><?php esc_html_e('Compression is an important way to increase the performance of a Web site. For some documents, size reduction of up to 70% lowers the bandwidth capacity needs.', 'http-headers'); ?></p>
        <hr>
        <p class="description"><?php esc_html_e('Read more at', 'http-headers'); ?>
            <a target="_blank" href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Encoding"><?php esc_html_e('MDN Web Docs', 'http-headers'); ?></a>
        </p>
	</th>
	<td>
		<fieldset>
			<legend class="screen-reader-text">Content-Encoding</legend>
			<?php
			$http_headers_content_encoding = get_option('hh_content_encoding', 0);
			foreach ($http_headers_bools as $http_headers_k => $http_headers_v)
			{
				?><p><label><input type="radio" class="http-header" name="hh_content_encoding" value="<?php echo esc_attr($http_headers_k); ?>"<?php checked($http_headers_content_encoding, $http_headers_k); ?> /> <?php echo esc_html($http_headers_v); ?></label></p><?php
			}
			?>
		</fieldset>
	</td>
	<td>
		<?php settings_fields( 'http-headers-ce' ); ?>
		<?php do_settings_sections( 'http-headers-ce' ); ?>
		<table>
		<tbody>
		<tr>
			<th colspan="2"><?php esc_html_e('Module', 'http-headers'); ?></th>
		</tr>
		<?php 
		$http_headers_content_encoding_module = get_option('hh_content_encoding_module');
		?>
		<tr>
			<td colspan="2" class="hh-td-inner">
    			<table style="width: 100%">
    				<tbody>
        				<tr>
        					<td>
        						<label><input type="radio" name="hh_content_encoding_module" value="deflate"<?php echo $http_headers_content_encoding_module == 'deflate' || !$http_headers_content_encoding_module ? ' checked' : NULL; ?>> <?php esc_html_e('DEFLATE', 'http-headers'); ?></label>
        					</td>
        					<td>
        						<label><input type="radio" name="hh_content_encoding_module" value="brotli"<?php checked($http_headers_content_encoding_module, 'brotli'); ?>> <?php esc_html_e('BROTLI', 'http-headers'); ?></label>
        					</td>
        					<td>
        						<label><input type="radio" name="hh_content_encoding_module" value="brotli_deflate"<?php checked($http_headers_content_encoding_module, 'brotli_deflate'); ?>> <?php esc_html_e('BROTLI; DEFLATE', 'http-headers'); ?></label>
        					</td>
        				</tr>
    				</tbody>
    			</table>
			</td>
		</tr>
		<tr>
			<th colspan="2"><?php esc_html_e('By content type', 'http-headers'); ?></th>
		</tr><tr>
		<?php
		$http_headers_items = array(
			'application/javascript', 
			'application/x-javascript',
			'application/json', 
			'application/ld+json',
			'application/manifest+json',
			'application/rdf+xml',
			'application/rss+xml',
			'application/schema+json',
			'application/vnd.geo+json',
			'application/x-web-app-manifest+json',
			'application/vnd.ms-fontobject', 
			'application/x-font-ttf', 
			'application/xhtml+xml',
			'application/xml',
			'font/opentype',
			'font/eot',
			'image/bmp',
			'image/svg+xml',
			'image/x-icon',
			'image/vnd.microsoft.icon',
			'text/javascript',
			'text/css',
			'text/html',
			'text/plain',
			'text/x-component',
			'text/xml',
		);
		$http_headers_content_encoding_value = get_option('hh_content_encoding_value');
		if (!$http_headers_content_encoding_value) {
			$http_headers_content_encoding_value = array();
		}
		foreach ($http_headers_items as $http_headers_i => $http_headers_item) {
			if ($http_headers_i > 0 && $http_headers_i % 2 === 0) {
				?></tr><tr><?php
			}
			?><td><label><input type="checkbox" class="http-header-value" name="hh_content_encoding_value[<?php echo esc_attr($http_headers_item); ?>]" value="1"<?php echo !array_key_exists($http_headers_item, $http_headers_content_encoding_value) ? NULL : ' checked'; ?><?php echo $http_headers_content_encoding == 1 ? NULL : ' readonly'; ?> /> <?php echo esc_html($http_headers_item); ?></label></td><?php
		}
		?>
		</tr>
		
		<tr>
			<th colspan="2"><?php esc_html_e('By extension', 'http-headers'); ?></th>
		</tr>
		<tr>
		<?php
		$http_headers_content_encoding_ext = get_option('hh_content_encoding_ext');
		if (!$http_headers_content_encoding_ext) {
			$http_headers_content_encoding_ext = array();
		}
		$http_headers_items = array('php', 'html', 'js', 'css', 'json', 'xml', 'svg', 'txt', 'bmp', 'ico', 'ttf', 'otf', 'eot');
		foreach ($http_headers_items as $http_headers_i => $http_headers_item) {
			if ($http_headers_i > 0 && $http_headers_i % 2 === 0) {
				?></tr><tr><?php
			}
			?><td><label><input type="checkbox" class="http-header-value" name="hh_content_encoding_ext[<?php echo esc_attr($http_headers_item); ?>]" value="1"<?php echo !array_key_exists($http_headers_item, $http_headers_content_encoding_ext) ? NULL : ' checked'; ?><?php echo $http_headers_content_encoding == 1 ? NULL : ' readonly'; ?> /> *.<?php echo esc_html($http_headers_item); ?></label></td><?php
		}
		?>
		</tr>
		
		</tbody></table>
	</td>
</tr>