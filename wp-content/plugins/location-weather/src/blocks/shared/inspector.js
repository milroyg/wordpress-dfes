import { __ } from '@wordpress/i18n';
import { PanelBody } from '@wordpress/components';
import {
	AdditionalDataGeneralTab,
	BasicPreferencesGeneralTab,
	CurrentWeatherGeneralTab,
	FooterGeneralTab,
	ForecastDataGeneralTab,
	SetLocationGeneralTab,
	MapPreferencesGeneralTab,
	MeasurementUnitsGeneralTab,
	TabsPreferenceGeneralTab,
	TemplatePresetsGeneralTab,
	AqiQualitySummaryTab,
	AqiQualityParametersTab,
	AqiForecastTab,
	AiAssistant,
} from './generalTab';
import {
	AdditionalDataStyleTab,
	aqiParameterStyleTab,
	aqiSummaryStyleTab,
	BasicPreferencesStyleTab,
	CurrentWeatherStyleTab,
	FooterStyleTab,
	ForecastDataStyleTab,
	MapPreferencesStyleTab,
	TablePreferencesStyleTab,
	TabsStyleTab,
	WeatherLayoutStyleTab,
} from './styleTab';
import { VisibilityTab, AdvancedTab } from './advancedTab';
import {
	AdditionalDataGeneralIcon,
	AdvanceSettingIcon,
	AqiForecastIcon,
	AqiPollutantIcon,
	AqiSummaryIcon,
	BasicPreferencesStylesIcon,
	CurrentWeatherStylesIcon,
	FooterStylesIcon,
	MeasurementUnitsIcon,
	PopupWeatherDataIcon,
	TableIcon,
	TabsIcon,
	TemplatePresetIcon,
	WeatherApiKeyIcon,
	WeatherDataByIcon,
	WeatherForecastDataStylesIcon,
} from '../../icons';
import { SelectField, TabControls } from '../../components';
import { useTogglePanelBody } from '../../context';
import { inArray } from '../../controls';

