import { useState } from '@wordpress/element';
import WeatherDetails from '../../shared/templates/additionalData';
import { iconFolderName, useDeviceType } from '../../../controls';
import { useForecastData } from '../../shared/templates/forecastData/useForecastData';
import {
	DateTimeHTML,
	ForecastHeader,
	ForecastImage,
	NavigationHtml,
} from '../../shared/templates/forecastData/templates';
import { Swiper, SwiperSlide } from 'swiper/react';
import { Autoplay, Navigation } from 'swiper/modules';
import CurrentWeather from '../../shared/templates/currentWeather';
import { useTogglePanelBody } from '../../../context';
import RenderForecast from '../../shared/templates/forecastData/renderForecast';

const HorizontalOne = ( { attributes, weatherData } ) => {
	const {
		displayWeatherForecastData,
		uniqueId,
		forecastCarouselAutoPlay,
		showForecastNavIcon,
		forecastCarouselAutoplayDelay,
		forecastCarouselInfiniteLoop,
		forecastCarouselColumns,
		forecastCarouselNavIcon,
		carouselStopOnHover,
		forecastCarouselSpeed,
		hourlyTitle,
		forecastData: forecastTitleArray,
		forecastDataIcon,
		forecastDataIconType,
		displayAdditionalData,
		hourlyForecastType,
		weatherForecastType,
	} = attributes;
	const { togglePanelBody } = useTogglePanelBody();
	const [ displayData, setDisplayData ] = useState( 'temperature' );
	const forecastIconFolder = iconFolderName( forecastDataIconType );

	const deviceType = useDeviceType();

	const { singleForecastData } = useForecastData(
		attributes,
		weatherData?.forecast_data,
		'hourly',
		displayData
	);

	const forecastValueAttr = {
		template: 'horizontal-one',
		displayIcon: forecastDataIcon,
		iconUrl: `${ splWeatherBlockLocalize.pluginUrl }/assets/images/icons/${ forecastIconFolder }`,
		hourlyForecastNum: `${ hourlyForecastType }${ weatherForecastType }`,
		forecastDataIconType,
	};

	return (
		<>
			<div className="spl-weather-horizontal-top sp-d-flex sp-justify-between sp-w-full">
				<div className="spl-weather-horizontal-left-wrapper">
					<CurrentWeather
						attributes={ attributes }
						weatherData={ weatherData?.weather_data }
					/>
				</div>
				{ displayAdditionalData && (
					<WeatherDetails
						attributes={ attributes }
						weatherData={ weatherData }
					/>
				) }
			</div>
			{ displayWeatherForecastData && (
				<div className="spl-weather-card-forecast-data">
					<ForecastHeader
						attributes={ {
							hourlyTitle,
							forecastTitleArray,
							weatherForecastType: 'hourly',
						} }
						displayData={ displayData }
						setDisplayData={ setDisplayData }
						flex={ true }
					/>
					<RenderForecast
						forecastData={ singleForecastData }
						attributes={ attributes }
						forecastValueAttr={ forecastValueAttr }
						type={ 'normal' }
					/>
				</div>
			) }
		</>
	);
};

export default HorizontalOne;
