<?php
require_once get_theme_file_path('/inc/class-tgm-plugin-activation.php');
add_action( 'tgmpa_register', 'xevso_register_required_plugins' );


function xevso_register_required_plugins() {
	/*
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins_path = get_template_directory() . '/inc/tgm/plugins';
	$plugins = array(

		// This is an example of how to include a plugin bundled with a theme.
		array(
			'name'               => esc_html__('xevso Core','xevso'), 
			'slug'               => 'xevsocore',
			'source'             => get_template_directory() . '/inc/plugins/xevsocore.zip', 
			'required'           => true,
			'version'            => '1.0.0',
		),
		array(
			'name'               => esc_html__('Codestar Framework','xevso'), 
			'slug'               => 'codestar-framework',
			'source'             => get_template_directory() . '/inc/plugins/codestar-framework.zip', 
			'required'           => true,
		),
		array(
			'name'      => esc_html__('Elementor','xevso'),
			'slug'      => 'elementor',
			'required'  => true,
		),
		array(
			'name'      => esc_html__('One Click Demo Import','xevso'),
			'slug'      => 'one-click-demo-import',
			'required'  => true,
		),
		array(
			'name'      => esc_html__('Breadcrumb Navxt','xevso'),
			'slug'      => 'breadcrumb-navxt',
			'required'  => '',
		),
		array(
			'name'      => esc_html__('Contact Form 7','xevso'),
			'slug'      => 'contact-form-7',
			'required'  => '',
		),
	);

	/*
	 * Array of configuration settings. Amend each line as needed.
	 *
	 * TGMPA will start providing localized text strings soon. If you already have translations of our standard
	 * strings available, please help us make TGMPA even better by giving us access to these translations or by
	 * sending in a pull-request with .po file(s) with the translations.
	 *
	 * Only uncomment the strings in the config array if you want to customize the strings.
	 */
	$config = array(
		'id'           => 'xevso',                 // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                      // Default absolute path to bundled plugins.
		'menu'         => 'theme-plu-settings', // Menu slug.
		'parent_slug'  => 'admin.php',            // Parent menu slug.
		'capability'   => 'xevso_plug_settings',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => false,                   // Automatically activate plugins after installation or not.
		'message'      => '',                      // Message to output right before the plugins table.
	);

	tgmpa( $plugins, $config );
}
