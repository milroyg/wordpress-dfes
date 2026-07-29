import { __ } from '@wordpress/i18n';
import {
	Button,
	ColorPicker,
	ColorIndicator,
	Dropdown,
} from '@wordpress/components';
import { memo, useState } from '@wordpress/element';
import { ResetButton } from '../index';
import './editor.scss';
import { jsonStringify } from '../../controls';
import { NewColorAddIcon } from './icons';
import axios from 'axios';

const ColorPalateDisplay = ( {
	label,
	colors = [],
	onChangePanelColor,
	showNewColorAdd = false,
	handleCustomColor = false,
	palateName = 'custom',
} ) => {
	return (
		<div className="spl-weather-color-picker-palette-display-container">
			<div className="spl-weather-color-picker-palette-title-wrapper">
				<label className="spl-weather-color-picker-palette-title">
					{ label }
				</label>
			</div>
			<ul className="spl-weather-color-picker-palette">
				{ colors?.map( ( item, i ) => (
					<li
						key={ i }
						style={ {
							backgroundColor: item?.color,
						} }
						title={ item?.name }
					>
						<Button
							onClick={ () => {
								const newColor = item?.color;
								const isHexColor =
									/^#([0-9A-Fa-f]{3}){1,2}$/.test( newColor );
								if ( isHexColor ) {
									onChangePanelColor( newColor );
								} else {
									const variableNameMatch =
										newColor.match( /--[\w-]+/ );
									if ( variableNameMatch ) {
										const rootStyles = getComputedStyle(
											document.documentElement
										);
										const astColor = rootStyles
											.getPropertyValue(
												variableNameMatch[ 0 ]
											)
											.trim();
										onChangePanelColor( astColor );
									}
								}
							} }
							value={ item.color }
						/>
						{ palateName === 'custom' && i > 3 && (
							<span
								onClick={ ( e ) => {
									e.stopPropagation();
									handleCustomColor( 'remove', item?.name );
								} }
								className="spl-weather-color-remove-button"
							>
								<svg
									xmlns="http://www.w3.org/2000/svg"
									fill="none"
									viewBox="0 0 24 24"
									height={ 24 }
									width={ 24 }
								>
									<path
										fill="#1E1E1E"
										d="m12 13.06 3.712 3.713 1.06-1.061-3.712-3.713 3.713-3.712-1.061-1.06-3.713 3.712-3.712-3.712-1.06 1.06L10.939 12l-3.712 3.713 1.06 1.06L12 13.06Z"
									/>
								</svg>
							</span>
						) }
					</li>
				) ) }
				{ showNewColorAdd && (
					<li
						onClick={ ( e ) => {
							e.stopPropagation();
							handleCustomColor(
								'add',
								`color-${ colors?.length + 1 }`
							);
						} }
						className="sp-d-flex sp-align-i-center sp-justify-center sp-cursor-pointer"
						title={ __(
							'Add this color to custom colors',
							'location-weather'
						) }
					>
						<NewColorAddIcon />
					</li>
				) }
			</ul>
		</div>
	);
};

const splwColorControlPicker = ( {
	setAttributes,
	value,
	attributesKey,
	label,
	onChange,
	defaultColor = '',
	resetButton = true,
	showThemeColors = true,
} ) => {
	const [ activeColor, setActiveColor ] = useState( value );
	const [ themeColors, setThemeColors ] = useState( [] );
	const [ savedCustomColors, setSavedCustomColors ] = useState( [] );
	const [ showNewColorAdd, setShowNewColorAdd ] = useState( false );

	const customColors = [
		{ name: 'color-0', color: '#F26C0D' },
		{ name: 'color-1', color: '#FFFFFF' },
		{ name: 'color-2', color: '#2F2F2F' },
		{ name: 'color-3', color: '#757575' },
		...savedCustomColors,
	];

	// api data and colors options.
	const getApiData = async ( customColors ) => {
		const formData = new FormData();
		formData.append( 'action', 'splw_block_color_settings_ajax' );
		formData.append( 'colorSettingsData', jsonStringify( customColors ) );
		formData.append(
			'splwBlockApiNonce',
			splWeatherBlockLocalize?.blockApiNonce
		);

		const response = await axios.post(
			splWeatherBlockLocalize.ajaxUrl,
			formData
		);

		const apiData = await response?.data?.data;
		setSavedCustomColors( apiData?.custom_colors || [] );
		setThemeColors( apiData?.theme_colors || [] );
	};

	const handleCustomColor = ( event, name ) => {
		let updatedColors = [];
		if ( event === 'add' ) {
			updatedColors = [
				...savedCustomColors,
				{ name: name, color: activeColor },
			];
		} else {
			updatedColors = savedCustomColors.filter(
				( item ) => item.name !== name
			);
		}
		setSavedCustomColors( updatedColors );
		getApiData( updatedColors );
		setShowNewColorAdd( false );
	};

	// color picker panel color change function.
	const onChangePanelColor = ( newColor ) => {
		const allColors = [ ...customColors, ...themeColors ];
		const findResult = allColors?.find(
			( item ) => item.color == newColor
		);
		const isExist = findResult ? true : false;
		setShowNewColorAdd( ! isExist );

		// set new color.
		setActiveColor( newColor );
		if ( onChange ) {
			onChange( newColor );
		} else {
			setAttributes( { [ attributesKey ]: newColor } );
		}
	};

	// color reset function.
	const setDefault = () => {
		if ( onChange ) {
			onChange( defaultColor );
		} else {
			onChangePanelColor( defaultColor );
		}
	};

	return (
		<div className="spl-weather-color-picker-component spl-weather-component-mb">
			<span className="spl-weather-component-title">{ label }</span>
			<div className="spl-weather-color-picker-right-area">
				{ resetButton && defaultColor !== activeColor && (
					<ResetButton onClick={ () => setDefault() } />
				) }
				<Dropdown
					popoverProps={ { placement: 'top-end' } }
					renderToggle={ ( { isOpen, onToggle } ) => (
						<Button onClick={ onToggle } aria-expanded={ isOpen }>
							<ColorIndicator
								colorValue={ value }
								onClick={ () => getApiData() }
							/>
						</Button>
					) }
					onClose={ ( event, isInside ) => {
						if ( isInside ) {
							event.stopPropagation();
							return;
						}
					} }
					renderContent={ () => (
						<div
							onMouseDown={ ( event ) => {
								event.stopPropagation();
							} }
							className="spl-weather-color-picker-component-renderer"
						>
							<ColorPicker
								className="spl-weather-color-picker"
								color={ activeColor }
								onChange={ onChangePanelColor }
								colors={ customColors }
								enableAlpha
							/>
							{ /* custom colors  */ }
							{ customColors?.length > 0 && (
								<ColorPalateDisplay
									label={ __(
										'Custom colors',
										'location-weather'
									) }
									colors={ customColors }
									onChangePanelColor={ onChangePanelColor }
									showNewColorAdd={ showNewColorAdd }
									handleCustomColor={ handleCustomColor }
								/>
							) }
							{ /* theme colors  */ }
							{ showThemeColors && themeColors?.length > 0 && (
								<ColorPalateDisplay
									label={ __(
										'Theme colors',
										'location-weather'
									) }
									colors={ themeColors }
									onChangePanelColor={ onChangePanelColor }
									palateName="theme"
								/>
							) }
						</div>
					) }
				/>
			</div>
		</div>
	);
};

export default memo( splwColorControlPicker );
