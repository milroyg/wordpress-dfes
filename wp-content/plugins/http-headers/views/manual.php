<?php 
if (!defined('ABSPATH')) {
    exit;
}
include dirname(__FILE__) . '/includes/breadcrumbs.inc.php';
?>
<div class="hh-tabs">
	<ul>
		<li class="hh-active"><a href="#hh-tab-1">Apache</a></li>
		<li><a href="#hh-tab-2">Nginx</a></li>
	</ul>
	<div id="hh-tab-1" class="hh-tab-active">
		<h3><span class="hh-highlight"><?php echo esc_html(http_headers_get_htaccess_filename()); ?></span></h3>
		<textarea class="hh-textarea-manual" rows="20" readonly><?php 
	$http_headers_lines = http_headers_apache_headers_directives();
	if ($http_headers_lines)
	{
	    echo esc_textarea(join("\n", $http_headers_lines));
	    echo "\n\n";
	}
	
	$http_headers_lines = http_headers_apache_auth_directives();
	if ($http_headers_lines)
	{
	   echo esc_textarea(join("\n", $http_headers_lines));
	   echo "\n\n";
	}
	
	$http_headers_lines = http_headers_apache_content_encoding_directives();
	if ($http_headers_lines)
	{
	    echo esc_textarea(join("\n", $http_headers_lines));
	    echo "\n\n";
	}
	
	$http_headers_lines = http_headers_apache_expires_directives();
	if ($http_headers_lines)
	{
	    echo esc_textarea(join("\n", $http_headers_lines));
	    echo "\n\n";
	}
	
	$http_headers_lines = http_headers_apache_cookie_security_directives();
	if ($http_headers_lines)
	{
	    echo esc_textarea(join("\n", $http_headers_lines));
	    echo "\n\n";
	}
	
	$http_headers_lines = http_headers_apache_timing_directives();
	echo esc_textarea(join("\n", $http_headers_lines));
	?></textarea>
	<?php 
	$http_headers_credentials = http_headers_apache_auth_credentials();
	if ($http_headers_credentials)
	{
	    ?>
	    <h3><span class="hh-highlight"><?php echo esc_html($http_headers_credentials['ht_file']); ?></span></h3>
	    <textarea class="hh-textarea-manual" rows="5" readonly><?php 
	    echo esc_textarea($http_headers_credentials['auth']);
	    ?></textarea><?php
	}
	?>
	</div>
	<div id="hh-tab-2" class="hh-hidden">
		<textarea class="hh-textarea-manual" rows="20" readonly><?php 
		$http_headers_lines = http_headers_nginx_headers_directives();
		if ($http_headers_lines)
		{
		    echo esc_textarea(join("\n", $http_headers_lines));
		    echo "\n\n";
		}
		
		$http_headers_lines = http_headers_nginx_auth_directives();
		if ($http_headers_lines)
		{
		    echo esc_textarea(join("\n", $http_headers_lines));
		    echo "\n\n";
		}
		
		$http_headers_lines = http_headers_nginx_content_encoding_directives();
		if ($http_headers_lines)
		{
		    echo esc_textarea(join("\n", $http_headers_lines));
		    echo "\n\n";
		}
		
		$http_headers_lines = http_headers_nginx_expires_directives();
		if ($http_headers_lines)
		{
		    echo esc_textarea(join("\n", $http_headers_lines));
		    echo "\n\n";
		}
		
		$http_headers_lines = http_headers_nginx_cookie_security_directives();
		if ($http_headers_lines)
		{
		    echo esc_textarea(join("\n", $http_headers_lines));
		    echo "\n\n";
		}
		
		$http_headers_lines = http_headers_nginx_timing_directives();
		if ($http_headers_lines)
		{
		    echo esc_textarea(join("\n", $http_headers_lines));
		    echo "\n\n";
		}
		?></textarea>
		<?php 
		$http_headers_credentials = http_headers_nginx_auth_credentials();
    	if ($http_headers_credentials)
    	{
    	    ?>
    	    <h3><span class="hh-highlight"><?php echo esc_html($http_headers_credentials['ht_file']); ?></span></h3>
    	    <textarea class="hh-textarea-manual" rows="5" readonly><?php 
    	    echo esc_textarea($http_headers_credentials['auth']);
    	    ?></textarea><?php
    	}
    	?>
	</div>
</div>