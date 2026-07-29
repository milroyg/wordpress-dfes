import { __ } from '@wordpress/i18n';
import { CheckboxControl } from '@wordpress/components';
import './editor.scss';

const SPCheckboxControl = ( {
	label = '',
	attributes,
	attributesKey,
	setAttributes,
	marginBottom = true,
} ) => {
	const onValueChange = () => {
		setAttributes( { [ attributesKey ]: ! attributes } );
	};

	return (
		<div
			className={ `spl-weather-checkbox-control-component${
				marginBottom ? ' spl-weather-component-mb' : ''
			}` }
		>
			<div className="spl-weather-checkbox-component-wrapper sp-d-flex sp-justify-between sp-align-i-center">
				<label className="spl-weather-component-title">{ label }</label>
				<CheckboxControl
					checked={ attributes }
					onChange={ onValueChange }
					__nextHasNoMarginBottom
				/>
			</div>
		</div>
	);
};

export default SPCheckboxControl;
