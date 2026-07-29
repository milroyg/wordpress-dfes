import sharedDynamicCss from '../shared/dynamicCss';
import { cssDataCheck, filterResponsiveDynamicCss, unit } from '../../controls';

const responsiveCss = ( attributes, device = 'Desktop' ) => {
	const { template, uniqueId, splwPadding } = attributes;

	const css = [
		{
			selector: `#${ uniqueId } .spl-weather-${ template }-wrapper`,
			styles: {
				padding: cssDataCheck(
					splwPadding.device[ device ],
					unit( splwPadding, device )
				),
			},
		},
		...( template === 'horizontal-one'
			? [
					{
						selector: `#${ uniqueId } .spl-weather-forecast-header-area`,
						styles: {
							'margin-left':
								'-' +
								splwPadding[ 'device' ][ device ][ 'left' ] +
								splwPadding[ 'unit' ][ device ],
							'margin-right':
								'-' +
								splwPadding[ 'device' ][ device ][ 'right' ] +
								splwPadding[ 'unit' ][ device ],
						},
					},
					{
						selector: `#${ uniqueId } .spl-weather-forecast-header-area .spl-weather-forecast-tabs`,
						styles: {
							'padding-left':
								splwPadding[ 'device' ][ device ][ 'left' ] +
								splwPadding[ 'unit' ][ device ],
						},
					},
			  ]
			: [] ),
	];
	return css;
};

const dynamicCss = ( attributes, page = 'editor' ) => {
	const { sharedDesktopCss, sharedMobileCss, sharedTabletCss } =
		sharedDynamicCss( attributes );

	const desktopCss = [
		...responsiveCss( attributes, 'Desktop' ),
		...sharedDesktopCss,
	];

	const tabletCss = [
		...responsiveCss( attributes, 'Tablet' ),
		...sharedTabletCss,
	];
	const mobileCss = [
		...responsiveCss( attributes, 'Mobile' ),
		...sharedMobileCss,
	];
	const cssObj = {
		desktopCss,
		tabletCss,
		mobileCss,
	};

	const dynamicCss = filterResponsiveDynamicCss( cssObj, page );
	return dynamicCss;
};
export default dynamicCss;
