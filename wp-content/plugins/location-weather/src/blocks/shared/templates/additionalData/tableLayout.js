import { __ } from '@wordpress/i18n';
import useAdditionalData from './useAdditionalData';
import { inArray } from '../../../../controls';
import { WeatherDetailsIcon } from './templates';
import { useTogglePanelBody } from '../../../../context';

const WeatherTableLayout = ( { attributes, weatherData } ) => {
	const { togglePanelBody } = useTogglePanelBody();
	const { additionalDataIcon, additionalDataIconType } = attributes;
	const { weatherDetailsData, weatherDetailsAttr } = useAdditionalData(
		weatherData,
		attributes
	);

	return (
		<div
			className="spl-weather-card-daily-details"
			onClick={ () => togglePanelBody( 'additional-data', true ) }
		>
			<div className="spl-weather-details-table-data sp-d-grid sp-grid-cols-2">
				{ weatherDetailsAttr?.map( ( { label, value }, i ) => (
					<div
						key={ i }
						className="spl-weather-details sp-d-flex sp-justify-between sp-align-i-center"
					>
						<div className="spl-weather-details-title-wrapper sp-d-flex sp-align-i-center sp-gap-6px">
							{ additionalDataIcon && (
								<WeatherDetailsIcon
									iconName={ value }
									iconType={ additionalDataIconType }
								/>
							) }
							<span className="spl-weather-details-title">
								{ label }
							</span>
						</div>
						{ value === 'wind' && (
							<span className="spl-weather-details-value">
								{ weatherDetailsData?.wind?.speed }{ ' ' }
								{ weatherDetailsData?.wind?.direction }
							</span>
						) }
						{ value === 'uv_index' && (
							<span className="spl-weather-details-value">
								{ weatherDetailsData?.uv_index?.index }
							</span>
						) }
						{ ! inArray( [ 'wind', 'uv_index' ], value ) && (
							<span className="spl-weather-details-value">
								{ weatherDetailsData[ value ] }
							</span>
						) }
					</div>
				) ) }
			</div>
		</div>
	);
};

export default WeatherTableLayout;
