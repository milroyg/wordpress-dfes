import { __ } from '@wordpress/i18n';
import {
	WeatherAccordionIcon,
	MapBlockIcon,
	AqiBlockIcon,
	CombinedBlockIcon,
	HistoricalWeatherBlockIcon,
	HistoricalAQIBlockIcon,
	SunMoonTimesBlockIcon,
} from '../icons';
import {
	VerticalBlockIcon,
	VerticalCardPresetOne,
	VerticalCardPresetTwo,
	VerticalCardPresetThree,
	VerticalCardPresetFour,
	VerticalCardPresetFive,
	VerticalCardPresetSix,
} from '../blocks/vertical/icons';
import {
	HorizontalBlockIcon,
	HorizontalPresetOne,
	HorizontalPresetTwo,
	HorizontalPresetThree,
	HorizontalPresetFour,
} from '../blocks/horizontal/icons';
import {
	TabsBlockIcon,
	TabsPresetOne,
	TabsPresetTwo,
} from '../blocks/tabs/icons';
import {
	TableBlockIcon,
	TablePresetOne,
	TablePresetTwo,
} from '../blocks/table/icons';
import {
	WeatherGridIcon,
	GridPresetOne,
	GridPresetTwo,
	GridPresetThree,
} from '../blocks/grid/icons';
import { ShortcodeBlockIcon } from '../blocks/shortcode/icons';
import {
	AqiMinimalBlockIcon,
	AqiMinimalFiveIcon,
	AqiMinimalFourIcon,
	AqiMinimalOneIcon,
	AqiMinimalSixIcon,
	AqiMinimalThreeIcon,
	AqiMinimalTwoIcon,
} from '../blocks/aqi-minimal/icons';
import { SectionHeadingBlockIcon } from '../blocks/section-heading/inspector/icons';
import { WindyMapBlockIcon } from '../blocks/windy-map/icons';

export const timeZonesListArray = [
	{ label: 'Default (auto-detected)', value: 'auto' },
	{ label: 'UTC-12', value: 'UTC-12' },
	{ label: 'UTC-11:30', value: 'UTC-11.5' },
	{ label: 'UTC-11', value: 'UTC-11' },
	{ label: 'UTC-10:30', value: 'UTC-10.5' },
	{ label: 'UTC-10', value: 'UTC-10' },
	{ label: 'UTC-9:30', value: 'UTC-9.5' },
	{ label: 'UTC-9', value: 'UTC-9' },
	{ label: 'UTC-8:30', value: 'UTC-8.5' },
	{ label: 'UTC-8', value: 'UTC-8' },
	{ label: 'UTC-7:30', value: 'UTC-7.5' },
	{ label: 'UTC-7', value: 'UTC-7' },
	{ label: 'UTC-6:30', value: 'UTC-6.5' },
	{ label: 'UTC-6', value: 'UTC-6' },
	{ label: 'UTC-5:30', value: 'UTC-5.5' },
	{ label: 'UTC-5', value: 'UTC-5' },
	{ label: 'UTC-4:30', value: 'UTC-4.5' },
	{ label: 'UTC-4', value: 'UTC-4' },
	{ label: 'UTC-3:30', value: 'UTC-3.5' },
	{ label: 'UTC-3', value: 'UTC-3' },
	{ label: 'UTC-2:30', value: 'UTC-2.5' },
	{ label: 'UTC-2', value: 'UTC-2' },
	{ label: 'UTC-1:30', value: 'UTC-1.5' },
	{ label: 'UTC-1', value: 'UTC-1' },
	{ label: 'UTC-0:30', value: 'UTC-0.5' },
	{ label: 'UTC+0', value: 'UTC+0' },
	{ label: 'UTC+0:30', value: 'UTC+0.5' },
	{ label: 'UTC+1', value: 'UTC+1' },
	{ label: 'UTC+1:30', value: 'UTC+1.5' },
	{ label: 'UTC+2', value: 'UTC+2' },
	{ label: 'UTC+2:30', value: 'UTC+2.5' },
	{ label: 'UTC+3', value: 'UTC+3' },
	{ label: 'UTC+3:30', value: 'UTC+3.5' },
	{ label: 'UTC+4', value: 'UTC+4' },
	{ label: 'UTC+4:30', value: 'UTC+4.5' },
	{ label: 'UTC+5', value: 'UTC+5' },
	{ label: 'UTC+5:30', value: 'UTC+5.5' },
	{ label: 'UTC+5:45', value: 'UTC+5.75' },
	{ label: 'UTC+6', value: 'UTC+6' },
	{ label: 'UTC+6:30', value: 'UTC+6.5' },
	{ label: 'UTC+7', value: 'UTC+7' },
	{ label: 'UTC+7:30', value: 'UTC+7.5' },
	{ label: 'UTC+8', value: 'UTC+8' },
	{ label: 'UTC+8:30', value: 'UTC+8.5' },
	{ label: 'UTC+8:45', value: 'UTC+8.75' },
	{ label: 'UTC+9', value: 'UTC+9' },
	{ label: 'UTC+9:30', value: 'UTC+9.5' },
	{ label: 'UTC+10', value: 'UTC+10' },
	{ label: 'UTC+10:30', value: 'UTC+10.5' },
	{ label: 'UTC+11', value: 'UTC+11' },
	{ label: 'UTC+11:30', value: 'UTC+11.5' },
	{ label: 'UTC+12', value: 'UTC+12' },
	{ label: 'UTC+12:30', value: 'UTC+12.5' },
	{ label: 'UTC+12:45', value: 'UTC+12.75' },
	{ label: 'UTC+13', value: 'UTC+13' },
	{ label: 'UTC+13:30', value: 'UTC+13.5' },
	{ label: 'UTC+13:45', value: 'UTC+13.75' },
	{ label: 'UTC+14', value: 'UTC+14' },
];

