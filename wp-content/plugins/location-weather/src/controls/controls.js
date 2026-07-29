import { dateI18n } from '@wordpress/date';
import { useSelect } from '@wordpress/data';
import { hexToCSSFilter } from 'hex-to-css-filter';
import tinycolor from 'tinycolor2';
import { blockRegisterInfo, weatherColors } from './constants';

export const getRandomId = ( prefix = '' ) => {
	const randomNumber = Math.floor( 10000000 + Math.random() * 90000000 );
	const randomId = `${ prefix }${ randomNumber }`;
	return randomId;
};

// Device type hook with memoization.
export const useDeviceType = () => {
	return useSelect( ( select ) => {
		// Detect which editor we're in
		const canvas = document.getElementsByClassName(
			'edit-site-visual-editor__editor-canvas'
		);

		// Use 'core/editor' for both Site Editor and Post Editor
		const selector =
			canvas.length > 0 ? 'core/edit-site' : 'core/edit-post';
		const editorStore = select( 'core/editor' );

		// Try to get device type safely
		return (
			editorStore?.getDeviceType?.() ||
			select( selector )?.__experimentalGetPreviewDeviceType?.() || // fallback for old WP
			'Desktop'
		);
	}, [] );
};

export const cssString = ( css ) => {
	let result = '';
	for ( const selector in css ) {
		let cssProps = '';
		for ( const property in css[ selector ] ) {
			if (
				css[ selector ][ property ] &&
				css[ selector ][ property ].length > 0
			) {
				cssProps += property + ':' + css[ selector ][ property ] + ';';
			}
		}
		result += '' !== cssProps ? selector + '{' + cssProps + '}' : '';
	}
	return result;
};

export const cssDataCheck = ( value, unit = '' ) => {
	if ( value === undefined ) return '';

	if ( typeof value === 'object' ) {
		const filtered = Object.values( value ).filter(
			( val ) =>
				val !== null &&
				val !== undefined &&
				val.toString().trim().length > 0
		);

		// if nothing left, return empty string
		if ( filtered.length === 0 ) return '';

		return filtered.map( ( val ) => `${ val }${ unit }` ).join( ' ' );
	}
	return value.toString().trim().length > 0 ? `${ value }${ unit }` : '';
};

// Generate box shadow css.
export const boxCss = ( enable, shadow, color ) => {
	if ( '' === shadow[ color ] ) {
		return '';
	}
	return enable
		? `${ shadow?.unit === 'inset' ? shadow?.unit : '' } ${
				shadow?.value.top
		  }px ${ shadow?.value.right }px ${ shadow.value.bottom }px ${
				shadow?.value.left
		  }px ${ shadow[ color ] }`
		: 'none';
};

// Check unit single or object.
export const unit = ( attributes, deviceType ) => {
	if ( 'object' !== typeof attributes.unit ) {
		return attributes.unit;
	}
	return attributes.unit[ deviceType ];
};

// Shows temperature value in Metric(Celsius) and Imperial(Fahrenheit) based on options (Display Temperature Unit & Active Temperature Unit ).
export const calculateTemperature = (
	temperature,
	fromUnit,
	toUnit = 'metric'
) => {
	if ( fromUnit === toUnit ) {
		return Math.round( temperature );
	}

	const conversions = {
		metric: {
			imperial: ( c ) => ( c * 9 ) / 5 + 32, // C → F
			kelvin: ( c ) => c + 273.15, // C → K
		},
		imperial: {
			metric: ( f ) => ( ( f - 32 ) * 5 ) / 9, // F → C
			kelvin: ( f ) => ( ( f - 32 ) * 5 ) / 9 + 273.15, // F → K
		},
		kelvin: {
			metric: ( k ) => k - 273.15, // K → C
			imperial: ( k ) => ( ( k - 273.15 ) * 9 ) / 5 + 32, // K → F
		},
	};
	const temp =
		Math.round( conversions[ fromUnit ][ toUnit ]( temperature ) ) || 0;
	return temp;
};

