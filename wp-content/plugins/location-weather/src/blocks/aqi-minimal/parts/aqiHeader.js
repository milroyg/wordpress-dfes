import { __ } from '@wordpress/i18n';
import { AqiConditionBlock, AqiLocationAndDateTime } from './templates-parts';
import { memo } from '@wordpress/element';

const AqiHeaderSection = ( { attributes, weatherData } ) => {
	// Parse attributes only when they change
	const {
		showCurrentTime,
		showCurrentDate,
		aqiSummaryHeadingLabel,
		showLocationName,
		template,
		enableSummaryAqiCondition = true,
	} = attributes;
	// Extract needed values
	const timeZone = weatherData?.time_zone ?? null;

	return (
		<div className="spl-aqi-card-header-section sp-d-flex sp-flex-col sp-w-full sp-justify-center sp-align-i-center">
			{ /* Heading */ }
			{ aqiSummaryHeadingLabel && (
				<div className="spl-aqi-card-heading">
					{ aqiSummaryHeadingLabel }
				</div>
			) }

			{ /* Location + Time */ }
			{ /* { ( showLocationName || showCurrentTime || showCurrentDate ) && ( */ }
			<AqiLocationAndDateTime
				attributes={ attributes }
				weatherInfo={ weatherData }
				timeZone={ timeZone }
			/>
		</div>
	);
};

export default memo( AqiHeaderSection );