export const languagesListArray = [
	{ label: 'English', value: 'en' },
	{ label: 'Afrikaans', value: 'af' },
	{ label: 'Albanian', value: 'sq' },
	{ label: 'Arabic', value: 'ar' },
	{ label: 'Azerbaijani', value: 'az' },
	{ label: 'Bulgarian', value: 'bg' },
	{ label: 'Catalan', value: 'ca' },
	{ label: 'Czech', value: 'cz' },
	{ label: 'Danish', value: 'da' },
	{ label: 'German', value: 'de' },
	{ label: 'Greek', value: 'el' },
	{ label: 'Basque', value: 'eu' },
	{ label: 'Persian (Farsi)', value: 'fa' },
	{ label: 'Finnish', value: 'fi' },
	{ label: 'French', value: 'fr' },
	{ label: 'Galician', value: 'gl' },
	{ label: 'Hebrew', value: 'he' },
	{ label: 'Hindi', value: 'hi' },
	{ label: 'Croatian', value: 'hr' },
	{ label: 'Hungarian', value: 'hu' },
	{ label: 'Indonesian', value: 'id' },
	{ label: 'Italian', value: 'it' },
	{ label: 'Japanese', value: 'ja' },
	{ label: 'Korean', value: 'kr' },
	{ label: 'Latvian', value: 'la' },
	{ label: 'Lithuanian', value: 'lt' },
	{ label: 'Macedonian', value: 'mk' },
	{ label: 'Norwegian', value: 'no' },
	{ label: 'Dutch', value: 'nl' },
	{ label: 'Polish', value: 'pl' },
	{ label: 'Portuguese', value: 'pt' },
	{ label: 'Português Brasil', value: 'pt_br' },
	{ label: 'Romanian', value: 'ro' },
	{ label: 'Russian', value: 'ru' },
	{ label: 'Swedish', value: 'sv' },
	{ label: 'Slovak', value: 'sk' },
	{ label: 'Slovenian', value: 'sl' },
	{ label: 'Spanish', value: 'es' },
	{ label: 'Serbian', value: 'sr' },
	{ label: 'Thai', value: 'th' },
	{ label: 'Turkish', value: 'tr' },
	{ label: 'Ukrainian', value: 'uk' },
	{ label: 'Vietnamese', value: 'vi' },
	{ label: 'Chinese Simplified', value: 'zh_cn' },
	{ label: 'Chinese Traditional', value: 'zh_tw' },
	{ label: 'Zulu', value: 'zu' },
];

