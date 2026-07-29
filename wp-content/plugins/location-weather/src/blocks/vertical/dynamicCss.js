import sharedDynamicCss from '../shared/dynamicCss';
import {
	filterResponsiveDynamicCss,
	getConvertedTinyColor,
	unit,
} from '../../controls';

const responsiveCss = ( attributes, device = 'Desktop' ) => {
	const { uniqueId, splwPadding, forecastContainerPadding } = attributes;

	const css = [
		{
			selector: `#${ uniqueId } .spl-weather-template-wrapper`,
			styles: {
				'padding-top': `${ splwPadding.device[ device ].top }${ unit(
					splwPadding,
					device
				) }`,
				'padding-bottom': `${
					splwPadding.device[ device ].bottom
				}${ unit( splwPadding, device ) }`,
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-template-wrapper :is(.spl-weather-card-current-weather, .spl-weather-card-daily-details, .spl-weather-card-forecast-data, .spl-weather-card-footer)`,
			styles: {
				'padding-left': `${ splwPadding.device[ device ].left }${ unit(
					splwPadding,
					device
				) }`,
				'padding-right': `${
					splwPadding.device[ device ].right
				}${ unit( splwPadding, device ) }`,
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-card-forecast-data>div`,
			styles: {
				'padding-left': `${
					forecastContainerPadding.device[ device ].left
				}${ unit( forecastContainerPadding, device ) }`,
				'padding-right': `${
					forecastContainerPadding.device[ device ].right
				}${ unit( forecastContainerPadding, device ) }`,
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-card-footer`,
			styles: {
				'margin-bottom': `${
					splwPadding.device[ device ].bottom
				}${ unit( splwPadding, device ) }`,
			},
		},
	];
	return css;
};

const dynamicCss = ( attributes, page = 'editor' ) => {
	const { sharedDesktopCss, sharedMobileCss, sharedTabletCss } =
		sharedDynamicCss( attributes );

	const {
		uniqueId,
		displayWeatherAttribution,
		weatherAttributionBgColor,
		forecastDataBgColor,
	} = attributes;

	const desktopCss = [
		...responsiveCss( attributes, 'Desktop' ),
		...sharedDesktopCss,
		displayWeatherAttribution && {
			selector: `#${ uniqueId } .spl-weather-card-footer`,
			styles: {
				'background-color':
					'' === forecastDataBgColor ||
					( '' !== forecastDataBgColor &&
						'#00000036' !== weatherAttributionBgColor )
						? weatherAttributionBgColor
						: getConvertedTinyColor( forecastDataBgColor ),
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
	];
	const cssObj = {
		desktopCss,
		tabletCss,
		mobileCss,
	};

	const dynamicCss = filterResponsiveDynamicCss( cssObj );
	return dynamicCss;
};
export default dynamicCss;
