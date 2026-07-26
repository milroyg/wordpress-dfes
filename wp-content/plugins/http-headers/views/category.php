<?php 
if (!defined('ABSPATH')) {
    exit;
} 
include dirname(__FILE__) . '/includes/config.inc.php';
include dirname(__FILE__) . '/includes/breadcrumbs.inc.php';
?>
<table class="hh-index-table">
	<thead>
		<tr>
			<th><?php esc_html_e('Header', 'http-headers'); ?></th>
			<th style="width: 45%"><?php esc_html_e('Value', 'http-headers'); ?></th>
			<th class="hh-status"><?php esc_html_e('Status', 'http-headers'); ?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
	<?php 
	foreach ($http_headers_headers as $http_headers_index => $http_headers_item)
	{
		if (!isset($_GET['category']) || $_GET['category'] != $http_headers_item[2])
		{
			continue;
		}
		
		$http_headers_key = $http_headers_item[1];
		
		$http_headers_option = get_option($http_headers_key, 0);
		$http_headers_isOn = (int) $http_headers_option === 1;
		$http_headers_value = NULL;
		if ($http_headers_isOn)
		{
			$http_headers_value = get_option($http_headers_key .'_value');
			if (is_string($http_headers_value))
            {
	            $http_headers_value = esc_html($http_headers_value);
            }
			switch ($http_headers_key)
			{
				case 'hh_age':
					$http_headers_value = (int) $http_headers_value;
					break;
				case 'hh_p3p':
					if (!empty($http_headers_value))
					{
						$http_headers_value = sprintf('CP="%s"', join(' ', array_keys($http_headers_value)));
					}
					break;
				case 'hh_x_xxs_protection':
					if ($http_headers_value == '1; report=') {
						$http_headers_value .= esc_html(get_option('hh_x_xxs_protection_uri'));
					}
					break;
				case 'hh_x_powered_by':
					if (get_option('hh_x_powered_by_option') == 'unset') {
						$http_headers_value = '[Unset]';
					}
					break;
				case 'hh_x_frame_options':
					$http_headers_value = strtoupper($http_headers_value);
					if ($http_headers_value == 'ALLOW-FROM')
					{
						$http_headers_value .= ' ' . esc_html(get_option('hh_x_frame_options_domain'));
					}
					break;
				case 'hh_strict_transport_security':
					$http_headers_tmp = array();
					$http_headers_hh_strict_transport_security_max_age = get_option('hh_strict_transport_security_max_age');
					if ($http_headers_hh_strict_transport_security_max_age !== false)
					{
						$http_headers_tmp[] = sprintf('max-age=%u', $http_headers_hh_strict_transport_security_max_age);
						if (get_option('hh_strict_transport_security_sub_domains'))
						{
							$http_headers_tmp[] = 'includeSubDomains';
						}
						if (get_option('hh_strict_transport_security_preload'))
						{
							$http_headers_tmp[] = 'preload';
						}
					} else {
						$http_headers_tmp = array(get_option('hh_strict_transport_security_value'));
					}
					if (!empty($http_headers_tmp))
					{
						$http_headers_value = join('; ', $http_headers_tmp);
					}
					break;
				case 'hh_timing_allow_origin':
					if ($http_headers_value == 'origin')
					{
						$http_headers_value = esc_html(get_option('hh_timing_allow_origin_url'));
					}
					break;
				case 'hh_access_control_allow_origin':
					if ($http_headers_value == 'origin')
					{
					    $http_headers_value = join('<br>', array_map('esc_html', get_option('hh_access_control_allow_origin_url', array())));
					}
					break;
				case 'hh_access_control_expose_headers':
				case 'hh_access_control_allow_headers':
				case 'hh_access_control_allow_methods':
					$http_headers_value = join(', ', array_keys($http_headers_value));
					break;
				case 'hh_content_security_policy':
				    $http_headers_value = http_headers_build_csp_value($http_headers_value, true);
					if (get_option('hh_content_security_policy_report_only')) {
						$http_headers_item[0] .= '-Report-Only';
					}
					break;
				case 'hh_content_encoding':
					$http_headers_value = !$http_headers_value ? null : join(', ', array_keys($http_headers_value));
					
					$http_headers_ext = get_option('hh_content_encoding_ext');
					if (!empty($http_headers_ext)) {
						$http_headers_ext = join(', ', array_keys($http_headers_ext));
						$http_headers_value .= (!empty($http_headers_value) ? '<br>' : null) . $http_headers_ext;
					}
					$http_headers_module = get_option('hh_content_encoding_module');
					switch ($http_headers_module) {
					    case 'brotli_deflate':
					        $http_headers_enc = 'br, gzip';
					        break;
					    case 'brotli':
					        $http_headers_enc = 'br';
					        break;
					    case 'deflate':
					    default:
					        $http_headers_enc = 'gzip';
					        break;
					}
					
					$http_headers_value = !empty($http_headers_value) ? sprintf('%s (%s)', $http_headers_enc, $http_headers_value) : $http_headers_enc;
					break;
				case 'hh_vary':
					$http_headers_value = !$http_headers_value ? null : join(', ', array_keys($http_headers_value));
					break;
				case 'hh_www_authenticate':
					$http_headers_value = esc_html(get_option('hh_www_authenticate_type'));
					break;
				case 'hh_cache_control':
					$http_headers_tmp = array();
					foreach ($http_headers_value as $http_headers_k => $http_headers_v) {
						if (in_array($http_headers_k, array('max-age', 's-maxage', 'stale-while-revalidate', 'stale-if-error'))) {
							if (strlen($http_headers_v) > 0) {
								$http_headers_tmp[] = sprintf("%s=%u", $http_headers_k, $http_headers_v);
							}
						} else {
							$http_headers_tmp[] = $http_headers_k;
						}
					}
					$http_headers_value = join(', ', $http_headers_tmp);
					break;
				case 'hh_expires':
					$http_headers_tmp = array();
					$http_headers_types = get_option('hh_expires_type', array());
					foreach ($http_headers_types as $http_headers_type => $http_headers_whatever) {
						list($http_headers_base, $http_headers_period, $http_headers_suffix) = explode('_', $http_headers_value[$http_headers_type]);
						if (in_array($http_headers_base, array('access', 'modification'))) {
							$http_headers_tmp[] = $http_headers_type != 'default'
								? sprintf('%s = "%s plus %u %s"', $http_headers_type, $http_headers_base, $http_headers_period, $http_headers_suffix)
								: sprintf('default = "%s plus %u %s"', $http_headers_base, $http_headers_period, $http_headers_suffix);
						} elseif ($http_headers_base == 'invalid') {
							$http_headers_tmp[] = $http_headers_type != 'default'
								? sprintf('%s = A0', $http_headers_type)
								: sprintf('default = A0');
						}
					}
					$http_headers_value = join('<br>', $http_headers_tmp);
					break;
				case 'hh_cookie_security':
				    if (is_array($http_headers_value)) {
				        if (isset($http_headers_value['SameSite']) && !http_headers_is_samesite_supported()) {
				            unset($http_headers_value['SameSite']);
                        }
                    }
					$http_headers_value = is_array($http_headers_value) && !empty($http_headers_value)
                        ? '&#10004; ' . join(' &#10004; ', array_keys($http_headers_value))
                        : NULL;
					break;
				case 'hh_expect_ct':
					$http_headers_tmp = array();
					$http_headers_tmp[] = sprintf('max-age=%u', get_option('hh_expect_ct_max_age'));
					if (get_option('hh_expect_ct_enforce') == 1) {
						$http_headers_tmp[] = 'enforce';
					}
					$http_headers_tmp[] = sprintf('report-uri="%s"', esc_html(get_option('hh_expect_ct_report_uri')));
					$http_headers_value = join(', ', $http_headers_tmp); 
					break;
				case 'hh_custom_headers':
					$http_headers__names = array($http_headers_item[0]);
					$http_headers__values = array('&nbsp;');
					foreach ($http_headers_value['name'] as $http_headers_key => $http_headers_name)
					{
						if (!empty($http_headers_name) && !empty($http_headers_value['value'][$http_headers_key]))
						{
							$http_headers__names[] = '<p class="hh-p">&nbsp;&nbsp;&nbsp;&nbsp;'.esc_html($http_headers_name).'</p>';
							$http_headers__values[] = '<p class="hh-p">'.esc_html($http_headers_value['value'][$http_headers_key]).'</p>';
						}
					}
					$http_headers_item[0] = join('', $http_headers__names);
					$http_headers_value = join('', $http_headers__values);
					break;
				case 'hh_report_to':
				    $http_headers_value = esc_html(http_headers_get_http_header('report_to'));
				    break;
				case 'hh_nel':
				    $http_headers_value = esc_html(http_headers_get_http_header('nel'));
				    break;
				case 'hh_feature_policy':
				    $http_headers_value = esc_html(http_headers_get_http_header('feature_policy'));
					break;
				case 'hh_permissions_policy':
				    $http_headers_value = esc_html(http_headers_get_http_header('permissions_policy'));
				    break;
				case 'hh_x_robots_tag':
					$http_headers_value = esc_html(http_headers_get_http_header('x_robots_tag'));
					break;
				case 'hh_clear_site_data':
				    $http_headers_value = '"' . join('", "', array_keys($http_headers_value)) . '"';
				    break;
                case 'hh_content_type':
                    $http_headers_tmp = array();
                    foreach ($http_headers_value as $http_headers_key => $http_headers_val)  {
                        $http_headers_tmp[] = sprintf(".%s => %s", $http_headers_key, $http_headers_val);
                    }
                    $http_headers_value = join("<br>", $http_headers_tmp);
                    break;
				default:
					$http_headers_value = !is_array($http_headers_value) ? $http_headers_value : join(', ', $http_headers_value);
			}
		}
		$http_headers_status = $http_headers_isOn ? __('On', 'http-headers') : __('Off', 'http-headers');
		?>
		<tr<?php echo $http_headers_isOn ? ' class="active"' : NULL; ?>>
			<td><?php echo $http_headers_item[0]; ?></td>
			<td><?php echo $http_headers_value; ?></td>
			<td class="hh-status hh-status-<?php echo $http_headers_isOn ? 'on' : 'off'; ?>"><span><?php echo esc_html($http_headers_status); ?></span></td>
			<td><a href="<?php echo esc_url(get_admin_url()); ?>options-general.php?page=http-headers&header=<?php
				echo esc_attr($http_headers_index); ?>"><?php esc_html_e('Edit', 'http-headers'); ?></a></td>
		</tr>
		<?php
	}
	?>
	</tbody>
</table>