<?php

namespace EACCustomWidgets\Admin\Settings\Templates;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use EACCustomWidgets\Core\Eac_Load_Config;
?>
<div class='eac-settings'>
	<ul class='tabs-nav'>
		<li class='tab-active'><a class='tab-1' href='#tab-1' rel='nofollow' aria-label="<?php esc_html_e( 'Advanced components', 'eac-components' ); ?>"><?php esc_html_e( 'Advanced', 'eac-components' ); ?></a></li>
		<li class=''><a class='tab-2' href='#tab-2' rel='nofollow' aria-label="<?php esc_html_e( 'Common components', 'eac-components' ); ?>"><?php esc_html_e( 'Basic', 'eac-components' ); ?></a></li>
		<li class=''><a class='tab-3' href='#tab-3' rel='nofollow' aria-label="<?php esc_html_e( 'Header Footer components', 'eac-components' ); ?>"><?php esc_html_e( 'Header & Footer', 'eac-components' ); ?></a></li>
		<li class=''><a class='tab-4' href='#tab-4' rel='nofollow' aria-label="<?php esc_html_e( 'Advanced features', 'eac-components' ); ?>"><?php esc_html_e( 'Features', 'eac-components' ); ?></a></li>
		<li class=''><a class='tab-5' href='#tab-5' rel='nofollow' aria-label="<?php esc_html_e( 'WordPress features', 'eac-components' ); ?>"><?php echo esc_html( 'WordPress' ); ?></a></li>
		<?php if ( Eac_Load_Config::is_widget_active( 'woo-product-grid' ) ) { ?>
			<li class=''><a class='tab-6' href='#tab-6' rel='nofollow' aria-label="<?php esc_html_e( 'Integration Woocommerce', 'eac-components' ); ?>"><?php esc_html_e( 'WC integration', 'eac-components' ); ?></a></li>
		<?php } ?>
		<li class=''><a class='tab-7' href='#tab-7' rel='nofollow'><?php esc_html_e( 'System Info', 'eac-components' ); ?></a></li>
	</ul>
</div>
