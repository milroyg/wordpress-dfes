import { memo } from '@wordpress/element';
import { useTogglePanelBody } from '../../../../context';

const DateTime = ( { attributes, weatherData } ) => {
	const { togglePanelBody } = useTogglePanelBody();
	const { showCurrentDate, showCurrentTime } = attributes;

	return (
		<div
			className="spl-weather-card-date-time sp-d-flex"
			onClick={ () => togglePanelBody( 'regional-preferences', true ) }
		>
			{ showCurrentTime && (
				<span className="spl-weather-current-time">
					{ weatherData?.time }
				</span>
			) }
			{ showCurrentDate && (
				<span className="spl-weather-date">{ weatherData?.date }</span>
			) }
		</div>
	);
};

export default memo( DateTime );
