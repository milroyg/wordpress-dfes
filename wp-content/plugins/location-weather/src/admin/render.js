import { __ } from '@wordpress/i18n';
import { useEffect, useState } from '@wordpress/element';
import { Toaster, toast } from 'react-hot-toast';
import Blocks from './pages/blocks';
import HeaderItems from './dashboard-parts/headerItems';
import QuickStart from './pages/quick-start';
import LiteVsPro from './pages/lite-vs-pro';
import useBlockOptions from './hooks/useBlockOptions';
import './style.scss';
import AboutUs from './pages/about-us';
import Settings from './pages/settings';
import Footer from './dashboard-parts/footer';
import SetupWizard from './setup-wizard';
import SavedTemplates from './pages/saved-templates';
import Integrations from './pages/integrations';
import useIntegrationsOptions from './hooks/useIntegrationsOptions';
import { OurPluginsIcon } from './icons';

// Function to update the active state of sidebar menu items.
const updateSidebarActive = ( pageName ) => {
	const postMenu = document.getElementById( 'menu-posts-location_weather' );
	if ( ! postMenu ) return;

	// Remove 'current' class from all items
	postMenu
		.querySelectorAll( 'li' )
		.forEach( ( el ) => el.classList.remove( 'current' ) );

	// Determine selector based on pageName
	const selector =
		pageName === 'getting-start' || pageName === ''
			? 'li a[href="edit.php?post_type=location_weather&page=splw_admin_dashboard"]'
			: `li a[href*="#${ pageName }"]`;

	// Add 'current' class if match found
	postMenu
		.querySelector( selector )
		?.closest( 'li' )
		?.classList.add( 'current' );
};

