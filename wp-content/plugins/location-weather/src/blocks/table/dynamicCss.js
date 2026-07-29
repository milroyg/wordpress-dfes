import { cssDataCheck, filterResponsiveDynamicCss, unit } from '../../controls';
import sharedDynamicCss from '../shared/dynamicCss';

export const responsiveCss = ( attributes, device ) => {
	const { uniqueId, template, splwPadding, additionalDataPadding } =
		attributes;
	return [
		// padding.
		{
			selector: `#${ uniqueId } .spl-weather-current-data-table-left`,
			styles: {
				padding: cssDataCheck(
					splwPadding.device[ device ],
					unit( splwPadding, device )
				),
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-detailed`,
			styles: {
				'padding-right': cssDataCheck(
					additionalDataPadding.device[ device ][ 'right' ],
					unit( additionalDataPadding, device )
				),
			},
		},
	];
};

const dynamicCss = ( attributes, page = 'frontend' ) => {
	const { sharedDesktopCss, sharedTabletCss, sharedMobileCss } =
		sharedDynamicCss( attributes, page );
	const { uniqueId, template, splwBorder } = attributes;

	const desktopCss = [
		...responsiveCss( attributes, 'Desktop' ),
		...sharedDesktopCss,
		{
			selector: `#${ uniqueId } .spl-weather-current-data-table :is(table, th, td)`,
			styles: {
				'border-style': splwBorder?.style,
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-current-data-table :is(table, th, td, .spl-weather-details)`,
			styles: {
				'border-color': splwBorder?.color,
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-current-data-table:hover :is(table, th, td, .spl-weather-details)`,
			styles: {
				'border-color': splwBorder?.hoverColor,
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-details`,
			styles: {
				'border-style': splwBorder?.style,
				'border-left': 'none',
				'border-top': 'none',
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-details:nth-of-type(even)`,
			styles: {
				'border-right': 'none',
			},
		},
	];

	const tabletCss = [
		...responsiveCss( attributes, 'Tablet' ),
		...sharedTabletCss,
	];

	const mobileCss = [
		...responsiveCss( attributes, 'Mobile' ),
		...sharedMobileCss,
		{
			selector: `#${ uniqueId } :is(.spl-weather-table-current-data, .spl-weather-forecast-table-layout)`,
			styles: {
				overflow: 'scroll',
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
