<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<tr>
	<th scope="row">Access-Control-Allow-Origin
		<p class="description"><?php esc_html_e('The Access-Control-Allow-Origin header indicates whether a resource can be shared.', 'http-headers'); ?></p>
        <hr>
        <p class="description"><?php esc_html_e('Read more at', 'http-headers'); ?>
            <a target="_blank" href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Allow-Origin"><?php esc_html_e('MDN Web Docs', 'http-headers'); ?></a>
        </p>
	</th>
	<td>
	    <fieldset>
	    	<legend class="screen-reader-text">Access-Control-Allow-Origin</legend>
	        <?php
	        $http_headers_access_control_allow_origin = get_option('hh_access_control_allow_origin', 0);
	        foreach ($http_headers_bools as $http_headers_k => $http_headers_v)
	        {
	        	?><p><label><input type="radio" class="http-header" name="hh_access_control_allow_origin" value="<?php echo esc_attr($http_headers_k); ?>"<?php checked($http_headers_access_control_allow_origin, $http_headers_k); ?> /> <?php echo esc_html($http_headers_v); ?></label></p><?php
	        }
	        ?>
	    </fieldset>
	</td>
	<td>
		<?php settings_fields( 'http-headers-acao' ); ?>
		<?php do_settings_sections( 'http-headers-acao' ); ?>
		<?php
		$http_headers_access_control_allow_origin_url = get_option('hh_access_control_allow_origin_url');
		if (is_scalar($http_headers_access_control_allow_origin_url))
		{
		    $http_headers_access_control_allow_origin_url = array($http_headers_access_control_allow_origin_url);
		}
		if (!is_array($http_headers_access_control_allow_origin_url))
		{
		    $http_headers_access_control_allow_origin_url = array(NULL);
		}
		?>
		<table>
    		<tr>
    			<td>
            		<select name="hh_access_control_allow_origin_value" class="http-header-value"<?php echo $http_headers_access_control_allow_origin == 1 ? NULL : ' readonly'; ?>>
            		<?php
            		$http_headers_items = array('*', 'origin', 'null');
            		$http_headers_access_control_allow_origin_value = get_option('hh_access_control_allow_origin_value');
            		foreach ($http_headers_items as $http_headers_item) {
            		    ?><option value="<?php echo esc_attr($http_headers_item); ?>"<?php selected($http_headers_access_control_allow_origin_value, $http_headers_item); ?>><?php echo esc_html($http_headers_item); ?></option><?php
            		}
            		?>
            		</select>
				</td>
    			<td class="hh-acao<?php echo $http_headers_access_control_allow_origin_value != 'origin' ? ' hh-hidden' : NULL; ?>">
                    <input type="text" name="hh_access_control_allow_origin_url[]" class="http-header-value"
                           placeholder="http://domain.com" size="35"
                           value="<?php echo isset($http_headers_access_control_allow_origin_url[0]) ? esc_attr($http_headers_access_control_allow_origin_url[0]) : NULL; ?>"<?php echo $http_headers_access_control_allow_origin == 1 && $http_headers_access_control_allow_origin_value == 'origin' ? NULL : ' readonly'; ?> />
                </td>
    			<td class="hh-acao<?php echo $http_headers_access_control_allow_origin_value != 'origin' ? ' hh-hidden' : NULL; ?>">&nbsp;</td>
    		</tr>
    		<?php 
		    foreach ($http_headers_access_control_allow_origin_url as $http_headers_i => $http_headers_url)
    		{
    		    if ($http_headers_i == 0)
    		    {
    		        continue;
    		    }
    		    ?>
				<tr class="hh-acao<?php echo $http_headers_access_control_allow_origin_value != 'origin' ? ' hh-hidden' : NULL; ?>">
        			<td>&nbsp;</td>
        			<td><input type="text" name="hh_access_control_allow_origin_url[]" class="http-header-value" placeholder="http://domain.com" size="35" value="<?php echo esc_attr($http_headers_url); ?>"<?php echo $http_headers_access_control_allow_origin == 1 && $http_headers_access_control_allow_origin_value == 'origin' ? NULL : ' readonly'; ?> /></td>
        			<td><button type="button" class="button button-small hh-btn-delete-origin" title="<?php esc_attr_e('Delete', 'http-headers'); ?>">x</button></td>
        		</tr>		    
    		    <?php 
    		}
    		?>
    		<tr class="hh-acao<?php echo $http_headers_access_control_allow_origin_value != 'origin' ? ' hh-hidden' : NULL; ?>">
    			<td>&nbsp;</td>
    			<td><button type="button" class="button hh-btn-add-origin">+ <?php esc_html_e('Add origin', 'http-headers'); ?></button></td>
    			<td>&nbsp;</td>
    		</tr>
		</table>
	</td>
</tr>