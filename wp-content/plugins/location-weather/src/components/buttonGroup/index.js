import { __ } from '@wordpress/i18n';
import {
	Popover,
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
	Tooltip,
} from '@wordpress/components';
import { useDeviceType } from '../../controls';
import Responsive from '../responsive';
import './editor.scss';
import { InfoIcon } from '../../icons';
import { useState } from '@wordpress/element';

const SPButtonGroup = ( {
	attributes,
	attributesKey,
	setAttributes,
	label,
	items,
	border = false,
	flexStyle = false,
	onClick = false,
	infoText = false,
} ) => {
	const [ isVisible, setIsVisible ] = useState( false );
	// Device type
	const deviceType = useDeviceType();

	// Update button group value
	const setButtonGroup = ( newValue ) => {
		if ( attributes?.device ) {
			setAttributes( {
				[ attributesKey ]: {
					...attributes.device,
					[ deviceType ]: newValue,
				},
			} );
		} else {
			setAttributes( { [ attributesKey ]: newValue } );
		}
	};

	// Get active value
	const activeValue = attributes?.device
		? attributes.device[ deviceType ]
		: attributes;

	// Handle change
	const handleChange = ( value ) => {
		if ( onClick ) {
			onClick( value );
		} else {
			setButtonGroup( value );
		}
	};

	const toggleVisible = () => {
		setIsVisible( ( state ) => ! state );
	};

	return (
		<div
			className={ `spl-weather-button-group spl-weather-component-mb ${
				flexStyle ? 'sp-d-flex button-style-2' : ''
			}` }
		>
			{ label && (
				<div className={ `spl-weather-component-top` }>
					<label className="spl-weather-component-title">
						{ label }
					</label>
					{ attributes?.device && <Responsive /> }
					{ infoText && (
						<span
							className="splw-info-icon sp-d-flex"
							onMouseEnter={ toggleVisible }
							onMouseLeave={ toggleVisible }
						>
							{ <InfoIcon /> }
						</span>
					) }
					{ isVisible && (
						<Popover
							variant="unstyled"
							shift={ true }
							placement="top"
							className="splw-info-popup"
						>
							<div
								className="splw-info-popup-content"
								onMouseEnter={ () => setIsVisible( true ) }
								onMouseLeave={ () => setIsVisible( false ) }
							>
								{ infoText }
							</div>
						</Popover>
					) }
				</div>
			) }

			<ToggleGroupControl
				value={ activeValue }
				onChange={ handleChange }
				className={ `spl-weather-button-group-list ${
					border ? 'has-border' : ''
				}` }
				isBlock
				__nextHasNoMarginBottom
				__next40pxDefaultSize
			>
				{ items?.map( ( item, i ) => (
					<ToggleGroupControlOption
						key={ i }
						value={ item.value }
						label={
							item?.onlyPro ? (
								<>
									<span className="spl-pro-title">
										{ item.label }
									</span>
									<span className="spl-pro-badge">(Pro)</span>
								</>
							) : (
								item.label
							)
						}
						className={ `${
							activeValue === item.value ? 'active' : ''
						} ${ item?.onlyPro ? 'spl-only-pro-button' : '' }` }
					/>
				) ) }
			</ToggleGroupControl>
		</div>
	);
};

export default SPButtonGroup;
