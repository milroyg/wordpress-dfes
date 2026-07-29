import { Swiper, SwiperSlide } from 'swiper/react';
import { Autoplay, Navigation } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import {
	ForecastLayoutOne,
	ForecastLayoutTwo,
	NavigationHtml,
} from './templates';
import { useDeviceType, inArray } from '../../../../controls';
import { useTogglePanelBody } from '../../../../context';

const RenderForecast = ( {
	attributes,
	forecastData,
	forecastValueAttr,
	type = false,
	predefinedSeparator = '',
	showDescriptionWithIcon = false,
} ) => {
	const {
		uniqueId,
		template,
		forecastCarouselAutoPlay,
		showForecastNavIcon,
		forecastCarouselAutoplayDelay,
		forecastCarouselInfiniteLoop,
		forecastCarouselColumns,
		forecastCarouselNavIcon,
		carouselStopOnHover,
		forecastCarouselSpeed,
		forecastCarouselHorizontalGap,
	} = attributes;

	const { togglePanelBody } = useTogglePanelBody();
	const activeLayout = inArray(
		[ 'vertical-one', 'vertical-three', 'grid-one' ],
		template
	)
		? 'normal'
		: 'swiper';

	const forecastType = type ? type : activeLayout;

	const deviceType = useDeviceType();

	let tempSeparator = '';
	switch ( template ) {
		case 'vertical-one':
		case 'vertical-two':
		case 'horizontal-one':
			tempSeparator = 'slash';
			break;
		case 'vertical-three':
		case 'tabs-one':
		case 'table-one':
			tempSeparator = 'vertical-bar';
			break;
		default:
			tempSeparator = 'slash';
			break;
	}
	const separator = predefinedSeparator ? predefinedSeparator : tempSeparator;

	return (
		<>
			{ inArray( [ 'normal', 'layout-three' ], forecastType ) && (
				<div
					className={ `spl-weather-forecast-data spl-weather-${ forecastType }` }
					onClick={ () => togglePanelBody( 'forecast-data', true ) }
				>
					{ forecastData?.map( ( forecast, i ) => (
						<ForecastLayoutOne
							key={ i }
							forecast={ forecast }
							attributes={ forecastValueAttr }
							separator={ separator }
							isLayoutThree={ forecastType === 'layout-three' }
							showDescriptionWithIcon={ showDescriptionWithIcon }
						/>
					) ) }
				</div>
			) }
			{ forecastType === 'swiper' && (
				<Swiper
					slidesPerView={
						forecastCarouselColumns.device[ deviceType ]
					}
					freeMode={ true }
					navigation={
						showForecastNavIcon
							? {
									prevEl: `#${ uniqueId } .spl-weather-forecast-swiper-nav-prev`,
									nextEl: `#${ uniqueId } .spl-weather-forecast-swiper-nav-next`,
							  }
							: false
					}
					speed={
						forecastCarouselSpeed.unit === 'ms'
							? forecastCarouselSpeed.value
							: forecastCarouselSpeed.value * 1000
					}
					loop={ forecastCarouselInfiniteLoop }
					modules={ [ Autoplay, Navigation ] }
					className="spl-weather-forecast-swiper"
					autoplay={
						forecastCarouselAutoPlay
							? {
									delay:
										forecastCarouselAutoplayDelay.unit ===
										'ms'
											? forecastCarouselAutoplayDelay.value
											: forecastCarouselAutoplayDelay.value *
											  1000,
									pauseOnMouseEnter: carouselStopOnHover,
									disableOnInteraction: carouselStopOnHover,
							  }
							: false
					}
					spaceBetween={
						forecastCarouselHorizontalGap.device[ deviceType ]
					}
				>
					{ forecastData?.map( ( forecast, i ) => (
						<SwiperSlide key={ i }>
							<ForecastLayoutTwo
								forecast={ forecast }
								attributes={ forecastValueAttr }
								separator={ separator }
							/>
						</SwiperSlide>
					) ) }
					{ showForecastNavIcon && (
						<NavigationHtml
							arrow={ forecastCarouselNavIcon }
							template="forecast"
						/>
					) }
				</Swiper>
			) }
		</>
	);
};

export default RenderForecast;
