import { memo } from '@wordpress/element';
import { inArray, jsonParse } from '../../../../controls';
import { useTogglePanelBody } from '../../../../context/panelBodyContext';
import { InnerBlocks } from '@wordpress/block-editor';

const WeatherIcon = () => (
	<svg xmlns="http://www.w3.org/2000/svg" width={ 16 } height={ 16 }>
		<path d="M8 0c3.314 0 6 2.865 6 6.4 0 1.274-.45 2.449-1.265 3.665L8.586 15.7a.72.72 0 0 1-1.172 0l-4.15-5.635C2.45 8.849 2 7.673 2 6.4 2 2.865 4.686 0 8 0zm0 1.6c-2.485 0-4.5 2.149-4.5 4.8 0 .888.336 1.766.985 2.735L8 13.919l3.515-4.783c.649-.969.985-1.847.985-2.735 0-2.651-2.015-4.8-4.5-4.8z" />
		<path
			fillRule="evenodd"
			d="M6.15 4.15a2.73 2.73 0 0 1 2.975-.592 2.73 2.73 0 0 1 1.477 1.477 2.73 2.73 0 0 1 0 2.089 2.73 2.73 0 0 1-1.477 1.477A2.73 2.73 0 0 1 6.15 8.01a2.73 2.73 0 0 1 0-3.861zm1.93.5a1.43 1.43 0 0 0-1.43 1.43 1.43 1.43 0 0 0 1.43 1.43 1.43 1.43 0 0 0 1.011-.419A1.43 1.43 0 0 0 9.51 6.08a1.43 1.43 0 0 0-.419-1.011A1.43 1.43 0 0 0 8.08 4.65z"
		/>
	</svg>
);

const LocationName = ( { attributes, weatherData } ) => {
	const { customCityName, searchWeatherBy, template, blockName, uniqueId } =
		attributes;

	const { city, country } = weatherData;
	const { togglePanelBody } = useTogglePanelBody();
	const showLocationIcon =
		inArray(
			[
				'vertical-three',
				'vertical-four',
				'vertical-five',
				'vertical-six',
			],
			template
		) || 'horizontal' === blockName;

	return (
		<div
			className={ `spl-weather-card-location-name${
				showLocationIcon
					? ' sp-d-flex sp-align-i-center sp-gap-2px'
					: ''
			}` }
		>
			{ showLocationIcon && <WeatherIcon /> }
			<span
				className="spl-weather-country-city-name"
				onClick={ () => togglePanelBody( 'set-location', true ) }
			>
				{ '' === customCityName
					? `${ city }, ${ country }`
					: customCityName }
			</span>
		</div>
	);
};

export default memo( LocationName );
