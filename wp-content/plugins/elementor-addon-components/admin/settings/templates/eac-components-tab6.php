<?php
/**
 * Description: Charge les options d'intégration WooCommerce
 * Affiche l'interface du formulaure 'WC integration'
 * Page de configuration du plugin
 *
 * @since 2.0.1
 */

namespace EACCustomWidgets\Admin\Settings\Templates;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Core\Eac_Load_Config;

/** Initialisation de la liste des pages */
$posts_list = array( '' => esc_html__( 'Select...', 'eac-components' ) );

$data = get_posts(
	array(
		'post_type'      => 'page',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	)
);

if ( ! empty( $data ) && ! is_wp_error( $data ) ) {
	foreach ( $data as $key ) {
		$posts_list[ $key->ID ] = $key->post_title;
	}
}

/** Les constantes et informations des balises HTML du formulaire */
$key_product_select_page  = 'wc_product_select_page';
$key_product_redirect     = 'wc_product_redirect_url';
$key_product_catalog      = 'wc_product_catalog';
$key_product_request      = 'wc_product_request';
$key_product_pages        = 'wc_product_redirect_pages';
$key_product_breadcrumb   = 'wc_product_breadcrumb';
$key_product_metas        = 'wc_product_metas';
$first                    = esc_html__( "The 'Shop and Cart' pages will be redirected to the selected page in the list", 'eac-components' );
$second                   = esc_html__( 'The Cart page will be emptied of its items', 'eac-components' );
$info_product_pages       = sprintf( '%1$s<br />%2$s', $first, $second );

/** Charge les options pour renseigner les champs de la page */
$options = get_option( Eac_Load_Config::get_woo_hooks_option_name() );

if ( $options ) {
	$active_product_id         = absint( $options['product-page']['shop']['id'] );
	$active_product_redirect   = isset( $options['product-page']['redirect_buttons'] ) ? $options['product-page']['redirect_buttons'] : $options['product-page']['redirect'];
	$active_product_catalog    = isset( $options['catalog']['active'] ) ? $options['catalog']['active'] : $options['catalog'];
	$active_product_request    = isset( $options['catalog']['request_quote'] ) ? $options['catalog']['request_quote'] : false;
	$active_product_pages      = isset( $options['redirect_pages'] ) ? $options['redirect_pages'] : false;
	$active_product_breadcrumb = $options['product-page']['breadcrumb'];
	$active_product_metas      = $options['product-page']['metas'];
} else {
	$active_product_id         = (int) 0;
	$active_product_redirect   = false;
	$active_product_catalog    = false;
	$active_product_request    = false;
	$active_product_pages      = false;
	$active_product_breadcrumb = false;
	$active_product_metas      = false;
}

/** Les class des éléments du formulaire */
$class_wrapper_config     = 'eac-elements__common-item config';
$class_wrapper_select     = 'eac-elements__common-item select';
$class_wrapper_redirect   = 'eac-elements__common-item redirect';
$class_wrapper_catalog    = 'eac-elements__common-item catalog';
$class_wrapper_request    = 'eac-elements__common-item request';
$class_wrapper_pages      = 'eac-elements__common-item pages';
$class_wrapper_breadcrumb = 'eac-elements__common-item breadcrumb';
$class_wrapper_metas      = 'eac-elements__common-item metas';

/** Pas d'ID de la liste des pages, on cache certaines DIVs */
if ( 0 === $active_product_id ) {
	$class_wrapper_redirect   = 'eac-elements__common-item redirect hide';
	$class_wrapper_breadcrumb = 'eac-elements__common-item breadcrumb hide';
	$class_wrapper_metas      = 'eac-elements__common-item metas hide';
	$class_wrapper_pages      = 'eac-elements__common-item pages hide';
}

/** L'option catalog n'est pas active, on cache certaines DIVs */
if ( ! $active_product_catalog ) {
	$class_wrapper_request = 'eac-elements__common-item request hide';
	$class_wrapper_pages   = 'eac-elements__common-item pages hide';
} elseif ( $active_product_catalog && 0 !== $active_product_id ) {
	$class_wrapper_pages = 'eac-elements__common-item pages';
}

