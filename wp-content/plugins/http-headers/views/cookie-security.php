<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<tr>
	<th scope="row">Cookie security
		<p class="description"><?php esc_html_e('A secure cookie is only sent to the server with a encrypted request over the HTTPS protocol.', 'http-headers'); ?></p>
		<p class="description"><?php esc_html_e("To prevent cross-site scripting (XSS) attacks, HttpOnly cookies are inaccessible to JavaScript's Document.cookie API; they are only sent to the server.", 'http-headers'); ?></p>
        <p class="description"><?php esc_html_e('SameSite prevents the browser from sending this cookie along with cross-site requests. The main goal is mitigate the risk of cross-origin information leakage. It also provides some protection against cross-site request forgery attacks.', 'http-headers'); ?></p>
        <hr>
        <p class="description"><?php esc_html_e('Read more at', 'http-headers'); ?>
            <a target="_blank" href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Cookies#Secure_and_HttpOnly_cookies"><?php esc_html_e('MDN Web Docs', 'http-headers'); ?></a>
        </p>
	</th>
	<td>
		<fieldset>
			<legend class="screen-reader-text">Cookie security</legend>
        <?php
        $http_headers_cookie_security = get_option('hh_cookie_security', 0);
        foreach ($http_headers_bools as $http_headers_k => $http_headers_v)
        {
        	?><p><label><input type="radio" class="http-header" name="hh_cookie_security" value="<?php echo esc_attr($http_headers_k); ?>"<?php checked($http_headers_cookie_security, $http_headers_k); ?> /> <?php echo esc_html($http_headers_v); ?></label></p><?php
		}
		?>
		</fieldset>
	</td>
	<td>
	<?php settings_fields( 'http-headers-cose' ); ?>
	<?php do_settings_sections( 'http-headers-cose' ); ?>
	<?php
	$http_headers_items = array('Secure', 'HttpOnly', 'SameSite');
	$http_headers_cookie_security_value = get_option('hh_cookie_security_value');
	foreach ($http_headers_items as $http_headers_item)
	{
        $http_headers_is_checked = is_array($http_headers_cookie_security_value) && array_key_exists($http_headers_item, $http_headers_cookie_security_value);
        ?>
        <p>
            <label><input type="checkbox"
                          class="http-header-value"
                          name="hh_cookie_security_value[<?php echo esc_attr($http_headers_item); ?>]"
                          value="1"<?php echo !$http_headers_is_checked ? NULL : ' checked'; ?><?php echo $http_headers_cookie_security == 1 ? NULL : ' readonly'; ?>> <?php echo esc_html($http_headers_item); ?><?php
                ?></label>
        </p>
        <?php
        if ($http_headers_item == 'SameSite')
        {
            foreach (array('None', 'Lax', 'Strict') as $http_headers_s_val)
            {
                ?>
                <p class="hh-csv-value<?php echo !$http_headers_is_checked ? ' hh-hidden' : NULL; ?>">
                    <label><input type="radio"
                          class="http-header-value"
                          name="hh_cookie_security_value[SameSite]"
                          value="<?php echo esc_attr($http_headers_s_val); ?>"<?php echo !is_array($http_headers_cookie_security_value) || !array_key_exists($http_headers_item, $http_headers_cookie_security_value) || $http_headers_cookie_security_value[$http_headers_item] != $http_headers_s_val ? NULL : ' checked'; ?><?php echo $http_headers_cookie_security == 1 ? NULL : ' readonly'; ?>> <?php echo esc_html($http_headers_s_val); ?></label>
                </p>
                <?php
            }
        }
	}
	?>
	</td>
</tr>