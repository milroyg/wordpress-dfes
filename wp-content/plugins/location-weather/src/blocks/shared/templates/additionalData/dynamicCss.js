import {
	cssDataCheck,
	generateTypographyCss,
	generateTypoResponsive,
	getTinyBackground,
	inArray,
	splwColorControl,
	unit,
} from '../../../../controls';

const responsiveCss = ( attributes, device = 'Desktop' ) => {
	const {
		uniqueId,
		blockName,
		template,
		additionalDataMargin,
		additionalDataIconSize,
		additionalDataVerticalGap,
		displayComportDataPosition,
		active_additional_data_layout,
		weatherComportDataVerticalGap,
		active_additional_data_layout_style,
		additionalDataHorizontalGap,
		additionalDataPadding,
	} = attributes;

	return [
		// container margin.
		{
			selector: `#${ uniqueId } .spl-weather-card-daily-details`,
			styles: {
				margin: cssDataCheck(
					additionalDataMargin.device[ device ],
					unit( additionalDataMargin, device )
				),
			},
		},
		// single data padding.
		{
			selector: `#${ uniqueId } .spl-weather-card-daily-details .spl-weather-details`,
			styles: {
				padding: cssDataCheck(
					additionalDataPadding.device[ device ],
					unit( additionalDataPadding, device )
				),
			},
		},
		// row gap.
		! inArray( [ 'tabs', 'table' ], blockName ) &&
			! inArray(
				[ 'carousel-simple', 'carousel-flat' ],
				active_additional_data_layout
			) &&
			! inArray(
				[ 'divided', 'striped' ],
				active_additional_data_layout_style
			) && {
				selector: `#${ uniqueId } .spl-weather-details-regular-data`,
				styles: {
					'row-gap': cssDataCheck(
						additionalDataVerticalGap.device[ device ],
						unit( additionalDataVerticalGap, device )
					),
				},
			},
		// column gap.
		inArray(
			[ 'column-two', 'grid-one', 'grid-two', 'grid-three' ],
			active_additional_data_layout
		) && {
			selector: `#${ uniqueId } .spl-weather-details-regular-data`,
			styles: {
				'column-gap': cssDataCheck(
					additionalDataHorizontalGap.device[ device ],
					unit( additionalDataHorizontalGap, device )
				),
			},
		},
		// icon
		{
			selector: `#${ uniqueId } .spl-weather-card-daily-details .spl-weather-details-icon i`,
			styles: {
				'font-size': cssDataCheck(
					additionalDataIconSize.device[ device ],
					unit( additionalDataIconSize, device )
				),
			},
		},
		// additional data label.
		{
			selector: `#${ uniqueId } .spl-weather-card-daily-details .spl-weather-details-title`,
			styles: {
				...generateTypoResponsive(
					attributes,
					device,
					'additionalDataLabel'
				),
			},
		},
		// additional data value.
		{
			selector: `#${ uniqueId } .spl-weather-card-daily-details .spl-weather-details-value`,
			styles: {
				...generateTypoResponsive(
					attributes,
					device,
					'additionalDataValue'
				),
			},
		},
		// sun orbit sunrise and sunset data font size.
		...( inArray(
			[ 'horizontal-four', 'tabs-one', 'table-two' ],
			template
		) || 'combined' === blockName
			? [
					{
						selector: `#${ uniqueId } .spl-weather-sun-orbit .spl-weather-details-icon i`,
						styles: {
							'font-size': cssDataCheck(
								additionalDataIconSize.device[ device ],
								unit( additionalDataIconSize, device )
							),
						},
					},
					// additional data label.
					{
						selector: `#${ uniqueId } .spl-weather-sun-orbit .spl-weather-details-title`,
						styles: {
							...generateTypoResponsive(
								attributes,
								device,
								'additionalDataLabel'
							),
						},
					},
					// additional data value.
					{
						selector: `#${ uniqueId } .spl-weather-sun-orbit .spl-weather-details-value`,
						styles: {
							...generateTypoResponsive(
								attributes,
								device,
								'additionalDataValue'
							),
						},
					},
			  ]
			: [] ),
		...( inArray(
			[ 'grid-one', 'carousel-simple', 'carousel-flat' ],
			active_additional_data_layout
		)
			? [
					{
						selector: `#${ uniqueId } .spl-weather-card-daily-details .spl-weather-details-icon`,
						styles: {
							left: `${
								1 +
								parseInt(
									additionalDataPadding.device[ device ]?.left
								)
							}px`,
							top: `calc(50% - ${
								parseInt(
									additionalDataIconSize.device[ device ]
								) / 2
							}px)`,
						},
					},
					{
						selector: `#${ uniqueId } .spl-weather-card-daily-details .spl-weather-details`,
						styles: {
							width: '100%',
						},
					},
					{
						selector: `#${ uniqueId } .spl-weather-card-daily-details .spl-weather-details-title-wrapper,
				#${ uniqueId } .spl-weather-card-daily-details .spl-weather-details-value`,
						styles: {
							'margin-left': `${
								parseInt(
									additionalDataIconSize.device[ device ]
								) + 15
							}${ additionalDataIconSize.unit[ device ] }`,
						},
					},
			  ]
			: [] ),
		...( ! displayComportDataPosition
			? [
					{
						selector: `#${ uniqueId } .spl-weather-details-comport-data .spl-weather-details-value`,
						styles: {
							...generateTypoResponsive(
								attributes,
								device,
								'weatherComportData'
							),
						},
					},
					{
						selector: `#${ uniqueId } .spl-weather-details-comport-data`,
						styles: {
							'margin-top': cssDataCheck(
								weatherComportDataVerticalGap.device[ device ],
								unit( weatherComportDataVerticalGap, device )
							),
						},
					},
			  ]
			: [] ),
	];
};

