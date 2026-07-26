<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<tr valign="top">
    <th scope="row">Report-To
    	<p class="description"><?php esc_html_e('The Report-To HTTP response header field instructs the user agent to store reporting endpoints for an origin.', 'http-headers'); ?></p>
    </th>
	<td>
   		<fieldset>
    		<legend class="screen-reader-text">Report-To</legend>
        <?php
        $http_headers_report_to = get_option('hh_report_to', 0);
        foreach ($http_headers_bools as $http_headers_k => $http_headers_v)
        {
        	?><p><label><input type="radio" class="http-header" name="hh_report_to" value="<?php echo esc_attr($http_headers_k); ?>"<?php checked($http_headers_report_to, $http_headers_k, true); ?> /> <?php echo esc_html($http_headers_v); ?></label></p><?php
        }
        ?>
    	</fieldset>
	<?php settings_fields( 'http-headers-rt' ); ?>
	<?php do_settings_sections( 'http-headers-rt' ); ?>
	</td>
</tr>
<?php 
$http_headers_default_value = array(
        array(
        'endpoints' => array(),
            'group' => '',
        'max_age' => '',
        )
    );
$http_headers_report_to_value = get_option('hh_report_to_value');
if (!is_array($http_headers_report_to_value) || empty($http_headers_report_to_value))
{
    $http_headers_report_to_value = $http_headers_default_value;
}
?>
<tr>
	<td colspan="2">
		<div style="max-width: 1024px; overflow-x: auto">
    		<table class="hh-bordered hh-p-sm">
				<tr>
    				<th rowspan="2" class="hh-center hh-middle">group</th>
    				<th rowspan="2" class="hh-center hh-middle">max_age</th>
    				<th rowspan="2" class="hh-center hh-middle">include_subdomains</th>
    				<th colspan="3" class="hh-center">endpoints</th>
    				<th>&nbsp;</th>
    				<th>&nbsp;</th>
    			</tr>
    			<tr>
    				<th class="hh-center">url</th>
    				<th class="hh-center">priority</th>
    				<th class="hh-center">weight</th>
    				<th>&nbsp;</th>
					<th>&nbsp;</th>
				</tr>
				<?php
				$http_headers_items = array('0' => '0 (Delete entire reporting cache)', '3600' => '1 hour', '86400' => '1 day', '604800' => '7 days', '2592000' => '30 days', '5184000' => '60 days', '7776000' => '90 days', '31536000' => '1 year', '63072000' => '2 years');
				$http_headers_i = 0;
    			foreach ($http_headers_report_to_value as $http_headers_item)
    			{
    			    if (isset($http_headers_item['endpoints']) && !empty($http_headers_item['endpoints']))
    				{
    				    $http_headers_cnt = count($http_headers_item['endpoints']);
    				    $http_headers_c = 0;
    				    foreach ($http_headers_item['endpoints'] as $http_headers_k => $http_headers_v)
						{
    				        $http_headers_classes = array();
    				        if ($http_headers_c == 0)
    				        {
    				            if ($http_headers_i == 0)
    				            {
    				                $http_headers_classes[] = 'hh-tr-first';
    				            }
    				            $http_headers_classes[] = 'hh-tr-group-start';
    				        }
    				        
    				        if ($http_headers_c == $http_headers_cnt - 1)
    				        {
    				            $http_headers_classes[] = 'hh-tr-group-end';
    				        }
    						?>
    				        <tr class="<?php echo esc_attr(join(' ', $http_headers_classes)); ?>">
    							<?php
    				        	if ($http_headers_c == 0)
    				        	{
    				        	    ?>
    				        	    <td rowspan="<?php echo esc_attr($http_headers_cnt); ?>" class="hh-middle"><input type="text" class="http-header-value" name="hh_report_to_value[<?php echo esc_attr($http_headers_i); ?>][group]" value="<?php echo esc_attr($http_headers_item['group']); ?>" placeholder="csp-endpoint"<?php echo $http_headers_report_to == 1 ? NULL : ' readonly'; ?>></td>
                    				<td rowspan="<?php echo esc_attr($http_headers_cnt); ?>" class="hh-middle"><select class="http-header-value" name="hh_report_to_value[<?php echo esc_attr($http_headers_i); ?>][max_age]"<?php echo $http_headers_report_to == 1 ? NULL : ' readonly'; ?>>
                    				<?php
                    				foreach ($http_headers_items as $http_headers_key => $http_headers_val) {
                    				    ?><option value="<?php echo esc_attr($http_headers_key); ?>"<?php selected($http_headers_item['max_age'], $http_headers_key); ?>><?php echo esc_html($http_headers_val); ?></option><?php
				    				}
				    				?>
				    				</select></td>
                    				<td rowspan="<?php echo esc_attr($http_headers_cnt); ?>" class="hh-middle hh-center"><input type="checkbox" class="http-header-value" name="hh_report_to_value[<?php echo esc_attr($http_headers_i); ?>][include_subdomains]" value="1"<?php array_key_exists('include_subdomains', $http_headers_item) ? checked($http_headers_item['include_subdomains'], 1, true) : NULL; ?><?php echo $http_headers_report_to == 1 ? NULL : ' readonly'; ?> /></td>
    				        	    <?php
    				        	}
    				        	?>
    				
        				        <td><input type="text" class="http-header-value" name="hh_report_to_value[<?php echo esc_attr($http_headers_i); ?>][endpoints][<?php echo esc_attr($http_headers_k); ?>][url]" value="<?php echo esc_attr($http_headers_v['url']); ?>" placeholder="https://example.com/report/csp"<?php echo $http_headers_report_to == 1 ? NULL : ' readonly'; ?> size="40"></td>
        				        <td><input type="number" class="http-header-value" name="hh_report_to_value[<?php echo esc_attr($http_headers_i); ?>][endpoints][<?php echo esc_attr($http_headers_k); ?>][priority]" value="<?php echo esc_attr($http_headers_v['priority']); ?>" min="0" step="1"></td>
        				        <td><input type="number" class="http-header-value" name="hh_report_to_value[<?php echo esc_attr($http_headers_i); ?>][endpoints][<?php echo esc_attr($http_headers_k); ?>][weight]" value="<?php echo esc_attr($http_headers_v['weight']); ?>" min="0" step="1"></td>
        				        
    							<td><?php 
        				        if ($http_headers_c == 0)
        				        {
        				            ?>
        				        	<button type="button" class="button hh-btn-add-endpoint"><?php esc_html_e('Add endpoint', 'http-headers'); ?></button>
        				            <?php
        				        } else {
        				            ?>
        				        	<button type="button" class="button hh-btn-delete-endpoint"><?php esc_html_e('Remove endpoint', 'http-headers'); ?></button>
        				            <?php
        				        }
        				        ?></td>
        				        <?php 
        				        if ($http_headers_c == 0)
        				        {
        				            ?>
        				        	<td rowspan="<?php echo esc_attr($http_headers_cnt); ?>" class="hh-middle hh-center"><?php 
				    				if ($http_headers_i > 0)
				    				{
                                        ?>
                				    	<button type="button" class="button hh-btn-delete-endpoint-group" title="<?php esc_attr_e('Delete', 'http-headers'); ?>"><?php esc_html_e('Remove group', 'http-headers'); ?></button>
                				    	<?php 
				    				}
				    				?></td>
                				  	<?php  
                				}
                				?>
			    			</tr>
			    			<?php
    				    	$http_headers_c += 1;
    				    }
    				} else {
    				    ?>
    				    <tr class="hh-tr-first hh-tr-group-start hh-tr-group-end">
    				    	<td><input type="text" class="http-header-value" name="hh_report_to_value[<?php echo esc_attr($http_headers_i); ?>][group]" value="<?php echo esc_attr($http_headers_item['group']); ?>" placeholder="csp-endpoint"<?php echo $http_headers_report_to == 1 ? NULL : ' readonly'; ?>></td>
            				<td><select class="http-header-value" name="hh_report_to_value[<?php echo esc_attr($http_headers_i); ?>][max_age]"<?php echo $http_headers_report_to == 1 ? NULL : ' readonly'; ?>>
            				<?php
            				foreach ($http_headers_items as $http_headers_key => $http_headers_val) {
            				    ?><option value="<?php echo esc_attr($http_headers_key); ?>"<?php selected($http_headers_item['max_age'], $http_headers_key); ?>><?php echo esc_html($http_headers_val); ?></option><?php
            				}
            				?>
            				</select></td>
            				<td class="hh-center"><input type="checkbox" class="http-header-value" name="hh_report_to_value[<?php echo esc_attr($http_headers_i); ?>][include_subdomains]" value="1"<?php array_key_exists('include_subdomains', $http_headers_item) ? checked($http_headers_item['include_subdomains'], 1, true) : NULL; ?><?php echo $http_headers_report_to == 1 ? NULL : ' readonly'; ?> /></td>
    				        
    				        <td><input type="text" class="http-header-value" name="hh_report_to_value[<?php echo esc_attr($http_headers_i); ?>][endpoints][0][url]" placeholder="https://example.com/report/csp"<?php echo $http_headers_report_to == 1 ? NULL : ' readonly'; ?> size="40"></td>
    				        <td><input type="number" class="http-header-value" name="hh_report_to_value[<?php echo esc_attr($http_headers_i); ?>][endpoints][0][priority]" min="0" step="1"></td>
    				        <td><input type="number" class="http-header-value" name="hh_report_to_value[<?php echo esc_attr($http_headers_i); ?>][endpoints][0][weight]" min="0" step="1"></td>
    				        
    				        <td>
    				        	<button type="button" class="button hh-btn-add-endpoint"><?php esc_html_e('Add endpoint', 'http-headers'); ?></button>
    				        </td>
    				        <td rowspan="1"><?php 
            				if ($http_headers_i > 0)
            				{
            				    ?><button type="button" class="button hh-btn-delete-endpoint-group" title="<?php esc_attr_e('Delete', 'http-headers'); ?>"><?php esc_html_e('Remove group', 'http-headers'); ?></button><?php
            				}
            				?></td>
				        </tr>
    				    <?php
    				}
    				$http_headers_i += 1;
				}
				?>
				<tr>
					<td colspan="8">
						<button type="button" class="button" id="hh-btn-add-endpoint-group">+ <?php esc_html_e('Add endpoint group', 'http-headers'); ?></button>
					</td>
				</tr>
			</table>
		</div>
	</td>
</tr>