import { getWeatherIconByType, inArray } from '../../../../controls';

const generateWrapperClass = ( str ) => {
	const wrapperClass = str?.replace( /([A-Z])/g, '-$1' ).toLowerCase();
	return wrapperClass;
};

export const WeatherDetailsIcon = ( { iconName, iconType } ) => {
	const additionalDataIcon = getWeatherIconByType( iconType );
	return (
		<span className="spl-weather-details-icon sp-d-flex sp-align-i-center">
			<i className={ additionalDataIcon[ iconName ] }></i>
		</span>
	);
};

export const CustomSliderNavigationIcon = () => (
	<>
		<button className="spl-weather-custom-slider-nav spl-weather-custom-slider-nav-prev sp-cursor-pointer">
			<i className="splwp-icon-chevron"></i>
		</button>
		<button className="spl-weather-custom-slider-nav spl-weather-custom-slider-nav-next sp-cursor-pointer">
			<i className="splwp-icon-chevron right"></i>
		</button>
	</>
);

export const RenderSingleWeatherData = ( {
	weatherData,
	iconType,
	option,
	displayColon = false,
	showIcon,
} ) => {
	const { label, value } = option;

	if ( ! weatherData[ value ] ) {
		return;
	}

	return (
		<div
			className={ `spl-weather-details spl-weather-${ generateWrapperClass(
				value
			) }` }
		>
			<span className="spl-weather-details-title-wrapper">
				{ showIcon && (
					<WeatherDetailsIcon
						iconName={ value }
						iconType={ iconType }
					/>
				) }
				<span className="spl-weather-details-title">
					{ label }
					{ displayColon ? ':' : '' }
				</span>
			</span>
			<span className="spl-weather-details-value">
				{ ! inArray( [ 'wind', 'uv_index' ], value ) &&
					weatherData[ value ] }
				{ value === 'uv_index' && weatherData[ 'uv_index' ]?.index }
				{ value === 'wind' && (
					<>
						<span className="spl-weather-wind-value">
							{ weatherData[ value ]?.speed }
						</span>{ ' ' }
						<span className="spl-weather-wind-direction">
							{ weatherData[ value ]?.direction }
						</span>
					</>
				) }
			</span>
		</div>
	);
};

export const ComportAdditionalData = ( {
	weatherData,
	comportData,
	iconType,
} ) => (
	<div className="spl-weather-details-comport-data sp-d-flex sp-justify-between sp-w-full">
		{ comportData?.map( ( { label, value }, i ) => (
			<div
				key={ i }
				className={ `spl-weather-details spl-weather-${ generateWrapperClass(
					value
				) } sp-d-flex sp-align-i-center sp-gap-8px` }
				title={ label }
			>
				<span className="spl-weather-details-title-wrapper">
					<span className="spl-weather-details-icon">
						<WeatherDetailsIcon
							iconName={ value }
							iconType={ iconType }
						/>
					</span>
				</span>
				<span className="spl-weather-details-value">
					{ value !== 'wind' && weatherData[ value ] }
					{ value === 'wind' && (
						<>
							<span className="spl-weather-wind-value">
								{ weatherData[ value ]?.speed }
							</span>{ ' ' }
							<span className="spl-weather-wind-direction">
								{ weatherData[ value ]?.direction }
							</span>
						</>
					) }
				</span>
			</div>
		) ) }
	</div>
);
