import { __ } from '@wordpress/i18n';
import {
	ElementorIcon,
	DiviIcon,
	WPBakeryIcon,
	OxygenIcon,
	BeaverIcon,
	BricksIcon,
} from '../pages/integrations/icons';

export const integrationsRegisterInfo = {
	elementor: {
		name: __( 'Elementor', 'location-weather' ),
		description: __(
			'It allows using Location Weather Gutenberg blocks in Elementor via the Saved Template Addon.',
			'location-weather'
		),
		icon: <ElementorIcon />,
		iconBg: '#EE00EE',
		demoLink: '#',
		docsLink: 'https://locationweather.io/docs/location-weather-with-elementor/',
		upcoming: false,
	},
	divi: {
		name: __( 'Divi', 'location-weather' ),
		description: __(
			'It allows using Location Weather Gutenberg blocks in Divi via the Saved Template Addon.',
			'location-weather'
		),
		icon: <DiviIcon />,
		iconBg: 'linear-gradient(135deg, #4b81eb 0%, #6752f4 100%)',
		demoLink: '#',
		docsLink: 'https://locationweather.io/docs/location-weather-with-divi-builder/',
		upcoming: false,
	},
	wpbakery: {
		name: __( 'WPBakery', 'location-weather' ),
		description: __(
			'It allows using Location Weather Gutenberg blocks in WPBakery via the Saved Template Addon.',
			'location-weather'
		),
		icon: <WPBakeryIcon />,
		iconBg: 'linear-gradient(135deg, #0074af 0%, #037bb1 12%, #0c79b7 24%, #1c80c1 36%, #328acf 39%, #4f96e1 51%, #7fabff 72%)',
		demoLink: 'https://locationweather.io/demo/wpbakery/',
		docsLink: 'https://locationweather.io/docs/',
		upcoming: true,
	},
	oxygen: {
		name: __( 'Oxygen', 'location-weather' ),
		description: __(
			'It allows using Location Weather Gutenberg blocks in Oxygen via the Saved Template Addon.',
			'location-weather'
		),
		icon: <OxygenIcon />,
		iconBg: 'linear-gradient(135deg, #2c1870 0%, #783fff 77%)',
		demoLink: 'https://locationweather.io/demo/oxygen/',
		docsLink: 'https://locationweather.io/docs/',
		upcoming: true,
	},
	beaver: {
		name: __( 'Beaver', 'location-weather' ),
		description: __(
			'It allows using Location Weather Gutenberg blocks in Beaver via the Saved Template Addon.',
			'location-weather'
		),
		icon: <BeaverIcon />,
		iconBg: 'linear-gradient(135deg, #aa7a55 13%, #8c5934 89%)',
		demoLink: 'https://locationweather.io/demo/beaver/',
		docsLink: 'https://locationweather.io/docs/',
		upcoming: true,
	},
	bricks: {
		name: __( 'Bricks', 'location-weather' ),
		description: __(
			'It allows using Location Weather Gutenberg blocks in Bricks via the Saved Template Addon.',
			'location-weather'
		),
		icon: <BricksIcon />,
		iconBg: 'linear-gradient(135deg, #ffaa00 11%, #ffce4f 88%)',
		demoLink: 'https://locationweather.io/demo/bricks/',
		docsLink: 'https://locationweather.io/docs/',
		upcoming: true,
	},
};