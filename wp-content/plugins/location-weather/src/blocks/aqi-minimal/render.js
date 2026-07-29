import { useMemo, memo, useCallback } from '@wordpress/element';
import AqiCardSummary from './templates/aqiCardSummary';
import AqiCardPollutant from './templates/aqiCardPollutant';
import Footer from '../shared/templates/footer';
import { useTogglePanelBody } from '../../context';

const Render = memo( ( { attributes, weatherData } ) => {
	const {
		template,
		enablePollutantDetails,
		displaySymbolDisplayStyle,
		enablePollutantIndicator,
		enablePollutantMeasurementUnit,
		bgColorType,
		videoType,
		bgVideo,
		youtubeVideo,
		displayWeatherAttribution,
		displayDateUpdateTime,
		enableSummaryAqiDesc,
		displayLinkToOpenWeatherMap,
		showCurrentTime,
		showCurrentDate,
		aqiSummaryHeadingLabel,
		showLocationName,
		customCityName,
		searchWeatherBy,
		uniqueId,
		searchPosition,
		weatherSearch,
		splwTimeZone,
		splwTimeFormat,
		splwDateFormat,
		splwCustomDateFormat,
		enableSummaryAqiCondition,
		blockName,
		lw_api_type,
	} = attributes;

	const { togglePanelBody } = useTogglePanelBody();

	// // Extract Current AQI data.
	const aqiData = weatherData?.aqi_data?.list[ 0 ]?.components;

	const weatherInfo = weatherData?.weather_data || {};
	const { time_zone, updated_time, city, country, time, date } = weatherInfo;

	const handleToggleSummary = useCallback(
		() => togglePanelBody( 'aqi-quality-summary', true ),
		[ togglePanelBody ]
	);

	// // Memoize props passed to child components
	const pollutantProps = useMemo(
		() => ( {
			displaySymbolDisplayStyle,
			weatherData: aqiData,
			enablePollutantIndicator,
			enablePollutantMeasurementUnit,
			template,
			uniqueId,
		} ),
		[
			displaySymbolDisplayStyle,
			aqiData,
			enablePollutantIndicator,
			enablePollutantMeasurementUnit,
			template,
			uniqueId,
		]
	);
	// Memoized attrs for the summery section.
	const aqiCardSummaryAttr = useMemo(
		() => ( {
			showCurrentTime,
			showCurrentDate,
			aqiSummaryHeadingLabel,
			showLocationName,
			customCityName,
			searchWeatherBy,
			template,
			blockName,
			uniqueId,
			searchPosition,
			weatherSearch,
			splwTimeZone,
			splwTimeFormat,
			splwDateFormat,
			splwCustomDateFormat,
			enableSummaryAqiCondition,
			enableSummaryAqiDesc,
		} ),
		[
			showCurrentTime,
			showCurrentDate,
			aqiSummaryHeadingLabel,
			showLocationName,
			customCityName,
			searchWeatherBy,
			template,
			blockName,
			uniqueId,
			searchPosition,
			weatherSearch,
			splwTimeZone,
			splwTimeFormat,
			splwDateFormat,
			splwCustomDateFormat,
			enableSummaryAqiCondition,
			enableSummaryAqiDesc,
		]
	);
	const summaryWeatherInfo = useMemo(
		() => ( {
			city,
			country,
			currentAQI: aqiData,
			time_zone,
			time,
			date,
		} ),
		[ city, country, aqiData, time_zone, time, date ]
	);

	return (
		<>
			<div
				className={ `spl-weather-aqi-minimal-card-wrapper spl-weather-template-wrapper sp-d-flex sp-flex-col sp-justify-center sp-${ template }-template` }
			>
				{ /* Summary */ }
				<AqiCardSummary
					attributes={ aqiCardSummaryAttr }
					weatherData={ summaryWeatherInfo }
				/>
				{ /* Pollutant Details */ }
				{ enablePollutantDetails && (
					<AqiCardPollutant { ...pollutantProps } />
				) }
				{ /* Footer */ }
				{ displayDateUpdateTime && (
					<div className="spl-weather-detailed has-padding sp-d-flex sp-justify-end sp-align-i-center">
						<div className="spl-weather-last-updated-time">
							Last updated: { updated_time }.
						</div>
					</div>
				) }
				{ displayWeatherAttribution && (
					<Footer
						displayLinkToOpenWeatherMap={
							displayLinkToOpenWeatherMap
						}
						blockName="aqi-minimal"
						lwApiType={ lw_api_type }
					/>
				) }
			</div>
		</>
	);
} );

export default Render;
