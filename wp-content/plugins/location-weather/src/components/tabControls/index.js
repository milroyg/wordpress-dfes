import { TabPanel } from '@wordpress/components';
import { GeneralIcon, StyleIcon } from './icon';
import './editor.scss';

const TabControls = ( {
	attributes,
	setAttributes,
	GeneralTab = '',
	StyleTab = '',
	AdvancedTab = '',
	VisibilityTab = '',
	tabName = 'general',
	setTabName = () => {},
} ) => {
	const Tabs = [];

	if ( GeneralTab ) {
		Tabs.push( {
			name: `general`,
			title: (
				<span className="location-weather-tab-panel-title">
					{ GeneralIcon() } General
				</span>
			),
			className: 'location-weather-general-tab',
		} );
	}
	if ( StyleTab ) {
		Tabs.push( {
			name: `style`,
			title: (
				<span className="location-weather-tab-panel-title">
					{ StyleIcon() } Style
				</span>
			),
			className: 'location-weather-style-tab',
		} );
	}
	if ( VisibilityTab ) {
		Tabs.push( {
			name: 'visibility',
			title: (
				<span className="location-weather-tab-panel-title">
					Visibility
				</span>
			),
			className: 'location-weather-visibility-tab',
		} );
	}

	if ( AdvancedTab ) {
		Tabs.push( {
			name: 'advanced',
			title: (
				<span className="location-weather-tab-panel-title">
					Advanced
				</span>
			),
			className: 'location-weather-advanced-tab',
		} );
	}

	return (
		<TabPanel
			className="location-weather-tab-panel"
			activeClass="active-tab"
			initialTabName={ tabName }
			onSelect={ ( newVal ) => setTabName( newVal ) }
			tabs={ Tabs }
		>
			{ ( tab ) => {
				return (
					<>
						{ tab.name === 'general' && GeneralTab && (
							<GeneralTab
								attributes={ attributes }
								setAttributes={ setAttributes }
							/>
						) }
						{ tab.name === 'style' && StyleTab && (
							<StyleTab
								attributes={ attributes }
								setAttributes={ setAttributes }
							/>
						) }
						{ tab.name === 'visibility' && VisibilityTab && (
							<VisibilityTab
								attributes={ attributes }
								setAttributes={ setAttributes }
							/>
						) }
						{ tab.name === 'advanced' && AdvancedTab && (
							<AdvancedTab
								attributes={ attributes }
								setAttributes={ setAttributes }
							/>
						) }
					</>
				);
			} }
		</TabPanel>
	);
};

export default TabControls;
