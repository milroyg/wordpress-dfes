<?php
if (!defined('ABSPATH')) {
	exit;
}
?>
<input type="text" name="hh_content_security_policy_value[<?php echo esc_attr($http_headers_item); ?>]" class="http-header-value" size="40"
	value="<?php echo isset($http_headers_csp_value[$http_headers_item]) ? esc_attr($http_headers_csp_value[$http_headers_item]) : NULL; ?>"<?php echo $http_headers_content_security_policy == 1 ? NULL : ' readonly'; ?>>
<?php 
if ($http_headers_item == 'plugin-types')
{
    ?>
    <br>
	<em>Example: application/x-shockwave-flash application/x-java-applet</em>
	<?php 
}
?>