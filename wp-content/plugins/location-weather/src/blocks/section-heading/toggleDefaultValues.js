const toggleDefaultValue = ( attributes ) => {
	const { headingStyle, headingPadding } = attributes;

	const defaultsValue = {
		'heading-one': {
			fullWidthBottomLine: false,
			headingBgType: 'transparent',
			headingPadding: {
				...headingPadding,
				value: {
					top: '0',
					right: '0',
					bottom: '0',
					left: '0',
				},
			},
		},
		'heading-two': {
			fullWidthBottomLine: false,
			headingBgType: 'transparent',
			headingPadding: {
				...headingPadding,
				value: {
					top: '0',
					right: '0',
					bottom: '0',
					left: '0',
				},
			},
		},
		'heading-three': {
			fullWidthBottomLine: false,
			headingBgType: 'transparent',
			headingPadding: {
				...headingPadding,
				value: {
					top: '0',
					right: '0',
					bottom: '0',
					left: '0',
				},
			},
		},
		'heading-four': {
			fullWidthBottomLine: true,
			headingBgType: 'solid',
			headingPadding: {
				...headingPadding,
				value: {
					top: '6',
					right: '12',
					bottom: '6',
					left: '12',
				},
			},
		},
	};

	const updatedValues = {
		...defaultsValue[ headingStyle ],
	};

	return updatedValues;
};
export default toggleDefaultValue;