// calculate pressure it, receive hpa value
export const calcPressure = ( pressure, scale ) => {
	pressure = parseInt( pressure );
	const conversionFactors = {
		hpa: 1,
		mb: 1, // Millibars: 1 hPa = 1 mb
		kpa: 0.1, // Kilopascals: 1 hPa = 0.1 kPa
		inhg: 0.029529983071445, // Inches of Mercury: 1 hPa ≈ 0.02953 inHg
		psi: 0.014503773773022, // Pounds per Square Inch: 1 hPa ≈ 0.0145 psi
		mmhg: 0.750061683, // Millimeters of Mercury: 1 hPa ≈ 0.7501 mmHg
		ksc: 0.001019716213, // Kilograms per Square Centimeter: 1 hPa ≈ 0.00102 kg/cm²
	};
	const value = Math.round( pressure * conversionFactors[ scale ] );
	return value;
};
// calculate precipitation, it receive mm value
export const calcPrecipitation = ( mmValue, unit ) => {
	if ( ! mmValue ) {
		return 0;
	}
	const value = typeof mmValue === 'number' ? mmValue : mmValue[ 0 ];
	const precipitation = parseFloat( value );
	const conversionFactors = {
		mm: 1,
		inch: 0.0393701,
	};
	const newValue = precipitation * conversionFactors[ unit ];
	return Number( newValue.toFixed( 2 ) );
};
/**
 * calculate wind speed
 */
export const calcWindSpeed = ( wind, toUnit, weatherUnit ) => {
	const conversionFactors = {
		imperial: {
			mph: 1,
			ms: 0.45,
			kmh: 1.61,
			kts: 0.87,
		},
		metric: {
			mph: 2.2,
			ms: 1,
			kmh: 3.6,
			kts: 1.94,
		},
	};

	const value = 'object' === typeof wind ? wind?.speed?.value : wind;

	const newVal = Math.round(
		value * conversionFactors[ weatherUnit ][ toUnit ]
	);
	return newVal;
};

/**
 * calculate Visibility
 *
 */
export const calcVisibility = ( visibility, unit ) => {
	visibility = parseInt( visibility );
	const conversionFactors = {
		m: {
			m: 1,
			km: 0.001,
			mi: 0.000621371,
			ft: 3.28084,
		},
	};
	const newVal = Math.round( visibility * conversionFactors[ 'm' ][ unit ] );
	return visibility ? newVal : 0;
};
// weather units.
export const weatherUnits = ( key ) => {
	const WEATHER_UNITS = {
		metric: '°C',
		auto_temp: '°C',
		imperial: '°F',
		hpa: 'hPa',
		mb: 'mb',
		kpa: 'kPa',
		inhg: 'inHg',
		psi: 'psi',
		mmhg: 'mmhg',
		ksc: 'kg/cm²',
		mm: 'mm',
		inch: 'inch',
		kmh: 'km/h',
		ms: 'm/s',
		mph: 'mph',
		kts: 'kn',
		km: 'km',
		mi: 'mi',
	};
	return WEATHER_UNITS[ key ] || '';
};

export const iconFolderName = ( type ) => {
	const folder_names = {
		forecast_icon_set_one: 'weather-icons',
		forecast_icon_set_two: 'weather-static-icons',
		forecast_icon_set_three: 'light-line',
		forecast_icon_set_four: 'fill-icon',
		forecast_icon_set_five: 'weather-glassmorphism',
		forecast_icon_set_six: 'animated-line',
		forecast_icon_set_seven: 'animated',
		forecast_icon_set_eight: 'medium-line',
	};
	return folder_names[ type ];
};

//sunrise and sunset calc
export const getFormatedTime = (
	timeStr,
	time_zone,
	splwTimeZone,
	splwTimeFormat
) => {
	if ( ! timeStr ) {
		return '';
	}
	const utc_zone = splwTimeZone?.replace( 'UTC', '' );
	const isoUtc = timeStr.replace( ' ', 'T' ) + 'Z';
	const offsetTime =
		splwTimeZone === 'auto'
			? new Date( isoUtc ).getTime() + time_zone * 1000
			: new Date( isoUtc ).getTime() + utc_zone * 3600000;
	const format_time = dateI18n( splwTimeFormat, offsetTime );
	return format_time;
};

// Optimized forecast title icon getter.
export const jsonStringify = ( data ) => {
	return JSON.stringify( data );
};
export const jsonParse = ( data ) => {
	return JSON.parse( data );
};
export const inArray = ( array, value ) => {
	return array.includes( value );
};
export const setItemOnLocalStorage = ( key, data ) => {
	localStorage.setItem( key, jsonStringify( data ) );
};
export const getItemFromLocalStorage = ( key ) => {
	const data = jsonParse( localStorage.getItem( key ) );
	return data;
};

