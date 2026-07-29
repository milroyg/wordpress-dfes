import { useMemo } from '@wordpress/element';
import {
	inArray,
	calcPressure,
	weatherUnits,
	calcWindSpeed,
	calcPrecipitation,
	getFormatedDate,
	getFormatedTime,
	calculateTemperature,
	jsonStringify,
} from '../../../../controls';
import { useSplWeatherContextData } from '../../../../context';

// todo: day length for combined block.
const calculateDayLength = ( sunriseObj, sunsetObj ) => {
	const sunriseTime = new Date( sunriseObj?.date );
	const sunsetTime = new Date( sunsetObj?.date );
	const diffMs = sunsetTime - sunriseTime;
	const diffHours = Math.floor( diffMs / ( 1000 * 60 * 60 ) );
	const diffMinutes = Math.floor(
		( diffMs % ( 1000 * 60 * 60 ) ) / ( 1000 * 60 )
	);

	return `${ diffHours } hr, ${ diffMinutes } min`;
};

export const useForecastData = (
	attributes,
	forecast_data,
	forecastDataObjKey = 'hourly',
	activeForecast = false,
	time_zone = false
) => {
	const { apiUnit, toUnit } = useSplWeatherContextData();

	const {
		template,
		blockName,
		bothTempUnit,
		hourlyForecastType,
		displayPressureUnit,
		displayTemperatureUnit,
		forecastDaysNameLength,
		displayPrecipitationUnit,
		displayWindSpeedUnit,
		splwTimeFormat,
		splwTimeZone,
		displayWeatherForecastData,
	} = attributes;

	const filteredForecastData = useMemo( () => {
		if ( ! forecast_data ) {
			return [];
		}
		const forecastsArray = inArray( Object.keys( forecast_data ), 'daily' )
			? forecast_data[ forecastDataObjKey ]
			: forecast_data;

		const forecastData = Array.isArray( forecastsArray )
			? forecastsArray
			: [];

		const getTemp = ( temperature, fromUnit, toUnit ) => {
			let temp = {};
			Object.entries( temperature )?.map( ( [ key, value ] ) => {
				if ( ! value ) return;
				temp = {
					...temp,
					[ key ]: calculateTemperature(
						value?.value,
						fromUnit,
						toUnit
					),
				};
			} );
			return { ...temp, unit: weatherUnits( toUnit ) };
		};

		const filteredForecastData = forecastData?.map( ( forecast ) => {
			const {
				last_update,
				weather,
				temperature,
				precipitation,
				wind,
				humidity,
				pressure,
				rainchance,
				snow,
				gusts,
				uv_index,
				clouds,
			} = forecast;

			return {
				temperature:
					forecastDataObjKey === 'hourly' &&
					hourlyForecastType === '1'
						? {
								now: calculateTemperature(
									temperature?.now?.value ||
										temperature?.value,
									apiUnit,
									toUnit
								),
								unit: weatherUnits( toUnit ),
						  }
						: getTemp( temperature, apiUnit, toUnit ),
				humidity: `${ humidity?.value }${ ' ' }%`,
				precipitation: `${ calcPrecipitation(
					precipitation,
					displayPrecipitationUnit
				) } ${ weatherUnits( displayPrecipitationUnit ) }`,
				wind: `${ calcWindSpeed(
					wind,
					displayWindSpeedUnit,
					displayTemperatureUnit
				) } ${ weatherUnits( displayWindSpeedUnit ) }`,
				...( template === 'horizontal-one' && {
					windDirection: wind?.direction?.value,
				} ),
				gust: `${ calcWindSpeed(
					gusts?.value,
					displayWindSpeedUnit,
					displayTemperatureUnit
				) } ${ weatherUnits( displayWindSpeedUnit ) }`,
				pressure: `${ calcPressure(
					pressure?.value,
					displayPressureUnit
				) } ${ weatherUnits( displayPressureUnit ) }`,
				date: getFormatedDate(
					last_update,
					attributes,
					forecastDataObjKey
				),
				forecastType: `${ hourlyForecastType }hourly`,
				rainchance,
				weather,
				snow,
				uv_index: uv_index?.value || '0',
				clouds: `${ clouds?.value } ${ clouds?.unit }`,
			};
		} );
		return filteredForecastData;
	}, [
		jsonStringify( {
			displayWeatherForecastData,
			template,
			bothTempUnit,
			displayTemperatureUnit,
			hourlyForecastType,
			forecastDaysNameLength,
			displayPrecipitationUnit,
			displayPressureUnit,
			displayWindSpeedUnit,
			splwTimeFormat,
			splwTimeZone,
			forecastDataObjKey,
			toUnit,
			apiUnit,
			forecast_data,
		} ),
	] );
	const singleForecastData = useMemo( () => {
		if ( filteredForecastData.length === 0 ) {
			return [];
		}
		const data = activeForecast
			? filteredForecastData?.map( ( forecast ) => ( {
					value: forecast[ activeForecast ],
					forecastType: forecast[ 'forecastType' ],
					weather: forecast[ 'weather' ],
					date: forecast[ 'date' ],
					activeForecast,
			  } ) )
			: [];
		return data;
	}, [ activeForecast, filteredForecastData ] );

	return {
		singleForecastData,
		filteredForecastData,
	};
};
