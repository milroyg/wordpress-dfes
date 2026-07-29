import { getResponsiveValue, getSpacingValue } from '../../controls';

const toggleDefaultValue = ( attributes ) => {
	const {
		template,
		locationNameFontSize,
		locationNameLineHeight,
		temperatureScaleFontSize,
		temperatureScaleLineHeight,
		temperatureUnitFontSize,
		temperatureUnitLineHeight,
		weatherConditionIconSize,
		forecastContainerMargin,
		additionalDataVerticalGap,
		regionalPreferenceMargin,
		dateTimeGap,
		forecastContainerPadding,
		additionalDataMargin,
		temperatureScaleMargin,
		additionalDataPadding,
		active_additional_data_layout,
	} = attributes;

	const defaultsValue = {
		'vertical-one': {
			locationNameFontSize: getResponsiveValue(
				locationNameFontSize,
				27
			),
			locationNameLineHeight: getResponsiveValue(
				locationNameLineHeight,
				38
			),
			weatherConditionIconSize: getResponsiveValue(
				weatherConditionIconSize,
				60
			),
			// new attr.
			dateTimeGap: getResponsiveValue( dateTimeGap, 6 ),
			regionalPreferenceMargin: getSpacingValue(
				regionalPreferenceMargin,
				{
					top: '0',
					bottom: '18',
					left: '0',
					right: '0',
				}
			),
			temperatureScaleMargin: getSpacingValue( temperatureScaleMargin, {
				top: '0',
				bottom: '8',
				left: '0',
				right: '0',
			} ),
			forecastContainerMargin: getSpacingValue( forecastContainerMargin, {
				top: '6',
				bottom: '0',
				left: '0',
				right: '0',
			} ),
		},
		'vertical-three': {
			locationNameFontSize: getResponsiveValue(
				locationNameFontSize,
				14
			),
			locationNameLineHeight: getResponsiveValue(
				locationNameLineHeight,
				20
			),
			weatherConditionIconSize: getResponsiveValue(
				weatherConditionIconSize,
				58
			),
			// new attr.
			dateTimeGap: getResponsiveValue( dateTimeGap, 8 ),
			regionalPreferenceMargin: getSpacingValue(
				regionalPreferenceMargin,
				{
					top: '0',
					bottom: '8',
					left: '0',
					right: '0',
				}
			),
			temperatureScaleMargin: getSpacingValue( temperatureScaleMargin, {
				top: '6',
				bottom: '0',
				left: '0',
				right: '0',
			} ),
			forecastContainerMargin: getSpacingValue( forecastContainerMargin, {
				top: '6',
				bottom: '0',
				left: '0',
				right: '0',
			} ),
		},
	};

	const updatedValues = {
		...defaultsValue[ template ],
		additionalDataPadding: getSpacingValue( additionalDataPadding, {
			top: '3',
			bottom: '3',
			left: '2',
			right: '2',
		} ),
	};

	return updatedValues;
};
export default toggleDefaultValue;
