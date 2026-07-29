import { useCallback, useEffect, useState } from '@wordpress/element';
import { inArray } from '../../../controls';
import {
	ForecastHeader,
	ForecastImage,
} from '../../shared/templates/forecastData/templates';
import RenderForecast from '../../shared/templates/forecastData/renderForecast';
import CurrentWeatherAndMap from '../../shared/templates/CurrentWeatherAndMap';
import { useForecastData } from '../../shared/templates/forecastData/useForecastData';
import { useTogglePanelBody } from '../../../context';

const GridOne = ( {
	attributes,
	weatherData,
	forecastTitleAttr,
	forecastValueAttr,
} ) => {
	const [ displayData, setDisplayData ] = useState( 'temperature' );
	const { togglePanelBody } = useTogglePanelBody();
	const {
		imageType,
		bgColorType,
		displayWeatherForecastData,
		weatherForecastType,
		forecastDataIconType,
	} = attributes;

	const forecast_data = weatherData?.forecast_data;
	const { singleForecastData } = useForecastData(
		attributes,
		forecast_data,
		'hourly',
		displayData
	);

	const weatherBasedImage =
		'image' === bgColorType && 'weather-based' === imageType
			? `weather-status-${ weatherData?.weather_data?.icon }`
			: '';

	return (
		<>
			<CurrentWeatherAndMap
				attributes={ attributes }
				weatherData={ weatherData }
				weatherBasedImage={ weatherBasedImage }
			/>
			{ /* hourly forecast */ }
			{ displayWeatherForecastData && (
				<div className="spl-weather-grid-card-tabs-forecast">
					<ForecastHeader
						attributes={ forecastTitleAttr }
						displayData={ displayData }
						setDisplayData={ setDisplayData }
						flex={ true }
					/>
					<RenderForecast
						forecastData={ singleForecastData }
						attributes={ attributes }
						forecastValueAttr={ forecastValueAttr }
					/>
				</div>
			) }
		</>
	);
};

export default GridOne;
