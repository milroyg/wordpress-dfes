import { __ } from '@wordpress/i18n';
import { memo, useMemo } from '@wordpress/element';
import Footer from '../shared/templates/footer';
import WeatherDetails from '../shared/templates/additionalData';
import ForecastData from '../shared/templates/forecastData';
import CurrentWeather from '../shared/templates/currentWeather';

const Render = ( { attributes, weatherData } ) => {
	const {
		template,
		displayAdditionalData,
		displayWeatherForecastData,
		displayWeatherAttribution,
		displayLinkToOpenWeatherMap,
		lw_api_type,
		displayDateUpdateTime,
	} = attributes;

	const updateTime = weatherData?.weather_data?.updated_time;

	return (
		<>
			<div
				className={ `spl-weather-template-wrapper spl-weather-${ template }-wrapper` }
			>
				<CurrentWeather
					attributes={ attributes }
					weatherData={ weatherData?.weather_data }
				/>
				{ /* forecast and additional data */ }
				{ displayAdditionalData && (
					<WeatherDetails
						attributes={ attributes }
						weatherData={ weatherData }
					/>
				) }
				{ displayWeatherForecastData && (
					<ForecastData
						attributes={ attributes }
						forecast_data={ weatherData?.forecast_data }
						forecastType={
							template === 'vertical-six' ? 'layout-three' : false
						}
						updateTime={ updateTime }
					/>
				) }
				{ ! displayWeatherForecastData && displayDateUpdateTime && (
					<div className="spl-weather-detailed sp-d-flex sp-justify-end sp-align-i-center has-padding">
						<div className="spl-weather-last-updated-time">
							Last updated: { updateTime }
						</div>
					</div>
				) }
				{ /* footer */ }
				{ displayWeatherAttribution && (
					<Footer
						displayLinkToOpenWeatherMap={
							displayLinkToOpenWeatherMap
						}
						lwApiType={ lw_api_type }
					/>
				) }
			</div>
		</>
	);
};

export default memo( Render );
