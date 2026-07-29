import { __ } from '@wordpress/i18n';
import Drawer from '@mui/material/Drawer';
import { useState } from '@wordpress/element';
import {
	Arrow,
	ArrowRight,
	Blog,
	ChangelogIcon,
	CheckCircleGreen,
	CloseIcon,
	Community,
	DocsStroked,
	FeatRequest,
	GetHelp,
	LWLogo,
	RightArrow,
	RightArrowLong,
	Roadmap,
	RotateIcon,
	SetupWizard,
	TechSupport,
	Video,
	WhatsNew,
} from '../icons';
import useChangelogData from '../hooks/useChangelogData';

const GreenCheckIcon = () => (
	<img
		src={ `${ splw_admin_settings_localize?.pluginUrl }/assets/images/bell-icon.gif` }
		width={18}
		height={18}
		alt=""
	/>
);

const GetHelpItems = [
	{
		title: __( 'Documentation', 'location-weather' ),
		Icon: DocsStroked,
		link: 'https://locationweather.io/docs/',
	},
	{
		title: __( 'Technical Support', 'location-weather' ),
		Icon: TechSupport,
		link: 'https://shapedplugin.com/create-new-ticket/',
	},
	{
		title: __( 'Setup Wizard', 'location-weather' ),
		Icon: SetupWizard,
		link: `${ splw_admin_settings_localize?.homeUrl }wp-admin/admin.php?page=splw_admin_dashboard#setupwizard`,
	},
	{
		title: __( 'Public Roadmap', 'location-weather' ),
		Icon: Roadmap,
		link: 'https://community.shapedplugin.com/roadmap/location-weather/',
	},
	{
		title: __( 'Request a Feature', 'location-weather' ),
		Icon: FeatRequest,
		link: 'https://community.shapedplugin.com/portal/space/locationweather/home?topic=feature-request',
	},
	{
		title: __( 'Video Tutorials', 'location-weather' ),
		Icon: Video,

		link: 'https://www.youtube.com/watch?v=lio26LDl5Sc&list=PLoUb-7uG-5jP_5pNrdBCKxgPrCp_rS89G',
	},
	{
		title: __( "What's New", 'location-weather' ),
		Icon: WhatsNew,

		link: 'https://wordpress.org/plugins/location-weather/#developers',
	},
	{
		title: __( 'Blog: Latest News', 'location-weather' ),
		Icon: Blog,

		link: 'https://locationweather.io/blog/',
	},
	{
		title: __( 'Join Community', 'location-weather' ),
		Icon: Community,

		link: 'https://community.shapedplugin.com/portal/space/locationweather/home',
	},
];

const HeaderItems = () => {
	const [ showSidebar, setShowSidebar ] = useState( false );

	const toggleDrawer = ( newOpen ) => () => {
		setShowSidebar( newOpen );
	};
	const changelog = useChangelogData( showSidebar );

	return (
		<>
			<div className="splw-green-header-notice">
				<div className="splw-green-header-notice-content">
					<GreenCheckIcon />
					<span className="splw-green-header-notice-text">
						<strong>You're on Lite</strong> — unlock the full power at <strong>50% OFF. Lifetime Deal.</strong> Pay once, Use forever.
					</span>
					<a
						className="splw-green-header-notice-link"
						href="https://locationweather.io/pricing/?ref=1"
						target="_blank"
						rel="noopener noreferrer"
					>
						{ __( 'Upgrade to Pro', 'location-weather' ) }
						<ArrowRight />
					</a>
				</div>
			</div>
			<div className="spl-weather-admin-page-header">
			<div className="spl-weather-block-setting-header-wrapper">
				<div className="spl-weather-admin-page-header-left">
					<LWLogo />
					<span
						onClick={ toggleDrawer( true ) }
					><RotateIcon />{ splw_admin_settings_localize?.pluginVersion }</span>
					{/* <button
						className="spl-weather-header-changelog-btn"
						onClick={ toggleDrawer( true ) }
					>
						<ChangelogIcon />
					</button> */}
					<Drawer
						anchor="right"
						open={ showSidebar }
						onClose={ toggleDrawer( false ) }
						slotProps={ {
							paper: {
								className:
									'spl-weather-admin-changelog-wrapper',
							},
						} }
					>
						<div className="spl-weather-changelog-heading">
							<p className="spl-weather-changelog-heading-title">
								{ __(
									'Latest Updates - Changelog',
									'location-weather'
								) }
							</p>
							<button
								className="spl-weather-changelog-close-btn"
								onClick={ toggleDrawer( false ) }
							>
								<CloseIcon />
							</button>
						</div>
						<div
							className="spl-weather-changelog-details"
							dangerouslySetInnerHTML={ { __html: changelog } }
						></div>
					</Drawer>
				</div>
				<button className="spl-weather-admin-page-header-right">
					<GetHelp />
					<span>{ __( 'Get Help', 'location-weather' ) }</span>
					<div className="splw-help-drop-down">
						{ GetHelpItems?.map(
							( { title, link, Icon }, index ) => (
								<a
									key={ index }
									href={ link }
									target="_blank"
									rel="noopener noreferrer"
								>
									<Icon />
									<span>{ title }</span>
									<span className="drop-down-arrow">
										<Arrow />
									</span>
								</a>
							)
						) }
					</div>
				</button>
			</div>
		</div>
		</>
	);
};

export default HeaderItems;
