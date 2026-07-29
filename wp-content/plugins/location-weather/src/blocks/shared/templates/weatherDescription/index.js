import { memo } from '@wordpress/element';
import { useTogglePanelBody } from '../../../../context';

const WeatherDescription = ( { description } ) => {
	const { togglePanelBody } = useTogglePanelBody();
	return (
		<div
			className={ `spl-weather-real-feel-desc-wrapper sp-d-flex sp-align-i-center sp-justify-center sp-gap-8px` }
		>
			<div
				className="spl-weather-card-short-desc"
				onClick={ () => togglePanelBody( 'current-weather', true ) }
			>
				<span className="spl-weather-desc">{ description }</span>
			</div>
		</div>
	);
};

export default memo( WeatherDescription );