export const getFormatedDate = (
	date,
	attributes,
	forecastDataObjKey = 'daily'
) => {
	const {
		blockName,
		template,
		splwTimeFormat,
		splwDateFormat,
		forecastDaysNameLength,
	} = attributes;

	const currentDate = new Date().getDate();
	const isoUtc = date?.date.replace( ' ', 'T' ) + 'Z';
	const newDate = new Date( isoUtc );
	const dayNameLength =
		forecastDaysNameLength === 'normal' || blockName === 'aqi-minimal'
			? 'l'
			: 'D';
	let dateStr = '';
	switch ( template ) {
		case 'vertical-one':
		case 'vertical-three':
		case 'horizontal-two':
		case 'tabs-one':
			dateStr = dateI18n( 'D M d', newDate );
			break;
		case 'table-one':
		case 'accordion-one':
		case 'accordion-two':
		case 'accordion-three':
		case 'accordion-four':
		case 'grid-one':
		case 'grid-two':
		case 'template':
			dateStr = dateI18n( 'M d', newDate );
			break;
		default:
			dateStr = dateI18n( dayNameLength, newDate );
	}

	const toDayNumber = new Date( date?.date ).getDate();

	if ( currentDate === toDayNumber ) {
		dateStr = 'Today';
	} else if ( currentDate + 1 === toDayNumber ) {
		dateStr = 'Tomorrow';
	}

	const formatDate =
		forecastDataObjKey === 'daily'
			? dateStr
			: dateI18n( splwDateFormat, newDate );

	return {
		date: formatDate,
		time: dateI18n( splwTimeFormat, newDate ),
		dateWithYear:
			blockName == 'aqi-minimal'
				? dateI18n( 'd M', newDate )
				: dateI18n( 'd M Y', newDate ),
	};
};

// dynamic css utils.
export const objectToCssString = ( dynamicCss ) => {
	let css = '';
	dynamicCss?.forEach( ( item ) => {
		if ( item.styles ) {
			let styles = '';
			Object.entries( item.styles ).forEach( ( [ property, value ] ) => {
				if ( value !== null && value !== undefined && value !== '' ) {
					styles += `${ property }: ${ value };`;
				}
			} );
			if ( styles ) {
				css += `${ item.selector } {${ styles }}`;
			}
		}
	} );
	return css;
};

export const generateTypoResponsive = ( attributes, device, key ) => {
	const fontSize = attributes[ `${ key }FontSize` ].device[ device ];
	const lineHeight = attributes[ `${ key }LineHeight` ].device[ device ];
	let letterSpacing = attributes[ `${ key }FontSpacing` ]?.device?.[ device ];

	if (
		letterSpacing === null ||
		letterSpacing === undefined ||
		letterSpacing === ''
	) {
		letterSpacing = 0;
	}

	return {
		...( fontSize && {
			'font-size':
				fontSize + attributes[ `${ key }FontSize` ].unit[ device ],
		} ),
		...( lineHeight && {
			'line-height':
				lineHeight + attributes[ `${ key }LineHeight` ].unit[ device ],
		} ),
		...( letterSpacing !== undefined && {
			'letter-spacing':
				letterSpacing +
				( attributes[ `${ key }FontSpacing` ]?.unit?.[ device ] ||
					'px' ),
		} ),
	};
};

/**
 * Merges duplicate CSS selectors and combines their style objects.
 */
export const filterDuplicateSelector = ( cssArray ) => {
	const selectorMap = new Map();
	cssArray.forEach( ( css ) => {
		if ( css ) {
			const { selector, styles } = css;
			if ( Object.keys( styles ).length > 0 ) {
				const existing = selectorMap.get( selector );
				selectorMap.set( selector, {
					selector,
					styles: existing
						? { ...existing.styles, ...styles }
						: styles,
				} );
			}
		}
	} );
	return Array.from( selectorMap.values() );
};

export const filterResponsiveDynamicCss = ( cssObj ) => {
	const { desktopCss, tabletCss, mobileCss } = cssObj;
	const filteredDesktopCss = filterDuplicateSelector( desktopCss );
	const filteredTabletCss = filterDuplicateSelector( tabletCss );
	const filteredMobileCss = filterDuplicateSelector( mobileCss );
	// css string for editor.
	const cssString = `${ objectToCssString(
		filteredDesktopCss
	) } @media only screen and (min-width: 600px) and (max-width: 1023px) { ${ objectToCssString(
		filteredTabletCss
	) } } @media only screen and (max-width: 599px) {${ objectToCssString(
		filteredMobileCss
	) }}`;
	return cssString;
};