const Inspector = ( { attributes, setAttributes } ) => {
	const { blockName } = attributes;
	const {
		activeTab,
		openedPanelBody,
		toggleActiveTab,
		togglePanelBody,
		lw_api_type,
	} = useTogglePanelBody();

	const templatePresetsLabels = {
		vertical: __( 'Vertical Card Templates', 'location-weather' ),
		horizontal: __( 'Horizontal Templates', 'location-weather' ),
		tabs: __( 'Tabs Templates', 'location-weather' ),
		table: __( 'Table Templates', 'location-weather' ),
		map: __( 'Map Templates', 'location-weather' ),
		'windy-map': __( 'Map Templates', 'location-weather' ),
		grid: __( 'Grid Templates', 'location-weather' ),
		'aqi-minimal': __( 'AQI Templates', 'location-weather' ),
	};

	const apiTypeOptions =
		blockName === 'aqi-minimal'
			? [
					{
						label: 'OpenWeather',
						value: 'openweather_api',
					},
			  ]
			: [
					{
						label: 'OpenWeather',
						value: 'openweather_api',
					},
					{
						label: 'WeatherAPI',
						value: 'weather_api',
					},
			  ];

	return (
		<>
			{ /* Template control panel */ }
			{ ! inArray( [ 'owm-map', 'windy-map' ], blockName ) && (
				<PanelBody
					title={ templatePresetsLabels[ blockName ] }
					icon={ <TemplatePresetIcon /> }
					opened={ openedPanelBody === 'templates' }
					onToggle={ () => togglePanelBody( 'templates' ) }
				>
					<TabControls
						GeneralTab={ TemplatePresetsGeneralTab }
						StyleTab={ WeatherLayoutStyleTab }
						attributes={ attributes }
						setAttributes={ setAttributes }
						AdvancedTab={ false }
						tabName={ activeTab }
						setTabName={ toggleActiveTab }
					/>
				</PanelBody>
			) }
			{ ! inArray( [ 'owm-map', 'windy-map' ], blockName ) && (
				<PanelBody
					title={ __( 'Weather API Source', 'location-weather' ) }
					icon={ <WeatherApiKeyIcon /> }
					opened={ openedPanelBody === 'api-key-type' }
					onToggle={ () => togglePanelBody( 'api-key-type' ) }
				>
					<SelectField
						label={ __( 'Select API Source', 'location-weather' ) }
						attributes={ lw_api_type }
						flexStyle={ false }
						items={ apiTypeOptions }
						onChange={ ( newValue ) => {
							setAttributes( {
								lw_api_type: newValue,
							} );
						} }
					/>
				</PanelBody>
			) }
			{ /* Get Weather Data by control panel */ }
			<PanelBody
				title={ __( 'Set Location', 'location-weather' ) }
				icon={ <WeatherDataByIcon /> }
				opened={ openedPanelBody === 'set-location' }
				onToggle={ () => togglePanelBody( 'set-location' ) }
			>
				<SetLocationGeneralTab
					attributes={ attributes }
					setAttributes={ setAttributes }
				/>
			</PanelBody>

			{ ! inArray( [ 'aqi-detailed', 'aqi-minimal' ], blockName ) && (
				<>
					{ /* Measurement Units control panel */ }
					<PanelBody
						title={ __( 'Measurement Units', 'location-weather' ) }
						icon={ <MeasurementUnitsIcon /> }
						opened={ openedPanelBody === 'measurement-units' }
						onToggle={ () =>
							togglePanelBody( 'measurement-units' )
						}
					>
						<MeasurementUnitsGeneralTab
							attributes={ attributes }
							setAttributes={ setAttributes }
						/>
					</PanelBody>
				</>
			) }

			{ ! inArray( [ 'owm-map', 'windy-map' ], blockName ) && (
				<>
					{ /* Regional Preferences control panel */ }
					<PanelBody
						title={ __(
							'Regional Preferences',
							'location-weather'
						) }
						icon={ <BasicPreferencesStylesIcon /> }
						opened={ openedPanelBody === 'regional-preferences' }
						onToggle={ () =>
							togglePanelBody( 'regional-preferences' )
						}
					>
						<TabControls
							GeneralTab={ BasicPreferencesGeneralTab }
							StyleTab={ BasicPreferencesStyleTab }
							attributes={ attributes }
							setAttributes={ setAttributes }
							AdvancedTab={ false }
							tabName={ activeTab }
							setTabName={ toggleActiveTab }
						/>
					</PanelBody>

					{ ! inArray(
						[ 'aqi-detailed', 'aqi-minimal' ],
						blockName
					) && (
						<>
							{ /* Current Weather control panel */ }
							<PanelBody
								title={ __(
									'Current Weather',
									'location-weather'
								) }
								icon={ <CurrentWeatherStylesIcon /> }
								opened={ openedPanelBody === 'current-weather' }
								onToggle={ () =>
									togglePanelBody( 'current-weather' )
								}
							>
								<TabControls
									GeneralTab={ CurrentWeatherGeneralTab }
									StyleTab={ CurrentWeatherStyleTab }
									attributes={ attributes }
									setAttributes={ setAttributes }
									AdvancedTab={ false }
									tabName={ activeTab }
									setTabName={ toggleActiveTab }
								/>
							</PanelBody>
							{ /* Additional Data control panel */ }
							<PanelBody
								title={ __(
									'Additional Data',
									'location-weather'
								) }
								icon={ <AdditionalDataGeneralIcon /> }
								opened={ openedPanelBody === 'additional-data' }
								onToggle={ () =>
									togglePanelBody( 'additional-data' )
								}
							>
								<TabControls
									GeneralTab={ AdditionalDataGeneralTab }
									StyleTab={ AdditionalDataStyleTab }
									attributes={ attributes }
									setAttributes={ setAttributes }
									AdvancedTab={ false }
									tabName={ activeTab }
									setTabName={ toggleActiveTab }
								/>
							</PanelBody>
							{ /* Weather Forecast Data control panel */ }
							<PanelBody
								title={ __(
									'Forecast Data',
									'location-weather'
								) }
								icon={ <WeatherForecastDataStylesIcon /> }
								opened={ openedPanelBody === 'forecast-data' }
								onToggle={ () =>
									togglePanelBody( 'forecast-data' )
								}
							>
								<TabControls
									GeneralTab={ ForecastDataGeneralTab }
									StyleTab={ ForecastDataStyleTab }
									attributes={ attributes }
									setAttributes={ setAttributes }
									AdvancedTab={ false }
									tabName={ activeTab }
									setTabName={ toggleActiveTab }
								/>
							</PanelBody>
						</>
					) }
				</>
			) }
			{ /* Map Preferences general */ }
			{ inArray( [ 'windy-map', 'grid', 'tabs' ], blockName ) && (
				<PanelBody
					title={ __(
						'Weather Map Preferences',
						'location-weather'
					) }
					icon={ <PopupWeatherDataIcon /> }
					opened={ openedPanelBody === 'map-preferences' }
					onToggle={ () => togglePanelBody( 'map-preferences' ) }
				>
					{ blockName !== 'tabs' && (
						<TabControls
							GeneralTab={ MapPreferencesGeneralTab }
							StyleTab={ MapPreferencesStyleTab }
							attributes={ attributes }
							setAttributes={ setAttributes }
							AdvancedTab={ false }
							tabName={ activeTab }
							setTabName={ toggleActiveTab }
						/>
					) }
					{ blockName === 'tabs' && (
						<MapPreferencesGeneralTab
							attributes={ attributes }
							setAttributes={ setAttributes }
						/>
					) }
				</PanelBody>
			) }
			{ /* Tabs control panel */ }
			{ blockName === 'tabs' && (
				<PanelBody
					title={ __( 'Tabs Preferences', 'location-weather' ) }
					icon={ <TabsIcon /> }
					opened={ openedPanelBody === 'tab' }
					onToggle={ () => togglePanelBody( 'tab' ) }
				>
					<TabControls
						GeneralTab={ TabsPreferenceGeneralTab }
						StyleTab={ TabsStyleTab }
						attributes={ attributes }
						setAttributes={ setAttributes }
						AdvancedTab={ false }
						tabName={ activeTab }
						setTabName={ toggleActiveTab }
					/>
				</PanelBody>
			) }
			{ inArray( [ 'tabs', 'table' ], blockName ) && (
				<PanelBody
					title={ __( 'Table Preferences', 'location-weather' ) }
					icon={ <TableIcon /> }
					opened={ openedPanelBody === 'table' }
					onToggle={ () => togglePanelBody( 'table' ) }
				>
					<TablePreferencesStyleTab
						attributes={ attributes }
						setAttributes={ setAttributes }
					/>
				</PanelBody>
			) }
			{ blockName === 'aqi-minimal' && (
				<>
					<PanelBody
						title={ __(
							'Air Quality Summary',
							'location-weather'
						) }
						icon={ <AqiSummaryIcon /> }
						opened={ openedPanelBody === 'aqi-quality-summary' }
						onToggle={ () =>
							togglePanelBody( 'aqi-quality-summary' )
						}
					>
						<TabControls
							GeneralTab={ AqiQualitySummaryTab }
							StyleTab={ aqiSummaryStyleTab }
							attributes={ attributes }
							setAttributes={ setAttributes }
							AdvancedTab={ false }
							tabName={ activeTab }
							setTabName={ toggleActiveTab }
						/>
					</PanelBody>

					<PanelBody
						title={ __( 'Pollutant Details', 'location-weather' ) }
						icon={ <AqiPollutantIcon /> }
						opened={ openedPanelBody === 'aqi-quality-parameter' }
						onToggle={ () =>
							togglePanelBody( 'aqi-quality-parameter' )
						}
					>
						<TabControls
							GeneralTab={ AqiQualityParametersTab }
							StyleTab={ aqiParameterStyleTab }
							attributes={ attributes }
							setAttributes={ setAttributes }
							AdvancedTab={ false }
							tabName={ activeTab }
							setTabName={ toggleActiveTab }
						/>
					</PanelBody>
					<PanelBody
						title={
							<>
								<span className="spl-pro-badge">
									<a
										href="https://locationweather.io/pricing/"
										target="_blank"
										rel="noopener noreferrer"
										className="spl-pro-card-link"
									>
										{/* { __( 'PRO', 'location-weather' ) } */}
									</a>
								</span>
								{ __(
									'Air Quality Forecast',
									'location-weather'
								) }
							</>
						}
						icon={ <AqiForecastIcon /> }
						opened={ openedPanelBody === 'air-quality-forecast' }
						onToggle={ () =>
							togglePanelBody( 'air-quality-forecast' )
						}
						className="spl-pro-panel"
					>
						<AqiForecastTab />
					</PanelBody>
				</>
			) }
			{['vertical', 'horizontal' ].includes(blockName) && <PanelBody
				title={
					<>
						<span className="spl-pro-badge">
							<a
								href="https://locationweather.io/pricing/"
								target="_blank"
								rel="noopener noreferrer"
								className="spl-pro-card-link"
							>
								{/* { __( 'PRO', 'location-weather' ) } */}
							</a>
						</span>
						{ __(
							'AI Weather Assistant',
							'location-weather'
						) }
					</>
				}
				icon={ <AqiForecastIcon /> }
				opened={ openedPanelBody === 'air-quality-forecast' }
				onToggle={ () =>
					togglePanelBody( 'air-quality-forecast' )
				}
				className="spl-pro-panel"
			>
				<AiAssistant />
			</PanelBody>}
			{ /* Footer control panel */ }
			{ inArray(
				[ 'vertical', 'horizontal', 'tabs', 'table', 'aqi-minimal' ],
				blockName
			) && (
				<PanelBody
					title={ __( 'Footer', 'location-weather' ) }
					icon={ <FooterStylesIcon /> }
					opened={ openedPanelBody === 'footer' }
					onToggle={ () => togglePanelBody( 'footer' ) }
				>
					<TabControls
						GeneralTab={ FooterGeneralTab }
						StyleTab={ FooterStyleTab }
						attributes={ attributes }
						setAttributes={ setAttributes }
						AdvancedTab={ false }
						tabName={ activeTab }
						setTabName={ toggleActiveTab }
					/>
				</PanelBody>
			) }
			{ /* Advanced control panel */ }
			<PanelBody
				title={ __( 'Advanced Settings', 'location-weather' ) }
				icon={ <AdvanceSettingIcon /> }
				opened={ openedPanelBody === 'advanced' }
				onToggle={ () => {
					togglePanelBody( 'advanced' );
					toggleActiveTab( 'visibility' );
				} }
			>
				<TabControls
					VisibilityTab={ VisibilityTab }
					AdvancedTab={ AdvancedTab }
					attributes={ attributes }
					setAttributes={ setAttributes }
					tabName={ activeTab }
					setTabName={ toggleActiveTab }
				/>
			</PanelBody>
		</>
	);
};

export default Inspector;
