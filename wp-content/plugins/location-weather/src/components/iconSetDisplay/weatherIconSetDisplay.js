import './editor.scss';

export const WeatherIconSetDisplay = ( {
	iconType = 'weather-condition',
	iconSetType = 'forecast_icon_set_one',
} ) => {
	const pluginUrl = splWeatherBlockLocalize?.pluginUrl;
	const folderName =
		iconType === 'weather-condition' ? 'forecast-icon-set' : 'icon-set';
	const imageUrl = `${ pluginUrl }/includes/Admin/framework/assets/images/${ folderName }/${ iconSetType }.svg`;

	return (
		<div className="spl-weather-icon-set-display-component spl-weather-component-mb">
			<div
				className={ `spl-weather-icon-set-container set-type-${ iconSetType }` }
			>
				<img src={ imageUrl } alt="weather icon set image" />
			</div>
		</div>
	);
};

export default WeatherIconSetDisplay;
