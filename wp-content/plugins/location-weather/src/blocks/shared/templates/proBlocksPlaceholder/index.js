import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import detailedAqiImg from './images/detailed_aqi.webp';
import detailedWeatherImg from './images/detailed_weather.webp';
import weatherAccordionImage from './images/weather_accordion.webp';
import openWeatherMapImg from './images/map.webp';
import historicalWeatherImg from './images/historical_weather.webp';
import historicalAqiImg from './images/historical_weather.webp';
import sunMoonImg from './images/sun_moon.webp';
import { useSelect, useDispatch } from '@wordpress/data';
import { store as blockEditorStore } from '@wordpress/block-editor';
import { blockRegisterInfo } from '../../../../controls';
import {
	AccordionPreview,
	AqiDetailedPreview,
	CombinedPreview,
	HistoricalAQIPreview,
	HistoricalWeatherPreview,
	OWMPreview,
	SunMoonTimesPreview,
} from '../../../../icons';
import { Dashicon } from '@wordpress/components';
const ProBlockPlaceholder = ( { clientId, attributes } ) => {
	const { isPreview } = attributes;
	const proBlockPreviewIcons = {
		'sp-location-weather-pro/combined': CombinedPreview,
		'sp-location-weather-pro/accordion': AccordionPreview,
		'sp-location-weather-pro/aqi-detailed': AqiDetailedPreview,
		'sp-location-weather-pro/map': OWMPreview,
		'sp-location-weather-pro/historical-weather': HistoricalWeatherPreview,
		'sp-location-weather-pro/historical-aqi': HistoricalAQIPreview,
		'sp-location-weather-pro/sun-moon': SunMoonTimesPreview,
	};

	const proPlaceHolderData = {
		'sp-location-weather-pro/combined': {
			title: 'Detailed Weather Forecast',
			desc: __(
				'Unlock Detailed Weather Forecast and advanced layouts by upgrading to the Pro plan.',
				'location-weather'
			),
			image: detailedWeatherImg,
		},
		'sp-location-weather-pro/aqi-detailed': {
			title: 'Detailed Air Quality',
			desc: __(
				'Unlock AQI - Detailed Air Quality and advanced layouts by upgrading to the Pro plan.',
				'location-weather'
			),
			image: detailedAqiImg,
		},
		'sp-location-weather-pro/accordion': {
			title: 'Weather Accordion',
			desc: __(
				'Unlock Weather Accordion and advanced layouts by upgrading to the Pro plan.',
				'location-weather'
			),
			image: weatherAccordionImage,
		},
		'sp-location-weather-pro/map': {
			title: 'Weather Map By Open Weather Map',
			desc: __(
				'Unlock Weather Map by OWM and advanced layouts by upgrading to the Pro plan.',
				'location-weather'
			),
			image: openWeatherMapImg,
		},
		'sp-location-weather-pro/historical-weather': {
			title: 'Historical Weather Data',
			desc: __(
				'Unlock Historical Weather Data and advanced layouts by upgrading to the Pro plan.',
				'location-weather'
			),
			image: historicalWeatherImg,
		},
		'sp-location-weather-pro/historical-aqi': {
			title: 'Historical Air Quality Data',
			desc: __(
				'Unlock Historical Air Quality Data and advanced layouts by upgrading to the Pro plan.',
				'location-weather'
			),
			image: historicalAqiImg,
		},
		'sp-location-weather-pro/sun-moon': {
			title: 'Sun & Moon Times',
			desc: __(
				'Unlock Detailed Weather Forecast and advanced layouts by upgrading to the Pro plan.',
				'location-weather'
			),
			image: sunMoonImg,
		},
	};
	const block = useSelect(
		( select ) => select( blockEditorStore ).getBlock( clientId ),
		[ clientId ]
	);

	const blockName = block?.name;

	const PreviewIcon = proBlockPreviewIcons[ blockName ];
	if ( isPreview ) {
		return <PreviewIcon />;
	}
	const { removeBlock } = useDispatch( blockEditorStore );

	const handleRemove = () => {
		removeBlock( clientId );
	};
	const demoLink = blockRegisterInfo[ blockName ]?.demoLink;

	return (
		<div { ...useBlockProps() }>
			<div className="spl-weather-pro-blocks-placeholder sp-d-flex">
				<button
					className="spl-weather-pro-block-placeholder-remove"
					onClick={ handleRemove }
				>
					<Dashicon icon="no-alt" />
				</button>
				<div className="spl-weather-pro-blocks-placeholder-left sp-d-flex sp-flex-col sp-justify-center">
					<div className="spl-weather-pro-block-placeholder-title">
						{ proPlaceHolderData[ blockName ]?.title }
					</div>
					<div className="spl-weather-pro-block-placeholder-desc">
						{ proPlaceHolderData[ blockName ]?.desc }
					</div>
					<div className="spl-weather-pro-plan-btn-wrapper sp-d-flex">
						<a
							className="spl-weather-pro-plan-demo-btn"
							href={ demoLink }
							target="_blank"
						>
							{ __( 'View Demo', 'location-weather' ) }
						</a>
						<a
							className="spl-weather-pro-plan-upgrade-btn"
							href="https://locationweather.io/pricing/"
							target="_blank"
						>
							{ __( 'Upgrade To Pro', 'location-weather' ) }
						</a>
					</div>
				</div>
				<div className="spl-weather-pro-blocks-placeholder-right sp-d-flex">
					<img
						src={ proPlaceHolderData[ blockName ]?.image }
						alt={ __( 'Upgrade to Pro Plan', 'location-weather' ) }
					/>
				</div>
			</div>
		</div>
	);
};

export default ProBlockPlaceholder;
