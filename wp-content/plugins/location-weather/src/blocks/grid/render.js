import { iconFolderName, inArray } from '../../controls';
import GridOne from './templates/grid-one';

const Render = ( { attributes, weatherData } ) => {
	const {
		template,
		weatherForecastType,
		hourlyForecastType,
		forecastDataIcon,
		forecastDataIconType,
		hourlyTitle,
		forecastData: forecastTitleArray,
	} = attributes;

	const forecastIconFolder = iconFolderName( forecastDataIconType );
	const forecastTitleAttr = {
		hourlyTitle,
		forecastTitleArray,
		weatherForecastType,
		forecastDataIconType,
	};

	const forecastValueAttr = {
		template,
		displayIcon: forecastDataIcon,
		iconUrl: `${ splWeatherBlockLocalize.pluginUrl }/assets/images/icons/${ forecastIconFolder }`,
		hourlyForecastNum: `${ hourlyForecastType }${ weatherForecastType }`,
		forecastDataIconType,
	};

	return (
		<div
			className={ `spl-weather-${ template }-wrapper sp-d-flex sp-flex-col` }
		>
			<GridOne
				attributes={ attributes }
				weatherData={ weatherData }
				forecastTitleAttr={ forecastTitleAttr }
				forecastValueAttr={ forecastValueAttr }
			/>
		</div>
	);
};

export default Render;
