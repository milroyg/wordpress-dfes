<?php

namespace EACCustomWidgets\Admin\Settings\Templates;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div id='eac-dialog_elements-help' class='hidden' style='max-width:800px'>
	<p><?php esc_html_e( 'By activating this option, the plugin will calculate the count of publications that use each component at least once and display this count next to its title.', 'eac-components' ); ?></p>
	<p><?php esc_html_e( 'The count of publications is an active link which, when clicked, displays the list of publications in which the component is used.', 'eac-components' ); ?></p>
	<p><?php esc_html_e( 'Each item in the list is itself a link to the publication which opens in a new tab.', 'eac-components' ); ?></p>
	<p><a href='https://elementor-addon-components.com/improve-page-loading-speed/#use-the-element-usage-option' target='_autre' rel='noopener noreferrer'><?php esc_html_e( 'Follow this link', 'eac-components' ); ?></a></p>
</div>
