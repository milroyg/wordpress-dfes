import classNames from 'classnames';
import {
	boxCss,
	cssDataCheck,
	generateTypographyCss,
	hexColorToCSSFilter,
	inArray,
	splwColorControl,
} from '../../controls';
import responsiveCss from './responsiveCss';
import forecastDynamicCss from './templates/forecastData/dynamicCss';
import additionalDataDynamicCss from './templates/additionalData/dynamicCss';

const sharedDynamicCss = ( attributes ) => {
	const { forecastDesktopCss, forecastTabletCss, forecastMobileCss } =
		forecastDynamicCss( attributes );
	const {
		weatherDetailsDesktopCss,
		weatherDetailsTabletCss,
		weatherDetailsMobileCss,
	} = additionalDataDynamicCss( attributes );

	const {
		uniqueId,
		template,
		blockName,
		bgColorType,
		bgColor,
		bgGradient,
		bgImage,
		bgImagePosition,
		bgImageAttachment,
		bgImageRepeat,
		bgImageSize,
		splwBorder,
		temperatureScaleTypography,
		temperatureUnitTypography,
		weatherDescTypography,
		imageType,
		locationNameColor,
		locationNameTypography,
		dateTimeColor,
		dateTimeTypography,
		temperatureScaleColor,
		weatherDescColor,
		detailedWeatherAndUpdateColor,
		weatherAttributionColor,
		weatherAttributionBgColor,
		displayWeatherAttribution,
		displayDateUpdateTime,
		tablePreferenceBorder,
		tableHeaderBgColor,
		tableEvenRowColor,
		tableOddRowColor,
		tableHeaderColor,
		showLocationName,
		showCurrentTime,
		showCurrentDate,
		displayTemperature,
		displayWeatherConditions,
		detailedWeatherAndUpdateTypography,
		weatherAttributionTypography,
		bgOverlay,
		splwHideOnDesktop,
		splwHideOnTablet,
		splwHideOnMobile,
		searchPosition,
		splwBorderWidth,
		splwBorderRadius,
		enableSplwBoxShadow,
		splwBoxShadow,
		tablePreferenceBorderWidth,
		weatherConditionIcon,
		weatherConditionIconType,
		weatherConditionIconColor,
		templateTypography,
		templatePrimaryColor,
		templateSecondaryColor,
		forecastTabsLabelColor,
		enableTemplateGlobalStyle,
		sunOrbitColor,
		sunOrbitIconColor,
	} = attributes;

	const splwBackground = {
		transparent: '',
		bgColor: bgColor,
		gradient: bgGradient,
	}[ bgColorType ];

	const isCurrentWeatherCard = inArray( [ 'grid-one' ], template );

	const templateWrapperSelector = classNames( {
		' .spl-weather-tabs-block-content': blockName === 'tabs',
		' .spl-weather-table-current-data': blockName === 'table',
		' .spl-weather-current-weather-card':
			isCurrentWeatherCard || blockName === 'grid',
		'.sp-location-weather-block-wrapper': inArray(
			[ 'vertical', 'horizontal' ],
			blockName
		),
	} );

	const templateBorderSelector = classNames( `#${ uniqueId }`, {
		'.spl-weather-current-weather-card': isCurrentWeatherCard,
		'.spl-weather-tabs-block-content': blockName === 'tabs',
		[ template === 'table-one'
			? '.spl-weather-current-data-table :is(table, th, td, .spl-weather-details)'
			: ':is(.spl-weather-table-current-data, .spl-weather-details)' ]:
			blockName === 'table',
	} );

	const templateShadowSelector = classNames( `#${ uniqueId }`, {
		'.spl-weather-tabs-block-content': blockName === 'tabs',
		'.spl-weather-table-current-data': blockName === 'table',
		'.spl-weather-current-weather-card': isCurrentWeatherCard,
	} );

	const isShowSunOrbit =
		inArray(
			[
				'vertical-two',
				'vertical-six',
				'horizontal-four',
				'tabs-one',
				'table-two',
			],
			template
		) || blockName === 'combined';

	let sharedDesktopCss = [
		...forecastDesktopCss,
		...weatherDetailsDesktopCss,
		...responsiveCss( attributes, 'Desktop' ),
		// global css.
		{
			selector: `#${ uniqueId }.sp-location-weather-block-wrapper`,
			styles: {
				'font-family': templateTypography?.family,
				color: templatePrimaryColor,
			},
		},
		blockName === 'combined' && {
			selector: `#${ uniqueId } .spl-weather-combined-tab-nav-item:not(.active)`,
			styles: {
				color: templateSecondaryColor,
			},
		},
		inArray( [ 'horizontal', 'grid' ], blockName ) && {
			selector: `#${ uniqueId } .spl-weather-forecast-tab:not(.active)`,
			styles: {
				color: templateSecondaryColor,
			},
		},
		// global css end.
		// container css.
		{
			selector: `#${ uniqueId }${ templateWrapperSelector }`,
			styles: {
				background: splwBackground,
				transition: '0.3s',
				...( blockName !== 'table' && {
					'border-style': splwBorder?.style,
					'border-color': splwBorder?.color,
					...( inArray( [ 'vertical', 'horizontal' ], blockName ) && {
						'border-width': cssDataCheck(
							splwBorderWidth.value,
							splwBorderWidth.unit
						),
						'border-radius': cssDataCheck(
							splwBorderRadius.value,
							splwBorderRadius.unit
						),
						'box-shadow': boxCss(
							enableSplwBoxShadow,
							splwBoxShadow,
							'color'
						),
					} ),
				} ),
			},
		},
		// Apply border and shadow in different selector for below blocks
		...( inArray( [ 'tabs', 'table' ], blockName ) ||
		template === 'grid-one'
			? [
					{
						selector: templateBorderSelector,
						styles: {
							'border-width': cssDataCheck(
								splwBorderWidth.value,
								splwBorderWidth.unit
							),
						},
					},
					{
						selector: templateShadowSelector,
						styles: {
							'border-radius': cssDataCheck(
								splwBorderRadius.value,
								splwBorderRadius.unit
							),
							'box-shadow': boxCss(
								enableSplwBoxShadow,
								splwBoxShadow,
								'color'
							),
							...( blockName === 'table' && {
								overflow: 'hidden',
							} ),
						},
					},
			  ]
			: [] ),
		{
			selector: `#${ uniqueId }${ templateWrapperSelector }:hover`,
			styles: {
				'border-color': splwBorder?.hoverColor,
			},
		},
		// current weather.
		weatherConditionIcon &&
			inArray(
				[
					'forecast_icon_set_three',
					'forecast_icon_set_eight',
					'forecast_icon_set_four',
				],
				weatherConditionIconType
			) && {
				selector: `#${ uniqueId } .spl-weather-condition-icon img`,
				styles: {
					filter: hexColorToCSSFilter( weatherConditionIconColor ),
				},
			},
		// location name css.
		showLocationName && {
			selector: `#${ uniqueId } .spl-weather-card-location-name`,
			styles: {
				fill: enableTemplateGlobalStyle
					? templatePrimaryColor
					: locationNameColor || templatePrimaryColor,
				color: splwColorControl(
					locationNameColor,
					enableTemplateGlobalStyle
				),
				...generateTypographyCss(
					locationNameTypography,
					enableTemplateGlobalStyle
				),
			},
		},
		// date time css.
		( showCurrentTime || showCurrentDate ) && {
			selector: `#${ uniqueId } .spl-weather-card-date-time`,
			styles: {
				color: splwColorControl(
					dateTimeColor,
					enableTemplateGlobalStyle
				),
				...generateTypographyCss(
					dateTimeTypography,
					enableTemplateGlobalStyle
				),
				gap: '6px',
				...( inArray( [ 'vertical-one', 'table-one' ], template ) && {
					'justify-content': 'center',
				} ),
			},
		},
		( showLocationName || showCurrentTime || showCurrentDate ) && {
			selector: `#${ uniqueId } .spl-weather-header-info-wrapper`,
			styles: {
				...( inArray( [ 'vertical-one' ], template ) && {
					'flex-direction': 'column',
				} ),
				...( inArray( [ 'tabs', 'table' ], blockName ) && {
					'flex-direction': 'column',
				} ),
				...( template === 'vertical-two' && {
					'flex-direction': 'column-reverse',
				} ),
				...( inArray(
					[
						'vertical-two',
						'vertical-three',
						'horizontal-one',
						'table-one',
					],
					template
				) && { 'align-items': 'center' } ),
			},
		},
		// current weather css.
		...( displayTemperature
			? [
					{
						selector: `#${ uniqueId } .spl-weather-card-current-temperature`,
						styles: {
							color: splwColorControl(
								temperatureScaleColor,
								enableTemplateGlobalStyle
							),
						},
					},
					{
						selector: `#${ uniqueId } .spl-weather-current-temp`,
						styles: {
							...generateTypographyCss(
								temperatureScaleTypography,
								enableTemplateGlobalStyle
							),
						},
					},
					{
						selector: `#${ uniqueId } .spl-weather-temperature-metric`,
						styles: {
							...generateTypographyCss(
								temperatureUnitTypography,
								enableTemplateGlobalStyle
							),
						},
					},
			  ]
			: [] ),
		// weather description.
		displayWeatherConditions && {
			selector: `#${ uniqueId } .spl-weather-card-short-desc`,
			styles: {
				color: splwColorControl(
					weatherDescColor,
					enableTemplateGlobalStyle
				),
				...generateTypographyCss(
					weatherDescTypography,
					enableTemplateGlobalStyle
				),
			},
		},
		// Detailed Weather and Update css.
		displayDateUpdateTime && {
			selector: `#${ uniqueId } .spl-weather-last-updated-time`,
			styles: {
				color: splwColorControl(
					detailedWeatherAndUpdateColor,
					enableTemplateGlobalStyle
				),
				...generateTypographyCss(
					detailedWeatherAndUpdateTypography,
					enableTemplateGlobalStyle
				),
			},
		},
		displayWeatherAttribution && {
			selector: `#${ uniqueId } .spl-weather-card-footer`,
			styles: {
				color: splwColorControl(
					weatherAttributionColor,
					enableTemplateGlobalStyle
				),
				...generateTypographyCss(
					weatherAttributionTypography,
					enableTemplateGlobalStyle
				),
				...( blockName !== 'vertical' && {
					'background-color': weatherAttributionBgColor,
				} ),
			},
		},
		// table forecast css.
		...( inArray( [ 'tabs', 'table' ], blockName )
			? [
					{
						selector: `#${ uniqueId } .spl-weather-forecast-table-layout :is(table, th, td)`,
						styles: {
							'border-style': tablePreferenceBorder.style,
							'border-color': tablePreferenceBorder.color,
							'border-width': cssDataCheck(
								tablePreferenceBorderWidth.value,
								tablePreferenceBorderWidth.unit
							),
						},
					},
					{
						selector: `#${ uniqueId } ${
							blockName === 'table' || template === 'tabs-two'
								? ':is(.spl-weather-table-header, .spl-weather-table-forecast-date)'
								: '.spl-weather-table-header'
						}`,
						styles: {
							color: splwColorControl(
								tableHeaderColor,
								enableTemplateGlobalStyle
							),
							'background-color': tableHeaderBgColor,
						},
					},
					{
						selector: `#${ uniqueId } .spl-weather-forecast-table-row:nth-of-type(odd)`,
						styles: {
							'background-color': tableOddRowColor,
						},
					},
					{
						selector: `#${ uniqueId } .spl-weather-forecast-table-row:nth-of-type(even)`,
						styles: {
							'background-color': tableEvenRowColor,
						},
					},
			  ]
			: [] ),
		// sun orbit css.
		...( blockName === 'tabs' && isShowSunOrbit
			? [
					{
						selector: `#${ uniqueId } .spl-weather-sun-orbit-sky`,
						styles: {
							'border-top-color': sunOrbitColor,
						},
					},
					{
						selector: `#${ uniqueId } .spl-weather-sun-orbit-sky i`,
						styles: {
							color: sunOrbitIconColor,
						},
					},
					{
						selector: `#${ uniqueId } .spl-weather-sun-orbit .spl-weather-details-value`,
						styles: {
							'margin-left': 0,
						},
					},
			  ]
			: [] ),
	];

	let sharedTabletCss = [
		...forecastTabletCss,
		...weatherDetailsTabletCss,
		...responsiveCss( attributes, 'Tablet' ),
	];

	let sharedMobileCss = [
		...forecastMobileCss,
		...weatherDetailsMobileCss,
		...responsiveCss( attributes, 'Mobile' ),
	];

	// visibility show hide css.
	if ( splwHideOnDesktop ) {
		sharedDesktopCss = [
			...sharedDesktopCss,
			{
				selector: `#${ uniqueId }`,
				styles: {
					display: 'none',
				},
			},
		];
	}

	if ( splwHideOnTablet ) {
		sharedTabletCss = [
			...sharedTabletCss,
			{
				selector: `#${ uniqueId }`,
				styles: {
					display: 'none',
				},
			},
		];
	} else {
		sharedTabletCss = [
			...sharedTabletCss,
			{
				selector: `#${ uniqueId }`,
				styles: {
					display: 'block',
				},
			},
		];
	}

	if ( splwHideOnMobile ) {
		sharedMobileCss = [
			...sharedMobileCss,
			{
				selector: `#${ uniqueId }`,
				styles: {
					display: 'none',
				},
			},
		];
	} else {
		sharedMobileCss = [
			...sharedMobileCss,
			{
				selector: `#${ uniqueId }`,
				styles: {
					display: 'block',
				},
			},
		];
	}

	return {
		sharedDesktopCss,
		sharedTabletCss,
		sharedMobileCss,
	};
};
export default sharedDynamicCss;
