import { __ } from '@wordpress/i18n';
import { RangeControl } from '@wordpress/components';
import { memo, useState } from '@wordpress/element';
import { useDeviceType } from '../../controls/controls';
import Responsive from '../responsive';
import ResetButton from '../resetButton';
import Units from '../units';
import './editor.scss';

const SPRangeControl = ( {
	attributes,
	attributesKey,
	setAttributes,
	label,
	units = [ 'px', '%', 'em' ],
	resetIcon = true,
	min = 0,
	max = 200,
	step = 1,
	defaultValue = { unit: 'px', value: 10 },
} ) => {
	const [ eventLoad, setEventLoad ] = useState( false );
	// Check device (desktop/tablet/mobile).
	const deviceType = useDeviceType();

	// Ranger single value and multiple device value.
	let value = 0;
	if ( attributes?.device ) {
		value = attributes?.device[ deviceType ];
	} else if ( attributes.value !== undefined ) {
		value = attributes?.value;
	} else {
		value = attributes;
	}
	const unit = attributes?.unit?.[ deviceType ] || attributes?.unit;
	// Ranger value set function.
	const setValue = ( newValue ) => {
		let newAttributes;
		if ( attributes.device ) {
			// Update object type responsive attribute.
			newAttributes = {
				...attributes,
				device: { ...attributes.device, [ deviceType ]: newValue },
			};
		} else if ( attributes.value !== undefined ) {
			// Update object type nonresponsive attribute.
			newAttributes = {
				...attributes,
				value: newValue,
			};
		} else {
			// Update single number type attribute.
			newAttributes = newValue;
		}

		setAttributes( { [ attributesKey ]: newAttributes } );
	};

	// Set default value function and reset.
	const setDefault = () => {
		// Handle multiple device types (desktop/tablet/mobile)
		let newAttributes;

		if ( attributes?.device ) {
			newAttributes = {
				...attributes,
				device: {
					...attributes.device,
					[ deviceType ]: defaultValue.value,
				},
				unit: {
					...attributes.unit,
					[ deviceType ]: defaultValue.unit,
				},
			};
		} else if ( attributes.value !== undefined ) {
			newAttributes = {
				value: defaultValue.value,
				unit: defaultValue.unit,
			};
		} else {
			newAttributes = defaultValue;
		}

		setAttributes( {
			[ attributesKey ]: newAttributes,
		} );
	};

	// Active Label.
	const activeLabel = ( e ) => {
		let input = e.target.parentNode.parentNode.parentNode;
		let inputId = input.querySelector( 'input' ).getAttribute( 'id' );
		e.target.setAttribute( 'for', inputId );
		setEventLoad( eventLoad );
	};

	const isChangedValue =
		value !== defaultValue?.value || unit !== defaultValue?.unit;

	return (
		<div className="spl-weather-range-control spl-weather-component-mb">
			<div className="spl-weather-header-control sp-mb-8px">
				<div className="spl-weather-header-control-left">
					<label
						onClick={ ( e ) => activeLabel( e ) }
						className="spl-weather-component-title"
					>
						{ label }
					</label>
					{ attributes?.device && <Responsive /> }
				</div>
				<div className="spl-weather-header-control-right">
					{ resetIcon && isChangedValue && (
						<ResetButton onClick={ () => setDefault() } />
					) }
					{ attributes?.unit && (
						<Units
							attributes={ attributes }
							setAttributes={ setAttributes }
							attributesKey={ attributesKey }
							units={ units }
						/>
					) }
				</div>
			</div>
			<RangeControl
				value={ value }
				color="#f26c0d"
				onChange={ ( newValue ) => setValue( newValue ) }
				min={ min }
				max={ attributes?.unit && unit === '%' ? 100 : max }
				step={ step }
				__nextHasNoMarginBottom
				__next40pxDefaultSize
			/>
		</div>
	);
};

export default memo( SPRangeControl );
