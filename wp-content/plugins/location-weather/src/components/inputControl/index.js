import { __ } from '@wordpress/i18n';
import { __experimentalInputControl as InputControl } from '@wordpress/components';
import './editor.scss';

const SpInputControl = ( {
	attributes,
	attributesKey,
	setAttributes,
	label = false,
	onChange = false,
	flexStyle = false,
	help = '',
	mb = true,
	type = '',
	placeholder = '',
} ) => {
	const setValue = ( value ) => {
		if ( onChange ) {
			onChange( value );
		} else {
			setAttributes( { [ attributesKey ]: value } );
		}
	};

	return (
		<div
			className={ `spl-weather-input-control-component ${
				mb ? 'spl-weather-component-mb' : ''
			} ${ flexStyle ? 'sp-d-flex sp-justify-between' : '' }` }
		>
			{ label && (
				<div
					className={ `spl-weather-input-control-label-wrapper ${
						flexStyle ? '' : 'sp-component-title-mb'
					}` }
				>
					<label className="spl-weather-component-title">
						{ label }
					</label>
				</div>
			) }
			<InputControl
				value={ attributes }
				onChange={ ( val ) => setValue( val ) }
				placeholder={ placeholder }
				help={ help }
				type={ type }
				__next40pxDefaultSize
			/>
		</div>
	);
};

export default SpInputControl;
