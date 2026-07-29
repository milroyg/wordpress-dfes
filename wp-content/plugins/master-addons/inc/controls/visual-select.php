<?php

namespace MasterAddons\Inc\Controls;

use Elementor\Base_Data_Control;
use MasterAddons\Inc\Classes\Assets_Manager;

if (!defined('ABSPATH')) {
	exit;
};

class JLTMA_Visual_Select extends Base_Data_Control
{

	public function get_type()
	{
		return 'jltma-visual-select';
	}

	protected function get_default_settings()
	{
		return ['label_block' => true];
	}

	public function get_default_value()
	{
		return parent::get_default_value();
	}

	public function enqueue()
	{
		Assets_Manager::enqueue('visual-select');
	}

	public function content_template()
	{
		$control_uid = $this->get_control_uid();
?>
		<div class="elementor-control-field">
			<label for="<?php echo esc_attr($control_uid); ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-input-wrapper">
				<select class="visual-select-wrapper" id="<?php echo esc_attr($control_uid); ?>" data-setting="{{ data.name }}">
					<# _.each( data.options, function( option_params, option_value ) { var value=data.controlValue; if ( typeof value=='string' ) { var selected=( option_value===value ) ? 'selected' : '' ; } else if ( null !==value ) { var value=_.values( value ); var selected=( -1 !==value.indexOf( option_value ) ) ? 'selected' : '' ; } if ( option_params.css_class ) { #>
						<option {{ selected }} data-class="{{ option_params.css_class }}" value="{{ option_value }}">{{{ option_params.label }}}</option>
						<# } else if( option_params.video_src ) { #>
							<option {{ selected }} data-video-src="{{ option_params.video_src }}" value="{{ option_value }}">{{{ option_params.label }}}</option>
							<# } else if( option_params.image ) { #>
								<option {{ selected }} data-symbol="{{ option_params.image }}" value="{{ option_value }}">{{{ option_params.label }}}</option>
								<# } else { #>
									<option {{ selected }} value="{{ option_value }}">{{{ option_params.label }}}</option>
									<# } }); #>
				</select>
				<# if( data.style_items ){ #>
					<style>
						#elementor-control-default- {
								{
									{
									data._cid
								}
							}
						}

						+.jltma-visual-select .jltma-select-item {
								{
									{
										{
										data.style_items
									}
								}
							}
						}
					</style>
					<# } #>
			</div>
		</div>
<?php
	}
}