const additionalDataDynamicCss = ( attributes ) => {
	const {
		uniqueId,
		blockName,
		template,
		additionalDataIconColor,
		active_additional_data_layout,
		additionalDataLabelColor,
		additionalDataValueColor,
		weatherComportDataColors,
		additionalNavigationVisibility,
		enableAdditionalNavIcon,
		additionalNavIconColors,
		additionalNavigationIconSize,
		additionalDataLabelTypography,
		additionalDataValueTypography,
		weatherComportDataTypography,
		enableTemplateGlobalStyle,
	} = attributes;

	if ( inArray( [ 'grid-two', 'grid-three' ], template ) ) {
		return {
			weatherDetailsDesktopCss: [],
			weatherDetailsTabletCss: [],
			weatherDetailsMobileCss: [],
		};
	}

	const isSwiperLayout = inArray(
		[ 'carousel-simple', 'carousel-flat' ],
		active_additional_data_layout
	);

	const tinyBackground = getTinyBackground( attributes );

	const weatherDetailsDesktopCss = [
		...responsiveCss( attributes, 'Desktop', isSwiperLayout ),
		{
			selector: `#${ uniqueId } .spl-weather-card-daily-details .spl-weather-details-icon i`,
			styles: {
				color: splwColorControl(
					additionalDataIconColor,
					enableTemplateGlobalStyle
				),
				'line-height': '1',
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-card-daily-details .spl-weather-details-title`,
			styles: {
				color: splwColorControl(
					additionalDataLabelColor,
					enableTemplateGlobalStyle
				),
				width: 'max-content',
				...generateTypographyCss(
					additionalDataLabelTypography,
					enableTemplateGlobalStyle
				),
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-card-daily-details .spl-weather-details-value`,
			styles: {
				color: splwColorControl(
					additionalDataValueColor,
					enableTemplateGlobalStyle
				),
				width: 'max-content',
				...generateTypographyCss(
					additionalDataValueTypography,
					enableTemplateGlobalStyle
				),
			},
		},
		...( inArray(
			[ 'horizontal-four', 'tabs-one', 'table-two' ],
			template
		) || 'combined' === blockName
			? [
					{
						selector: `#${ uniqueId } .spl-weather-sun-orbit .spl-weather-details-icon i`,
						styles: {
							color: splwColorControl(
								additionalDataIconColor,
								enableTemplateGlobalStyle
							),
							'line-height': '1',
						},
					},
					{
						selector: `#${ uniqueId } .spl-weather-sun-orbit .spl-weather-details-title`,
						styles: {
							color: splwColorControl(
								additionalDataLabelColor,
								enableTemplateGlobalStyle
							),
							width: 'max-content',
							...generateTypographyCss(
								additionalDataLabelTypography,
								enableTemplateGlobalStyle
							),
						},
					},
					{
						selector: `#${ uniqueId } .spl-weather-sun-orbit .spl-weather-details-value`,
						styles: {
							color: splwColorControl(
								additionalDataValueColor,
								enableTemplateGlobalStyle
							),
							width: 'max-content',
							...generateTypographyCss(
								additionalDataValueTypography,
								enableTemplateGlobalStyle
							),
						},
					},
			  ]
			: [] ),
		{
			selector: `#${ uniqueId } .spl-weather-details-comport-data .spl-weather-details-value`,
			styles: {
				color: splwColorControl(
					weatherComportDataColors?.Color,
					enableTemplateGlobalStyle
				),
				...generateTypographyCss(
					weatherComportDataTypography,
					enableTemplateGlobalStyle
				),
			},
		},
		...( isSwiperLayout && additionalNavigationVisibility === 'onHover'
			? [
					{
						selector: `#${ uniqueId } .spl-weather-swiper-nav.additional-data`,
						styles: {
							display: 'none',
						},
					},
					{
						selector: `#${ uniqueId } .spl-weather-card-daily-details:hover .spl-weather-swiper-nav.additional-data:not(.swiper-button-disabled)`,
						styles: {
							display: 'block',
						},
					},
			  ]
			: [] ),
		...( isSwiperLayout && enableAdditionalNavIcon
			? [
					{
						selector: `#${ uniqueId } .spl-weather-swiper-nav.additional-data`,
						styles: {
							color: additionalNavIconColors.color,
							'font-size': `${ additionalNavigationIconSize.value }${ additionalNavigationIconSize.unit }`,
						},
					},
					{
						selector: `#${ uniqueId } .spl-weather-swiper-nav.additional-data:hover`,
						styles: {
							color: additionalNavIconColors.hoverColor,
						},
					},
			  ]
			: [] ),
	];

	const dynamicCss = {
		weatherDetailsDesktopCss,
		weatherDetailsTabletCss: responsiveCss(
			attributes,
			'Tablet',
			isSwiperLayout
		),
		weatherDetailsMobileCss: responsiveCss(
			attributes,
			'Mobile',
			isSwiperLayout
		),
	};
	return dynamicCss;
};
export default additionalDataDynamicCss;
