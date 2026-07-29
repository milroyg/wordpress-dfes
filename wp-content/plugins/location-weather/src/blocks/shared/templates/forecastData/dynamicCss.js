import {
	unit,
	inArray,
	cssDataCheck,
	generateTypoResponsive,
	generateTypographyCss,
	getTinyBackground,
	hexColorToCSSFilter,
	splwColorControl,
} from '../../../../controls';

const responsiveCss = ( attributes, device = 'Desktop', conditions ) => {
	const {
		uniqueId,
		blockName,
		forecastDataIconSize,
		forecastLabelVerticalGap,
		forecastContainerPadding,
		forecastContainerMargin,
		forecastToggleVerticalGap,
		template,
		forecastTabsGap,
	} = attributes;

	return [
		{
			selector: `#${ uniqueId } .spl-weather-forecast-icon img`,
			styles: {
				width: cssDataCheck(
					forecastDataIconSize.device[ device ],
					unit( forecastDataIconSize, device )
				),
				height: cssDataCheck(
					forecastDataIconSize.device[ device ],
					unit( forecastDataIconSize, device )
				),
				'max-width': 'none',
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-forecast-date-time span`,
			styles: {
				...generateTypoResponsive(
					attributes,
					device,
					'forecastLabel'
				),
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-forecast-value span, #${ uniqueId } .spl-weather-forecast-value`,
			styles: {
				...generateTypoResponsive( attributes, device, 'forecastData' ),
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-card-forecast-data`,
			styles: {
				padding: cssDataCheck(
					forecastContainerPadding.device[ device ],
					unit( forecastContainerPadding, device )
				),
				margin: cssDataCheck(
					forecastContainerMargin.device[ device ],
					unit( forecastContainerMargin, device )
				),
			},
		},
		inArray( [ 'combined', 'accordion', blockName ] ) && {
			selector: `#${ uniqueId } .spl-weather-forecast-date-time`,
			styles: {
				'margin-right': cssDataCheck(
					forecastLabelVerticalGap.device[ device ],
					unit( forecastLabelVerticalGap, device )
				),
			},
		},

		( template === 'horizontal-one' || blockName === 'grid' ) && {
			selector: `#${ uniqueId } .spl-weather-forecast-tabs`,
			styles: {
				gap: cssDataCheck(
					forecastTabsGap.device[ device ],
					unit( forecastTabsGap, device )
				),
			},
		},
	];
};

const forecastDynamicCss = ( attributes ) => {
	const {
		template,
		uniqueId,
		forecastDropdownTextColor,
		forecastLiveFilterBorder,
		forecastDropdownBgColor,
		forecastLiveFilterBgColor,
		forecastLiveFilterColors,
		displayWeatherForecastData,
		forecastNavigationVisibility,
		forecastNavigationIconColors,
		forecastNavigationIconSize,
		showForecastNavIcon,
		forecastLabelColor,
		forecastDataColor,
		forecastDataBgColor,
		blockName,
		forecastDataTypography,
		forecastLabelTypography,
		forecastTabsFullWidthBottomLine,
		forecastTabsBottomLineColor,
		forecastTabsLabelColor,
		forecastTabsBottomLineWidth,
		forecastActiveTabsBottomLineColor,
		forecastDisplayStyle,
		forecastToggleBorderWidth,
		forecastToggleRadius,
		forecastLiveFilterBorderWidth,
		forecastDataIconColor,
		forecastDataIcon,
		forecastDataIconType,
		enableTemplateGlobalStyle,
	} = attributes;

	if ( ! displayWeatherForecastData ) {
		return {
			forecastDesktopCss: [],
			forecastTabletCss: [],
			forecastMobileCss: [],
		};
	}

	const isShowSelectLiveFilter =
		inArray(
			[ 'horizontal-two', 'horizontal-four', 'grid-two', 'grid-three' ],
			template
		) || blockName === 'vertical';

	const isForecastSwiper = inArray(
		[
			'vertical-two',
			'vertical-four',
			'horizontal-one',
			'horizontal-three',
			'horizontal-three',
		],
		template
	);

	const tinyBackground = getTinyBackground( attributes );

	const conditions = {
		isForecastSwiper,
		isShowSelectLiveFilter,
	};
	const forecastDesktopCss = [
		...responsiveCss( attributes, 'Desktop', conditions ),
		...( template === 'horizontal-four' ||
		( template === 'vertical-six' && forecastDisplayStyle === 'inline' )
			? [
					{
						selector: `#${ uniqueId } .spl-weather-forecast-icon`,
						styles: {
							'background-color': tinyBackground,
						},
					},
					{
						selector: `#${ uniqueId } .spl-weather-forecast-value`,
						styles: {
							'border-left': `2px solid ${ tinyBackground }`,
							'padding-left': '10px',
						},
					},
			  ]
			: [] ),

		// forecast select filter.
		...( isShowSelectLiveFilter
			? [
					{
						selector: `#${ uniqueId } .spl-weather-forecast-select`,
						styles: {
							color: splwColorControl(
								forecastLiveFilterColors?.color,
								enableTemplateGlobalStyle
							),
							'background-color':
								forecastLiveFilterBgColor?.color,
							'border-style': forecastLiveFilterBorder?.style,
							'border-color': forecastLiveFilterBorder?.color,
							'border-width': cssDataCheck(
								forecastLiveFilterBorderWidth.value,
								forecastLiveFilterBorderWidth.unit
							),
						},
					},
					{
						selector: `#${ uniqueId } .spl-weather-forecast-select-list`,
						styles: {
							padding: '0',
							margin: '0',
							color: forecastDropdownTextColor?.color,
							'background-color': forecastDropdownBgColor?.color,
							'border-color': forecastLiveFilterBorder?.color,
						},
					},
					{
						selector: `#${ uniqueId } .spl-weather-forecast-select-item.active`,
						styles: {
							color: forecastDropdownTextColor?.active,
							'background-color': forecastDropdownBgColor?.active,
						},
					},
					{
						selector: `#${ uniqueId } .spl-weather-forecast-select-item:hover`,
						styles: {
							color: forecastDropdownTextColor?.hover,
							'background-color': forecastDropdownBgColor?.hover,
						},
					},
			  ]
			: [] ),
		// forecast tabs live filter.
		...( template === 'horizontal-one' || blockName === 'grid'
			? [
					{
						selector: `#${ uniqueId } .spl-weather-forecast-tabs-header`,
						styles: {
							'border-bottom': `${ forecastTabsFullWidthBottomLine?.value }${ forecastTabsFullWidthBottomLine?.unit } solid ${ forecastTabsBottomLineColor }`,
						},
					},
					{
						selector: `#${ uniqueId } .spl-weather-forecast-tabs`,
						styles: {
							display: 'flex',
							'align-items': 'center',
							'list-style': 'none',
							margin: '0',
							padding: '0',
						},
					},
					{
						selector: `#${ uniqueId } .spl-weather-forecast-tabs .spl-weather-forecast-tab:not(.active)`,
						styles: {
							color: splwColorControl(
								forecastTabsLabelColor?.color,
								enableTemplateGlobalStyle
							),
						},
					},
					{
						selector: `#${ uniqueId } .spl-weather-forecast-tabs .spl-weather-forecast-tab.active`,
						styles: {
							color: splwColorControl(
								forecastTabsLabelColor?.active,
								enableTemplateGlobalStyle
							),
						},
					},
					{
						selector: `#${ uniqueId } .spl-weather-forecast-tabs .spl-weather-forecast-tab::after`,
						styles: {
							height: `${ forecastTabsBottomLineWidth.value }${ forecastTabsBottomLineWidth.unit }`,
							background: forecastActiveTabsBottomLineColor,
						},
					},
			  ]
			: [] ),
		...( isForecastSwiper && forecastNavigationVisibility === 'onHover'
			? [
					{
						selector: `#${ uniqueId } .spl-weather-card-forecast-data .spl-weather-swiper-nav.forecast`,
						styles: {
							display: 'none',
						},
					},
					{
						selector: `#${ uniqueId } .spl-weather-card-forecast-data:hover .spl-weather-swiper-nav.forecast:not(.swiper-button-disabled)`,
						styles: {
							display: 'block',
						},
					},
			  ]
			: [] ),
		...( isForecastSwiper && showForecastNavIcon
			? [
					{
						selector: `#${ uniqueId } .spl-weather-swiper-nav.forecast`,
						styles: {
							color: forecastNavigationIconColors.color,
							'font-size': `${ forecastNavigationIconSize.value }${ forecastNavigationIconSize.unit }`,
						},
					},
					{
						selector: `#${ uniqueId } .spl-weather-swiper-nav.forecast:hover`,
						styles: {
							color: forecastNavigationIconColors.hoverColor,
						},
					},
			  ]
			: [] ),
		forecastDataIcon &&
			inArray(
				[
					'forecast_icon_set_three',
					'forecast_icon_set_eight',
					'forecast_icon_set_four',
				],
				forecastDataIconType
			) && {
				selector: `#${ uniqueId } .spl-weather-forecast-icon img`,
				styles: {
					filter: hexColorToCSSFilter( forecastDataIconColor ),
				},
			},
		{
			selector: `#${ uniqueId } .spl-weather-forecast-date-time span`,
			styles: {
				color: splwColorControl(
					forecastLabelColor,
					enableTemplateGlobalStyle
				),
				...generateTypographyCss(
					forecastLabelTypography,
					enableTemplateGlobalStyle
				),
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-forecast-title`,
			styles: {
				color: splwColorControl(
					forecastLabelColor,
					enableTemplateGlobalStyle
				),
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-forecast-value`,
			styles: {
				color: splwColorControl(
					forecastDataColor,
					enableTemplateGlobalStyle
				),
				...generateTypographyCss(
					forecastDataTypography,
					enableTemplateGlobalStyle
				),
			},
		},
		inArray( [ 'tabs', 'table' ], blockName ) && {
			selector: `#${ uniqueId } .spl-weather-forecast-icon .spl-weather-forecast-description`,
			styles: {
				color: splwColorControl(
					forecastDataColor,
					enableTemplateGlobalStyle
				),
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-card-forecast-data`,
			styles: {
				'background-color': forecastDataBgColor,
			},
		},
	];

	const dynamicCss = {
		forecastDesktopCss,
		forecastTabletCss: responsiveCss( attributes, 'Tablet', conditions ),
		forecastMobileCss: responsiveCss( attributes, 'Mobile', conditions ),
	};
	return dynamicCss;
};
export default forecastDynamicCss;
