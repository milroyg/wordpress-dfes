import { useMemo } from '@wordpress/element';
import { getRandomId } from '../../../../controls';
import { getAllPollutantData } from '../../../aqi-minimal/getPollutantData';

export default function AqiPollutantGauge( {
	value: rawValue,
	pollutant = 'pm2_5',
	segment = 'iHigh',
	size = 300,
	strokeWidth = 40,
	labelPadding = 20,
	isAccordion = false,
	extra = false,
	aqiText = '',
	pointerBaseWidth = 12,
	conditionLabels = true,
	showScaleValues = true,
} ) {
	const data = useMemo(
		() => getAllPollutantData( rawValue, pollutant ),
		[ rawValue, pollutant ]
	);
	const effectiveValue = segment === 'iHigh' ? data.iaqi : rawValue;

	// maxValue: use last segment unless it's Infinity
	const maxValue = useMemo( () => {
		if ( ! data?.scaleSegments?.length ) return 300;
		const last =
			data.scaleSegments[ data.scaleSegments.length - 1 ][ segment ];
		return last === Infinity || last === null ? 300 : last;
	}, [ data, segment, pollutant ] );

	const gaugeStartAngle = -180;
	const gaugeEndAngle = 0;

	// Clamp and calculate pointer angle
	const pointerAngle = useMemo( () => {
		if ( typeof effectiveValue !== 'number' || ! maxValue )
			return gaugeStartAngle;
		const clamped = Math.min( Math.max( effectiveValue, 0 ), maxValue );
		return (
			gaugeStartAngle +
			( clamped / maxValue ) * ( gaugeEndAngle - gaugeStartAngle )
		);
	}, [ effectiveValue, maxValue, pollutant, segment ] );

	const radius = ( size - strokeWidth ) / 2;
	const cx = size / 2 + labelPadding;
	const cy = size / 2 + strokeWidth / 2;

	if ( ! data?.scaleSegments?.length )
		return <div>No scale data available</div>;

	/** Helpers */
	const polarToCartesian = ( angleDeg, r ) => {
		const angleRad = ( Math.PI / 180 ) * angleDeg;
		return {
			x: cx + r * Math.cos( angleRad ),
			y: cy + r * Math.sin( angleRad ),
		};
	};

	/** Arc Segments */
	let prevAngle = gaugeStartAngle;
	const arcs = data.scaleSegments.map( ( seg, idx ) => {
		const segMax =
			seg[ segment ] === Infinity || seg[ segment ] === null
				? maxValue
				: seg[ segment ];
		const endAngle =
			gaugeStartAngle +
			( segMax / maxValue ) * ( gaugeEndAngle - gaugeStartAngle );

		const start = polarToCartesian( prevAngle, radius );
		const end = polarToCartesian( endAngle, radius );
		const largeArcFlag = endAngle - prevAngle > 180 ? 1 : 0;
		const pathId = `arc-${ seg.condition }-${ getRandomId( 'id-' ) }`;
		const d = `M ${ start.x } ${ start.y } A ${ radius } ${ radius } 0 ${ largeArcFlag } 1 ${ end.x } ${ end.y }`;

		prevAngle = endAngle;

		return (
			<g key={ seg.condition }>
				{ aqiText ? (
					<>
						<defs>
							<linearGradient id="gaugeGradient">
								<stop offset="0%" stopColor="#00B150" />
								<stop offset="20%" stopColor="#EEC631" />
								<stop offset="40%" stopColor="#EA8B34" />
								<stop offset="60%" stopColor="#E95378" />
								<stop offset="80%" stopColor="#B33FB9" />
								<stop offset="100%" stopColor="#C91F33" />
							</linearGradient>
						</defs>
						<path
							d={ `M ${
								polarToCartesian( gaugeStartAngle, radius ).x
							} ${ polarToCartesian( gaugeStartAngle, radius ).y }
	  				 A ${ radius } ${ radius } 0 1 1 ${
							polarToCartesian( gaugeEndAngle, radius ).x
						} ${ polarToCartesian( gaugeEndAngle, radius ).y }` }
							stroke={ aqiText ? 'url(#gaugeGradient)' : '#ccc' }
							strokeWidth={ strokeWidth }
							strokeLinecap="round"
							fill="none"
						/>
					</>
				) : (
					<path
						id={ pathId }
						d={ d }
						stroke={ seg.color }
						strokeWidth={ strokeWidth }
						fill="none"
					/>
				) }

				{ conditionLabels && (
					<text
						fill="#fff"
						fontSize={ size * 0.03 }
						fontWeight="bold"
						textAnchor="middle"
						dominantBaseline="middle"
					>
						<textPath href={ `#${ pathId }` } startOffset="50%">
							{ seg.condition }
						</textPath>
					</text>
				) }
			</g>
		);
	} );

	/** Pointer */
	const pointerColor =
		data.scaleSegments.find(
			( seg ) => effectiveValue <= ( seg[ segment ] ?? Infinity )
		)?.color || '#000';
	const pointerLength = radius - strokeWidth / 2 - 10;
	const tip = polarToCartesian( pointerAngle, pointerLength );
	const leftBase = polarToCartesian(
		pointerAngle - 90,
		pointerBaseWidth / 2
	);
	const rightBase = polarToCartesian(
		pointerAngle + 90,
		pointerBaseWidth / 2
	);
	const pointerEnd = polarToCartesian( pointerAngle, radius );

	/** Scale labels path */
	const scaleArcRadius = radius + 25;
	const uniqueId = getRandomId( 'gauge' );
	const scalePathId = `scale-arc-${ uniqueId }`;
	const scalePathD = `M ${
		polarToCartesian( gaugeStartAngle, scaleArcRadius ).x
	} ${
		polarToCartesian( gaugeStartAngle, scaleArcRadius ).y
	} A ${ scaleArcRadius } ${ scaleArcRadius } 0 0 1 ${
		polarToCartesian( gaugeEndAngle, scaleArcRadius ).x
	} ${ polarToCartesian( gaugeEndAngle, scaleArcRadius ).y }`;

	// Labels: 0 … last value+
	const scaleValues = [
		0,
		...data.scaleSegments.map( ( seg ) =>
			seg[ segment ] === Infinity || seg[ segment ] === null
				? maxValue
				: seg[ segment ]
		),
	];

	return (
		<svg
			width={ size + labelPadding * 2 }
			height={ size / 1.583 }
			viewBox={ `0 0 ${ size + labelPadding * 2 } ${ size / 1.583 }` }
		>
			{ arcs }

			{ ! isAccordion && (
				<>
					{ /* Invisible scale arc for labels */ }
					<path id={ scalePathId } d={ scalePathD } fill="none" />

					{ /* Scale labels along top arc */ }
					{ showScaleValues &&
						scaleValues.map( ( val, idx ) => {
							const display =
								idx === 0
									? '0'
									: idx === scaleValues.length - 1
									? `${ val }+`
									: val;
							const offset =
								idx === scaleValues.length - 1
									? '95%'
									: ( val / maxValue ) * 100 + '%';
							return (
								<text
									key={ `scale-${ idx }` }
									fill="#333"
									fontSize={ size * 0.04 }
									fontWeight="bold"
								>
									<textPath
										href={ `#${ scalePathId }` }
										startOffset={ offset }
										textAnchor="middle"
										dominantBaseline="middle"
									>
										{ display }
									</textPath>
								</text>
							);
						} ) }

					{ /* Pointer */ }
					<path
						d={ `M ${ leftBase.x } ${ leftBase.y } L ${ tip.x } ${ tip.y } L ${ rightBase.x } ${ rightBase.y } Z` }
						fill={ pointerColor }
					/>

					{ /* Center circle */ }
					<circle
						cx={ cx }
						cy={ cy }
						r={ pointerBaseWidth / 2 }
						fill="#fff"
						stroke={ pointerColor }
						strokeWidth="2"
					/>
				</>
			) }

			{ isAccordion && (
				<>
					{ /* Small circle pointer for accordion */ }
					<circle
						cx={ pointerEnd.x }
						cy={ pointerEnd.y }
						r={ 5 }
						fill="#fff"
						stroke={ pointerColor }
						strokeWidth="2"
					/>
				</>
			) }
			{ ( isAccordion || extra ) && (
				<>
					{ aqiText && (
						<text
							x="50%"
							y={ extra ? cy - 30 : cy - 30 }
							textAnchor="middle"
							fontSize={ size * 0.1 }
							fontWeight="bold"
							fill="#000"
							className="spl-progress-text"
						>
							{ aqiText }
						</text>
					) }
					<text
						x="50%"
						y={ extra ? cy - 30 : cy }
						textAnchor="middle"
						fontSize={ size * 0.2 }
						fontWeight="bold"
						fill={ pointerColor }
					>
						{ effectiveValue }
					</text>
				</>
			) }
		</svg>
	);
}