export const layoutIconItems = {
	vertical: [
		{
			label: 'Template One',
			value: 'vertical-one',
			additional_layout: 'center',
			icon: VerticalCardPresetOne,
		},
		{
			label: 'Template Two',
			value: 'vertical-three',
			additional_layout: 'column-two-justified',
			icon: VerticalCardPresetThree,
		},
		{
			label: 'Template Three',
			value: 'vertical-two',
			additional_layout: 'carousel-simple',
			icon: VerticalCardPresetTwo,
			onlyPro: true,
		},
		{
			label: 'Template Four',
			value: 'vertical-four',
			additional_layout: 'column-two-justified',
			icon: VerticalCardPresetFour,
			onlyPro: true,
		},
		{
			label: 'Template Five',
			value: 'vertical-five',
			additional_layout: 'grid-three',
			icon: VerticalCardPresetFive,
			onlyPro: true,
		},
		{
			label: 'Template Six',
			value: 'vertical-six',
			additional_layout: 'carousel-flat',
			icon: VerticalCardPresetSix,
			onlyPro: true,
		},
	],
	horizontal: [
		{
			label: 'Template One',
			value: 'horizontal-one',
			additional_layout: 'column-two',
			icon: HorizontalPresetOne,
		},
		{
			label: 'Template Two',
			value: 'horizontal-two',
			additional_layout: 'grid-two',
			icon: HorizontalPresetTwo,
			onlyPro: true,
		},
		{
			label: 'Template Three',
			value: 'horizontal-three',
			additional_layout: 'carousel-simple',
			icon: HorizontalPresetThree,
			onlyPro: true,
		},
		{
			label: 'Template Four',
			value: 'horizontal-four',
			additional_layout: 'grid-one',
			icon: HorizontalPresetFour,
			onlyPro: true,
		},
	],
	tabs: [
		{
			label: 'Template One',
			value: 'tabs-one',
			icon: TabsPresetOne,
		},
		{
			label: 'Template Two',
			value: 'tabs-two',
			icon: TabsPresetTwo,
			onlyPro: true,
		},
	],
	table: [
		{
			label: 'Template One',
			value: 'table-one',
			icon: TablePresetOne,
		},
		{
			label: 'Template Two',
			value: 'table-two',
			icon: TablePresetTwo,
			onlyPro: true,
		},
	],
	grid: [
		{
			label: 'Template One',
			value: 'grid-one',
			additional_layout: 'carousel-simple',
			icon: GridPresetOne,
		},
		{
			label: 'Template Two',
			value: 'grid-two',
			additional_layout: 'center',
			icon: GridPresetTwo,
			onlyPro: true,
		},
		{
			label: 'Template Three',
			value: 'grid-three',
			additional_layout: 'center',
			icon: GridPresetThree,
			onlyPro: true,
		},
	],
	'aqi-minimal': [
		{
			label: 'Template One',
			value: 'aqi-minimal-one',
			icon: AqiMinimalOneIcon,
		},
		{
			label: 'Template Two',
			value: 'aqi-minimal-two',
			icon: AqiMinimalTwoIcon,
			onlyPro: true,
		},
		{
			label: 'Template Three',
			value: 'aqi-minimal-three',
			icon: AqiMinimalThreeIcon,
			onlyPro: true,
		},
		{
			label: 'Template Four',
			value: 'aqi-minimal-four',
			icon: AqiMinimalFourIcon,
			onlyPro: true,
		},
		{
			label: 'Template Five',
			value: 'aqi-minimal-five',
			icon: AqiMinimalFiveIcon,
			onlyPro: true,
		},
		{
			label: 'Template Six',
			value: 'aqi-minimal-six',
			icon: AqiMinimalSixIcon,
			onlyPro: true,
		},
	],
	map: [],
};
export const weatherItemLabels = {
	temperature: __( 'Temperature', 'location-weather' ),
	pressure: __( 'Pressure', 'location-weather' ),
	humidity: __( 'Humidity', 'location-weather' ),
	wind: __( 'Wind', 'location-weather' ),
	windSpeed: __( 'Wind Speed', 'location-weather' ),
	windDirection: __( 'Wind Direction', 'location-weather' ),
	precipitation: __( 'Precipitation', 'location-weather' ),
	clouds: __( 'Cloud Cover', 'location-weather' ),
	rainchance: __( 'Rain Chances', 'location-weather' ),
	snow: __( 'Snow', 'location-weather' ),
	gust: __( 'Wind Gust', 'location-weather' ),
	uv_index: __( 'UV Index', 'location-weather' ),
	dew_point: __( 'Dew Point', 'location-weather' ),
	air_index: __( 'Air Quality', 'location-weather' ),
	visibility: __( 'Visibility', 'location-weather' ),
	sunriseSunset: __( 'Sunrise & Sunset', 'location-weather' ),
	moonriseMoonset: __( 'Moonrise & Moonset', 'location-weather' ),
	moon_phase: __( 'Moon Phase', 'location-weather' ),
	national_weather_alerts: __( 'National Alerts', 'location-weather' ),
	sunrise: __( 'Sunrise', 'location-weather' ),
	sunset: __( 'Sunset', 'location-weather' ),
	moonrise: __( 'Moonrise', 'location-weather' ),
	moonset: __( 'Moonset', 'location-weather' ),
};

