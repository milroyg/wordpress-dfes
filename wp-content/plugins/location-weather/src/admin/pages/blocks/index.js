import { __ } from '@wordpress/i18n';
import ToggleCard from '../../dashboard-parts/toggleCard';

const Blocks = ( { blockSettings, blockShowHideHandler } ) => {
	return (
		<div className="spl-weather-blocks-settings-container">
			<h3 className="spl-weather-blocks-setting-title">{ __( 'Weather & AQI Blocks', 'location-weather' ) }</h3>
			<div className="spl-weather-blocks-settings-card-wrapper">
				{ blockSettings?.map( ( card, i ) => (
					<ToggleCard
						key={ i }
						attributes={ card }
						blockShowHideHandler={ blockShowHideHandler }
					/>
				) ) }
			</div>
		</div>
	);
};

export default Blocks;
