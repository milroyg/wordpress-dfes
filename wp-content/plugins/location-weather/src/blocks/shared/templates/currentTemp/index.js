import { useEffect } from '@wordpress/element';
import {
	weatherUnits,
	calculateTemperature,
	inArray,
} from '../../../../controls';
import {
	useSplWeatherContextData,
	useTogglePanelBody,
} from '../../../../context';

const CurrentTempHTML = ( { attributes, temp } ) => {
	const { togglePanelBody } = useTogglePanelBody();
	const { apiUnit, toUnit, activeTempUnit, setActiveTempUnit } =
		useSplWeatherContextData();
	const { template, displayTemperatureUnit, blockName } = attributes;

	const isVerticalLayout =
		inArray(
			[
				'vertical-one',
				'vertical-four',
				'vertical-six',
				'horizontal-two',
				'tabs-one',
				'table-two',
			],
			template
		) || inArray( [ 'accordion', 'grid', 'combined' ], blockName );

	return (
		<div
			className={ `spl-weather-card-current-temperature ${
				isVerticalLayout ? 'splw-vertical-temp' : 'splw-horizontal-temp'
			} sp-d-flex sp-gap-4px` }
			onClick={ () => togglePanelBody( 'current-weather', true ) }
		>
			<span className="spl-weather-current-temp">
				{ calculateTemperature( temp, apiUnit, toUnit ) }
			</span>
			<div className="spl-weather-temperature-metric splw-single-unit">
				<span className="spl-weather-temp-scale">
					{ weatherUnits( toUnit ) }
				</span>
			</div>
		</div>
	);
};

export default CurrentTempHTML;
