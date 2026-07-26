<?php
// If uninstall is not called from WordPress, exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

$http_headers_options = include dirname(__FILE__) . '/views/includes/options.inc.php';

foreach ($http_headers_options as $http_headers_option)
{
	delete_option( $http_headers_option[0] );
}