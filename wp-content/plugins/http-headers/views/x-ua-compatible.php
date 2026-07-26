<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<tr valign="top">
    <th scope="row">X-UA-Compatible
    	<p class="description"><?php esc_html_e('In some cases, it might be necessary to restrict a webpage to a document mode supported by an older version of Windows Internet Explorer. Here we look at the x-ua-compatible header, which allows a webpage to be displayed as if it were viewed by an earlier version of the browser.', 'http-headers'); ?></p>
    </th>
    <td>
   		<fieldset>
    		<legend class="screen-reader-text">X-UA-Compatible</legend>
        <?php
        $http_headers_x_ua_compatible = get_option('hh_x_ua_compatible', 0);
        foreach ($http_headers_bools as $http_headers_k => $http_headers_v)
        {
        	?><p><label><input type="radio" class="http-header" name="hh_x_ua_compatible" value="<?php echo esc_attr($http_headers_k); ?>"<?php checked($http_headers_x_ua_compatible, $http_headers_k, true); ?> /> <?php echo esc_html($http_headers_v); ?></label></p><?php
        }
        ?>
    	</fieldset>
    </td>
	<td>
		<?php settings_fields( 'http-headers-uac' ); ?>
		<?php do_settings_sections( 'http-headers-uac' ); ?>
		<select name="hh_x_ua_compatible_value" class="http-header-value"<?php echo $http_headers_x_ua_compatible == 1 ? NULL : ' readonly'; ?>>
		<?php
		$http_headers_items = array('IE=7', 'IE=8', 'IE=9', 'IE=10', 'IE=edge', 'IE=edge,chrome=1');
		$http_headers_x_ua_compatible_value = get_option('hh_x_ua_compatible_value');
		foreach ($http_headers_items as $http_headers_item) {
			?><option value="<?php echo esc_attr($http_headers_item); ?>"<?php selected($http_headers_x_ua_compatible_value, $http_headers_item); ?>><?php echo esc_html($http_headers_item); ?></option><?php
		}
		?>
		</select>
	</td>
</tr>