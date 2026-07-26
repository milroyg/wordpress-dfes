<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<tr valign="top">
	<th scope="row">Expect-CT
		<p class="description"><?php esc_html_e('Expect-CT is an HTTP header that allows sites to opt in to reporting and/or enforcement of Certificate Transparency requirements, which prevents the use of misissued certificates for that site from going unnoticed. When a site enables the Expect-CT header, they are requesting that Chrome check that any certificate for that site appears in public CT logs.', 'http-headers'); ?></p>
        <hr>
        <p class="description"><?php esc_html_e('Read more at', 'http-headers'); ?>
            <a target="_blank" href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Expect-CT"><?php esc_html_e('MDN Web Docs', 'http-headers'); ?></a>
        </p>
	</th>
	<td>
		<fieldset>
			<legend class="screen-reader-text">Expect-CT</legend>
			<?php
	        $http_headers_expect_ct = get_option('hh_expect_ct', 0);
	        foreach ($http_headers_bools as $http_headers_k => $http_headers_v)
	        {
	        	?><p><label><input type="radio" class="http-header" name="hh_expect_ct" value="<?php echo esc_attr($http_headers_k); ?>"<?php checked($http_headers_expect_ct, $http_headers_k, true); ?> /> <?php echo esc_html($http_headers_v); ?></label></p><?php
	        }
	        ?>
        	</fieldset>
	</td>
	<td>
	<?php settings_fields( 'http-headers-ect' ); ?>
	<?php do_settings_sections( 'http-headers-ect' ); ?>
		<table>
			<tr>
				<td>max-age:</td>
				<td><select name="hh_expect_ct_max_age" class="http-header-value"<?php echo $http_headers_expect_ct == 1 ? NULL : ' readonly'; ?>>
				<?php
				$http_headers_items = array('3600' => '1 hour', '86400' => '1 day', '604800' => '7 days', '2592000' => '30 days', '5184000' => '60 days', '7776000' => '90 days', '31536000' => '1 year');
				$http_headers_expect_ct_max_age = get_option('hh_expect_ct_max_age');
				foreach ($http_headers_items as $http_headers_key => $http_headers_item) {
					?><option value="<?php echo esc_attr($http_headers_key); ?>"<?php selected($http_headers_expect_ct_max_age, $http_headers_key); ?>><?php echo esc_html($http_headers_item); ?></option><?php
				}
				?>
				</select></td>
			</tr>
			<tr>
				<td>report-uri:</td>
				<td><input type="text" class="http-header-value" name="hh_expect_ct_report_uri" value="<?php echo esc_attr(get_option('hh_expect_ct_report_uri')); ?>" placeholder="https://example.com/ct-report"<?php echo $http_headers_expect_ct == 1 ? NULL : ' readonly'; ?> /></td>
			</tr>
			<tr>
				<td>enforce:</td>
				<td><input type="checkbox" class="http-header-value" name="hh_expect_ct_enforce" value="1"<?php checked(get_option('hh_expect_ct_enforce'), 1, true); ?><?php echo $http_headers_expect_ct == 1 ? NULL : ' readonly'; ?> /></td>
			</tr>
		</table>
	</td>
</tr>