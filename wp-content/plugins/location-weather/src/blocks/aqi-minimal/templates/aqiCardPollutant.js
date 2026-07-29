import { __ } from '@wordpress/i18n';
import { useMemo, memo, useCallback } from '@wordpress/element';
import { useTogglePanelBody } from '../../../context';
import { hexToRgba, inArray } from '../../../controls';
import classNames from 'classnames';
import { getAllPollutantData } from '../getPollutantData';

const subscriptMap = {
	0: '₀',
	1: '₁',
	2: '₂',
	3: '₃',
	4: '₄',
	5: '₅',
	6: '₆',
	7: '₇',
	8: '₈',
	9: '₉',
};

const toSubscript = ( symbol ) =>
	symbol.replace( /\d/g, ( d ) => subscriptMap[ d ] );
const getLabel = ( symbol, style ) =>
	style === 'subscript' ? toSubscript( symbol ) : symbol;

const POLLUTANTS = [
	{
		key: 'pm2_5',
		name: 'Particulate Matter',
		symbol: 'PM2.5',
		unit: 'µg/m³',
	},
	{ key: 'pm10', name: 'Particulate Matter', symbol: 'PM10', unit: 'µg/m³' },
	{ key: 'so2', name: 'Sulphur Dioxide', symbol: 'SO2', unit: 'µg/m³' },
	{ key: 'no2', name: 'Nitrogen Dioxide', symbol: 'NO2', unit: 'µg/m³' },
	{ key: 'o3', name: 'Ozone', symbol: 'O3', unit: 'µg/m³' },
	{ key: 'co', name: 'Carbon Monoxide', symbol: 'CO', unit: 'mg/m³' },
];

const AqiCardPollutant = ( {
	displaySymbolDisplayStyle,
	weatherData,
	enablePollutantIndicator,
	enablePollutantMeasurementUnit,
} ) => {
	// Early return for safety
	if ( ! weatherData ) return null;
	const { togglePanelBody } = useTogglePanelBody();
	// Build pollutants list only if weatherData or style changes
	const pollutantItems = useMemo( () => {
		return POLLUTANTS.reduce( ( acc, { key, name, symbol, unit } ) => {
			const value = weatherData?.[ key ];
			if ( ! value ) return acc;

			const data = getAllPollutantData( value, key );

			if ( ! data ) return acc;
			acc.push( {
				key,
				label: getLabel( symbol, displaySymbolDisplayStyle ),
				name,
				value,
				unit,
				condition: data.condition || 'unknown',
				color: data?.color,
			} );
			return acc;
		}, [] );
	}, [ weatherData, displaySymbolDisplayStyle ] );

	// Stable click handler
	const handleClick = useCallback( () => {
		togglePanelBody( 'aqi-quality-parameter', true );
	}, [ togglePanelBody ] );

	// Nothing to render
	if ( ! pollutantItems.length ) return null;

	return (
		<div className="spl-aqi-card-pollutant-details-wrapper">
			<div
				className="spl-aqi-card-pollutant-details sp-d-grid sp-w-full sp-grid-cols-2 sp-gap-10px"
				onClick={ handleClick }
			>
				{ pollutantItems.map(
					( { key, label, value, condition, unit, color } ) => {
						return (
							<div
								key={ key }
								className={ `spl-aqi-card-pollutant-item sp-d-flex sp-w-full sp-justify-between sp-align-i-center spl-aqi-condition-${ condition?.toLowerCase() }` }
								style={ {
									'--spl-pollutant-color': hexToRgba( color ),
								} }
							>
								<div
									className={ classNames(
										`spl-pollutant-title`,
										{
											indicator: enablePollutantIndicator,
										}
									) }
								>
									{ label }
								</div>
								<div className="spl-pollutant-value">
									{ value }{ ' ' }
									{ enablePollutantMeasurementUnit && (
										<span className="spl-pollutant-unit">
											{ unit }
										</span>
									) }
								</div>
							</div>
						);
					}
				) }
			</div>
		</div>
	);
};

export default memo( AqiCardPollutant );