ob_start();
?>
<form action='' method='POST' id='eac-form-wc-integration' name='eac-form-wc-integration'>
	<!-- Onglet 'WC integration' -->
	<div id='tab-6' style='display: none;'>
		<div class='eac-settings-tabs'>
			<div class='eac-elements__table-common wc'>

				<div class="<?php echo esc_attr( $class_wrapper_config ); ?>">
					<span class='info'><?php esc_html_e( 'For a perfect integration of the product grid in your showcase site, you must define the behavior of WooCommerce links, buttons and pages', 'eac-components' ); ?></span>
				</div>

				<div class="<?php echo esc_attr( $class_wrapper_select ); ?>">
					<span class='eac-elements__item-content'><?php esc_html_e( 'Select product page', 'eac-components' ); ?></span>
					<span style='margin: 13.3px 10px;'>
						<select name="<?php echo esc_attr( $key_product_select_page ); ?>" id="<?php echo esc_attr( $key_product_select_page ); ?>">
							<?php
							foreach ( $posts_list as $ident => $widget_title ) {
								if ( $ident === $active_product_id ) {
									echo '<option value="' . esc_attr( $ident ) . '" selected>' . esc_attr( $widget_title ) . '</option>';
								} else {
									echo '<option value="' . esc_attr( $ident ) . '">' . esc_attr( $widget_title ) . '</option>';
								}
							}
							?>
						</select>
					</span>
					<span class='info'><?php esc_html_e( 'The page you created with the WC Products Grid component', 'eac-components' ); ?></span>
				</div>

				<div class="<?php echo esc_attr( $class_wrapper_catalog ); ?>">
					<span class='eac-elements__item-content'><?php esc_html_e( 'Catalog', 'eac-components' ); ?>
						<a class='eac-accessible-link' href="<?php echo esc_url( 'https://elementor-addon-components.com/woocommerce-product-grid-for-elementor/#turn-your-woocommerce-store-into-a-catalog' ); ?>" target='_blank' rel='noopener noreferrer' aria-label="<?php esc_html_e( 'Open content in a new tab', 'eac-components' ); ?>">
							<span class='eac-admin-help'></span>
						</a>
					</span>
					<span>
						<label class='switch'>
							<input tabindex='-1' aria-hidden='true' type='checkbox' class='ios-switch bigswitch' id="<?php echo esc_attr( $key_product_catalog ); ?>" name="<?php echo esc_attr( $key_product_catalog ); ?>" <?php checked( 1, $active_product_catalog, true ); ?>>
							<div tabindex='0' role='checkbox' <?php checked( 1, $active_product_catalog, true ); ?> aria-label="<?php printf( '%1$s - %2$s', esc_html__( 'Enable/Disable', 'eac-components' ), esc_html__( 'Catalog', 'eac-components' ) ); ?>"><div></div></div>
						</label>
					</span>
					<span class='info'><?php esc_html_e( 'Turns the store into a product catalog without the possibility of purchase', 'eac-components' ); ?></span>
				</div>

				<div class="<?php echo esc_attr( $class_wrapper_request ); ?>">
					<span class='eac-elements__item-content'><?php esc_html_e( 'Request a quote', 'eac-components' ); ?></span>
					<span>
						<label class='switch'>
							<input tabindex='-1' aria-hidden='true' type='checkbox' class='ios-switch bigswitch' id="<?php echo esc_attr( $key_product_request ); ?>" name="<?php echo esc_attr( $key_product_request ); ?>" <?php checked( 1, $active_product_request, true ); ?>>
							<div tabindex='0' role='checkbox' <?php checked( 1, $active_product_request, true ); ?> aria-label="<?php printf( '%1$s - %2$s', esc_html__( 'Enable/Disable', 'eac-components' ), esc_html__( 'Request a quote', 'eac-components' ) ); ?>"><div></div></div>
						</label>
					</span>
					<span class='info'><?php esc_html_e( 'Product Page: display a message to request a quote', 'eac-components' ); ?></span>
				</div>

				<div class="<?php echo esc_attr( $class_wrapper_pages ); ?>">
					<span class='eac-elements__item-content'><?php esc_html_e( 'Redirect page URLs', 'eac-components' ); ?></span>
					<span>
						<label class='switch'>
							<input tabindex='-1' aria-hidden='true' type='checkbox' class='ios-switch bigswitch' id="<?php echo esc_attr( $key_product_pages ); ?>" name="<?php echo esc_attr( $key_product_pages ); ?>" <?php checked( 1, $active_product_pages, true ); ?>>
							<div tabindex='0' role='checkbox' <?php checked( 1, $active_product_pages, true ); ?> aria-label="<?php printf( '%1$s - %2$s', esc_html__( 'Enable/Disable', 'eac-components' ), esc_html__( 'Redirect page URLs', 'eac-components' ) ); ?>"><div></div></div>
						</label>
					</span>
					<span class='info'><?php echo $info_product_pages; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				</div>

				<div class="<?php echo esc_attr( $class_wrapper_redirect ); ?>">
					<span class='eac-elements__item-content'><?php esc_html_e( 'Redirect button URLs', 'eac-components' ); ?></span>
					<span>
						<label class='switch'>
							<input tabindex='-1' aria-hidden='true' type='checkbox' class='ios-switch bigswitch' id="<?php echo esc_attr( $key_product_redirect ); ?>" name="<?php echo esc_attr( $key_product_redirect ); ?>" <?php checked( 1, $active_product_redirect, true ); ?>>
							<div tabindex='0' role='checkbox' <?php checked( 1, $active_product_redirect, true ); ?> aria-label="<?php printf( '%1$s - %2$s', esc_html__( 'Enable/Disable', 'eac-components' ), esc_html__( 'Redirect button URLs', 'eac-components' ) ); ?>"><div></div></div>
						</label>
					</span>
					<span class='info'><?php esc_html_e( "Shopping Cart Page: the 'Return to shop' and 'Continue shopping' buttons will be redirected to the selected page in the list", 'eac-components' ); ?></span>
				</div>

				<div class="<?php echo esc_attr( $class_wrapper_breadcrumb ); ?>">
					<span class='eac-elements__item-content'><?php esc_html_e( 'Redirect breadcrumb', 'eac-components' ); ?></span>
					<span>
						<label class='switch'>
							<input tabindex='-1' aria-hidden='true' type='checkbox' class='ios-switch bigswitch' id="<?php echo esc_attr( $key_product_breadcrumb ); ?>" name="<?php echo esc_attr( $key_product_breadcrumb ); ?>" <?php checked( 1, $active_product_breadcrumb, true ); ?>>
							<div tabindex='0' role='checkbox' <?php checked( 1, $active_product_breadcrumb, true ); ?> aria-label="<?php printf( '%1$s - %2$s', esc_html__( 'Enable/Disable', 'eac-components' ), esc_html__( 'Redirect breadcrumb', 'eac-components' ) ); ?>"><div></div></div>
						</label>
					</span>
					<span class='info'><?php esc_html_e( "Product Page:  the breadcrumb 'category' link will be redirected to the selected page in the list", 'eac-components' ); ?></span>
				</div>

				<div class="<?php echo esc_attr( $class_wrapper_metas ); ?>">
					<span class='eac-elements__item-content'><?php esc_html_e( 'Redirect meta tag URLs', 'eac-components' ); ?></span>
					<span>
						<label class='switch'>
							<input tabindex='-1' aria-hidden='true' type='checkbox' class='ios-switch bigswitch' id="<?php echo esc_attr( $key_product_metas ); ?>" name="<?php echo esc_attr( $key_product_metas ); ?>" <?php checked( 1, $active_product_metas, true ); ?>>
							<div tabindex='0' role='checkbox' <?php checked( 1, $active_product_metas, true ); ?> aria-label="<?php printf( '%1$s - %2$s', esc_html__( 'Enable/Disable', 'eac-components' ), esc_html__( 'Redirect meta tag URLs', 'eac-components' ) ); ?>"><div></div></div>
						</label>
					</span>
					<span class='info'><?php esc_html_e( 'Product Page: meta tag URLs will be redirected to the selected page in the list', 'eac-components' ); ?></span>
				</div>

			</div> <!-- Table common -->
		</div> <!-- Settings TAB -->
	</div> <!-- TAB 6 -->

	<div class='eac-saving-box'>
		<div id='eac-wc-integration-to-save'><?php esc_html_e( 'You need to save the settings', 'eac-components' ); ?></div>
		<input id='eac-wc-submit' type='submit' value="<?php esc_html_e( 'Save changes', 'eac-components' ); ?>">
		<div id='eac-wc-integration-saved'></div>
		<div id='eac-wc-integration-notsaved'></div>
	</div>
</form>
<?php
echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
