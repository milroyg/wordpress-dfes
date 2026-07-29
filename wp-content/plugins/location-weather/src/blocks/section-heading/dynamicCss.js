import {
	cssDataCheck,
	filterResponsiveDynamicCss,
	generateBorderCSS,
	generateTypographyCss,
	generateTypoResponsive,
	inArray,
} from '../../controls';

const dynamicCSS = ( attributes, page = 'editor' ) => {
	const {
		uniqueId,
		headingStyle,
		headingColor,
		headingAlignment,
		headingBgGradient,
		headingBgColor,
		headingBgType,
		headingBorder,
		headingBorderRadius,
		headingBorderWidth,
		headingPadding,
		headingTypography,
		subHeading,
		subHeadingColor,
		subHeadingTypography,
		subHeadingBgGradient,
		subHeadingBgColor,
		subHeadingBgType,
		subHeadingPadding,
		subHeadingBorder,
		subHeadingBorderWidth,
		subHeadingBorderRadius,
		headingContainerMargin,
		headingContainerPadding,
		headingContainerBorderWidth,
		headingContainerBorderRadius,
		headingContainerBorder,
		headingContainerBgGradient,
		headingContainerBgColor,
		headingContainerBgType,
		bottomLineHorPosition,
		bottomLineWidth,
		bottomLineThickness,
		bottomLineColor,
		fullWidthBottomLineThickness,
		fullWidthBottomLineColor,
		fullWidthBottomLine,
		sideLineType,
		sideLineThickness,
		sideLineColor,
		sideLineWidth,
		sideLineGap,
		subHeadingGap,
	} = attributes;
	const responsiveCss = ( deviceType ) => {
		return [
			{
				selector: `#${ uniqueId } .spl-weather-section-heading-text`,
				styles: {
					...generateTypoResponsive(
						attributes,
						deviceType,
						'heading'
					),
				},
			},
			subHeading && {
				selector: `#${ uniqueId } .spl-weather-section-sub-heading-text`,
				styles: {
					...generateTypoResponsive(
						attributes,
						deviceType,
						'subHeading'
					),
				},
			},
		];
	};

	const desktopCss = [
		...responsiveCss( 'Desktop' ),
		subHeading && {
			selector: `#${ uniqueId }.spl-weather-section-heading-block-wrapper`,
			styles: {
				gap: cssDataCheck( subHeadingGap.value, subHeadingGap.unit ),
			},
		},
		{
			selector: `#${ uniqueId }.spl-weather-section-heading-block-wrapper > div`,
			styles: {
				'justify-content': headingAlignment,
			},
		},
		fullWidthBottomLine && {
			selector: `#${ uniqueId } ${
				headingStyle === 'heading-two'
					? '.spl-weather-section-heading-text'
					: '.spl-weather-section-heading'
			}`,
			styles: {
				'border-bottom': `${ cssDataCheck(
					fullWidthBottomLineThickness.value,
					fullWidthBottomLineThickness.unit
				) } solid ${ fullWidthBottomLineColor }`,
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-section-heading-text`,
			styles: {
				color: headingColor,
				...generateTypographyCss( headingTypography ),
				background: {
					transparent: 'transparent',
					solid: headingBgColor,
					gradient: headingBgGradient,
				}[ headingBgType ],
				padding: cssDataCheck(
					headingPadding.value,
					headingPadding.unit
				),
				...( inArray(
					[ 'heading-one', 'heading-four' ],
					headingStyle
				) && {
					...generateBorderCSS( headingBorder, headingBorderWidth ),
					'border-radius': cssDataCheck(
						headingBorderRadius.value,
						headingBorderRadius.unit
					),
				} ),
				...( headingStyle === 'heading-three' && {
					display: 'flex',
					gap: cssDataCheck( sideLineGap.value, sideLineGap.unit ),
				} ),
			},
		},
		// Heading Style Two CSS.
		headingStyle === 'heading-two' && {
			selector: `#${ uniqueId } .spl-weather-section-heading-text::after`,
			styles: {
				content: "''",
				position: 'absolute',
				left: cssDataCheck(
					bottomLineHorPosition.value,
					bottomLineHorPosition.unit
				),
				...( bottomLineHorPosition.unit === '%' && {
					transform: `translateX(-${ cssDataCheck(
						bottomLineHorPosition.value,
						bottomLineHorPosition.unit
					) })`,
				} ),
				bottom: fullWidthBottomLine
					? `-${ cssDataCheck(
							fullWidthBottomLineThickness.value,
							fullWidthBottomLineThickness.unit
					  ) }`
					: '0',
				width: cssDataCheck(
					bottomLineWidth.value,
					bottomLineWidth.unit
				),
				height: cssDataCheck(
					bottomLineThickness.value,
					bottomLineThickness.unit
				),
				'background-color': bottomLineColor,
			},
		},
		// Heading Style Three CSS.
		headingStyle === 'heading-three' && {
			selector: {
				both: `#${ uniqueId } .spl-weather-section-heading-text::before, #${ uniqueId } .spl-weather-section-heading-text::after`,
				left: `#${ uniqueId } .spl-weather-section-heading-text::before`,
				right: `#${ uniqueId } .spl-weather-section-heading-text::after`,
			}[ sideLineType ],
			styles: {
				content: "''",
				width: cssDataCheck( sideLineWidth.value, sideLineWidth.unit ),
				height: cssDataCheck(
					sideLineThickness.value,
					sideLineThickness.unit
				),
				'background-color': sideLineColor,
				'align-self': 'center',
			},
		},
		// Sub Heading CSS.
		subHeading && {
			selector: `#${ uniqueId } .spl-weather-section-sub-heading-text`,
			styles: {
				color: subHeadingColor,
				...generateTypographyCss( subHeadingTypography ),
				background: {
					transparent: 'transparent',
					solid: subHeadingBgColor,
					gradient: subHeadingBgGradient,
				}[ subHeadingBgType ],
				padding: cssDataCheck(
					subHeadingPadding.value,
					subHeadingPadding.unit
				),
				...generateBorderCSS( subHeadingBorder, subHeadingBorderWidth ),
				'border-radius': cssDataCheck(
					subHeadingBorderRadius.value,
					subHeadingBorderRadius.unit
				),
			},
		},
		// Block container CSS.
		{
			selector: `#${ uniqueId }.spl-weather-section-heading-block-wrapper`,
			styles: {
				background: {
					transparent: 'transparent',
					solid: headingContainerBgColor,
					gradient: headingContainerBgGradient,
				}[ headingContainerBgType ],
				...generateBorderCSS(
					headingContainerBorder,
					headingContainerBorderWidth
				),
				'border-radius': cssDataCheck(
					headingContainerBorderRadius.value,
					headingContainerBorderRadius.unit
				),
				padding: cssDataCheck(
					headingContainerPadding.value,
					headingContainerPadding.unit
				),
				margin: cssDataCheck(
					headingContainerMargin.value,
					headingContainerMargin.unit
				),
			},
		},
	];
	const tabletCss = [ ...responsiveCss( 'Tablet' ) ];

	const mobileCss = [ ...responsiveCss( 'Mobile' ) ];

	const dynamicCss = filterResponsiveDynamicCss(
		{
			desktopCss,
			tabletCss,
			mobileCss,
		},
		page
	);
	return dynamicCss;
};

export default dynamicCSS;
