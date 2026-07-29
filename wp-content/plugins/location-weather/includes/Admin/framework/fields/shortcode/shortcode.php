<?php
/**
 * Framework shortcode field file.
 *
 * @link       https://shapedplugin.com/
 *
 * @package    Location_Weather
 * @subpackage Location_Weather/Includes/Admin
 * @author     ShapedPlugin <support@shapedplugin.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

if ( ! class_exists( 'SPLWT_Field_shortcode' ) ) {
	/**
	 * SP_EAP_Field_shortcode
	 */
	class SPLWT_Field_shortcode extends SPLWT_Fields {

		/**
		 * Field constructor.
		 *
		 * @param array  $field The field type.
		 * @param string $value The values of the field.
		 * @param string $unique The unique ID for the field.
		 * @param string $where To where show the output CSS.
		 * @param string $parent The parent args.
		 */
		public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
			parent::__construct( $field, $value, $unique, $where, $parent );
		}

		/**
		 * Render
		 *
		 * @return void
		 */
		public function render() {

			// Get the Post ID.
			$post_id = get_the_ID();
			if ( ! empty( $this->field['shortcode'] ) && 'shortcode' === $this->field['shortcode'] ) {
				if ( ! empty( $post_id ) ) {
					$block_editor_url = admin_url( 'post-new.php?post_type=spl_weather_template&splwblock_inserter' );
					echo '<div class="splw_shortcode-area">';
					echo '<p>' . sprintf(
						/* translators: 1: opening anchor tag to docs, 2: closing anchor tag. */
						esc_html__( 'To display the Location Weather, copy and paste this shortcode into your post, page, custom post, block editor, or page builder. %1$sLearn how%2$s to include it in your template file.', 'location-weather' ),
						'<a href="https://locationweather.io/docs/docs/how-to-use-location-weather-shortcode-to-your-theme-files-or-php-templates/" target="_blank">',
						'</a>'
					) . '</p>';
					echo '<span class="splw-code selectable">[location-weather id="' . esc_attr( $post_id ) . '"]</span>';
					echo '<div class="splw-live-editor-promo">';
					echo '<h4 class="splw-live-editor-promo__title">' . esc_html__( 'Want a Live Visual Editor?', 'location-weather' ) . '</h4>';
					echo '<p class="splw-live-editor-promo__desc">' . sprintf(
						/* translators: 1: opening anchor tag to the block-editor template, 2: closing anchor tag. */
						esc_html__( 'Design visually and see every change instantly. %1$sTry Block Editor ↗%2$s', 'location-weather' ),
						'<a class="splw-live-editor-promo__link" href="' . esc_url( $block_editor_url ) . '">',
						'</a>'
					) . '</p>';
					echo '</div>';
					echo '</div>';
					echo '<div class="splw-after-copy-text text-center"><i class="fa fa-check-circle"></i> ' . esc_html__( 'Shortcode Copied to Clipboard!', 'location-weather' ) . ' </div>';
				}
			} elseif ( ! empty( $this->field['shortcode'] ) && 'pro_notice' === $this->field['shortcode'] ) {
				if ( ! empty( $post_id ) ) {

					$features = array(
						array(
							'icon'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="24" height="24" rx="4" fill="#e8edfa"/><path d="M12.152 9.398a4.01 4.01 0 0 0 3.37 3.371l.862.13-.862.127a4.01 4.01 0 0 0-3.37 3.371l-.13.863-.128-.863a4.01 4.01 0 0 0-3.37-3.37l-.863-.129.863-.129a4.01 4.01 0 0 0 3.37-3.37l.129-.862zm-.116-3.377a3.714 3.714 0 0 1 3.668 3.103c.01.057.038.107.072.139.031.03.062.04.088.04 1.775 0 3.063 1.444 3.063 3.171 0 .854-.265 1.483-.604 2.003-.25.382-.729.81-1.419.99a.438.438 0 1 1-.22-.846 1.57 1.57 0 0 0 .906-.622c.268-.41.462-.875.462-1.525 0-1.291-.943-2.296-2.187-2.296-.559 0-.947-.447-1.024-.91a2.84 2.84 0 0 0-2.805-2.372 2.84 2.84 0 0 0-2.804 2.371c-.078.464-.466.91-1.024.91a2.3 2.3 0 0 0-1.627.67c-.382.383-.56.943-.56 1.627 0 .465.11.836.316 1.157.21.326.538.626 1.009.919a.439.439 0 0 1-.463.743c-.54-.336-.979-.72-1.281-1.19-.306-.475-.456-1.013-.456-1.629 0-.824.216-1.643.817-2.245a3.17 3.17 0 0 1 2.245-.926c.027 0 .057-.01.09-.04a.25.25 0 0 0 .07-.14 3.715 3.715 0 0 1 3.668-3.102" fill="#524ae5"/></svg>',
							'tile'  => '#E8EDFA',
							'label' => __( 'AI Weather Assistant', 'location-weather' ) . ' 🔥',
						),
						array(
							'icon'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="24" height="24" rx="4" fill="#f2ede3"/><g clip-path="url(#a)" fill="#f26c0d"><path d="M10.625 6.467c0-.292-.27-.592-.684-.592H6.559c-.414 0-.684.3-.684.592v3.441c0 .293.27.592.684.592h3.382c.413 0 .684-.3.684-.592zm.875 3.441c0 .845-.735 1.467-1.56 1.467H6.56c-.825 0-1.56-.622-1.56-1.467V6.467C5 5.622 5.734 5 6.56 5h3.38c.825 0 1.56.622 1.56 1.467zm6.625-3.441c0-.292-.27-.592-.684-.592h-3.382c-.413 0-.684.3-.684.592v3.441c0 .293.27.592.684.592h3.382c.414 0 .684-.3.684-.592zM19 9.908c0 .845-.735 1.467-1.56 1.467h-3.38c-.825 0-1.56-.622-1.56-1.467V6.467c0-.845.735-1.467 1.56-1.467h3.38c.825 0 1.56.622 1.56 1.467zm-8.375 4.059c0-.293-.27-.592-.684-.592H6.559c-.414 0-.684.3-.684.592v3.441c0 .293.27.592.684.592h3.382c.413 0 .684-.3.684-.592zm.875 3.441c0 .845-.735 1.467-1.56 1.467H6.56c-.825 0-1.56-.622-1.56-1.467v-3.441c0-.845.734-1.467 1.56-1.467h3.38c.825 0 1.56.622 1.56 1.467zm6.625-3.441c0-.293-.27-.592-.684-.592h-3.382c-.413 0-.684.3-.684.592v3.441c0 .293.27.592.684.592h3.382c.414 0 .684-.3.684-.592zM19 17.408c0 .845-.735 1.467-1.56 1.467h-3.38c-.825 0-1.56-.622-1.56-1.467v-3.441c0-.845.735-1.467 1.56-1.467h3.38c.825 0 1.56.622 1.56 1.467z"/></g><defs><clipPath id="a"><path fill="#fff" d="M5 5h14v14H5z"/></clipPath></defs></svg>',
							'tile'  => '#F2EDE3',
							'label' => __( '15+ Gutenberg Blocks', 'location-weather' ),
							'url'   => 'https://locationweather.io/#weather-showcase',
						),
						array(
							'icon'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="24" height="24" rx="4" fill="#f7edf5"/><g clip-path="url(#a)"><path d="M18.016 17.36v-1.532a.656.656 0 0 0-.653-.656l-1.196.029h-.01a.656.656 0 0 0-.657.657v1.53c0 .362.292.655.652.657l1.197-.03h.01a.656.656 0 0 0 .657-.656m-5.578-1.561a.656.656 0 0 0-.657-.657h-5.25a.656.656 0 0 0-.656.657v1.53c0 .363.294.657.656.657h5.25a.656.656 0 0 0 .656-.656zm5.687-9.332c0-.292-.27-.592-.684-.592H6.559c-.414 0-.684.3-.684.592v4.941c0 .293.27.592.684.592h10.882c.414 0 .684-.3.684-.592zm.766 10.892a1.53 1.53 0 0 1-1.521 1.531l-1.203.03h-.01a1.53 1.53 0 0 1-1.532-1.531v-1.531c0-.843.68-1.526 1.52-1.532l1.204-.03h.01c.846 0 1.532.686 1.532 1.532zm-5.578-.03c0 .847-.686 1.532-1.532 1.532h-5.25A1.53 1.53 0 0 1 5 17.33v-1.531c0-.846.686-1.532 1.531-1.532h5.25c.846 0 1.531.686 1.531 1.532zM19 11.41c0 .844-.735 1.466-1.56 1.466H6.56c-.825 0-1.56-.622-1.56-1.467V6.467C5 5.622 5.734 5 6.56 5h10.88c.825 0 1.56.622 1.56 1.467z" fill="#a21caf"/></g><defs><clipPath id="a"><path fill="#fff" d="M5 5h14v14H5z"/></clipPath></defs></svg>',
							'tile'  => '#F7EDF5',
							'label' => __( '200+ Ready Weather Patterns', 'location-weather' ),
						),
						array(
							'icon'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="24" height="24" rx="4" fill="#e2eef2"/><g clip-path="url(#a)"><path d="M16.977 10.824a4.977 4.977 0 0 0-9.954 0c0 .662.218 1.457.593 2.297.373.834.888 1.684 1.454 2.447s1.173 1.428 1.722 1.896c.274.234.526.413.744.53.224.12.377.158.464.158s.24-.037.464-.158c.218-.117.47-.296.744-.53.549-.468 1.156-1.132 1.722-1.896a13.4 13.4 0 0 0 1.454-2.447c.375-.84.593-1.635.593-2.297m-5.414 4.225v-2.215a.438.438 0 0 1 .874 0v2.215a.438.438 0 0 1-.874 0m-1.641-1.244v-.971a.438.438 0 0 1 .875 0v.97a.438.438 0 0 1-.875 0m3.281 0v-.971a.438.438 0 0 1 .875 0v.97a.438.438 0 0 1-.875 0M8.925 9.417a2.23 2.23 0 0 1 3.998-1.355q.168-.032.343-.033a1.809 1.809 0 1 1 0 3.618h-2.111a2.23 2.23 0 0 1-2.23-2.23m8.927 1.407c0 .834-.268 1.757-.67 2.653a14.3 14.3 0 0 1-1.55 2.612c-.595.804-1.246 1.52-1.856 2.04-.305.26-.608.48-.896.635-.281.152-.584.263-.88.263s-.599-.111-.88-.263a5.3 5.3 0 0 1-.896-.634c-.61-.52-1.261-1.237-1.857-2.041a14.3 14.3 0 0 1-1.55-2.612c-.4-.896-.669-1.82-.669-2.653a5.852 5.852 0 1 1 11.704 0M9.8 9.417c0 .749.606 1.355 1.354 1.355h2.112a.934.934 0 1 0-.36-1.796.44.44 0 0 1-.556-.197 1.354 1.354 0 0 0-2.55.638" fill="#0584c7"/></g><defs><clipPath id="a"><path fill="#fff" d="M5 5h14v14H5z"/></clipPath></defs></svg>',
							'tile'  => '#E2EEF2',
							'label' => __( 'Interactive Weather Map', 'location-weather' ),
						),
						array(
							'icon'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="24" height="24" rx="4" fill="#faebea"/><g clip-path="url(#a)"><path d="M7.01 17.107a.44.44 0 0 1 .55.283.902.902 0 1 0 .86-1.18H5.546a.438.438 0 0 1 0-.874h2.872a1.778 1.778 0 0 1 0 3.555 1.78 1.78 0 0 1-1.692-1.234.44.44 0 0 1 .283-.55m4.553 1.346V8.801a.438.438 0 0 1 .874 0v3.243l2.208-2.187a.438.438 0 0 1 .616.622l-2.823 2.797v1.212h2.46a3.117 3.117 0 0 0 2.227-5.298.44.44 0 0 1-.125-.286 3.063 3.063 0 0 0-4.757-2.406.44.44 0 0 1-.486 0 3.06 3.06 0 0 0-4.263.874.438.438 0 0 1-.732-.479A3.934 3.934 0 0 1 12 5.621a3.937 3.937 0 0 1 5.863 3.077 3.992 3.992 0 0 1-2.964 6.665h-2.461v3.09a.438.438 0 0 1-.876 0m-1.75-5.305a.438.438 0 0 1 0 .875H5.547a.437.437 0 1 1 0-.875zm-.493-3.09a.902.902 0 0 0-1.76-.276.438.438 0 0 1-.833-.267 1.777 1.777 0 1 1 1.692 2.32H5.547a.437.437 0 1 1 0-.874h2.872a.903.903 0 0 0 .901-.903" fill="#db2627"/></g><defs><clipPath id="a"><path fill="#fff" d="M5 5h14v14H5z"/></clipPath></defs></svg>',
							'tile'  => '#FAEBEA',
							'label' => __( 'Air Quality (AQI)', 'location-weather' ) . ' 🔥',
						),
						array(
							'icon'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="24" height="24" rx="4" fill="#e3edd8"/><g clip-path="url(#a)"><path d="M5.11 15.72V5.548a.437.437 0 1 1 .874 0V15.72a2.297 2.297 0 0 0 2.297 2.297h10.172a.438.438 0 0 1 0 .875H8.281A3.17 3.17 0 0 1 5.11 15.72m2.816.547V13.75a.438.438 0 0 1 .875 0v2.516a.437.437 0 1 1-.875 0m2.242 0v-5.06a.437.437 0 1 1 .875 0v5.06a.437.437 0 1 1-.875 0m2.242 0v-3.965a.437.437 0 1 1 .875 0v3.965a.438.438 0 0 1-.875 0m2.242 0v-3.528a.438.438 0 0 1 .875 0v3.528a.437.437 0 1 1-.875 0m2.243 0v-4.594a.438.438 0 0 1 .875 0v4.594a.437.437 0 1 1-.875 0m1.148-10.872a.437.437 0 0 1 .82.306c-.237.634-.692 1.933-1.343 3.07-.326.567-.714 1.119-1.167 1.532-.455.414-1.007.717-1.649.713-.604-.003-1.492-.304-2.148-.944-.683-.667-1.248-.872-1.693-.86-.682.018-1.291.495-1.792 1.13a7.5 7.5 0 0 0-.91 1.536.438.438 0 0 1-.8-.356A8.3 8.3 0 0 1 8.385 9.8c.548-.694 1.375-1.434 2.456-1.462.76-.02 1.54.341 2.327 1.109.493.48 1.164.693 1.543.696q.518.003 1.054-.485c.358-.327.693-.792.997-1.322.607-1.06 1.037-2.282 1.282-2.94" fill="#3a6d0f"/></g><defs><clipPath id="a"><path fill="#fff" d="M5 5h14v14H5z"/></clipPath></defs></svg>',
							'tile'  => '#E3EDD8',
							'label' => __( 'Weather & AQI Graph Charts', 'location-weather' ),
						),
						array(
							'icon'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="24" height="24" rx="4" fill="#efebfa"/><g clip-path="url(#a)"><path d="M16.266 5.11c.241 0 .437.195.437.437v.695a2.62 2.62 0 0 1 2.188 2.586v7.438a2.625 2.625 0 0 1-2.625 2.625H7.734a2.625 2.625 0 0 1-2.625-2.625V8.828c0-1.3.947-2.377 2.188-2.586v-.695a.437.437 0 1 1 .875 0v.656h3.39v-.656a.438.438 0 0 1 .876 0v.656h3.39v-.656c0-.242.196-.438.438-.438M7.734 7.077a1.75 1.75 0 0 0-1.75 1.75v7.438c0 .966.784 1.75 1.75 1.75h8.532a1.75 1.75 0 0 0 1.75-1.75V8.828a1.75 1.75 0 0 0-1.75-1.75zm2.079 8.64a.547.547 0 1 1 0 1.095.547.547 0 0 1 0-1.094m2.187 0a.547.547 0 1 1 0 1.095.547.547 0 0 1 0-1.094m2.188 0a.547.547 0 1 1 0 1.095.547.547 0 0 1 0-1.094M12 8.282c.986 0 1.865.282 2.503.855.56.503.9 1.202.98 2.042.67.166 1.33.728 1.33 1.697 0 .41-.163.832-.414 1.152-.25.32-.638.598-1.118.598H9.266a2.08 2.08 0 0 1-2.078-2.078c0-.923.703-1.822 1.763-1.973.232-.587.56-1.123 1.01-1.531.53-.478 1.208-.762 2.039-.762m0 .875c-.614 0-1.084.204-1.451.536-.376.34-.666.834-.87 1.437a.44.44 0 0 1-.413.298c-.736 0-1.203.577-1.203 1.12 0 .664.538 1.203 1.203 1.203h6.015c.124 0 .283-.076.429-.262.145-.184.227-.42.227-.613 0-.576-.45-.875-.874-.875a.44.44 0 0 1-.438-.437c0-.8-.273-1.387-.707-1.777-.44-.395-1.093-.63-1.918-.63" fill="#7c3aec"/></g><defs><clipPath id="a"><path fill="#fff" d="M5 5h14v14H5z"/></clipPath></defs></svg>',
							'tile'  => '#EFEBFA',
							'label' => __( '46-Year Historical Data', 'location-weather' ),
						),
						array(
							'icon'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="24" height="24" rx="4" fill="#faf2dc"/><g clip-path="url(#a)"><path d="M12.384 14.598a.89.89 0 0 0-.889-.889c-.488 0-.888.4-.888.889 0 .488.4.888.888.888.49 0 .889-.4.889-.888m-4.695-.435a.438.438 0 0 1 .618.618l-.882.883a.438.438 0 0 1-.62-.619zm-1.031-3.365a.438.438 0 0 1 0 .875H5.409a.437.437 0 1 1 0-.875zm.148-3.991a.44.44 0 0 1 .619 0l.882.883a.437.437 0 1 1-.618.618l-.883-.882a.44.44 0 0 1 0-.62m8.238 0a.438.438 0 0 1 .62.619l-.884.882a.437.437 0 0 1-.618-.618zm-4.247-.148V5.41a.438.438 0 0 1 .875 0v1.25a.438.438 0 0 1-.875 0m5.055 10.126c0 .21.173.383.383.383s.383-.173.383-.383a.385.385 0 0 0-.383-.383.384.384 0 0 0-.383.383m-2.593-2.187a1.766 1.766 0 0 1-2.172 1.715 3.81 3.81 0 0 0 3.258 1.84c.468 0 .916-.087 1.331-.242a1.258 1.258 0 0 1 .559-2.384 1.26 1.26 0 0 1 1.23.995 3.79 3.79 0 0 0-.187-4.604 1.26 1.26 0 0 1-1.213.928 1.26 1.26 0 0 1-1.258-1.257c0-.378.168-.716.432-.947a3.8 3.8 0 0 0-.894-.107 3.82 3.82 0 0 0-3.556 2.447 1.766 1.766 0 0 1 2.47 1.616m5.77-.255a4.69 4.69 0 0 1-4.684 4.684 4.69 4.69 0 0 1-4.684-4.684l.001-.055A3.44 3.44 0 0 1 7.8 11.236 3.44 3.44 0 0 1 11.235 7.8a3.44 3.44 0 0 1 3.05 1.86l.06-.001a4.69 4.69 0 0 1 4.684 4.683m-3.347-2.754c0 .21.173.382.383.382s.383-.172.383-.382a.385.385 0 0 0-.383-.383.385.385 0 0 0-.383.383m-7.007-.353a2.56 2.56 0 0 0 1.097 2.097 4.7 4.7 0 0 1 3.558-3.561 2.56 2.56 0 0 0-2.095-1.096c-1.41 0-2.56 1.15-2.56 2.56" fill="#b25308"/></g><defs><clipPath id="a"><path fill="#fff" d="M5 5h14v14H5z"/></clipPath></defs></svg>',
							'tile'  => '#FAF2DC',
							'label' => __( 'Astronomy & Sun & Moon', 'location-weather' ) . ' 🔥',
						),
						array(
							'icon'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="24" height="24" rx="4" fill="#e2f0e2"/><g clip-path="url(#a)"><path d="M14.106 14.42a2.106 2.106 0 1 0-4.212 0 2.106 2.106 0 0 0 4.212 0M5.11 13.209c0-.964.554-1.8 1.36-2.197a2.3 2.3 0 0 1-.182-.9c0-1.258 1.008-2.287 2.263-2.287q.21 0 .412.038a4.26 4.26 0 0 1 3.7-2.158c2.022 0 3.71 1.418 4.158 3.318a3.45 3.45 0 0 1 2.07 3.175 3.46 3.46 0 0 1-2.005 3.146.438.438 0 0 1-.365-.795 2.58 2.58 0 0 0 1.496-2.351 2.58 2.58 0 0 0-1.718-2.44.44.44 0 0 1-.288-.338c-.285-1.619-1.68-2.84-3.348-2.84a3.4 3.4 0 0 0-3.078 1.983.44.44 0 0 1-.552.225A1.4 1.4 0 0 0 8.55 8.7a1.4 1.4 0 0 0-1.388 1.412c0 .329.11.63.295.87a.437.437 0 0 1-.254.694 1.56 1.56 0 0 0-1.219 1.533c0 .819.616 1.484 1.392 1.56a.438.438 0 1 1-.085.87 2.43 2.43 0 0 1-2.182-2.43m9.872 1.211a2.97 2.97 0 0 1-.746 1.972l1.157 1.157a.438.438 0 0 1-.618.618l-1.21-1.209a2.982 2.982 0 1 1 1.417-2.538" fill="#16803c"/></g><defs><clipPath id="a"><path fill="#fff" d="M5 5h14v14H5z"/></clipPath></defs></svg>',
							'tile'  => '#E2F0E2',
							'label' => __( 'Global Weather Search', 'location-weather' ),
						),
						array(
							'icon'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="24" height="24" rx="4" fill="#faece2"/><g clip-path="url(#a)"><path d="M18.016 8.282a2.297 2.297 0 0 0-2.297-2.297H8.28a2.297 2.297 0 0 0-2.297 2.297v7.438a2.297 2.297 0 0 0 2.297 2.297h7.438a2.297 2.297 0 0 0 2.297-2.297zm-7.218.222c.492-.988 1.912-.988 2.404 0l.542 1.086c.067.135.198.23.351.252l1.212.174c1.095.158 1.545 1.5.742 2.275l-.876.845a.45.45 0 0 0-.133.403l.207 1.195c.19 1.097-.97 1.913-1.945 1.406l-1.083-.564a.48.48 0 0 0-.438 0l-1.083.564c-.976.507-2.135-.309-1.945-1.406l.207-1.195a.45.45 0 0 0-.133-.402l-.876-.846c-.803-.775-.353-2.117.742-2.275l1.212-.174a.47.47 0 0 0 .351-.252zm1.621.39a.47.47 0 0 0-.838 0l-.542 1.087a1.34 1.34 0 0 1-1.01.727l-1.211.174a.455.455 0 0 0-.26.779l.877.846c.317.306.463.748.387 1.181l-.207 1.195a.464.464 0 0 0 .679.48l1.083-.564a1.35 1.35 0 0 1 1.245 0l1.084.564a.465.465 0 0 0 .679-.48l-.207-1.194c-.076-.434.07-.876.387-1.182l.877-.846a.455.455 0 0 0-.26-.779l-1.211-.174a1.34 1.34 0 0 1-.971-.655l-.04-.072zm6.472 6.826a3.17 3.17 0 0 1-3.172 3.172H8.28a3.17 3.17 0 0 1-3.17-3.172V8.282A3.17 3.17 0 0 1 8.281 5.11h7.438a3.17 3.17 0 0 1 3.172 3.172z" fill="#ea6a1a"/></g><defs><clipPath id="a"><path fill="#fff" d="M5 5h14v14H5z"/></clipPath></defs></svg>',
							'tile'  => '#FAECE2',
							'label' => __( '12+ Weather Icon Packs', 'location-weather' ),
						),
					);

					echo '<div class="splw_shortcode-area lw-pro-notice-wrapper">';

					echo '<div class="lw-pro-notice-heading">' . sprintf(
						/* translators: 1: start span tag wrapping the product name, 2: close tag. */
						esc_html__( 'Power up with %1$sLocation Weather Pro%2$s', 'location-weather' ),
						'<span>',
						'</span>'
					) . '</div>';

					echo '<ul class="lw-pro-notice-features">';
					foreach ( $features as $feature ) {
						$tile_bg = esc_attr( $feature['tile'] );

						echo '<li>';
						echo '<span class="lw-pro-feature__icon">';
						echo $feature['icon']; // phpcs:ignore -- No user input.
						echo '</span>';

						if ( ! empty( $feature['url'] ) ) {
							echo '<a class="lw-pro-feature__link" href="' . esc_url( $feature['url'] ) . '" target="_blank" rel="noopener">';
							echo esc_html( $feature['label'] );
							echo '<svg width="9" height="9" viewBox="0 0 9 9" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M.75 7.417 7.417.75m0 5.128V.75H2.288" stroke="#2f2f2f" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
							echo '</a>';
						} else {
							echo '<span class="lw-pro-feature__label">' . esc_html( $feature['label'] ) . '</span>';
						}
						echo '</li>';
					}
					echo '</ul>';

					echo '<div class="lw-pro-notice-button">';
					echo '<a class="lw-open-live-demo" href="https://locationweather.io/pricing/?ref=1" target="_blank" rel="noopener">';
					echo '<span class="sp-go-pro-icon"></span>';
					echo '<span>' . esc_html__( 'Upgrade to Pro Now', 'location-weather' ) . '</span>';
					echo '</a>';
					echo '</div>';

					echo '</div>';
				}
			}
		}
	}
}
