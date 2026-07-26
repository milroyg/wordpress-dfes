<?php
if (!defined('ABSPATH')) {
	exit;
}
$http_headers_sandbox = array(
    'allow-forms', 
    'allow-same-origin', 
    'allow-scripts', 
    'allow-popups',
    'allow-modals', 
    'allow-downloads',
    'allow-orientation-lock', 
    'allow-pointer-lock', 
    'allow-presentation',
    'allow-popups-to-escape-sandbox', 
    'allow-top-navigation',
    'allow-top-navigation-by-user-activation',
);
foreach ($http_headers_sandbox as $http_headers_origin)
{
    ?>
    <p>
        <input type="checkbox" 
        	name="hh_content_security_policy_value[<?php echo esc_attr($http_headers_item); ?>][<?php echo esc_attr($http_headers_origin); ?>]"
        	id="csp-<?php echo esc_attr($http_headers_item); ?>-<?php echo esc_attr($http_headers_origin); ?>"
        	value="1"<?php echo isset($http_headers_csp_value[$http_headers_item][$http_headers_origin]) ? ' checked' : NULL; ?>
        	class="http-header-value"<?php echo $http_headers_content_security_policy == 1 ? NULL : ' readonly'; ?>>
    	<label for="csp-<?php echo esc_attr($http_headers_item); ?>-<?php echo esc_attr($http_headers_origin); ?>"><?php echo esc_html($http_headers_origin); ?></label>
    </p>
    <?php
}
?>