const Render = () => {
	const hashValue = window.location.hash.replace( '#', '' );
	const pluginsContainer = document.querySelector(
		'.splw-recommended-plugins-wrapper'
	);

	const [ page, setPage ] = useState(
		hashValue ? hashValue : 'getting-start'
	);

	useEffect( () => {
		if ( hashValue === 'our_plugins' && pluginsContainer ) {
			pluginsContainer.style.display = 'block';
		}
		const postMenu = document.getElementById(
			'menu-posts-location_weather'
		);
		const allItems = postMenu.querySelectorAll( 'li' );

		const removeCurrentClass = () => {
			allItems.forEach( ( element ) => {
				element.classList.remove( 'current' );
			} );
		};

		const postMenuAction = ( e ) => {
			const currentItem = e.target.closest( 'li' );
			removeCurrentClass();
			currentItem.classList.add( 'current' );

			setTimeout( () => {
				setPage( window.location.hash.replace( '#', '' ) );
			}, 0 );
		};
		postMenu.addEventListener( 'click', postMenuAction );

		return () => postMenu.removeEventListener( 'click', postMenuAction );
	}, [] );

	const setPageAndHash = ( pageName ) => {
		setPage( pageName );
		if ( pageName === 'our_plugins' ) {
			pluginsContainer.style.display = 'block';
		} else {
			pluginsContainer.style.display = 'none';
		}
		window.location.hash = pageName;
	};
	// Update Sidebar active menu based on hash link.
	updateSidebarActive( hashValue );

	// Fetch block settings data from API.
	const [ options, setOptions ] = useBlockOptions();

	// Fetch integration settings data from API.
	const [ integrationOptions, setIntegrationOptions ] = useIntegrationsOptions();

	const show_notification = ( block ) => {
		const message = block.show
			? __( 'Block disabled successfully', 'location-weather' )
			: __( 'Block enabled successfully', 'location-weather' );
		toast.success( message, {
			style: {
				marginTop: '28px',
				fontSize: '15px',
				padding: '10px 18px',
			},
		} );
	};

	const blockShowHideHandler = ( name ) => {
		const newData = options?.map( ( item ) => {
			if ( name === item.name ) {
				show_notification( item );
				return { ...item, show: ! item.show };
			}
			return item;
		} );
		setOptions( newData );
	};

	const integrationToggleHandler = ( id ) => {
		const integrationIds = [
			'elementor',
			'divi',
			'wpbakery',
			'oxygen',
			'beaver',
			'bricks',
		];

		const newData = integrationIds.map( ( item ) => {
			const savedOption = integrationOptions?.find( ( opt ) => opt.id === item );
			if ( id === item ) {
				const isEnabled = savedOption?.enabled ?? false;
				const message = isEnabled
					? __( 'This addon disabled successfully', 'location-weather' )
					: __( 'This addon enabled successfully', 'location-weather' );
				toast.success( message, {
					style: {
						marginTop: '28px',
						fontSize: '15px',
						padding: '10px 18px',
					},
				} );
				return { id: item, enabled: ! isEnabled };
			}
			return { id: item, enabled: savedOption?.enabled ?? true };
		} );
		setIntegrationOptions( newData );
	};

	const menuItems = [
		{
			label: __( 'Getting Started', 'location-weather' ),
			value: 'getting-start',
			hash: '#',
		},
		{
			label: __( 'Blocks', 'location-weather' ),
			value: 'blocks',
			hash: '#blocks',
			badge: 'new',
		},
		{
			label: __( 'Saved Templates', 'location-weather' ),
			value: 'saved_templates',
			hash: '#saved_templates',
		},
		{
			label: __( 'Integrations', 'location-weather' ),
			value: 'integrations',
			hash: '#integrations',
		},
		{
			label: __( 'Settings', 'location-weather' ),
			value: 'lw_settings',
			hash: '#lw_settings',
		},
		{
			label: __( 'Lite vs Pro', 'location-weather' ),
			value: 'lite_vs_pro',
			hash: '#lite_vs_pro',
		},
		{
			label: __( 'About Us', 'location-weather' ),
			value: 'about_us',
			hash: '#about_us',
		},
		{
			label: __( 'Our Plugins', 'location-weather' ),
			value: 'our_plugins',
			hash: '#our_plugins',
			icon: <OurPluginsIcon />,
		},
	];

	if ( hashValue === 'setupwizard' ) {
		return <SetupWizard />;
	}

	return (
		<>
			{ /* header */ }
			<HeaderItems />
			<div className="spl-weather-blocks-settings-page-container">
				{ /* Blocks settings page navigation tab */ }
				<div className="spl-weather-block-settings-navigation">
					<ul className={ page === 'our_plugins' || page === 'about_us' ? 'splwb-hide-our-plugins-border' : '' }>
						{ menuItems.map( ( item ) => (
							<li key={ item.value } className={ `${ item.value === 'our_plugins' ? 'splwb-nav-our-plugins' : '' } ${ page === item.value ? 'active' : '' }` }>
								<a
									href={ item.hash }
									className={
										page === item.value ? 'active' : ''
									}
									onClick={ () =>
										setPageAndHash( item.value )
									}
								>
									{ item.icon }
									{ item.label }
								{ item.badge && (
									<span className="splwb-nav-badge">{ __( 'NEW!', 'location-weather' ) }</span>
								) }
								</a>
							</li>
						) ) }
					</ul>
				</div>
				{ /* Render pages based on tab click */ }
				<div className="spl-weather-blocks-settings-page-wrapper">
					{ ( page === 'getting-start' || page === '' ) && (
						<QuickStart
							blockSettings={ options }
							blockShowHideHandler={ blockShowHideHandler }
							setPageAndHash={ setPageAndHash }
						/>
					) }
					{ page === 'blocks' && (
						<Blocks
							blockSettings={ options }
							blockShowHideHandler={ blockShowHideHandler }
						/>
					) }
					{ page === 'saved_templates' && <SavedTemplates /> }
					{ page === 'lw_settings' && <Settings /> }
						{ page === 'integrations' && (
							<Integrations
								integrationSettings={ integrationOptions }
								integrationToggleHandler={ integrationToggleHandler }
							/>
						) }
					{ page === 'lite_vs_pro' && <LiteVsPro /> }
					{ page === 'about_us' && <AboutUs /> }
				</div>
			</div>
			{ /* Render footer except about us page */ }
			{/* { page && page !== 'our_plugins' && <Footer /> } */}

			{ /* React Hot Toast Container for notifications */ }
			<Toaster
				position="top-right"
				toastOptions={ {
					style: {
						padding: '16px 24px',
						fontSize: '18px',
						borderRadius: '10px',
						maxWidth: '400px',
					},
				} }
			/>
		</>
	);
};

export default Render;
