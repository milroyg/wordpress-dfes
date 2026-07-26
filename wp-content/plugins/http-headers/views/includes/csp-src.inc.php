<?php
if (!defined('ABSPATH')) {
    exit;
}
$http_headers_origins = array(
    'wildcard' => '*', 
    'self' => "'self'", 
    'none' => "'none'", 
    'unsafe-inline' => "'unsafe-inline'", 
    'unsafe-eval' => "'unsafe-eval'",
    'strict-dynamic' => "'strict-dynamic'",
    'report-sample' => "'report-sample'",
    'http' => 'http:',
    'https' => 'https:', 
    'data' => 'data:',
    'mediastream' => 'mediastream:',
    'blob' => 'blob:',
    'filesystem' => 'filesystem:',
);
 
foreach ($http_headers_origins as $http_headers_k => $http_headers_origin)
{
    ?>
    <p<?php echo $http_headers_origin == '*' || !isset($http_headers_csp_value[$http_headers_item]['*']) ? NULL : ' style="display: none"'; ?>>
        <input type="checkbox"
            name="hh_content_security_policy_value[<?php echo esc_attr($http_headers_item); ?>][<?php echo esc_attr($http_headers_origin); ?>]"
            id="csp-<?php echo esc_attr($http_headers_item); ?>-<?php echo esc_attr($http_headers_k); ?>"
            value="1"<?php echo isset($http_headers_csp_value[$http_headers_item][$http_headers_origin]) ? ' checked' : NULL; ?>
            class="http-header-value"<?php echo $http_headers_content_security_policy == 1 ? NULL : ' readonly'; ?>>
        <label for="csp-<?php echo esc_attr($http_headers_item); ?>-<?php echo esc_attr($http_headers_k); ?>"><?php echo esc_html($http_headers_origin); ?></label>
    </p>
    <?php
}

switch ($http_headers_item) {
    case 'script-src':
	case 'script-src-elem':
        $http_headers_host_sources = array(
            'js.example.com',
            'http://js.example.com',
            'https://js.example.com',
        );
        break;
    case 'style-src':
	case 'style-src-elem':
        $http_headers_host_sources = array(
            'css.example.com',
            'http://css.example.com',
            'https://css.example.com',
        );
        break;
    case 'img-src':
        $http_headers_host_sources = array(
            'img.example.com',
            'http://img.example.com',
            'https://img.example.com',
        );
        break;
    case 'font-src':
        $http_headers_host_sources = array(
            'font.example.com',
            'http://font.example.com',
            'https://font.example.com',
        );
        break;
    case 'default-src':
        $http_headers_host_sources = array(
            'http://*.example.com',
            'mail.example.com:443',
            'https://assets.example.com',
            'cdn.example.com',
        );
        break;
    default:
        $http_headers_host_sources = array(
            'https://store.example.com',
            'store.example.com',
            '*.example.com',
        );
}
shuffle($http_headers_host_sources);
?>
<p<?php echo !isset($http_headers_csp_value[$http_headers_item]['*']) ? NULL : ' style="display: none"'; ?>>
	<input type="text" 
		name="hh_content_security_policy_value[<?php echo esc_attr($http_headers_item); ?>][source]"
		class="http-header-value" 
		size="40"
		placeholder="<?php echo esc_attr($http_headers_host_sources[0]); ?>"
		value="<?php echo isset($http_headers_csp_value[$http_headers_item]['source']) ? esc_attr($http_headers_csp_value[$http_headers_item]['source']) : NULL; ?>"<?php echo $http_headers_content_security_policy == 1 ? NULL : ' readonly'; ?>>
</p>