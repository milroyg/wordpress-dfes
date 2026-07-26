<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<tr valign="top">
    <th scope="row">X-Content-Type-Options
    	<p class="description"><?php esc_html_e('Prevents Internet Explorer and Google Chrome from MIME-sniffing a response away from the declared content-type. This also applies to Google Chrome, when downloading extensions. This reduces exposure to drive-by download attacks and sites serving user uploaded content that, by clever naming, could be treated by MSIE as executable or dynamic HTML files.', 'http-headers'); ?></p>
        <hr>
        <p class="description"><?php esc_html_e('Read more at', 'http-headers'); ?>
            <a target="_blank" href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Content-Type-Options"><?php esc_html_e('MDN Web Docs', 'http-headers'); ?></a>
        </p>
    </th>
    <td>
   		<fieldset>
    		<legend class="screen-reader-text">X-Content-Type-Options</legend>
        <?php
        $http_headers_x_content_type_options = get_option('hh_x_content_type_options', 0);
        foreach ($http_headers_bools as $http_headers_k => $http_headers_v)
        {
        	?><p><label><input type="radio" class="http-header" name="hh_x_content_type_options" value="<?php echo esc_attr($http_headers_k); ?>"<?php checked($http_headers_x_content_type_options, $http_headers_k); ?> /> <?php echo esc_html($http_headers_v); ?></label></p><?php
        }
        ?>
    	</fieldset>
    </td>
	<td>
		<?php settings_fields( 'http-headers-cto' ); ?>
		<?php do_settings_sections( 'http-headers-cto' ); ?>
		<select name="hh_x_content_type_options_value" class="http-header-value"<?php echo $http_headers_x_content_type_options == 1 ? NULL : ' readonly'; ?>>
		<?php
		$http_headers_items = array('nosniff');
		$http_headers_x_content_type_options_value = get_option('hh_x_content_type_options_value');
		foreach ($http_headers_items as $http_headers_item) {
			?><option value="<?php echo esc_attr($http_headers_item); ?>"<?php selected($http_headers_x_content_type_options_value, $http_headers_item); ?>><?php echo esc_html($http_headers_item); ?></option><?php
		}
		?>
		</select>
	</td>
</tr>