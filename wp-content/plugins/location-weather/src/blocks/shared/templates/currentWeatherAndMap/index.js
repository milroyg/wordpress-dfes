import { __ } from '@wordpress/i18n';
import LocationName from '../locationName';
import DateTime from '../dateTime';
import WeatherImage from '../weatherImage';
import CurrentTempHTML from '../currentTemp';
import WeatherDescription from '../weatherDescription';
import { customSlider } from '../../../../controls';
import useAdditionalData from '../additionalData/useAdditionalData';
import { RenderMap } from '../maps';
import { useTogglePanelBody } from '../../../../context';
import {
	CustomSliderNavigationIcon,
	RenderSingleWeatherData,
} from '../additionalData/templates';
import { useEffect, useRef } from '@wordpress/element';

const CurrentWeatherAndMap = ( {
	attributes,
	weatherData,
	weatherBasedImage = '',
} ) => {
	const {
		displayWeatherConditions,
		weatherConditionIconType,
		showLocationName,
		weatherConditionIcon,
		displayTemperature,
		displayAdditionalData,
		additionalDataIcon,
		additionalDataIconType,
	} = attributes;
	const sliderContainerRef = useRef( null );
	const { togglePanelBody } = useTogglePanelBody();

	const { desc, icon, temp, time_zone } = weatherData?.weather_data;
	const { weatherDetailsData, weatherDetailsAttr } = useAdditionalData(
		weatherData,
		attributes
	);

	// Initialize custom slider when component mounts and data is available
	useEffect( () => {
		if ( sliderContainerRef.current && displayAdditionalData ) {
			customSlider( sliderContainerRef.current );
		}
	}, [ weatherDetailsAttr ] );

	return (
		<div className="spl-weather-map-and-current-weather sp-d-flex sp-w-full">
			<div
				className={ `spl-weather-current-weather-card sp-d-flex sp-flex-col sp-justify-between ${ weatherBasedImage }` }
			>
				<div className="spl-weather-header-info-wrapper sp-d-flex sp-flex-col">
					<div className="sp-d-flex sp-justify-between sp-align-i-center">
						<span className="spl-weather-card-location-name">
							{ __( 'Current Weather', 'location-weather' ) }
						</span>
						{ showLocationName && (
							<LocationName
								attributes={ attributes }
								weatherData={ weatherData?.weather_data }
							/>
						) }
					</div>
					<DateTime
						attributes={ attributes }
						weatherData={ weatherData?.weather_data }
					/>
				</div>
				<div className="sp-d-flex sp-justify-between sp-align-i-center">
					<div className="spl-weather-current-weather-icon-wrapper sp-d-flex">
						{ weatherConditionIcon && (
							<WeatherImage
								icon={ icon }
								iconType={ weatherConditionIconType }
							/>
						) }
						{ displayTemperature && (
							<CurrentTempHTML
								temp={ temp }
								attributes={ attributes }
							/>
						) }
					</div>
					<div className="sp-text-align-right">
						{ displayWeatherConditions && (
							<WeatherDescription description={ desc } />
						) }
					</div>
				</div>
				{ displayAdditionalData && (
					<div
						className="spl-weather-card-daily-details"
						onClick={ () =>
							togglePanelBody( 'additional-data', true )
						}
					>
						<div
							ref={ sliderContainerRef }
							className="spl-weather-custom-slider"
						>
							{ weatherDetailsAttr?.map( ( option, i ) => (
								<div
									key={ i }
									className="spl-weather-custom-slider-item"
								>
									<RenderSingleWeatherData
										option={ option }
										weatherData={ weatherDetailsData }
										showIcon={ additionalDataIcon }
										iconType={ additionalDataIconType }
									/>
								</div>
							) ) }
							<CustomSliderNavigationIcon />
						</div>
					</div>
				) }
			</div>
			<RenderMap attributes={ attributes } />
		</div>
	);
};

export default CurrentWeatherAndMap;