export const hexToRgba = ( hex, opacity ) => {
	const bigint = parseInt( hex?.replace( '#', '' ), 16 );
	const r = ( bigint >> 16 ) & 255;
	const g = ( bigint >> 8 ) & 255;
	const b = bigint & 255;
	if ( opacity ) {
		return `rgba(${ r }, ${ g }, ${ b }, ${ opacity })`;
	} else {
		//return rgb value only if opacity is not defined
		return `${ r }, ${ g }, ${ b }`;
	}
};

// Generates a gradient background color for the graph chart based on weather conditions.
export const getTemperatureGradient = ( ctx, conditions ) => {
	// Function to get weather color with opacity
	const getWeatherColor = ( weatherDescription, opacity = 0.8 ) => {
		if ( ! weatherDescription ) return `rgba(204, 204, 204, ${ opacity })`;
		const hexColor =
			weatherColors[ weatherDescription.toLowerCase() ] || '#CCCCCC';
		return hexToRgba( hexColor, opacity );
	};
	const { width } = ctx.canvas;

	// Create horizontal gradient (0 = left, width = right)
	const gradient = ctx.createLinearGradient( 0, 0, width, 0 );

	// Calculate step size (evenly distributed)
	const step = 1 / ( conditions.length - 1 );

	// Add color stops for each weather condition
	conditions.forEach( ( condition, index ) => {
		const position = index * step;
		const color = getWeatherColor( condition, 0.8 );
		gradient.addColorStop( position, color );
	} );

	return gradient;
};

export const generateTypographyCss = (
	typography,
	enableTemplateGlobalStyle = false
) => {
	const { family, fontWeight, style, transform, decoration } = typography;
	const styles = {
		...( family &&
			! enableTemplateGlobalStyle && { 'font-family': family } ),
		...( fontWeight && { 'font-weight': fontWeight } ),
		...( style && style !== 'normal' && { 'font-style': style } ),
		...( transform !== 'none' && { 'text-transform': transform } ),
		...( decoration !== 'none' && { 'text-decoration': decoration } ),
	};
	return styles;
};

export const splwColorControl = ( color = '', isGlobalSelect = false ) => {
	const selectedColor = isGlobalSelect ? '' : color;
	return selectedColor;
};

export const getConvertedTinyColor = ( color ) => {
	const tinyColor = tinycolor( color ).isDark()
		? `${ tinycolor( color ).lighten( 10 ).toString() }`
		: `${ tinycolor( color ).brighten( 10 ).toString() }`;
	return tinyColor;
};

export const getTinyBackground = ( attributes ) => {
	const { bgColor, bgColorType, secondaryBgColor } = attributes;
	const splwBgColor = bgColorType === 'bgColor' ? bgColor : secondaryBgColor;
	const tinyBackground = getConvertedTinyColor( splwBgColor );
	return tinyBackground;
};

// this functions for toggle default values.
export const getResponsiveValue = ( attr, value ) => {
	return {
		...attr,
		device: {
			...attr.device,
			Desktop: value,
		},
	};
};
export const getSpacingValue = ( attr, value ) => {
	return {
		...attr,
		device: {
			...attr.device,
			Desktop: { ...attr.device.Desktop, ...value },
		},
	};
};

export const getWeatherIconByType = ( type ) => {
	const icon_set = {
		icon_set_one: 1,
		icon_set_two: 2,
		icon_set_three: 3,
		icon_set_four: 4,
	}[ type ];

	const additionalIcon = {
		humidity: `splwp-icon-humidity-${ icon_set }`,
		pressure: `splwp-icon-pressure-${ icon_set }`,
		wind: `splwp-icon-wind-${ icon_set }`,
		gust: `splwp-icon-wind-gust-${ icon_set }`,
		uv_index: `splwp-icon-uv-index-${ icon_set }`,
		clouds: `splwp-icon-clouds-${ icon_set }`,
		visibility: `splwp-icon-visibility-${ icon_set }`,
		sunrise: `splwp-icon-sunrise-${ icon_set }`,
		sunset: `splwp-icon-sunset-${ icon_set }`,
		temperature: `splwp-icon-temperature-${ icon_set }`,
		precipitation: `splwp-icon-precipitation-${ icon_set }`,
		rainchance: `splwp-icon-rain-chance-${ icon_set }`,
	};
	return additionalIcon;
};

