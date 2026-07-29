import { memo, useMemo } from '@wordpress/element';
import { useTogglePanelBody } from '../../../../context';
import { getRandomId } from '../../../../controls';

const SunOrbitHTML = ( { weatherData, iconUrl, translate = '-60px' } ) => {
	const { sun_position, sunrise, sunset } = weatherData;
	const { togglePanelBody } = useTogglePanelBody();
	const animationName = useMemo(
		() => getRandomId( 'spl-weather-sun-orbit-animation' ),
		[ sun_position ]
	);

	return (
		<div
			className="spl-weather-sun-orbit sp-d-flex sp-justify-between"
			onClick={ () => togglePanelBody( 'additional-data', true ) }
		>
			{ sunrise && (
				<div className="spl-weather-sunrise sp-d-flex sp-flex-col sp-justify-center">
					<span className="lw-title-wrapper">
						<span className="spl-weather-details-title">
							Sunrise
						</span>
					</span>
					<span className="spl-weather-details-value">
						{ sunrise }
					</span>
				</div>
			) }
			<div className="spl-weather-sun-orbit-sky sp-d-flex sp-flex-row sp-align-i-center sp-justify-between">
				<div className="spl-weather-sun-orbit-sunrise-icon">
					<i className="splwp-icon-sunrise-2"></i>
				</div>
				{ sun_position && sun_position > 0 && (
					<>
						<style>
							{ `@keyframes ${ animationName } {
                              0% {
                                transform: rotate(0deg) translate(${ translate }) rotate(10deg);
                              }  
                              100% {
                                transform: rotate(${ sun_position }deg) translate(${ translate }) rotate(10deg);
                              }  
                            }
                        ` }
						</style>
						<div
							className="spl-weather-sun-orbit-sun"
							style={ {
								transform: `rotate(${ sun_position }deg) translate(${ translate }) rotate(0deg)`,
								animation: `${ animationName } 6s linear`,
							} }
						>
							<img
								decoding="async"
								src={ `${ iconUrl }/weather-icons/01d.svg` }
								alt="Sun in orbit"
								width="30"
								height="30"
							/>
						</div>
					</>
				) }
				<div className="spl-weather-sun-orbit-sunset-icon">
					<i className="splwp-icon-sunset-2"></i>
				</div>
			</div>
			{ sunset && (
				<div className="spl-weather-sunset sp-d-flex sp-flex-col sp-justify-center">
					<span className="lw-title-wrapper">
						<span className="spl-weather-details-title">
							Sunset
						</span>
					</span>
					<span className="spl-weather-details-value">
						{ sunset }
					</span>
				</div>
			) }
		</div>
	);
};

export default memo( SunOrbitHTML );
