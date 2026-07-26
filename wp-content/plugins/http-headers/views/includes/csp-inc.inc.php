<?php
if (!defined('ABSPATH')) {
	exit;
}
?>
<input type="checkbox"
    name="hh_content_security_policy_value[<?php echo esc_attr($http_headers_item); ?>]"
    value="1"<?php echo isset($http_headers_csp_value[$http_headers_item]) ? ' checked' : NULL; ?>
    class="http-header-value"<?php echo $http_headers_content_security_policy == 1 ? NULL : ' readonly'; ?>>