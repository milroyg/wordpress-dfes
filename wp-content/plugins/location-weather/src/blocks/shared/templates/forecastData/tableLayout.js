import { memo, useState } from '@wordpress/element';
import { ForecastTableRow, ForecastTableHeader } from './templates';
import { useForecastData } from './useForecastData';
import { useTogglePanelBody } from '../../../../context';

const ForecastTableLayout = ( {
	attributes,
	forecast_data,
	dataType,
	isShowTop = false,
	separator = 'slash',
} ) => {
	const { togglePanelBody } = useTogglePanelBody();
	const {
		blockName,
		forecastData,
		forecastDataIconType,
		additionalDataIconType,
	} = attributes;
	const [ currentPage, setCurrentPage ] = useState( 1 );

	const forecastDataObjKey = 'hourly';
	const forecastsArray = forecast_data;

	const weatherForecast = forecastsArray?.slice(
		currentPage * 8 - 8,
		currentPage * 8
	);

	const { filteredForecastData } = useForecastData(
		attributes,
		weatherForecast,
		forecastDataObjKey,
		false
	);

	const forecastTitle = [
		{ id: forecastData.length + 1, name: 'date', value: true },
		{ id: forecastData.length + 2, name: 'weather', value: true },
		...forecastData,
	].filter( ( { value } ) => value );

	return (
		<div
			className="spl-weather-forecast-table-layout"
			onClick={ () => togglePanelBody( 'forecast-data', true ) }
		>
			{ blockName === 'table' && (
				<div className="spl-weather-table-forecast-title">
					Hourly Forecast
				</div>
			) }
			<table className="spl-weather-forecast-table sp-w-full">
				{ /* thead */ }
				<ForecastTableHeader
					isShowTop={ isShowTop }
					forecastData={ forecastTitle }
					iconType={ additionalDataIconType }
					forecastType={ dataType }
				/>
				{ /* thead */ }
				<tbody>
					{ filteredForecastData?.map( ( forecast, i ) => (
						<ForecastTableRow
							key={ i }
							forecast={ forecast }
							attributes={ {
								forecastTitle,
								forecastDataIconType,
							} }
							separator={ separator }
						/>
					) ) }
				</tbody>
			</table>
		</div>
	);
};

export default memo( ForecastTableLayout );
