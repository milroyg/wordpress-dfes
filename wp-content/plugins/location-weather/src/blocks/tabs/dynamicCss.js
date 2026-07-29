import { cssDataCheck, filterResponsiveDynamicCss, unit } from '../../controls';
import sharedDynamicCss from '../shared/dynamicCss';
import { mapDynamicCss } from '../shared/templates/maps';

const responsiveCss = ( attributes, device = 'Desktop' ) => {
	const {
		uniqueId,
		tabTopBorderWidth,
		splwPadding,
		splwTabAlignment,
		tabTopBorderColor,
	} = attributes;
	const css = [
		// padding.
		{
			selector: `#${ uniqueId } .spl-weather-tabs-block-content`,
			styles: {
				padding: cssDataCheck(
					splwPadding.device[ device ],
					unit( splwPadding, device )
				),
				...( splwTabAlignment === 'left' && {
					'border-top-left-radius': '0',
				} ),
				...( splwTabAlignment === 'right' && {
					'border-top-right-radius': '0',
				} ),
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-tab-nav.active`,
			styles: {
				'box-shadow': `0 ${ tabTopBorderWidth.device[ device ] }${ tabTopBorderWidth.unit[ device ] } 0 0 ${ tabTopBorderColor } inset`,
			},
		},
	];
	return css;
};

const dynamicCss = ( attributes, page = 'editor' ) => {
	const { sharedDesktopCss, sharedMobileCss, sharedTabletCss } =
		sharedDynamicCss( attributes );
	const { mapDesktopCss, mapTabletCss, mapMobileCss } =
		mapDynamicCss( attributes );
	const {
		uniqueId,
		tablePreferenceBorder,
		tabTitleColors,
		tabTitleBgColors,
		splwBorder,
		splwTabAlignment,
		splwBorderWidth,
		tablePreferenceBorderWidth,
	} = attributes;

	const desktopCss = [
		...sharedDesktopCss,
		...responsiveCss( attributes, 'Desktop' ),
		...mapDesktopCss,
		// tab nav css.
		{
			selector: `#${ uniqueId } .spl-weather-tab-navs`,
			styles: {
				...( splwTabAlignment !== 'between'
					? {
							'justify-content': splwTabAlignment,
					  }
					: { 'flex-direction': 'column' } ),
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-tab-nav`,
			styles: {
				color: tabTitleColors?.color,
				'background-color': tabTitleBgColors?.color,
				'margin-bottom': `-${ splwBorderWidth.value.top }${ splwBorderWidth.unit }`,
				transition: 'box-shadow .3s ease',
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-tab-nav.active`,
			styles: {
				color: tabTitleColors?.activeColor,
				'background-color': tabTitleBgColors?.activeColor,
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-tab-nav:first-child`,
			styles: {
				'border-left-style': splwBorder.style,
				'border-left-color': splwBorder.color,
				'border-top-left-radius': '4px',
				'border-left-width': `${ splwBorderWidth.value.left }${ splwBorderWidth.unit }`,
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-tab-nav:last-child`,
			styles: {
				'border-right-style': splwBorder.style,
				'border-right-color': splwBorder.color,
				'border-top-right-radius': '4px',
				'border-right-width': `${ splwBorderWidth.value.right }${ splwBorderWidth.unit }`,
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-tab-nav:not(:last-child)`,
			styles: {
				'border-right': '1px solid #ddd',
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-details`,
			styles: {
				'border-style': tablePreferenceBorder?.style,
				'border-color': tablePreferenceBorder?.color,
				'border-width': cssDataCheck(
					tablePreferenceBorderWidth.value,
					tablePreferenceBorderWidth.unit
				),
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-details-table-data:hover .spl-weather-details`,
			styles: {
				'border-color': tablePreferenceBorder?.hoverColor,
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-details:nth-of-type(even)`,
			styles: {
				'border-left': 'none',
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-details:not(:first-child, :nth-child(2))`,
			styles: {
				'border-top': 'none',
			},
		},
	];

	const tabletCss = [
		...sharedTabletCss,
		...responsiveCss( attributes, 'Tablet' ),
		...mapTabletCss,
	];

	const mobileCss = [
		...sharedMobileCss,
		...responsiveCss( attributes, 'Mobile' ),
		...mapMobileCss,
		{
			selector: `#${ uniqueId } .spl-weather-tab-nav:not(:last-child)`,
			styles: {
				'border-right': `${
					splwBorderWidth.value.right + splwBorderWidth.unit
				} ${ splwBorder?.style } ${ splwBorder?.color }`,
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-tab-nav:not(:first-child)`,
			styles: {
				'border-left': `${
					splwBorderWidth.value.left + splwBorderWidth.unit
				} ${ splwBorder?.style } ${ splwBorder?.color }`,
			},
		},
	];

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
export default dynamicCss;
