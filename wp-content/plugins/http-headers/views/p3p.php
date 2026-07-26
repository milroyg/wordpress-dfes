<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
        <tr valign="top">
	        <th scope="row">P3P
	        	<p class="description"><?php esc_html_e('The Platform for Privacy Preferences Project (P3P) is a protocol allowing websites to declare their intended use of information they collect about web browser users.', 'http-headers'); ?></p>
	        </th>
	        <td>
	       		<fieldset>
	        		<legend class="screen-reader-text">P3P</legend>
		        <?php
		        $http_headers_p3p = get_option('hh_p3p', 0);
		        foreach ($http_headers_bools as $http_headers_k => $http_headers_v)
		        {
		        	?><p><label><input type="radio" class="http-header" name="hh_p3p" value="<?php echo esc_attr($http_headers_k); ?>"<?php checked($http_headers_p3p, $http_headers_k); ?> /> <?php echo esc_html($http_headers_v); ?></label></p><?php
		        }
		        ?>
	        	</fieldset>
	        </td>
			<td>
			<?php settings_fields( 'http-headers-p3p' ); ?>
			<?php do_settings_sections( 'http-headers-p3p' ); ?>
			<?php 
			$http_headers_p3p_value = get_option('hh_p3p_value');
			if (!$http_headers_p3p_value)
			{
				$http_headers_p3p_value = array();
			}
			$http_headers_in_creq = array('ADM', 'DEV', 'TAI', 'PSA', 'PSD', 'IVA', 'IVD', 'CON', 'HIS', 'TEL', 'OTP', 'DEL', 'SAM', 'UNR', 'PUB', 'OTR',);
			$http_headers_creq = array('a', 'i', 'o');
			?>
			<table>
				<tbody>
					<tr>
						<td>Compact ACCESS</td>
						<td class="hh-td-inner">
							<table><tbody><tr><?php
							$http_headers_items = array('NOI', 'ALL', 'CAO', 'IDC', 'OTI', 'NON');
							foreach ($http_headers_items as $http_headers_i => $http_headers_item) {
								if ($http_headers_i > 0 && $http_headers_i % 4 === 0) {
									?></tr><tr><?php
								}
								?><td><label><input type="checkbox" class="http-header-value" name="hh_p3p_value[<?php echo esc_attr($http_headers_item); ?>]" value="1"<?php echo !array_key_exists($http_headers_item, $http_headers_p3p_value) ? NULL : ' checked'; ?><?php echo $http_headers_p3p == 1 ? NULL : ' readonly'; ?> /> <?php echo esc_html($http_headers_item); ?></label></td><?php
							}
							?></tr></tbody></table>
						</td>
					</tr>
					<tr>
						<td>Compact DISPUTES</td>
						<td class="hh-td-inner">
							<table><tbody><tr><?php
							$http_headers_items = array('DSP');
							foreach ($http_headers_items as $http_headers_i => $http_headers_item) {
								if ($http_headers_i > 0 && $http_headers_i % 4 === 0) {
									?></tr><tr><?php
								}
								?><td><label><input type="checkbox" class="http-header-value" name="hh_p3p_value[<?php echo esc_attr($http_headers_item); ?>]" value="1"<?php echo !array_key_exists($http_headers_item, $http_headers_p3p_value) ? NULL : ' checked'; ?><?php echo $http_headers_p3p == 1 ? NULL : ' readonly'; ?> /> <?php echo esc_html($http_headers_item); ?></label></td><?php
							}
							?></tr></tbody></table>
						</td>
					</tr>
					<tr>
						<td>Compact REMEDIES</td>
						<td class="hh-td-inner">
							<table><tbody><tr><?php
							$http_headers_items = array('COR', 'MON', 'LAW');
							foreach ($http_headers_items as $http_headers_i => $http_headers_item) {
								if ($http_headers_i > 0 && $http_headers_i % 4 === 0) {
									?></tr><tr><?php
								}
								?><td><label><input type="checkbox" class="http-header-value" name="hh_p3p_value[<?php echo esc_attr($http_headers_item); ?>]" value="1"<?php echo !array_key_exists($http_headers_item, $http_headers_p3p_value) ? NULL : ' checked'; ?><?php echo $http_headers_p3p == 1 ? NULL : ' readonly'; ?> /> <?php echo esc_html($http_headers_item); ?></label></td><?php
							}
							?></tr></tbody></table>
						</td>
					</tr>
					<tr>
						<td>Compact NON-IDENTIFIABLE</td>
						<td class="hh-td-inner">
							<table><tbody><tr><?php
							$http_headers_items = array('NID');
							foreach ($http_headers_items as $http_headers_i => $http_headers_item) {
								if ($http_headers_i > 0 && $http_headers_i % 4 === 0) {
									?></tr><tr><?php
								}
								?><td><label><input type="checkbox" class="http-header-value" name="hh_p3p_value[<?php echo esc_attr($http_headers_item); ?>]" value="1"<?php echo !array_key_exists($http_headers_item, $http_headers_p3p_value) ? NULL : ' checked'; ?><?php echo $http_headers_p3p == 1 ? NULL : ' readonly'; ?> /> <?php echo esc_html($http_headers_item); ?></label></td><?php
							}
							?></tr></tbody></table>
						</td>
					</tr>
					<tr>
						<td>Compact PURPOSE</td>
						<td class="hh-td-inner">
							<table><tbody><tr><?php
							$http_headers_items = array('CUR', 'ADM', 'DEV', 'TAI', 'PSA', 'PSD', 'IVA', 'IVD', 'CON', 'HIS', 'TEL', 'OTP');
							foreach ($http_headers_items as $http_headers_i => $http_headers_item) {
								if ($http_headers_i > 0 && $http_headers_i % 4 === 0) {
									?></tr><tr><?php
								}
								?><td><label><input type="checkbox" class="http-header-value" name="hh_p3p_value[<?php echo esc_attr($http_headers_item); ?>]" value="1"<?php echo !array_key_exists($http_headers_item, $http_headers_p3p_value) ? NULL : ' checked'; ?><?php echo $http_headers_p3p == 1 ? NULL : ' readonly'; ?> /> <?php echo esc_html($http_headers_item); ?></label></td><?php
							}
							?></tr></tbody></table>
						</td>
					</tr>
					<tr>
						<td>Compact RECIPIENT</td>
						<td class="hh-td-inner">
							<table><tbody><tr><?php
							$http_headers_items = array('OUR', 'DEL', 'SAM', 'UNR', 'PUB', 'OTR');
							foreach ($http_headers_items as $http_headers_i => $http_headers_item) {
								if ($http_headers_i > 0 && $http_headers_i % 4 === 0) {
									?></tr><tr><?php
								}
								?><td><label><input type="checkbox" class="http-header-value" name="hh_p3p_value[<?php echo esc_attr($http_headers_item); ?>]" value="1"<?php echo !array_key_exists($http_headers_item, $http_headers_p3p_value) ? NULL : ' checked'; ?><?php echo $http_headers_p3p == 1 ? NULL : ' readonly'; ?> /> <?php echo esc_html($http_headers_item); ?></label></td><?php
							}
							?></tr></tbody></table>
						</td>
					</tr>
					<tr>
						<td>Compact RETENTION</td>
						<td class="hh-td-inner">
							<table><tbody><tr><?php
							$http_headers_items = array('NOR', 'STP', 'LEG', 'BUS', 'IND');
							foreach ($http_headers_items as $http_headers_i => $http_headers_item) {
								if ($http_headers_i > 0 && $http_headers_i % 4 === 0) {
									?></tr><tr><?php
								}
								?><td><label><input type="checkbox" class="http-header-value" name="hh_p3p_value[<?php echo esc_attr($http_headers_item); ?>]" value="1"<?php echo !array_key_exists($http_headers_item, $http_headers_p3p_value) ? NULL : ' checked'; ?><?php echo $http_headers_p3p == 1 ? NULL : ' readonly'; ?> /> <?php echo esc_html($http_headers_item); ?></label></td><?php
							}
							?></tr></tbody></table>
						</td>
					</tr>
					<tr>
						<td>Compact CATEGORIES</td>
						<td class="hh-td-inner">
							<table><tbody><tr><?php
							$http_headers_items = array('PHY', 'ONL', 'UNI', 'PUR', 'FIN', 'COM', 'NAV', 'INT', 'DEM', 'CNT', 'STA', 'POL', 'HEA', 'PRE', 'LOC', 'GOV', 'OTC');
							foreach ($http_headers_items as $http_headers_i => $http_headers_item) {
								if ($http_headers_i > 0 && $http_headers_i % 4 === 0) {
									?></tr><tr><?php
								}
								?><td><label><input type="checkbox" class="http-header-value" name="hh_p3p_value[<?php echo esc_attr($http_headers_item); ?>]" value="1"<?php echo !array_key_exists($http_headers_item, $http_headers_p3p_value) ? NULL : ' checked'; ?><?php echo $http_headers_p3p == 1 ? NULL : ' readonly'; ?> /> <?php echo esc_html($http_headers_item); ?></label></td><?php
							}
							?></tr></tbody></table>
						</td>
					</tr>
					<tr>
						<td>Compact TEST</td>
						<td class="hh-td-inner">
							<table><tbody><tr><?php
							$http_headers_items = array('TST');
							foreach ($http_headers_items as $http_headers_i => $http_headers_item) {
								if ($http_headers_i > 0 && $http_headers_i % 4 === 0) {
									?></tr><tr><?php
								}
								?><td><label><input type="checkbox" class="http-header-value" name="hh_p3p_value[<?php echo esc_attr($http_headers_item); ?>]" value="1"<?php echo !array_key_exists($http_headers_item, $http_headers_p3p_value) ? NULL : ' checked'; ?><?php echo $http_headers_p3p == 1 ? NULL : ' readonly'; ?> /> <?php echo esc_html($http_headers_item); ?></label></td><?php
							}
							?></tr></tbody></table>
						</td>
					</tr>
				</tbody>
			</table>
			
			</td>
        </tr>