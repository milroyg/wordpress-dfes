<?php

namespace EACCustomWidgets\Admin\Settings\Templates;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Core\Eac_Load_Config;
?>
<div class='eac-header-settings'>
	<div class='eac-header'>
		<div>
			<img class='eac-logo' src="<?php echo esc_url( EAC_PLUGIN_URL ) . 'admin/images/logos/eac-03.svg'; ?>" />
		</div>
		<div>
			<h1 class='eac-title-main'><?php echo esc_html( 'Elementor Addon Components' ); ?></h1>
			<p class='eac-title-version'>Version: <?php echo esc_attr( EAC_PLUGIN_VERSION ); ?></p>
		</div>
	</div>
	<div class='eac-stat'>
		<div class='eac-stat__items'>
			<div class='eac-stat__item'>
				<p class='eac-stat__count'><?php echo absint( Eac_Load_Config::get_count_all_elements() ); ?></p>
				<h2 class='eac-stat__title'><?php esc_html_e( 'Components', 'eac-components' ); ?></h2>
			</div>
			<div class='eac-stat__item'>
				<p class='eac-stat__count'><?php echo absint( Eac_Load_Config::get_count_enabled_elements() ); ?></p>
				<h2 class='eac-stat__title'><?php esc_html_e( 'Active components', 'eac-components' ); ?></h2>
			</div>
			<div class='eac-stat__item'>
			<p class='eac-stat__count'><?php echo absint( Eac_Load_Config::get_count_disabled_elements() ); ?></p>
				<h2 class='eac-stat__title'><?php esc_html_e( 'Inactive components', 'eac-components' ); ?></h2>
			</div>
		</div>
		<div class='eac-languages'>
			<h2 class='eac-title-languages'><?php esc_html_e( 'Internationalization', 'eac-components' ); ?></h2>
			<div class='eac-languages__support'>
				<span class='eac-language__support'><?php esc_html_e( 'English (en_US) is the default language', 'eac-components' ); ?></span>
				<span class='eac-language__support'><?php esc_html_e( 'French (fr_FR), Spanish (es_ES), Italian (it_IT) and Hindi (hi_IN) language support', 'eac-components' ); ?></span>
				<span class='eac-language__support'><?php esc_html_e( 'RTL language support', 'eac-components' ); ?></span>
			</div>
		</div>
	</div>
</div>
