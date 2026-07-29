import { useMemo } from '@wordpress/element';
import {
	calcWindSpeed,
	jsonStringify,
	weatherItemLabels,
	weatherUnits,
} from '../../../../controls';
import { useSplWeatherContextData } from '../../../../context';

const useAdditionalData = ( weatherData, attributes, skip_value = false ) => {
	const {
		displayPressureUnit,
		displayPrecipitationUnit,
		displayWindSpeedUnit,
		displayVisibilityUnit,
		splwTimeFormat,
		splwTimeZone,
		additionalDataOptions,
		displayTemperatureUnit,
	} = attributes;
	const { toUnit } = useSplWeatherContextData();

	const weatherDetailsData = useMemo( () => {
		const {
			clouds,
			gust,
			humidity,
			pressure,
			sunrise_time,
			sunset_time,
			visibility,
			wind,
			desc,
		} = weatherData?.weather_data || {};

		const isSpace = true;

		const data = {
			pressure: pressure,
			weather_desc: desc,
			humidity: `${ humidity?.value }${ isSpace ? ' ' : '' }%`,
			wind: {
				speed: `${ calcWindSpeed(
					wind,
					displayWindSpeedUnit,
					displayTemperatureUnit
				) }${ isSpace ? ' ' : '' }${ weatherUnits(
					displayWindSpeedUnit
				) }`,
				direction: wind?.direction?.unit,
			},
			gust: `${ calcWindSpeed(
				gust?.value,
				displayWindSpeedUnit,
				displayTemperatureUnit
			) }${ isSpace ? ' ' : '' }${ weatherUnits(
				displayWindSpeedUnit
			) }`,
			visibility: visibility,
			sunrise: sunrise_time,
			sunset: sunset_time,
			clouds,
		};
		return data;
	}, [
		displayPressureUnit,
		displayWindSpeedUnit,
		displayVisibilityUnit,
		splwTimeFormat,
		splwTimeZone,
		jsonStringify( weatherData?.weather_data ),
		displayTemperatureUnit,
		toUnit,
	] );

	const weatherDetailsAttr = useMemo( () => {
		const options = additionalDataOptions?.reduce(
			( acc, { value, isActive } ) => {
				if ( ! isActive && ! skip_value ) return acc;
				switch ( value ) {
					case 'sunriseSunset':
						return acc.concat( [
							{ label: 'Sunrise', value: 'sunrise', isActive },
							{ label: 'Sunset', value: 'sunset', isActive },
						] );
					default:
						return acc.concat( {
							label: weatherItemLabels[ value ],
							value,
							isActive,
						} );
				}
			},
			[]
		);
		let filteredOptions = [];
		options?.forEach( ( option ) => {
			if ( weatherDetailsData[ option?.value ] ) {
				filteredOptions = [ ...filteredOptions, option ];
			}
		} );
		return filteredOptions;
	}, [ jsonStringify( additionalDataOptions ) ] );

	return {
		weatherDetailsData,
		weatherDetailsAttr,
	};
};
export default useAdditionalData;
