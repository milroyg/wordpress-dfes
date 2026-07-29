import { __ } from '@wordpress/i18n';
import { registerBlockType, updateCategory } from '@wordpress/blocks';
import domReady from '@wordpress/dom-ready';
import { CategoryIcon, ProBlocksCategoryIcon } from './icons';
import ShortCodeEdit from './blocks/shortcode/edit';
import VerticalBlockEdit from './blocks/vertical/edit';
import HorizontalBlockEdit from './blocks/horizontal/edit';
import WindyMapEdit from './blocks/windy-map/edit';
import WeatherGridEdit from './blocks/grid/edit';
import TabsBlockEdit from './blocks/tabs/edit';
import TableBlockEdit from './blocks/table/edit';
import { blockRegisterInfo } from './controls';
import './style.scss';
import './editor.scss';
import saveBlockCSS from './controls/saveBlockCSS';
import AqiMinimalEdit from './blocks/aqi-minimal/edit';
import SectionHeadingEdit from './blocks/section-heading/edit';
import SectionHeadingSave from './blocks/section-heading/save';
import { SectionHeadingAttributes } from './blocks/section-heading/attributes';
import ProBlockPlaceholder from './blocks/shared/templates/proBlocksPlaceholder';
import { ToolbarLibrary } from './prebuild-library';
import './controls/redirectToBlockEditor';
import './saved-template-sidebar';

updateCategory( 'location-weather', { icon: <CategoryIcon /> } );
updateCategory( 'location-weather-pro-blocks', {
	icon: <ProBlocksCategoryIcon />,
} );

const blocks = [
	{
		apiVersion: 3,
		name: 'sp-location-weather-pro/vertical-card',
		edit: VerticalBlockEdit,
		save: () => null,
	},
	{
		apiVersion: 3,
		name: 'sp-location-weather-pro/horizontal',
		edit: HorizontalBlockEdit,
		save: () => null,
	},
	{
		apiVersion: 3,
		name: 'sp-location-weather-pro/aqi-minimal',
		edit: AqiMinimalEdit,
		save: () => null,
	},
	{
		apiVersion: 3,
		name: 'sp-location-weather-pro/grid',
		edit: WeatherGridEdit,
		save: () => null,
	},
	{
		apiVersion: 3,
		name: 'sp-location-weather-pro/tabs',
		edit: TabsBlockEdit,
		save: () => null,
	},
	{
		apiVersion: 3,
		name: 'sp-location-weather-pro/table',
		edit: TableBlockEdit,
		save: () => null,
	},
	{
		apiVersion: 3,
		name: 'sp-location-weather-pro/windy-map',
		edit: WindyMapEdit,
		save: () => null,
	},
	{
		apiVersion: 3,
		name: 'sp-location-weather-pro/combined',
		edit: ProBlockPlaceholder,
		save: () => null,
	},
	{
		apiVersion: 3,
		name: 'sp-location-weather-pro/aqi-detailed',
		edit: ProBlockPlaceholder,
		save: () => null,
	},
	{
		apiVersion: 3,
		name: 'sp-location-weather-pro/accordion',
		edit: ProBlockPlaceholder,
		save: () => null,
	},
	{
		apiVersion: 3,
		name: 'sp-location-weather-pro/map',
		edit: ProBlockPlaceholder,
		save: () => null,
	},
	{
		apiVersion: 3,
		name: 'sp-location-weather-pro/historical-weather',
		edit: ProBlockPlaceholder,
		save: () => null,
	},
	{
		apiVersion: 3,
		name: 'sp-location-weather-pro/historical-aqi',
		edit: ProBlockPlaceholder,
		save: () => null,
	},
	{
		apiVersion: 3,
		name: 'sp-location-weather-pro/sun-moon',
		edit: ProBlockPlaceholder,
		save: () => null,
	},
	{
		apiVersion: 3,
		name: 'sp-location-weather-pro/section-heading',
		attributes: SectionHeadingAttributes,
		edit: SectionHeadingEdit,
		save: SectionHeadingSave,
	},
	{
		name: 'sp-location-weather-pro/shortcode',
		edit: ShortCodeEdit,
		save: () => null,
	},
];

const proBlockList = [
	'sp-location-weather-pro/combined',
	'sp-location-weather-pro/aqi-detailed',
	'sp-location-weather-pro/accordion',
	'sp-location-weather-pro/map',
	'sp-location-weather-pro/historical-weather',
	'sp-location-weather-pro/historical-aqi',
	'sp-location-weather-pro/sun-moon',
];

// Register Parent (active) Blocks.
const registerBlockTypeFn = ( options ) => {
	const blockCategory = proBlockList.includes( options.name )
		? 'location-weather-pro-blocks'
		: 'location-weather';
	const blockOptions = {
		...options,
		name: options.name,
		title: blockRegisterInfo[ options.name ]?.title,
		icon: blockRegisterInfo[ options.name ]?.icon,
		description: blockRegisterInfo[ options.name ]?.description || '',
		category: blockCategory,
		textdomain: 'location-weather',
		supports: {
			align: [ 'wide', 'full' ],
			customClassName: false,
		},
		example: {
			attributes: {
				isPreview: true,
			},
		},
	};
	registerBlockType( blockOptions.name, blockOptions );
};

blocks?.map( ( block ) => {
	const isActive = splWeatherBlockLocalize?.blockOptions?.find(
		( option ) => option.name === block.name
	)?.show;
	// If the block is active, register it.
	if ( isActive ) {
		registerBlockTypeFn( block );
	}
} );

ToolbarLibrary();

// Run once the editor DOM is ready
domReady( () => {
	saveBlockCSS();
} );
