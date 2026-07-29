import Select from 'react-select';
import { __ } from '@wordpress/i18n';
import { memo, useEffect, useMemo, useState } from '@wordpress/element';
import { __experimentalColorGradientControl as ColorGradientControl } from '@wordpress/block-editor';
import { BgIcon, GradientIcon } from '../../components/background/svgIcons';
import {
	BgButtons,
	Border,
	BoxShadow,
	ButtonGroup,
	ColorPicker,
	RangeControl,
	SelectField,
	Spacing,
	Toggle,
	TypographyNew,
	InputControl,
	SpPopover,
	IconSetDisplay,
	FlatBorder,
	SPLWBackgroundPicker,
	WeatherIconSetDisplay,
} from '../../components';
import { borderStyles, inArray, weatherIconsSets } from '../../controls';
import { fetchFonts } from '../../components/typography/utils';

export const TemplateGlobalStyle = memo( ( { attributes, setAttributes } ) => {
	const [ allFonts, setAllFonts ] = useState( [] );
	const [ fontLists, setFontLists ] = useState( [] );
	const {
		blockName,
		enableTemplateGlobalStyle,
		templateTypography,
		templatePrimaryColor,
		templateSecondaryColor,
	} = attributes;

	useEffect( () => {
		if ( allFonts.length === 0 ) {
			fetchFonts().then( ( data ) => {
				let fonts = data?.items?.map( ( item ) => ( {
					label: item.family,
					value: item.family,
				} ) );
				setAllFonts( fonts );
				setFontLists( fonts.filter( ( font, i ) => i < 50 && font ) );
			} );
		}
	}, [] );

	const fontSearch = ( inputValue ) => {
		const filteredFonts = allFonts
			.filter( ( font ) => {
				return font.label
					.toLowerCase()
					.includes( inputValue.toLowerCase() );
			} )
			.filter( ( font, i ) => i < 30 && font );
		setFontLists( filteredFonts );
	};

	// font family.
	const defaultFamilyOption = {
		label: 'Default',
		value: 'Default',
	};
	const activeFontFamily =
		templateTypography.family === ''
			? defaultFamilyOption
			: allFonts?.find(
					( { value } ) => value === templateTypography.family
			  );

	const allFamilyList = [ defaultFamilyOption, ...fontLists ];
	const isAvailableOnList = allFamilyList?.find(
		( font ) => font.value === activeFontFamily?.value
	);
	const fontFamilySelectOptions = isAvailableOnList
		? allFamilyList
		: [ defaultFamilyOption, activeFontFamily, ...fontLists ];

	return (
		<>
			<Toggle
				label={ __(
					'Override Specific with Global',
					'location-weather'
				) }
				attributes={ enableTemplateGlobalStyle }
				attributesKey={ 'enableTemplateGlobalStyle' }
				setAttributes={ setAttributes }
			/>
			<div className="spl-weather-global-family-picker spl-weather-component-mb">
				<div className="spl-weather-component-title sp-mb-8px">
					Font Family
				</div>
				<Select
					classNamePrefix="spl-weather-font-family-select"
					options={ fontFamilySelectOptions }
					value={ activeFontFamily }
					placeholder={ activeFontFamily?.label }
					onChange={ ( { value } ) => {
						setAttributes( {
							templateTypography: {
								...templateTypography,
								family: 'Default' !== value ? value : '',
							},
						} );
					} }
					onInputChange={ ( inputValue ) => {
						fontSearch( inputValue );
					} }
				/>
			</div>
			<ColorPicker
				label={ __( 'Primary Text Color', 'location-weather' ) }
				value={ templatePrimaryColor }
				attributesKey={ 'templatePrimaryColor' }
				setAttributes={ setAttributes }
				defaultColor={
					inArray( [ 'vertical', 'horizontal' ], blockName )
						? '#fff'
						: '#2F2F2F'
				}
			/>
			<ColorPicker
				label={ __( 'Secondary Text Color', 'location-weather' ) }
				value={ templateSecondaryColor }
				attributesKey={ 'templateSecondaryColor' }
				setAttributes={ setAttributes }
				defaultColor={
					inArray( [ 'vertical', 'horizontal' ], blockName )
						? '#fff'
						: '#757575'
				}
			/>
		</>
	);
} );

