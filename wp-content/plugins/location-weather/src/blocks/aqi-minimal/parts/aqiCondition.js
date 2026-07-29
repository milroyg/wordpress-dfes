import { __ } from '@wordpress/i18n';
import { memo, useMemo } from '@wordpress/element';
import AqiPollutantGauge from '../../shared/templates/aqiPollutantGauge';
import {
	AqiConditionBlock,
	AqiDescription,
	AqiLocationAndDateTime,
} from './templates-parts';

const AQI_LABEL = __( 'AQI', 'location-weather' );

const AqiCondition = ( {
	value = 0,
	pollutantData,
	attributes,
	weatherData,
} ) => {
	const {
		enableSummaryAqiDesc = true,
		enableSummaryAqiCondition = true,
		template,
	} = useMemo( () => attributes, [ attributes ] );

	// Extract needed values
	const timeZone = weatherData?.time_zone ?? null;

	// Condition block (reused in both templates)
	const conditionBlock = enableSummaryAqiCondition && (
		<AqiConditionBlock
			condition={ pollutantData.condition }
			template={ template }
		/>
	);

	return (
		<div className="spl-aqi-card-aqi-condition sp-d-flex sp-w-full sp-justify-between sp-align-i-center">
			{ template === 'aqi-minimal-three' && (
				<>
					<div className="spl-aqi-overall-value">
						{ pollutantData.iaqi }
					</div>
					<AqiLocationAndDateTime
						attributes={ attributes }
						weatherInfo={ weatherData }
						timeZone={ timeZone }
					/>
				</>
			) }
			{ template === 'aqi-minimal-six' && (
				<div className="spl-aqi-live-value sp-d-flex sp-flex-col">
					<span className="title">
						<span className="live-icon"></span>
						{ __( 'Live AQI', 'location-weather' ) }
					</span>
					<span className="value">{ pollutantData.iaqi }</span>
				</div>
			) }
			<div className="spl-aqi-card-progress-bar">
				<AqiPollutantGauge
					value={ value }
					size={ 140 }
					strokeWidth={ 8 }
					isAccordion={ true }
					conditionLabels={ false }
					labelPadding={ 5 }
					aqiText={ AQI_LABEL }
				/>
			</div>
			{ conditionBlock }
		</div>
	);
};

export default memo( AqiCondition );