export const windyMapLayersOptions = [
	{
		id: 1,
		label: __( 'Wind', 'location-weather' ),
		value: 'wind',
	},
	{
		id: 2,
		label: __( 'Rain & Thunder', 'location-weather' ),
		value: 'rain',
	},
	{
		id: 3,
		label: __( 'Cloud Cover', 'location-weather' ),
		value: 'clouds',
	},
	{
		id: 4,
		label: __( 'Humidity', 'location-weather' ),
		value: 'rh',
	},
	{
		id: 5,
		label: __( 'Waves', 'location-weather' ),
		value: 'waves',
	},
	{
		id: 6,
		label: __( 'Temperature', 'location-weather' ),
		value: 'temp',
	},
	{
		id: 7,
		label: __( 'Pressure', 'location-weather' ),
		value: 'pressure',
	},
	{
		id: 8,
		label: __( 'Snow Cover', 'location-weather' ),
		value: 'snowcover',
	},
	{
		id: 9,
		label: __( 'Weather Radar', 'location-weather' ),
		value: 'radar',
	},
	{
		id: 10,
		label: __( 'CO₂ Concentration', 'location-weather' ),
		value: 'cosc',
	},
	{
		id: 11,
		label: __( 'Ocean Currents', 'location-weather' ),
		value: 'currents',
	},
	{
		id: 12,
		label: __( 'Wind Gusts', 'location-weather' ),
		value: 'gust',
	},
	{
		id: 13,
		label: __( 'Lightning', 'location-weather' ),
		value: 'thunder',
	},
	{
		id: 14,
		label: __( 'Sea Surface Temperature', 'location-weather' ),
		value: 'sst',
	},
];

export const windyMapElevationsOptions = [
	{
		id: 1,
		label: 'Surface',
		value: 'surface',
	},
	{
		id: 2,
		label: '100m (330ft)',
		value: '100m',
	},
	{
		id: 3,
		label: '600m (2000ft) 950hPa',
		value: '950h',
	},
	{
		id: 4,
		label: '750m (2500ft) 925hPa',
		value: '925h',
	},
	{
		id: 5,
		label: '900m (3000ft) 900hPa',
		value: '900h',
	},
	{
		id: 6,
		label: '1500m (5000ft) 850hPa',
		value: '850h',
	},
	{
		id: 7,
		label: '2000m (6400ft) 800hPa',
		value: '800h',
	},
	{
		id: 8,
		label: '3000m (FL100) 700hPa',
		value: '700h',
	},
	{
		id: 9,
		label: '4200m (FL140) 600hPa',
		value: '600h',
	},
	{
		id: 10,
		label: '5500m (FL180) 500hPa',
		value: '500h',
	},
	{
		id: 11,
		label: '7000m (FL240) 400hPa',
		value: '400h',
	},
	{
		id: 12,
		label: '9000m (FL300) 300hPa',
		value: '300h',
	},
	{
		id: 13,
		label: '10000m (FL340) 250hPa',
		value: '250h',
	},
	{
		id: 14,
		label: '11700m (FL390) 200hPa',
		value: '200h',
	},
	{
		id: 15,
		label: '13500m (FL450) 150hPa',
		value: '150h',
	},
];
export const windyForecastModelsOptions = [
	{
		id: 1,
		label: 'ECMWF',
		value: 'ecmwf',
	},
	{
		id: 2,
		label: 'GFS',
		value: 'gfs',
	},
	{
		id: 3,
		label: 'ICON',
		value: 'icon',
	},
	{
		id: 4,
		label: 'NAM',
		value: 'nam',
	},
	{
		id: 5,
		label: 'AROME',
		value: 'arome',
	},
	{
		id: 6,
		label: 'HRRR',
		value: 'hrrr',
	},
	{
		id: 7,
		label: 'NEMS',
		value: 'nems',
	},
	{
		id: 8,
		label: 'IFS',
		value: 'ifs',
	},
	{
		id: 9,
		label: 'ICON-EU',
		value: 'iconEu',
	},
	{
		id: 10,
		label: 'ARPEGE',
		value: 'arpege',
	},
];

