import { getSpacingValue } from '../../controls';

const toggleDefaultValue = ( attributes ) => {
	const { template, splwPadding, additionalDataMargin } = attributes;

	const tabsBlockAttributes = {
		'tabs-one': {
			forecastData: [
				{ id: 1, name: 'temperature', value: true },
				{ id: 2, name: 'precipitation', value: true },
				{ id: 4, name: 'wind', value: true },
				{ id: 5, name: 'humidity', value: true },
				{ id: 6, name: 'pressure', value: true },
				{ id: 3, name: 'rainchance', value: true },
				{ id: 7, name: 'snow', value: false },
			],
			splwPadding: getSpacingValue( splwPadding, {
				top: '26',
				bottom: '26',
				left: '26',
				right: '26',
			} ),
			additionalDataMargin: getSpacingValue( additionalDataMargin, {
				top: '14',
				bottom: '8',
				left: '0',
				right: '0',
			} ),
		},
	};
	return tabsBlockAttributes[ template ];
};

export default toggleDefaultValue;
