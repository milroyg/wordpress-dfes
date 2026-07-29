import { __ } from '@wordpress/i18n';

// Breakpoints for pollutants (based on US EPA AQI scale, used by OpenWeather)
const BREAKPOINTS = {
	pm2_5: [
		{ cLow: 0.0, cHigh: 10, iLow: 0, iHigh: 50 },
		{ cLow: 10, cHigh: 25, iLow: 51, iHigh: 100 },
		{ cLow: 25, cHigh: 50, iLow: 101, iHigh: 150 },
		{ cLow: 50, cHigh: 75, iLow: 151, iHigh: 200 },
		{ cLow: 75, cHigh: 100, iLow: 201, iHigh: 250 },
		{ cLow: 100, cHigh: Infinity, iLow: 251, iHigh: 300 },
	],
	pm10: [
		{ cLow: 0, cHigh: 20, iLow: 0, iHigh: 50 },
		{ cLow: 20, cHigh: 50, iLow: 51, iHigh: 100 },
		{ cLow: 50, cHigh: 100, iLow: 101, iHigh: 150 },
		{ cLow: 100, cHigh: 200, iLow: 151, iHigh: 200 },
		{ cLow: 200, cHigh: 250, iLow: 201, iHigh: 250 },
		{ cLow: 250, cHigh: Infinity, iLow: 251, iHigh: 300 },
	],
	no2: [
		{ cLow: 0, cHigh: 40, iLow: 0, iHigh: 50 },
		{ cLow: 40, cHigh: 70, iLow: 51, iHigh: 100 },
		{ cLow: 70, cHigh: 150, iLow: 101, iHigh: 150 },
		{ cLow: 150, cHigh: 200, iLow: 151, iHigh: 200 },
		{ cLow: 200, cHigh: 250, iLow: 201, iHigh: 250 },
		{ cLow: 250, cHigh: Infinity, iLow: 251, iHigh: 300 },
	],
	o3: [
		{ cLow: 0.0, cHigh: 60, iLow: 0, iHigh: 50 },
		{ cLow: 60, cHigh: 100, iLow: 51, iHigh: 100 },
		{ cLow: 100, cHigh: 140, iLow: 101, iHigh: 150 },
		{ cLow: 140, cHigh: 180, iLow: 151, iHigh: 200 },
		{ cLow: 180, cHigh: 220, iLow: 201, iHigh: 250 },
		{ cLow: 220, cHigh: Infinity, iLow: 251, iHigh: 300 },
	],
	co: [
		{ cLow: 0.0, cHigh: 4400, iLow: 0, iHigh: 50 },
		{ cLow: 4400, cHigh: 9400, iLow: 51, iHigh: 100 },
		{ cLow: 9400, cHigh: 12400, iLow: 101, iHigh: 150 },
		{ cLow: 12400, cHigh: 15400, iLow: 151, iHigh: 200 },
		{ cLow: 15400, cHigh: 18000, iLow: 201, iHigh: 250 },
		{ cLow: 18000, cHigh: Infinity, iLow: 251, iHigh: 300 },
	],
	so2: [
		{ cLow: 0, cHigh: 20, iLow: 0, iHigh: 50 },
		{ cLow: 20, cHigh: 80, iLow: 51, iHigh: 100 },
		{ cLow: 80, cHigh: 250, iLow: 101, iHigh: 150 },
		{ cLow: 250, cHigh: 350, iLow: 151, iHigh: 200 },
		{ cLow: 350, cHigh: 400, iLow: 201, iHigh: 250 },
		{ cLow: 400, cHigh: Infinity, iLow: 251, iHigh: 300 },
	],
};

const COLORS = {
	Good: '#00B150',
	Moderate: '#EEC631',
	Poor: '#EA8B34',
	Unhealthy: '#E95378',
	Severe: '#B33FB9',
	Hazardous: '#C91F33',
};

export const REPORT = {
	Good: __(
		'Air is clean and safe. No health risks expected.',
		'location-weather'
	),
	Moderate: __(
		'Air is acceptable, but sensitive individuals should closely monitor their symptoms.',
		'location-weather'
	),
	Poor: __(
		'Air may cause discomfort. Sensitive groups should reduce outdoor exposure.',
		'location-weather'
	),
	Unhealthy: __(
		'Health risks increase. Everyone should limit the time spent outdoors.',
		'location-weather'
	),
	Severe: __(
		'Air is very unhealthy. Avoid outdoor activity whenever possible.',
		'location-weather'
	),
	Hazardous: __(
		'Serious health threat. Stay indoors and follow public health recommendations.',
		'location-weather'
	),
};

