import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';

const WelcomePage = () => {
	const [ showVideo, setShowVideo ] = useState( false );
	const featureLists = [
		{
			title: __( 'Real-Time Weather', 'location-weather' ),
			icon: 'realtime-weather',
		},
		{
			title: __( 'Weather Map', 'location-weather' ),
			icon: 'weather-map',
		},
		{
			title: __( 'Weather Forecasts', 'location-weather' ),
			icon: 'weather-forecast',
		},
		{
			title: __( 'Astronomy Data', 'location-weather' ),
			icon: 'astronomy',
		},
		{
			title: __( 'Air Quality Insights', 'location-weather' ),
			icon: 'aqi-insights',
		},
		{
			title: __( 'Weather Search', 'location-weather' ),
			icon: 'weather-search',
		},
		{
			title: __( 'Historical Data (Time Machine)', 'location-weather' ),
			icon: 'historical-data',
		},
		{
			title: __( 'AI Weather Assistant', 'location-weather' ),
			icon: 'ai-assistant',
		},
	];
	return (
		<div className="splw-setup-welcome-page">
			<div className="splw-setup-welcome-page-left">
				<h3 className="splw-setup-page-title">
					{ __( 'Welcome to ', 'location-weather' ) }
					<span className="splw-logo-title">Location Weather!</span>
				</h3>
				<p className="splw-setup-page-desc">
					{ __(
						'Thanks for installing Location Weather — your all-in-one solution for displaying beautiful, accurate ',
						'location-weather'
					) }
					<b>{ __( 'Weather Forecast ', 'location-weather' ) }</b>
					{ __( 'and ', 'location-weather' ) }{ ' ' }
					<b>{ __( 'Air Quality', 'location-weather' ) }</b>
					{ __(
						' data on your WordPress site.',
						'location-weather'
					) }
				</p>
				<p className="splw-setup-page-desc">
					{ __(
						'Set up quickly and start displaying real-time weather with ',
						'location-weather'
					) }
					<b>{ __( '40+ Layouts and 200+', 'location-weather' ) }</b>{ ' ' }
					{ __(
						'Weather Patterns. Packed with features, including:',
						'location-weather'
					) }
				</p>
				<div className="splw-setup-feature-lists">
					{ featureLists?.map( ( { title, icon }, index ) => (
						<div key={ index } className="splw-setup-feature-list">
							<span className="splw-setup-feature-img">
								<img
									src={ `${ splw_admin_settings_localize?.pluginUrl }/includes/Admin/framework/assets/images/setup-wizard/${ icon }.svg` }
								/>
							</span>
							<span className="splw-setup-feature-title">
								{ title }
							</span>
							{ index === featureLists.length - 1 && (
								<span className="splw-setup-feature-hot">
									{ __( 'Hot', 'location-weather' ) }
								</span>
							) }
						</div>
					) ) }
				</div>
			</div>
			<div
				className="splw-setup-welcome-page-right"
				style={ {
					backgroundImage: `url(${ splw_admin_settings_localize?.pluginUrl }/includes/Admin/framework/assets/images/setup-wizard/getting-started-thumb.png)`,
				} }
			>
				{ showVideo ? (
					<iframe
						width="510"
						height="410"
						src="https://www.youtube.com/embed/vLdIHhP4v-I?si=zkLvmuhqMjxE4XTx&autoplay=1"
						title="YouTube video player"
						allow="autoplay; encrypted-media"
					></iframe>
				) : (
					<div className="splw-setup-video-overlay">
						<button
							id="splw-play-btn"
							className="splw-play-btn-sonar"
							onClick={ () => setShowVideo( true ) }
						>
							<img
								src={ `${ splw_admin_settings_localize?.pluginUrl }/includes/Admin/framework/assets/images/setup-wizard/video-play.svg` }
							/>
						</button>
					</div>
				) }
			</div>
		</div>
	);
};

export default WelcomePage;
