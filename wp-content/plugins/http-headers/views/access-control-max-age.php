<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<tr>
	<th scope="row">Access-Control-Max-Age
		<p class="description"><?php esc_html_e('The Access-Control-Max-Age header indicates how much time, the result of a preflight request, can be cached.', 'http-headers'); ?></p>
        <hr>
        <p class="description"><?php esc_html_e('Read more at', 'http-headers'); ?>
            <a target="_blank" href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Max-Age"><?php esc_html_e('MDN Web Docs', 'http-headers'); ?></a>
        </p>
	</th>
	<td>
        <fieldset>
        	<legend class="screen-reader-text">Access-Control-Max-Age</legend>
	    <?php
        $http_headers_access_control_max_age = get_option('hh_access_control_max_age', 0);
        foreach ($http_headers_bools as $http_headers_k => $http_headers_v)
        {
        	?><p><label><input type="radio" class="http-header" name="hh_access_control_max_age" value="<?php echo esc_attr($http_headers_k); ?>"<?php checked($http_headers_access_control_max_age, $http_headers_k); ?> /> <?php echo esc_html($http_headers_v); ?></label></p><?php
        }
        ?>
		</fieldset>
	</td>
	<td>
		<?php settings_fields( 'http-headers-acma' ); ?>
		<?php do_settings_sections( 'http-headers-acma' ); ?>
		<input type="text" name="hh_access_control_max_age_value" class="http-header-value" value="<?php echo esc_attr(get_option('hh_access_control_max_age_value')); ?>"<?php echo $http_headers_access_control_max_age == 1 ? NULL : ' checked'; ?>>
		<?php esc_html_e('seconds', 'http-headers'); ?>
	</td>
</tr>