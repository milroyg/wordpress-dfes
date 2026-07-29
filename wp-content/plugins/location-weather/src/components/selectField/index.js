import { __ } from '@wordpress/i18n';
import { memo } from '@wordpress/element';
import { SelectControl } from '@wordpress/components';
import { Responsive } from '../index';
import { useDeviceType } from '../../controls';
import './editor.scss';

const SelectField = ( {
	attributes,
	attributesKey,
	setAttributes,
	label,
	items,
	flexStyle = false,
	onChange = false,
	value = false,
	defaultOption = false,
} ) => {
	// Set Button value
	const setNewValue = ( newValue ) => {
		if ( onChange ) {
			onChange( newValue );
		} else {
			setAttributes( { [ attributesKey ]: newValue } );
		}
	};

	let selectItems = [];
	if ( defaultOption ) {
		selectItems = [ { label: 'Default', value: '' }, ...items ];
	} else {
		selectItems = items;
	}

	return (
		<div
			className={ `spl-weather-select-field spl-weather-component-mb ${
				flexStyle ? 'spl-weather-d-flex' : 'sp-d-block'
			}` }
		>
			<div
				className={ `spl-weather-select-field-header ${
					flexStyle ? '' : 'sp-mb-8px'
				}` }
			>
				<label className="spl-weather-component-title">
					{ ' ' }
					{ label }
				</label>
			</div>
			<SelectControl
				className="custom-select-control"
				value={ attributes }
				options={ selectItems }
				onChange={ ( newValue ) => setNewValue( newValue ) }
				__nextHasNoMarginBottom
				__next40pxDefaultSize
			/>
		</div>
	);
};

export default memo( SelectField );
