import { __ } from '@wordpress/i18n';
import { useMemo, memo, useCallback } from '@wordpress/element';
import { useTogglePanelBody } from '../../../context';
import AqiHeaderSection from '../parts/aqiHeader';
import AqiCondition from '../parts/aqiCondition';
import { AqiDescription } from '../parts/templates-parts';
import { hexToRgba, inArray } from '../../../controls';
import { getAllPollutantData } from '../getPollutantData';

const AqiCardSummary = ( { attributes, weatherData } ) => {
	const { togglePanelBody } = useTogglePanelBody();
	const handleClick = useCallback( () => {
		togglePanelBody( 'aqi-quality-summary', true );
	}, [ togglePanelBody ] );

	// Destructure only needed attributes once
	const { enableSummaryAqiDesc } = attributes;

	// Extract first hourly AQI
	const currentAqi = weatherData?.currentAQI ?? null;

	// Memoize pollutant data to avoid recalculation on every render
	const pollutantData = useMemo( () => {
		if ( ! currentAqi?.pm2_5 ) return null;
		return getAllPollutantData( currentAqi.pm2_5, 'pm2_5' );
	}, [ currentAqi?.pm2_5 ] );

	return (
		<div
			className="spl-weather-aqi-card-summary sp-d-flex sp-flex-col sp-w-full"
			onClick={ handleClick }
			style={ {
				'--spl-aqi-condition-color': hexToRgba( pollutantData?.color ),
			} }
		>
			{ pollutantData && (
				<>
					<AqiHeaderSection
						attributes={ attributes }
						weatherData={ weatherData }
						condition={ pollutantData.condition }
					/>
					<AqiCondition
						value={ currentAqi?.pm2_5 }
						pollutantData={ pollutantData }
						attributes={ attributes }
						weatherData={ weatherData }
					/>
				</>
			) }
			{ enableSummaryAqiDesc && (
				<AqiDescription description={ pollutantData?.reportDetailed } />
			) }
		</div>
	);
};

export default memo(
	AqiCardSummary,
	( prev, next ) =>
		prev.attributes === next.attributes &&
		prev.weatherData === next.weatherData
);
