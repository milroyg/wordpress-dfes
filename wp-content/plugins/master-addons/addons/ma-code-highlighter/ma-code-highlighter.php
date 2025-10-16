<?php

namespace MasterAddons\Addons;

/**
 * Author Name: Liton Arefin
 * Author URL : https: //jeweltheme.com
 */

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Background;
use \Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Code_Highlighter extends Widget_Base {

	public function __construct($data = [], $args = null) {

		parent::__construct($data, $args);

		wp_register_script( 'jltma-prism', JLTMA_URL . '/assets/vendors/prism/prism.js', [], '1.29', true );
		wp_register_style( 'jltma-prism', JLTMA_URL . '/assets/vendors/prism/prism.css', [], '1.29' );

	}

	public function get_name() {
		return 'jtlma-code-highlighter';
	}

	public function get_title() {
		return __( 'Code Highlighter', 'master-addons' );
	}

	public function get_icon() {
		return 'jltma-icon eicon-editor-code';
	}

	public function get_categories() {
		return ['master-addons'];
	}

	public function get_keywords() {
		return [ 'code' ];
	}

	public function get_script_depends() {
		return [ 'jltma-prism' ];
	}

	public function get_style_depends() {
		return [ 'jltma-prism' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'general_section',
			[
				'label' => __( 'General', 'master-addons' ),
			]
		);

		// https://prismjs.com/download.html#themes=prism&languages=markup+css+clike+javascript+aspnet+bash+c+csharp+cpp+git+go+haskell+http+java+json+kotlin+less+markup-templating+objectivec+perl+php+python+r+jsx+tsx+ruby+sass+scss+sql+swift+typescript+visual-basic+xml-doc
		$languages = [
			'markup' => 'Markup',
			'html' => 'HTML',
			'css' => 'CSS',
			'sass' => 'Sass (Sass)',
			'scss' => 'Sass (Scss)',
			'less' => 'Less',
			'javascript' => 'JavaScript',
			'typescript' => 'TypeScript',
			'jsx' => 'React JSX',
			'tsx' => 'React TSX',
			'php' => 'PHP',
			'ruby' => 'Ruby',
			'json' => 'JSON + Web App Manifest',
			'http' => 'HTTP',
			'xml' => 'XML',
			'svg' => 'SVG',
			'csharp' => 'C#',
			'git' => 'Git',
			'java' => 'Java',
			'sql' => 'SQL',
			'go' => 'Go',
			'kotlin' => 'Kotlin + Kotlin Script',
			'python' => 'Python',
			'swift' => 'Swift',
			'bash' => 'Bash + Shell',
			'haskell' => 'Haskell',
			'perl' => 'Perl',
			'objectivec' => 'Objective-C',
			'visual-basic,' => 'Visual Basic + VBA',
			'r' => 'R',
			'c' => 'C',
			'cpp' => 'C++',
			'aspnet' => 'ASP.NET (C#)',
		];

		$this->add_control(
			'language',
			[
				'label' => esc_html__( 'Language', 'master-addons' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => false,
				'options' => $languages,
				'default' => 'javascript',
			]
		);

		$this->add_control(
			'code',
			[
				'label' => esc_html__( 'Code', 'master-addons' ),
				'type' => Controls_Manager::CODE,
				'default' => '<h1>I am a title!</h1>',
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'theme',
			[
				'label' => esc_html__( 'Theme', 'master-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'  => 'Default',
					'okaidia' => 'Okaidia',
					'tomorrow' => 'Tomorrow Night',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'line_numbers',
			[
				'label' => esc_html__( 'Line Numbers', 'master-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'line-numbers',
				'default' => 'line-numbers',
			]
		);

		$this->add_control(
			'line_highlight',
			[
				'label' => esc_html__( 'Line Highlight', 'master-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => '1, 4-6',
				'dynamic' => [
					'active' => true,
				],
				'ai' => [
					'active' => false,
				]
			]
		);

		$this->end_controls_section();

		\LQD_Elementor_Helper::add_style_controls(
			$this,
			'code',
			[
				'content' => [
					'controls' => [
						[
							'type' => 'typography',
							'selector' => '.lqd-code-highlighter, {{WRAPPER}} .lqd-code-highlighter code, {{WRAPPER}} .lqd-code-highlighter .line-numbers .line-numbers-row'
						],
						[
							'type' => 'margin',
							'selector' => '.lqd-code-highlighter'
						],
						[
							'type' => 'padding',
							'selector' => '.lqd-code-highlighter'
						],
						[
							'type' => 'liquid_background_css',
							'selector' => '.lqd-code-highlighter'
						],
						[
							'type' => 'border',
							'selector' => '.lqd-code-highlighter'
						],
						[
							'type' => 'border_radius',
							'selector' => '.lqd-code-highlighter'
						],
						[
							'type' => 'box_shadow',
							'selector' => '.lqd-code-highlighter'
						],
						[
							'type' => 'text_shadow',
							'selector' => '.lqd-code-highlighter code'
						],
					],
					'plural_heading' => false,
					'state_tabs' => [ 'normal', 'hover' ],
					'state_selectors_before' => [ 'hover' => '{{WRAPPER}}' ]
				],
			],
		);

	}

	protected function render() {

		$settings = $this->get_settings_for_display();
		extract( $settings );

		$this->add_render_attribute( 'div_wrapper', 'class', 'theme-' . $settings['theme'] );

		$this->add_render_attribute( 'code_wrapper', 'class', 'language-' . $settings['language'] );
		if ( $line_numbers ) $this->add_render_attribute( 'code_wrapper', 'class', $line_numbers );

		$this->add_render_attribute( 'pre_wrapper', 'class', 'lqd-code-highlighter' );
		//if ( $copy_to_clipboard ) $this->add_render_attribute( 'pre_wrapper', 'class', $copy_to_clipboard );
		if ( $line_highlight ) $this->add_render_attribute( 'pre_wrapper', 'data-line', $line_highlight );


		?>
		<div <?php $this->print_render_attribute_string( 'div_wrapper' ); ?>>
<pre <?php $this->print_render_attribute_string( 'pre_wrapper' ); ?>><code <?php $this->print_render_attribute_string( 'code_wrapper' ); ?>><?php echo esc_html( $settings['code'] ); ?></code></pre>
		</div>
		<?php

	}

}
\Elementor\Plugin::instance()->widgets_manager->register( new Code_Highlighter() );
