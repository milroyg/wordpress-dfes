import classNames from 'classnames';
import {
	boxCss,
	cssDataCheck,
	generateTypoResponsive,
	inArray,
	unit,
} from '../../controls';

const responsiveCss = ( attributes, device = 'Desktop' ) => {
	const {
		uniqueId,
		blockName,
		template,
		detailedWeatherAndUpdateMargin,
		splwBorderWidth,
		splwBorderRadius,
		enableSplwBoxShadow,
		splwBoxShadow,
		splwMaxWidth,
		weatherConditionIconSize,
		temperatureScaleMargin,
		displayWeatherAttribution,
		displayDateUpdateTime,
		currentWeatherCardWidth,
		showLocationName,
		showCurrentTime,
		showCurrentDate,
		displayTemperature,
		displayLowAndHighTemp,
		displayWeatherConditions,
		weatherConditionIcon,
		splwPadding,
		dateTimeGap,
		regionalPreferenceMargin,
		temperatureScaleFontSize,
		additionalCarouselColumns,
		displayWeatherMap,
	} = attributes;

	const isCurrentWeatherCard = inArray(
		[
			'accordion-one',
			'accordion-two',
			'accordion-three',
			'accordion-four',
			'combined-one',
			'grid-one',
		],
		template
	);

	const css = [
		// container css.
		{
			selector: `#${ uniqueId }.sp-location-weather-block-wrapper`,
			styles: {
				'max-width': cssDataCheck(
					splwMaxWidth.device[ device ],
					unit( splwMaxWidth, device )
				),
			},
		},
		// location name css.
		showLocationName && {
			selector: `#${ uniqueId } .spl-weather-card-location-name`,
			styles: {
				...generateTypoResponsive( attributes, device, 'locationName' ),
			},
		},
		// date time css.
		( showCurrentTime || showCurrentDate ) && {
			selector: `#${ uniqueId } .spl-weather-card-date-time`,
			styles: {
				...generateTypoResponsive( attributes, device, 'dateTime' ),
			},
		},
		( showLocationName || showCurrentTime || showCurrentDate ) && {
			selector: `#${ uniqueId } .spl-weather-header-info-wrapper`,
			styles: {
				gap: cssDataCheck(
					dateTimeGap.device[ device ],
					unit( dateTimeGap, device )
				),
				margin: cssDataCheck(
					regionalPreferenceMargin.device[ device ],
					unit( regionalPreferenceMargin, device )
				),
			},
		},
		// current weather.
		weatherConditionIcon && {
			selector: `#${ uniqueId } :is(.spl-weather-condition-icon, .spl-weather-condition-icon img)`,
			styles: {
				width: cssDataCheck(
					weatherConditionIconSize.device[ device ],
					unit( weatherConditionIconSize, device )
				),
				height: cssDataCheck(
					weatherConditionIconSize.device[ device ],
					unit( weatherConditionIconSize, device )
				),
				'max-width': 'unset',
			},
		},
		// temp scale.
		...( displayTemperature
			? [
					{
						selector: `#${ uniqueId } .spl-weather-current-weather-icon-wrapper`,
						styles: {
							margin: cssDataCheck(
								temperatureScaleMargin.device[ device ],
								unit( temperatureScaleMargin, device )
							),
						},
					},
					{
						selector: `#${ uniqueId } .spl-weather-current-temp`,
						styles: {
							...generateTypoResponsive(
								attributes,
								device,
								'temperatureScale'
							),
						},
					},
					{
						selector: `#${ uniqueId } .spl-weather-temperature-metric`,
						styles: {
							...generateTypoResponsive(
								attributes,
								device,
								'temperatureUnit'
							),
						},
					},
			  ]
			: [] ),
		// weather desc.
		displayWeatherConditions && {
			selector: `#${ uniqueId } .spl-weather-card-short-desc`,
			styles: {
				...generateTypoResponsive( attributes, device, 'weatherDesc' ),
			},
		},
		...( isCurrentWeatherCard
			? [
					{
						selector: `#${ uniqueId } .spl-weather-current-weather-card .spl-weather-card-daily-details .spl-weather-custom-slider-item`,
						styles: {
							'min-width': `calc(100% / ${ additionalCarouselColumns?.device[ device ] })`,
						},
					},
					{
						selector: `#${ uniqueId } .spl-weather-current-weather-card .spl-weather-custom-slider-nav-next`,
						styles: {
							'margin-right': `-${
								parseInt( splwPadding.device[ device ].right ) +
								4
							}${ splwPadding.unit[ device ] }`,
						},
					},
					{
						selector: `#${ uniqueId } .spl-weather-current-weather-card .spl-weather-custom-slider-nav-prev`,
						styles: {
							'margin-left': `-${
								parseInt( splwPadding.device[ device ].left ) +
								4
							}${ splwPadding.unit[ device ] }`,
						},
					},
			  ]
			: [] ),
		// Footer css.
		...( displayDateUpdateTime
			? [
					{
						selector: `#${ uniqueId } .spl-weather-detailed`,
						styles: {
							margin: cssDataCheck(
								detailedWeatherAndUpdateMargin.device[ device ],
								unit( detailedWeatherAndUpdateMargin, device )
							),
						},
					},
					{
						selector: `#${ uniqueId } .spl-weather-detailed.has-padding`,
						styles: {
							'padding-right': cssDataCheck(
								splwPadding.device[ device ][ 'right' ],
								unit( splwPadding, device )
							),
						},
					},
					{
						selector: ` #${ uniqueId } .spl-weather-last-updated-time`,
						styles: {
							...generateTypoResponsive(
								attributes,
								device,
								'detailedWeatherAndUpdate'
							),
						},
					},
			  ]
			: [] ),
		displayWeatherAttribution && {
			selector: `#${ uniqueId } .spl-weather-attribution`,
			styles: {
				...generateTypoResponsive(
					attributes,
					device,
					'weatherAttribution'
				),
			},
		},
		// Current Weather Card css.
		...( inArray( [ 'accordion', 'grid', 'combined' ], blockName )
			? [
					{
						selector: `#${ uniqueId } .spl-weather-current-weather-card`,
						styles: {
							width:
								device === 'Desktop' && displayWeatherMap
									? `calc(${ cssDataCheck(
											currentWeatherCardWidth.device[
												device
											],
											unit(
												currentWeatherCardWidth,
												device
											)
									  ) } - 10px)`
									: '100%',
							padding: cssDataCheck(
								splwPadding.device[ device ],
								unit( splwPadding, device )
							),
						},
					},
					{
						selector: `#${ uniqueId } .sp-weather-card-map-renderer`,
						styles: {
							width:
								inArray(
									[ 'grid-two', 'grid-three' ],
									template
								) || device !== 'Desktop'
									? '100%'
									: `calc(100% - ${ cssDataCheck(
											currentWeatherCardWidth.device[
												device
											],
											unit(
												currentWeatherCardWidth,
												device
											)
									  ) } - 10px)`,
							height: {
								Desktop: '',
								Tablet: '294px',
								Mobile: '230px',
							}[ device ],
						},
					},
			  ]
			: [] ),
	];

	return css;
};

export default responsiveCss;