export const weatherColors = {
	// Thunderstorm
	'thunderstorm with light rain': '#B3E5FC',
	'thunderstorm with rain': '#81D4FA',
	'thunderstorm with heavy rain': '#4FC3F7',
	'light thunderstorm': '#BBDEFB',
	thunderstorm: '#64B5F6',
	'heavy thunderstorm': '#42A5F5',
	'ragged thunderstorm': '#2196F3',
	'thunderstorm with light drizzle': '#B3E5FC',
	'thunderstorm with drizzle': '#81D4FA',
	'thunderstorm with heavy drizzle': '#4FC3F7',

	// Drizzle
	'light intensity drizzle': '#D1EEFC',
	drizzle: '#A3DFF5',
	'heavy intensity drizzle': '#72D0EE',
	'light intensity drizzle rain': '#C5E9FB',
	'drizzle rain': '#91D9F4',
	'heavy intensity drizzle rain': '#5CC8E7',
	'shower rain and drizzle': '#85D5F6',
	'heavy shower rain and drizzle': '#4BB7E0',
	'shower drizzle': '#A3DFF5',

	// Rain
	'light rain': '#C5E9FB',
	'moderate rain': '#91D9F4',
	'heavy intensity rain': '#5CC8E7',
	'very heavy rain': '#4BB7E0',
	'extreme rain': '#0288D1',
	'freezing rain': '#A3DFF5',
	'light intensity shower rain': '#D1EEFC',
	'shower rain': '#85D5F6',
	'heavy intensity shower rain': '#5CC8E7',
	'ragged shower rain': '#4BA6D8',

	// Snow
	'light snow': '#E3F2FD',
	snow: '#B3E5FC',
	'heavy snow': '#81D4FA',
	sleet: '#C5E9FB',
	'light shower sleet': '#D1EEFC',
	'shower sleet': '#91D9F4',
	'light rain and snow': '#BBDEFB',
	'rain and snow': '#72C3E7',
	'light shower snow': '#E0F7FA',
	'shower snow': '#B3E5FC',
	'heavy shower snow': '#81D4FA',

	mist: '#E3F2FD',
	smoke: '#FFCCBC',
	haze: '#FFE0B2',
	dust: '#FFF8E1',
	fog: '#E0F7FA',
	sand: '#FFF3E0',
	ash: '#F5F5F5',
	squalls: '#B3E5FC',
	tornado: '#FFCDD2',
	'clear sky': '#81D4FA',
	'few clouds': '#B3E5FC',
	'scattered clouds': '#CFD8DC',
	'broken clouds': '#ECEFF1',
	'overcast clouds': '#CFD8DC',
};

export const borderStyles = [
	{
		label: <span className="spl-weather-border-none">None</span>,
		value: 'none',
	},
	{
		label: <span className="spl-weather-border-solid">Solid</span>,
		value: 'solid',
	},
	{
		label: <span className="spl-weather-border-dashed">Dashed</span>,
		value: 'dashed',
	},
	{
		label: <span className="spl-weather-border-dotted">Dotted</span>,
		value: 'dotted',
	},
	{
		label: <span className="spl-weather-border-double">Double</span>,
		value: 'double',
	},
];

