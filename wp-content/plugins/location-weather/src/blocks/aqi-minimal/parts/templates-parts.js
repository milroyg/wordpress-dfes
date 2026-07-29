import { __ } from '@wordpress/i18n';
import DateTime from '../../shared/templates/dateTime';
import LocationName from '../../shared/templates/locationName';
import { hexToRgba, jsonParse } from '../../../controls';
import { memo, useMemo } from '@wordpress/element';

export const AqiDescription = memo( ( { description } ) => (
	<div className="spl-aqi-card-aqi-description sp-d-flex sp-w-full">
		{ description }
	</div>
) );

export const AqiConditionBlock = memo( ( { condition, template } ) => {
	const AIR_QUALITY_LABEL = __( 'Air Quality', 'location-weather' );
	// Compute CSS class only when condition changes
	const safeCondition =
		condition?.toLowerCase?.().replace( /\s+/g, '-' ) || 'unknown';
	const conditionClass = `condition spl-aqi-card-aqi-condition-${ safeCondition }`;

	return (
		<div
			className="spl-aqi-card-condition sp-d-flex sp-flex-col sp-justify-center sp-align-i-center sp-gap-4px"
			role="status"
			aria-label={ AIR_QUALITY_LABEL }
		>
			{ template !== 'aqi-minimal-three' && (
				<span className="title">{ AIR_QUALITY_LABEL }</span>
			) }
			<span className={ conditionClass }>{ condition }</span>
		</div>
	);
} );

export const AqiLocationAndDateTime = memo(
	( { attributes, weatherInfo, timeZone } ) => {
		const {
			showCurrentTime = true,
			showCurrentDate = false,
			showLocationName = true,
			customCityName,
			searchWeatherBy,
			template,
			blockName,
			uniqueId,
			searchPosition,
		} = attributes || {};

		const { city, country } = weatherInfo || {};

		return (
			<div className="spl-aqi-card-location-time sp-d-flex sp-gap-4px">
				{ showLocationName && (
					<LocationName
						attributes={ {
							customCityName,
							searchWeatherBy,
							template,
							blockName,
							uniqueId,
							searchPosition,
						} }
						weatherData={ {
							city,
							country,
						} }
					/>
				) }
				{ ( showCurrentTime || showCurrentDate ) && (
					<DateTime
						weatherData={ weatherInfo }
						attributes={ {
							showCurrentDate,
							showCurrentTime,
						} }
					/>
				) }
			</div>
		);
	}
);