// export default StyleTab;
export const WeatherLayoutStyleTab = ( { attributes, setAttributes } ) => {
	const {
		bgColorType,
		bgColor,
		bgGradient,
		splwBorderRadius,
		splwBorderWidth,
		splwBorder,
		splwBoxShadow,
		enableSplwBoxShadow,
		splwPadding,
		splwMaxWidth,
		splwMapHeight,
		blockName,
		enableTemplateGlobalStyle,
		templateTypography,
		templatePrimaryColor,
		templateSecondaryColor,
		secondaryBgColor,
	} = attributes;

	let bgButtonOption = [
		{
			label: <BgIcon />,
			value: 'bgColor',
			tooltip: 'Solid',
		},
		{
			label: <GradientIcon />,
			value: 'gradient',
			tooltip: 'Gradient',
		},
	];

	const globalStylesAttr = useMemo( () => {
		return {
			blockName,
			enableTemplateGlobalStyle,
			templateTypography,
			templatePrimaryColor,
			templateSecondaryColor,
		};
	}, [
		blockName,
		enableTemplateGlobalStyle,
		templateTypography,
		templatePrimaryColor,
		templateSecondaryColor,
	] );

	return (
		<>
			{ /* global styles */ }
			<TemplateGlobalStyle
				attributes={ globalStylesAttr }
				setAttributes={ setAttributes }
			/>
			{ /* bg styles */ }
			{ ! inArray( [ 'grid' ], blockName ) && (
				<>
					<BgButtons
						label={ __( 'Background Type', 'location-weather' ) }
						attributes={ bgColorType }
						attributesKey={ 'bgColorType' }
						setAttributes={ setAttributes }
						items={ bgButtonOption }
					/>
					{ 'bgColor' === bgColorType && (
						<ColorPicker
							label={ __(
								'Background Color',
								'location-weather'
							) }
							value={ bgColor }
							attributesKey={ 'bgColor' }
							setAttributes={ setAttributes }
						/>
					) }
					{ 'gradient' === bgColorType && (
						<div className="splw-background spl-weather-component-mb">
							<ColorGradientControl
								gradientValue={ bgGradient }
								gradients={ [] }
								onGradientChange={ ( newValue ) =>
									setAttributes( { bgGradient: newValue } )
								}
							/>
						</div>
					) }
					{ inArray( [ 'aqi-minimal' ], blockName ) && (
						<ColorPicker
							label={ __(
								'Secondary Background',
								'location-weather'
							) }
							value={ secondaryBgColor }
							attributesKey={ 'secondaryBgColor' }
							setAttributes={ setAttributes }
						/>
					) }
					<Border
						borderColorBtn={ false }
						attributes={ {
							border: splwBorder,
							borderWidth: splwBorderWidth,
						} }
						attributesKey={ {
							border: 'splwBorder',
							borderWidth: 'splwBorderWidth',
						} }
						defaultValue={ {
							unit: 'px',
							value: {
								top: '2',
								right: '2',
								bottom: '2',
								left: '2',
							},
						} }
						setAttributes={ setAttributes }
					/>

					<Spacing
						label={ __( 'Border Radius', 'location-weather' ) }
						attributes={ splwBorderRadius }
						attributesKey={ 'splwBorderRadius' }
						setAttributes={ setAttributes }
						defaultValue={ {
							unit: 'px',
							value: {
								top: '8',
								right: '8',
								bottom: '8',
								left: '8',
							},
						} }
						radiusIndicators={ true }
					/>
					{ blockName !== 'table' && (
						<>
							<BoxShadow
								shadowColorBtn={ false }
								attributes={ splwBoxShadow }
								setAttributes={ setAttributes }
								attributesKey={ 'splwBoxShadow' }
								enableAttribute={ enableSplwBoxShadow }
								enableAttributeKey={ 'enableSplwBoxShadow' }
							/>
						</>
					) }
					<Spacing
						label={ __( 'Content Padding', 'location-weather' ) }
						attributes={ splwPadding }
						attributesKey={ 'splwPadding' }
						setAttributes={ setAttributes }
						defaultValue={ {
							unit: 'px',
							value: {
								top: '20',
								right: '20',
								bottom: '20',
								left: '20',
							},
						} }
					/>
				</>
			) }

			{ /* <RangeControl
				label={ __( 'Max Width', 'location-weather' ) }
				setAttributes={ setAttributes }
				attributes={ splwMaxWidth }
				max={ 1200 }
				attributesKey={ 'splwMaxWidth' }
				defaultValue={ {
					unit: 'px',
					value: 360,
				} }
			/> */ }

			{ blockName === 'map' && (
				<RangeControl
					label={ __( 'Max Height', 'location-weather' ) }
					setAttributes={ setAttributes }
					attributes={ splwMapHeight }
					max={ 1000 }
					attributesKey={ 'splwMapHeight' }
					defaultValue={ { unit: 'px', value: 320 } }
				/>
			) }

			<div className="spl-block-pro-notice">
				{ __(
					'Set weather-based images, custom images, and video backgrounds with max width.',
					'location-weather'
				) }{ ' ' }
				<a
					href="https://locationweather.io/pricing/"
					target="_blank"
					rel="noopener noreferrer"
				>
					{ __( 'Upgrade to Pro!', 'location-weather' ) }
				</a>
			</div>
		</>
	);
};
export const BasicPreferencesStyleTab = ( { attributes, setAttributes } ) => {
	const {
		blockName,
		locationNameTypography,
		locationNameFontSize,
		locationNameFontSpacing,
		locationNameLineHeight,
		locationNameColor,
		dateTimeTypography,
		dateTimeFontSize,
		dateTimeLineHeight,
		dateTimeColor,
		dateTimeFontSpacing,
		dateTimeGap,
		regionalPreferenceMargin,
		template,
	} = attributes;

	return (
		<>
			<SpPopover label={ __( 'Location Name', 'location-weather' ) }>
				<ColorPicker
					label={ __( 'Color', 'location-weather' ) }
					value={ locationNameColor }
					attributesKey={ 'locationNameColor' }
					setAttributes={ setAttributes }
				/>
				<TypographyNew
					attributes={ {
						typography: locationNameTypography,
						typographyKey: 'locationNameTypography',
						fontSize: locationNameFontSize,
						fontSizeKey: 'locationNameFontSize',
						fontSpacing: locationNameFontSpacing,
						fontSpacingKey: 'locationNameFontSpacing',
						lineHeight: locationNameLineHeight,
						lineHeightKey: 'locationNameLineHeight',
					} }
					setAttributes={ setAttributes }
					fontSizeDefault={ {
						unit: 'px',
						value: inArray(
							[ 'vertical-one', 'tab-one', 'table-one' ],
							template
						)
							? 27
							: 14,
					} }
				/>
			</SpPopover>
			<SpPopover label={ __( 'Date & Time', 'location-weather' ) }>
				<ColorPicker
					label={ __( 'Color', 'location-weather' ) }
					value={ dateTimeColor }
					attributesKey={ 'dateTimeColor' }
					setAttributes={ setAttributes }
				/>
				<TypographyNew
					attributes={ {
						typography: dateTimeTypography,
						typographyKey: 'dateTimeTypography',
						fontSize: dateTimeFontSize,
						fontSizeKey: 'dateTimeFontSize',
						fontSpacing: dateTimeFontSpacing,
						fontSpacingKey: 'dateTimeFontSpacing',
						lineHeight: dateTimeLineHeight,
						lineHeightKey: 'dateTimeLineHeight',
					} }
					setAttributes={ setAttributes }
					fontSizeDefault={ {
						unit: 'px',
						value: blockName === 'grid' ? 13 : 14,
					} }
				/>
			</SpPopover>

			<RangeControl
				label={ __( 'Gap', 'location-weather' ) }
				setAttributes={ setAttributes }
				attributes={ dateTimeGap }
				attributesKey={ 'dateTimeGap' }
				defaultValue={ { unit: 'px', value: 8 } }
			/>
			{ blockName !== 'aqi-minimal' && (
				<Spacing
					label={ __( 'Margin', 'location-weather' ) }
					attributes={ regionalPreferenceMargin }
					attributesKey={ 'regionalPreferenceMargin' }
					setAttributes={ setAttributes }
					defaultValue={ {
						unit: 'px',
						value: {
							top: '0',
							right: '0',
							bottom: '8',
							left: '0',
						},
					} }
				/>
			) }
		</>
	);
};
export const CurrentWeatherStyleTab = ( { attributes, setAttributes } ) => {
	const {
		template,
		weatherConditionIcon,
		weatherConditionIconType,
		weatherConditionIconSize,
		temperatureScaleTypography,
		temperatureScaleFontSize,
		temperatureScaleFontSpacing,
		temperatureScaleLineHeight,
		temperatureScaleColor,
		temperatureScaleMargin,
		weatherDescTypography,
		weatherDescFontSize,
		weatherDescFontSpacing,
		weatherDescLineHeight,
		weatherDescColor,
		bgColorType,
		bgColor,
		bgGradient,
		splwBorderRadius,
		splwBorderWidth,
		splwBorder,
		splwBoxShadow,
		enableSplwBoxShadow,
		splwPadding,
		currentWeatherCardWidth,
		temperatureUnitTypography,
		temperatureUnitFontSize,
		temperatureUnitFontSpacing,
		temperatureUnitLineHeight,
		weatherConditionIconColor,
		blockName,
	} = attributes;

	const bgButtonOption = [
		{
			label: <BgIcon />,
			value: 'bgColor',
			tooltip: 'Solid',
		},
		{
			label: <GradientIcon />,
			value: 'gradient',
			tooltip: 'Gradient',
		},
	];

	return (
		<>
			{ weatherConditionIcon && (
				<SpPopover
					label={ __( 'Weather Condition Icon', 'location-weather' ) }
				>
					<SelectField
						label={ __( 'Icon Type', 'location-weather' ) }
						attributes={ weatherConditionIconType }
						attributesKey={ 'weatherConditionIconType' }
						setAttributes={ setAttributes }
						flexStyle={ true }
						items={ weatherIconsSets }
					/>
					<WeatherIconSetDisplay
						iconType="weather-condition"
						iconSetType={ weatherConditionIconType }
					/>
					<RangeControl
						label={ __( 'Icon Size', 'location-weather' ) }
						setAttributes={ setAttributes }
						attributes={ weatherConditionIconSize }
						min={ 1 }
						attributesKey={ 'weatherConditionIconSize' }
						defaultValue={ { unit: 'px', value: 60 } }
					/>
				</SpPopover>
			) }
			<SpPopover
				label={ __( 'Current Temperature', 'location-weather' ) }
			>
				<ColorPicker
					label={ __( 'Color', 'location-weather' ) }
					value={ temperatureScaleColor }
					attributesKey={ 'temperatureScaleColor' }
					setAttributes={ setAttributes }
				/>
				<Spacing
					label={ __( 'Margin', 'location-weather' ) }
					attributes={ temperatureScaleMargin }
					attributesKey={ 'temperatureScaleMargin' }
					setAttributes={ setAttributes }
					defaultValue={ {
						unit: 'px',
						value: {
							top: '0',
							right: '0',
							bottom: '8',
							left: '0',
						},
					} }
				/>
				<TypographyNew
					attributes={ {
						typography: temperatureScaleTypography,
						typographyKey: 'temperatureScaleTypography',
						fontSize: temperatureScaleFontSize,
						fontSizeKey: 'temperatureScaleFontSize',
						fontSpacing: temperatureScaleFontSpacing,
						fontSpacingKey: 'temperatureScaleFontSpacing',
						lineHeight: temperatureScaleLineHeight,
						lineHeightKey: 'temperatureScaleLineHeight',
					} }
					setAttributes={ setAttributes }
					fontSizeDefault={ {
						unit: 'px',
						value: inArray(
							[ 'vertical', 'horizontal' ],
							blockName
						)
							? 48
							: 64,
					} }
				/>
			</SpPopover>
			<SpPopover label={ __( 'Temperature Scale', 'location-weather' ) }>
				<TypographyNew
					attributes={ {
						typography: temperatureUnitTypography,
						typographyKey: 'temperatureUnitTypography',
						fontSize: temperatureUnitFontSize,
						fontSizeKey: 'temperatureUnitFontSize',
						fontSpacing: temperatureUnitFontSpacing,
						fontSpacingKey: 'temperatureUnitFontSpacing',
						lineHeight: temperatureUnitLineHeight,
						lineHeightKey: 'temperatureUnitLineHeight',
					} }
					setAttributes={ setAttributes }
					fontSizeDefault={ {
						unit: 'px',
						value: inArray(
							[ 'vertical', 'horizontal' ],
							blockName
						)
							? 16
							: 21,
					} }
				/>
			</SpPopover>
			<SpPopover
				label={ __( 'Weather Description', 'location-weather' ) }
			>
				<ColorPicker
					label={ __( 'Color', 'location-weather' ) }
					value={ weatherDescColor }
					attributesKey={ 'weatherDescColor' }
					setAttributes={ setAttributes }
				/>
				<TypographyNew
					attributes={ {
						typography: weatherDescTypography,
						typographyKey: 'weatherDescTypography',
						fontSize: weatherDescFontSize,
						fontSizeKey: 'weatherDescFontSize',
						fontSpacing: weatherDescFontSpacing,
						fontSpacingKey: 'weatherDescFontSpacing',
						lineHeight: weatherDescLineHeight,
						lineHeightKey: 'weatherDescLineHeight',
					} }
					setAttributes={ setAttributes }
					fontSizeDefault={ {
						unit: 'px',
						value: blockName === 'grid' ? 18 : 16,
					} }
				/>
			</SpPopover>
			{ template === 'grid-one' && (
				<>
					<BgButtons
						label={ __( 'Background Type', 'location-weather' ) }
						attributes={ bgColorType }
						attributesKey={ 'bgColorType' }
						setAttributes={ setAttributes }
						items={ bgButtonOption }
					/>

					{ 'bgColor' === bgColorType && (
						<ColorPicker
							label={ __(
								'Background Color',
								'location-weather'
							) }
							value={ bgColor }
							attributesKey={ 'bgColor' }
							setAttributes={ setAttributes }
						/>
					) }

					{ 'gradient' === bgColorType && (
						<div className="splw-background spl-weather-component-mb">
							<ColorGradientControl
								gradientValue={ bgGradient }
								gradients={ [] }
								onGradientChange={ ( newValue ) =>
									setAttributes( { bgGradient: newValue } )
								}
							/>
						</div>
					) }

					<Border
						borderColorBtn={ false }
						attributes={ {
							border: splwBorder,
							borderWidth: splwBorderWidth,
						} }
						attributesKey={ {
							border: 'splwBorder',
							borderWidth: 'splwBorderWidth',
						} }
						defaultValue={ {
							unit: 'px',
							value: {
								top: '2',
								right: '2',
								bottom: '2',
								left: '2',
							},
						} }
						setAttributes={ setAttributes }
					/>

					<Spacing
						label={ __( 'Border Radius', 'location-weather' ) }
						attributes={ splwBorderRadius }
						attributesKey={ 'splwBorderRadius' }
						setAttributes={ setAttributes }
						defaultValue={ {
							unit: 'px',
							value: {
								top: '4',
								right: '4',
								bottom: '4',
								left: '4',
							},
						} }
						radiusIndicators={ true }
					/>

					<BoxShadow
						shadowColorBtn={ false }
						attributes={ splwBoxShadow }
						setAttributes={ setAttributes }
						attributesKey={ 'splwBoxShadow' }
						enableAttribute={ enableSplwBoxShadow }
						enableAttributeKey={ 'enableSplwBoxShadow' }
					/>
					<Spacing
						label={ __( 'Content Padding', 'location-weather' ) }
						attributes={ splwPadding }
						attributesKey={ 'splwPadding' }
						setAttributes={ setAttributes }
						defaultValue={ {
							unit: 'px',
							value: {
								top: '15',
								right: '10',
								bottom: '0',
								left: '10',
							},
						} }
					/>
					<RangeControl
						label={ __( 'Width', 'location-weather' ) }
						setAttributes={ setAttributes }
						attributes={ currentWeatherCardWidth }
						max={ 1200 }
						attributesKey={ 'currentWeatherCardWidth' }
						defaultValue={ { unit: '%', value: 50 } }
					/>
				</>
			) }
		</>
	);
};
export const GridAdditionalDataStyle = ( { attributes, setAttributes } ) => {
	const {
		additionalDataColor,
		additionalDataBgType,
		additionalDataBgColor,
		additionalDataBgGradient,
		additionalDataBorder,
		additionalDataBorderWidth,
		additionalDataBorderRadius,
		enableAdditionalDataBoxShadow,
		additionalDataBoxShadow,
		additionalDataPadding,
	} = attributes;

	const bgButtonOption = [
		{
			label: <BgIcon />,
			value: 'bgColor',
			tooltip: 'Solid',
		},
		{
			label: <GradientIcon />,
			value: 'gradient',
			tooltip: 'Gradient',
		},
	];

	const [ colorState, setColorState ] = useState( 'color' );

	return (
		<>
			<SpPopover label={ __( 'Additional Data', 'location-weather' ) }>
				<ButtonGroup
					attributes={ colorState }
					onClick={ ( e ) => setColorState( e ) }
					border={ false }
					items={ [
						{
							label: 'Normal',
							value: 'color',
						},
						{
							label: 'Hover',
							value: 'hover',
						},
					] }
				/>
				<ColorPicker
					label={ __( 'Color', 'location-weather' ) }
					value={ additionalDataColor[ colorState ] }
					onChange={ ( newColor ) =>
						setAttributes( {
							additionalDataColor: {
								...additionalDataColor,
								[ colorState ]: newColor,
							},
						} )
					}
				/>
				<BgButtons
					label={ __( 'Background Type', 'location-weather' ) }
					attributes={ additionalDataBgType[ colorState ] }
					items={ bgButtonOption }
					onClick={ ( value ) => {
						setAttributes( {
							additionalDataBgType: {
								...additionalDataBgType,
								[ colorState ]: value,
							},
						} );
					} }
				/>
				{ 'bgColor' === additionalDataBgType[ colorState ] && (
					<ColorPicker
						label={ __( 'Background Color', 'location-weather' ) }
						value={ additionalDataBgColor[ colorState ] }
						onChange={ ( value ) => {
							setAttributes( {
								additionalDataBgColor: {
									...additionalDataBgColor,
									[ colorState ]: value,
								},
							} );
						} }
					/>
				) }

				{ 'gradient' === additionalDataBgType[ colorState ] && (
					<div className="splw-background spl-weather-component-mb">
						<ColorGradientControl
							gradientValue={
								additionalDataBgGradient[ colorState ]
							}
							gradients={ [] }
							onGradientChange={ ( newValue ) =>
								setAttributes( {
									additionalDataBgGradient: {
										...additionalDataBgGradient,
										[ colorState ]: newValue,
									},
								} )
							}
						/>
					</div>
				) }
				<FlatBorder
					attributes={ {
						border: additionalDataBorder,
						borderWidth: additionalDataBorderWidth,
					} }
					attributesKey={ {
						border: 'additionalDataBorder',
						borderWidth: 'additionalDataBorderWidth',
					} }
					defaultValue={ {
						unit: 'px',
						value: {
							top: '1',
							right: '1',
							bottom: '1',
							left: '1',
						},
					} }
					setAttributes={ setAttributes }
				/>
				<Spacing
					label={ __( 'Border Radius', 'location-weather' ) }
					attributes={ additionalDataBorderRadius }
					attributesKey={ 'additionalDataBorderRadius' }
					setAttributes={ setAttributes }
					radiusIndicators={ true }
					defaultValue={ {
						unit: 'px',
						value: {
							top: '8',
							right: '8',
							bottom: '8',
							left: '8',
						},
					} }
				/>
				<BoxShadow
					shadowColorBtn={ false }
					attributes={ additionalDataBoxShadow }
					attributesKey={ 'additionalDataBoxShadow' }
					setAttributes={ setAttributes }
					enableAttribute={ enableAdditionalDataBoxShadow }
					enableAttributeKey={ 'enableAdditionalDataBoxShadow' }
				/>
				<Spacing
					label={ __( 'Padding', 'location-weather' ) }
					attributes={ additionalDataPadding }
					attributesKey={ 'additionalDataPadding' }
					setAttributes={ setAttributes }
					defaultValue={ {
						unit: 'px',
						value: {
							top: '0',
							right: '0',
							bottom: '0',
							left: '0',
						},
					} }
				/>
			</SpPopover>
		</>
	);
};
export const AdditionalDataStyleTab = ( { attributes, setAttributes } ) => {
	const {
		blockName,
		template,
		displayComportDataPosition,
		additionalStylesBorderWidth,
		additionalStylesBorder,
		additionalCarouselAutoPlay,
		additionalNavigationIcon,
		additionalCarouselDelayTime,
		additionalCarouselSpeed,
		additionalCarouselStopOnHover,
		additionalCarouselInfiniteLoop,
		additionalCarouselColumns,
		additionalNavigationIconSize,
		additionalNavIconColors,
		enableAdditionalNavIcon,
		additionalDataIcon,
		additionalDataIconType,
		additionalDataIconSize,
		additionalDataIconColor,
		weatherComportDataTypography,
		weatherComportDataFontSize,
		weatherComportDataFontSpacing,
		weatherComportDataLineHeight,
		weatherComportDataColors,
		additionalDataHorizontalGap,
		additionalDataMargin,
		active_additional_data_layout,
		active_additional_data_layout_style,
		additionalDataLabelColor,
		additionalDataLabelLineHeight,
		additionalDataLabelFontSpacing,
		additionalDataLabelFontSize,
		additionalDataLabelTypography,
		additionalDataVerticalGap,
		additionalDataValueColor,
		additionalDataValueLineHeight,
		additionalDataValueFontSpacing,
		additionalDataValueFontSize,
		additionalDataValueTypography,
		weatherComportDataVerticalGap,
		additionalNavigationVisibility,
		additionalDataPadding,
	} = attributes;

	const [ additionalColorType, setAdditionalColorType ] = useState( 'color' );
	const isCurrentWeatherCard = inArray(
		[
			'accordion-one',
			'accordion-two',
			'accordion-three',
			'accordion-four',
			'combined-one',
			'grid-one',
		],
		template
	);

	return (
		<>
			<SpPopover
				label={ __( 'Additional Data Icon', 'location-weather' ) }
			>
				<Toggle
					label={ __( 'Additional Data Icon', 'location-weather' ) }
					attributes={ additionalDataIcon }
					setAttributes={ setAttributes }
					attributesKey={ 'additionalDataIcon' }
				/>
				<SelectField
					label={ __( 'Icon Style', 'location-weather' ) }
					attributes={ additionalDataIconType }
					attributesKey={ 'additionalDataIconType' }
					setAttributes={ setAttributes }
					flexStyle={ true }
					items={ [
						{
							label: 'Regular',
							value: 'icon_set_one',
						},
						{
							label: 'Fill [Pro]',
							value: 'icon_set_two',
							disabled: true,
						},
						{
							label: 'Light [Pro]',
							value: 'icon_set_three',
							disabled: true,
						},
						{
							label: 'Light 2 [Pro]',
							value: 'icon_set_four',
							disabled: true,
						},
					] }
				/>
				<WeatherIconSetDisplay
					iconType="additional-data"
					iconSetType={ additionalDataIconType }
				/>
				<RangeControl
					label={ __( 'Icon Size', 'location-weather' ) }
					setAttributes={ setAttributes }
					attributes={ additionalDataIconSize }
					attributesKey={ 'additionalDataIconSize' }
					defaultValue={ { unit: 'px', value: 16 } }
				/>
				<ColorPicker
					label={ __( 'Color', 'location-weather' ) }
					value={ additionalDataIconColor }
					attributesKey={ 'additionalDataIconColor' }
					setAttributes={ setAttributes }
					defaultColor="#ffffff"
				/>
			</SpPopover>
			{ ! inArray( [ 'grid-two', 'grid-three' ], template ) && (
				<>
					<SpPopover
						label={ __(
							'Additional Data (Label)',
							'location-weather'
						) }
					>
						<ColorPicker
							label={ __( 'Color', 'location-weather' ) }
							value={ additionalDataLabelColor }
							attributesKey={ 'additionalDataLabelColor' }
							setAttributes={ setAttributes }
						/>
						<TypographyNew
							attributes={ {
								typography: additionalDataLabelTypography,
								typographyKey: 'additionalDataLabelTypography',
								fontSize: additionalDataLabelFontSize,
								fontSizeKey: 'additionalDataLabelFontSize',
								fontSpacing: additionalDataLabelFontSpacing,
								fontSpacingKey:
									'additionalDataLabelFontSpacing',
								lineHeight: additionalDataLabelLineHeight,
								lineHeightKey: 'additionalDataLabelLineHeight',
							} }
							setAttributes={ setAttributes }
							fontSizeDefault={ {
								unit: 'px',
								value: 14,
							} }
						/>
					</SpPopover>
					<SpPopover
						label={ __(
							'Additional Data (Value)',
							'location-weather'
						) }
					>
						<ColorPicker
							label={ __( 'Color', 'location-weather' ) }
							value={ additionalDataValueColor }
							attributesKey={ 'additionalDataValueColor' }
							setAttributes={ setAttributes }
						/>
						<TypographyNew
							attributes={ {
								typography: additionalDataValueTypography,
								typographyKey: 'additionalDataValueTypography',
								fontSize: additionalDataValueFontSize,
								fontSizeKey: 'additionalDataValueFontSize',
								fontSpacing: additionalDataValueFontSpacing,
								fontSpacingKey:
									'additionalDataValueFontSpacing',
								lineHeight: additionalDataValueLineHeight,
								lineHeightKey: 'additionalDataValueLineHeight',
							} }
							setAttributes={ setAttributes }
							fontSizeDefault={ {
								unit: 'px',
								value: 14,
							} }
						/>
					</SpPopover>
					{ blockName === 'vertical' &&
						! displayComportDataPosition &&
						inArray(
							[ 'center', 'left', 'justified' ],
							active_additional_data_layout
						) && (
							<SpPopover
								label={ __(
									'Comport Data (Pressure, Humid...)',
									'location-weather'
								) }
							>
								<ColorPicker
									label={ __( 'Color', 'location-weather' ) }
									value={ weatherComportDataColors.Color }
									onChange={ ( newColor ) =>
										setAttributes( {
											weatherComportDataColors: {
												...weatherComportDataColors,
												Color: newColor,
											},
										} )
									}
								/>
								<RangeControl
									label={ __(
										'Vertical Gap',
										'location-weather'
									) }
									setAttributes={ setAttributes }
									attributes={ weatherComportDataVerticalGap }
									attributesKey={
										'weatherComportDataVerticalGap'
									}
									defaultValue={ {
										unit: 'px',
										value: 8,
									} }
								/>
								<TypographyNew
									attributes={ {
										typography:
											weatherComportDataTypography,
										typographyKey:
											'weatherComportDataTypography',
										fontSize: weatherComportDataFontSize,
										fontSizeKey:
											'weatherComportDataFontSize',
										fontSpacing:
											weatherComportDataFontSpacing,
										fontSpacingKey:
											'weatherComportDataFontSpacing',
										lineHeight:
											weatherComportDataLineHeight,
										lineHeightKey:
											'weatherComportDataLineHeight',
									} }
									setAttributes={ setAttributes }
									fontSizeDefault={ {
										unit: 'px',
										value: 25,
									} }
								/>
							</SpPopover>
						) }
					{ blockName === 'vertical' &&
						inArray(
							[ 'center', 'left', 'justified' ],
							active_additional_data_layout
						) &&
						inArray(
							[ 'divided', 'striped' ],
							active_additional_data_layout_style
						) && (
							<Border
								attributes={ {
									border: additionalStylesBorder,
									borderWidth: additionalStylesBorderWidth,
								} }
								attributesKey={ {
									border: 'additionalStylesBorder',
									borderWidth: 'additionalStylesBorderWidth',
								} }
								defaultValue={ {
									unit: 'px',
									value: {
										top: '2',
										right: '2',
										bottom: '2',
										left: '2',
									},
								} }
								setAttributes={ setAttributes }
							/>
						) }
				</>
			) }
			{ inArray(
				[ 'carousel-simple', 'carousel-flat' ],
				active_additional_data_layout
			) && (
				<SpPopover
					label={ __(
						'Additional Data Carousel',
						'location-weather'
					) }
				>
					<RangeControl
						label={ __( 'Columns', 'location-weather' ) }
						setAttributes={ setAttributes }
						attributes={ additionalCarouselColumns }
						attributesKey={ 'additionalCarouselColumns' }
						defaultValue={ { value: 3 } }
						min={ 1 }
						max={ 10 }
						units={ false }
					/>
					{ ! isCurrentWeatherCard && (
						<>
							<Toggle
								label={ __(
									'Carousel AutoPlay',
									'location-weather'
								) }
								attributes={ additionalCarouselAutoPlay }
								setAttributes={ setAttributes }
								attributesKey={ 'additionalCarouselAutoPlay' }
							/>
							{ additionalCarouselAutoPlay && (
								<RangeControl
									label={ __(
										'AutoPlay Delay Time',
										'location-weather'
									) }
									setAttributes={ setAttributes }
									attributes={ additionalCarouselDelayTime }
									attributesKey={
										'additionalCarouselDelayTime'
									}
									units={ [ 'ms', 's' ] }
									max={ 9000 }
									defaultValue={ { unit: 'ms', value: 3000 } }
								/>
							) }
							<RangeControl
								label={ __(
									'Carousel Speed',
									'location-weather'
								) }
								setAttributes={ setAttributes }
								attributes={ additionalCarouselSpeed }
								units={ [ 'ms' ] }
								max={ 3000 }
								attributesKey={ 'additionalCarouselSpeed' }
								defaultValue={ { unit: 'ms', value: 600 } }
							/>
							<Toggle
								label={ __(
									'Stop On Hover',
									'location-weather'
								) }
								attributes={ additionalCarouselStopOnHover }
								setAttributes={ setAttributes }
								attributesKey={
									'additionalCarouselStopOnHover'
								}
							/>
							<Toggle
								label={ __(
									'Infinite Loop',
									'location-weather'
								) }
								attributes={ additionalCarouselInfiniteLoop }
								setAttributes={ setAttributes }
								attributesKey={
									'additionalCarouselInfiniteLoop'
								}
							/>
						</>
					) }
					{ ! isCurrentWeatherCard && (
						<Toggle
							label={ __(
								'Navigation Arrow',
								'location-weather'
							) }
							attributes={ enableAdditionalNavIcon }
							setAttributes={ setAttributes }
							attributesKey={ 'enableAdditionalNavIcon' }
						/>
					) }
					{ ( isCurrentWeatherCard || enableAdditionalNavIcon ) && (
						<>
							{ ! isCurrentWeatherCard && (
								<>
									<ButtonGroup
										label={ __(
											'Navigation Visibility',
											'location-weather'
										) }
										attributes={
											additionalNavigationVisibility
										}
										attributesKey={
											'additionalNavigationVisibility'
										}
										setAttributes={ setAttributes }
										border={ false }
										items={ [
											{
												label: 'Always',
												value: 'always',
											},
											{
												label: 'On Hover',
												value: 'onHover',
											},
										] }
									/>
									<ButtonGroup
										label={ __(
											'Arrow Icon Style',
											'location-weather'
										) }
										attributes={ additionalNavigationIcon }
										setAttributes={ setAttributes }
										attributesKey="additionalNavigationIcon"
										items={ [
											{
												label: (
													<i className="splwp-icon-chevron right"></i>
												),
												value: 'chevron',
											},
											{
												label: (
													<i className="splw-icon-angle"></i>
												),
												value: 'angle',
											},
											{
												label: (
													<i className="splw-icon-arrow"></i>
												),
												value: 'arrow',
											},
										] }
									/>
								</>
							) }

							<RangeControl
								label={ __(
									'Arrow Icon Size',
									'location-weather'
								) }
								setAttributes={ setAttributes }
								attributes={ additionalNavigationIconSize }
								attributesKey={ 'additionalNavigationIconSize' }
								defaultValue={ { unit: 'px', value: 16 } }
							/>
							{ ! isCurrentWeatherCard && (
								<>
									<ButtonGroup
										attributes={ additionalColorType }
										onClick={ ( e ) =>
											setAdditionalColorType( e )
										}
										border={ false }
										items={ [
											{
												label: 'Default',
												value: 'color',
											},
											{
												label: 'Hover',
												value: 'hoverColor',
											},
										] }
									/>
									<ColorPicker
										label={ __(
											'Color',
											'location-weather'
										) }
										value={
											additionalNavIconColors[
												additionalColorType
											]
										}
										onChange={ ( newColor ) =>
											setAttributes( {
												additionalNavIconColors: {
													...additionalNavIconColors,
													[ additionalColorType ]:
														newColor,
												},
											} )
										}
									/>
								</>
							) }
						</>
					) }
				</SpPopover>
			) }
			{ inArray(
				[ 'column-two', 'grid-one' ],
				active_additional_data_layout
			) && (
				<RangeControl
					label={ __( 'Horizontal Gap', 'location-weather' ) }
					setAttributes={ setAttributes }
					attributes={ additionalDataHorizontalGap }
					attributesKey={ 'additionalDataHorizontalGap' }
					defaultValue={ { unit: 'px', value: 10 } }
				/>
			) }
			{ ! inArray( [ 'tabs', 'table' ], blockName ) &&
				! inArray(
					[ 'carousel-simple', 'carousel-flat' ],
					active_additional_data_layout
				) &&
				! inArray(
					[ 'divided', 'striped' ],
					active_additional_data_layout_style
				) && (
					<RangeControl
						label={ __( 'Vertical Gap', 'location-weather' ) }
						setAttributes={ setAttributes }
						attributes={ additionalDataVerticalGap }
						attributesKey={ 'additionalDataVerticalGap' }
						defaultValue={ {
							unit: 'px',
							value: 10,
						} }
					/>
				) }
			<Spacing
				label={ __( 'Padding', 'location-weather' ) }
				attributes={ additionalDataPadding }
				attributesKey={ 'additionalDataPadding' }
				setAttributes={ setAttributes }
				defaultValue={ {
					unit: 'px',
					value: {
						top: '2',
						right: '2',
						bottom: '2',
						left: '2',
					},
				} }
			/>
			<Spacing
				label={ __( 'Margin', 'location-weather' ) }
				attributes={ additionalDataMargin }
				attributesKey={ 'additionalDataMargin' }
				setAttributes={ setAttributes }
				defaultValue={ {
					unit: 'px',
					value: {
						top: '8',
						right: '0',
						bottom: '3',
						left: '0',
					},
				} }
			/>
		</>
	);
};
export const GridForecastStyleTab = ( { attributes, setAttributes } ) => {
	const {
		gridHourlyForecastColor,
		hourlyForecastBgType,
		hourlyForecastBgColor,
		hourlyForecastBorder,
		hourlyForecastBorderWidth,
		hourlyForecastBorderRadius,
		enableHourlyForecastBoxShadow,
		hourlyForecastBoxShadow,
		hourlyForecastPadding,
		hourlyForecastBgGradient,
	} = attributes;

	const bgButtonOption = [
		{
			label: <BgIcon />,
			value: 'bgColor',
			tooltip: 'Solid',
		},
		{
			label: <GradientIcon />,
			value: 'gradient',
			tooltip: 'Gradient',
		},
	];

	const [ colorState, setColorState ] = useState( 'color' );

	return (
		<>
			<SpPopover
				label={ __( 'Hourly Forecast Data', 'location-weather' ) }
			>
				<ColorPicker
					label={ __( 'Color', 'location-weather' ) }
					value={ gridHourlyForecastColor[ colorState ] }
					onChange={ ( newColor ) =>
						setAttributes( {
							gridHourlyForecastColor: {
								...gridHourlyForecastColor,
								[ colorState ]: newColor,
							},
						} )
					}
				/>
				<BgButtons
					label={ __( 'Background Type', 'location-weather' ) }
					attributes={ hourlyForecastBgType }
					attributesKey={ 'hourlyForecastBgType' }
					setAttributes={ setAttributes }
					items={ bgButtonOption }
				/>
				{ 'bgColor' === hourlyForecastBgType && (
					<ColorPicker
						label={ __( 'Background Color', 'location-weather' ) }
						value={ hourlyForecastBgColor }
						attributesKey={ 'hourlyForecastBgColor' }
						setAttributes={ setAttributes }
					/>
				) }

				{ 'gradient' === hourlyForecastBgType && (
					<div className="splw-background spl-weather-component-mb">
						<ColorGradientControl
							gradientValue={ hourlyForecastBgGradient }
							gradients={ [] }
							onGradientChange={ ( newValue ) =>
								setAttributes( {
									hourlyForecastBgGradient: newValue,
								} )
							}
						/>
					</div>
				) }
				<FlatBorder
					attributes={ {
						border: hourlyForecastBorder,
						borderWidth: hourlyForecastBorderWidth,
					} }
					attributesKey={ {
						border: 'hourlyForecastBorder',
						borderWidth: 'hourlyForecastBorderWidth',
					} }
					defaultValue={ {
						unit: 'px',
						value: {
							top: '1',
							right: '1',
							bottom: '1',
							left: '1',
						},
					} }
					setAttributes={ setAttributes }
				/>
				<Spacing
					label={ __( 'Border Radius', 'location-weather' ) }
					attributes={ hourlyForecastBorderRadius }
					attributesKey={ 'hourlyForecastBorderRadius' }
					setAttributes={ setAttributes }
					radiusIndicators={ true }
					defaultValue={ {
						unit: 'px',
						value: {
							top: '8',
							right: '8',
							bottom: '8',
							left: '8',
						},
					} }
				/>
				<BoxShadow
					shadowColorBtn={ false }
					attributes={ hourlyForecastBoxShadow }
					attributesKey={ 'hourlyForecastBoxShadow' }
					setAttributes={ setAttributes }
					enableAttribute={ enableHourlyForecastBoxShadow }
					enableAttributeKey={ 'enableHourlyForecastBoxShadow' }
				/>
				<Spacing
					label={ __( 'Padding', 'location-weather' ) }
					attributes={ hourlyForecastPadding }
					attributesKey={ 'hourlyForecastPadding' }
					setAttributes={ setAttributes }
					defaultValue={ {
						unit: 'px',
						value: {
							top: '20',
							right: '20',
							bottom: '20',
							left: '20',
						},
					} }
				/>
			</SpPopover>
		</>
	);
};
export const ForecastDataStyleTab = ( { attributes, setAttributes } ) => {
	const {
		template,
		forecastCarouselAutoPlay,
		forecastCarouselNavIcon,
		forecastCarouselAutoplayDelay,
		forecastCarouselSpeed,
		carouselStopOnHover,
		forecastCarouselInfiniteLoop,
		forecastCarouselColumns,
		forecastNavigationIconColors,
		forecastLabelTypography,
		forecastLabelFontSize,
		forecastLabelFontSpacing,
		forecastLabelLineHeight,
		forecastLabelColor,
		forecastLabelVerticalGap,
		forecastDataIcon,
		forecastDataIconType,
		forecastDataIconSize,
		forecastDataTypography,
		forecastDataFontSize,
		forecastDataFontSpacing,
		forecastDataLineHeight,
		forecastDataColor,
		forecastDataBgColor,
		forecastContainerMargin,
		forecastLiveFilterColors,
		forecastLiveFilterBgColor,
		forecastLiveFilterBorder,
		forecastLiveFilterBorderWidth,
		showForecastNavIcon,
		forecastDropdownTextColor,
		forecastDropdownBgColor,
		forecastContainerPadding,
		forecastTabsBottomLineWidth,
		forecastTabsFullWidthBottomLine,
		forecastTabsLabelColor,
		forecastTabsBottomLineColor,
		forecastActiveTabsBottomLineColor,
		forecastCarouselHorizontalGap,
		forecastNavigationVisibility,
		forecastNavigationIconSize,
		blockName,
		dailyForecastBorderWidth,
		dailyForecastBorderStyle,
		dailyForecastBg,
		dailyForecastColor,
		dailyForecastBorderColor,
		dailyForecastBorderRadius,
		forecastNavIconBgColor,
		forecastDetailsColor,
		forecastDetailsBg,
		forecastDetailTabsLabelColor,
		forecastDetailsTabsBottomLineColor,
		forecastContainerBorder,
		forecastContainerBorderWidth,
		forecastContainerBorderRadius,
		enableForecastContainerBoxShadow,
		forecastContainerBoxShadow,
		forecastContainerBgType,
		forecastContainerBg,
		dailyForecastBoxShadow,
		enableDailyForecastBoxShadow,
		dailyForecastActiveBoxShadow,
		enableDailyForecastActiveBoxShadow,
		forecastTabsGap,
		forecastDisplayStyle,
		forecastDataIconColor,
	} = attributes;

	const [ navigationColorType, setNavigationColorType ] = useState( 'color' );
	const [ toggleState, setToggleState ] = useState( 'color' );
	const [ liveFilterState, setLiveFilterState ] = useState( 'color' );
	const [ dailyForecastStyleType, setDailyForecastStyleType ] =
		useState( 'normal' );
	const [ forecastDetailsStyleType, setForecastDetailsStyleType ] =
		useState( 'normal' );

	const isShowSelectFilter = blockName === 'vertical';

	return (
		<>
			<>
				{ inArray( [ 'horizontal-one' ], template ) && (
					<SpPopover label={ __( 'Carousel', 'location-weather' ) }>
						<RangeControl
							label={ __( 'Columns', 'location-weather' ) }
							setAttributes={ setAttributes }
							attributes={ forecastCarouselColumns }
							attributesKey={ 'forecastCarouselColumns' }
							defaultValue={ { value: 3 } }
							min={ 1 }
							max={ 10 }
						/>
						<Toggle
							label={ __(
								'Carousel AutoPlay',
								'location-weather'
							) }
							attributes={ forecastCarouselAutoPlay }
							setAttributes={ setAttributes }
							attributesKey={ 'forecastCarouselAutoPlay' }
						/>
						{ forecastCarouselAutoPlay && (
							<RangeControl
								label={ __(
									'AutoPlay Delay Time',
									'location-weather'
								) }
								setAttributes={ setAttributes }
								attributes={ forecastCarouselAutoplayDelay }
								attributesKey={
									'forecastCarouselAutoplayDelay'
								}
								defaultValue={ { unit: 'ms', value: 3000 } }
								units={ [ 'ms', 's' ] }
								max={ 9000 }
							/>
						) }
						<RangeControl
							label={ __( 'Carousel Speed', 'location-weather' ) }
							setAttributes={ setAttributes }
							attributes={ forecastCarouselSpeed }
							attributesKey={ 'forecastCarouselSpeed' }
							units={ [ 'ms', 's' ] }
							max={ 3000 }
							defaultValue={ { unit: 'ms', value: 600 } }
						/>
						<Toggle
							label={ __( 'Stop On Hover', 'location-weather' ) }
							attributes={ carouselStopOnHover }
							setAttributes={ setAttributes }
							attributesKey={ 'carouselStopOnHover' }
						/>
						<Toggle
							label={ __( 'Infinite Loop', 'location-weather' ) }
							attributes={ forecastCarouselInfiniteLoop }
							setAttributes={ setAttributes }
							attributesKey={ 'forecastCarouselInfiniteLoop' }
						/>
						<Toggle
							label={ __(
								'Navigation Arrow',
								'location-weather'
							) }
							attributes={ showForecastNavIcon }
							setAttributes={ setAttributes }
							attributesKey={ 'showForecastNavIcon' }
						/>
						{ showForecastNavIcon && (
							<>
								<ButtonGroup
									label={ __(
										'Navigation Visibility',
										'location-weather'
									) }
									attributes={ forecastNavigationVisibility }
									setAttributes={ setAttributes }
									attributesKey={
										'forecastNavigationVisibility'
									}
									border={ false }
									items={ [
										{
											label: 'Always',
											value: 'always',
										},
										{
											label: 'On Hover',
											value: 'onHover',
										},
									] }
								/>
								<ButtonGroup
									label={ __(
										'Arrow Icon Style',
										'location-weather'
									) }
									attributes={ forecastCarouselNavIcon }
									setAttributes={ setAttributes }
									attributesKey="forecastCarouselNavIcon"
									items={ [
										{
											label: (
												<i className="splwp-icon-chevron right"></i>
											),
											value: 'chevron',
										},
										{
											label: (
												<i className="splwp-icon-angle"></i>
											),
											value: 'angle',
										},
										{
											label: (
												<i className="splwp-icon-arrow"></i>
											),
											value: 'arrow',
										},
									] }
								/>
								<RangeControl
									label={ __(
										'Arrow Icon Size',
										'location-weather'
									) }
									setAttributes={ setAttributes }
									attributes={ forecastNavigationIconSize }
									attributesKey={
										'forecastNavigationIconSize'
									}
									max={ 50 }
									defaultValue={ {
										unit: 'px',
										value: 16,
									} }
								/>
								<ButtonGroup
									attributes={ navigationColorType }
									onClick={ ( e ) =>
										setNavigationColorType( e )
									}
									border={ false }
									items={ [
										{
											label: 'Normal',
											value: 'color',
										},
										{
											label: 'Hover',
											value: 'hoverColor',
										},
									] }
								/>
								<ColorPicker
									label={ __( 'Color', 'location-weather' ) }
									value={
										forecastNavigationIconColors[
											navigationColorType
										]
									}
									onChange={ ( newColor ) =>
										setAttributes( {
											forecastNavigationIconColors: {
												...forecastNavigationIconColors,
												[ navigationColorType ]:
													newColor,
											},
										} )
									}
								/>
							</>
						) }
					</SpPopover>
				) }
				<SpPopover
					label={ __( 'Forecast Data Icon', 'location-weather' ) }
				>
					<Toggle
						label={ __( 'Forecast Data Icon', 'location-weather' ) }
						attributes={ forecastDataIcon }
						setAttributes={ setAttributes }
						attributesKey={ 'forecastDataIcon' }
					/>

					<SelectField
						label={ __( 'Icon Style', 'location-weather' ) }
						attributes={ forecastDataIconType }
						attributesKey={ 'forecastDataIconType' }
						setAttributes={ setAttributes }
						flexStyle={ true }
						items={ weatherIconsSets }
					/>
					<WeatherIconSetDisplay
						iconType="weather-condition"
						iconSetType={ forecastDataIconType }
					/>
					{ inArray(
						[
							'forecast_icon_set_three',
							'forecast_icon_set_eight',
							'forecast_icon_set_four',
						],
						forecastDataIconType
					) && (
						<ColorPicker
							label={ __( 'Icon Color', 'location-weather' ) }
							value={ forecastDataIconColor }
							attributesKey={ 'forecastDataIconColor' }
							setAttributes={ setAttributes }
						/>
					) }
					<RangeControl
						label={ __( 'Icon Size', 'location-weather' ) }
						setAttributes={ setAttributes }
						attributes={ forecastDataIconSize }
						attributesKey={ 'forecastDataIconSize' }
						defaultValue={ { unit: 'px', value: 50 } }
					/>
				</SpPopover>
				{ blockName === 'grid' && (
					<GridForecastStyleTab
						attributes={ attributes }
						setAttributes={ setAttributes }
					/>
				) }
				{ blockName === 'combined' && (
					<>
						<SpPopover
							label={ __(
								'Forecast Data (Days)',
								'location-weather'
							) }
						>
							<ButtonGroup
								attributes={ dailyForecastStyleType }
								items={ [
									{ label: 'Normal', value: 'normal' },
									{ label: 'Active', value: 'active' },
								] }
								onClick={ ( e ) =>
									setDailyForecastStyleType( e )
								}
							/>
							<ColorPicker
								label={ __( 'Color', 'location-weather' ) }
								value={
									dailyForecastColor[ dailyForecastStyleType ]
								}
								onChange={ ( newColor ) =>
									setAttributes( {
										dailyForecastColor: {
											...dailyForecastColor,
											[ dailyForecastStyleType ]:
												newColor,
										},
									} )
								}
								defaultColor="#000000"
							/>
							<ColorPicker
								label={ __(
									'Daily Forecast Background',
									'location-weather'
								) }
								value={
									dailyForecastBg[ dailyForecastStyleType ]
								}
								onChange={ ( newColor ) =>
									setAttributes( {
										dailyForecastBg: {
											...dailyForecastBg,
											[ dailyForecastStyleType ]:
												newColor,
										},
									} )
								}
								defaultColor="#ffffff"
							/>
							<div className="spl-weather-border-component">
								{ dailyForecastStyleType === 'normal' && (
									<>
										<ButtonGroup
											label={ false }
											attributes={
												dailyForecastBorderStyle
											}
											items={ borderStyles }
											onClick={ ( newStyle ) => {
												setAttributes( {
													dailyForecastBorderStyle:
														newStyle,
												} );
											} }
										/>
										<Spacing
											label={ __(
												'Border Width',
												'location-weather'
											) }
											attributes={
												dailyForecastBorderWidth
											}
											attributesKey={
												'dailyForecastBorderWidth'
											}
											setAttributes={ setAttributes }
											defaultValue={ {
												unit: 'px',
												value: {
													top: '1',
													right: '1',
													bottom: '1',
													left: '1',
												},
											} }
										/>
									</>
								) }
								<ColorPicker
									label={ __(
										'Border Color',
										'location-weather'
									) }
									value={
										dailyForecastBorderColor[
											dailyForecastStyleType
										]
									}
									onChange={ ( newColor ) =>
										setAttributes( {
											dailyForecastBorderColor: {
												...dailyForecastBorderColor,
												[ dailyForecastStyleType ]:
													newColor,
											},
										} )
									}
									defaultColor="#2f2f2f"
								/>
							</div>
							{ dailyForecastStyleType === 'normal' && (
								<>
									<Spacing
										label={ __(
											'Border Radius',
											'location-weather'
										) }
										attributes={ dailyForecastBorderRadius }
										attributesKey={
											'dailyForecastBorderRadius'
										}
										setAttributes={ setAttributes }
										defaultValue={ {
											unit: 'px',
											value: {
												top: '8',
												right: '8',
												bottom: '8',
												left: '8',
											},
										} }
										radiusIndicators={ true }
									/>
									<BoxShadow
										shadowColorBtn={ false }
										attributes={ dailyForecastBoxShadow }
										attributesKey={
											'dailyForecastBoxShadow'
										}
										setAttributes={ setAttributes }
										enableAttribute={
											enableDailyForecastBoxShadow
										}
										enableAttributeKey={
											'enableDailyForecastBoxShadow'
										}
									/>
								</>
							) }
							{ dailyForecastStyleType === 'active' && (
								<BoxShadow
									shadowColorBtn={ false }
									attributes={ dailyForecastActiveBoxShadow }
									attributesKey={
										'dailyForecastActiveBoxShadow'
									}
									setAttributes={ setAttributes }
									enableAttribute={
										enableDailyForecastActiveBoxShadow
									}
									enableAttributeKey={
										'enableDailyForecastActiveBoxShadow'
									}
								/>
							) }
						</SpPopover>
						<SpPopover
							label={ __(
								'Forecast Data Carousel',
								'location-weather'
							) }
						>
							<RangeControl
								label={ __(
									'Horizontal Gap',
									'location-weather'
								) }
								setAttributes={ setAttributes }
								attributes={ forecastCarouselHorizontalGap }
								attributesKey={
									'forecastCarouselHorizontalGap'
								}
								defaultValue={ { unit: 'px', value: 26 } }
								min={ 1 }
								max={ 100 }
							/>
							<RangeControl
								label={ __(
									'Arrow Icon Size',
									'location-weather'
								) }
								setAttributes={ setAttributes }
								attributes={ forecastNavigationIconSize }
								attributesKey={ 'forecastNavigationIconSize' }
								max={ 50 }
								defaultValue={ { unit: 'px', value: 16 } }
							/>
							<ButtonGroup
								attributes={ navigationColorType }
								onClick={ ( e ) => setNavigationColorType( e ) }
								border={ false }
								items={ [
									{
										label: 'Normal',
										value: 'color',
									},
									{
										label: 'Hover',
										value: 'hoverColor',
									},
								] }
							/>
							<ColorPicker
								label={ __( 'Icon Color', 'location-weather' ) }
								value={
									forecastNavigationIconColors[
										navigationColorType
									]
								}
								onChange={ ( newColor ) =>
									setAttributes( {
										forecastNavigationIconColors: {
											...forecastNavigationIconColors,
											[ navigationColorType ]: newColor,
										},
									} )
								}
							/>
							<ColorPicker
								label={ __(
									'Icon Background Color',
									'location-weather'
								) }
								value={
									forecastNavIconBgColor[
										navigationColorType
									]
								}
								onChange={ ( newColor ) =>
									setAttributes( {
										forecastNavIconBgColor: {
											...forecastNavIconBgColor,
											[ navigationColorType ]: newColor,
										},
									} )
								}
							/>
						</SpPopover>
						<SpPopover
							label={ __(
								'Forecast Details Data',
								'location-weather'
							) }
						>
							<ColorPicker
								label={ __( 'Color', 'location-weather' ) }
								value={ forecastDetailsColor }
								onChange={ ( newColor ) =>
									setAttributes( {
										forecastDetailsColor: newColor,
									} )
								}
							/>
							<ColorPicker
								label={ __(
									'Background Color',
									'location-weather'
								) }
								value={ forecastDetailsBg }
								onChange={ ( newColor ) =>
									setAttributes( {
										forecastDetailsBg: newColor,
									} )
								}
							/>
							<ButtonGroup
								attributes={ forecastDetailsStyleType }
								items={ [
									{ label: 'Normal', value: 'normal' },
									{ label: 'Active', value: 'active' },
								] }
								onClick={ ( e ) =>
									setForecastDetailsStyleType( e )
								}
							/>
							<ColorPicker
								label={ __(
									'Filter Tabs Label Color',
									'location-weather'
								) }
								value={
									forecastDetailTabsLabelColor[
										forecastDetailsStyleType
									]
								}
								defaultColor="#FFFFFF"
								onChange={ ( e ) =>
									setAttributes( {
										forecastDetailTabsLabelColor: {
											...forecastDetailTabsLabelColor,
											[ forecastDetailsStyleType ]: e,
										},
									} )
								}
							/>
							<ColorPicker
								label={ __(
									forecastDetailsStyleType === 'normal'
										? 'Full-Width Bottom Line Color'
										: 'Bottom Line Color',
									'location-weather'
								) }
								value={
									forecastDetailsTabsBottomLineColor[
										forecastDetailsStyleType
									]
								}
								defaultColor="#FFFFFF"
								onChange={ ( e ) =>
									setAttributes( {
										forecastDetailsTabsBottomLineColor: {
											...forecastDetailsTabsBottomLineColor,
											[ forecastDetailsStyleType ]: e,
										},
									} )
								}
							/>
						</SpPopover>
						<Border
							attributes={ {
								border: forecastContainerBorder,
								borderWidth: forecastContainerBorderWidth,
							} }
							attributesKey={ {
								border: 'forecastContainerBorder',
								borderWidth: 'forecastContainerBorderWidth',
							} }
							defaultValue={ {
								unit: 'px',
								value: {
									top: '',
									right: '',
									bottom: '',
									left: '',
								},
							} }
							setAttributes={ setAttributes }
						/>
						<Spacing
							label={ __( 'Border Radius', 'location-weather' ) }
							attributes={ forecastContainerBorderRadius }
							attributesKey={ 'forecastContainerBorderRadius' }
							setAttributes={ setAttributes }
							defaultValue={ {
								unit: 'px',
								value: {
									top: '8',
									right: '8',
									bottom: '8',
									left: '8',
								},
							} }
							radiusIndicators={ true }
						/>
						<BoxShadow
							shadowColorBtn={ false }
							attributes={ forecastContainerBoxShadow }
							setAttributes={ setAttributes }
							attributesKey={ 'accordionBoxShadow' }
							enableAttribute={ enableForecastContainerBoxShadow }
							enableAttributeKey={
								'enableForecastContainerBoxShadow'
							}
						/>
						<BgButtons
							label={ __(
								'Background Type',
								'location-weather'
							) }
							attributes={ forecastContainerBgType }
							items={ [
								{
									label: <BgIcon />,
									value: 'bgColor',
									tooltip: 'Solid',
								},
								{
									label: <GradientIcon />,
									value: 'gradient',
									tooltip: 'Gradient',
								},
							] }
							onClick={ ( value ) => {
								setAttributes( {
									forecastContainerBgType: value,
								} );
							} }
						/>
						{ 'bgColor' === forecastContainerBgType && (
							<ColorPicker
								label={ __(
									'Background Color',
									'location-weather'
								) }
								value={ forecastContainerBg[ 'bgColor' ] }
								onChange={ ( value ) => {
									setAttributes( {
										forecastContainerBg: {
											...forecastContainerBg,
											bgColor: value,
										},
									} );
								} }
							/>
						) }

						{ 'gradient' === forecastContainerBgType && (
							<div className="splw-background spl-weather-component-mb">
								<ColorGradientControl
									gradientValue={
										forecastContainerBg[ 'gradient' ]
									}
									gradients={ [] }
									onGradientChange={ ( newValue ) =>
										setAttributes( {
											forecastContainerBg: {
												...forecastContainerBg,
												gradient: newValue,
											},
										} )
									}
								/>
							</div>
						) }
					</>
				) }
				{ ! inArray( [ 'grid', 'combined' ], blockName ) && (
					<>
						<SpPopover
							label={ __(
								'Forecast Data (Days & Hours)',
								'location-weather'
							) }
						>
							<ColorPicker
								label={ __( 'Color', 'location-weather' ) }
								value={ forecastLabelColor }
								attributesKey={ 'forecastLabelColor' }
								setAttributes={ setAttributes }
							/>
							<RangeControl
								label={ __(
									'Vertical Gap',
									'location-weather'
								) }
								setAttributes={ setAttributes }
								attributes={ forecastLabelVerticalGap }
								attributesKey={ 'forecastLabelVerticalGap' }
								defaultValue={ {
									unit: 'px',
									value: 10,
								} }
							/>
							<TypographyNew
								attributes={ {
									typography: forecastLabelTypography,
									typographyKey: 'forecastLabelTypography',
									fontSize: forecastLabelFontSize,
									fontSizeKey: 'forecastLabelFontSize',
									fontSpacing: forecastLabelFontSpacing,
									fontSpacingKey: 'forecastLabelFontSpacing',
									lineHeight: forecastLabelLineHeight,
									lineHeightKey: 'forecastLabelLineHeight',
								} }
								setAttributes={ setAttributes }
								fontSizeDefault={ {
									unit: 'px',
									value: 14,
								} }
							/>
						</SpPopover>
						<SpPopover
							label={ __(
								'Forecast Data (Value)',
								'location-weather'
							) }
						>
							<ColorPicker
								label={ __( 'Color', 'location-weather' ) }
								value={ forecastDataColor }
								attributesKey={ 'forecastDataColor' }
								setAttributes={ setAttributes }
							/>
							<TypographyNew
								attributes={ {
									typography: forecastDataTypography,
									typographyKey: 'forecastDataTypography',
									fontSize: forecastDataFontSize,
									fontSizeKey: 'forecastDataFontSize',
									fontSpacing: forecastDataFontSpacing,
									fontSpacingKey: 'forecastDataFontSpacing',
									lineHeight: forecastDataLineHeight,
									lineHeightKey: 'forecastDataLineHeight',
								} }
								setAttributes={ setAttributes }
								fontSizeDefault={ {
									unit: 'px',
									value: 14,
								} }
							/>
						</SpPopover>
					</>
				) }
				{ isShowSelectFilter && (
					<SpPopover
						label={ __(
							'Forecast Data (Live Filter)',
							'location-weather'
						) }
					>
						<ButtonGroup
							attributes={ liveFilterState }
							items={ [
								{ label: 'Default', value: 'color' },
								{ label: 'Hover', value: 'hover' },
								{ label: 'Active', value: 'active' },
							] }
							onClick={ ( e ) => setLiveFilterState( e ) }
						/>
						{ liveFilterState === 'color' && (
							<>
								<ColorPicker
									label={ __( 'Color', 'location-weather' ) }
									value={
										forecastLiveFilterColors[
											liveFilterState
										]
									}
									onChange={ ( e ) =>
										setAttributes( {
											forecastLiveFilterColors: {
												...forecastLiveFilterColors,
												[ liveFilterState ]: e,
											},
										} )
									}
								/>
								<ColorPicker
									label={ __(
										'Background',
										'location-weather'
									) }
									value={
										forecastLiveFilterBgColor[
											liveFilterState
										]
									}
									onChange={ ( e ) =>
										setAttributes( {
											forecastLiveFilterBgColor: {
												...forecastLiveFilterBgColor,
												[ liveFilterState ]: e,
											},
										} )
									}
								/>
							</>
						) }
						<ColorPicker
							label={ __(
								'Dropdown Text Color',
								'location-weather'
							) }
							value={
								forecastDropdownTextColor[ liveFilterState ]
							}
							defaultColor="#2F2F2F"
							onChange={ ( e ) =>
								setAttributes( {
									forecastDropdownTextColor: {
										...forecastDropdownTextColor,
										[ liveFilterState ]: e,
									},
								} )
							}
						/>
						<ColorPicker
							label={ __(
								'Dropdown Background',
								'location-weather'
							) }
							value={ forecastDropdownBgColor[ liveFilterState ] }
							defaultColor="#FFFFFF"
							onChange={ ( e ) =>
								setAttributes( {
									forecastDropdownBgColor: {
										...forecastDropdownBgColor,
										[ liveFilterState ]: e,
									},
								} )
							}
						/>
						{ liveFilterState === 'color' && (
							<FlatBorder
								attributes={ {
									border: forecastLiveFilterBorder,
									borderWidth: forecastLiveFilterBorderWidth,
								} }
								attributesKey={ {
									border: 'forecastLiveFilterBorder',
									borderWidth:
										'forecastLiveFilterBorderWidth',
								} }
								defaultValue={ {
									unit: 'px',
									value: {
										top: '0',
										right: '0',
										bottom: '3',
										left: '0',
									},
								} }
								setAttributes={ setAttributes }
							/>
						) }
					</SpPopover>
				) }
				{ blockName === 'grid' && (
					<SpPopover
						label={ __(
							'Forecast Data (Filter by Tabs)',
							'location-weather'
						) }
					>
						<RangeControl
							label={ __(
								'Gap Between Tabs',
								'location-weather'
							) }
							setAttributes={ setAttributes }
							attributes={ forecastTabsGap }
							attributesKey={ 'forecastTabsGap' }
							defaultValue={ {
								unit: 'px',
								value: 20,
							} }
						/>
						<RangeControl
							label={ __(
								'Active Bottom Line Width',
								'location-weather'
							) }
							setAttributes={ setAttributes }
							attributes={ forecastTabsBottomLineWidth }
							min={ 1 }
							attributesKey={ 'forecastTabsBottomLineWidth' }
							defaultValue={ {
								unit: 'px',
								value: 3,
							} }
						/>
						<RangeControl
							label={ __(
								'Full-Width Bottom Line',
								'location-weather'
							) }
							min={ 1 }
							setAttributes={ setAttributes }
							attributes={ forecastTabsFullWidthBottomLine }
							attributesKey={ 'forecastTabsFullWidthBottomLine' }
							defaultValue={ {
								unit: 'px',
								value: 1,
							} }
						/>
						<ButtonGroup
							attributes={ liveFilterState }
							items={ [
								{ label: 'Normal', value: 'color' },
								{ label: 'Active', value: 'active' },
							] }
							onClick={ ( e ) => setLiveFilterState( e ) }
						/>
						<ColorPicker
							label={ __( 'Label Color', 'location-weather' ) }
							value={ forecastTabsLabelColor[ liveFilterState ] }
							defaultColor="#757575"
							onChange={ ( e ) =>
								setAttributes( {
									forecastTabsLabelColor: {
										...forecastTabsLabelColor,
										[ liveFilterState ]: e,
									},
								} )
							}
						/>
						{ liveFilterState !== 'color' && (
							<ColorPicker
								label={ __(
									'Active Bottom Line Color',
									'location-weather'
								) }
								value={ forecastActiveTabsBottomLineColor }
								defaultColor="#FFFFFF"
								onChange={ ( newColor ) =>
									setAttributes( {
										forecastActiveTabsBottomLineColor:
											newColor,
									} )
								}
							/>
						) }
						{ liveFilterState === 'color' && (
							<ColorPicker
								label={ __(
									'Full-Width Bottom Line Color',
									'location-weather'
								) }
								value={ forecastTabsBottomLineColor }
								defaultColor="#FFFFFF"
								attributesKey={ 'forecastTabsBottomLineColor' }
								setAttributes={ setAttributes }
							/>
						) }
					</SpPopover>
				) }
				{ inArray(
					[ 'vertical-one', 'vertical-two', 'vertical-three' ],
					template
				) && (
					<ColorPicker
						label={ __( 'Background Color', 'location-weather' ) }
						value={ forecastDataBgColor }
						attributesKey={ 'forecastDataBgColor' }
						setAttributes={ setAttributes }
					/>
				) }
				{ ! inArray( [ 'accordion', 'grid', 'table' ], blockName ) && (
					<Spacing
						label={ __( 'Padding', 'location-weather' ) }
						attributes={ forecastContainerPadding }
						attributesKey={ 'forecastContainerPadding' }
						setAttributes={ setAttributes }
						defaultValue={ {
							unit: 'px',
							value: {
								top: '14',
								right: '0',
								bottom: '20',
								left: '0',
							},
						} }
					/>
				) }
				{ ! inArray( [ 'grid', 'table' ], blockName ) && (
					<Spacing
						label={ __( 'Margin', 'location-weather' ) }
						attributes={ forecastContainerMargin }
						attributesKey={ 'forecastContainerMargin' }
						setAttributes={ setAttributes }
						defaultValue={ {
							unit: 'px',
							value: {
								top: '14',
								right: '0',
								bottom: '0',
								left: '0',
							},
						} }
					/>
				) }
			</>
		</>
	);
};
export const FooterStyleTab = ( { attributes, setAttributes } ) => {
	const {
		weatherAttributionTypography,
		weatherAttributionFontSize,
		weatherAttributionFontSpacing,
		weatherAttributionLineHeight,
		weatherAttributionColor,
		weatherAttributionBgColor,
		detailedWeatherAndUpdateTypography,
		detailedWeatherAndUpdateFontSize,
		detailedWeatherAndUpdateFontSpacing,
		detailedWeatherAndUpdateLineHeight,
		detailedWeatherAndUpdateColor,
		displayDateUpdateTime,
		displayWeatherAttribution,
		detailedWeatherAndUpdateMargin,
	} = attributes;
	return (
		<>
			{ displayDateUpdateTime && (
				<SpPopover label={ __( 'Weather Update', 'location-weather' ) }>
					<ColorPicker
						label={ __( 'Color', 'location-weather' ) }
						value={ detailedWeatherAndUpdateColor }
						attributesKey={ 'detailedWeatherAndUpdateColor' }
						setAttributes={ setAttributes }
					/>
					<Spacing
						label={ __( 'Margin', 'location-weather' ) }
						attributes={ detailedWeatherAndUpdateMargin }
						attributesKey={ 'detailedWeatherAndUpdateMargin' }
						setAttributes={ setAttributes }
						defaultValue={ {
							unit: 'px',
							value: {
								top: '10',
								right: '0',
								bottom: '10',
								left: '0',
							},
						} }
					/>
					<TypographyNew
						attributes={ {
							typography: detailedWeatherAndUpdateTypography,
							typographyKey: 'detailedWeatherAndUpdateTypography',
							fontSize: detailedWeatherAndUpdateFontSize,
							fontSizeKey: 'detailedWeatherAndUpdateFontSize',
							fontSpacing: detailedWeatherAndUpdateFontSpacing,
							fontSpacingKey:
								'detailedWeatherAndUpdateFontSpacing',
							lineHeight: detailedWeatherAndUpdateLineHeight,
							lineHeightKey: 'detailedWeatherAndUpdateLineHeight',
						} }
						setAttributes={ setAttributes }
						fontSizeDefault={ {
							unit: 'px',
							value: 12,
						} }
					/>
				</SpPopover>
			) }
			{ displayWeatherAttribution && (
				<SpPopover
					label={ __( 'Weather Attribution', 'location-weather' ) }
				>
					<ColorPicker
						label={ __( 'Color', 'location-weather' ) }
						value={ weatherAttributionColor }
						attributesKey={ 'weatherAttributionColor' }
						setAttributes={ setAttributes }
					/>
					<ColorPicker
						label={ __( 'Background Color', 'location-weather' ) }
						value={ weatherAttributionBgColor }
						attributesKey={ 'weatherAttributionBgColor' }
						defaultColor={ '#00000036' }
						setAttributes={ setAttributes }
					/>
					<TypographyNew
						attributes={ {
							typography: weatherAttributionTypography,
							typographyKey: 'weatherAttributionTypography',
							fontSize: weatherAttributionFontSize,
							fontSizeKey: 'weatherAttributionFontSize',
							fontSpacing: weatherAttributionFontSpacing,
							fontSpacingKey: 'weatherAttributionFontSpacing',
							lineHeight: weatherAttributionLineHeight,
							lineHeightKey: 'weatherAttributionLineHeight',
						} }
						setAttributes={ setAttributes }
						fontSizeDefault={ { unit: 'px', value: 12 } }
					/>
				</SpPopover>
			) }
		</>
	);
};
export const TabsStyleTab = ( { attributes, setAttributes } ) => {
	const {
		tabTitleColors,
		tabTitleBgColors,
		tabTopBorderColor,
		tabTopBorderWidth,
	} = attributes;
	const [ buttonTab, setButtonTab ] = useState( 'color' );

	return (
		<SpPopover label={ __( 'Tabs Title', 'location-weather' ) }>
			<ButtonGroup
				attributes={ buttonTab }
				items={ [
					{ label: 'Default', value: 'color' },
					{ label: 'Active', value: 'activeColor' },
				] }
				onClick={ ( e ) => setButtonTab( e ) }
			/>
			<ColorPicker
				label={ __( 'Color', 'location-weather' ) }
				value={ tabTitleColors[ buttonTab ] }
				onChange={ ( value ) =>
					setAttributes( {
						tabTitleColors: {
							...tabTitleColors,
							[ buttonTab ]: value,
						},
					} )
				}
			/>
			<ColorPicker
				label={ __( 'Background Color', 'location-weather' ) }
				value={ tabTitleBgColors[ buttonTab ] }
				onChange={ ( value ) =>
					setAttributes( {
						tabTitleBgColors: {
							...tabTitleBgColors,
							[ buttonTab ]: value,
						},
					} )
				}
			/>
			<ColorPicker
				label={ __( 'Top Line Color', 'location-weather' ) }
				value={ tabTopBorderColor }
				attributesKey={ 'tabTopBorderColor' }
				setAttributes={ setAttributes }
			/>
			<RangeControl
				label={ __( 'Active Tab Top Line Border', 'location-weather' ) }
				max={ 10 }
				setAttributes={ setAttributes }
				attributes={ tabTopBorderWidth }
				attributesKey={ 'tabTopBorderWidth' }
				defaultValue={ { unit: 'px', value: 4 } }
			/>
		</SpPopover>
	);
};
export const TablePreferencesStyleTab = ( { attributes, setAttributes } ) => {
	const {
		tablePreferenceBorder,
		tablePreferenceBorderWidth,
		tableHeaderColor,
		tableHeaderBgColor,
		tableEvenRowColor,
		tableOddRowColor,
	} = attributes;
	return (
		<>
			<SpPopover label={ __( 'Table Header', 'location-weather' ) }>
				<ColorPicker
					label={ __( 'Color', 'location-weather' ) }
					value={ tableHeaderColor }
					defaultColor="#000"
					attributesKey={ 'tableHeaderColor' }
					setAttributes={ setAttributes }
				/>
				<ColorPicker
					label={ __( 'Background Color', 'location-weather' ) }
					value={ tableHeaderBgColor }
					defaultColor="#e7ecf1"
					attributesKey={ 'tableHeaderBgColor' }
					setAttributes={ setAttributes }
				/>
			</SpPopover>
			<SpPopover label={ __( 'Table Body', 'location-weather' ) }>
				<ColorPicker
					label={ __( 'Even Row Color', 'location-weather' ) }
					defaultColor="#FFFFFF"
					value={ tableEvenRowColor }
					attributesKey={ 'tableEvenRowColor' }
					setAttributes={ setAttributes }
				/>
				<ColorPicker
					label={ __( 'Odd Row Color', 'location-weather' ) }
					defaultColor="#F4F4F4"
					value={ tableOddRowColor }
					attributesKey={ 'tableOddRowColor' }
					setAttributes={ setAttributes }
				/>
			</SpPopover>
			<Border
				borderColorBtn={ false }
				attributes={ {
					border: tablePreferenceBorder,
					borderWidth: tablePreferenceBorderWidth,
				} }
				attributesKey={ {
					border: 'tablePreferenceBorder',
					borderWidth: 'tablePreferenceBorderWidth',
				} }
				defaultValue={ {
					unit: 'px',
					value: {
						top: '1',
						right: '1',
						bottom: '1',
						left: '1',
					},
				} }
				setAttributes={ setAttributes }
			/>
		</>
	);
};
export const MapPreferencesStyleTab = ( { attributes, setAttributes } ) => {
	const {
		weatherMapBgColorType,
		weatherMapBgColor,
		weatherMapBgGradient,
		weatherMapBorder,
		weatherMapBorderWidth,
		weatherMapBorderRadius,
		enableWeatherMapBoxShadow,
		weatherMapBoxShadow,
		weatherMapPadding,
	} = attributes;

	return (
		<>
			<BgButtons
				label={ __( 'Background Type', 'location-weather' ) }
				attributes={ weatherMapBgColorType }
				attributesKey={ 'weatherMapBgColorType' }
				setAttributes={ setAttributes }
				items={ [
					{
						label: <BgIcon />,
						value: 'bgColor',
						tooltip: 'Solid',
					},
					{
						label: <GradientIcon />,
						value: 'gradient',
						tooltip: 'Gradient',
					},
				] }
			/>
			{ 'bgColor' === weatherMapBgColorType && (
				<ColorPicker
					label={ __( 'Background Color', 'location-weather' ) }
					value={ weatherMapBgColor }
					attributesKey={ 'weatherMapBgColor' }
					setAttributes={ setAttributes }
				/>
			) }
			{ 'gradient' === weatherMapBgColorType && (
				<div className="splw-background spl-weather-component-mb">
					<ColorGradientControl
						gradientValue={ weatherMapBgGradient }
						gradients={ [] }
						onGradientChange={ ( newValue ) =>
							setAttributes( {
								weatherMapBgGradient: newValue,
							} )
						}
					/>
				</div>
			) }
			<Border
				borderColorBtn={ false }
				attributes={ {
					border: weatherMapBorder,
					borderWidth: weatherMapBorderWidth,
				} }
				attributesKey={ {
					border: 'weatherMapBorder',
					borderWidth: 'weatherMapBorderWidth',
				} }
				defaultValue={ {
					unit: 'px',
					value: {
						top: '1',
						right: '1',
						bottom: '1',
						left: '1',
					},
				} }
				setAttributes={ setAttributes }
			/>
			<Spacing
				label={ __( 'Border Radius', 'location-weather' ) }
				attributes={ weatherMapBorderRadius }
				attributesKey={ 'weatherMapBorderRadius' }
				setAttributes={ setAttributes }
				defaultValue={ {
					unit: 'px',
					value: {
						top: '8',
						right: '8',
						bottom: '8',
						left: '8',
					},
				} }
				radiusIndicators={ true }
			/>
			<BoxShadow
				shadowColorBtn={ false }
				attributes={ weatherMapBoxShadow }
				attributesKey={ 'weatherMapBoxShadow' }
				setAttributes={ setAttributes }
				enableAttribute={ enableWeatherMapBoxShadow }
				enableAttributeKey={ 'enableWeatherMapBoxShadow' }
			/>
			<Spacing
				label={ __( 'Padding', 'location-weather' ) }
				attributes={ weatherMapPadding }
				attributesKey={ 'weatherMapPadding' }
				setAttributes={ setAttributes }
				defaultValue={ {
					unit: 'px',
					value: {
						top: '20',
						right: '20',
						bottom: '20',
						left: '20',
					},
				} }
			/>
		</>
	);
};

