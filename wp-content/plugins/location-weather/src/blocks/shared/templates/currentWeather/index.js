import LocationName from '../locationName';
import DateTime from '../dateTime';
import WeatherImage from '../weatherImage';
import CurrentTempHTML from '../currentTemp';
import { useSplWeatherContextData } from '../../../../context';
import WeatherDescription from '../weatherDescription';

const CurrentWeather = ( { attributes, weatherData } ) => {
	const { toUnit } = useSplWeatherContextData();
	const {
		template,
		weatherConditionIcon,
		weatherConditionIconType,
		showLocationName,
		displayTemperature,
		displayWeatherConditions,
		uniqueId,
	} = attributes;

	const { icon, temp, desc } = weatherData;

	return (
		<div className="spl-weather-card-current-weather sp-d-flex">
			<div className="spl-weather-header-info-wrapper sp-d-flex sp-w-full">
				{ showLocationName && (
					<LocationName
						attributes={ attributes }
						weatherData={ weatherData }
					/>
				) }
				<DateTime
					attributes={ attributes }
					weatherData={ weatherData }
				/>
			</div>
			<div className="spl-weather-current-weather-icon-wrapper sp-d-flex sp-justify-center sp-align-i-center">
				{ weatherConditionIcon && (
					<WeatherImage
						icon={ icon }
						iconType={ weatherConditionIconType }
					/>
				) }
				{ displayTemperature && (
					<CurrentTempHTML temp={ temp } attributes={ attributes } />
				) }
			</div>
			{ displayWeatherConditions && (
				<WeatherDescription description={ desc } />
			) }
		</div>
	);
};

export default CurrentWeather;