export const fontFamilyToUrlGenerator = (
	typographiesArray,
	page = 'editor'
) => {
	const familyArray = typographiesArray?.filter(
		( { family } ) => family?.length > 0
	);
	const familyString = familyArray
		?.map(
			( { family, fontWeight } ) =>
				`family=${ family.replaceAll(
					' ',
					'+'
				) }:wght@${ fontWeight }&`
		)
		.join( '&' );

	const fontList =
		page === 'editor'
			? `@import url('https://fonts.googleapis.com/css2?${ familyString }display=swap');`
			: familyArray?.map(
					( { family, fontWeight } ) => `${ family }:${ fontWeight }`
			  );
	return fontList;
};

// Custom slider function to handle horizontal scrolling of weather items.
export const customSlider = ( sliderContainer ) => {
	if ( ! sliderContainer ) return;
	const sliderItems = sliderContainer.querySelectorAll(
		'.spl-weather-custom-slider-item'
	);
	const navPrev = sliderContainer.querySelector(
		'.spl-weather-custom-slider-nav-prev'
	);
	const navNext = sliderContainer.querySelector(
		'.spl-weather-custom-slider-nav-next'
	);
	let currentPosition = 0;

	// Get computed style to read the gap value
	const sliderStyles = window.getComputedStyle( sliderContainer );
	const gap = parseInt( sliderStyles.gap ) || 0;

	const firstItemWidth = sliderItems[ 0 ]?.offsetWidth || 0;
	const itemWidth = sliderItems[ 1 ]?.offsetWidth || firstItemWidth;

	// Calculate visible items including gap
	const visibleItems = Math.floor(
		sliderContainer.offsetWidth / ( itemWidth + gap )
	);

	// Scroll amount includes gaps between items
	const scrollAmount = ( visibleItems - 1 ) * ( itemWidth + gap );

	// Max position calculation includes gaps
	const maxPosition =
		( sliderItems?.length - visibleItems ) * ( itemWidth + gap ) +
		( firstItemWidth - itemWidth ); // Add the difference if first item is expanded

	// Function to update scroll position
	const updateScrollPosition = ( position ) => {
		sliderContainer.scrollTo( {
			left: position,
			behavior: 'smooth',
		} );
	};

	// Previous button click handler
	const handlePrevClick = () => {
		currentPosition = Math.max( 0, currentPosition - scrollAmount );
		updateScrollPosition( currentPosition );
		updateButtonStates( currentPosition );
	};

	// Next button click handler
	const handleNextClick = () => {
		currentPosition = currentPosition + scrollAmount;
		updateScrollPosition( currentPosition );
		updateButtonStates( currentPosition );
	};

	// Update button states
	const updateButtonStates = ( position = 0 ) => {
		if ( navPrev ) navPrev.classList.toggle( 'active', position > 0 );
		if ( navNext )
			navNext.classList.toggle( 'active', position < maxPosition );
	};

	// Add event listeners
	if ( navPrev ) {
		navPrev.addEventListener( 'click', handlePrevClick );
	}
	if ( navNext ) {
		navNext.addEventListener( 'click', handleNextClick );
	}

	// Initialize button states
	updateButtonStates();

	// Cleanup function to be called when component unmounts
	return () => {
		if ( navPrev ) {
			navPrev.removeEventListener( 'click', handlePrevClick );
		}
		if ( navNext ) {
			navNext.removeEventListener( 'click', handleNextClick );
		}
	};
};

export const hexColorToCSSFilter = ( hexColor = '#fff' ) => {
	const isHexColor = /^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/.test( hexColor );
	if ( isHexColor ) {
		const filter = hexToCSSFilter( hexColor )?.filter;
		return filter;
	} else {
		return `invert(99%) sepia(0%) saturate(2867%) hue-rotate(233deg) brightness(114%) contrast(100%)`;
	}
};

export const generateBorderCSS = ( border, borderWidth ) => {
	return {
		'border-style': border.style,
		...( border.style !== 'none' && {
			'border-color': border.color,
			'border-width': cssDataCheck( borderWidth.value, borderWidth.unit ),
		} ),
	};
};

// Modify block names that doesn't match the register info keys.
export const currentBlockTitle = ( blockName ) => {
	const blockNames = {
		vertical: 'vertical-card',
		'owm-map': 'map',
	};
	const modifiedBlockName = `sp-location-weather-pro/${
		blockNames[ blockName ] || blockName
	}`;
	return blockRegisterInfo[ modifiedBlockName ]?.title;
};

export const navigateToPricing = () => {
	window.open(
		'https://locationweather.io/pricing/',
		'_blank',
		'noopener,noreferrer'
	);
};
