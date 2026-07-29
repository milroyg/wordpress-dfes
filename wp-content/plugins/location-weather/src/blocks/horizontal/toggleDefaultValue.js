import { getResponsiveValue, getSpacingValue } from '../../controls';

const toggleDefaultValue = ( attributes ) => {
	const {
		template,
		temperatureScaleFontSize,
		temperatureScaleLineHeight,
		additionalDataMargin,
		forecastContainerPadding,
		forecastContainerMargin,
		additionalDataPadding,
		forecastCarouselColumns,
		forecastDataIconSize,
		weatherDescFontSize,
		splwMaxWidth,
		additionalDataVerticalGap,
		temperatureScaleMargin,
		dateTimeGap,
		regionalPreferenceMargin,
		forecastLabelTypography,
		weatherConditionIconSize,
		temperatureUnitFontSize,
		temperatureUnitLineHeight,
	} = attributes;

	const defaultsValue = {
		'horizontal-one': {
			dateTimeGap: getResponsiveValue( dateTimeGap, 8 ),
			forecastCarouselColumns: getResponsiveValue(
				forecastCarouselColumns,
				5
			),
			additionalDataPadding: getSpacingValue( additionalDataPadding, {
				top: '3',
				bottom: '3',
				left: '2',
				right: '2',
			} ),
			additionalDataMargin: getSpacingValue( additionalDataMargin, {
				top: '0',
				bottom: '0',
				left: '0',
				right: '0',
			} ),
			forecastContainerPadding: getSpacingValue(
				forecastContainerPadding,
				{ top: '14', bottom: '0', left: '0', right: '0' }
			),
			forecastContainerMargin: getSpacingValue( forecastContainerMargin, {
				top: '8',
				bottom: '0',
				left: '0',
				right: '0',
			} ),
			forecastLabelTypography: {
				...forecastLabelTypography,
				fontWeight: 500,
			},
			regionalPreferenceMargin: getSpacingValue(
				regionalPreferenceMargin,
				{
					top: '0',
					bottom: '8',
					left: '0',
					right: '0',
				}
			),
		},
	};

	const otherAttributes = {
		splwMaxWidth: getResponsiveValue( splwMaxWidth, '800' ),
		weatherConditionIconSize: getResponsiveValue(
			weatherConditionIconSize,
			'60'
		),
		temperatureUnitFontSize: getResponsiveValue(
			temperatureUnitFontSize,
			16
		),
		temperatureUnitLineHeight: getResponsiveValue(
			temperatureUnitLineHeight,
			21
		),
		forecastDataIconSize: getResponsiveValue( forecastDataIconSize, 48 ),
		weatherDescFontSize: getResponsiveValue( weatherDescFontSize, 16 ),
		temperatureScaleFontSize: getResponsiveValue(
			temperatureScaleFontSize,
			48
		),
		temperatureScaleLineHeight: getResponsiveValue(
			temperatureScaleLineHeight,
			56
		),
		additionalDataVerticalGap: getResponsiveValue(
			additionalDataVerticalGap,
			2
		),
		temperatureScaleMargin: getSpacingValue( temperatureScaleMargin, {
			top: '0',
			bottom: '8',
			left: '0',
			right: '0',
		} ),
	};

	const blockAttributes = {
		...defaultsValue[ template ],
		...otherAttributes,
	};
	return blockAttributes;
};
export default toggleDefaultValue;
