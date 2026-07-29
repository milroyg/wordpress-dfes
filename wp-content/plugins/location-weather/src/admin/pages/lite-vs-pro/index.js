import { __ } from '@wordpress/i18n';
import InfoText from '../../dashboard-parts/infoText';
import { VideoTooltipIcon } from '../../../icons';
import { HalfStarIcon, ProIconLight, StarIcon, WordPressIcon } from '../../icons';

const features = [
	{
		title: __( 'All Core Plugin Features', 'location-weather' ),
		info: __(
			'Access all the essential features of the plugin in the free version.',
			'location-weather'
		),
		free: 'yes',
		pro: 'yes',
	},
	{
		title: __( 'Weather Blocks', 'location-weather' ),
		free: 9,
		pro: 16,
		new: true,
		hot: true,
	},
	{
		title: __( 'Pre-made Weather Templates ', 'location-weather' ),
		free: 7,
		pro: 33,
	},
	{
		title: __( 'Ready-to-use Weather Patterns', 'location-weather' ),
		free: <b>20+</b>,
		pro: <b>100+</b>,
		new: true,
		hot: true,
	},
	{
		title: __( 'Extended Weather Forecasts', 'location-weather' ),
		free: 'no',
		pro: 'yes',
	},
	{
		title: __( 'Display Daily Forecast up to 30 Days', 'location-weather' ),
		free: 'no',
		pro: 'yes',
	},
	{
		title: __(
			'Display Hourly (1h & 3h) Forecast up to 120 Hours',
			'location-weather'
		),
		free: 'yes',
		pro: 'yes',
	},
	{
		title: __( 'Weather AI Assistant', 'location-weather' ),
		new: true,
		free: 'no',
		pro: 'yes',
	},
	{
		title: __( 'Detailed Weather in Popup View', 'location-weather' ),
		free: 'no',
		pro: 'yes',
	},
	{
		title: __( '46+ Years of Weather Historical Data', 'location-weather' ),
		free: 'no',
		pro: 'yes',
	},
	{
		title: __( 'Weather Dynamic Graph Chart', 'location-weather' ),
		free: 'no',
		pro: 'yes',
	},
	{
		title: __( 'Custom Weather Search', 'location-weather' ),
		free: 'no',
		pro: 'yes',
		video: 'https://ps.w.org/location-weather/assets/visuals/custom-weather-search.webm',
		new: true,
		hot: true,
	},
	{
		title: __( 'Control Styles Globally', 'location-weather' ),
		free: 'yes',
		pro: 'yes',
		new: true,
	},
	{
		title: __( 'Automatic User Location Detection', 'location-weather' ),
		free: 'no',
		pro: 'yes',
	},
	{
		title: __(
			'Temperature Units (°C, °F, Auto, etc.)',
			'location-weather'
		),
		info: __(
			'Select your preferred temperature format: °Celsius, °Fahrenheit, or set Auto (location-based) to show weather data',
			'location-weather'
		),
		free: 2,
		pro: 5,
	},
	{
		title: __( 'Current Weather Data Display Options', 'location-weather' ),
		info: (
			<>
				{ __(
					'Customize which current weather data you want to show, including:'
				) }
				<ul>
					<li>
						{ __( 'Weather Condition Icon', 'location-weather' ) }
					</li>
					<li>{ __( 'Temperature', 'location-weather' ) }</li>
					<li>
						{ __( 'Low & High Temperature', 'location-weather' ) }
					</li>
					<li>{ __( 'Real Feel', 'location-weather' ) }</li>
					<li>{ __( 'Weather Description', 'location-weather' ) }</li>
				</ul>
			</>
		),
		free: 3,
		pro: 5,
	},
	{
		title: __(
			'Additional Weather Data Display Options',
			'location-weather'
		),
		info: (
			<>
				{ __(
					'Customize which additional weather data you want to show, including:'
				) }
				<ul>
					<li>{ __( 'Humidity', 'location-weather' ) }</li>
					<li>{ __( 'Pressure', 'location-weather' ) }</li>
					<li>{ __( 'Wind', 'location-weather' ) }</li>
					<li>{ __( 'Wind Gust', 'location-weather' ) }</li>
					<li>{ __( 'UV Index', 'location-weather' ) }</li>
					<li>{ __( 'Precipitation', 'location-weather' ) }</li>
					<li>{ __( 'Dew Point', 'location-weather' ) }</li>
					<li>{ __( 'Cloud Cover', 'location-weather' ) }</li>
					<li>{ __( 'Rain Chances', 'location-weather' ) }</li>
					<li>{ __( 'Snow', 'location-weather' ) }</li>
					<li>{ __( 'Visibility', 'location-weather' ) }</li>
					<li>{ __( 'Sunrise & Sunset', 'location-weather' ) }</li>
					<li>{ __( 'Air Quality', 'location-weather' ) }</li>
					<li>{ __( 'Moonrise & Moonset', 'location-weather' ) }</li>
					<li>{ __( 'Moon Phase', 'location-weather' ) }</li>
				</ul>
			</>
		),
		free: 7,
		pro: 15,
	},
	{
		title: __( 'Real-Time National Weather Alerts', 'location-weather' ),
		free: 'no',
		pro: 'yes',
	},
	{
		title: __( 'Show UV Index', 'location-weather' ),
		free: 'no',
		pro: 'yes',
	},
	{
		title: __(
			'Air Quality (AQI) Line and Bar Graph Chart',
			'location-weather'
		),
		free: 'no',
		pro: 'yes',
		new: true,
		hot: true,
	},
	{
		title: __(
			'4 Years of Air Quality (AQI) Historical Data',
			'location-weather'
		),
		free: 'no',
		pro: 'yes',
		new: true,
	},
	{
		title: __( 'Air Quality (AQI) Forecast Data', 'location-weather' ),
		free: 'no',
		pro: 'yes',
		new: true,
	},
	{
		title: __(
			'Air Quality (AQI) Forecast Style Presets',
			'location-weather'
		),
		info: __(
			'Choose how your Air Quality Forecast appears with three preset styles: List for a simple vertical view, Graph for visual trend analysis, and Carousel for an interactive, swipe-friendly layout.',
			'location-weather'
		),
		free: 'no',
		pro: 'yes',
		new: true,
	},
	{
		title: __( 'Detailed Weather Forecast', 'location-weather' ),
		info: __(
			'Show your visitors a rich, detailed weather forecast with all the essential insights they need. Ideal for planning ahead with confidence.',
			'location-weather'
		),
		free: 'no',
		pro: 'yes',
	},
	{
		title: __( 'AQI - Detailed Air Quality', 'location-weather' ),
		info: __(
			'Get a complete breakdown of air quality with real-time AQI levels and pollutant details. This block helps users understand environmental conditions at a glance.',
			'location-weather'
		),
		free: 'no',
		pro: 'yes',
		new: true,
	},
	{
		title: __( 'Weather Accordion', 'location-weather' ),
		info: __(
			'Display weather information in a compact, collapsible accordion layout for easy navigation. Perfect for keeping your layout clean while offering rich information.',
			'location-weather'
		),
		free: 'no',
		pro: 'yes',
	},
	{
		title: __( 'Weather Map by OpenWeatherMap', 'location-weather' ),
		info: __(
			'Get real-time weather updates for any location with the Weather Map by OpenWeather. Easily visualize temperature, precipitation, and other weather conditions on an interactive map.',
			'location-weather'
		),
		free: 'no',
		pro: 'yes',
	},
	{
		title: __( 'Historical Weather Data', 'location-weather' ),
		info: __(
			'View past weather records with accurate historical temperature, precipitation, and condition data. Ideal for analyzing trends or reviewing previous climate patterns.',
			'location-weather'
		),
		free: 'no',
		pro: 'yes',
		new: true,
		hot: true,
	},
	{
		title: __( 'Historical Air Quality Data', 'location-weather' ),
		info: __(
			'Access past air quality information for any location with Historical Air Quality Data. Track pollutants and trends over time to understand environmental changes.',
			'location-weather'
		),
		free: 'no',
		pro: 'yes',
		new: true,
		hot: true,
	},
	{
		title: __(
			'Sun & Moon Times - Full Astronomy Data',
			'location-weather'
		),
		info: __(
			'Get accurate sunrise, sunset, moonrise, and moonset times for any location with Sun & Moon Times. Plan your day or night activities with precise celestial information.',
			'location-weather'
		),
		free: 'no',
		pro: 'yes',
		new: true,
		hot: true,
	},
	{
		title: __(
			'2 Weather API Integration (OpenWeather + WeatherAPI)',
			'location-weather'
		),
		free: 'yes',
		pro: 'yes',
	},
	{
		title: __( 'Multisite Compatible', 'location-weather' ),
		free: 'yes',
		pro: 'yes',
	},
	{
		title: __( 'Priority Top-notch Support', 'location-weather' ),
		free: 'no',
		pro: 'yes',
	},
];

