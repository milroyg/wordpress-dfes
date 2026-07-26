<?php
if (!defined('ABSPATH')) {
	exit;
}
if (!(isset($_POST['url']) && preg_match('|^https?://|', sanitize_text_field(wp_unslash($_POST['url'])))))
{
	?>
	<section class="hh-panel">
		<h3><span class="hh-highlight"><?php esc_html_e('URL malformed', 'http-headers'); ?></span></h3>
	</section>
	<?php
	exit;
}

include 'includes/config.inc.php';

$http_headers_args = array();

if (isset($_POST['authentication'], $_POST['username'], $_POST['password'])
	&& !empty($_POST['username'])
	&& !empty($_POST['password'])
)
{
    $http_headers_args['headers'] = array(
        'Authorization' => sprintf('Basic %s', base64_encode(sanitize_text_field(wp_unslash($_POST['username'])) .':'. sanitize_text_field(wp_unslash($_POST['password']))))
    );
}

$http_headers_response = wp_safe_remote_head(sanitize_text_field(wp_unslash($_POST['url'])), $http_headers_args);
$http_headers_status = wp_remote_retrieve_response_code($http_headers_response);
$http_headers_dictionary = wp_remote_retrieve_headers($http_headers_response);
$http_headers_responseHeaders = $http_headers_dictionary ? $http_headers_dictionary->getAll() : array();

if ($http_headers_status !== 200)
{
	?>
	<section class="hh-panel">
		<h3><span class="hh-highlight"><?php esc_html_e('HTTP Status', 'http-headers'); ?>: <?php echo esc_html($http_headers_status); ?></span></h3>
		<p><?php
		switch ($http_headers_status)
		{
			case 400:
				echo 'Bad Request';
				break;
			case 401:
				echo 'Unauthorized';
				break;
			case 403:
				echo 'Forbidden';
				break;
			case 404:
				echo 'Not Found';
				break;
			case 405:
				echo 'Method Not Allowed';
				break;
			default:
		}
		?></p>
	</section>
	<?php
	exit;
}
?>
<section class="hh-panel">
	<h3><span class="hh-highlight"><?php esc_html_e('Response headers', 'http-headers'); ?></span></h3>
	<table class="hh-results">
		<thead>
			<tr>
				<th style="width: 30%"><?php esc_html_e('Header', 'http-headers'); ?></th>
				<th><?php esc_html_e('Value', 'http-headers'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php 
		$http_headers_reportOnly = array('content-security-policy-report-only');
		foreach ($http_headers_responseHeaders as $http_headers_k => $http_headers_v)
		{
			$http_headers_k = strtolower($http_headers_k);
			$http_headers_found = in_array($http_headers_k, $http_headers_reportOnly);
			$http_headers_v = is_array($http_headers_v) ? join(", ", $http_headers_v) : $http_headers_v;
			?>
			<tr<?php echo array_key_exists($http_headers_k, $http_headers_headers) || $http_headers_found ? ' class="hh-found"' : NULL; ?>>
				<td><?php echo esc_html($http_headers_k); ?></td>
				<td><?php echo esc_html($http_headers_v); ?></td>
			</tr>
			<?php
		}
		?>
		</tbody>
	</table>
</section>
<?php
$http_headers_special = array('content-security-policy');
$http_headers_exclude = array('custom-headers', 'cookie-security', 'x-powered-by');
$http_headers_missing = array();
foreach ($http_headers_headers as $http_headers_k => $http_headers_v)
{
	if (!array_key_exists($http_headers_k, $http_headers_responseHeaders)
	    && !in_array($http_headers_k, $http_headers_exclude)
	    && !(in_array($http_headers_k, $http_headers_special) && array_key_exists($http_headers_k . '-report-only', $http_headers_responseHeaders) ))
	{
		$http_headers_missing[$http_headers_k] = isset($http_headers_categories[$http_headers_v[2]]) ? $http_headers_categories[$http_headers_v[2]] : 'Other';
	}
}

if (!empty($http_headers_missing))
{
	asort($http_headers_missing);
	?>
	<section class="hh-panel">
		<h3><span class="hh-highlight"><?php esc_html_e('Missing headers', 'http-headers'); ?></span></h3>
		<table class="hh-results">
			<thead>
				<tr>
					<th style="width: 30%"><?php esc_html_e('Header', 'http-headers'); ?></th>
					<th><?php esc_html_e('Category', 'http-headers'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
			foreach ($http_headers_missing as $http_headers_k => $http_headers_v)
			{
				?>
				<tr>
					<td><a href="<?php echo esc_url(get_admin_url()); ?>options-general.php?page=http-headers&amp;header=<?php echo esc_attr($http_headers_k); ?>"><?php echo esc_html($http_headers_k); ?></a></td>
					<td><?php echo esc_html($http_headers_v); ?></td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>
	</section>
	<?php
}