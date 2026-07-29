import { __ } from '@wordpress/i18n';
import { memo } from '@wordpress/element';
import { useTogglePanelBody } from '../../context';

export const TabNavigationBar = memo(
	( { attributes, activeTab, setActiveTab } ) => {
		const { togglePanelBody } = useTogglePanelBody();
		const { hourlyTitle, displayWeatherForecastData, displayWeatherMap } =
			attributes;

		const currentWeather = {
			label: __( 'Current Weather', 'location-weather' ),
			value: 'current_weather',
		};

		const navOptions = {
			hourly: [ currentWeather, { label: hourlyTitle, value: 'hourly' } ],
		};

		const tabNavOptions = [
			...( ( displayWeatherForecastData && navOptions[ 'hourly' ] ) ||
				[] ),
			...( displayWeatherMap
				? [
						{
							label: 'Radar Map',
							value: 'map',
						},
				  ]
				: [] ),
		];

		return (
			<div
				className="spl-weather-tabs-navigation sp-w-full"
				onClick={ () => togglePanelBody( 'tab', true ) }
			>
				<ul className="spl-weather-tab-navs">
					{ tabNavOptions?.map( ( { label, value }, i ) => (
						<li
							key={ i }
							className={ `spl-weather-tab-nav${
								activeTab === value ? ' active' : ''
							}` }
							onClick={ () => setActiveTab( value ) }
						>
							{ label }
						</li>
					) ) }
				</ul>
			</div>
		);
	}
);
