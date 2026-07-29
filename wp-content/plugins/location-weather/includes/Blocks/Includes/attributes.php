<?php
/**
 * All Blocks Common Attributes File.
 *
 * @package Location_weather_Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

use ShapedPlugin\Weather\Blocks\Includes\Block_Helpers;

$brand_color = '#F26C0D';
if ( ! function_exists( 'get_color_default_attr' ) ) {
	/**
	 * Method get_color_default_attr.
	 *
	 * @param string $color color.
	 *
	 * @return array $colors_array.
	 */
	function get_color_default_attr( $color = '' ) {
		$colors_array = array(
			'locationNameColor'             => array(
				'type'    => 'string',
				'default' => $color,
			),
			'dateTimeColor'                 => array(
				'type'    => 'string',
				'default' => $color,
			),
			'temperatureScaleColor'         => array(
				'type'    => 'string',
				'default' => $color,
			),
			'weatherDescColor'              => array(
				'type'    => 'string',
				'default' => $color,
			),
			'additionalDataIconColor'       => array(
				'type'    => 'string',
				'default' => $color,
			),
			'additionalDataLabelColor'      => array(
				'type'    => 'string',
				'default' => $color,
			),
			'additionalDataValueColor'      => array(
				'type'    => 'string',
				'default' => $color,
			),
			'forecastDataColor'             => array(
				'type'    => 'string',
				'default' => $color,
			),
			'forecastLabelColor'            => array(
				'type'    => 'string',
				'default' => $color,
			),
			'detailedWeatherAndUpdateColor' => array(
				'type'    => 'string',
				'default' => $color,
			),
			'weatherAttributionColor'       => array(
				'type'    => 'string',
				'default' => $color,
			),
			'weatherConditionIconColor'     => array(
				'type'    => 'string',
				'default' => $color,
			),
			'forecastDataIconColor'         => array(
				'type'    => 'string',
				'default' => $color,
			),
		);
		return $colors_array;
	}
}
if ( ! function_exists( 'single_value_responsive_attr' ) ) {
	/**
	 * Method single_value_responsive_attr.
	 * Usually used for range-control with single value.
	 *
	 * @param string $desktop desktop value.
	 * @param string $tablet tablet value.
	 * @param string $mobile mobile value.
	 * @param string $unit unit value.
	 *
	 * @return $spacing
	 */
	function single_value_responsive_attr( $desktop = '', $tablet = '', $mobile = '', $unit = 'px' ) {
		return array(
			'type'    => 'object',
			'default' => array(
				'device' => array(
					'Desktop' => $desktop,
					'Tablet'  => $tablet,
					'Mobile'  => $mobile,
				),
				'unit'   => array(
					'Desktop' => $unit,
					'Tablet'  => $unit,
					'Mobile'  => $unit,
				),
			),
		);
	}
}
if ( ! function_exists( 'spacing_value_responsive_attr' ) ) {
	/**
	 * Method spacing_value_responsive_attr
	 * Usually used for fields which has four values (top, right, bottom, left).
	 *
	 * @param string  $top spacing top value.
	 * @param string  $right spacing right value.
	 * @param string  $bottom bottom value.
	 * @param string  $left left value.
	 * @param string  $unit unit value.
	 * @param boolean $all_change all_change.
	 *
	 * @return $spacing
	 */
	function spacing_value_responsive_attr( $top = '', $right = '', $bottom = '', $left = '', $unit = 'px', $all_change = false ) {
		return array(
			'type'    => 'object',
			'default' => array(
				'device'    => array(
					'Desktop' => array(
						'top'    => $top,
						'right'  => $right,
						'bottom' => $bottom,
						'left'   => $left,
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
					'Desktop' => $unit,
					'Tablet'  => $unit,
					'Mobile'  => $unit,
				),
				'allChange' => $all_change,
			),
		);
	}
}
if ( ! function_exists( 'spacing_value_attr' ) ) {
	/**
	 * Method spacing_value_attr
	 * Usually used for fields which has four values (top, right, bottom, left) and no device specified.
	 *
	 * @param string  $top spacing top value.
	 * @param string  $right spacing right value.
	 * @param string  $bottom bottom value.
	 * @param string  $left left value.
	 * @param string  $unit unit value.
	 * @param boolean $all_change all_change.
	 *
	 * @return $spacing
	 */
	function spacing_value_attr( $top = '', $right = '', $bottom = '', $left = '', $unit = 'px', $all_change = true ) {
		return array(
			'type'    => 'object',
			'default' => array(
				'value'     => array(
					'top'    => $top,
					'right'  => $right,
					'bottom' => $bottom,
					'left'   => $left,
				),
				'unit'      => $unit,
				'allChange' => $all_change,
			),
		);
	}
}

$block_required_attributes = array(
	'uniqueId'                  => array(
		'type'    => 'string',
		'default' => '',
	),
	'blockName'                 => array(
		'type'    => 'string',
		'default' => '',
	),
	'pluginUrl'                 => array(
		'type'    => 'string',
		'default' => '',
	),
	'dynamicClassNames'         => array(
		'type'    => 'object',
		'default' => array(),
	),
	'fontLists'                 => array(
		'type'    => 'string',
		'default' => '',
	),
	'iconUrl'                   => array(
		'type'    => 'string',
		'default' => '',
	),
	'customCss'                 => array(
		'type'    => 'string',
		'default' => '',
	),
	'customClassName'           => array(
		'type'    => 'string',
		'default' => '',
	),
	'align'                     => array(
		'type'    => 'string',
		'default' => 'wide',
	),
	'template'                  => array(
		'type'    => 'string',
		'default' => '',
	),
	'isPreview'                 => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'searchWeatherBy'           => array(
		'type'    => 'string',
		'default' => 'city_name',
	),
	'getDataByCityName'         => array(
		'type'    => 'string',
		'default' => 'London, GB',
	),
	'getDataByCityID'           => array(
		'type'    => 'string',
		'default' => '2643743',
	),
	'getDataByZIPCode'          => array(
		'type'    => 'string',
		'default' => '77070,US',
	),
	'getDataByCoordinates'      => array(
		'type'    => 'string',
		'default' => '51.509865,-0.118092',
	),
	'customCityName'            => array(
		'type'    => 'string',
		'default' => '',
	),
	// 'locationAutoDetect'        => array(
	// 'type'    => 'boolean',
	// 'default' => false,
	// ),
	'displayTemperatureUnit'    => array(
		'type'    => 'string',
		'default' => 'metric',
	),
	'displayPressureUnit'       => array(
		'type'    => 'string',
		'default' => 'hpa',
	),
	'displayPrecipitationUnit'  => array(
		'type'    => 'string',
		'default' => 'mm',
	),
	'displayWindSpeedUnit'      => array(
		'type'    => 'string',
		'default' => 'kmh',
	),
	'displayVisibilityUnit'     => array(
		'type'    => 'string',
		'default' => 'km',
	),
	// visibility attr.
	'splwHideOnDesktop'         => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'splwHideOnTablet'          => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'splwHideOnMobile'          => array(
		'type'    => 'boolean',
		'default' => false,
	),
	// global styles attr.
	'enableTemplateGlobalStyle' => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'templateTypography'        => array(
		'type'    => 'object',
		'default' => array(
			'family'     => '',
			'fontWeight' => '400',
		),
	),
	'templatePrimaryColor'      => array(
		'type'    => 'string',
		'default' => '#FFFFFF',
	),
	'templateSecondaryColor'    => array(
		'type'    => 'string',
		'default' => '#FFFFFF',
	),
);

$current_weather_card_default_attr = array(
	'enableSplwBoxShadow'           => array(
		'type'    => 'boolean',
		'default' => true,
	),
	'bgColor'                       => array(
		'type'    => 'string',
		'default' => '#F2F7FC',
	),
	'locationNameTypography'        => array(
		'type'    => 'object',
		'default' => array(
			'family'     => '',
			'fontWeight' => '600',
			'style'      => 'normal',
			'transform'  => 'none',
			'decoration' => 'none',
		),
	),
	'locationNameFontSize'          => single_value_responsive_attr( 14 ),
	'locationNameLineHeight'        => single_value_responsive_attr( 16 ),
	'dateTimeTypography'            => array(
		'type'    => 'object',
		'default' => array(
			'family'     => '',
			'fontWeight' => '400',
			'style'      => 'normal',
			'transform'  => 'none',
			'decoration' => 'none',
		),
	),
	'dateTimeFontSize'              => single_value_responsive_attr( 13 ),
	'dateTimeLineHeight'            => single_value_responsive_attr( 16 ),
	'temperatureScaleColor'         => array(
		'type'    => 'string',
		'default' => '',
	),
	'temperatureScaleTypography'    => array(
		'type'    => 'object',
		'default' => array(
			'family'     => '',
			'fontWeight' => '600',
			'style'      => 'normal',
			'transform'  => 'none',
			'decoration' => 'none',
		),
	),
	'temperatureScaleFontSize'      => single_value_responsive_attr( 64, '', 50 ),
	'temperatureScaleLineHeight'    => single_value_responsive_attr( 78 ),
	'temperatureUnitTypography'     => array(
		'type'    => 'object',
		'default' => array(
			'family'     => '',
			'fontWeight' => '400',
			'style'      => 'normal',
			'transform'  => 'none',
			'decoration' => 'none',
		),
	),
	'temperatureUnitFontSize'       => single_value_responsive_attr( 22 ),
	'temperatureUnitLineHeight'     => single_value_responsive_attr( 36 ),
	'weatherDescTypography'         => array(
		'type'    => 'object',
		'default' => array(
			'family'     => '',
			'fontWeight' => '500',
			'style'      => 'normal',
			'transform'  => 'capitalize',
			'decoration' => 'none',
		),
	),
	'weatherDescFontSize'           => single_value_responsive_attr( 18 ),
	'weatherDescLineHeight'         => single_value_responsive_attr( 16 ),
	'additionalDataLabelTypography' => array(
		'type'    => 'object',
		'default' => array(
			'family'     => '',
			'fontWeight' => '400',
			'style'      => 'normal',
			'transform'  => 'none',
			'decoration' => 'none',
		),
	),
	'additionalDataLabelFontSize'   => single_value_responsive_attr( 14 ),
	'additionalDataLabelLineHeight' => single_value_responsive_attr( 18 ),
	'additionalDataValueTypography' => array(
		'type'    => 'object',
		'default' => array(
			'family'     => '',
			'fontWeight' => '600',
			'style'      => 'normal',
			'transform'  => 'none',
			'decoration' => 'none',
		),
	),
	'additionalDataValueFontSize'   => single_value_responsive_attr( 14 ),
	'additionalDataValueLineHeight' => single_value_responsive_attr( 24 ),
	'additionalDataMargin'          => spacing_value_responsive_attr( '16', '0', '4', '0' ),
	'additionalNavIconColors'       => array(
		'type'    => 'object',
		'default' => array(
			'color'      => '#2f2f2f',
			'hoverColor' => '',
		),
	),
	'currentWeatherCardWidth'       => single_value_responsive_attr( 51, 100, 100, '%' ),
	'weatherConditionIconSize'      => single_value_responsive_attr( 88 ),
	'additionalDataHorizontalGap'   => single_value_responsive_attr( 40, 30, 15, 'px' ),
	'regionalPreferenceMargin'      => spacing_value_responsive_attr( '0', '0', '30', '0' ),
	'splwPadding'                   => spacing_value_responsive_attr( '24', '24', '24', '24' ),
	'additionalDataPadding'         => spacing_value_responsive_attr( '2', '2', '2', '2' ),
	'splwBorder'                    => array(
		'type'    => 'object',
		'default' => array(
			'style'      => 'solid',
			'color'      => '#e2e2e2',
			'hoverColor' => '',
		),
	),
	'weatherMapBgColorType'         => array(
		'type'    => 'string',
		'default' => 'bgColor',
	),
	'weatherMapBgColor'             => array(
		'type'    => 'string',
		'default' => '#F2F7FC',
	),
	'dateTimeGap'                   => single_value_responsive_attr( 4 ),
	'additionalDataIconSize'        => single_value_responsive_attr( 14 ),
	'active_additional_data_layout' => array(
		'type'    => 'string',
		'default' => 'carousel-simple',
	),
	'layerDisplayType'              => array(
		'type'    => 'string',
		'default' => 'collapsible',
	),
	'weatherMapBorder'              => array(
		'type'    => 'object',
		'default' => array(
			'style'      => 'solid',
			'color'      => '#e2e2e2',
			'hoverColor' => '',
		),
	),
	'splwBoxShadow'                 => array(
		'type'    => 'object',
		'default' => array(
			'value'      => array(
				'top'    => '0',
				'right'  => '4',
				'bottom' => '8',
				'left'   => '0',
			),
			'unit'       => 'Outset',
			'color'      => 'rgba(17,17,17,.06)',
			'hoverColor' => '',
		),
	),
	'additionalCarouselColumns'     => array(
		'type'    => 'object',
		'default' => array(
			'device' => array(
				'Desktop' => 4,
				'Tablet'  => 3,
				'Mobile'  => 2,
			),
		),
	),
	'bgGradient'                    => array(
		'type'    => 'string',
		'default' => 'linear-gradient(135deg, #A1C4FD 0%, #C2E9FB 50%, #E0EAFC 100%)',
	),
	// primary color attr.
	'templatePrimaryColor'          => array(
		'type'    => 'string',
		'default' => '#2F2F2F',
	),
	'templateSecondaryColor'        => array(
		'type'    => 'string',
		'default' => '#757575',
	),
);

$maps_attr = array(
	'showPreloader'             => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'weatherMapType'            => array(
		'type'    => 'string',
		'default' => 'windy-map',
	),
	'layerDisplayType'          => array(
		'type'    => 'string',
		'default' => 'visible',
	),
	'layerOpacity'              => array(
		'type'    => 'object',
		'default' => array(
			'value' => 50,
			'unit'  => '%',
		),
	),
	'mapZoomLevel'              => array(
		'type'    => 'number',
		'default' => 8,
	),
	'activeZoomScrollWheel'     => array(
		'type'    => 'boolean',
		'default' => true,
	),
	'activeLegends'             => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'weatherMapBgGradient'      => array(
		'type'    => 'string',
		'default' => 'linear-gradient(135deg,rgb(6,147,227) 0%,rgb(133,49,213) 100%)',
	),
	'weatherMapBorder'          => array(
		'type'    => 'object',
		'default' => array(
			'color'      => '#f0f0f0',
			'style'      => 'solid',
			'hoverColor' => '',
		),
	),
	'enableWeatherMapBoxShadow' => array(
		'type'    => 'boolean',
		'default' => true,
	),
	'weatherMapBoxShadow'       => array(
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
	// Windy map.
	'defaultElevation'          => array(
		'type'    => 'string',
		'default' => 'surface',
	),
	'showMarker'                => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'spotForecast'              => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'forecastModel'             => array(
		'type'    => 'string',
		'default' => 'ecmwf',
	),
	'forecastFrom'              => array(
		'type'    => 'string',
		'default' => 'now',
	),
	'airflowPressureLines'      => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'weatherAttribution'        => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'weatherMapMaxWidth'        => single_value_responsive_attr( 1200, 950, 400 ),
	'weatherMapMaxHeight'       => single_value_responsive_attr( 700, 500, 400 ),
	'dataLayerBorderWidth'      => spacing_value_attr( '1', '1', '1', '1' ),
	'cityBorderWidth'           => spacing_value_attr( '1', '1', '1', '1' ),
	'weatherPopupBorderWidth'   => spacing_value_attr( '1', '1', '1', '1' ),
	'weatherMapPadding'         => spacing_value_responsive_attr( '10', '10', '10', '10' ),
	'weatherMapBorderRadius'    => spacing_value_attr( '8', '8', '8', '8' ),
	'weatherMapBorderWidth'     => spacing_value_attr( '1', '1', '1', '1' ),
);



$additional_data_carousel_attr = array(
	'additionalCarouselColumns'      => array(
		'type'    => 'object',
		'default' => array(
			'device' => array(
				'Desktop' => 5,
				'Tablet'  => 5,
				'Mobile'  => 3,
			),
		),
	),
	'additionalCarouselAutoPlay'     => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'additionalCarouselDelayTime'    => array(
		'type'    => 'object',
		'default' => array(
			'value' => 3000,
			'unit'  => 'ms',
		),
	),
	'additionalCarouselSpeed'        => array(
		'type'    => 'object',
		'default' => array(
			'value' => 600,
			'unit'  => 'ms',
		),
	),
	'additionalCarouselStopOnHover'  => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'additionalCarouselInfiniteLoop' => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'enableAdditionalNavIcon'        => array(
		'type'    => 'boolean',
		'default' => true,
	),
	'additionalNavigationVisibility' => array(
		'type'    => 'string',
		'default' => 'onHover',
	),
	'additionalNavigationIcon'       => array(
		'type'    => 'string',
		'default' => 'chevron',
	),
	'additionalNavigationIconSize'   => array(
		'type'    => 'object',
		'default' => array(
			'value' => 16,
			'unit'  => 'px',
		),
	),
);

$shared_attr = array(
	'lw_api_type'                         => array(
		'type'    => 'string',
		'default' => 'openweather_api',
	),
	'weatherView'                         => array(
		'type'    => 'object',
		'default' => array(
			'weather-view' => '',
		),
	),
	'blockId'                             => array(
		'type' => 'string',
	),
	'active_additional_data_layout'       => array(
		'type'    => 'string',
		'default' => 'center',
	),
	'active_additional_data_layout_style' => array(
		'type'    => 'string',
		'default' => 'clean',
	),
	'displayAdditionalData'               => array(
		'type'    => 'boolean',
		'default' => true,
	),
	'additionalDataOptions'               => array(
		'type'    => 'array',
		'default' => array(
			array(
				'id'       => 1,
				'value'    => 'humidity',
				'isActive' => true,
			),
			array(
				'id'       => 2,
				'value'    => 'pressure',
				'isActive' => true,
			),
			array(
				'id'       => 3,
				'value'    => 'wind',
				'isActive' => true,
			),
			array(
				'id'       => 4,
				'value'    => 'clouds',
				'isActive' => true,
			),
			array(
				'id'       => 5,
				'value'    => 'gust',
				'isActive' => true,
			),
			array(
				'id'       => 6,
				'value'    => 'visibility',
				'isActive' => true,
			),
			array(
				'id'       => 7,
				'value'    => 'sunriseSunset',
				'isActive' => true,
			),
			array(
				'id'       => 9,
				'value'    => 'uv_index',
				'isActive' => false,
			),
			array(
				'id'       => 8,
				'value'    => 'precipitation',
				'isActive' => false,
			),
			array(
				'id'       => 10,
				'value'    => 'dew_point',
				'isActive' => false,
			),
			array(
				'id'       => 12,
				'value'    => 'rainchance',
				'isActive' => false,
			),
			array(
				'id'       => 13,
				'value'    => 'snow',
				'isActive' => false,
			),
			array(
				'id'       => 11,
				'value'    => 'air_index',
				'isActive' => false,
			),
			array(
				'id'       => 14,
				'value'    => 'moonriseMoonset',
				'isActive' => false,
			),
			array(
				'id'       => 15,
				'value'    => 'moon_phase',
				'isActive' => false,
			),
		),
	),
	'forecastData'                        => array(
		'type'    => 'array',
		'default' => array(
			array(
				'id'    => 1,
				'name'  => 'temperature',
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
				'id'    => 7,
				'name'  => 'snow',
				'value' => false,
			),
		),
	),
	'additionalNavIconColors'             => array(
		'type'    => 'object',
		'default' => array(
			'color'      => '#fff',
			'hoverColor' => '',
		),
	),
	// location name attr.
	'locationNameTypography'              => array(
		'type'    => 'object',
		'default' => array(
			'family'     => '',
			'fontWeight' => '600',
			'style'      => 'normal',
			'transform'  => 'none',
			'decoration' => 'none',
		),
	),
	'locationNameFontSize'                => single_value_responsive_attr( 27 ),
	'locationNameLineHeight'              => single_value_responsive_attr( 38 ),
	'locationNameFontSpacing'             => single_value_responsive_attr( 0 ),
	'displayWeatherMap'                   => array(
		'type'    => 'boolean',
		'default' => true,
	),
	// date time attr.
	'dateTimeTypography'                  => array(
		'type'    => 'object',
		'default' => array(
			'family'     => '',
			'fontWeight' => '500',
			'style'      => 'normal',
			'transform'  => 'none',
			'decoration' => 'none',
		),
	),
	'dateTimeFontSize'                    => single_value_responsive_attr( 14 ),
	'dateTimeFontSpacing'                 => single_value_responsive_attr( 0 ),
	'dateTimeLineHeight'                  => single_value_responsive_attr( 16 ),
	'dateTimeGap'                         => single_value_responsive_attr( 8 ),
	'regionalPreferenceMargin'            => spacing_value_responsive_attr( '0', '0', '8', '0' ),
	// weather condition icon.
	'weatherConditionIcon'                => array(
		'type'    => 'boolean',
		'default' => true,
	),
	'disableWeatherIconAnimation'         => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'weatherConditionIconType'            => array(
		'type'    => 'string',
		'default' => 'forecast_icon_set_one',
	),
	'weatherConditionIconSize'            => single_value_responsive_attr( 60 ),
	// temperature scale.
	'temperatureScaleColor'               => array(
		'type'    => 'string',
		'default' => '',
	),
	'temperatureScaleTypography'          => array(
		'type'    => 'object',
		'default' => array(
			'family'     => '',
			'fontWeight' => '600',
			'style'      => 'normal',
			'transform'  => 'none',
			'decoration' => 'none',
		),
	),
	'temperatureScaleFontSize'            => single_value_responsive_attr( 48 ),
	'temperatureScaleLineHeight'          => single_value_responsive_attr( 56 ),
	'temperatureScaleFontSpacing'         => single_value_responsive_attr( 0 ),
	'temperatureScaleMargin'              => spacing_value_responsive_attr( '0', '0', '8', '0' ),
	'temperatureUnitTypography'           => array(
		'type'    => 'object',
		'default' => array(
			'family'     => '',
			'fontWeight' => '500',
			'style'      => 'normal',
			'transform'  => 'none',
			'decoration' => 'none',
		),
	),
	'temperatureUnitFontSize'             => single_value_responsive_attr( 16 ),
	'temperatureUnitLineHeight'           => single_value_responsive_attr( 21 ),
	'temperatureUnitFontSpacing'          => single_value_responsive_attr( 0 ),
	// weather desc.
	'weatherDescTypography'               => array(
		'type'    => 'object',
		'default' => array(
			'family'     => '',
			'fontWeight' => '600',
			'style'      => 'normal',
			'transform'  => 'capitalize',
			'decoration' => 'none',
		),
	),
	'weatherDescFontSize'                 => single_value_responsive_attr( 16 ),
	'weatherDescFontSpacing'              => single_value_responsive_attr( 0 ),
	'weatherDescLineHeight'               => single_value_responsive_attr( 20 ),
	// additional data options.
	'additionalDataIcon'                  => array(
		'type'    => 'boolean',
		'default' => true,
	),
	'additionalDataIconType'              => array(
		'type'    => 'string',
		'default' => 'icon_set_one',
	),
	'additionalDataIconSize'              => single_value_responsive_attr( 16 ),
	'additionalDataLabelTypography'       => array(
		'type'    => 'object',
		'default' => array(
			'family'     => '',
			'fontWeight' => '400',
			'style'      => 'normal',
			'transform'  => 'none',
			'decoration' => 'none',
		),
	),
	'additionalDataLabelFontSize'         => single_value_responsive_attr( 14 ),
	'additionalDataLabelLineHeight'       => single_value_responsive_attr( 20 ),
	'additionalDataLabelFontSpacing'      => single_value_responsive_attr( 0 ),
	'additionalDataVerticalGap'           => single_value_responsive_attr( 2 ),
	'additionalDataValueTypography'       => array(
		'type'    => 'object',
		'default' => array(
			'family'     => '',
			'fontWeight' => '600',
			'style'      => 'normal',
			'transform'  => 'none',
			'decoration' => 'none',
		),
	),
	'additionalDataValueFontSize'         => single_value_responsive_attr( 14 ),
	'additionalDataValueLineHeight'       => single_value_responsive_attr( 20 ),
	'additionalDataValueFontSpacing'      => single_value_responsive_attr( 0 ),
	'weatherComportDataTypography'        => array(
		'type'    => 'object',
		'default' => array(
			'family'     => '',
			'fontWeight' => '600',
			'style'      => 'normal',
			'transform'  => 'none',
			'decoration' => 'none',
		),
	),
	'weatherComportDataFontSize'          => single_value_responsive_attr( 14 ),
	'weatherComportDataFontSpacing'       => single_value_responsive_attr( 0 ),
	'weatherComportDataLineHeight'        => single_value_responsive_attr( 20 ),
	'weatherComportDataColors'            => array(
		'type'    => 'object',
		'default' => array(
			'Color'      => '',
			'HoverColor' => '',
		),
	),
	'weatherComportDataVerticalGap'       => single_value_responsive_attr( 8 ),
	'weatherComportDataMargin'            => spacing_value_responsive_attr( '10', '10', '10', '10' ),
	'additionalDataHorizontalGap'         => single_value_responsive_attr( 10, 8, 5, 'px' ),
	'additionalDataPadding'               => spacing_value_responsive_attr( '2', '2', '2', '2' ),
	'additionalDataMargin'                => spacing_value_responsive_attr( '14', '0', '0', '0' ),
	'sunOrbitIconColor'                   => array(
		'type'    => 'string',
		'default' => '#FFDF00',
	),
	'sunOrbitColor'                       => array(
		'type'    => 'string',
		'default' => '#FF7D7D',
	),
	// get weather data.
	'displayDateUpdateTime'               => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'displayWeatherAttribution'           => array(
		'type'    => 'boolean',
		'default' => true,
	),
	'displayLinkToOpenWeatherMap'         => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'weatherData'                         => array(
		'type'    => 'object',
		'default' => array(),
	),
	// forecast data attr.
	'displayWeatherForecastData'          => array(
		'type'    => 'boolean',
		'default' => true,
	),
	'swapForecastDisplay'                 => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'weatherForecastType'                 => array(
		'type'    => 'string',
		'default' => 'hourly',
	),
	'hourlyTitle'                         => array(
		'type'    => 'string',
		'default' => 'Hourly Forecast',
	),
	'forecastDaysNameLength'              => array(
		'type'    => 'string',
		'default' => 'normal',
	),
	'numberOfForecastDays'                => array(
		'type'    => 'string',
		'default' => '5',
	),
	'hourlyForecastType'                  => array(
		'type'    => 'string',
		'default' => '3',
	),
	'numberOfForecastHours'               => array(
		'type'    => 'string',
		'default' => '8',
	),
	'numOfForecastThreeHours'             => array(
		'type'    => 'string',
		'default' => '8',
	),
	'bothTempUnit'                        => array(
		'type'    => 'string',
		'default' => 'metric',
	),
	'forecastDataIcon'                    => array(
		'type'    => 'boolean',
		'default' => true,
	),
	'forecastIconAnimationDisable'        => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'forecastDataIconType'                => array(
		'type'    => 'string',
		'default' => 'forecast_icon_set_one',
	),
	'forecastDataIconSize'                => single_value_responsive_attr( 48 ),
	'forecastLabelTypography'             => array(
		'type'    => 'object',
		'default' => array(
			'family'     => '',
			'fontWeight' => '400',
			'style'      => 'normal',
			'transform'  => 'none',
			'decoration' => 'none',
		),
	),
	'forecastLabelFontSize'               => single_value_responsive_attr( 14 ),
	'forecastLabelFontSpacing'            => single_value_responsive_attr( 0 ),
	'forecastLabelLineHeight'             => single_value_responsive_attr( 24 ),
	'forecastLabelVerticalGap'            => single_value_responsive_attr( 10 ),
	'forecastDataTypography'              => array(
		'type'    => 'object',
		'default' => array(
			'family'     => '',
			'fontWeight' => '400',
			'style'      => 'normal',
			'transform'  => 'none',
			'decoration' => 'none',
		),
	),
	'forecastDataFontSize'                => single_value_responsive_attr( 14 ),
	'forecastDataLineHeight'              => single_value_responsive_attr( 24 ),
	'forecastDataFontSpacing'             => single_value_responsive_attr( 0 ),
	'forecastDataBgColor'                 => array(
		'type'    => 'string',
		'default' => '',
	),
	'bgOverlay'                           => array(
		'type'    => 'string',
		'default' => '#00000075',
	),
	// forecast live filter.
	'forecastLiveFilterColors'            => array(
		'type'    => 'object',
		'default' => array(
			'color' => '',
			'hover' => '',
		),
	),
	'forecastLiveFilterBgColor'           => array(
		'type'    => 'object',
		'default' => array(
			'color' => '',
			'hover' => '',
		),
	),
	'forecastDropdownTextColor'           => array(
		'type'    => 'object',
		'default' => array(
			'color'  => '#2F2F2F',
			'hover'  => '#2F2F2F',
			'active' => '#2F2F2F',
		),
	),
	'forecastDropdownBgColor'             => array(
		'type'    => 'object',
		'default' => array(
			'color'  => '#fff',
			'hover'  => '#CCCCCC',
			'active' => '#CCCCCC',
		),
	),
	'weatherSvgImageColor'                => array(
		'type'    => 'string',
		'default' => '',
	),
	'forecastSvgImageColor'               => array(
		'type'    => 'string',
		'default' => '',
	),
	'forecastToggleBtnColors'             => array(
		'type'    => 'object',
		'default' => array(
			'color'  => '',
			'active' => $brand_color,
		),
	),
	'forecastToggleBtnBgColors'           => array(
		'type'    => 'object',
		'default' => array(
			'color'  => '',
			'active' => '#fff',
		),
	),
	'forecastToggleBorder'                => array(
		'type'    => 'object',
		'default' => array(
			'color' => '#fff',
			'style' => 'solid',
		),
	),
	'forecastToggleBorderWidth'           => spacing_value_attr( '2', '2', '2', '2' ),
	'forecastToggleRadius'                => spacing_value_attr( '2', '2', '2', '2' ),
	'forecastToggleVerticalGap'           => single_value_responsive_attr( 14, 14, 10 ),
	'forecastLiveFilterBorder'            => array(
		'type'    => 'object',
		'default' => array(
			'style' => 'solid',
			'color' => '#fff',
		),
	),
	'forecastLiveFilterBorderWidth'       => spacing_value_attr( '0', '0', '2', '0', 'px', false ),
	'forecastContainerPadding'            => spacing_value_responsive_attr( '14', '0', '20', '0' ),
	'forecastContainerMargin'             => spacing_value_responsive_attr( '14', '0', '0', '0' ),
	'forecastCarouselAutoPlay'            => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'showForecastNavIcon'                 => array(
		'type'    => 'boolean',
		'default' => true,
	),
	'forecastCarouselNavIcon'             => array(
		'type'    => 'string',
		'default' => 'chevron',
	),
	'forecastCarouselAutoplayDelay'       => array(
		'type'    => 'object',
		'default' => array(
			'value' => 3000,
			'unit'  => 'ms',
		),
	),
	'forecastCarouselSpeed'               => array(
		'type'    => 'object',
		'default' => array(
			'value' => 600,
			'unit'  => 'ms',
		),
	),
	'forecastNavigationVisibility'        => array(
		'type'    => 'string',
		'default' => 'onHover',
	),
	'carouselStopOnHover'                 => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'forecastCarouselInfiniteLoop'        => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'forecastCarouselColumns'             => array(
		'type'    => 'object',
		'default' => array(
			'device' => array(
				'Desktop' => 3,
				'Tablet'  => 3,
				'Mobile'  => 3,
			),
		),
	),
	'forecastCarouselHorizontalGap'       => single_value_responsive_attr( 5, 4, 3, 'px' ),
	'forecastNavigationIconColors'        => array(
		'type'    => 'object',
		'default' => array(
			'color'      => '#fff',
			'hoverColor' => '',
		),
	),
	'forecastNavigationIconSize'          => array(
		'type'    => 'object',
		'default' => array(
			'value' => 16,
			'unit'  => 'px',
		),
	),
	// Weather Attribution attr.
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
	'weatherAttributionFontSize'          => single_value_responsive_attr( 12 ),
	'weatherAttributionFontSpacing'       => single_value_responsive_attr( 0 ),
	'weatherAttributionLineHeight'        => single_value_responsive_attr( 26 ),
	'weatherAttributionBgColor'           => array(
		'type'    => 'string',
		'default' => '#00000036',
	),
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
	'detailedWeatherAndUpdateLineHeight'  => single_value_responsive_attr( 26 ),
	'detailedWeatherAndUpdateMargin'      => spacing_value_responsive_attr( '10', '0', '10', '0' ),
	// main container attr.
	'bgColorType'                         => array(
		'type'    => 'string',
		'default' => 'bgColor',
	),
	'bgColor'                             => array(
		'type'    => 'string',
		'default' => '#FFFFFF',
	),
	'bgGradient'                          => array(
		'type'    => 'string',
		'default' => 'linear-gradient(135deg, #8E2DE2 0%, #4A00E0 100%)',
	),
	'bgImage'                             => array(
		'type'    => 'object',
		'default' => array(),
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
	'imageType'                           => array(
		'type'    => 'string',
		'default' => 'weather-based',
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
	'splwBorder'                          => array(
		'type'    => 'object',
		'default' => array(
			'style'      => 'none',
			'color'      => '#ddd',
			'hoverColor' => '',
		),
	),
	'splwBorderWidth'                     => spacing_value_attr( '1', '1', '1', '1' ),
	'splwBorderRadius'                    => spacing_value_attr( '8', '8', '8', '8' ),
	'additionalStylesBorder'              => array(
		'type'    => 'object',
		'default' => array(
			'style' => 'solid',
			'color' => '',
		),
	),
	'additionalStylesBorderWidth'         => spacing_value_attr( '2', '2', '2', '2' ),
	'enableSplwBoxShadow'                 => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'splwBoxShadow'                       => array(
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
	'splwPadding'                         => spacing_value_responsive_attr( '20', '20', '20', '20' ),
	'splwMaxWidth'                        => single_value_responsive_attr( 1200 ),
	'splwMapHeight'                       => single_value_responsive_attr( 700 ),
	'mapWidthHeight'                      => array(
		'type'    => 'object',
		'default' => array(
			'width'  => array(
				'device' => array(
					'Desktop' => 700,
					'Tablet'  => '',
					'Mobile'  => '',
				),
			),
			'height' => array(
				'device' => array(
					'Desktop' => 700,
					'Tablet'  => '',
					'Mobile'  => '',
				),
			),
			'unit'   => array(
				'Desktop' => 'px',
				'Tablet'  => 'px',
				'Mobile'  => 'px',
			),
		),
	),
	'showLocationName'                    => array(
		'type'    => 'boolean',
		'default' => true,
	),
	'showCurrentDate'                     => array(
		'type'    => 'boolean',
		'default' => true,
	),
	'displayComportDataPosition'          => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'showCurrentTime'                     => array(
		'type'    => 'boolean',
		'default' => true,
	),
	'showNationalAlerts'                  => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'showPreloader'                       => array(
		'type'    => 'boolean',
		'default' => true,
	),
	'splwDateFormat'                      => array(
		'type'    => 'string',
		'default' => 'M j, Y',
	),
	'splwCustomDateFormat'                => array(
		'type'    => 'string',
		'default' => 'F j, Y',
	),
	'splwTimeFormat'                      => array(
		'type'    => 'string',
		'default' => 'g:i A',
	),
	'splwTimeZone'                        => array(
		'type'    => 'string',
		'default' => 'auto',
	),
	'splwLanguage'                        => array(
		'type'    => 'string',
		'default' => 'en',
	),
	'displayTemperature'                  => array(
		'type'    => 'boolean',
		'default' => true,
	),
	'currentUnit'                         => array(
		'type'    => 'string',
		'default' => 'metric',
	),
	'displayWeatherConditions'            => array(
		'type'    => 'boolean',
		'default' => true,
	),
);

$shared_attributes = array_merge(
	get_color_default_attr( '' ),
	$block_required_attributes,
	$shared_attr,
);
