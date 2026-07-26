<?php 
if (!defined('ABSPATH')) {
    exit;
}
include dirname(__FILE__) . '/includes/config.inc.php';
include dirname(__FILE__) . '/includes/breadcrumbs.inc.php';
?>

<section class="hh-panel">
	<form method="post" action="options.php">
	    <table class="form-table hh-table">
			<tbody>
			<?php
            if (isset($_GET['header']))
            {
	            $http_headers_header_file = sprintf('%s/%s.php', dirname(__FILE__), basename(sanitize_text_field(wp_unslash($_GET['header']))));
	            if (is_file($http_headers_header_file))
	            {
		            include $http_headers_header_file;
	            }
            }
			?>
			</tbody>
		</table>
		<?php submit_button(); ?>
	</form>
</section>