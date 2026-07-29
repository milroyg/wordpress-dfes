import { Arrow, ArrowRight, DocIcon, PlayIcon, ProIconLight, SupportIcon, TeamIcon } from '../../icons';
import {
	AiWeatherIcon,
	BlocksIcon,
	PatternsIcon,
	MapIcon,
	AqiIcon,
	ChartIcon,
	HistoryIcon,
	SunMoonIcon,
	GlobalSearchIcon,
	IconPacksIcon,
} from './icons';
import { __ } from '@wordpress/i18n';
import { useState, useEffect, useMemo } from '@wordpress/element';
import ToggleCard from '../../dashboard-parts/toggleCard';

// Blocks to display in quick start grid (10 blocks)
const quickStartBlocks = [
	'sp-location-weather-pro/vertical-card',
	'sp-location-weather-pro/horizontal',
	'sp-location-weather-pro/combined',
	'sp-location-weather-pro/aqi-minimal',
	'sp-location-weather-pro/aqi-detailed',
	'sp-location-weather-pro/grid',
	'sp-location-weather-pro/table',
	'sp-location-weather-pro/map',
	'sp-location-weather-pro/sun-moon',
	'sp-location-weather-pro/historical-weather',
];

// Pro features list
const proFeatures = [
	{
		url: 'https://locationweather.io/ai-weather-assistant/',
		icon: <AiWeatherIcon />,
		iconClass: 'splwb-qs-pro-icon-blue',
		label: __( 'AI Weather Assistant', 'location-weather' ),
		hot: true,
	},
	{
		url: 'https://locationweather.io/blocks/',
		icon: <BlocksIcon />,
		iconClass: 'splwb-qs-pro-icon-green',
		label: __( '15+ Weather & AQI Gutenberg Blocks', 'location-weather' ),
	},
	{
		url: 'https://locationweather.io/patterns/',
		icon: <PatternsIcon />,
		iconClass: 'splwb-qs-pro-icon-pink',
		label: __( '200+ Ready Weather Patterns', 'location-weather' ),
	},
	{
		url: 'https://locationweather.io/visualize-weather-conditions-with-interactive-maps/',
		icon: <MapIcon />,
		iconClass: 'splwb-qs-pro-icon-cyan',
		label: __( 'Interactive Live Weather Map', 'location-weather' ),
	},
	{
		url: 'https://locationweather.io/display-air-quality-details-for-safe-comfortable-planning/',
		icon: <AqiIcon />,
		iconClass: 'splwb-qs-pro-icon-red',
		label: __( 'Air Quality (AQI)', 'location-weather' ),
		hot: true,
	},
	{
		url: 'https://locationweather.io/#interactive-graph-chart',
		icon: <ChartIcon />,
		iconClass: 'splwb-qs-pro-icon-lime',
		label: __( 'Weather & AQI Graph Charts', 'location-weather' ),
	},
	{
		url: 'https://locationweather.io/display-reliable-weather-history-for-46-years/',
		icon: <HistoryIcon />,
		iconClass: 'splwb-qs-pro-icon-purple',
		label: __( '46-Year Historical Weather Data', 'location-weather' ),
	},
	{
		url: 'https://locationweather.io/display-sunrise-sunset-and-moon-phase-data/',
		icon: <SunMoonIcon />,
		iconClass: 'splwb-qs-pro-icon-yellow',
		label: __( 'Astronomy Data & Sun & Moon', 'location-weather' ),
		hot: true,
	},
	{
		url: 'https://locationweather.io/let-visitors-search-the-weather-for-any-location-worldwide/',
		icon: <GlobalSearchIcon />,
		iconClass: 'splwb-qs-pro-icon-green-light',
		label: __( 'Global Weather Search', 'location-weather' ),
	},
	{
		url: 'https://locationweather.io/12-weather-icon-packs/',
		icon: <IconPacksIcon />,
		iconClass: 'splwb-qs-pro-icon-orange',
		label: __( '12+ Weather Icon Packs', 'location-weather' ),
	},
];