export const aqiSummaryStyleTab = ( { attributes, setAttributes } ) => {
	const {
		aqiSummaryTextColor,
		aqiSummaryBgColorType,
		aqiSummaryBgColor,
		aqiSummaryBgGradient,
		blockName,
		aqiSummaryPadding,
		aqiSummaryMargin,
		aqiSummaryToggleBorder,
		aqiSummaryToggleBorderWidth,
		aqiSummaryToggleRadius,
		aqiSummaryLabelColors,
		aqiSummaryLabelTypography,
		aqiSummaryLabelFontSize,
		aqiSummaryLabelFontSpacing,
		aqiSummaryLabelLineHeight,
		aqiSummaryDescColors,
		aqiSummaryDescBgColor,
		aqiSummaryDescTypography,
		aqiSummaryDescFontSize,
		aqiSummaryDescFontSpacing,
		aqiSummaryDescLineHeight,
		enableSummaryAqiDesc,
	} = attributes;

	let bgButtonOption = [
		{
			label: <BgIcon />,
			value: 'bgColor',
			tooltip: 'Solid',
		},
		{
			label: <GradientIcon />,
			value: 'gradient',
			tooltip: 'Gradient',
		},
	];

	return (
		<>
			{ inArray( [ 'aqi-detailed', 'aqi-minimal' ], blockName ) && (
				<>
					<SpPopover label={ __( 'Heading', 'location-weather' ) }>
						<ColorPicker
							label={ __( 'Color', 'location-weather' ) }
							value={ aqiSummaryLabelColors }
							attributesKey={ 'aqiSummaryLabelColors' }
							setAttributes={ setAttributes }
						/>
						<TypographyNew
							attributes={ {
								typography: aqiSummaryLabelTypography,
								typographyKey: 'aqiSummaryLabelTypography',
								fontSize: aqiSummaryLabelFontSize,
								fontSizeKey: 'aqiSummaryLabelFontSize',
								fontSpacing: aqiSummaryLabelFontSpacing,
								fontSpacingKey: 'aqiSummaryLabelFontSpacing',
								lineHeight: aqiSummaryLabelLineHeight,
								lineHeightKey: 'aqiSummaryLabelLineHeight',
							} }
							setAttributes={ setAttributes }
							fontSizeDefault={ {
								unit: 'px',
								value: 24,
							} }
						/>
					</SpPopover>
					{ 'aqi-detailed' === blockName && (
						<>
							<ColorPicker
								label={ __( 'Text Color', 'location-weather' ) }
								value={ aqiSummaryTextColor }
								attributesKey={ 'aqiSummaryTextColor' }
								setAttributes={ setAttributes }
							/>

							<BgButtons
								label={ __(
									'Background Type',
									'location-weather'
								) }
								attributes={ aqiSummaryBgColorType }
								attributesKey={ 'aqiSummaryBgColorType' }
								setAttributes={ setAttributes }
								items={ bgButtonOption }
							/>

							{ 'bgColor' === aqiSummaryBgColorType && (
								<ColorPicker
									label={ __( 'Color', 'location-weather' ) }
									value={ aqiSummaryBgColor }
									attributesKey={ 'aqiSummaryBgColor' }
									setAttributes={ setAttributes }
									defaultColor="#ffffff"
								/>
							) }
							{ 'gradient' === aqiSummaryBgColorType && (
								<div className="splw-background spl-weather-component-mb">
									<ColorGradientControl
										gradientValue={ aqiSummaryBgGradient }
										gradients={ [] }
										onGradientChange={ ( newValue ) =>
											setAttributes( {
												aqiSummaryBgGradient: newValue,
											} )
										}
									/>
								</div>
							) }

							<SpPopover
								label={ __( 'Border', 'location-weather' ) }
							>
								<FlatBorder
									attributes={ {
										border: aqiSummaryToggleBorder,
										borderWidth:
											aqiSummaryToggleBorderWidth,
									} }
									attributesKey={ {
										border: 'aqiSummaryToggleBorder',
										borderWidth:
											'aqiSummaryToggleBorderWidth',
									} }
									defaultValue={ {
										unit: 'px',
										value: {
											top: '1',
											right: '1',
											bottom: '1',
											left: '1',
										},
									} }
									setAttributes={ setAttributes }
								/>
							</SpPopover>
							<Spacing
								label={ __(
									'Border Radius',
									'location-weather'
								) }
								attributes={ aqiSummaryToggleRadius }
								attributesKey={ 'aqiSummaryToggleRadius' }
								setAttributes={ setAttributes }
								radiusIndicators={ true }
								defaultValue={ {
									unit: 'px',
									value: {
										top: '4',
										right: '4',
										bottom: '4',
										left: '4',
									},
								} }
							/>
							<Spacing
								label={ __( 'Padding', 'location-weather' ) }
								attributes={ aqiSummaryPadding }
								attributesKey={ 'aqiSummaryPadding' }
								setAttributes={ setAttributes }
								defaultValue={ {
									unit: 'px',
									value: {
										top: '24',
										right: '24',
										bottom: '24',
										left: '24',
									},
								} }
							/>
						</>
					) }
					{ enableSummaryAqiDesc && (
						<SpPopover
							label={ __( 'Description', 'location-weather' ) }
						>
							<ColorPicker
								label={ __( 'Text Color', 'location-weather' ) }
								value={ aqiSummaryTextColor }
								attributesKey={ 'aqiSummaryTextColor' }
								setAttributes={ setAttributes }
							/>
							<ColorPicker
								label={ __( 'Background', 'location-weather' ) }
								value={ aqiSummaryDescBgColor }
								attributesKey={ 'aqiSummaryDescBgColor' }
								setAttributes={ setAttributes }
							/>
							<FlatBorder
								attributes={ {
									border: aqiSummaryToggleBorder,
									borderWidth: aqiSummaryToggleBorderWidth,
								} }
								attributesKey={ {
									border: 'aqiSummaryToggleBorder',
									borderWidth: 'aqiSummaryToggleBorderWidth',
								} }
								defaultValue={ {
									unit: 'px',
									value: {
										top: '1',
										right: '1',
										bottom: '1',
										left: '1',
									},
								} }
								setAttributes={ setAttributes }
							/>
							<Spacing
								label={ __(
									'Border Radius',
									'location-weather'
								) }
								attributes={ aqiSummaryToggleRadius }
								attributesKey={ 'aqiSummaryToggleRadius' }
								setAttributes={ setAttributes }
								radiusIndicators={ true }
								defaultValue={ {
									unit: 'px',
									value: {
										top: '4',
										right: '4',
										bottom: '4',
										left: '4',
									},
								} }
							/>
							<Spacing
								label={ __( 'Padding', 'location-weather' ) }
								attributes={ aqiSummaryPadding }
								attributesKey={ 'aqiSummaryPadding' }
								setAttributes={ setAttributes }
								defaultValue={ {
									unit: 'px',
									value: {
										top: '12',
										right: '12',
										bottom: '12',
										left: '12',
									},
								} }
							/>
							<TypographyNew
								attributes={ {
									typography: aqiSummaryDescTypography,
									typographyKey: 'aqiSummaryDescTypography',
									fontSize: aqiSummaryDescFontSize,
									fontSizeKey: 'aqiSummaryDescFontSize',
									fontSpacing: aqiSummaryDescFontSpacing,
									fontSpacingKey: 'aqiSummaryDescFontSpacing',
									lineHeight: aqiSummaryDescLineHeight,
									lineHeightKey: 'aqiSummaryDescLineHeight',
								} }
								setAttributes={ setAttributes }
								fontSizeDefault={ {
									unit: 'px',
									value: 14,
								} }
							/>
						</SpPopover>
					) }
					<Spacing
						label={ __( 'Margin', 'location-weather' ) }
						attributes={ aqiSummaryMargin }
						attributesKey={ 'aqiSummaryMargin' }
						setAttributes={ setAttributes }
						defaultValue={ {
							unit: 'px',
							value: {
								top: '0',
								right: '0',
								bottom: '0',
								left: '0',
							},
						} }
					/>
				</>
			) }
		</>
	);
};
export const aqiParameterStyleTab = ( { attributes, setAttributes } ) => {
	const {
		blockName,
		template,
		pollutantConditionColor,
		pollutantConditionTypography,
		pollutantConditionFontSize,
		pollutantConditionFontSpacing,
		pollutantConditionLineHeight,
		pollutantConditionLabelColors,
		pollutantConditionLabelTypography,
		pollutantConditionLabelFontSize,
		pollutantConditionLabelFontSpacing,
		pollutantConditionLabelLineHeight,
		pollutantValueColors,
		pollutantValueTypography,
		pollutantValueFontSize,
		pollutantValueFontSpacing,
		pollutantValueLineHeight,
		pollutantBoxBgColor,
		pollutantBoxPadding,
		pollutantParametersHorizontalGap,
		pollutantParametersVerticalGap,
		pollutantAreaMargin,
		aqiPollutantDetailsBorder,
		aqiPollutantDetailsBorderWidth,
		aqiPollutantDetailsBorderRadius,
		aqiPollutantHeadingColors,
		aqiPollutantHeadingTypography,
		aqiPollutantHeadingFontSize,
		aqiPollutantHeadingFontSpacing,
		aqiPollutantHeadingLineHeight,
	} = attributes;

	let bgButtonOption = [
		{
			label: <BgIcon />,
			value: 'bgColor',
			tooltip: 'Solid',
		},
		{
			label: <GradientIcon />,
			value: 'gradient',
			tooltip: 'Gradient',
		},
	];

	return (
		<>
			{ blockName === 'aqi-detailed' && (
				<>
					<SpPopover label={ __( 'Heading', 'location-weather' ) }>
						<ColorPicker
							label={ __( 'Color', 'location-weather' ) }
							value={ aqiPollutantHeadingColors }
							attributesKey={ 'aqiPollutantHeadingColors' }
							setAttributes={ setAttributes }
						/>
						<TypographyNew
							attributes={ {
								typography: aqiPollutantHeadingTypography,
								typographyKey: 'aqiPollutantHeadingTypography',
								fontSize: aqiPollutantHeadingFontSize,
								fontSizeKey: 'aqiPollutantHeadingFontSize',
								fontSpacing: aqiPollutantHeadingFontSpacing,
								fontSpacingKey:
									'aqiPollutantHeadingFontSpacing',
								lineHeight: aqiPollutantHeadingLineHeight,
								lineHeightKey: 'aqiPollutantHeadingLineHeight',
							} }
							setAttributes={ setAttributes }
							fontSizeDefault={ {
								unit: 'px',
								value: 24,
							} }
						/>
					</SpPopover>
					<SpPopover
						label={ __(
							'Pollutant Condition',
							'location-weather'
						) }
					>
						<ColorPicker
							label={ __( 'Color', 'location-weather' ) }
							value={ pollutantConditionColor }
							attributesKey={ 'pollutantConditionColor' }
							setAttributes={ setAttributes }
						/>
						<TypographyNew
							attributes={ {
								typography: pollutantConditionTypography,
								typographyKey: 'pollutantConditionTypography',
								fontSize: pollutantConditionFontSize,
								fontSizeKey: 'pollutantConditionFontSize',
								fontSpacing: pollutantConditionFontSpacing,
								fontSpacingKey: 'pollutantConditionFontSpacing',
								lineHeight: pollutantConditionLineHeight,
								lineHeightKey: 'pollutantConditionLineHeight',
							} }
							setAttributes={ setAttributes }
							fontSizeDefault={ {
								unit: 'px',
								value: 18,
							} }
						/>
					</SpPopover>
				</>
			) }
			<SpPopover label={ __( 'Pollutant Label', 'location-weather' ) }>
				<ColorPicker
					label={ __( 'Color', 'location-weather' ) }
					value={ pollutantConditionLabelColors }
					attributesKey={ 'pollutantConditionLabelColors' }
					setAttributes={ setAttributes }
				/>
				<TypographyNew
					attributes={ {
						typography: pollutantConditionLabelTypography,
						typographyKey: 'pollutantConditionLabelTypography',
						fontSize: pollutantConditionLabelFontSize,
						fontSizeKey: 'pollutantConditionLabelFontSize',
						fontSpacing: pollutantConditionLabelFontSpacing,
						fontSpacingKey: 'pollutantConditionLabelFontSpacing',
						lineHeight: pollutantConditionLabelLineHeight,
						lineHeightKey: 'pollutantConditionLabelLineHeight',
					} }
					setAttributes={ setAttributes }
					fontSizeDefault={ {
						unit: 'px',
						value: 16,
					} }
				/>
			</SpPopover>
			<SpPopover label={ __( 'Pollutant Value', 'location-weather' ) }>
				<ColorPicker
					label={ __( 'Color', 'location-weather' ) }
					value={ pollutantValueColors }
					attributesKey={ 'pollutantValueColors' }
					setAttributes={ setAttributes }
				/>
				<TypographyNew
					attributes={ {
						typography: pollutantValueTypography,
						typographyKey: 'pollutantValueTypography',
						fontSize: pollutantValueFontSize,
						fontSizeKey: 'pollutantValueFontSize',
						fontSpacing: pollutantValueFontSpacing,
						fontSpacingKey: 'pollutantValueFontSpacing',
						lineHeight: pollutantValueLineHeight,
						lineHeightKey: 'pollutantValueLineHeight',
					} }
					setAttributes={ setAttributes }
					fontSizeDefault={ {
						unit: 'px',
						value: 16,
					} }
				/>
			</SpPopover>
			{ blockName === 'aqi-detailed' && (
				<>
					<ColorPicker
						label={ __( 'Background Color', 'location-weather' ) }
						value={ pollutantBoxBgColor }
						attributesKey={ 'pollutantBoxBgColor' }
						setAttributes={ setAttributes }
						defaultColor="#ffffff"
					/>
					<SpPopover label={ __( 'Border', 'location-weather' ) }>
						<FlatBorder
							attributes={ {
								border: aqiPollutantDetailsBorder,
								borderWidth: aqiPollutantDetailsBorderWidth,
							} }
							attributesKey={ {
								border: 'aqiPollutantDetailsBorder',
								borderWidth: 'aqiPollutantDetailsBorderWidth',
							} }
							defaultValue={ {
								unit: 'px',
								value: {
									top: '1',
									right: '1',
									bottom: '1',
									left: '1',
								},
							} }
							setAttributes={ setAttributes }
						/>
					</SpPopover>
					<Spacing
						label={ __( 'Border Radius', 'location-weather' ) }
						attributes={ aqiPollutantDetailsBorderRadius }
						attributesKey={ 'aqiPollutantDetailsBorderRadius' }
						setAttributes={ setAttributes }
						radiusIndicators={ true }
						defaultValue={ {
							unit: 'px',
							value: {
								top: '4',
								right: '4',
								bottom: '4',
								left: '4',
							},
						} }
					/>
				</>
			) }
			{ blockName === 'aqi-minimal' && (
				<>
					<SpPopover
						label={ __( 'Pollutant Box', 'location-weather' ) }
					>
						<ColorPicker
							label={ __(
								'Background Color',
								'location-weather'
							) }
							value={ pollutantBoxBgColor }
							attributesKey={ 'pollutantBoxBgColor' }
							setAttributes={ setAttributes }
						/>
						<FlatBorder
							attributes={ {
								border: aqiPollutantDetailsBorder,
								borderWidth: aqiPollutantDetailsBorderWidth,
							} }
							attributesKey={ {
								border: 'aqiPollutantDetailsBorder',
								borderWidth: 'aqiPollutantDetailsBorderWidth',
							} }
							defaultValue={ {
								unit: 'px',
								value: {
									top: '1',
									right: '1',
									bottom: '1',
									left: '1',
								},
							} }
							setAttributes={ setAttributes }
						/>
						<Spacing
							label={ __( 'Border Radius', 'location-weather' ) }
							attributes={ aqiPollutantDetailsBorderRadius }
							attributesKey={ 'aqiPollutantDetailsBorderRadius' }
							setAttributes={ setAttributes }
							radiusIndicators={ true }
							defaultValue={ {
								unit: 'px',
								value: {
									top: '4',
									right: '4',
									bottom: '4',
									left: '4',
								},
							} }
						/>
						<Spacing
							label={ __( 'Padding', 'location-weather' ) }
							attributes={ pollutantBoxPadding }
							attributesKey={ 'pollutantBoxPadding' }
							setAttributes={ setAttributes }
							defaultValue={ {
								unit: 'px',
								value: {
									top: '12',
									right: '12',
									bottom: '12',
									left: '12',
								},
							} }
						/>
					</SpPopover>
				</>
			) }

			<RangeControl
				label={ __( 'Horizontal Gap', 'location-weather' ) }
				setAttributes={ setAttributes }
				attributes={ pollutantParametersHorizontalGap }
				attributesKey={ 'pollutantParametersHorizontalGap' }
				defaultValue={ { unit: 'px', value: 16 } }
			/>
			<RangeControl
				label={ __( 'Vertical Gap', 'location-weather' ) }
				setAttributes={ setAttributes }
				attributes={ pollutantParametersVerticalGap }
				attributesKey={ 'pollutantParametersVerticalGap' }
				defaultValue={ { unit: 'px', value: 16 } }
			/>
			{ blockName === 'aqi-detailed' && (
				<Spacing
					label={ __( 'Padding', 'location-weather' ) }
					attributes={ pollutantBoxPadding }
					attributesKey={ 'pollutantBoxPadding' }
					setAttributes={ setAttributes }
					defaultValue={ {
						unit: 'px',
						value: {
							top: '16',
							right: '16',
							bottom: '16',
							left: '16',
						},
					} }
				/>
			) }
			<Spacing
				label={ __( 'Margin', 'location-weather' ) }
				attributes={ pollutantAreaMargin }
				attributesKey={ 'pollutantAreaMargin' }
				setAttributes={ setAttributes }
				defaultValue={ {
					unit: 'px',
					value: {
						top: '16',
						right: '0',
						bottom: '0',
						left: '0',
					},
				} }
			/>
		</>
	);
};
