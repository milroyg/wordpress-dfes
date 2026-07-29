import { ComportAdditionalData, RenderSingleWeatherData } from './templates';
import { useDeviceType, inArray } from '../../../../controls';

export const AdditionalDataRegularLayout = ( {
	displayColon,
	weatherData,
	iconSetType,
	weatherDetailsAttr,
	isAvailablePWHLayout = false,
	showIcon,
} ) => {
	const withComportData = weatherDetailsAttr?.filter(
		( { value } ) => ! inArray( [ 'pressure', 'humidity', 'wind' ], value )
	);
	const comportData = weatherDetailsAttr?.filter( ( { value } ) =>
		inArray( [ 'pressure', 'humidity', 'wind' ], value )
	);
	const additionalOptions = isAvailablePWHLayout
		? withComportData
		: weatherDetailsAttr;

	return (
		<>
			<div className="spl-weather-details-regular-data sp-w-full">
				{ additionalOptions?.map( ( option, i ) => (
					<RenderSingleWeatherData
						key={ i }
						option={ option }
						weatherData={ weatherData }
						displayColon={ displayColon }
						iconType={ iconSetType }
						showIcon={ showIcon }
					/>
				) ) }
			</div>
			{ isAvailablePWHLayout && (
				<ComportAdditionalData
					iconType={ iconSetType }
					weatherData={ weatherData }
					comportData={ comportData }
				/>
			) }
		</>
	);
};

// export const AdditionalDataSwiperLayout = ( {
// 	attributes,
// 	weatherData,
// 	weatherDetailsAttr,
// } ) => {
// 	const {
// 		uniqueId,
// 		dynamicClassNames,
// 		enableAdditionalNavIcon,
// 		additionalCarouselAutoPlay,
// 		additionalDataIconType,
// 		additionalCarouselColumns,
// 		additionalCarouselInfiniteLoop,
// 		additionalCarouselDelayTime,
// 		additionalCarouselSpeed,
// 		additionalNavigationIcon,
// 		additionalCarouselStopOnHover,
// 		additionalDataIcon,
// 		additionalDataHorizontalGap,
// 	} = attributes;

// 	const deviceType = useDeviceType();
// 	const carouselAutoplay = additionalCarouselAutoPlay
// 		? {
// 				delay:
// 					additionalCarouselDelayTime.unit === 'ms'
// 						? additionalCarouselDelayTime.value
// 						: additionalCarouselDelayTime.value * 1000,
// 				pauseOnMouseEnter: additionalCarouselStopOnHover,
// 				disableOnInteraction: additionalCarouselStopOnHover,
// 		  }
// 		: false;

// 	const carouselSpeed =
// 		additionalCarouselSpeed?.unit === 'ms'
// 			? additionalCarouselSpeed.value
// 			: additionalCarouselSpeed.value * 1000;

// 	return (
// 		<>
// 			<Swiper
// 				slidesPerView={ additionalCarouselColumns.device[ deviceType ] }
// 				freeMode={ true }
// 				navigation={
// 					enableAdditionalNavIcon
// 						? {
// 								prevEl: `#${ uniqueId } .spl-weather-additional-data-swiper-nav-prev`,
// 								nextEl: `#${ uniqueId } .spl-weather-additional-data-swiper-nav-next`,
// 						  }
// 						: false
// 				}
// 				loop={ additionalCarouselInfiniteLoop }
// 				modules={ [ Autoplay, FreeMode, Navigation ] }
// 				speed={ carouselSpeed }
// 				autoplay={ carouselAutoplay }
// 				className="spl-weather-additional-data-swiper"
// 				spaceBetween={
// 					additionalDataHorizontalGap.device[ deviceType ]
// 				}
// 			>
// 				{ weatherDetailsAttr?.map( ( option, i ) =>
// 					weatherData[ option?.value ] ? (
// 						<SwiperSlide key={ i }>
// 							<RenderSingleWeatherData
// 								option={ option }
// 								weatherData={ weatherData }
// 								dynamicClassNames={ dynamicClassNames }
// 								showIcon={ additionalDataIcon }
// 								iconType={ additionalDataIconType }
// 							/>
// 						</SwiperSlide>
// 					) : (
// 						''
// 					)
// 				) }
// 			</Swiper>
// 			{ enableAdditionalNavIcon && (
// 				<NavigationHtml
// 					arrow={ additionalNavigationIcon }
// 					template="additional-data"
// 				/>
// 			) }
// 		</>
// 	);
// };
