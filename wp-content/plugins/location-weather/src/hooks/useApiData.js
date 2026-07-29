import axios from 'axios';
import { useEffect, useState } from '@wordpress/element';
import { jsonStringify } from '../controls';

const useApiData = ( attributes ) => {
	const {
		blockName,
		locationAutoDetect,
		displayTemperatureUnit,
		AirQualityIndex,
		displayPressureUnit,
		displayVisibilityUnit,
		displayWindSpeedUnit,
		displayPrecipitationUnit,
		splwTimeFormat,
		splwDateFormat,
		splwCustomDateFormat,
		splwLanguage,
		splwTimeZone,
		additionalDataIcon,
		displayWeatherForecastData,
		weatherForecastType,
		numberOfForecastDays,
		numberOfForecastHours,
		numOfForecastThreeHours,
		hourlyForecastType,
		searchWeatherBy,
		getDataByCityName,
		getDataByCityID,
		getDataByZIPCode,
		getDataByCoordinates,
		customCityName,
		lw_api_type,
	} = attributes;

	const [ weatherData, setWeatherData ] = useState( {} );
	const [ loading, setLoading ] = useState( true );
	const data = new FormData();

	data.append( 'action', 'splw_ajax_block_data' );

	const queryData = {
		blockName,
		getDataByCityName,
		displayTemperatureUnit,
		displayPressureUnit,
		displayVisibilityUnit,
		displayWindSpeedUnit,
		displayPrecipitationUnit,
		splwTimeFormat,
		splwDateFormat,
		splwCustomDateFormat,
		locationAutoDetect,
		splwLanguage,
		splwTimeZone,
		additionalDataIcon,
		displayWeatherForecastData,
		weatherForecastType,
		numberOfForecastDays,
		numberOfForecastHours,
		numOfForecastThreeHours,
		hourlyForecastType,
		searchWeatherBy,
		getDataByCityID,
		getDataByZIPCode,
		getDataByCoordinates,
		customCityName,
		AirQualityIndex,
		lw_api_type,
	};
	data.append( 'weatherFormData', jsonStringify( queryData ) );
	data.append( 'splwBlockApiNonce', splWeatherBlockLocalize?.blockApiNonce );

	const getApiData = async () => {
		const response = await axios.post(
			splWeatherBlockLocalize.ajaxUrl,
			data
		);
		const weatherData = await response?.data?.data;
		setWeatherData( weatherData );
		setLoading( false );
	};

	useEffect( () => {
		getApiData();
	}, [ jsonStringify( queryData ) ] );
	return { weatherData, loading };
};

export default useApiData;
