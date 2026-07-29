import { boxCss, cssDataCheck, inArray, unit } from '../../../../controls';

const responsiveCss = (
	attributes,
	device = 'Desktop',
	isOWMBlock = false
) => {
	const {
		uniqueId,
		blockName,
		weatherMapPadding,
		weatherMapMaxWidth,
		weatherMapMaxHeight,
	} = attributes;

	const css = [
		// container css.
		...( blockName !== 'tabs'
			? [
					{
						selector: `#${ uniqueId } .spl-weather-map-template`,
						styles: {
							padding: cssDataCheck(
								weatherMapPadding.device[ device ],
								unit( weatherMapPadding, device )
							),
							...( inArray( [ 'windy-map' ], blockName ) && {
								'max-width': cssDataCheck(
									weatherMapMaxWidth.device[ device ],
									unit( weatherMapMaxWidth, device )
								),
								height: cssDataCheck(
									weatherMapMaxHeight.device[ device ],
									unit( weatherMapMaxHeight, device )
								),
							} ),
						},
					},
			  ]
			: [] ),
	];

	return css;
};

const dynamicCss = ( attributes ) => {
	const {
		uniqueId,
		blockName,
		weatherMapBgColorType,
		weatherMapBgColor,
		weatherMapBgGradient,
		weatherMapBorder,
		weatherMapBorderRadius,
		weatherMapBorderWidth,
		enableWeatherMapBoxShadow,
		weatherMapBoxShadow,
	} = attributes;

	const mapBackground = {
		bgColor: weatherMapBgColor,
		gradient: weatherMapBgGradient,
	}[ weatherMapBgColorType ];

	const isOWMBlock = false;

	const mapDesktopCss = [
		...responsiveCss( attributes, 'Desktop', isOWMBlock ),
		...( blockName !== 'tabs'
			? [
					{
						selector: `#${ uniqueId } .spl-weather-map-template`,
						styles: {
							background: mapBackground,
							'border-style': weatherMapBorder?.style,
							'border-color': weatherMapBorder?.color,
							'border-width': cssDataCheck(
								weatherMapBorderWidth.value,
								weatherMapBorderWidth.unit
							),
							'border-radius': cssDataCheck(
								weatherMapBorderRadius.value,
								weatherMapBorderRadius.unit
							),
							'box-shadow': boxCss(
								enableWeatherMapBoxShadow,
								weatherMapBoxShadow,
								'color'
							),
						},
					},
					{
						selector: `#${ uniqueId } .spl-weather-map-template:hover`,
						styles: {
							'border-color': weatherMapBorder?.hoverColor,
						},
					},
			  ]
			: [] ),
	];

	return {
		mapDesktopCss,
		mapTabletCss: responsiveCss( attributes, 'Tablet' ),
		mapMobileCss: responsiveCss( attributes, 'Mobile' ),
	};
};
export default dynamicCss;
