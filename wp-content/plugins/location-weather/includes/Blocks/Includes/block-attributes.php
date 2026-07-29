<?php
/**
 * Block Attributes
 *
 * @package Location_weather_Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

$brand_color = '#F26C0D';
require LOCATION_WEATHER_TEMPLATE_PATH . '/Blocks/Includes/attributes.php';

$vertical_block_override_attr = array(
	'blockName'                          => array(
		'type'    => 'string',
		'default' => 'vertical',
	),
	'align'                              => array(
		'type'    => 'string',
		'default' => 'none',
	),
	'bgColor'                            => array(
		'type'    => 'string',
		'default' => $brand_color,
	),
	'stripedColor'                       => array(
		'type'    => 'string',
		'default' => '',
	),
	'splwMaxWidth'                       => single_value_responsive_attr( 360, 360, 360 ),
	'additionalCarouselColumns'          => array(
		'type'    => 'object',
		'default' => array(
			'device' => array(
				'Desktop' => 2,
				'Tablet'  => 2,
				'Mobile'  => 1,
			),
		),
	),
	'splwPadding'                        => array(
		'type'    => 'object',
		'default' => array(
			'device'    => array(
				'Desktop' => array(
					'top'    => '20',
					'right'  => '20',
					'bottom' => '0',
					'left'   => '20',
				),
				'Tablet'  => array(
					'top'    => '20',
					'right'  => '20',
					'bottom' => '0',
					'left'   => '20',
				),
				'Mobile'  => array(
					'top'    => '20',
					'right'  => '20',
					'bottom' => '0',
					'left'   => '20',
				),
			),
			'unit'      => array(
				'Desktop' => 'px',
				'Tablet'  => 'px',
				'Mobile'  => 'px',
			),
			'allChange' => false,
		),
	),
	'regionalPreferenceMargin'           => spacing_value_responsive_attr( '0', '0', '18', '0' ),
	'weatherAttributionBgColor'          => array(
		'type'    => 'string',
		'default' => '#00000036',
	),
	'additionalDataPadding'              => spacing_value_responsive_attr( '3', '2', '3', '2' ),
	'detailedWeatherAndUpdateLineHeight' => single_value_responsive_attr( 12 ),
	// tabs filter.
	'forecastTabsGap'                    => single_value_responsive_attr( 20 ),
	'forecastTabsLabelColor'             => array(
		'type'    => 'object',
		'default' => array(
			'color'  => '',
			'active' => '',
		),
	),
	'forecastActiveTabsBottomLineColor'  => array(
		'type'    => 'string',
		'default' => '#FFFFFF',
	),
	'forecastTabsBottomLineWidth'        => array(
		'type'    => 'object',
		'default' => array(
			'value' => 2,
			'unit'  => 'px',
		),
	),
	'forecastTabsBottomLineColor'        => array(
		'type'    => 'object',
		'default' => 'rgb(236 234 233 / 50%)',
	),
	'forecastTabsFullWidthBottomLine'    => array(
		'type'    => 'object',
		'default' => array(
			'value' => 1,
			'unit'  => 'px',
		),
	),
);

$horizontal_block_override_attrs = array(
	'blockName'                         => array(
		'type'    => 'string',
		'default' => 'horizontal',
	),
	'bgColor'                           => array(
		'type'    => 'string',
		'default' => $brand_color,
	),
	'splwMaxWidth'                      => array(
		'type'    => 'object',
		'default' => array(
			'device' => array(
				'Desktop' => 800,
				'Tablet'  => '',
				'Mobile'  => '',
			),
			'unit'   => array(
				'Desktop' => 'px',
				'Tablet'  => 'px',
				'Mobile'  => 'px',
			),
		),
	),
	// tabs filter.
	'forecastTabsGap'                   => single_value_responsive_attr( 20 ),
	'forecastTabsLabelColor'            => array(
		'type'    => 'object',
		'default' => array(
			'color'  => '',
			'active' => '',
		),
	),
	'forecastActiveTabsBottomLineColor' => array(
		'type'    => 'string',
		'default' => '#FFFFFF',
	),
	'forecastTabsBottomLineWidth'       => array(
		'type'    => 'object',
		'default' => array(
			'value' => 2,
			'unit'  => 'px',
		),
	),
	'forecastTabsBottomLineColor'       => array(
		'type'    => 'object',
		'default' => 'rgb(236 234 233 / 50%)',
	),
	'forecastTabsFullWidthBottomLine'   => array(
		'type'    => 'object',
		'default' => array(
			'value' => 1,
			'unit'  => 'px',
		),
	),
	'forecastCarouselHorizontalGap'     => single_value_responsive_attr( 0, 0, 0, 'px' ),
	'pluginUrl'                         => array(
		'type'    => 'string',
		'default' => '',
	),
	'weatherAttributionBgColor'         => array(
		'type'    => 'string',
		'default' => '#00000036',
	),
	'locationNameFontSize'              => single_value_responsive_attr( 14 ),
	'locationNameLineHeight'            => single_value_responsive_attr( 20 ),
);

$tabs_block_override_attr = array(
	// template color attr.
	'templatePrimaryColor'         => array(
		'type'    => 'string',
		'default' => '#2F2F2F',
	),
	'templateSecondaryColor'       => array(
		'type'    => 'string',
		'default' => '#757575',
	),
	'blockName'                    => array(
		'type'    => 'string',
		'default' => 'tabs',
	),
	'displayWeatherMap'            => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'splwDefaultOpenTab'           => array(
		'type'    => 'string',
		'default' => 'current_weather',
	),
	'splwTabOrientation'           => array(
		'type'    => 'string',
		'default' => 'horizontal',
	),
	'splwTabAlignment'             => array(
		'type'    => 'string',
		'default' => 'left',
	),
	'tabTitleColors'               => array(
		'type'    => 'object',
		'default' => array(
			'color'       => '#FFFFFF',
			'activeColor' => $brand_color,
		),
	),
	'tabTitleBgColors'             => array(
		'type'    => 'object',
		'default' => array(
			'color'       => $brand_color,
			'activeColor' => '#fff',
		),
	),
	'tabTopBorderColor'            => array(
		'type'    => 'object',
		'default' => $brand_color,
	),
	'tabTopBorderWidth'            => array(
		'type'    => 'object',
		'default' => array(
			'device' => array(
				'Desktop' => 4,
				'Tablet'  => '',
				'Mobile'  => '',
			),
			'unit'   => array(
				'Desktop' => 'px',
				'Tablet'  => 'px',
				'Mobile'  => 'px',
			),
		),
	),
	'splwBorder'                   => array(
		'type'    => 'object',
		'default' => array(
			'style'      => 'solid',
			'color'      => $brand_color,
			'hoverColor' => '',
		),
	),
	'splwPadding'                  => spacing_value_responsive_attr( '26', '26', '26', '26' ),
	'temperatureScaleTypography'   => array(
		'type'    => 'object',
		'default' => array(
			'family'     => '',
			'fontWeight' => '700',
			'style'      => 'normal',
			'transform'  => 'none',
			'decoration' => 'none',
		),
	),
	'forecastDataIconSize'         => array(
		'type'    => 'object',
		'default' => array(
			'device' => array(
				'Desktop' => 32,
				'Tablet'  => '',
				'Mobile'  => '',
			),
			'unit'   => array(
				'Desktop' => 'px',
				'Tablet'  => 'px',
				'Mobile'  => 'px',
			),
		),
	),
	'temperatureScaleColor'        => array(
		'type'    => 'string',
		'default' => '',
	),
	'temperatureScaleFontSize'     => array(
		'type'    => 'object',
		'default' => array(
			'device' => array(
				'Desktop' => 64,
				'Tablet'  => '',
				'Mobile'  => '',
			),
			'unit'   => array(
				'Desktop' => 'px',
				'Tablet'  => 'px',
				'Mobile'  => 'px',
			),
		),
	),
	'temperatureScaleLineHeight'   => array(
		'type'    => 'object',
		'default' => array(
			'device' => array(
				'Desktop' => 72,
				'Tablet'  => '',
				'Mobile'  => '',
			),
			'unit'   => array(
				'Desktop' => 'px',
				'Tablet'  => 'px',
				'Mobile'  => 'px',
			),
		),
	),
	'weatherDescTypography'        => array(
		'type'    => 'object',
		'default' => array(
			'family'     => '',
			'fontWeight' => '600',
			'style'      => 'normal',
			'transform'  => 'capitalize',
			'decoration' => 'none',
		),
	),
	'additionalDataPadding'        => array(
		'type'    => 'object',
		'default' => array(
			'device'    => array(
				'Desktop' => array(
					'top'    => '8',
					'right'  => '14',
					'bottom' => '8',
					'left'   => '14',
				),
				'Tablet'  => array(
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				),
				'Mobile'  => array(
					'top'    => '5',
					'right'  => '10',
					'bottom' => '5',
					'left'   => '10',
				),
			),
			'unit'      => array(
				'Desktop' => 'px',
				'Tablet'  => 'px',
				'Mobile'  => 'px',
			),
			'allChange' => false,
		),
	),
	'additionalDataLabelFontSize'  => single_value_responsive_attr( 14, '', 12 ),
	'additionalDataValueFontSize'  => single_value_responsive_attr( 14, '', 12 ),
	'additionalDataMargin'         => array(
		'type'    => 'object',
		'default' => array(
			'device'    => array(
				'Desktop' => array(
					'top'    => '14',
					'right'  => '0',
					'bottom' => '8',
					'left'   => '0',
				),
				'Tablet'  => array(
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				),
				'Mobile'  => array(
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				),
			),
			'unit'      => array(
				'Desktop' => 'px',
				'Tablet'  => 'px',
				'Mobile'  => 'px',
			),
			'allChange' => false,
		),
	),
	// Weather Attribution attr.
	'weatherAttributionTypography' => array(
		'type'    => 'object',
		'default' => array(
			'family'     => '',
			'fontWeight' => '400',
			'style'      => 'normal',
			'transform'  => 'none',
			'decoration' => 'none',
		),
	),
	'weatherAttributionBgColor'    => array(
		'type'    => 'string',
		'default' => '#00000036',
	),
	'temperatureUnitFontSize'      => single_value_responsive_attr( 21 ),
	'temperatureUnitLineHeight'    => single_value_responsive_attr( 27 ),
	'splwBorderWidth'              => spacing_value_attr( '2', '2', '2', '2' ),
);

$table_block_override_attr = array(
	// template color attr.
	'templatePrimaryColor'       => array(
		'type'    => 'string',
		'default' => '#2F2F2F',
	),
	'templateSecondaryColor'     => array(
		'type'    => 'string',
		'default' => '#757575',
	),
	'blockName'                  => array(
		'type'    => 'string',
		'default' => 'table',
	),
	'forecastData'               => array(
		'type'    => 'array',
		'default' => array(
			array(
				'id'    => 1,
				'name'  => 'temperature',
				'value' => true,
			),
			array(
				'id'    => 4,
				'name'  => 'wind',
				'value' => true,
			),
			array(
				'id'    => 5,
				'name'  => 'humidity',
				'value' => true,
			),
			array(
				'id'    => 6,
				'name'  => 'pressure',
				'value' => true,
			),
			array(
				'id'    => 2,
				'name'  => 'precipitation',
				'value' => true,
			),
			array(
				'id'    => 3,
				'name'  => 'rainchance',
				'value' => true,
			),
			array(
				'id'    => 7,
				'name'  => 'snow',
				'value' => false,
			),
		),
	),
	'temperatureScaleTypography' => array(
		'type'    => 'object',
		'default' => array(
			'family'     => '',
			'fontWeight' => '700',
			'style'      => 'normal',
			'transform'  => 'none',
			'decoration' => 'none',
		),
	),
	'tablePreferenceBorder'      => array(
		'type'    => 'object',
		'default' => array(
			'style' => 'solid',
			'color' => '#DDDDDD',
		),
	),
	'tableHeaderColor'           => array(
		'type'    => 'string',
		'default' => '',
	),
	'tableHeaderBgColor'         => array(
		'type'    => 'string',
		'default' => '#e7ecf1',
	),
	'tableEvenRowColor'          => array(
		'type'    => 'string',
		'default' => '#FFFFFF',
	),
	'tableOddRowColor'           => array(
		'type'    => 'string',
		'default' => '#F4F4F4',
	),
	'temperatureUnitFontSize'    => single_value_responsive_attr( 21 ),
	'temperatureUnitLineHeight'  => single_value_responsive_attr( 27 ),
	'tablePreferenceBorderWidth' => spacing_value_attr( '1', '1', '1', '1' ),
	'splwBorder'                 => array(
		'type'    => 'object',
		'default' => array(
			'style'      => 'solid',
			'color'      => '#E2E2E2',
			'hoverColor' => '',
		),
	),
	'splwBorderRadius'           => spacing_value_attr( '4', '4', '4', '4' ),
	'additionalDataPadding'      => spacing_value_responsive_attr( '8', '14', '8', '14' ),
	'additionalDataMargin'       => spacing_value_responsive_attr( '0', '0', '-2', '0' ),
	'forecastDataIconSize'       => single_value_responsive_attr( 32 ),
	'temperatureScaleFontSize'   => single_value_responsive_attr( 64 ),
	'temperatureScaleLineHeight' => single_value_responsive_attr( 72 ),
	'additionalDataIconSize'     => single_value_responsive_attr( 14 ),
	'weatherAttributionBgColor'  => array(
		'type'    => 'string',
		'default' => '#00000036',
	),
);

$grid_block_override_attr = array(
	'blockName'                         => array(
		'type'    => 'string',
		'default' => 'grid',
	),
	'currentWeatherCardWidth'           => single_value_responsive_attr( 50, 100, 100, '%' ),
	'currentWeatherMargin'              => spacing_value_responsive_attr( '0', '0', '24', '0' ),
	'additionalDataColor'               => array(
		'type'    => 'object',
		'default' => array(
			'color' => '',
			'hover' => '#FFFFFF',
		),
	),
	'additionalDataBgType'              => array(
		'type'    => 'object',
		'default' => array(
			'color' => 'bgColor',
			'hover' => 'bgColor',
		),
	),
	'additionalDataBgColor'             => array(
		'type'    => 'object',
		'default' => array(
			'color' => '#f2f7fc',
			'hover' => '#131F49',
		),
	),
	'additionalDataBgGradient'          => array(
		'type'    => 'object',
		'default' => array(
			'color' => 'linear-gradient(135deg, #A1C4FD 0%, #C2E9FB 50%, #E0EAFC 100%)',
			'hover' => 'linear-gradient(135deg, #A1C4FD 0%, #C2E9FB 50%, #E0EAFC 100%)',
		),
	),
	'additionalDataBorder'              => array(
		'type'    => 'object',
		'default' => array(
			'style' => 'solid',
			'color' => '#E2E2E2',
		),
	),
	'additionalDataBorderWidth'         => spacing_value_attr( '1', '1', '1', '1' ),
	'additionalDataBorderRadius'        => spacing_value_attr( '8', '8', '8', '8' ),
	'enableAdditionalDataBoxShadow'     => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'additionalDataBoxShadow'           => array(
		'type'    => 'object',
		'default' => array(
			'value'      => array(
				'top'    => '4',
				'right'  => '4',
				'bottom' => '8',
				'left'   => '0',
			),
			'unit'       => 'Outset',
			'color'      => '#E0E0E0',
			'hoverColor' => '',
		),
	),
	'additionalDataPadding'             => spacing_value_responsive_attr( '20', '20', '20', '20' ),
	'forecastLabelTypography'           => array(
		'type'    => 'object',
		'default' => array(
			'family'     => '',
			'fontWeight' => '600',
			'style'      => 'normal',
			'transform'  => 'none',
			'decoration' => 'none',
		),
	),
	'forecastCarouselColumns'           => array(
		'type'    => 'object',
		'default' => array(
			'device' => array(
				'Desktop' => 11,
				'Tablet'  => 5,
				'Mobile'  => 3,
			),
		),
	),
	'gridHourlyForecastColor'           => array(
		'type'    => 'object',
		'default' => array(
			'color' => '',
			'hover' => '',
		),
	),
	'hourlyForecastBgType'              => array(
		'type'    => 'string',
		'default' => 'bgColor',
	),
	'hourlyForecastBgColor'             => array(
		'type'    => 'string',
		'default' => '#f2f7fc',
	),
	'hourlyForecastBgGradient'          => array(
		'type'    => 'string',
		'default' => 'linear-gradient(135deg, #A1C4FD 0%, #C2E9FB 50%, #E0EAFC 100%)',
	),
	'hourlyForecastBorder'              => array(
		'type'    => 'object',
		'default' => array(
			'style' => 'solid',
			'color' => '#E2E2E2',
		),
	),
	'hourlyForecastBorderWidth'         => spacing_value_attr( '1', '1', '1', '1' ),
	'hourlyForecastBorderRadius'        => spacing_value_attr( '8', '8', '8', '8' ),
	'enableHourlyForecastBoxShadow'     => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'hourlyForecastBoxShadow'           => array(
		'type'    => 'object',
		'default' => array(
			'value'      => array(
				'top'    => '4',
				'right'  => '4',
				'bottom' => '8',
				'left'   => '0',
			),
			'unit'       => 'Outset',
			'color'      => '#E0E0E0',
			'hoverColor' => '',
		),
	),
	'hourlyForecastPadding'             => spacing_value_responsive_attr( '20', '20', '20', '24' ),
	'dailyForecastColumns'              => array(
		'type'    => 'object',
		'default' => array(
			'device' => array(
				'Desktop' => 5,
				'Tablet'  => 3,
				'Mobile'  => 1,
			),
		),
	),
	'dailyForecastColor'                => array(
		'type'    => 'object',
		'default' => array(
			'color' => '',
			'hover' => '#FFFFFF',
		),
	),
	'forecastSelectDividerColor'        => array(
		'type'    => 'string',
		'default' => 'rgba(204, 204, 204, 1)',
	),
	'dailyForecastBgType'               => array(
		'type'    => 'object',
		'default' => array(
			'color' => 'bgColor',
			'hover' => 'bgColor',
		),
	),
	'dailyForecastBgColor'              => array(
		'type'    => 'object',
		'default' => array(
			'color' => '#f2f7fc',
			'hover' => '#131F49',
		),
	),
	'dailyForecastBgGradient'           => array(
		'type'    => 'object',
		'default' => array(
			'color' => 'linear-gradient(135deg, #A1C4FD 0%, #C2E9FB 50%, #E0EAFC 100%)',
			'hover' => 'linear-gradient(135deg, #A1C4FD 0%, #C2E9FB 50%, #E0EAFC 100%)',
		),
	),
	'dailyForecastHoverBottomLineColor' => array(
		'type'    => 'string',
		'default' => '#1E1E1E',
	),
	'dailyForecastBorder'               => array(
		'type'    => 'object',
		'default' => array(
			'style' => 'solid',
			'color' => '#E2E2E2',
		),
	),
	'dailyForecastBorderWidth'          => spacing_value_attr( '1', '1', '1', '1' ),
	'dailyForecastBorderRadius'         => spacing_value_attr( '8', '8', '8', '8' ),
	'enableDailyForecastBoxShadow'      => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'dailyForecastBoxShadow'            => array(
		'type'    => 'object',
		'default' => array(
			'value'      => array(
				'top'    => '4',
				'right'  => '4',
				'bottom' => '8',
				'left'   => '0',
			),
			'unit'       => 'Outset',
			'color'      => '#E0E0E0',
			'hoverColor' => '',
		),
	),
	'dailyForecastPadding'              => spacing_value_responsive_attr( '20', '20', '20', '20' ),
	'forecastLiveFilterBorder'          => array(
		'type'    => 'object',
		'default' => array(
			'style' => 'none',
			'color' => '#cccccc',
		),
	),
	// tabs filter.
	'forecastTabsGap'                   => single_value_responsive_attr( 20 ),
	'forecastTabsLabelColor'            => array(
		'type'    => 'object',
		'default' => array(
			'color'  => '',
			'active' => '',
		),
	),
	'forecastActiveTabsBottomLineColor' => array(
		'type'    => 'string',
		'default' => '#2F2F2F',
	),
	'forecastTabsBottomLineWidth'       => array(
		'type'    => 'object',
		'default' => array(
			'value' => 2,
			'unit'  => 'px',
		),
	),
	'forecastTabsBottomLineColor'       => array(
		'type'    => 'object',
		'default' => '#E2E2E2',
	),
	'forecastTabsFullWidthBottomLine'   => array(
		'type'    => 'object',
		'default' => array(
			'value' => 1,
			'unit'  => 'px',
		),
	),
	'forecastDataIconSize'              => single_value_responsive_attr( 32 ),
);

$windy_map_attr = array(
	'blockName'                 => array(
		'type'    => 'string',
		'default' => 'windy-map',
	),
	'align'                     => array(
		'type'    => 'string',
		'default' => 'wide',
	),
	'searchWeatherBy'           => array(
		'type'    => 'string',
		'default' => 'latlong',
	),
	'weatherMapPadding'         => spacing_value_responsive_attr( '20', '20', '20', '20' ),
	'weatherMapBgColorType'     => array(
		'type'    => 'string',
		'default' => 'bgColor',
	),
	'weatherMapBgColor'         => array(
		'type'    => 'string',
		'default' => '',
	),
	'defaultDataLayerSelection' => array(
		'type'    => 'string',
		'default' => 'wind',
	),
);

$common_attr = array(
	// Layout tabs options.
	'bgColorType'             => array(
		'type'    => 'string',
		'default' => 'bgColor',
	),
	'bgColor'                 => array(
		'type'    => 'string',
		'default' => '#FFFFFF',
	),
	'bgGradient'              => array(
		'type'    => 'string',
		'default' => 'linear-gradient(135deg, #8E2DE2 0%, #4A00E0 100%)',
	),
	'splwBorder'              => array(
		'type'    => 'object',
		'default' => array(
			'style'      => 'none',
			'color'      => '#e2e2e2',
			'hoverColor' => '',
		),
	),
	'splwBorderWidth'         => spacing_value_attr( '1', '1', '1', '1' ),
	'splwBorderRadius'        => spacing_value_attr( '8', '8', '8', '8' ),
	'enableSplwBoxShadow'     => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'splwBoxShadow'           => array(
		'type'    => 'object',
		'default' => array(
			'value'      => array(
				'top'    => '0',
				'right'  => '3',
				'bottom' => '6',
				'left'   => '0',
			),
			'unit'       => 'Outset',
			'color'      => '#00000026',
			'hoverColor' => '',
		),
	),
	'splwPadding'             => array(
		'type'    => 'object',
		'default' => array(
			'device'    => array(
				'Desktop' => array(
					'top'    => '20',
					'right'  => '20',
					'bottom' => '20',
					'left'   => '20',
				),
				'Tablet'  => array(
					'top'    => '20',
					'right'  => '20',
					'bottom' => '20',
					'left'   => '20',
				),
				'Mobile'  => array(
					'top'    => '12',
					'right'  => '12',
					'bottom' => '12',
					'left'   => '12',
				),
			),
			'unit'      => array(
				'Desktop' => 'px',
				'Tablet'  => 'px',
				'Mobile'  => 'px',
			),
			'allChange' => false,
		),
	),
	'splwMaxWidth'            => single_value_responsive_attr( 960 ),
	'showPreloader'           => array(
		'type'    => 'boolean',
		'default' => true,
	),
	'searchWeatherBy'         => array(
		'type'    => 'string',
		'default' => 'latlong',
	),
	// basic preference tabs options.
	'locationNameColor'       => array(
		'type'    => 'string',
		'default' => '',
	),
	'locationNameTypography'  => array(
		'type'    => 'object',
		'default' => array(
			'family'     => '',
			'fontWeight' => '400',
			'style'      => 'normal',
			'transform'  => 'none',
			'decoration' => 'none',
		),
	),
	'locationNameFontSize'    => single_value_responsive_attr( 16, 16, 14 ),
	'locationNameLineHeight'  => single_value_responsive_attr( 19 ),
	'locationNameFontSpacing' => single_value_responsive_attr( 0 ),
	'displayDateUpdateTime'   => array(
		'type'    => 'boolean',
		'default' => true,
	),
	'showLocationName'        => array(
		'type'    => 'boolean',
		'default' => true,
	),
	'showCurrentDate'         => array(
		'type'    => 'boolean',
		'default' => true,
	),
	'showCurrentTime'         => array(
		'type'    => 'boolean',
		'default' => true,
	),
	'splwDateFormat'          => array(
		'type'    => 'string',
		'default' => 'M j, Y',
	),
	'splwCustomDateFormat'    => array(
		'type'    => 'string',
		'default' => 'F j, Y',
	),
	'splwTimeFormat'          => array(
		'type'    => 'string',
		'default' => 'g:i A',
	),
	'splwTimeZone'            => array(
		'type'    => 'string',
		'default' => 'auto',
	),
	'splwLanguage'            => array(
		'type'    => 'string',
		'default' => 'en',
	),
	'dateTimeColor'           => array(
		'type'    => 'string',
		'default' => '',
	),
	'dateTimeTypography'      => array(
		'type'    => 'object',
		'default' => array(
			'family'     => '',
			'fontWeight' => '400',
			'style'      => 'normal',
			'transform'  => 'none',
			'decoration' => 'none',
		),
	),
	'dateTimeLineHeight'      => single_value_responsive_attr( 19 ),
	'dateTimeFontSpacing'     => single_value_responsive_attr( 0 ),
	'dateTimeGap'             => single_value_responsive_attr( 16 ),
);

$aqi_attributes = array(
	'lw_api_type'                        => array(
		'type'    => 'string',
		'default' => 'openweather_api',
	),
	'secondaryBgColor'                   => array(
		'type'    => 'string',
		'default' => '#ffffff',
	),
	'aqiSectionGap'                      => single_value_responsive_attr( 20 ),
	'templatePrimaryColor'               => array(
		'type'    => 'string',
		'default' => '#2F2F2F',
	),
	'templateSecondaryColor'             => array(
		'type'    => 'string',
		'default' => '#757575',
	),
	'enableScaleBar'                     => array(
		'type'    => 'boolean',
		'default' => true,
	),
	'aqiSummaryHeadingLabel'             => array(
		'type'    => 'string',
		'default' => __( 'Today’s Air Quality Index (AQI)', 'location-weather' ),
	),
	'aqiSummaryLabelColors'              => array(
		'type'    => 'string',
		'default' => '',
	),
	'aqiSummaryLabelTypography'          => array(
		'type'    => 'object',
		'default' => array(
			'family'     => '',
			'fontWeight' => '600',
			'style'      => 'normal',
			'transform'  => 'none',
			'decoration' => 'none',
		),
	),
	'aqiSummaryLabelFontSize'            => single_value_responsive_attr( 24, 20, 20 ),
	'aqiSummaryLabelFontSpacing'         => single_value_responsive_attr( 0 ),
	'aqiSummaryLabelLineHeight'          => single_value_responsive_attr( 28 ),
	'aqiSummaryTextColor'                => array(
		'type'    => 'string',
		'default' => '',
	),
	'aqiSummaryBgColorType'              => array(
		'type'    => 'string',
		'default' => 'bgColor',
	),
	'aqiSummaryBgColor'                  => array(
		'type'    => 'string',
		'default' => '#FFFFFF',
	),
	'aqiSummaryBgGradient'               => array(
		'type'    => 'string',
		'default' => 'linear-gradient(135deg,rgb(6,147,227) 0%,rgb(133,49,213) 100%)',
	),
	'aqiSummaryPadding'                  => array(
		'type'    => 'object',
		'default' => array(
			'device'    => array(
				'Desktop' => array(
					'top'    => '24',
					'right'  => '24',
					'bottom' => '24',
					'left'   => '24',
				),
				'Tablet'  => array(
					'top'    => '16',
					'right'  => '16',
					'bottom' => '16',
					'left'   => '16',
				),
				'Mobile'  => array(
					'top'    => '12',
					'right'  => '12',
					'bottom' => '12',
					'left'   => '12',
				),
			),
			'unit'      => array(
				'Desktop' => 'px',
				'Tablet'  => 'px',
				'Mobile'  => 'px',
			),
			'allChange' => false,
		),
	),
	'aqiSummaryMargin'                   => array(
		'type'    => 'object',
		'default' => array(
			'device'    => array(
				'Desktop' => array(
					'top'    => '0',
					'right'  => '0',
					'bottom' => '0',
					'left'   => '0',
				),
				'Tablet'  => array(
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				),
				'Mobile'  => array(
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				),
			),
			'unit'      => array(
				'Desktop' => 'px',
				'Tablet'  => 'px',
				'Mobile'  => 'px',
			),
			'allChange' => false,
		),
	),
	'enablePollutantDetails'             => array(
		'type'    => 'boolean',
		'default' => true,
	),
	'pollutantHeadingLabel'              => array(
		'type'    => 'string',
		'default' => __( 'Pollutant Details', 'location-weather' ),
	),
	'aqiPollutantHeadingColors'          => array(
		'type'    => 'string',
		'default' => '',
	),
	'aqiPollutantHeadingTypography'      => array(
		'type'    => 'object',
		'default' => array(
			'family'     => '',
			'fontWeight' => '600',
			'style'      => 'normal',
			'transform'  => 'none',
			'decoration' => 'none',
		),
	),
	'aqiPollutantHeadingFontSize'        => single_value_responsive_attr( 24, 20, 20 ),
	'aqiPollutantHeadingFontSpacing'     => single_value_responsive_attr( 0 ),
	'aqiPollutantHeadingLineHeight'      => single_value_responsive_attr( 28 ),
	'displayPollutantNameFormat'         => array(
		'type'    => 'string',
		'default' => 'both',
	),
	'displaySymbolDisplayStyle'          => array(
		'type'    => 'string',
		'default' => 'subscript',
	),
	'pollutantConditionColor'            => array(
		'type'    => 'string',
		'default' => '',
	),
	'pollutantConditionTypography'       => array(
		'type'    => 'object',
		'default' => array(
			'family'     => '',
			'fontWeight' => '600',
			'style'      => 'normal',
			'transform'  => 'none',
			'decoration' => 'none',
		),
	),
	'pollutantConditionFontSize'         => single_value_responsive_attr( 18, 18, 16 ),
	'pollutantConditionFontSpacing'      => single_value_responsive_attr( 0 ),
	'pollutantConditionLineHeight'       => single_value_responsive_attr( 21, 21, 20 ),
	'pollutantConditionLabelColors'      => array(
		'type'    => 'string',
		'default' => '',
	),
	'pollutantConditionLabelTypography'  => array(
		'type'    => 'object',
		'default' => array(
			'family'     => '',
			'fontWeight' => '500',
			'style'      => 'normal',
			'transform'  => 'none',
			'decoration' => 'none',
		),
	),
	'pollutantConditionLabelFontSize'    => single_value_responsive_attr( 16, 16, 14 ),
	'pollutantConditionLabelFontSpacing' => single_value_responsive_attr( 0 ),
	'pollutantConditionLabelLineHeight'  => single_value_responsive_attr( 19 ),
	'pollutantValueColors'               => array(
		'type'    => 'string',
		'default' => '',
	),
	'pollutantValueTypography'           => array(
		'type'    => 'object',
		'default' => array(
			'family'     => '',
			'fontWeight' => '600',
			'style'      => 'normal',
			'transform'  => 'none',
			'decoration' => 'none',
		),
	),
	'pollutantValueFontSize'             => single_value_responsive_attr( 16, 16, 14 ),
	'pollutantValueFontSpacing'          => single_value_responsive_attr( 0 ),
	'pollutantValueLineHeight'           => single_value_responsive_attr( 19 ),
	'pollutantBoxBgColor'                => array(
		'type'    => 'string',
		'default' => '',
	),
	'pollutantBoxPadding'                => array(
		'type'    => 'object',
		'default' => array(
			'device'    => array(
				'Desktop' => array(
					'top'    => '16',
					'right'  => '16',
					'bottom' => '16',
					'left'   => '16',
				),
				'Tablet'  => array(
					'top'    => '16',
					'right'  => '16',
					'bottom' => '16',
					'left'   => '16',
				),
				'Mobile'  => array(
					'top'    => '8',
					'right'  => '8',
					'bottom' => '8',
					'left'   => '8',
				),
			),
			'unit'      => array(
				'Desktop' => 'px',
				'Tablet'  => 'px',
				'Mobile'  => 'px',
			),
			'allChange' => false,
		),
	),
	'pollutantAreaMargin'                => array(
		'type'    => 'object',
		'default' => array(
			'device'    => array(
				'Desktop' => array(
					'top'    => '16',
					'right'  => '0',
					'bottom' => '0',
					'left'   => '0',
				),
				'Tablet'  => array(
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				),
				'Mobile'  => array(
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				),
			),
			'unit'      => array(
				'Desktop' => 'px',
				'Tablet'  => 'px',
				'Mobile'  => 'px',
			),
			'allChange' => false,
		),
	),
	'pollutantParametersHorizontalGap'   => single_value_responsive_attr( 16, 16, 8 ),
	'pollutantParametersVerticalGap'     => single_value_responsive_attr( 16, 16, 8 ),

	'aqiStandard'                        => array(
		'type'    => 'string',
		'default' => 'aqi-us',
	),

	'aqiSummaryToggleBorder'             => array(
		'type'    => 'object',
		'default' => array(
			'color'      => '#DDDDDD',
			'hoverColor' => '#DDDDDD',
			'style'      => 'solid',
		),
	),
	'aqiSummaryToggleBorderWidth'        => spacing_value_attr( '1', '1', '1', '1' ),
	'aqiSummaryToggleRadius'             => spacing_value_attr( '4', '4', '4', '4' ),
	'enableAqiSummaryBoxShadow'          => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'aqiPollutantDetailsBorder'          => array(
		'type'    => 'object',
		'default' => array(
			'color'      => '#DDDDDD',
			'hoverColor' => '#757575',
			'style'      => 'solid',
		),
	),
	'aqiPollutantDetailsBorderWidth'     => spacing_value_attr( '1', '1', '1', '1' ),
	'aqiPollutantDetailsBorderRadius'    => spacing_value_attr( '4', '4', '4', '4' ),
	'aqiLastUpdateTimeColor'             => array(
		'type'    => 'string',
		'default' => '',
	),
	'aqiLastUpdateTypography'            => array(
		'type'    => 'object',
		'default' => array(
			'family'     => '',
			'fontWeight' => '400',
			'style'      => 'normal',
			'transform'  => 'none',
			'decoration' => 'none',
		),
	),
	'aqiLastUpdateFontSize'              => single_value_responsive_attr( 13 ),
	'aqiLastUpdateFontSpacing'           => single_value_responsive_attr( 0 ),
	'aqiLastUpdateLineHeight'            => single_value_responsive_attr( 15 ),
);

$aqi_minimal_card = array(
	'blockName'                           => array(
		'type'    => 'string',
		'default' => 'aqi-minimal',
	),
	'splwMaxWidth'                        => single_value_responsive_attr( 360 ),
	'splwPadding'                         => spacing_value_responsive_attr( '20', '20', '0', '20' ),
	'splwBorder'                          => array(
		'type'    => 'object',
		'default' => array(
			'style'      => 'solid',
			'color'      => '#e2e2e2',
			'hoverColor' => '',
		),
	),
	'imageType'                           => array(
		'type'    => 'string',
		'default' => 'custom',
	),
	'bgImage'                             => array(
		'type'    => 'object',
		'default' => array(),
	),
	'bgImagePosition'                     => array(
		'type'    => 'string',
		'default' => 'center',
	),
	'bgImageAttachment'                   => array(
		'type'    => 'string',
		'default' => 'scroll',
	),
	'bgImageRepeat'                       => array(
		'type'    => 'string',
		'default' => 'no-repeat',
	),
	'bgImageSize'                         => array(
		'type'    => 'string',
		'default' => 'cover',
	),
	'showCurrentDate'                     => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'dateTimeColor'                       => array(
		'type'    => 'string',
		'default' => '',
	),
	'locationNameFontSize'                => single_value_responsive_attr( 14 ),
	'dateTimeFontSize'                    => single_value_responsive_attr( 14 ),
	'aqiSummaryHeadingLabel'              => array(
		'type'    => 'string',
		'default' => __( 'Today’s Air Quality', 'location-weather' ),
	),
	'aqiSummaryLabelFontSize'             => single_value_responsive_attr( 16, 16, 16 ),
	'detailedWeatherAndUpdateColor'       => array(
		'type'    => 'string',
		'default' => '',
	),
	'enableSummaryAqiCondition'           => array(
		'type'    => 'boolean',
		'default' => true,
	),
	'enableSummaryAqiDesc'                => array(
		'type'    => 'boolean',
		'default' => true,
	),
	'aqiSummaryDescTypography'            => array(
		'type'    => 'object',
		'default' => array(
			'family'     => '',
			'fontWeight' => '400',
			'style'      => 'normal',
			'transform'  => 'none',
			'decoration' => 'none',
		),
	),
	'aqiSummaryDescFontSize'              => single_value_responsive_attr( 14 ),
	'aqiSummaryDescFontSpacing'           => single_value_responsive_attr( 0 ),
	'aqiSummaryDescLineHeight'            => single_value_responsive_attr( 18 ),
	'aqiSummaryToggleBorder'              => array(
		'type'    => 'object',
		'default' => array(
			'color' => '#DDDDDD',
			'style' => 'solid',
		),
	),
	'aqiSummaryDescBgColor'               => array(
		'type'    => 'string',
		'default' => '',
	),
	'aqiSummaryPadding'                   => array(
		'type'    => 'object',
		'default' => array(
			'device'    => array(
				'Desktop' => array(
					'top'    => '12',
					'right'  => '12',
					'bottom' => '12',
					'left'   => '12',
				),
				'Tablet'  => array(
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				),
				'Mobile'  => array(
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				),
			),
			'unit'      => array(
				'Desktop' => 'px',
				'Tablet'  => 'px',
				'Mobile'  => 'px',
			),
			'allChange' => false,
		),
	),
	'aqiSummaryMargin'                    => array(
		'type'    => 'object',
		'default' => array(
			'device'    => array(
				'Desktop' => array(
					'top'    => '0',
					'right'  => '0',
					'bottom' => '20',
					'left'   => '0',
				),
				'Tablet'  => array(
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				),
				'Mobile'  => array(
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				),
			),
			'unit'      => array(
				'Desktop' => 'px',
				'Tablet'  => 'px',
				'Mobile'  => 'px',
			),
			'allChange' => false,
		),
	),
	'enablePollutantMeasurementUnit'      => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'enablePollutantIndicator'            => array(
		'type'    => 'boolean',
		'default' => true,
	),
	'pollutantConditionLabelFontSize'     => single_value_responsive_attr( 14, 14, 14 ),
	'pollutantValueFontSize'              => single_value_responsive_attr( 14, 14, 14 ),
	'aqiPollutantDetailsBorder'           => array(
		'type'    => 'object',
		'default' => array(
			'color' => '#DDDDDD',
			'style' => 'solid',
		),
	),
	'pollutantParametersHorizontalGap'    => single_value_responsive_attr( 12, 12, 12 ),
	'pollutantParametersVerticalGap'      => single_value_responsive_attr( 12, 12, 12 ),
	'pollutantBoxPadding'                 => array(
		'type'    => 'object',
		'default' => array(
			'device'    => array(
				'Desktop' => array(
					'top'    => '12',
					'right'  => '12',
					'bottom' => '12',
					'left'   => '12',
				),
				'Tablet'  => array(
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				),
				'Mobile'  => array(
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				),
			),
			'unit'      => array(
				'Desktop' => 'px',
				'Tablet'  => 'px',
				'Mobile'  => 'px',
			),
			'allChange' => false,
		),
	),
	'pollutantAreaMargin'                 => array(
		'type'    => 'object',
		'default' => array(
			'device'    => array(
				'Desktop' => array(
					'top'    => '0',
					'right'  => '0',
					'bottom' => '20',
					'left'   => '0',
				),
				'Tablet'  => array(
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				),
				'Mobile'  => array(
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				),
			),
			'unit'      => array(
				'Desktop' => 'px',
				'Tablet'  => 'px',
				'Mobile'  => 'px',
			),
			'allChange' => false,
		),
	),
	// Attribution attr.
	'displayWeatherAttribution'           => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'displayLinkToOpenWeatherMap'         => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'weatherAttributionTypography'        => array(
		'type'    => 'object',
		'default' => array(
			'family'     => '',
			'fontWeight' => '400',
			'style'      => 'normal',
			'transform'  => 'none',
			'decoration' => 'none',
		),
	),
	'weatherAttributionColor'             => array(
		'type'    => 'string',
		'default' => '#2f2f2f',
	),
	'weatherAttributionFontSize'          => single_value_responsive_attr( 12 ),
	'weatherAttributionFontSpacing'       => single_value_responsive_attr( 0 ),
	'weatherAttributionLineHeight'        => single_value_responsive_attr( 26 ),
	'detailedWeatherAndUpdateTypography'  => array(
		'type'    => 'object',
		'default' => array(
			'family'     => '',
			'fontWeight' => '400',
			'style'      => 'normal',
			'transform'  => 'none',
			'decoration' => 'none',
		),
	),
	'detailedWeatherAndUpdateFontSize'    => single_value_responsive_attr( 12 ),
	'detailedWeatherAndUpdateFontSpacing' => single_value_responsive_attr( 0 ),
	'detailedWeatherAndUpdateLineHeight'  => single_value_responsive_attr( 14 ),
	'detailedWeatherAndUpdateMargin'      => spacing_value_responsive_attr( '10', '0', '10', '0' ),
	'displayDateUpdateTime'               => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'weatherAttributionBgColor'           => array(
		'type'    => 'string',
		'default' => 'rgba(0, 0, 0, .2)',
	),
	'bgOverlay'                           => array(
		'type'    => 'string',
		'default' => '#00000075',
	),
	'videoType'                           => array(
		'type'    => 'string',
		'default' => 'youtube',
	),
	'bgVideo'                             => array(
		'type'    => 'object',
		'default' => array(),
	),
	'youtubeVideo'                        => array(
		'type'    => 'string',
		'default' => 'https://www.youtube.com/watch?v=CEh5Ej6LSSQ&t=2s&ab_channel=Md.KhalilUddin',
	),
);

// Merge necessary attributes for each the block.
$vertical_block_attributes = array_merge(
	$shared_attributes,
	get_color_default_attr(),
	$additional_data_carousel_attr,
	$vertical_block_override_attr,
);

$horizontal_block_attributes = array_merge(
	$shared_attributes,
	get_color_default_attr(),
	$additional_data_carousel_attr,
	$horizontal_block_override_attrs,
);

$table_block_attributes = array_merge(
	$shared_attributes,
	get_color_default_attr(),
	$table_block_override_attr
);

$grid_block_attributes = array_merge(
	$shared_attributes,
	$maps_attr,
	$additional_data_carousel_attr,
	$current_weather_card_default_attr,
	$grid_block_override_attr,
);

$tabs_block_attributes = array_merge(
	$shared_attributes,
	$table_block_override_attr,
	$maps_attr,
	$tabs_block_override_attr
);

$windy_map_block_attributes = array_merge( $block_required_attributes, $maps_attr, $windy_map_attr );

$aqi_minimal_card_block_attributes = array_merge( $block_required_attributes, $common_attr, $aqi_attributes, $aqi_minimal_card );
