import { __ } from '@wordpress/i18n';
import { WeatherAPIKey } from '../../pages/settings/settings-tab-content';
import { useState } from '@wordpress/element';

const ApiIntegrations = ( {
	settingsOptions,
	setSettingsOptions,
	handleNext,
	nextBtnRef,
	setIsEmptyApiFields,
	errorMessage,
} ) => {
	const [ showVideo, setShowVideo ] = useState( false );
	const apiKeyInfos = [
		__( 'Required to Fetch Weather & AQI Data', 'location-weather' ),
		__( 'Each User Needs a Unique Key', 'location-weather' ),
		__( 'Avoid Call Limits & Data Restrictions', 'location-weather' ),
		__( 'Full Access to Your Chosen Plan', 'location-weather' ),
		__( 'Secure & Reliable Integration', 'location-weather' ),
	];
	return (
		<div className="splw-setup-api-integration-page">
			<div className="splw-setup-blocks-page-header">
				<h3 className="splw-setup-page-title">
					{ __( 'Weather API Key Integration', 'location-weather' ) }
				</h3>
				<p className="splw-setup-page-desc">
					{ __( 'Need help? Follow our ', 'location-weather' ) }
					<a
						href="https://locationweather.io/api-integration-guidelines/"
						target="_blank"
						rel="noreferrer"
					>
						{ __(
							'API Integration Guidelines',
							'location-weather'
						) }
					</a>
					{ __(
						' to generate and connect your API key.',
						'location-weather'
					) }
				</p>
			</div>
			<div className="splw-setup-api-integration-content">
				<div className="splw-settings-page-container">
					<WeatherAPIKey
						settingsOptions={ settingsOptions }
						setSettingsOptions={ setSettingsOptions }
						isTourGuide={ true }
						nextBtnRef={ nextBtnRef }
						setIsEmptyApiFields={ setIsEmptyApiFields }
					/>
					<button
						className="splw-setup-wizard-nav-btn prev-btn"
						onClick={ handleNext }
					>
						{ __( 'I will set later', 'location-weather' ) }
					</button>
					{ errorMessage && (
						<span className="splw-api-integration-notice">
							<i className="dashicons dashicons-info-outline"></i>
							{ __(
								'Please set your Weather API key to display weather smoothly.',
								'location-weather'
							) }
						</span>
					) }
				</div>
				<div className="splw-setup-api-integration-right">
					<h3 className="splw-setup-page-title">
						{ __(
							'Why You Must Use Your Own API Key',
							'location-weather'
						) }
					</h3>
					<div className="splw-setup-feature-lists">
						{ apiKeyInfos.map( ( title, index ) => (
							<div
								className="splw-setup-feature-list"
								key={ index }
							>
								<img
									src={ `${ splw_admin_settings_localize?.pluginUrl }/includes/Admin/framework/assets/images/setup-wizard/tick-overflow.svg` }
								/>
								<span className="splw-setup-feature-title">
									{ title }
								</span>
							</div>
						) ) }
					</div>
					<div
						className="splw-setup-api-integration-video"
						style={ {
							backgroundImage: `url(${ splw_admin_settings_localize?.pluginUrl }/includes/Admin/framework/assets/images/setup-wizard/api-guide-thumb.png)`,
						} }
					>
						{ showVideo ? (
							<iframe
								width="338"
								height="206"
								src="https://www.youtube.com/embed/pJzumrOLxSQ?si=TdisjIeGxnXX-CxN&autoplay=1"
								title="YouTube video player"
								allow="autoplay; encrypted-media"
							/>
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
			</div>
		</div>
	);
};

export default ApiIntegrations;
