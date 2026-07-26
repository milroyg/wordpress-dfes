<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<tr valign="top">
    <th scope="row">X-Download-Options
    	<p class="description"><?php esc_html_e("For web applications that need to serve untrusted HTML files, Microsoft IE introduced a mechanism to help prevent the untrusted content from compromising your site's security. When the X-Download-Options header is present with the value noopen, the user is prevented from opening a file download directly; instead, they must first save the file locally. When the locally saved file is later opened, it no longer executes in the security context of your site, helping to prevent script injection.", 'http-headers'); ?></p>
    </th>
    <td>
   		<fieldset>
    		<legend class="screen-reader-text">X-Download-Options</legend>
        <?php
        $http_headers_x_download_options = get_option('hh_x_download_options', 0);
        foreach ($http_headers_bools as $http_headers_k => $http_headers_v)
        {
        	?><p><label><input type="radio" class="http-header" name="hh_x_download_options" value="<?php echo esc_attr($http_headers_k); ?>"<?php checked($http_headers_x_download_options, $http_headers_k); ?> /> <?php echo esc_html($http_headers_v); ?></label></p><?php
        }
        ?>
    	</fieldset>
    </td>
	<td>
		<?php settings_fields( 'http-headers-xdo' ); ?>
		<?php do_settings_sections( 'http-headers-xdo' ); ?>
		<select name="hh_x_download_options_value" class="http-header-value"<?php echo $http_headers_x_download_options == 1 ? NULL : ' readonly'; ?>>
		<?php
		$http_headers_items = array('noopen');
		$http_headers_x_download_options_value = get_option('hh_x_download_options_value');
		foreach ($http_headers_items as $http_headers_item) {
			?><option value="<?php echo esc_attr($http_headers_item); ?>"<?php selected($http_headers_x_download_options_value, $http_headers_item); ?>><?php echo esc_html($http_headers_item); ?></option><?php
		}
		?>
		</select>
	</td>
</tr>