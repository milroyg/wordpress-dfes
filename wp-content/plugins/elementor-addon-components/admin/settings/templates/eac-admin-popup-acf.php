<?php

namespace EACCustomWidgets\Admin\Settings\Templates;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div id='eac-dialog_acf-json' class='hidden' style='max-width:800px'>
<h3><?php esc_html_e( 'Folder ACF-JSON', 'eac-components' ); ?></h3>
<p><?php esc_html_e( "This feature will create the 'acf-json' folder in the plugin if it does not exist in the current theme.", 'eac-components' ); ?></p>
<p><?php esc_html_e( 'ACF groups and fields will be saved locally in this folder in JSON format.', 'eac-components' ); ?></p>
<p><?php esc_html_e( 'The idea is similar to caching, and both greatly speeds up ACF and allows version control over your field settings.', 'eac-components' ); ?></p>
<p><?php esc_html_e( "Folder '/includes/acf'", 'eac-components' ); ?></p>
<p><a href='https://www.advancedcustomfields.com/resources/local-json/' target='_autre' rel='noopener noreferrer'><?php esc_html_e( 'Follow this link', 'eac-components' ); ?></a></p>
</div>
