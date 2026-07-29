import { __ } from '@wordpress/i18n';
import { memo } from '@wordpress/element';
import Footer from '../../shared/templates/footer';
import WeatherTableLayout from '../../shared/templates/additionalData/tableLayout';
import CurrentWeather from '../../shared/templates/currentWeather';

const TableOne = ( { attributes, weatherData } ) => {
	const {
		displayAdditionalData,
		displayDateUpdateTime,
		displayWeatherAttribution,
		lw_api_type,
		displayLinkToOpenWeatherMap,
	} = attributes;

	return (
		<div className="spl-weather-table-current-data">
			<table className="spl-weather-current-data-table sp-w-full">
				<thead>
					<tr className="spl-weather-table-header">
						<th colSpan="1">
							{ __( 'Current Weather', 'location-weather' ) }
						</th>
						{ displayAdditionalData && (
							<th colSpan="1">
								{ __( 'Additional Data', 'location-weather' ) }
							</th>
						) }
					</tr>
				</thead>
				<tbody>
					<tr>
						<td className="spl-weather-current-data-table-left">
							<CurrentWeather
								attributes={ attributes }
								weatherData={ weatherData?.weather_data }
							/>
							{ ! displayAdditionalData &&
								displayDateUpdateTime && (
									<div className="spl-weather-detailed sp-d-flex sp-justify-end sp-align-i-center">
										<div className="spl-weather-last-updated-time">
											{ 'Last updated:' }{ ' ' }
											{
												weatherData?.weather_data
													?.updated_time
											}
										</div>
									</div>
								) }
						</td>
						{ displayAdditionalData && (
							<td className="spl-weather-current-data-table-right">
								<WeatherTableLayout
									attributes={ attributes }
									weatherData={ weatherData }
								/>
								{ displayDateUpdateTime && (
									<div className="spl-weather-detailed sp-d-flex sp-justify-end sp-align-i-center">
										<div className="spl-weather-last-updated-time">
											{ 'Last updated:' }{ ' ' }
											{
												weatherData?.weather_data
													?.updated_time
											}
										</div>
									</div>
								) }
							</td>
						) }
					</tr>
					{ displayWeatherAttribution && (
						<tr>
							<td colSpan="2" style={ { padding: 0 } }>
								<Footer
									displayLinkToOpenWeatherMap={
										displayLinkToOpenWeatherMap
									}
									lwApiType={ lw_api_type }
								/>
							</td>
						</tr>
					) }
				</tbody>
			</table>
		</div>
	);
};

export default memo( TableOne );
