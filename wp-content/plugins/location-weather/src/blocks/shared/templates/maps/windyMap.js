import { memo } from '@wordpress/element';

const WindyMap = ( { attributes } ) => {
	const {
		displayTemperatureUnit,
		displayPrecipitationUnit,
		displayWindSpeedUnit,
		getDataByCoordinates,
		mapZoomLevel,
		defaultDataLayerSelection,
		defaultElevation,
		showMarker,
		spotForecast,
		forecastModel,
		forecastFrom,
		airflowPressureLines,
		weatherAttribution,
	} = attributes;

	const [ lat, lon ] = getDataByCoordinates.split( ',' );

	const query_params = {
		lat,
		lon,
		detailLat: lat,
		detailLon: lon,
		location: 'coordinates',
		zoom: mapZoomLevel,
		overlay: defaultDataLayerSelection,
		marker: showMarker,
		pressure: airflowPressureLines,
		metricRain: displayPrecipitationUnit === 'inch' ? 'in' : 'mm',
		metricSnow: displayPrecipitationUnit === 'inch' ? 'in' : 'mm',
		metricWind: displayWindSpeedUnit,
		metricTemp: displayTemperatureUnit === 'imperial' ? '°F' : '°C',
		detail: spotForecast,
		calendar: forecastFrom,
		product: forecastModel,
		level: defaultElevation,
		message: weatherAttribution,
		type: 'map',
		radarRange: '-1',
	};

	const queryString = new URLSearchParams();
	for ( const [ key, value ] of Object.entries( query_params ) ) {
		if ( value !== undefined && value !== null && value !== '' ) {
			queryString.append( key, value );
		}
	}

	return (
		<div className="spl-weather-map-template spl-weather-windy-map">
			<iframe
				className="sp-location-weather-windy"
				style={ { width: '100%', height: '100%' } }
				title="Location Weather"
				loading="lazy"
				src={ `https://embed.windy.com/embed.html?${ queryString.toString() }` }
				frameBorder={ 0 }
			></iframe>
		</div>
	);
};

export default memo( WindyMap );
