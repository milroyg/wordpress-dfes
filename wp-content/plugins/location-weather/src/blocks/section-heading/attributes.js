export const SectionHeadingAttributes = {
	uniqueId: {
		type: 'string',
		default: '',
	},
	isPreview: {
		type: 'boolean',
		default: false,
	},
	fontLists: {
		type: 'string',
	},
	// Heading attributes.
	headingLabel: {
		type: 'string',
		default: 'This is a Title',
	},
	headingStyle: {
		type: 'string',
		default: 'heading-one',
	},
	headingHtmlTag: {
		type: 'string',
		default: 'h2',
	},
	headingAlignment: {
		type: 'string',
		default: 'left',
	},
	headingColor: {
		type: 'string',
		default: '#2f2f2f',
	},
	headingTypography: {
		type: 'object',
		default: {
			family: '',
			fontWeight: '500',
			style: 'normal',
			transform: '',
			decoration: 'none',
		},
	},
	headingFontSize: {
		type: 'object',
		default: {
			device: {
				Desktop: 36,
				Tablet: '',
				Mobile: '',
			},
			unit: {
				Desktop: 'px',
				Tablet: 'px',
				Mobile: 'px',
			},
		},
	},
	headingLineHeight: {
		type: 'object',
		default: {
			device: {
				Desktop: 43,
				Tablet: '',
				Mobile: '',
			},
			unit: {
				Desktop: 'px',
				Tablet: 'px',
				Mobile: 'px',
			},
		},
	},
	headingFontSpacing: {
		type: 'object',
		default: {
			device: {
				Desktop: 0,
				Tablet: '',
				Mobile: '',
			},
			unit: {
				Desktop: 'px',
				Tablet: 'px',
				Mobile: 'px',
			},
		},
	},
	headingBgType: {
		type: 'string',
		default: 'transparent',
	},
	headingBgColor: {
		type: 'string',
		default: '#FFE0B3',
	},
	headingBgGradient: {
		type: 'string',
		default:
			'linear-gradient(135deg, #A1C4FD 0%, #C2E9FB 50%, #E0EAFC 100%)',
	},
	headingBorder: {
		type: 'object',
		default: {
			style: 'none',
			color: '#DDDDDD',
		},
	},
	headingBorderRadius: {
		type: 'object',
		default: {
			value: {
				top: '0',
				right: '0',
				bottom: '0',
				left: '0',
			},
			unit: 'px',
			allChange: true,
		},
	},
	headingBorderWidth: {
		type: 'object',
		default: {
			value: {
				top: '0',
				right: '0',
				bottom: '0',
				left: '0',
			},
			unit: 'px',
			allChange: true,
		},
	},
	headingPadding: {
		type: 'object',
		default: {
			value: {
				top: '0',
				right: '0',
				bottom: '0',
				left: '0',
			},
			unit: 'px',
			allChange: true,
		},
	},
	// Sub Heading attributes.
	subHeading: {
		type: 'boolean',
		default: false,
	},
	subHeadingLabel: {
		type: 'string',
		default: 'Sub Heading',
	},
	subHeadingPosition: {
		type: 'string',
		default: 'bottom',
	},
	subHeadingColor: {
		type: 'string',
		default: '#2F2F2F',
	},
	subHeadingTypography: {
		type: 'object',
		default: {
			family: '',
			fontWeight: '500',
			style: 'normal',
			transform: '',
			decoration: 'none',
		},
	},
	subHeadingFontSize: {
		type: 'object',
		default: {
			device: {
				Desktop: 18,
				Tablet: '',
				Mobile: '',
			},
			unit: {
				Desktop: 'px',
				Tablet: 'px',
				Mobile: 'px',
			},
		},
	},
	subHeadingLineHeight: {
		type: 'object',
		default: {
			device: {
				Desktop: 21,
				Tablet: '',
				Mobile: '',
			},
			unit: {
				Desktop: 'px',
				Tablet: 'px',
				Mobile: 'px',
			},
		},
	},
	subHeadingFontSpacing: {
		type: 'object',
		default: {
			device: {
				Desktop: 0,
				Tablet: '',
				Mobile: '',
			},
			unit: {
				Desktop: 'px',
				Tablet: 'px',
				Mobile: 'px',
			},
		},
	},
	subHeadingBgType: {
		type: 'string',
		default: 'transparent',
	},
	subHeadingBgColor: {
		type: 'string',
		default: '#FFE0B3',
	},
	subHeadingBgGradient: {
		type: 'string',
		default:
			'linear-gradient(135deg, #A1C4FD 0%, #C2E9FB 50%, #E0EAFC 100%)',
	},
	subHeadingBorder: {
		type: 'object',
		default: {
			style: 'none',
			color: '#DDDDDD',
		},
	},
	subHeadingBorderRadius: {
		type: 'object',
		default: {
			value: {
				top: '0',
				right: '0',
				bottom: '0',
				left: '0',
			},
			unit: 'px',
			allChange: true,
		},
	},
	subHeadingBorderWidth: {
		type: 'object',
		default: {
			value: {
				top: '1',
				right: '1',
				bottom: '1',
				left: '1',
			},
			unit: 'px',
			allChange: true,
		},
	},
	subHeadingPadding: {
		type: 'object',
		default: {
			value: {
				top: '0',
				right: '0',
				bottom: '0',
				left: '0',
			},
			unit: 'px',
			allChange: true,
		},
	},
	bottomLineColor: {
		type: 'string',
		default: '#F26C0D',
	},
	bottomLineThickness: {
		type: 'object',
		default: {
			value: 2,
			unit: 'px',
		},
	},
	bottomLineWidth: {
		type: 'object',
		default: {
			value: 140,
			unit: 'px',
		},
	},
	bottomLineHorPosition: {
		type: 'object',
		default: {
			value: 0,
			unit: '%',
		},
	},
	sideLineType: {
		type: 'string',
		default: 'both',
	},
	sideLineColor: {
		type: 'string',
		default: '#F26C0D',
	},
	sideLineThickness: {
		type: 'object',
		default: {
			value: 2,
			unit: 'px',
		},
	},
	sideLineWidth: {
		type: 'object',
		default: {
			value: 50,
			unit: 'px',
		},
	},
	sideLineGap: {
		type: 'object',
		default: {
			value: 12,
			unit: 'px',
		},
	},
	fullWidthBottomLine: {
		type: 'boolean',
		default: false,
	},
	fullWidthBottomLineColor: {
		type: 'string',
		default: '#FFE0B3',
	},
	fullWidthBottomLineThickness: {
		type: 'object',
		default: {
			value: 2,
			unit: 'px',
		},
	},
	// Section Heading Container attributes.
	headingContainerBgType: {
		type: 'string',
		default: 'transparent',
	},
	headingContainerBgColor: {
		type: 'string',
		default: '#2f2f2f',
	},
	headingContainerBgGradient: {
		type: 'string',
		default:
			'linear-gradient(135deg, #A1C4FD 0%, #C2E9FB 50%, #E0EAFC 100%)',
	},
	headingContainerBorder: {
		type: 'object',
		default: {
			style: 'none',
			color: '#DDDDDD',
		},
	},
	headingContainerBorderRadius: {
		type: 'object',
		default: {
			value: {
				top: '0',
				right: '0',
				bottom: '0',
				left: '0',
			},
			unit: 'px',
			allChange: true,
		},
	},
	headingContainerBorderWidth: {
		type: 'object',
		default: {
			value: {
				top: '0',
				right: '0',
				bottom: '0',
				left: '0',
			},
			unit: 'px',
			allChange: false,
		},
	},
	headingContainerPadding: {
		type: 'object',
		default: {
			value: {
				top: '0',
				right: '0',
				bottom: '0',
				left: '0',
			},
			unit: 'px',
			allChange: true,
		},
	},
	headingContainerMargin: {
		type: 'object',
		default: {
			value: {
				top: '0',
				right: '0',
				bottom: '40',
				left: '0',
			},
			unit: 'px',
			allChange: false,
		},
	},
	subHeadingGap: {
		type: 'object',
		default: {
			value: 10,
			unit: 'px',
		},
	},
};
