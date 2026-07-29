import { __ } from '@wordpress/i18n';
import { inArray, jsonParse } from '../../controls';
import ForecastTableLayout from '../shared/templates/forecastData/tableLayout';
import TableOne from './templates/table-one';

const Render = ( { attributes, weatherData } ) => {
	const {
		template,
		imageType,
		bgColorType,
		weatherForecastType,
		displayWeatherForecastData,
	} = attributes;

	const isShowTop = template === 'table-one' ? true : false;

	return (
		<div className={ `spl-weather-${ template }-block-wrapper` }>
			<TableOne attributes={ attributes } weatherData={ weatherData } />
			{ displayWeatherForecastData && (
				<ForecastTableLayout
					attributes={ attributes }
					forecast_data={ weatherData?.forecast_data }
					dataType={ 'hourly' }
					isShowTop={ isShowTop }
					separator={ 'vertical-bar' }
				/>
			) }
		</div>
	);
};

export default Render;
