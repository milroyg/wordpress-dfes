import {
	boxCss,
	cssDataCheck,
	inArray,
	filterResponsiveDynamicCss,
	unit,
	splwColorControl,
} from '../../controls';
import sharedDynamicCss from '../shared/dynamicCss';
import { mapDynamicCss } from '../shared/templates/maps';

const responsiveCss = ( attributes, device = 'Desktop' ) => {
	const {
		uniqueId,
		template,
		dailyForecastColumns,
		hourlyForecastPadding,
		additionalDataPadding,
		currentWeatherMargin,
	} = attributes;

	const dynamicCss = [
		// hourly forecast.
		{
			selector: `#${ uniqueId } .spl-weather-grid-card-tabs-forecast`,
			styles: {
				padding: cssDataCheck(
					hourlyForecastPadding.device[ device ],
					unit( hourlyForecastPadding, device )
				),
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-grid-card-tabs-forecast .spl-weather-forecast-header-area`,
			styles: {
				'margin-left': `-${
					hourlyForecastPadding.device[ device ].left
				}${ unit( hourlyForecastPadding, device ) }`,
				'margin-right': `-${
					hourlyForecastPadding.device[ device ].right
				}${ unit( hourlyForecastPadding, device ) }`,
				'padding-left': `${
					hourlyForecastPadding.device[ device ].left
				}${ unit( hourlyForecastPadding, device ) }`,
			},
		},
	];
	return dynamicCss;
};

const dynamicCss = ( attributes, page = 'editor' ) => {
	const { sharedDesktopCss, sharedMobileCss, sharedTabletCss } =
		sharedDynamicCss( attributes );
	const { mapDesktopCss, mapTabletCss, mapMobileCss } =
		mapDynamicCss( attributes );

	const {
		uniqueId,
		gridHourlyForecastColor,
		hourlyForecastBgType,
		hourlyForecastBgColor,
		hourlyForecastBgGradient,
		hourlyForecastBorder,
		hourlyForecastBorderWidth,
		hourlyForecastBorderRadius,
		enableHourlyForecastBoxShadow,
		hourlyForecastBoxShadow,
		enableTemplateGlobalStyle,
	} = attributes;

	const getBackground = ( key = 'dailyForecast', colorState ) => {
		const bgType = attributes[ `${ key }BgType` ][ colorState ];
		const background = {
			bgColor: attributes[ `${ key }BgColor` ][ colorState ],
			gradient: attributes[ `${ key }BgGradient` ][ colorState ],
		}[ bgType ];
		return background;
	};

	const hourlyForecastBg = {
		bgColor: hourlyForecastBgColor,
		gradient: hourlyForecastBgGradient,
	}[ hourlyForecastBgType ];

	const desktopCss = [
		...sharedDesktopCss,
		...mapDesktopCss,
		...responsiveCss( attributes, 'Desktop' ),
		// hourly forecast css.
		{
			selector: `#${ uniqueId } .spl-weather-grid-card-tabs-forecast .spl-weather-forecast-data span`,
			styles: {
				color: splwColorControl(
					gridHourlyForecastColor?.color,
					enableTemplateGlobalStyle
				),
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-grid-card-tabs-forecast`,
			styles: {
				background: hourlyForecastBg,
				'border-style': hourlyForecastBorder?.style,
				'border-color': hourlyForecastBorder?.color,
				'border-width': cssDataCheck(
					hourlyForecastBorderWidth.value,
					hourlyForecastBorderWidth.unit
				),
				'border-radius': cssDataCheck(
					hourlyForecastBorderRadius.value,
					hourlyForecastBorderRadius.unit
				),
				'box-shadow': boxCss(
					enableHourlyForecastBoxShadow,
					hourlyForecastBoxShadow,
					'color'
				),
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-grid-card-tabs-forecast .spl-weather-forecast-container`,
			styles: {
				'align-items': 'start',
			},
		},
		{
			selector: `#${ uniqueId } .spl-weather-grid-card-tabs-forecast .spl-weather-forecast-icon`,
			styles: {
				'margin-left': '10px',
			},
		},
	];

	const tabletCss = [
		...sharedTabletCss,
		...mapTabletCss,
		...responsiveCss( attributes, 'Tablet' ),
	];

	const mobileCss = [
		...sharedMobileCss,
		...mapMobileCss,
		...responsiveCss( attributes, 'Mobile' ),
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
