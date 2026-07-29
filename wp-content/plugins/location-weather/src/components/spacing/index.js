import { __ } from '@wordpress/i18n';
import { memo, useMemo } from '@wordpress/element';
import { Button } from '@wordpress/components';
import { SpacingControlActiveIcon, SpacingControlIcon } from './spacingIcons';
import { useDeviceType } from '../../controls';
import { ResetButton, Units } from '../index';
import Responsive from '../responsive';
import './editor.scss';
import classNames from 'classnames';

const Spacing = ( {
	label,
	attributes,
	attributesKey,
	setAttributes,
	units = [ 'px', '%', 'em' ],
	linkButton = true,
	labelItem = false,
	defaultValue = {
		unit: 'px',
		value: {
			top: '0',
			right: '0',
			bottom: '0',
			left: '0',
		},
	},
	radiusIndicators = false,
} ) => {
	const deviceType = useDeviceType();

	const SpacingAllChangeIcon = attributes?.allChange ? (
		<SpacingControlActiveIcon />
	) : (
		<SpacingControlIcon />
	);

	// Get values from device-specific field if available, otherwise use default value.
	const values = attributes?.device
		? attributes?.device[ deviceType ]
		: attributes?.value;

	const setSpacingData = ( newValue, typeKey ) => {
		// Check if spacing is device-specific
		const isDevice = !! attributes?.device;

		// Select the correct target object (device-based or global value)
		const target = isDevice
			? attributes.device[ deviceType ]
			: attributes.value;

		const updatedValues = attributes.allChange
			? {
					top: newValue,
					right: newValue,
					bottom: newValue,
					left: newValue,
			  }
			: { ...target, [ typeKey ]: newValue };

		// Build updated attributes object based on type (device/global)
		const updatedAttributes = isDevice
			? {
					device: {
						...attributes.device,
						[ deviceType ]: updatedValues,
					},
			  }
			: { value: updatedValues };

		// Save changes to attributes
		setAttributes( {
			[ attributesKey ]: { ...attributes, ...updatedAttributes },
		} );
	};

	const setDefaultValue = () => {
		const isDevice = !! attributes?.device;

		// Prepare default sides
		const defaultSides = {
			top: defaultValue?.value?.top,
			right: defaultValue?.value?.right,
			bottom: defaultValue?.value?.bottom,
			left: defaultValue?.value?.left,
		};

		// Prepare updated data
		const updatedData = isDevice
			? {
					device: {
						...attributes.device,
						[ deviceType ]: {
							...attributes.device[ deviceType ],
							...defaultSides,
						},
					},
					unit: {
						...attributes.unit,
						[ deviceType ]: defaultValue?.unit,
					},
			  }
			: {
					value: { ...attributes.value, ...defaultSides },
					unit: defaultValue?.unit,
			  };

		// Save updated attributes
		setAttributes( {
			[ attributesKey ]: { ...attributes, ...updatedData },
		} );
	};

	return (
		<>
			<div className="spl-weather-spacing spl-weather-component-mb">
				<div className="spl-weather-spacing-part-1 sp-mb-8px">
					<div className="spl-weather-header-control">
						<div className="spl-weather-header-control-left">
							<label className="spl-weather-component-title">
								{ label }
							</label>
							{ attributes?.device && <Responsive /> }
						</div>
						<div className="spl-weather-header-control-right">
							<ResetButton onClick={ () => setDefaultValue() } />
							<Units
								attributes={ attributes }
								setAttributes={ setAttributes }
								attributesKey={ attributesKey }
								units={ units }
							/>
						</div>
					</div>
				</div>
				<div
					className={ classNames( 'spl-weather-spacing-part-2', {
						'spl-weather-radius-indicators': radiusIndicators,
						'spl-weather-spacing-indicators':
							! labelItem && ! radiusIndicators,
					} ) }
				>
					{ Object.keys( values )?.map( ( position ) => (
						<div
							key={ position }
							className={ classNames(
								`spl-weather-spacing-${ position }`,
								{
									box: ! linkButton && position === 'left',
								}
							) }
						>
							<input
								id={ `spl-weather-spacing-${ position }` }
								onChange={ ( e ) =>
									setSpacingData( e.target.value, position )
								}
								type="number"
								value={ values[ position ] || '' }
							/>
							{ labelItem && (
								<label>{ labelItem[ position ] }</label>
							) }
						</div>
					) ) }
					{ linkButton && (
						<div className={ `spl-weather-spacing-all` }>
							<Button
								className={
									attributes?.allChange ? 'active' : ''
								}
								onClick={ ( e ) => {
									e.stopPropagation();
									setAttributes( {
										[ attributesKey ]: {
											...attributes,
											allChange: ! attributes?.allChange,
										},
									} );
								} }
							>
								{ SpacingAllChangeIcon }
							</Button>
						</div>
					) }
				</div>
			</div>
		</>
	);
};

export default memo( Spacing );
