import { AdditionalDataRegularLayout } from './additionalData';
import { inArray } from '../../../../controls';
import useAdditionalData from './useAdditionalData';
import { useTogglePanelBody } from '../../../../context';

const WeatherDetails = ( { attributes, weatherData } ) => {
	const { togglePanelBody } = useTogglePanelBody();
	const {
		active_additional_data_layout,
		displayComportDataPosition,
		template,
		additionalDataIconType,
		additionalDataIcon,
	} = attributes;

	const { weatherDetailsData, weatherDetailsAttr } = useAdditionalData(
		weatherData,
		attributes
	);

	const isAvailablePWHLayout =
		inArray(
			[ 'center', 'left', 'justified' ],
			active_additional_data_layout
		) && ! displayComportDataPosition
			? true
			: false;

	// display colon when needed.
	const displayColon =
		inArray(
			[ 'center', 'column-two', 'left', 'column-two-justified' ],
			active_additional_data_layout
		) ||
		inArray(
			[ 'horizontal-one', 'vertical-three', 'vertical-four' ],
			template
		);

	return (
		<div
			className={ `spl-weather-card-daily-details spl-weather-details-${ active_additional_data_layout }` }
			onClick={ () => togglePanelBody( 'additional-data', true ) }
		>
			<div
				className={ `spl-weather-details-wrapper${
					isAvailablePWHLayout
						? ' spl-weather-comport-data-enabled'
						: ''
				}` }
			>
				{ /* grid and list layout*/ }
				{ ! inArray(
					[ 'carousel-simple', 'carousel-flat' ],
					active_additional_data_layout
				) && (
					<AdditionalDataRegularLayout
						displayColon={ displayColon }
						weatherData={ weatherDetailsData }
						iconSetType={ additionalDataIconType }
						weatherDetailsAttr={ weatherDetailsAttr }
						isAvailablePWHLayout={ isAvailablePWHLayout }
						showIcon={ additionalDataIcon }
					/>
				) }

				{ /* swiper layout*/ }
				{ /* { inArray(
					[ 'carousel-simple', 'carousel-flat' ],
					active_additional_data_layout
				) && (
					<AdditionalDataSwiperLayout
						attributes={ attributes }
						weatherData={ weatherDetailsData }
						weatherDetailsAttr={ weatherDetailsAttr }
					/>
				) } */ }
			</div>
			{ /* { inArray( [ 'vertical-two', 'vertical-six' ], template ) && (
				<SunOrbitHtml
					iconUrl={ `${ splWeatherBlockLocalize.pluginUrl }/assets/images/icons/` }
					weatherData={ {
						sun_position: weatherDetailsData?.sun_position,
						sunrise: weatherDetailsData?.sunrise,
						sunset: weatherDetailsData?.sunset,
					} }
				/>
			) } */ }
		</div>
	);
};

export default WeatherDetails;
