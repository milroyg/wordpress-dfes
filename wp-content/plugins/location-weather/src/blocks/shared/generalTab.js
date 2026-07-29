import { __ } from '@wordpress/i18n';
import { format } from '@wordpress/date';
import { useEffect, useMemo, useState } from '@wordpress/element';
import {
	SortableContext,
	verticalListSortingStrategy,
} from '@dnd-kit/sortable';
import {
	CenterIcon,
	LeftIcon,
	RightIcon,
} from '../../components/buttonGroup/svgIcon';
import {
	InputControl,
	SelectField,
	SortableItem,
	Toggle,
	ButtonGroup,
	TemplatePresetSlider,
	CheckboxControl,
	Layouts,
	RangeControl,
	ColorPicker,
} from '../../components';
import {
	inArray,
	languagesListArray,
	layoutIconItems,
	timeZonesListArray,
	weatherItemLabels,
	windyForecastModelsOptions,
	windyMapElevationsOptions,
	windyMapLayersOptions,
} from '../../controls';
import {
	AdditionalGridOneLayout,
	AdditionalGridTwoLayout,
	CarouselFlat,
	CarouselSimple,
	LayoutStyleClean,
	LayoutStyleDivided,
	LayoutStyleStriped,
	ListCenter,
	ListJustified,
	ListLeft,
	ListTwoColJustified,
	ListTwoColumn,
	ListTwoColumnOne,
} from '../../icons';

// Custom hook that updates an attribute only after the user stops typing (debounced).
const useDebouncedAttribute = ( initialValue, delay, callback ) => {
	const [ value, setValue ] = useState( initialValue );

	useEffect( () => {
		const timeout = setTimeout( () => {
			callback( value );
		}, delay );

		return () => clearTimeout( timeout );
	}, [ value ] );

	return [ value, setValue ];
};

