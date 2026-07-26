<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<tr valign="top">
    <th scope="row">X-Permitted-Cross-Domain-Policies
    	<p class="description"><?php esc_html_e('A cross-domain policy file is an XML document that grants a web client, such as Adobe Flash Player or Adobe Acrobat (though not necessarily limited to these), permission to handle data across domains.', 'http-headers'); ?></p>
    </th>
    <td>
   		<fieldset>
    		<legend class="screen-reader-text">X-Permitted-Cross-Domain-Policies</legend>
        <?php
        $http_headers_x_permitted_cross_domain_policies = get_option('hh_x_permitted_cross_domain_policies', 0);
        foreach ($http_headers_bools as $http_headers_k => $http_headers_v)
        {
        	?><p><label><input type="radio" class="http-header" name="hh_x_permitted_cross_domain_policies" value="<?php echo esc_attr($http_headers_k); ?>"<?php checked($http_headers_x_permitted_cross_domain_policies, $http_headers_k); ?> /> <?php echo esc_html($http_headers_v); ?></label></p><?php
        }
        ?>
    	</fieldset>
    </td>
	<td>
		<?php settings_fields( 'http-headers-xpcd' ); ?>
		<?php do_settings_sections( 'http-headers-xpcd' ); ?>
		<select name="hh_x_permitted_cross_domain_policies_value" class="http-header-value"<?php echo $http_headers_x_permitted_cross_domain_policies == 1 ? NULL : ' readonly'; ?>>
		<?php
		$http_headers_items = array('none', 'master-only', 'by-content-type', 'by-ftp-filename', 'all');
		$http_headers_x_permitted_cross_domain_policies_value = get_option('hh_x_permitted_cross_domain_policies_value');
		foreach ($http_headers_items as $http_headers_item) {
			?><option value="<?php echo esc_attr($http_headers_item); ?>"<?php selected($http_headers_x_permitted_cross_domain_policies_value, $http_headers_item); ?>><?php echo esc_html($http_headers_item); ?></option><?php
		}
		?>
		</select>
	</td>
</tr>