const testimonials = [
	{
		text: __(
			'The free trial worked great upon testing, but needed the advanced features and upgraded. At first the advanced features (i.e. auto location weather and multiple day forecast) did not work as advertised....',
			'location-weather'
		),
		user: 'wordpress',
		name: 'Dawie Hanekom',
		role: 'Managing Director, Newbe Marketing',
		img: '/assets/images/Dawie-Hanekom-min.png',
	},
	{
		text: __(
			'I must take a moment to emphasize just how exceptional the support for the product has been. While the product itself is fantastic, it’s the support that truly sets it apart. Every time I reached out with a question or concern...',
			'location-weather'
		),
		user: 'wordpress',
		name: 'Mike Cruywagen',
		role: 'Founder & owner at Nudge Studio',
		img: '/assets/images/mike-cruywagen.png',
	},
	{
		text: __(
			'I am very pleased with ShapedPlugin\'s Location Weather plugin. It does what it promises, and it is frequently updated and improved. It is an excellent product.Whenever I have contacted support the company responds quickly...',
			'location-weather'
		),
		user: 'trustpilot',
		name: 'Anton',
		role: 'Entrepreneur from Netherland',
		img: '/assets/images/swan.svg',
	},
];

const TrustpilotIcon = () => (
	<svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="m10.5 15.252 4.563-1.164L16.969 20zM21 7.61h-8.031L10.5 0 8.031 7.61H0l6.5 4.717-2.469 7.61 6.5-4.717 4-2.893z" fill="#00b57a"/></svg>
);