export const TemplatePresetsGeneralTab = ( { attributes, setAttributes } ) => {
	const { blockName, template, weatherSearch } = attributes;

	return (
		<>
			<Layouts
				attributes={ template }
				displayActive={ true }
				grid={
					inArray( [ 'vertical', 'aqi-minimal' ], blockName ) ? 3 : 2
				}
				setAttributes={ setAttributes }
				attributesKey={ 'template' }
				items={ layoutIconItems[ blockName ] }
				preset={ true }
				blockName={ blockName }
			/>
			<Toggle
				label={ __( 'Custom Weather Search', 'location-weather' ) }
				attributes={ weatherSearch }
				setAttributes={ setAttributes }
				attributesKey={ 'weatherSearch' }
				onlyPro={ true }
				videoTooltip="https://ps.w.org/location-weather/assets/visuals/custom-weather-search.webm"
				demoLink="https://locationweather.io/blocks/#demoId54"
			/>
			<div className="spl-block-pro-notice">
				{ __(
					'Access to exclusive layouts, custom weather search.',
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
export const SetLocationGeneralTab = ( { attributes, setAttributes } ) => {
	const {
		blockName,
		searchWeatherBy,
		getDataByCoordinates,
		getDataByZIPCode,
		getDataByCityID,
		getDataByCityName,
		customCityName,
		locationAutoDetect,
	} = attributes;
	// Debounced city name
	const [ cityName, setCityName ] = useDebouncedAttribute(
		getDataByCityName,
		3000,
		( val ) => setAttributes( { getDataByCityName: val } )
	);

	// Debounced city ID
	const [ cityID, setCityID ] = useDebouncedAttribute(
		getDataByCityID,
		3000,
		( val ) => setAttributes( { getDataByCityID: val } )
	);

	// Debounced ZIP Code
	const [ zipCode, setZipCode ] = useDebouncedAttribute(
		getDataByZIPCode,
		3000,
		( val ) => setAttributes( { getDataByZIPCode: val } )
	);

	// Debounced Coordinates
	const [ coordinates, setCoordinates ] = useDebouncedAttribute(
		getDataByCoordinates,
		3000,
		( val ) => setAttributes( { getDataByCoordinates: val } )
	);

	const showSetLocationType = ! inArray(
		[ 'owm-map', 'windy-map', 'aqi-detailed', 'aqi-minimal' ],
		blockName
	);

	return (
		<>
			{ showSetLocationType && (
				<ButtonGroup
					label={ __( 'Set Specific Location', 'location-weather' ) }
					items={ [
						{ label: 'City', value: 'city_name' },
						{ label: 'City ID', value: 'city_id' },
						{ label: 'ZIP', value: 'zip' },
						{ label: 'Coords', value: 'latlong' },
					] }
					attributes={ searchWeatherBy }
					setAttributes={ setAttributes }
					attributesKey={ 'searchWeatherBy' }
				/>
			) }
			{ searchWeatherBy === 'city_name' && (
				<InputControl
					label={ __( 'Enter City Name', 'location-weather' ) }
					attributes={ cityName }
					onChange={ ( val ) => setCityName( val ) }
					help={ __(
						'Write your city or country name only.',
						'location-weather'
					) }
				/>
			) }
			{ searchWeatherBy === 'city_id' && (
				<InputControl
					label={ __( 'Enter City ID', 'location-weather' ) }
					help={
						<>
							{ __( 'Set your city ID.', 'location-weather' ) }
							<a
								target="_blank"
								href="https://openweathermap.org/find"
							>
								Get city ID
							</a>{ ' ' }
						</>
					}
					attributes={ cityID }
					onChange={ ( val ) => setCityID( val ) }
				/>
			) }
			{ searchWeatherBy === 'zip' && (
				<InputControl
					label={ __( 'Enter ZIP Code', 'location-weather' ) }
					help={
						<>
							{ ' ' }
							<span>
								{ __(
									'Set your zip code and country code. See',
									'location-weather'
								) }
							</span>{ ' ' }
							<a
								href="https://locationweather.io/docs/how-to-display-weather-details-with-zip-code/"
								target="_blank"
							>
								{ __( 'instructions', 'location-weather' ) }
							</a>{ ' ' }
						</>
					}
					attributes={ zipCode }
					onChange={ ( val ) => setZipCode( val ) }
				/>
			) }
			{ searchWeatherBy === 'latlong' && (
				<InputControl
					label={ __( 'Enter Geo Coordinates', 'location-weather' ) }
					help={
						<>
							{ __(
								'Set coordinates (latitude & longitude).',
								'location-weather'
							) }
							<a href="https://www.latlong.net/" target="_blank">
								Get coordinates
							</a>
						</>
					}
					attributes={ coordinates }
					onChange={ ( val ) => setCoordinates( val ) }
				/>
			) }
			<InputControl
				label={ __( 'Custom Location Name', 'location-weather' ) }
				attributes={ customCityName }
				onChange={ ( val ) => setAttributes( { customCityName: val } ) }
			/>
			<div className="location-auto-detect">
				<Toggle
					label={ __(
						"Auto Detect Visitor's Location",
						'location-weather'
					) }
					attributes={ locationAutoDetect }
					setAttributes={ setAttributes }
					attributesKey={ 'locationAutoDetect' }
					onlyPro={ true }
				/>
			</div>
		</>
	);
};
export const MeasurementUnitsGeneralTab = ( { attributes, setAttributes } ) => {
	const {
		displayTemperatureUnit,
		displayPressureUnit,
		displayPrecipitationUnit,
		displayWindSpeedUnit,
		displayVisibilityUnit,
	} = attributes;

	return (
		<>
			<SelectField
				label={ __( 'Display Temperature Unit', 'location-weather' ) }
				attributes={ displayTemperatureUnit }
				setAttributes={ setAttributes }
				attributesKey={ 'displayTemperatureUnit' }
				items={ [
					{ label: 'Celsius (°C)', value: 'metric' },
					{ label: 'Fahrenheit (°F)', value: 'imperial' },
					{
						label: 'Auto (°C or °F) [Pro]',
						value: 'auto_temp',
						disabled: true,
					},
					{
						label: 'Both (°C & °F) [Pro]',
						value: 'auto',
						disabled: true,
					},
					{
						label: 'Degree Symbol (°) [pro]',
						value: 'none',
						disabled: true,
					},
				] }
			/>
			<SelectField
				label={ __( 'Pressure Unit', 'location-weather' ) }
				attributes={ displayPressureUnit }
				setAttributes={ setAttributes }
				attributesKey={ 'displayPressureUnit' }
				items={ [
					{ label: 'Millibars (mb)', value: 'mb' },
					{ label: 'Hectopascals (hPa)', value: 'hpa' },
					{ label: 'Kilopascals (kPa)', value: 'kpa' },
					{
						label: 'Inches of Mercury (inHg)',
						value: 'inhg',
					},
					{
						label: 'Pounds per Square Inch (psi)',
						value: 'psi',
					},
					{
						label: 'Millimeter of Mercury (mmHg/torr)',
						value: 'mmhg',
					},
					{
						label: 'Kilogram per Square Centimeter (kg/cm²)',
						value: 'ksc',
					},
				] }
			/>
			<SelectField
				label={ __( 'Precipitation Unit', 'location-weather' ) }
				attributes={ displayPrecipitationUnit }
				setAttributes={ setAttributes }
				attributesKey={ 'displayPrecipitationUnit' }
				items={ [
					{ label: 'Millimeters (mm)', value: 'mm' },
					{ label: 'Inches (inch)', value: 'inch' },
				] }
			/>
			<SelectField
				label={ __( 'Wind Speed Unit', 'location-weather' ) }
				attributes={ displayWindSpeedUnit }
				setAttributes={ setAttributes }
				attributesKey={ 'displayWindSpeedUnit' }
				items={ [
					{
						label: 'Kilometer per hour (km/h)',
						value: 'kmh',
					},
					{
						label: 'Miles per hour (mph)',
						value: 'mph',
					},
					{
						label: 'Meter per second (m/s)',
						value: 'ms',
					},
					{ label: 'Knot (kn)', value: 'kts' },
				] }
			/>
			<SelectField
				label={ __( 'Visibility Unit', 'location-weather' ) }
				attributes={ displayVisibilityUnit }
				setAttributes={ setAttributes }
				attributesKey={ 'displayVisibilityUnit' }
				items={ [
					{ label: 'Kilometer', value: 'km' },
					{ label: 'Miles', value: 'mi' },
				] }
			/>
		</>
	);
};
export const BasicPreferencesGeneralTab = ( { attributes, setAttributes } ) => {
	const {
		showLocationName,
		showCurrentDate,
		showCurrentTime,
		showPreloader,
		splwDateFormat,
		splwCustomDateFormat,
		splwTimeFormat,
		splwTimeZone,
		splwLanguage,
		blockName,
		aqiStandard,
		displayDateUpdateTime,
	} = attributes;

	const timeFormatOptions = useMemo(
		() => [
			{
				label: `${ format( 'g:i a' ) } (g:i a)`,
				value: 'g:i a',
			},
			{
				label: `${ format( 'g:i A' ) } (g:i A)`,
				value: 'g:i A',
			},
			{ label: `${ format( 'H:i' ) } (H:i)`, value: 'H:i' },
		],
		[]
	);

	const dateFormatOptions = useMemo(
		() => [
			{
				label: `${ format( 'M j, Y' ) } (M j, Y)`,
				value: 'M j, Y',
			},
			{
				label: `${ format( 'F j, Y' ) } (F j, Y)`,
				value: 'F j, Y',
			},
			{
				label: `${ format( 'Y-m-d' ) } (Y-m-d)`,
				value: 'Y-m-d',
			},
			{
				label: `${ format( 'm/d/Y' ) } (m/d/Y)`,
				value: 'm/d/Y',
			},
			{
				label: `${ format( 'd/m/Y' ) } (d/m/Y)`,
				value: 'd/m/Y',
			},
			{
				label: 'Custom',
				value: 'custom',
			},
		],
		[]
	);
	return (
		<>
			<Toggle
				label={ __( 'Location Name', 'location-weather' ) }
				attributes={ showLocationName }
				setAttributes={ setAttributes }
				attributesKey={ 'showLocationName' }
			/>
			<Toggle
				label={ __( 'Current Time', 'location-weather' ) }
				attributes={ showCurrentTime }
				setAttributes={ setAttributes }
				attributesKey={ 'showCurrentTime' }
			/>
			{ showCurrentTime && (
				<SelectField
					label={ __( 'Time Format', 'location-weather' ) }
					attributes={ splwTimeFormat }
					setAttributes={ setAttributes }
					attributesKey={ 'splwTimeFormat' }
					items={ timeFormatOptions }
				/>
			) }
			<Toggle
				label={ __( 'Current Date', 'location-weather' ) }
				attributes={ showCurrentDate }
				setAttributes={ setAttributes }
				attributesKey={ 'showCurrentDate' }
			/>
			{ showCurrentDate && (
				<SelectField
					label={ __( 'Date Format', 'location-weather' ) }
					attributes={ splwDateFormat }
					setAttributes={ setAttributes }
					attributesKey={ 'splwDateFormat' }
					items={ dateFormatOptions }
				/>
			) }
			{ splwDateFormat === 'custom' && (
				<InputControl
					defaultValue={ splwCustomDateFormat }
					attributes={ splwCustomDateFormat }
					onChange={ ( val ) =>
						setAttributes( { splwCustomDateFormat: val } )
					}
				/>
			) }
			{ blockName === 'aqi-detailed' && (
				<Toggle
					label={ __( 'Data Update Time', 'location-weather' ) }
					attributes={ displayDateUpdateTime }
					setAttributes={ setAttributes }
					attributesKey={ 'displayDateUpdateTime' }
				/>
			) }
			{ ( showCurrentTime || showCurrentDate ) && (
				<SelectField
					label={ __( 'Time Zone', 'location-weather' ) }
					attributes={ splwTimeZone }
					setAttributes={ setAttributes }
					attributesKey={ 'splwTimeZone' }
					items={ timeZonesListArray }
				/>
			) }
			<SelectField
				label={ __( 'language', 'location-weather' ) }
				flexStyle={ true }
				attributes={ splwLanguage }
				setAttributes={ setAttributes }
				attributesKey={ 'splwLanguage' }
				items={ languagesListArray }
			/>
			{ /* <Toggle
				label={__('Preloader', 'location-weather')}
				attributes={showPreloader}
				setAttributes={setAttributes}
				attributesKey={'showPreloader'}
			/> */ }
		</>
	);
};
export const CurrentWeatherGeneralTab = ( { attributes, setAttributes } ) => {
	const {
		displayTemperature,
		displayLowAndHighTemp = false,
		displayRealFeel = false,
		displayWeatherConditions,
		weatherConditionIcon,
	} = attributes;

	return (
		<>
			<Toggle
				label={ __( 'Weather Condition Icon', 'location-weather' ) }
				attributes={ weatherConditionIcon }
				setAttributes={ setAttributes }
				attributesKey={ 'weatherConditionIcon' }
			/>
			<Toggle
				label={ __( 'Temperature', 'location-weather' ) }
				attributes={ displayTemperature }
				setAttributes={ setAttributes }
				attributesKey={ 'displayTemperature' }
			/>
			<Toggle
				label={ __( 'Weather Description', 'location-weather' ) }
				attributes={ displayWeatherConditions }
				setAttributes={ setAttributes }
				attributesKey={ 'displayWeatherConditions' }
			/>
			<Toggle
				label={ __( 'Low & High Temperature', 'location-weather' ) }
				attributes={ displayLowAndHighTemp }
				setAttributes={ setAttributes }
				attributesKey={ 'displayLowAndHighTemp' }
				onlyPro={ true }
			/>
			<Toggle
				label={ __( 'Real Feel', 'location-weather' ) }
				attributes={ displayRealFeel }
				setAttributes={ setAttributes }
				attributesKey={ 'displayRealFeel' }
				onlyPro={ true }
			/>
			<div className="spl-block-pro-notice">
				{ __(
					'Get More Details—Low & High Temp, Real Feel, Icon Sets.',
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
export const AdditionalDataGeneralTab = ( { attributes, setAttributes } ) => {
	const {
		blockName,
		displayAdditionalData,
		additionalDataOptions,
		displayComportDataPosition,
		active_additional_data_layout,
		active_additional_data_layout_style,
		stripedColor,
		showNationalAlerts,
	} = attributes;

	const updateAdditionalDataOption = ( id ) => {
		const updatedArray = additionalDataOptions?.map( ( option ) => {
			if ( option.id === id ) {
				return { ...option, isActive: ! option.isActive };
			}
			return option;
		} );
		setAttributes( { additionalDataOptions: updatedArray } );
	};

	return (
		<>
			<Toggle
				label={ __( 'Display Additional Data', 'location-weather' ) }
				attributes={ displayAdditionalData }
				attributesKey={ 'displayAdditionalData' }
				setAttributes={ setAttributes }
			/>
			{ blockName === 'vertical' && displayAdditionalData && (
				<>
					<TemplatePresetSlider
						label={ __(
							'Additional Data Layouts',
							'location-weather'
						) }
						attributes={ active_additional_data_layout }
						attributesKey={ 'active_additional_data_layout' }
						setAttributes={ setAttributes }
						demoLink="https://locationweather.io/blocks/#demoId16"
						items={ [
							{
								Icon: ListCenter,
								value: 'center',
							},
							{
								Icon: ListLeft,
								value: 'left',
							},
							{
								Icon: ListJustified,
								value: 'justified',
							},
							{
								Icon: ListTwoColumn,
								value: 'column-two',
							},
							{
								Icon: ListTwoColJustified,
								value: 'column-two-justified',
							},
							{
								Icon: ListTwoColumnOne,
								value: 'grid-one',
								onlyPro: true,
							},
							{
								Icon: AdditionalGridOneLayout,
								value: 'grid-two',
								onlyPro: true,
							},
							{
								Icon: AdditionalGridTwoLayout,
								value: 'grid-three',
								onlyPro: true,
							},
							{
								Icon: CarouselSimple,
								value: 'carousel-simple',
								onlyPro: true,
							},
							{
								Icon: CarouselFlat,
								value: 'carousel-flat',
								onlyPro: true,
							},
						] }
					/>
					{ inArray(
						[ 'center', 'left', 'justified' ],
						active_additional_data_layout
					) && (
						<>
							<CheckboxControl
								label={ __(
									'Comport Data in List View',
									'location-weather'
								) }
								attributes={ displayComportDataPosition }
								attributesKey={ 'displayComportDataPosition' }
								setAttributes={ setAttributes }
							/>
							<Layouts
								label={ __(
									'Layout Style',
									'location-weather'
								) }
								attributes={
									active_additional_data_layout_style
								}
								grid={ 3 }
								setAttributes={ setAttributes }
								attributesKey={
									'active_additional_data_layout_style'
								}
								items={ [
									{
										icon: LayoutStyleClean,
										label: 'Clean',
										value: 'clean',
									},
									{
										icon: LayoutStyleDivided,
										label: 'Divided',
										value: 'divided',
										onlyPro: true,
									},
									{
										icon: LayoutStyleStriped,
										label: 'Striped',
										value: 'striped',
										onlyPro: true,
									},
								] }
								blockName="vertical"
							/>
						</>
					) }
				</>
			) }
			{ displayAdditionalData && (
				<>
					<SortableContext
						items={ additionalDataOptions }
						strategy={ verticalListSortingStrategy }
					>
						{ additionalDataOptions?.map(
							( { value, isActive, id } ) => (
								<SortableItem key={ id } id={ id }>
									<Toggle
										updated={ true }
										label={ weatherItemLabels[ value ] }
										attributes={ isActive }
										onChange={ () =>
											updateAdditionalDataOption( id )
										}
										{ ...( id > 7
											? { onlyPro: true }
											: {} ) }
									/>
								</SortableItem>
							)
						) }
					</SortableContext>
					<Toggle
						label={ __( 'National Alerts', 'location-weather' ) }
						attributes={ showNationalAlerts }
						attributesKey={ 'showNationalAlerts' }
						setAttributes={ setAttributes }
						updated={ true }
						onlyPro={ true }
					/>
				</>
			) }
			<div className="spl-block-pro-notice">
				{ __(
					'Display more weather data—exclusive layouts and insights.',
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
// Helper function to generate forecast items.
const generateForecastItems = ( max, multiplier = 1, prefix = '' ) => {
	return Array.from( { length: max }, ( _, i ) => ( {
		label:
			i === 0 && prefix === 'Days'
				? 'Today'
				: `${ prefix } ${ ( i + 1 ) * multiplier }`,
		value: ( i + 1 ).toString(),
	} ) );
};
export const ForecastDataGeneralTab = ( { attributes, setAttributes } ) => {
	const {
		displayWeatherForecastData,
		hourlyForecastType,
		numberOfForecastHours,
		forecastData,
		numOfForecastThreeHours,
		weatherForecastType,
		hourlyTitle,
		blockName,
		forecastDisplayStyle = 'inline',
		template,
		weatherGraph,
	} = attributes;

	const [ forecastsHours, forecastsThreeHours ] = useMemo(
		() => [
			generateForecastItems( 8, 1, 'Hours' ),
			generateForecastItems( 8, 3, 'Hours' ),
		],
		[]
	);

	const toggleForecastContent = ( id ) => {
		const minForecast = forecastData.filter( ( f ) => f.value === true );
		if ( minForecast.length === 1 && minForecast[ 0 ].id === id ) {
			return alert( 'You have to must show one forecast value' );
		}
		const updateArray = forecastData.map( ( f ) => {
			if ( f.id === id ) {
				return { ...f, value: ! f.value };
			}
			return f;
		} );
		setAttributes( { forecastData: updateArray } );
	};

	const infoText = () => {
		return (
			<>
				<h4>Enable Weather Forecast</h4>
				<span>
					This option allows you to enable or activate weather
					forecasts, providing users with future weather predictions
					and enhancing the utility of your weather feature.
				</span>
			</>
		);
	};

	return (
		<>
			<Toggle
				label={ __( 'Forecast Data', 'location-weather' ) }
				attributes={ displayWeatherForecastData }
				setAttributes={ setAttributes }
				attributesKey={ 'displayWeatherForecastData' }
				enableInfoIcon={ true }
				InfoText={ infoText }
			/>
			{ /* { template === 'grid-two' && (
				<Toggle
					label={ __( 'Swap Forecast Display', 'location-weather' ) }
					attributes={ swapForecastDisplay }
					attributesKey={ 'swapForecastDisplay' }
					setAttributes={ setAttributes }
				/>
			) } */ }
			{ displayWeatherForecastData && (
				<>
					<SortableContext
						items={ forecastData }
						strategy={ verticalListSortingStrategy }
					>
						{ forecastData?.map( ( { name, id, value } ) => {
							const optionName =
								name === 'rainchance' ? 'Rain Chance' : name;
							return (
								<SortableItem key={ id } id={ id }>
									<Toggle
										updated={ true }
										label={ optionName }
										attributes={ value }
										onChange={ () =>
											toggleForecastContent( id )
										}
									/>
								</SortableItem>
							);
						} ) }
					</SortableContext>
				</>
			) }
			{ template === 'horizontal-one' && (
				<Toggle
					label={ __( 'Weather Graph Chart', 'location-weather' ) }
					attributes={ weatherGraph }
					setAttributes={ setAttributes }
					attributesKey={ 'weatherGraph' }
					onlyPro={ true }
				/>
			) }
			{ displayWeatherForecastData && blockName === 'vertical' && (
				<>
					<ButtonGroup
						label={ __(
							'Forecast Display Style',
							'location-weather'
						) }
						attributes={ forecastDisplayStyle }
						items={ [
							{ label: 'Inline', value: 'inline' },
							{ label: 'Popup', value: 'popup', onlyPro: true },
						] }
						onClick={ ( newValue ) => {
							setAttributes( { forecastDisplayStyle: newValue } );
						} }
					/>
				</>
			) }
			{ displayWeatherForecastData && (
				<>
					<ButtonGroup
						label={ __(
							'Weather Forecast Type',
							'location-weather'
						) }
						attributes={ weatherForecastType }
						setAttributes={ setAttributes }
						attributesKey={ 'weatherForecastType' }
						items={ [
							{ label: 'Hourly', value: 'hourly' },
							{
								label: 'Daily',
								value: 'daily',
								onlyPro: true,
							},
							{
								label: 'Both',
								value: 'both',
								onlyPro: true,
							},
						] }
					/>

					{ inArray( [ 'hourly', 'both' ], weatherForecastType ) && (
						<>
							<ButtonGroup
								label={ __(
									'Hourly Forecast Type',
									'location-weather'
								) }
								attributes={ hourlyForecastType }
								setAttributes={ setAttributes }
								attributesKey={ 'hourlyForecastType' }
								items={ [
									{ label: '1-Hour', value: '1' },
									{
										label: '3-Hour',
										value: '3',
									},
								] }
								infoText={
									<>
										<h4>
											{ __(
												'Hourly Forecast Type',
												'location-weather'
											) }
										</h4>
										<span>
											{ __(
												'With the free OpenWeather API, weather data is available only in 3-hour intervals.',
												'location-weather'
											) }
										</span>
									</>
								}
							/>
							{ hourlyForecastType === '1' && (
								<SelectField
									label={ __(
										'Number of Forecasts Hour to Show',
										'location-weather'
									) }
									attributes={ numberOfForecastHours }
									setAttributes={ setAttributes }
									attributesKey={ 'numberOfForecastHours' }
									items={ forecastsHours }
								/>
							) }
							{ hourlyForecastType === '3' && (
								<SelectField
									label={ __(
										'Number of Forecasts Hour to Show',
										'location-weather'
									) }
									attributes={ numOfForecastThreeHours }
									setAttributes={ setAttributes }
									attributesKey={ 'numOfForecastThreeHours' }
									items={ forecastsThreeHours }
								/>
							) }
							{ forecastDisplayStyle !== 'popup' && (
								<InputControl
									label={ __( 'Label', 'location-weather' ) }
									attributes={ hourlyTitle }
									onChange={ ( val ) =>
										setAttributes( { hourlyTitle: val } )
									}
								/>
							) }
						</>
					) }
				</>
			) }
			<div className="spl-block-pro-notice">
				<div className="spl-pro-notice-heading">
					Unlock Next-Level Detailed Weather Forecasts, including:
				</div>
				<ul>
					<li>- Weather Graph Chart</li>
					<li>- Detailed Forecast in Popup</li>
					<li>- Daily Forecast up to 16 to 30 days</li>
					<li>- Hourly Forecast up to 4 to 5 days</li>
					<li>- Daily & Hourly both Forecast Toggle</li>
					<li>- 8+ Weather Forecast Icon Sets</li>
					<li>- Extended Customization Options</li>
				</ul>
				<a
					href="https://locationweather.io/pricing/"
					target="_blank"
					rel="noopener noreferrer"
					className="spl-upgrade-pro-btn"
				>
					{ __( 'Upgrade to Pro!', 'location-weather' ) }
				</a>
			</div>
		</>
	);
};
export const MapPreferencesGeneralTab = ( { attributes, setAttributes } ) => {
	const {
		blockName,
		displayWeatherMap,
		weatherMapType,
		mapZoomLevel,
		defaultDataLayerSelection,
		defaultElevation,
		showMarker,
		spotForecast,
		forecastModel,
		forecastFrom,
		airflowPressureLines,
		weatherAttribution,
		weatherMapMaxWidth,
		weatherMapMaxHeight,
	} = attributes;

	const getActiveBlock = ( layout ) => {
		const isBlockActive =
			( displayWeatherMap &&
				weatherMapType === layout &&
				! inArray( [ 'windy-map', 'owm-map' ], blockName ) ) ||
			blockName === layout;
		return isBlockActive;
	};
	const isIndependentBlock = inArray( [ 'owm-map', 'windy-map' ], blockName );

	return (
		<>
			{ ! isIndependentBlock && (
				<>
					<Toggle
						label={ __( 'Weather Map', 'location-weather' ) }
						attributes={ displayWeatherMap }
						setAttributes={ setAttributes }
						attributesKey={ 'displayWeatherMap' }
					/>
					{ displayWeatherMap && (
						<ButtonGroup
							label={ __( 'Map Type', 'location-weather' ) }
							attributes={ weatherMapType }
							attributesKey={ 'weatherMapType' }
							setAttributes={ setAttributes }
							border={ false }
							items={ [
								{
									label: 'OpenWeather',
									value: 'owm-map',
									onlyPro: true,
								},
								{
									label: 'Windy',
									value: 'windy-map',
								},
							] }
						/>
					) }
				</>
			) }
			{ getActiveBlock( 'windy-map' ) && (
				<>
					<SelectField
						label={ __(
							'Default Data Layer Selection',
							'location-weather'
						) }
						attributes={ defaultDataLayerSelection }
						attributesKey={ 'defaultDataLayerSelection' }
						setAttributes={ setAttributes }
						items={ windyMapLayersOptions }
					/>
					<RangeControl
						label={ __( 'Zoom Level', 'location-weather' ) }
						setAttributes={ setAttributes }
						attributes={ mapZoomLevel }
						attributesKey={ 'mapZoomLevel' }
						max={ 20 }
						defaultValue={ 8 }
					/>
					<SelectField
						label={ __( 'Default Elevation', 'location-weather' ) }
						attributes={ defaultElevation }
						attributesKey={ 'defaultElevation' }
						setAttributes={ setAttributes }
						items={ windyMapElevationsOptions }
					/>
					<Toggle
						label={ __( 'Show Marker', 'location-weather' ) }
						attributes={ showMarker }
						attributesKey={ 'showMarker' }
						setAttributes={ setAttributes }
					/>
					<Toggle
						label={ __( 'Spot Forecast', 'location-weather' ) }
						attributes={ spotForecast }
						attributesKey={ 'spotForecast' }
						setAttributes={ setAttributes }
					/>
					{ spotForecast && (
						<>
							<SelectField
								label={ __(
									'Forecast Model',
									'location-weather'
								) }
								attributes={ forecastModel }
								attributesKey={ 'forecastModel' }
								setAttributes={ setAttributes }
								items={ windyForecastModelsOptions }
							/>
							<ButtonGroup
								label={ __(
									'Forecast From',
									'location-weather'
								) }
								attributes={ forecastFrom }
								attributesKey={ 'forecastFrom' }
								setAttributes={ setAttributes }
								items={ [
									{ label: 'Now', value: 'now' },
									{ label: '12h', value: '12' },
									{ label: '24h', value: '24' },
								] }
							/>
						</>
					) }
					<Toggle
						label={ __(
							'Airflow Pressure Lines',
							'location-weather'
						) }
						attributes={ airflowPressureLines }
						attributesKey={ 'airflowPressureLines' }
						setAttributes={ setAttributes }
					/>
					<Toggle
						label={ __(
							'Weather Attribution',
							'location-weather'
						) }
						attributes={ weatherAttribution }
						attributesKey={ 'weatherAttribution' }
						setAttributes={ setAttributes }
					/>
				</>
			) }
			<div className="spl-block-pro-notice">
				{ __(
					'Enhance your forecast—unlock OpenWeatherMap.',
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
export const TabsPreferenceGeneralTab = ( { attributes, setAttributes } ) => {
	const {
		splwDefaultOpenTab,
		displayWeatherForecastData,
		weatherForecastType,
		displayWeatherMap,
		splwTabAlignment,
	} = attributes;

	// default open tab
	const forecastTabs = {
		daily: [
			{
				label: __( 'Daily Forecast', 'location-weather' ),
				value: 'daily',
			},
		],
		hourly: [
			{
				label: __( 'Hourly Forecast', 'location-weather' ),
				value: 'hourly',
			},
		],
		both: [
			{
				label: __( 'Daily Forecast', 'location-weather' ),
				value: 'daily',
			},
			{
				label: __( 'Hourly Forecast', 'location-weather' ),
				value: 'hourly',
			},
		],
	};

	let initialOpenTabValues = [
		{ label: 'Current Weather', value: 'current_weather' },
	];

	// Add forecast-related tabs if enabled
	if ( displayWeatherForecastData && forecastTabs[ weatherForecastType ] ) {
		initialOpenTabValues.push( ...forecastTabs[ weatherForecastType ] );
	}

	// Add weather map tab if enabled
	if ( displayWeatherMap ) {
		initialOpenTabValues.push( { label: 'Weather Map', value: 'map' } );
	}

	return (
		<>
			<SelectField
				label={ __( 'Default Open Tab', 'location-weather' ) }
				attributes={ splwDefaultOpenTab }
				setAttributes={ setAttributes }
				attributesKey={ 'splwDefaultOpenTab' }
				items={ initialOpenTabValues }
			/>
			<ButtonGroup
				label={ __( 'Tab Alignment', 'location-weather' ) }
				attributes={ splwTabAlignment }
				setAttributes={ setAttributes }
				attributesKey={ 'splwTabAlignment' }
				items={ [
					{ label: <LeftIcon />, value: 'left' },
					{ label: <CenterIcon />, value: 'center' },
					{ label: <RightIcon />, value: 'right' },
				] }
			/>
		</>
	);
};
export const FooterGeneralTab = ( { attributes, setAttributes } ) => {
	const {
		blockName,
		displayDateUpdateTime,
		displayWeatherAttribution,
		displayLinkToOpenWeatherMap,
	} = attributes;

	return (
		<>
			{ blockName !== 'aqi-detailed' && (
				<Toggle
					label={ __( 'Data Update Time', 'location-weather' ) }
					attributes={ displayDateUpdateTime }
					setAttributes={ setAttributes }
					attributesKey={ 'displayDateUpdateTime' }
				/>
			) }
			<Toggle
				label={ __( 'Weather Attribution', 'location-weather' ) }
				attributes={ displayWeatherAttribution }
				setAttributes={ setAttributes }
				attributesKey={ 'displayWeatherAttribution' }
			/>
			{ displayWeatherAttribution && (
				<Toggle
					label={ __( 'Link to OpenWeather', 'location-weather' ) }
					attributes={ displayLinkToOpenWeatherMap }
					attributesKey={ 'displayLinkToOpenWeatherMap' }
					setAttributes={ setAttributes }
				/>
			) }
		</>
	);
};

export const AqiQualitySummaryTab = ( { attributes, setAttributes } ) => {
	const {
		enableScaleBar,
		aqiSummaryHeadingLabel,
		blockName,
		enableSummaryAqiCondition,
		enableSummaryAqiDesc,
	} = attributes;

	return (
		<>
			{ blockName === 'aqi-detailed' && (
				<Toggle
					label={ __( 'AQI Scale Bar', 'location-weather' ) }
					attributes={ enableScaleBar }
					attributesKey={ 'enableScaleBar' }
					setAttributes={ setAttributes }
				/>
			) }

			<InputControl
				label={ __( 'Heading Label', 'location-weather' ) }
				attributes={ aqiSummaryHeadingLabel }
				onChange={ ( val ) =>
					setAttributes( { aqiSummaryHeadingLabel: val } )
				}
			/>
			{ blockName === 'aqi-minimal' && (
				<>
					<Toggle
						label={ __( 'AQI Condition', 'location-weather' ) }
						attributes={ enableSummaryAqiCondition }
						attributesKey={ 'enableSummaryAqiCondition' }
						setAttributes={ setAttributes }
					/>
					<Toggle
						label={ __( 'AQI Description', 'location-weather' ) }
						attributes={ enableSummaryAqiDesc }
						attributesKey={ 'enableSummaryAqiDesc' }
						setAttributes={ setAttributes }
					/>
				</>
			) }
		</>
	);
};

export const AqiQualityParametersTab = ( { attributes, setAttributes } ) => {
	const {
		blockName,
		enablePollutantDetails,
		pollutantHeadingLabel,
		displayPollutantNameFormat,
		displaySymbolDisplayStyle,
		enablePollutantMeasurementUnit,
		enablePollutantIndicator,
	} = attributes;

	const setActiveTempUnit = ( unit ) => {
		setAttributes( { displaySymbolDisplayStyle: unit } );
	};

	return (
		<>
			<Toggle
				label={ __( 'Pollutant Details', 'location-weather' ) }
				attributes={ enablePollutantDetails }
				attributesKey={ 'enablePollutantDetails' }
				setAttributes={ setAttributes }
			/>
			{ blockName === 'aqi-detailed' && (
				<>
					<InputControl
						label={ __( 'Heading Label', 'location-weather' ) }
						attributes={ pollutantHeadingLabel }
						onChange={ ( val ) =>
							setAttributes( { pollutantHeadingLabel: val } )
						}
					/>
					<SelectField
						label={ __(
							'Pollutants Name Format',
							'location-weather'
						) }
						attributes={ displayPollutantNameFormat }
						setAttributes={ setAttributes }
						attributesKey={ 'displayPollutantNameFormat' }
						items={ [
							{
								label: __(
									'Abbreviation (SO₂)',
									'location-weather'
								),
								value: 'abbreviation',
							},
							{
								label: __(
									'Name (Sulphur Dioxide)',
									'location-weather'
								),
								value: 'name',
							},
							{
								label: __(
									'Both (Sulphur Dioxide (SO₂))',
									'location-weather'
								),
								value: 'both',
							},
						] }
					/>
				</>
			) }
			{ blockName === 'aqi-minimal' && (
				<>
					<Toggle
						label={ __( 'Measurement Units', 'location-weather' ) }
						attributes={ enablePollutantMeasurementUnit }
						attributesKey={ 'enablePollutantMeasurementUnit' }
						setAttributes={ setAttributes }
					/>
					<Toggle
						label={ __(
							'Condition Indicator',
							'location-weather'
						) }
						attributes={ enablePollutantIndicator }
						attributesKey={ 'enablePollutantIndicator' }
						setAttributes={ setAttributes }
					/>
				</>
			) }

			{ ( displayPollutantNameFormat !== 'name' ||
				blockName === 'aqi-minimal' ) && (
				<ButtonGroup
					label={ __( 'Symbol Display Style', 'location-weather' ) }
					attributes={ displaySymbolDisplayStyle }
					items={ [
						{
							label: __( 'Normal (SO2)', 'location-weather' ),
							value: 'normal',
						},
						{
							label: __( 'Subscript (SO₂)', 'location-weather' ),
							value: 'subscript',
						},
					] }
					onClick={ ( e ) => setActiveTempUnit( e ) }
				/>
			) }
		</>
	);
};

export const AqiForecastTab = () => {
	return (
		<>
			<div className="spl-block-pro-notice">
				<div className="spl-pro-only-title">
					{ __( 'Premium Only', 'location-weather' ) }
				</div>
				<div className="spl-pro-notice-heading">
					{ __(
						'Unlock Advanced AQI Forecasts',
						'location-weather'
					) }
					:
				</div>
				<ul>
					<li>
						{ '- ' }
						{ __(
							'Forecast Types: Daily, Hourly, or Both',
							'location-weather'
						) }
					</li>
				</ul>
				<div style={ { fontWeight: '600', fontSize: '12px' } }>
					{ __( 'Display AQI Forecasts as', 'location-weather' ) }:
				</div>
				<ul
					className="nested-list"
					style={ { marginLeft: '14px', marginTop: '3px' } }
				>
					<li style={ { margin: '0' } }>
						{ '• ' }
						{ __( 'Daily & Hourly Toggle', 'location-weather' ) }
					</li>
					<li style={ { margin: '0' } }>
						{ '• ' }
						{ __( 'Daily & Hourly Graph', 'location-weather' ) }
					</li>
					<li style={ { margin: '0' } }>
						{ '• ' }
						{ __(
							'Hourly Graph Inside Daily',
							'location-weather'
						) }
					</li>
					<li style={ { margin: '0' } }>
						{ '• ' }
						{ __(
							'Daily & Hourly Individually',
							'location-weather'
						) }
					</li>
				</ul>
				<ul>
					<li>
						{ '- ' }
						{ __(
							'Daily forecast (up to 4 days)',
							'location-weather'
						) }
					</li>
					<li>
						{ '- ' }
						{ __(
							'Hourly forecast (up to 96 hours)',
							'location-weather'
						) }
					</li>
					<li>
						{ '- ' }
						{ __(
							'More Customization Options',
							'location-weather'
						) }
					</li>
				</ul>
				<a
					href="https://locationweather.io/pricing/"
					target="_blank"
					rel="noopener noreferrer"
					className="spl-upgrade-pro-btn"
				>
					{ __( 'Upgrade to Pro!', 'location-weather' ) }
				</a>
			</div>
		</>
	);
};

export const AiAssistant = () => {
	return (
		<>
			<div className="spl-block-pro-notice">
				<div className="spl-pro-only-title">
					{ __( 'Premium Only', 'location-weather' ) }
				</div>
				<div className="spl-pro-notice-heading">
					{ __(
						'Upgrade to Pro and give your visitors an AI Weather Assistant, not just a widget.',
						'location-weather'
					) }
				</div>

				<ul>
					<li>
						{ __( '— AI chat — visitors ask, it answers', 'location-weather' ) }
					</li>
					<li>
						{ __( '— 7-day weather forecast', 'location-weather' ) }
					</li>
					<li>
						{ __( '— Outdoor activity & health advices', 'location-weather' ) }
					</li>
					<li>
						{ __( '— Outfit tips by age & conditions', 'location-weather' ) }
					</li>
					<li>
						{ __( '— Compare weather across destinations', 'location-weather' ) }
					</li>
					<li>
						{ __( '— Responds in 50+ languages', 'location-weather' ) }
					</li>
					<li>
						{ __( '— Full widget branding & style controls', 'location-weather' ) }
					</li>
				</ul>
				<a
					href="https://locationweather.io/pricing/"
					target="_blank"
					rel="noopener noreferrer"
					className="spl-upgrade-pro-btn"
				>
					{ __( 'Upgrade to Pro!', 'location-weather' ) }
				</a>
				<a
					href="https://locationweather.io/ai-weather-assistant/"
					target="_blank"
					rel="noopener noreferrer"
					className="spl-upgrade-pro-btn spl-upgrade-pro-btn-light"
				>
					{ __( 'See all Pro features', 'location-weather' ) }
				</a>
			</div>
		</>
	);
};