// Helper: Capitalize first letter
const capitalize = ( str ) => str.charAt( 0 ).toUpperCase() + str.slice( 1 );

const QuickStart = ( { blockSettings, blockShowHideHandler, setPageAndHash } ) => {
	const [ isVideoModalOpen, setIsVideoModalOpen ] = useState( false );
	const [ userName, setUserName ] = useState( '' );

	// Memoized greeting based on time of day
	const greeting = useMemo( () => {
		const hour = new Date().getHours();
		if ( hour < 12 ) return __( 'Good morning', 'location-weather' );
		if ( hour < 17 ) return __( 'Good afternoon', 'location-weather' );
		if ( hour < 21 ) return __( 'Good evening', 'location-weather' );
		return __( 'Good night', 'location-weather' );
	}, [] );

	// Fetch user name on mount
	useEffect( () => {
		const getUserName = () => {
			// Priority: Localized data
			if ( splw_admin_settings_localize?.current_user ) {
				return capitalize( splw_admin_settings_localize.current_user );
			}

			// Fallback: wp.data store
			try {
				const store = wp.data?.select( 'core' );
				if ( store ) {
					const user = store.getCurrentUser?.() || store.currentUser;
					return user?.display_name || user?.name || '';
				}
			} catch {
				// Silent fail
			}

			return '';
		};

		const name = getUserName();
		if ( name ) {
			setUserName( `, ${ name }` );
		}
	}, [] );

	const handleViewMoreBlocks = () => {
		setPageAndHash( 'blocks' );
	};

	return (
		<div className="splwb-settings-getting-start-page">
			<div className="splwb-settings-getting-start-page-content">
				{/* Left Side */}
				<div className="splwb-qs-left">
					{/* About Section */}
					<div className="splwb-qs-about-section">
						<div className="splwb-qs-about-content">
							<div className="splwb-qs-about-text">
								<p className="splwb-qs-greeting">
									{ greeting + userName }
								</p>
								<h3 className="splwb-qs-welcome-title">
									{ __( 'Welcome to Location Weather!', 'location-weather' ) }
								</h3>
								<p className="splwb-qs-welcome-desc">
									{ __(
										'Thank you for installing Location Weather! This video will help you get started with the plugin. Enjoy!',
										'location-weather'
									) }
								</p>
								<a
									href={ `${ splw_admin_settings_localize?.homeUrl }wp-admin/post-new.php?post_type=spl_weather_template&splwblock_inserter` }
									className="splwb-qs-create-btn"
								>
									<i className="dashicons dashicons-plus-alt2"></i>
									{ __( 'Add Your First Weather', 'location-weather' ) }
								</a>
							</div>
							<div className="splwb-qs-video-wrapper">
								<img
									src={`${ splw_admin_settings_localize?.pluginUrl }/assets/images/video-overlay.png`}
									alt={ __( 'Video Tutorial', 'location-weather' ) }
									className="splwb-qs-video-placeholder"
								/>
								<button
									className="splwb-qs-play-btn"
									onClick={ () => setIsVideoModalOpen( true ) }
								>
									<PlayIcon color={"#F26C0D"} />
								</button>
							</div>
						</div>
					</div>

					{/* Weather & AQI Blocks Section */}
					<div className="splwb-qs-blocks-section">
						<div className="splwb-qs-section-header">
							<h3 className="splwb-qs-section-title">
								{ __( 'Weather & AQI Blocks', 'location-weather' ) }
							</h3>
							<button
								className="splwb-qs-view-more-btn"
								onClick={ handleViewMoreBlocks }
							>
								{ __( 'View All Blocks', 'location-weather' ) }
								<ArrowRight />
							</button>
						</div>
						<div className="splwb-qs-blocks-grid spl-weather-blocks-settings-card-wrapper">
							{ quickStartBlocks.map( ( blockName ) => {
								const blockSetting = blockSettings?.find( ( b ) => b.name === blockName );
								const attributes = {
									name: blockName,
									show: blockSetting ? blockSetting.show : true,
								};
								return (
									<ToggleCard
										key={ blockName }
										attributes={ attributes }
										onlyLiveDemo={true}
										blockShowHideHandler={ blockShowHideHandler }
									/>
								);
							} ) }
						</div>
						<div className="splwb-qs-blocks-gradient"></div>
					</div>

					{/* Video Tutorials Section */}
					<div className="splwb-qs-tutorials-section">
						<div className="splwb-qs-section-header">
							<h3 className="splwb-qs-section-title">
								{ __( 'Video Tutorials', 'location-weather' ) }
							</h3>
							<a
								href="https://www.youtube.com/watch?v=vLdIHhP4v-I&list=PLoUb-7uG-5jP_5pNrdBCKxgPrCp_rS89G&index=1"
								target="_blank"
								className="splwb-qs-view-more-btn"
							>
								{ __( 'View More Tutorials', 'location-weather' ) }
								<ArrowRight />
							</a>
						</div>
						<div className="splwb-qs-tutorials-grid">
							<div className="splwb-qs-tutorial-card">
								<div className="splwb-qs-tutorial-video">
									<iframe width="560" height="315" src="https://www.youtube.com/embed/cMNJnJ3d4Zk?si=ty1Q76VBL8F3xcX8" title="YouTube video player" frameBorder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerPolicy="strict-origin-when-cross-origin" allowFullScreen></iframe>
								</div>
								<h4 className="splwb-qs-tutorial-title">
									{ __(
										'How to Use Location Weather Blocks in Elementor',
										'location-weather'
									) }
								</h4>
							</div>
							<div className="splwb-qs-tutorial-card">
								<div className="splwb-qs-tutorial-video">
									<iframe
										width="560"
										height="315"
										src="https://www.youtube.com/embed/XMCBVk_ADfs?si=K4cBOmPdPE8KMOBd"
										title="YouTube video player"
										frameBorder="0"
										allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
										referrerPolicy="strict-origin-when-cross-origin"
										allowFullScreen
									></iframe>
								</div>
								<h4 className="splwb-qs-tutorial-title">
									{ __(
										'How to Integrate Weather API with Location Weather Plugin',
										'location-weather'
									) }
								</h4>
							</div>
						</div>
					</div>
				</div>

				{/* Right Side - Sidebar */}
				<div className="splwb-qs-sidebar">
					{/* Weather Patterns Library Card */}
					<div className="splwb-qs-patterns-card">
						<div className="splwb-qs-patterns-image">
							<div className="splwb-qs-patterns-bg"></div>
							<img
								src={`${ splw_admin_settings_localize?.pluginUrl }/assets/images/patterns-card.png`}
								alt={ __( 'Weather Patterns', 'location-weather' ) }
								className="splwb-qs-patterns-cards"
							/>
							<span className="splwb-qs-hot-tag">{ __( 'Hot', 'location-weather' ) }</span>
							<div className="splwb-qs-patterns-gradient">
								<span className="splwb-qs-patterns-text">
									{ __( '200+ Weather patterns Library', 'location-weather' ) }
								</span>
							</div>
						</div>
						<div className="splwb-qs-patterns-content">
							<h4 className="splwb-qs-patterns-title">
								{ __(
									'Ready Weather Patterns Made Easy',
									'location-weather'
								) }
							</h4>
							<p className="splwb-qs-patterns-desc">
								{ __(
									'Choose from beautifully crafted weather layouts and launch stunning forecasts in seconds.',
									'location-weather'
								) }
							</p>
							<div className="splwb-qs-patterns-btn-wrapper">
								<a
									href={ `${ splw_admin_settings_localize?.homeUrl }wp-admin/post-new.php?post_type=spl_weather_template&splw_pattern_library` }
									className="splwb-qs-patterns-btn"
								>
									{ __( 'Start with Ready Patterns', 'location-weather' ) }
								</a>
							</div>
						</div>
					</div>

					{/* Go Pro Card */}
					<div className="splwb-qs-pro-card">
						<div className="splwb-qs-pro-content">
							<div className="splwb-qs-pro-header">
								<h4 className="splwb-qs-pro-title">
									{ __( 'Go Pro & Unlock More! 🚀', 'location-weather' ) }
								</h4>
								<p className="splwb-qs-pro-desc">
									{ __(
										'Unlock the full potential of Location Weather to create and manage weather.',
										'location-weather'
									) }
								</p>
							</div>
							<div className="splwb-qs-pro-features">
								{ proFeatures.map( ( feature, index ) => (
									<a key={ index } href={ feature.url } target="_blank" className="splwb-qs-pro-feature">
										<span className={ `splwb-qs-pro-icon ${ feature.iconClass }` }>
											{ feature.icon }
										</span>
										<span>{ feature.label }{ feature.hot && " 🔥" }</span>
										<ArrowRight className="splwb-qs-pro-feature-arrow" />
									</a>
								) ) }
							</div>
							<div className="splwb-qs-pro-buttons">
								<a
									href="https://locationweather.io/pricing/?ref=1"
									target="_blank"
									className="splwb-qs-pro-upgrade-btn"
								>
									<ProIconLight />
									{ __( 'Upgrade to Pro', 'location-weather' ) }
								</a>
								<button
									onClick={ () => { setPageAndHash( 'lite_vs_pro' ); setTimeout( () => window.scrollTo( 0, 0 ), 100 ); } }
									className="splwb-qs-pro-compare-btn"
								>
									{ __( 'Lite vs Pro', 'location-weather' ) }
								</button>
							</div>
						</div>
					</div>

					{/* Documentation Card */}
					<div className="splwb-qs-info-card">
						<div className="splwb-qs-info-header">
							<div className="splwb-qs-info-icon">
								<DocIcon />
							</div>
							<h4 className="splwb-qs-info-title">
								{ __( 'Documentation', 'location-weather' ) }
							</h4>
						</div>
						<div className="splwb-qs-info-content-wrapper">
							<p className="splwb-qs-info-desc">
								{ __(
									'Explore Location Weather plugin capabilities in our enriched documentation.',
									'location-weather'
								) }
							</p>
							<div className="splwb-qs-info-link-wrapper">
								<a
									href="https://locationweather.io/docs/"
									target="_blank"
									className="splwb-qs-info-link"
								>
									{ __( 'Browse Now', 'location-weather' ) }
									<ArrowRight />
								</a>
							</div>
						</div>
					</div>

					{/* Community Card */}
					<div className="splwb-qs-info-card">
						<div className="splwb-qs-info-header">
							<div className="splwb-qs-info-icon">
								<TeamIcon />
							</div>
							<h4 className="splwb-qs-info-title">
								{ __( 'Join The Community', 'location-weather' ) }
							</h4>
						</div>
						<div className="splwb-qs-info-content-wrapper">
							<p className="splwb-qs-info-desc">
								{ __(
									'Join the official ShapedPlugin Community to share your experiences, thoughts, and ideas.',
									'location-weather'
								) }
							</p>
							<div className="splwb-qs-info-link-wrapper">
								<a
									href="https://community.shapedplugin.com/portal/space/locationweather/home"
									target="_blank"
									className="splwb-qs-info-link"
								>
									{ __( 'Join Community', 'location-weather' ) }
									<ArrowRight />
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>

			{/* Video Modal */}
			{ isVideoModalOpen && (
				<div className="splwb-qs-video-modal" onClick={ () => setIsVideoModalOpen( false ) }>
					<div className="splwb-qs-video-modal-content" onClick={ ( e ) => e.stopPropagation() }>
						<button
							className="splwb-qs-video-modal-close"
							onClick={ () => setIsVideoModalOpen( false ) }
						>
							×
						</button>
						<div className="splwb-qs-video-modal-wrapper">
							<iframe
								width="100%"
								height="100%"
								src="https://www.youtube.com/embed/videoseries?list=PLoUb-7uG-5jP_5pNrdBCKxgPrCp_rS89G&autoplay=1"
								title="YouTube video player"
								frameBorder="0"
								allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
								allowFullScreen
							></iframe>
						</div>
					</div>
				</div>
			) }
		</div>
	);
};

export default QuickStart;