export const REPORT2 = {
	Good: __(
		'Air is clean and safe. Everyone can enjoy outdoor activities without concern.',
		'location-weather'
	),
	Moderate: __(
		'The air quality is generally acceptable for most people. However, sensitive groups may experience minor to moderate symptoms from long-term exposure.',
		'location-weather'
	),
	Poor: __(
		'Air quality is poor. Sensitive individuals may experience symptoms such as coughing or throat irritation. Limit outdoor activity.',
		'location-weather'
	),
	Unhealthy: __(
		'Air quality poses health risks for all. Even healthy individuals may experience breathing difficulties and other symptoms. Minimize outdoor activities.',
		'location-weather'
	),
	Severe: __(
		'Air quality poses health risks for all. Even healthy individuals may experience breathing difficulties and other symptoms. Minimize outdoor activities.',
		'location-weather'
	),
	Hazardous: __(
		'Air pollution is at emergency levels. Severe health risk for all. Stay indoors and follow public health instructions.',
		'location-weather'
	),
};

export function calculateIAQI( value, pollutant ) {
	const bps = BREAKPOINTS[ pollutant ];
	if ( ! bps ) return null;

	for ( const bp of bps ) {
		if ( value >= bp.cLow && value <= bp.cHigh ) {
			if ( ! isFinite( bp.cHigh ) ) {
				return bp.iHigh;
			}
			return Math.round(
				( ( bp.iHigh - bp.iLow ) / ( bp.cHigh - bp.cLow ) ) *
					( value - bp.cLow ) +
					bp.iLow
			);
		}
	}
	return null;
}

function getAQICategory( iaqi ) {
	if ( iaqi <= 50 ) return 'Good';
	if ( iaqi <= 100 ) return 'Moderate';
	if ( iaqi <= 150 ) return 'Poor';
	if ( iaqi <= 200 ) return 'Unhealthy';
	if ( iaqi <= 250 ) return 'Severe';
	return 'Hazardous';
}

function getPollutantScaleSegments( pollutant ) {
	const segments = BREAKPOINTS[ pollutant ];
	if ( ! segments ) return [];

	return segments.map( ( { cLow, cHigh, iHigh } ) => {
		const testValue = cHigh === Infinity ? cLow + 1 : cHigh;
		const iaqi = calculateIAQI( testValue, pollutant );
		const condition = getAQICategory( iaqi );
		const color = COLORS[ condition ];
		return { cLow, cHigh, iHigh, condition, color };
	} );
}

// Utility to get pollutant info array for all pollutants
const conditions_key = {
	Good: 'good',
	Moderate: 'moderate',
	Poor: 'poor',
	Unhealthy: 'unhealthy',
	Severe: 'severe',
	Hazardous: 'hazardous',
};

// Overall AQI from multiple pollutants
export function getOverallAQIData( data ) {
	const pollutants = [ 'pm2_5', 'pm10', 'o3', 'no2', 'so2', 'co' ];
	const iaqiValues = pollutants.map( ( p ) => ( {
		pollutant: p,
		iaqi: calculateIAQI( data[ p ], p ) || 0,
	} ) );
	const max = iaqiValues.reduce( ( a, b ) => ( b.iaqi > a.iaqi ? b : a ) );
	const conditionKey = getAQICategory( max.iaqi );

	return {
		pollutant: max.pollutant,
		aqiValue: max.iaqi,
		condition: conditionKey,
		color: COLORS[ conditionKey ],
		report: __( REPORT[ conditionKey ], 'location-weather' ),
		reportDetailed: __( REPORT2[ conditionKey ], 'location-weather' ),
	};
}

export function getAllPollutantData( value, key ) {
	const iaqi = calculateIAQI( value, key );
	const conditionKey = getAQICategory( iaqi );
	const ScaleSegments = getPollutantScaleSegments( key );
	return {
		pollutant: key,
		iaqi,
		condition: conditionKey,
		color: COLORS[ conditionKey ],
		report: __( REPORT[ conditionKey ], 'location-weather' ),
		reportDetailed: __( REPORT2[ conditionKey ], 'location-weather' ),
		scaleSegments: ScaleSegments,
		condition_value: conditions_key[ conditionKey ],
	};
}