export const blockRegisterInfo = {
	'sp-location-weather-pro/vertical-card': {
		icon: <VerticalBlockIcon />,
		title: __( 'Weather Card', 'location-weather' ),
		description: __(
			'A clean vertical card showing current weather and upcoming forecasts.',
			'location-weather'
		),
		docLink: 'https://locationweather.io/docs/weather-card/',
		demoLink: 'https://locationweather.io/blocks/#demoId16',
	},
	'sp-location-weather-pro/horizontal': {
		icon: <HorizontalBlockIcon />,
		title: __( 'Weather Horizontal', 'location-weather' ),
		description: __(
			'Display current weather and forecast in a horizontal card layout.',
			'location-weather'
		),
		docLink: 'https://locationweather.io/docs/weather-horizontal/',
		demoLink: 'https://locationweather.io/blocks/#demoId37',
	},
	'sp-location-weather-pro/aqi-minimal': {
		icon: <AqiMinimalBlockIcon />,
		title: __( 'AQI - Minimal Card', 'location-weather' ),
		description: __(
			'Display real-time air quality with AQI, main pollutants, and health advisory.',
			'location-weather'
		),
		docLink: 'https://locationweather.io/docs/aqi-minimal-card/',
		demoLink: 'https://locationweather.io/blocks/#demoId47',
	},
	'sp-location-weather-pro/tabs': {
		icon: <TabsBlockIcon />,
		title: __( 'Weather Tabs', 'location-weather' ),
		description: __(
			'Display Daily, Hourly, 7-Day, Weekend, 16-Day, AQI, and Maps in tabbed navigation.',
			'location-weather'
		),
		docLink: 'https://locationweather.io/docs/weather-tabs/',
		demoLink: 'https://locationweather.io/blocks/#demoId38',
	},
	'sp-location-weather-pro/table': {
		icon: <TableBlockIcon />,
		title: __( 'Weather Table', 'location-weather' ),
		description: __(
			'Display current weather, forecasts, and maps in a structured table format.',
			'location-weather'
		),
		docLink: 'https://locationweather.io/docs/weather-table/',
		demoLink: 'https://locationweather.io/blocks/#demoId39',
	},
	'sp-location-weather-pro/accordion': {
		icon: <WeatherAccordionIcon />,
		title: __( 'Weather Accordion', 'location-weather' ),
		description: __(
			'Display current weather, forecasts, and maps in a collapsible accordion layout.',
			'location-weather'
		),
		docLink: 'https://locationweather.io/docs/weather-accordion/',
		demoLink: 'https://locationweather.io/blocks/#demoId40',
	},
	'sp-location-weather-pro/grid': {
		icon: <WeatherGridIcon />,
		title: __( 'Weather Grid', 'location-weather' ),
		description: __(
			'Display current weather, forecasts, and maps in a responsive grid layout.',
			'location-weather'
		),
		docLink: 'https://locationweather.io/docs/weather-grid/',
		demoLink: 'https://locationweather.io/blocks/#demoId41',
	},
	'sp-location-weather-pro/combined': {
		icon: <CombinedBlockIcon />,
		title: __( 'Detailed Forecast', 'location-weather' ),
		description: __(
			'Display detailed daily and hourly forecasts with graphs, maps, and insights.',
			'location-weather'
		),
		docLink: 'https://locationweather.io/docs/weather-detailed-forecast/',
		demoLink: 'https://locationweather.io/blocks/#demoId42',
	},
	'sp-location-weather-pro/section-heading': {
		icon: <SectionHeadingBlockIcon />,
		title: __( 'Section Heading', 'location-weather' ),
		description: __(
			'Add stylish section headings with powerful customization options.',
			'location-weather'
		),
		docLink: 'https://locationweather.io/docs/section-header/',
		demoLink: 'https://locationweather.io/blocks/#demoId52',
	},
	'sp-location-weather-pro/map': {
		icon: <MapBlockIcon />,
		title: __( 'Weather Map by OWM', 'location-weather' ),
		description: __(
			'Display an interactive map from OpenWeather with live weather overlay information.',
			'location-weather'
		),
		docLink: 'https://locationweather.io/docs/weather-map-by-owm/',
		demoLink: 'https://locationweather.io/blocks/#demoId43',
	},
	'sp-location-weather-pro/windy-map': {
		icon: <WindyMapBlockIcon />,
		title: __( 'Radar Map by Windy', 'location-weather' ),
		description: __(
			'Display animated radar map from Windy showing real-time wind flows, storm tracking.',
			'location-weather'
		),
		docLink: 'https://locationweather.io/docs/radar-map-by-windy/',
		demoLink: 'https://locationweather.io/blocks/#demoId44',
	},
	'sp-location-weather-pro/aqi-detailed': {
		icon: <AqiBlockIcon />,
		title: __( 'AQI - Detailed Air Quality', 'location-weather' ),
		description: __(
			'Display current air quality index and forecast for the upcoming days.',
			'location-weather'
		),
		docLink: 'https://locationweather.io/docs/aqi-detailed-air-quality/',
		demoLink: 'https://locationweather.io/blocks/#demoId46',
	},
	'sp-location-weather-pro/historical-weather': {
		icon: <HistoricalWeatherBlockIcon />,
		title: __( 'Historical Weather Data', 'location-weather' ),
		description: __(
			'Display historical weather data by selecting a specific date and location.',
			'location-weather'
		),
		docLink: 'https://locationweather.io/docs/historical-weather-data/',
		demoLink: 'https://locationweather.io/blocks/#demoId45',
	},
	'sp-location-weather-pro/historical-aqi': {
		icon: <HistoricalAQIBlockIcon />,
		title: __( 'Historical Air Quality Data', 'location-weather' ),
		description: __(
			'Display historical AQI data calendar by selecting a specific date and location.',
			'location-weather'
		),
		docLink: 'https://locationweather.io/docs/historical-air-quality-data/',
		demoLink: 'https://locationweather.io/blocks/#demoId48',
	},
	'sp-location-weather-pro/sun-moon': {
		icon: <SunMoonTimesBlockIcon />,
		title: __( 'Sun & Moon Times', 'location-weather' ),
		description: __(
			'Display daily sun and moon times, phases, and durations for your location.',
			'location-weather'
		),
		docLink: 'https://locationweather.io/docs/sun-moon-times/',
		demoLink: 'https://locationweather.io/blocks/#demoId50',
	},
	'sp-location-weather-pro/shortcode': {
		icon: <ShortcodeBlockIcon />,
		title: __( 'Classic Shortcode', 'location-weather' ),
		description: __(
			'Insert Location Weather shortcode into any page with one click.',
			'location-weather'
		),
		docLink: 'https://locationweather.io/docs/location-weather-shortcode/',
		demoLink: 'https://locationweather.io/demos/',
	},
};

