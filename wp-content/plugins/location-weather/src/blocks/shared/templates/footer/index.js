import { __ } from '@wordpress/i18n';
import { memo, useCallback } from '@wordpress/element';
import { useTogglePanelBody } from '../../../../context';

const Footer = ( {
	displayLinkToOpenWeatherMap,
	blockName = 'vertical',
	lwApiType = '',
} ) => {
	const { togglePanelBody } = useTogglePanelBody();

	const attributionTextByBlock = {
		'historical-weather': __(
			'Historical Weather from',
			'location-weather'
		),
		'historical-aqi': __( 'Historical AQI from', 'location-weather' ),
		'aqi-minimal': __( 'AQI from', 'location-weather' ),
		'aqi-detailed': __( 'AQI from', 'location-weather' ),
	};

	const apiProviderInfo = {
		openweather_api: {
			label: __( 'OpenWeather', 'location-weather' ),
			url: 'https://openweathermap.org/',
		},
		weather_api: {
			label: __( 'WeatherAPI', 'location-weather' ),
			url: 'https://www.weatherapi.com/',
		},
	};

	const attributionPrefix =
		attributionTextByBlock[ blockName ] ||
		__( 'Weather from', 'location-weather' );

	const apiInfo = apiProviderInfo[ lwApiType ];
	const fullAttributionText = `${ attributionPrefix } ${ apiInfo?.label }`;

	const handleToggleFooter = useCallback( () => {
		togglePanelBody( 'footer', true );
	}, [ togglePanelBody ] );

	return (
		<div className="spl-weather-card-footer" onClick={ handleToggleFooter }>
			<div className="spl-weather-attribution sp-text-align-center">
				{ displayLinkToOpenWeatherMap ? (
					<a href={ apiInfo?.url } target="_blank">
						{ fullAttributionText }
					</a>
				) : (
					<span>{ fullAttributionText }</span>
				) }
			</div>
		</div>
	);
};

export default memo( Footer );
