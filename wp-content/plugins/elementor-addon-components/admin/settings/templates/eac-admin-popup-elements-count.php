<?php
/**
 *
 * Description: Fancybox affiche les URLs du nombre d'éléments dans la page de paramétrage
 *
 * @since 2.2.5
 */

namespace EACCustomWidgets\Admin\Settings\Templates;

if ( isset( $_SERVER['SCRIPT_FILENAME'] ) ) {
	$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
	require_once $parse_uri[0] . 'wp-load.php';
} else {
	exit;
}

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! isset( $_REQUEST['ajax_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['ajax_nonce'] ) ), 'elements_count_nonce' ) ) {
	header( 'Content-Type: text/plain' );
	echo esc_html( 'Invalid nonce' );
	exit;
}
?>
<div class='eac-elements__count'>
	<div class='eac-elements__count-title'></div>
</div>
<?php
