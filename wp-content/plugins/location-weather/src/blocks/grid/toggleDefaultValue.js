import {
	additionalDataOption,
	getResponsiveValue,
	getSpacingValue,
} from '../../controls';

const toggleDefaultValue = ( attributes ) => {
	const {
		template,
		locationNameFontSize,
		locationNameLineHeight,
		temperatureScaleFontSize,
		additionalDataPadding,
		temperatureUnitFontSize,
		forecastCarouselColumns,
		regionalPreferenceMargin,
		locationNameTypography,
		weatherConditionIconSize,
		temperatureUnitTypography,
		weatherDescTypography,
		temperatureScaleLineHeight,
		temperatureUnitLineHeight,
		hourlyForecastPadding,
	} = attributes;

	const blockAttributes = {
		temperatureUnitFontSize: getResponsiveValue(
			temperatureUnitFontSize,
			template === 'grid-one' ? 21 : 16
		),
		temperatureUnitLineHeight: getResponsiveValue(
			temperatureUnitLineHeight,
			template === 'grid-one' ? 36 : 30
		),
		forecastCarouselColumns: getResponsiveValue(
			forecastCarouselColumns,
			template === 'grid-two' ? 7 : 11
		),
		locationNameFontSize: getResponsiveValue(
			locationNameFontSize,
			template === 'grid-one' ? 14 : 32
		),
		locationNameLineHeight: getResponsiveValue(
			locationNameLineHeight,
			template === 'grid-one' ? 16 : 38
		),
		regionalPreferenceMargin: getSpacingValue( regionalPreferenceMargin, {
			top: '0',
			bottom: template === 'grid-one' ? '30' : '8',
			left: '0',
			right: '0',
		} ),
		additionalDataPadding: getSpacingValue( additionalDataPadding, {
			top: template === 'grid-one' ? '2' : '20',
			bottom: template === 'grid-one' ? '2' : '20',
			left: template === 'grid-one' ? '2' : '20',
			right: template === 'grid-one' ? '2' : '20',
		} ),
		hourlyForecastPadding: getSpacingValue( hourlyForecastPadding, {
			top: '20',
			bottom: '20',
			left: template === 'grid-one' ? '24' : '20',
			right: '20',
		} ),
		temperatureUnitTypography: {
			...temperatureUnitTypography,
			fontWeight: template === 'grid-one' ? 400 : 500,
		},
		weatherDescTypography: {
			...weatherDescTypography,
			fontWeight: template === 'grid-one' ? 500 : 600,
		},
		weatherConditionIconSize: getResponsiveValue(
			weatherConditionIconSize,
			template === 'grid-one' ? 88 : 52
		),
		additionalDataOptions: additionalDataOption,
		locationNameTypography: {
			...locationNameTypography,
			fontWeight: template === 'grid-one' ? 600 : 400,
		},
	};

	return blockAttributes;
};

export default toggleDefaultValue;
