import { __ } from '@wordpress/i18n';
import { inArray, jsonParse } from '../../controls';
import HorizontalOne from './templates/horizontal-one';
import Footer from '../shared/templates/footer';

const Render = ( { attributes, weatherData } ) => {
	const {
		template,
		displayDateUpdateTime,
		displayLinkToOpenWeatherMap,
		displayWeatherAttribution,
		lw_api_type,
	} = attributes;

	return (
		<>
			<div
				className={ `spl-weather-template-wrapper spl-weather-${ template }-wrapper` }
			>
				{ template === 'horizontal-one' && (
					<HorizontalOne
						attributes={ attributes }
						weatherData={ weatherData }
					/>
				) }
			</div>
			{ displayDateUpdateTime && (
				<div className="spl-weather-detailed sp-d-flex sp-justify-end sp-align-i-center has-padding">
					<div className="spl-weather-last-updated-time">
						Last updated:{ ' ' }
						{ weatherData?.weather_data?.updated_time }
					</div>
				</div>
			) }
			{ displayWeatherAttribution && (
				<Footer
					displayLinkToOpenWeatherMap={ displayLinkToOpenWeatherMap }
					lwApiType={ lw_api_type }
				/>
			) }
		</>
	);
};

export default Render;
