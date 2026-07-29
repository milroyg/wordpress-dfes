import { memo, useEffect, useMemo, useState } from '@wordpress/element';
import { inArray, jsonStringify } from '../../../../controls';
import RenderForecast from './renderForecast';
import { ForecastHeader } from './templates';
import { useForecastData } from './useForecastData';
import { useTogglePanelBody } from '../../../../context';

const ForecastData = ( {
	attributes,
	forecast_data,
	forecastType = false,
	updateTime = false,
	separator = 'slash',
} ) => {
	if ( ! forecast_data ) {
		return;
	}

	const {
		forecastDataIconType,
		template,
		weatherForecastType,
		hourlyForecastType,
		forecastDataIcon,
		hourlyTitle,
		forecastData: forecastTitleArray,
		forecastDisplayStyle,
		blockName,
		displayDateUpdateTime,
	} = attributes;

	const [ displayData, setDisplayData ] = useState( '' );
	const [ dataType, setDataType ] = useState( 'daily' );
	const { togglePanelBody } = useTogglePanelBody();

	// filtered forecast data.
	const { singleForecastData } = useForecastData(
		attributes,
		forecast_data,
		'hourly',
		displayData
	);

	const isCalcMinMax =
		template === 'vertical-five' && displayData === 'temperature';

	const maximumTemp = isCalcMinMax
		? Math.max(
				...singleForecastData?.map( ( forecast ) => forecast.value.max )
		  )
		: '';
	const minimumTemp = isCalcMinMax
		? Math.min(
				...singleForecastData?.map( ( forecast ) => forecast.value.min )
		  )
		: '';

	const forecastTitleAttr = {
		dataType,
		hourlyTitle,
		forecastTitleArray,
		weatherForecastType,
		forecastDataIconType,
	};
	const forecastValueAttr = {
		...attributes,
		displayIcon: forecastDataIcon,
		forecastDataIconType,
		hourlyForecastNum: `${ hourlyForecastType }${ weatherForecastType }`,
		weatherForecastType,
		template,
		displayData,
		maximumTemp,
		minimumTemp,
	};
	const isHeaderFlex = inArray( [ 'horizontal-one', 'grid-one' ], template );

	useEffect( () => {
		const defaultActive = forecastTitleArray?.find(
			( option ) => option.value
		)?.name;
		setDisplayData( defaultActive );
	}, [ jsonStringify( forecastTitleArray ) ] );

	return (
		<div
			className="spl-weather-card-forecast-data"
			onClick={ () => togglePanelBody( 'forecast-data', true ) }
		>
			<ForecastHeader
				attributes={ forecastTitleAttr }
				displayData={ displayData }
				setDisplayData={ setDisplayData }
				flex={ isHeaderFlex }
			/>
			<RenderForecast
				forecastData={ singleForecastData }
				attributes={ attributes }
				forecastValueAttr={ forecastValueAttr }
				type={ forecastType }
			/>
			{ displayDateUpdateTime && 'vertical' === blockName && (
				<div className="spl-weather-detailed sp-d-flex sp-justify-end sp-align-i-center">
					<div className="spl-weather-last-updated-time">
						Last updated: { updateTime }
					</div>
				</div>
			) }
		</div>
	);
};

export default memo( ForecastData );
