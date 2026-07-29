import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { AdditionalCodesIcon, AdvancedIcon, APIKeyIcon } from '../../icons';
import {
	AdditionalCodes,
	AdvancedControls,
	WeatherAPIKey,
} from './settings-tab-content';
const Settings = () => {
	const [ settingTab, setSettingTab ] = useState( 'weather-api' );
	const [ settingsOptions, setSettingsOptions ] = useState(
		splw_admin_settings_localize?.settings
	);

	const tabs = [
		{
			label: __( 'Weather API Key', 'location-weather' ),
			Icon: APIKeyIcon,
			value: 'weather-api',
			Render: WeatherAPIKey,
		},
		{
			label: __( 'Advanced Controls', 'location-weather' ),
			Icon: AdvancedIcon,
			value: 'advanced',
			Render: AdvancedControls,
		},
		{
			label: __( 'Additional CSS & JS', 'location-weather' ),
			Icon: AdditionalCodesIcon,
			value: 'additional',
			Render: AdditionalCodes,
		},
	];
	return (
		<section className="splw-settings-page-container">
			<div className="splw-setting-tabs">
				<ul>
					{ tabs?.map( ( { label, value, Icon } ) => (
						<li
							key={ value }
							className={ `splw-setting-tab ${
								settingTab === value ? 'active' : ''
							}` }
							onClick={ () => setSettingTab( value ) }
						>
							<span className="splw-setting-icon">
								<Icon />
							</span>{ ' ' }
							<span>{ label }</span>
						</li>
					) ) }
				</ul>
				<div className="splw-setting-tabs-bottom"></div>
			</div>
			<div className="splw-setting-tab-content">
				{ tabs?.map( ( { value, Render } ) =>
					settingTab === value ? (
						<Render
							settingsOptions={ settingsOptions }
							setSettingsOptions={ setSettingsOptions }
							key={ value }
						/>
					) : null
				) }
			</div>
		</section>
	);
};

export default Settings;