export const additionalDataOption = [
	{ id: 1, value: 'pressure', isActive: true },
	{ id: 2, value: 'humidity', isActive: true },
	{ id: 3, value: 'wind', isActive: true },
	{ id: 4, value: 'clouds', isActive: true },
	{ id: 5, value: 'gust', isActive: true },
	{
		id: 6,
		value: 'visibility',
		isActive: true,
	},
	{
		id: 7,
		value: 'sunriseSunset',
		isActive: true,
	},
	{
		id: 8,
		value: 'precipitation',
		isActive: false,
	},
	{
		id: 12,
		value: 'rainchance',
		isActive: false,
	},
	{ id: 13, value: 'snow', isActive: false },
	{ id: 9, value: 'uv_index', isActive: false },
	{ id: 10, value: 'dew_point', isActive: false },
	{
		id: 11,
		value: 'air_index',
		isActive: false,
	},
	{
		id: 14,
		value: 'moonriseMoonset',
		isActive: false,
	},
	{
		id: 15,
		value: 'moon_phase',
		isActive: false,
	},
];

export const weatherIconsSets = [
	{
		label: 'Animated',
		value: 'forecast_icon_set_one',
	},
	{
		label: 'Static (No-animation)',
		value: 'forecast_icon_set_two',
	},
	{
		label: 'Animated 2 (Line) [Pro]',
		value: 'forecast_icon_set_six',
		disabled: true,
	},
	{
		label: 'Animated 3 [Pro]',
		value: 'forecast_icon_set_seven',
		disabled: true,
	},
	{
		label: 'Light (Line) [Pro]',
		value: 'forecast_icon_set_three',
		disabled: true,
	},
	{
		label: 'Medium (Line) [Pro]',
		value: 'forecast_icon_set_eight',
		disabled: true,
	},
	{
		label: 'Fill [Pro]',
		value: 'forecast_icon_set_four',
		disabled: true,
	},
	{
		label: 'Glassmorphism [Pro]',
		value: 'forecast_icon_set_five',
		disabled: true,
	},
];

export const proBlocks = [
	'sp-location-weather-pro/combined',
	'sp-location-weather-pro/aqi-detailed',
	'sp-location-weather-pro/accordion',
	'sp-location-weather-pro/map',
	'sp-location-weather-pro/historical-weather',
	'sp-location-weather-pro/historical-aqi',
	'sp-location-weather-pro/sun-moon',
];
