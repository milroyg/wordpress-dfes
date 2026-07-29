import { weatherItemLabels } from '../../../../controls';
import { WeatherDetailsIcon } from '../additionalData/templates';

const ForecastAdditionalData = ( {
	data,
	forecastData,
	iconType,
	extraItems = [
		{ id: 8, name: 'gust', value: true },
		{ id: 9, name: 'uv_index', value: true },
		{ id: 10, name: 'sunrise', value: true },
		{ id: 11, name: 'sunset', value: true },
		{ id: 12, name: 'clouds', value: true },
	],
} ) => {
	const forecastTitle = [
		...forecastData?.filter( ( { name } ) => name !== 'temperature' ),
		...extraItems,
	];

	return (
		<div className="spl-weather-forecast-details sp-d-flex sp-justify-center">
			{ forecastTitle?.map( ( { name } ) => {
				const value = data[ name ] || false;
				return value ? (
					<div
						key={ name }
						className="spl-weather-forecast-card sp-d-flex sp-flex-col sp-justify-center sp-align-i-center sp-gap-4px"
					>
						<WeatherDetailsIcon
							iconName={ name }
							iconType={ iconType }
						/>
						<span className="spl-weather-forecast-details-label">
							{ weatherItemLabels[ name ] }
						</span>
						<span className="spl-weather-forecast-details-value">
							{ value }
						</span>
					</div>
				) : null;
			} ) }
		</div>
	);
};

export default ForecastAdditionalData;
