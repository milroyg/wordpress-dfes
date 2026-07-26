<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<tr valign="top">
	<th scope="row">WWW-Authenticate
		<p class="description"><?php esc_html_e('HTTP supports the use of several authentication mechanisms to control access to pages and other resources. These mechanisms are all based around the use of the 401 status code and the WWW-Authenticate response header.', 'http-headers'); ?></p>
        <hr>
        <p class="description"><?php esc_html_e('Read more at', 'http-headers'); ?>
            <a target="_blank" href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/WWW-Authenticate"><?php esc_html_e('MDN Web Docs', 'http-headers'); ?></a>
        </p>
	</th>
	<td>
		<fieldset>
			<legend class="screen-reader-text">WWW-Authenticate</legend>
			<?php
			$http_headers_www_authenticate = get_option ( 'hh_www_authenticate', 0 );
			foreach ( $http_headers_bools as $http_headers_k => $http_headers_v ) {
				?><p>
					<label><input type="radio" class="http-header" name="hh_www_authenticate" value="<?php echo esc_attr($http_headers_k); ?>" <?php checked($http_headers_www_authenticate, $http_headers_k, true); ?> /> <?php echo esc_html($http_headers_v); ?></label>
				</p><?php
			}
			?>
		</fieldset>
	</td>
	<td>
		<?php settings_fields( 'http-headers-wwa' ); ?>
		<?php do_settings_sections( 'http-headers-wwa' ); ?>
		<table>
			<tbody>
				<tr>
					<td>Type</td>
					<td colspan="3">
						<select name="hh_www_authenticate_type" class="http-header-value"<?php echo $http_headers_www_authenticate == 1 ? NULL : ' readonly'; ?>>
						<?php
						$http_headers_items = array ('Basic', 'Digest');
						$http_headers_www_authenticate_type = get_option ( 'hh_www_authenticate_type' );
						foreach ( $http_headers_items as $http_headers_item ) {
							?><option value="<?php echo esc_attr($http_headers_item); ?>" <?php selected($http_headers_www_authenticate_type, $http_headers_item); ?>><?php echo esc_html($http_headers_item); ?></option><?php
						}
						?>		
						</select>
					</td>
				</tr>
				<tr>
					<td>Realm</td>
					<td colspan="3"><input type="text" name="hh_www_authenticate_realm" class="http-header-value" size="30" value="<?php echo esc_attr(get_option('hh_www_authenticate_realm')); ?>"<?php echo $http_headers_www_authenticate == 1 ? NULL : ' readonly'; ?> placeholder="Restricted area"></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><strong><?php esc_html_e('Username', 'http-headers'); ?></strong></td>
					<td><strong><?php esc_html_e('Password', 'http-headers'); ?></strong></td>
					<td>&nbsp;</td>
				</tr>
				<?php 
				$http_headers_usernames = get_option('hh_www_authenticate_user', array());
				$http_headers_passwords = get_option('hh_www_authenticate_pswd', array());
				if (!is_array($http_headers_usernames)) {
				    $http_headers_usernames = array($http_headers_usernames);
				}
				if (!is_array($http_headers_passwords)) {
				    $http_headers_passwords = array($http_headers_passwords);
				}
				$http_headers_i = 0;
				foreach ($http_headers_usernames as $http_headers_k => $http_headers_user) {
				    ?>
    				<tr>
    					<td>&nbsp;</td>
    					<td><input type="text" name="hh_www_authenticate_user[]" class="http-header-value" value="<?php echo esc_attr($http_headers_user); ?>"<?php echo $http_headers_www_authenticate == 1 ? NULL : ' readonly'; ?>></td>
    					<td><input type="text" name="hh_www_authenticate_pswd[]" class="http-header-value" value="<?php echo esc_attr($http_headers_passwords[$http_headers_k]); ?>"<?php echo $http_headers_www_authenticate == 1 ? NULL : ' readonly'; ?>></td>
    					<td><?php 
    					if ($http_headers_i > 0)
    					{
    					    ?><button type="button" class="button button-small hh-btn-delete-user" title="<?php esc_attr_e('Delete', 'http-headers'); ?>">x</button><?php
    					} else {
    					    echo "&nbsp;";
    					}
    					?></td>
    				</tr>    
				    <?php
				    $http_headers_i += 1;
				}
				?>
				<tr>
					<td>&nbsp;</td>
					<td colspan="3">
						<button type="button" class="button hh-btn-add-user">+ <?php esc_html_e('Add user', 'http-headers'); ?></button>
					</td>
				</tr>
			</tbody>
		</table>
	</td>
</tr>