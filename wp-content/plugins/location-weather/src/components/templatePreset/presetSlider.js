import 'swiper/css';
import 'swiper/css/pagination';
import { memo } from '@wordpress/element';
import { Swiper, SwiperSlide } from 'swiper/react';
import { Pagination, Navigation } from 'swiper/modules';
import { __ } from '@wordpress/i18n';
import './editor.scss';
import { navigateToPricing } from '../../controls';

const TemplatePresetSlider = ( {
	items,
	label = '',
	attributes,
	demoLink,
	setAttributes,
	attributesKey,
} ) => {
	const activeSlide = items?.findIndex(
		( item ) => item?.value === attributes
	);
	const onPresetSlideChange = ( activeIndex, isPricingPage ) => {
		if ( isPricingPage ) {
			navigateToPricing();
			return;
		}
		const selectedItem = items[ activeIndex ];

		// Prevent updating layout for Pro-only items
		if ( selectedItem?.onlyPro ) {
			return; // stop right here — no attribute update
		}
		const updatedPresets = items[ activeIndex ]?.value;
		const active_additional_data_layout =
			items[ activeIndex ]?.additional_layout;
		setAttributes( {
			[ attributesKey ]: updatedPresets,
			...( active_additional_data_layout && {
				active_additional_data_layout,
			} ),
		} );
	};

	return (
		<div className="spl-weather-layout-preset-slider-component spl-weather-component-mb">
			<div className="spl-weather-component-title-wrapper sp-mb-8px">
				<label className="spl-weather-component-title">{ label }</label>
			</div>
			<Swiper
				pagination={ { clickable: true } }
				navigation={ true }
				modules={ [ Pagination, Navigation ] }
				className="mySwiper"
				initialSlide={ activeSlide }
				onSlideChange={ ( e ) =>
					onPresetSlideChange( e.activeIndex, false )
				}
				key={ activeSlide }
			>
				{ items?.map( ( { Icon, onlyPro }, i ) => {
					const isActive = i === activeSlide;
					return (
						<SwiperSlide key={ i }>
							<div
								className={ `splw-preset-image ${
									onlyPro ? 'spl-only-pro-card' : ''
								} ${ isActive ? 'active' : '' }` }
								onClick={ () => {
									onPresetSlideChange( i, onlyPro );
								} }
							>
								<Icon />
								{ onlyPro && (
									<span className="spl-pro-badge">
										<a
											href="https://locationweather.io/pricing/"
											target="_blank"
											rel="noopener noreferrer"
											className="spl-pro-card-link"
										>
											{ __( 'PRO', 'location-weather' ) }
										</a>
									</span>
								) }
							</div>
						</SwiperSlide>
					);
				} ) }
			</Swiper>
		</div>
	);
};

export default memo( TemplatePresetSlider );