const generateFreeOrProContent = ( content ) => {
	if ( typeof content === 'number' ) {
		return <b>{ content }</b>;
	} else {
		if ( content === 'yes' ) {
			return <i className="dashicons dashicons-saved"></i>;
		} else if ( content === 'no' ) {
			return <i className="dashicons dashicons-no-alt"></i>;
		} else {
			return content;
		}
	}
};

const LiteVsPro = () => {
	return (
		<section className="splw-lite-pro-page" id="lite-pro-tab">
			<div className="splw-lite-pro-table">
				<div className="splw-lite-pro-header">
					<div>
						<h2 className="splw-section-title">
							{ __(
								'Unlock More with Location Weather Pro',
								'location-weather'
							) }
						</h2>
						<span className="splw-lite-pro-subtitle">
							{ __(
								'Unlock Pro features and give your visitors a more accurate weather experience',
								'location-weather'
							) }
						</span>
					</div>
					<a
						target="_blank"
						href="https://locationweather.io/pricing/?ref=1"
						className="splw-upgrade-to-pro-btn"
					>
						{ __( 'Upgrade to Pro Now!', 'location-weather' ) }
					</a>
				</div>
				<div className="splw-lite-pro-table-list">
					<ul>
						<li className="splw-lite-pro-table-row splw-header splw-header-bg">
							<span className="splw-title">
								{ __( 'FEATURES', 'location-weather' ) }
							</span>
							<span className="splw-free">
								{ __( 'LITE', 'location-weather' ) }
							</span>
							<span className="splw-pro splw-pro-icon">
								<ProIconLight />
								{ __( 'PRO', 'location-weather' ) }
							</span>
						</li>
						{ features.map( ( item, index ) => (
							<li
								className="splw-lite-pro-table-row"
								key={ index }
							>
								<span className="splw-title">
									{ item?.title }
									{ item?.info && (
										<InfoText text={ item?.info } />
									) }
									{ item?.video && (
										<span className="splw-settings-info">
											<VideoTooltipIcon color="#757575" />
											<span className="splw-settings-info-text">
												<video
													src={ item?.video }
													autoPlay
													loop
													muted
												/>
											</span>
										</span>
									) }
									{ item?.new && (
										<span className="splw-new">
											{ __( 'new', 'location-weather' ) }
										</span>
									) }
									{ item?.hot && (
										<span className="splw-hot">
											{ __( 'hot', 'location-weather' ) }
										</span>
									) }
								</span>
								<span className="splw-free">
									{ generateFreeOrProContent( item?.free ) }
								</span>
								<span className="splw-pro">
									{ generateFreeOrProContent( item?.pro ) }
								</span>
							</li>
						) ) }
					</ul>
				</div>
			</div>
			<div className="splw-upgrade-to-pro-promotion">
				<h2 className="splw-section-title">
					{ __(
						'Ready to Take Your Weather Display to the Next Level?',
						'location-weather'
					) }
				</h2>
				<span className="splw-section-subtitle">
					{ __( 'Join ', 'location-weather' ) }
					<b>{ __( '20,000+', 'location-weather' ) }</b>
					{ __(
						' website owners who trust Location Weather Pro for stunning, fully customized weather and air quality index—AQI displays.',
						'location-weather'
					) }
				</span>
				<div className="splw-upgrade-to-pro-btn-wrapper">
					<a
						target="_blank"
						href="https://locationweather.io/pricing/?ref=1"
						className="splw-upgrade-to-pro-btn"
					>
						{ __( 'Upgrade to Pro Now!', 'location-weather' ) }
					</a>
					<a
						target="_blank"
						href="https://locationweather.io/"
						className="splw-upgrade-to-pro-btn"
					>
						{ __( 'See All Features', 'location-weather' ) }
					</a>
					{/* <a
						target="_blank"
						className="splw-upgrade-to-pro-btn"
						href="https://locationweather.io/demos/vertical-card/"
					>
						{ __( 'Pro Live Demo', 'location-weather' ) }
					</a> */}
				</div>
			</div>


				<div className="splw-testimonial">
					<div className="splw-testimonial-header">
						<div className="splw-testimonial-ratings">
							<a
							href='https://wordpress.org/support/plugin/location-weather/reviews/'
							target='_blank'
							className="splw-testimonial-rating-item">
								<div className="splw-testimonial-rating-wordpress">
									<div className="splw-wp-mark">
										<WordPressIcon />
									</div>
										<div className="splw-wp-text">
											<StarIcon />
											<StarIcon />
											<StarIcon />
											<StarIcon />
											<HalfStarIcon />
										</div>
								</div>
								<span className="splw-testimonial-rating-score">4.5</span>
								<span className="splw-testimonial-review-count">100+ Reviews</span>
							</a>
							<a
							href='https://www.trustpilot.com/review/shapedplugin.com'
							target='_blank'
							className="splw-testimonial-rating-item">
								<div className="splw-testimonial-rating-trustpilot">
									<div className="splw-trustpilot-mark">
										<svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="m10.5 15.252 4.563-1.164L16.969 20zM21 7.61h-8.031L10.5 0 8.031 7.61H0l6.5 4.717-2.469 7.61 6.5-4.717 4-2.893z" fill="#00b57a"/></svg>
									</div>
								</div>
								<div className="splw-trustpilot-stars">
									<StarIcon color='#fff' />
									<StarIcon color='#fff' />
									<StarIcon color='#fff' />
									<StarIcon color='#fff' />
									<StarIcon color='#fff' />
								</div>
								<span className="splw-testimonial-rating-score">4.9</span>
								<span className="splw-testimonial-review-count">119+ Reviews</span>
							</a>
						</div>
						<h2 className="splw-testimonial-title">
							{ __(
								'Don\'t Just Take Our Word for It — See What Users Say!',
								'location-weather'
							) }
						</h2>
					</div>
					<div className="splw-testimonial-wrap">
						{ testimonials?.map( ( item, index ) => (
							<div className={ `splw-testimonial-card ${ item?.user === 'trustpilot' ? 'splw-testimonial-card-trustpilot' : '' }` } key={ index }>
								<div className="splw-testimonial-card-header">
									<div className="splw-testimonial-reviewer">
										<div className="splw-testimonial-avatar">
											{ item?.user === 'trustpilot' ? (
												<div className="splw-testimonial-avatar-initials">
													<span>{ item.name.slice( 0, 2 ).toUpperCase() }</span>
												</div>
											) : (
												<img
													src={ `${ splw_admin_settings_localize?.pluginUrl }${ item?.img }` }
													alt={ item?.name }
												/>
											) }
											<div className="splw-testimonial-source-badge">
												{ item?.user === 'trustpilot' ? (
													<TrustpilotIcon />
												) : (
													<WordPressIcon />
												) }
											</div>
										</div>
										<div className="splw-testimonial-reviewer-info">
											<h3>{ item.name }</h3>
											<p>{ item.role }</p>
										</div>
									</div>
								</div>
								<div className="splw-testimonial-rating-stars">
									{ item?.user === 'trustpilot' ? (
										<div className="splw-testimonial-rating-trustpilot-stars">
											<span className="splw-trustpilot-star-item"><StarIcon color='#fff' /></span>
											<span className="splw-trustpilot-star-item"><StarIcon color='#fff' /></span>
											<span className="splw-trustpilot-star-item"><StarIcon color='#fff' /></span>
											<span className="splw-trustpilot-star-item"><StarIcon color='#fff' /></span>
											<span className="splw-trustpilot-star-item"><StarIcon color='#fff' /></span>
										</div>
									) : (
										<span>★★★★★</span>
									) }
								</div>
								<div className="splw-testimonial-card-content">
									<p>{ item?.text }</p>
								</div>
							</div>
						) ) }
					</div>
				</div>
			</section>
		);
	};

	export default LiteVsPro;