import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { inArray } from '../../controls';
import ForecastTableLayout from '../shared/templates/forecastData/tableLayout';
import RenderMap from '../shared/templates/maps/renderMap';
import CurrentWeather from '../shared/templates/currentWeather';
import SunOrbitHtml from '../shared/templates/sunOrbitHtml';
import WeatherTableLayout from '../shared/templates/additionalData/tableLayout';
import Footer from '../shared/templates/footer';
import { TabNavigationBar } from './templates';

const Render = ( { attributes, weatherData } ) => {
	const {
		template,
		imageType,
		bgColorType,
		splwDefaultOpenTab,
		displayWeatherForecastData,
		displayAdditionalData,
		displayDateUpdateTime,
		lw_api_type,
		displayLinkToOpenWeatherMap,
		displayWeatherAttribution,
	} = attributes;

	const [ activeTab, setActiveTab ] = useState( splwDefaultOpenTab );

	const weatherBasedImage =
		'image' === bgColorType && 'weather-based' === imageType
			? `weather-status-${ weatherData?.weather_data?.icon }`
			: '';

	const { sun_position, sunrise_time, sunset_time } =
		weatherData?.weather_data;

	return (
		<div
			className={ `spl-weather-template-wrapper ${ weatherBasedImage }` }
		>
			<div className={ `spl-weather-${ template }-block-wrapper` }>
				<TabNavigationBar
					attributes={ attributes }
					activeTab={ activeTab }
					setActiveTab={ setActiveTab }
				/>
				<div className="spl-weather-tabs-block-content">
					{ activeTab === 'current_weather' && (
						<>
							<div className="spl-weather-current-data sp-d-flex sp-justify-between">
								<CurrentWeather
									attributes={ attributes }
									weatherData={ weatherData?.weather_data }
								/>
								{ template === 'tabs-one' && (
									<SunOrbitHtml
										iconUrl={ `${ splWeatherBlockLocalize.pluginUrl }/assets/images/icons/` }
										weatherData={ {
											sun_position,
											sunrise: sunrise_time,
											sunset: sunset_time,
										} }
										translate="-140px"
									/>
								) }
							</div>
							{ displayAdditionalData && (
								<WeatherTableLayout
									attributes={ attributes }
									weatherData={ weatherData }
								/>
							) }
							{ displayDateUpdateTime && (
								<div className="spl-weather-detailed sp-d-flex sp-justify-end sp-align-i-center">
									<div className="spl-weather-last-updated-time">
										Last updated:{ ' ' }
										{
											weatherData?.weather_data
												?.updated_time
										}
									</div>
								</div>
							) }
							{ displayWeatherAttribution && (
								<Footer
									displayLinkToOpenWeatherMap={
										displayLinkToOpenWeatherMap
									}
									lwApiType={ lw_api_type }
								/>
							) }
						</>
					) }
					{ displayWeatherForecastData &&
						inArray( [ 'hourly' ], activeTab ) &&
						weatherData?.forecast_data && (
							<ForecastTableLayout
								attributes={ attributes }
								forecast_data={ weatherData?.forecast_data }
								dataType={ activeTab }
								isShowTop={
									template === 'tabs-two' ? true : false
								}
								separator={ 'vertical-bar' }
							/>
						) }
					{ activeTab === 'map' && (
						<RenderMap attributes={ attributes } />
					) }
				</div>
			</div>
		</div>
	);
};

export default Render;
