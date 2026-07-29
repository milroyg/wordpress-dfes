import { memo } from '@wordpress/element';
import { iconFolderName } from '../../../../controls';
import { useTogglePanelBody } from '../../../../context';

const WeatherImage = ( { iconType, icon } ) => {
	const { togglePanelBody } = useTogglePanelBody();
	const weatherIconFolder = iconFolderName( iconType );
	const iconUrl = `${ splWeatherBlockLocalize.pluginUrl }/assets/images/icons/`;

	return (
		<div
			className="spl-weather-condition-icon sp-d-flex"
			onClick={ () => togglePanelBody( 'current-weather', true ) }
		>
			<img
				src={ `${ iconUrl }${ weatherIconFolder }/${ icon }.svg` }
				alt="weather icon"
				className="spl-weather-icon"
			/>
		</div>
	);
};

export default memo( WeatherImage );
