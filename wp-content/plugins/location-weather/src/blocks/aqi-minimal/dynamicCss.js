import color from '../../components/color';
import {
	cssDataCheck,
	filterResponsiveDynamicCss,
	boxCss,
	unit,
	generateTypographyCss,
	generateTypoResponsive,
	hexToRgba,
	inArray,
	splwColorControl,
} from '../../controls';

const responsiveCss = ( attributes, device = 'Desktop' ) => {
	const {
		uniqueId,
		aqiSummaryPadding,
		aqiSummaryMargin,
		pollutantBoxPadding,
		pollutantParametersVerticalGap,
		pollutantParametersHorizontalGap,
		pollutantAreaMargin,
		splwPadding,
		splwMaxWidth,
		enableSplwBoxShadow,
		splwBoxShadow,
		dateTimeGap,
		displayDateUpdateTime,
		detailedWeatherAndUpdateMargin,
		displayWeatherAttribution,
		weatherAttributionColor,
		weatherAttributionBgColor,
		enableSummaryAqiDesc,
		enablePollutantDetails,
		weatherAttributionTypography,
	} = attributes;

	const css = [
		{
			selector: `#${ uniqueId }.sp-location-weather-block-wrapper`,
			styles: {
				'max-width': cssDataCheck(
					splwMaxWidth.device[ device ],
					unit( splwMaxWidth, device )
				),
			},
		},

		{
			selector: `#${ uniqueId } .spl-weather-aqi-minimal-card-wrapper .spl-aqi-card-header-section`,
			styles: {
				'margin-bottom': cssDataCheck(
					dateTimeGap.device[ device ],
					unit( dateTimeGap, device )
				),
			},
		},
		{
			selector: `#${ uniqueId } .spl-aqi-card-heading`,
			styles: {
				...generateTypoResponsive(
					attributes,
					device,
					'aqiSummaryLabel'
				),
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-aqi-card-summary`,
			styles: {
				margin: cssDataCheck(
					aqiSummaryMargin.device[ device ],
					unit( aqiSummaryMargin, device )
				),
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-aqi-card-summary .spl-weather-card-location-name`,
			styles: {
				...generateTypoResponsive( attributes, device, 'locationName' ),
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-aqi-card-summary .spl-weather-card-date-time`,
			styles: {
				...generateTypoResponsive( attributes, device, 'dateTime' ),
			},
		},
		...( enableSummaryAqiDesc
			? [
					{
						selector: `#${ uniqueId } .spl-aqi-card-aqi-description`,
						styles: {
							...generateTypoResponsive(
								attributes,
								device,
								'aqiSummaryDesc'
							),
							padding: cssDataCheck(
								aqiSummaryPadding.device[ device ],
								unit( aqiSummaryPadding, device )
							),
						},
					},
			  ]
			: [] ),

		{
			selector: `#${ uniqueId }.spl-weather-aqi-minimal-card`,
			styles: {
				'padding-top': cssDataCheck(
					splwPadding.device[ device ][ 'top' ],
					unit( splwPadding, device )
				),
				'padding-bottom': cssDataCheck(
					splwPadding.device[ device ][ 'bottom' ],
					unit( splwPadding, device )
				),
				'box-shadow': boxCss(
					enableSplwBoxShadow,
					splwBoxShadow,
					'color'
				),
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-aqi-minimal-card-wrapper div:is(.spl-weather-aqi-card-summary,.spl-aqi-card-pollutant-details-wrapper)`,
			styles: {
				'padding-left': cssDataCheck(
					splwPadding.device[ device ][ 'left' ],
					unit( splwPadding, device )
				),
				'padding-right': cssDataCheck(
					splwPadding.device[ device ][ 'right' ],
					unit( splwPadding, device )
				),
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-aqi-minimal-card-wrapper .spl-aqi-card-aqi-description-wrapper`,
			styles: {
				'padding-left': cssDataCheck(
					splwPadding.device[ device ][ 'left' ],
					unit( splwPadding, device )
				),
				'padding-right': cssDataCheck(
					splwPadding.device[ device ][ 'right' ],
					unit( splwPadding, device )
				),
			},
		},
		...( enablePollutantDetails
			? [
					{
						selector: `#${ uniqueId } .spl-aqi-card-pollutant-details`,
						styles: {
							'row-gap': cssDataCheck(
								pollutantParametersVerticalGap.device[ device ],
								unit( pollutantParametersVerticalGap, device )
							),
							'column-gap': cssDataCheck(
								pollutantParametersHorizontalGap.device[
									device
								],
								unit( pollutantParametersHorizontalGap, device )
							),
						},
					},
					{
						selector: `#${ uniqueId } .spl-aqi-card-pollutant-details`,
						styles: {
							margin: cssDataCheck(
								pollutantAreaMargin.device[ device ],
								unit( pollutantAreaMargin, device )
							),
						},
					},
					{
						selector: `#${ uniqueId } .spl-aqi-card-pollutant-item`,
						styles: {
							padding: cssDataCheck(
								pollutantBoxPadding.device[ device ],
								unit( pollutantBoxPadding, device )
							),
						},
					},
					{
						selector: `#${ uniqueId } .spl-aqi-card-pollutant-item .spl-pollutant-title`,
						styles: {
							...generateTypoResponsive(
								attributes,
								device,
								'pollutantConditionLabel'
							),
						},
					},
					{
						selector: `#${ uniqueId } .spl-aqi-card-pollutant-item .spl-pollutant-value`,
						styles: {
							...generateTypoResponsive(
								attributes,
								device,
								'pollutantValue'
							),
						},
					},
			  ]
			: '' ),
		// Footer css.
		...( displayDateUpdateTime
			? [
					{
						selector: `#${ uniqueId } .spl-weather-detailed`,
						styles: {
							margin: cssDataCheck(
								detailedWeatherAndUpdateMargin.device[ device ],
								unit( detailedWeatherAndUpdateMargin, device )
							),
						},
					},
					{
						selector: `#${ uniqueId } .spl-weather-detailed.has-padding`,
						styles: {
							'padding-right': cssDataCheck(
								splwPadding.device[ device ][ 'right' ],
								unit( splwPadding, device )
							),
						},
					},
					{
						selector: ` #${ uniqueId } .spl-weather-last-updated-time`,
						styles: {
							...generateTypoResponsive(
								attributes,
								device,
								'detailedWeatherAndUpdate'
							),
						},
					},
			  ]
			: [] ),
		displayWeatherAttribution && {
			selector: `#${ uniqueId } .spl-weather-attribution`,
			styles: {
				...generateTypoResponsive(
					attributes,
					device,
					'weatherAttribution'
				),
				...generateTypographyCss( weatherAttributionTypography ),
				color: weatherAttributionColor,
			},
		},
		displayWeatherAttribution && {
			selector: `#${ uniqueId } .spl-weather-card-footer`,
			styles: {
				...( '' !== weatherAttributionBgColor && {
					'background-color': weatherAttributionBgColor,
				} ),
				...( '' === weatherAttributionBgColor && {
					'background-color': getConvertedTinyColor(
						aqiForecastAccordionBg?.Color
					),
				} ),
			},
		},
	];
	return css;
};

const dynamicCss = ( attributes, page = 'editor' ) => {
	const {
		uniqueId,
		bgColorType,
		bgColor,
		bgGradient,
		aqiSummaryToggleBorder,
		aqiSummaryLabelColors,
		aqiSummaryLabelTypography,
		pollutantConditionLabelColors,
		pollutantConditionLabelTypography,
		pollutantValueColors,
		pollutantValueTypography,
		pollutantBoxBgColor,
		aqiPollutantDetailsBorder,
		aqiSummaryTextColor,
		splwBorder,
		dateTimeColor,
		dateTimeTypography,
		locationNameTypography,
		splwHideOnDesktop,
		splwHideOnTablet,
		splwHideOnMobile,
		aqiSummaryDescTypography,
		aqiSummaryDescBgColor,
		bgOverlay,
		enableSummaryAqiDesc,
		enablePollutantDetails,
		splwBorderRadius,
		splwBorderWidth,
		aqiSummaryToggleBorderWidth,
		aqiSummaryToggleRadius,
		aqiPollutantDetailsBorderWidth,
		aqiPollutantDetailsBorderRadius,
		templateTypography,
		templatePrimaryColor,
		templateSecondaryColor,
		enableTemplateGlobalStyle,
		locationNameColor,
		displayDateUpdateTime,
		detailedWeatherAndUpdateTypography,
		detailedWeatherAndUpdateColor,
		secondaryBgColor,
		bgImage,
		imageType,
		bgImageSize,
		bgImageRepeat,
		bgImageAttachment,
		bgImagePosition,
	} = attributes;

	// const aqiLayoutBg = {
	// 	bgColor: bgColor,
	// 	gradient: bgGradient,
	// }[ bgColorType ];

	const aqiLayoutBg = {
		transparent: '',
		bgColor: bgColor,
		gradient: bgGradient,
		image:
			bgImage?.url && imageType === 'custom'
				? `url(${ bgImage.url })`
				: '',
	}[ bgColorType ];

	const splwBgImageCss = {
		'background-position': bgImagePosition,
		'background-attachment': bgImageAttachment,
		'background-repeat': bgImageRepeat,
		'background-size': bgImageSize,
	};

	let desktopCss = [
		...responsiveCss( attributes, 'Desktop' ),
		// global css.
		{
			selector: `#${ uniqueId }.sp-location-weather-block-wrapper`,
			styles: {
				'font-family': templateTypography?.family,
				color: templatePrimaryColor,
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-aqi-card-summary .spl-weather-card-location-name`,
			styles: {
				color: splwColorControl(
					locationNameColor,
					enableTemplateGlobalStyle
				),
				display: 'inline',
				...generateTypographyCss( locationNameTypography ),
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-aqi-card-summary .spl-pollutant-scale-wrapper`,
			styles: {
				color: splwColorControl(
					locationNameColor,
					enableTemplateGlobalStyle
				),
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-aqi-card-summary .spl-weather-card-date-time`,
			styles: {
				color: splwColorControl(
					dateTimeColor,
					enableTemplateGlobalStyle
				),
				...generateTypographyCss( dateTimeTypography ),
			},
		},
		{
			selector: `#${ uniqueId } .spl-aqi-card-heading`,
			styles: {
				color: splwColorControl(
					aqiSummaryLabelColors,
					enableTemplateGlobalStyle
				),
				...generateTypographyCss( aqiSummaryLabelTypography ),
			},
		},
		{
			selector: `#${ uniqueId }.spl-weather-aqi-minimal-card`,
			styles: {
				background: aqiLayoutBg,
				...( bgColorType === 'image' && splwBgImageCss ),
				'border-style': splwBorder?.style,
				'border-color': splwBorder?.color,
				'border-width': cssDataCheck(
					splwBorderWidth.value,
					splwBorderWidth.unit
				),
				'border-radius': cssDataCheck(
					splwBorderRadius.value,
					splwBorderRadius.unit
				),
			},
		},
		...( enableSummaryAqiDesc
			? [
					{
						selector: `#${ uniqueId } .spl-aqi-card-aqi-description`,
						styles: {
							background:
								splwColorControl(
									aqiSummaryDescBgColor,
									enableTemplateGlobalStyle
								) || secondaryBgColor,
							'border-style': aqiSummaryToggleBorder?.style,
							'border-color': aqiSummaryToggleBorder?.color,
							'border-width': cssDataCheck(
								aqiSummaryToggleBorderWidth.value,
								aqiSummaryToggleBorderWidth.unit
							),
							'border-radius': cssDataCheck(
								aqiSummaryToggleRadius.value,
								aqiSummaryToggleRadius.unit
							),
						},
					},
					{
						selector: `#${ uniqueId } .spl-aqi-card-aqi-description`,
						styles: {
							color: splwColorControl(
								aqiSummaryTextColor,
								enableTemplateGlobalStyle
							),
							...generateTypographyCss(
								aqiSummaryDescTypography
							),
						},
					},
			  ]
			: [] ),
		{
			selector: `#${ uniqueId } .spl-aqi-card-progress-bar .spl-progress-text`,
			styles: {
				fill:
					splwColorControl(
						aqiSummaryLabelColors,
						enableTemplateGlobalStyle
					) || templatePrimaryColor,
				color: splwColorControl(
					aqiSummaryLabelColors,
					enableTemplateGlobalStyle
				),
			},
		},
		{
			selector: `#${ uniqueId } .spl-aqi-card-condition .title`,
			styles: {
				color: splwColorControl(
					aqiSummaryLabelColors,
					enableTemplateGlobalStyle
				),
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-aqi-gauge-center-text .spl-weather-aqi-text`,
			styles: {
				color: splwColorControl(
					aqiSummaryLabelColors,
					enableTemplateGlobalStyle
				),
			},
		},
		...( enablePollutantDetails
			? [
					{
						selector: `#${ uniqueId } .spl-aqi-card-pollutant-item:not(.spl-aqi-pollutant-indicator)`,
						styles: {
							background:
								splwColorControl(
									pollutantBoxBgColor,
									enableTemplateGlobalStyle
								) || secondaryBgColor,
							'border-style': aqiPollutantDetailsBorder?.style,
							'border-color': aqiPollutantDetailsBorder?.color,
							'border-width': cssDataCheck(
								aqiPollutantDetailsBorderWidth.value,
								aqiPollutantDetailsBorderWidth.unit
							),
							'border-radius': cssDataCheck(
								aqiPollutantDetailsBorderRadius.value,
								aqiPollutantDetailsBorderRadius.unit
							),
						},
					},
					{
						selector: `#${ uniqueId } .spl-aqi-card-pollutant-item`,
						styles: {
							'border-style': aqiPollutantDetailsBorder?.style,
							'border-width': cssDataCheck(
								aqiPollutantDetailsBorderWidth.value,
								aqiPollutantDetailsBorderWidth.unit
							),
							'border-radius': cssDataCheck(
								aqiPollutantDetailsBorderRadius.value,
								aqiPollutantDetailsBorderRadius.unit
							),
						},
					},
					{
						selector: `#${ uniqueId } .spl-aqi-card-pollutant-item:not(.spl-aqi-pollutant-indicator):hover`,
						styles: {
							'border-color':
								aqiPollutantDetailsBorder?.hoverColor,
						},
					},
					{
						selector: `#${ uniqueId } .spl-aqi-card-pollutant-item .spl-pollutant-title`,
						styles: {
							color:
								splwColorControl(
									pollutantConditionLabelColors,
									enableTemplateGlobalStyle
								) || templateSecondaryColor,
							...generateTypographyCss(
								pollutantConditionLabelTypography
							),
						},
					},
					{
						selector: `#${ uniqueId } .spl-aqi-card-pollutant-item .spl-pollutant-value`,
						styles: {
							color: splwColorControl(
								pollutantValueColors,
								enableTemplateGlobalStyle
							),
							...generateTypographyCss(
								pollutantValueTypography
							),
						},
					},
			  ]
			: '' ),
		inArray( [ 'video', 'image' ], bgColorType ) && {
			selector:
				bgColorType === 'image'
					? `#${ uniqueId }.spl-weather-aqi-minimal-card::after`
					: `#${ uniqueId } .spl-weather-video-player::after`,
			styles: {
				inset: 0,
				content: "''",
				position: 'absolute',
				'background-color': bgOverlay,
			},
		},
		displayDateUpdateTime && {
			selector: ` #${ uniqueId } .spl-weather-last-updated-time`,
			styles: {
				...generateTypographyCss( detailedWeatherAndUpdateTypography ),
				color: detailedWeatherAndUpdateColor,
			},
		},
	];
	let tabletCss = [ ...responsiveCss( attributes, 'Tablet' ) ];
	let mobileCss = [ ...responsiveCss( attributes, 'Mobile' ) ];

	// visibility show hide css.
	if ( splwHideOnDesktop ) {
		desktopCss = [
			...desktopCss,
			{
				selector: `#${ uniqueId }`,
				styles: {
					display: 'none',
				},
			},
		];
	}

	if ( splwHideOnTablet ) {
		tabletCss = [
			{
				selector: `#${ uniqueId }`,
				styles: {
					display: 'none',
				},
			},
		];
	} else {
		tabletCss = [
			...tabletCss,
			{
				selector: `#${ uniqueId }`,
				styles: {
					display: 'block',
				},
			},
		];
	}

	if ( splwHideOnMobile ) {
		mobileCss = [
			{
				selector: `#${ uniqueId }`,
				styles: {
					display: 'none',
				},
			},
		];
	} else {
		mobileCss = [
			...mobileCss,
			{
				selector: `#${ uniqueId }`,
				styles: {
					display: 'block',
				},
			},
		];
	}

	const cssObj = {
		desktopCss,
		tabletCss,
		mobileCss,
	};

	const dynamicCss = filterResponsiveDynamicCss( cssObj, page );

	return dynamicCss;
};
export default dynamicCss;
