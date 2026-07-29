import { Arrow } from '../../icons';
import { ArrowRight } from '../../icons';
import { __ } from '@wordpress/i18n';
import {
	SmartPostIcon,
	EasyAccordionIcon,
	SmartTabsIcon,
	WooGalleryIcon,
	WPCarouselIcon,
	LogoShowcaseIcon,
	RealTestimonialIcon,
	WooProductIcon,
	SmartSwatch,
	WooCategoryIcon,
	SmartTeam,
	SmartBrand,
} from './icon';

// Plugin data for showcase - matching Figma design exactly
const morePlugins = [
	{
		name: 'WP Carousel',
		description: 'The most powerful and user-friendly multi-purpose carousel, slider, & gallery plugin for WordPress.',
		url: 'https://wpcarousel.io/',
		icon: <WPCarouselIcon />,
	},
	{
		name: 'Real Testimonial',
		description: 'Simply collect, manage, and display Testimonials on your website and boost conversions.',
		url: 'https://realtestimonials.io/',
		icon: <RealTestimonialIcon />,
	},
	{
		name: 'Easy Accordion',
		description: 'Minimize customer support by offering comprehensive FAQs and increasing conversions.',
		url: 'https://easyaccordion.io/',
		icon: <EasyAccordionIcon />,
	},
	{
		name: 'Smart Tabs',
		description: 'Best WooCommerce Custom Product Tabs & WordPress Tabs Builder Plugin to create responsive tabs.',
		url: 'https://wptabs.com/',
		icon: <SmartTabsIcon />,
	},
	{
		name: 'Smart Post',
		description: 'Filter and display posts (any post types), pages, taxonomy, custom taxonomy, and custom field, in beautiful layouts.',
		url: 'https://wpsmartpost.com/',
		icon: <SmartPostIcon />,
	},
	{
		name: 'Smart Team',
		description: 'Simply collect, manage, and display Testimonials on your website and boost conversions.',
		url: 'https://getwpteam.com/',
		icon: <SmartTeam />,
	},
	{
		name: 'Logo Carousel',
		description: 'Showcase a group of logo images with Title, Description, Tooltips, Links, and Popup as a grid or in a carousel.',
		url: 'https://logocarousel.com/',
		icon: <LogoShowcaseIcon />,
	},
	{
		name: 'WooGallery',
		description: 'Product gallery slider and additional variation images gallery for WooCommerce and boost your sales.',
		url: 'https://woogallery.io/',
		icon: <WooGalleryIcon />,
	},
	{
		name: 'Product Slider for WooCommerce',
		description: 'Boost sales by interactive product Slider, Grid, and Table in your WooCommerce website or store.',
		url: 'https://wooproductslider.io/',
		icon: <WooProductIcon />,
	},
	{
		name: 'WooCategory',
		description: 'Display by filtering the list of categories aesthetically and boosting sales.',
		url: 'https://shapedplugin.com/woocategory/',
		icon: <WooCategoryIcon />,
	},
	{
		name: 'Smart Swatches',
		description: 'Smart Swatches is a Best Product Variation Swatches for WooCommerce to Boost Your Store Sales.',
		url: 'https://shapedplugin.com/smart-swatches-for-woocommerce',
		icon: <SmartSwatch />,
	},
	{
		name: 'Smart Brands',
		description: 'Smart Brands for WooCommerce Pro helps you display product brands in an attractive way on your online store.',
		url: 'https://shapedplugin.com/smart-brands/',
		icon: <SmartBrand />,
	},
];

const AboutUs = () => {
	return (
		<section id="about-us-tab" className="splw-about-page">
			<div className="splw-about-box">
				<div className="splw-about-info">
					<h3>
						{ __(
							'The Most Powerful Weather & AQI Plugin for ',
							'location-weather'
						) }
						{ __(
							'WordPress from the ',
							'location-weather'
						) }
						<span className="splw-highlight-text">
							{ __(
								'Location Weather Team',
								'location-weather'
							) }
						</span>
					</h3>
					<p>
						{ __(
							'In early 2016, while building a WordPress news site for our partner company, we needed a simple, reliable way to display live weather updates and forecasts.',
							'location-weather'
						) }{ ' ' }
						{ __(
							"After searching extensively, we couldn't find any plugin that met our standards for accuracy, design, and ease of use.",
							'location-weather'
						) }{ ' ' }
						{ __(
							'So we set out with a clear mission: create a powerful yet user-friendly WordPress weather solution.',
							'location-weather'
						) }{ ' ' }
						{ __(
							'That mission became Location Weather—built to help anyone display beautiful, accurate',
							'location-weather'
						) }{ ' ' }
						<b>
							{ __(
								'Weather Forecasts, AQI, and Astronomy Data',
								'location-weather'
							) }
						</b>{ ' ' }
						{ __(
							"easily on their WordPress sites. We're confident you'll love the experience!",
							'location-weather'
						) }
					</p>
					<div className="splwb-video-section-btn">
						<ul>
							<li>
								<a
									target="_blank"
									href="https://locationweather.io/"
									className="splw-medium-btn"
								>
									{ __(
										'Explore Location Weather',
										'location-weather'
									) }
								</a>
							</li>
							<li>
								<a
									target="_blank"
									href="https://shapedplugin.com/about-us/"
									className="splw-medium-btn splw-arrow-btn"
								>
									{ __(
										'More About Us ',
										'location-weather'
									) }{ ' ' }
									<Arrow />
								</a>
							</li>
						</ul>
					</div>
				</div>
				<div className="splw-about-img">
					<img
						src={ `${ splw_admin_settings_localize?.pluginUrl }/assets/images/lw_team.webp` }
						alt="Team"
						height="402"
						width="610"
					/>
					<span>
						{ __(
							'The Creative Minds Behind the Location Weather Plugin',
							'location-weather'
						) }
					</span>
				</div>
			</div>

			{/* More Plugins Section */}
			<div className="splw-more-plugins-section">
				<div className="splw-more-plugins-header">
					<h2 className="splw-more-plugins-title">
						{ __( 'Your Website Deserves More Than Typical — Go Premium Today!', 'location-weather' ) }
					</h2>
					<p className="splw-more-plugins-subtitle">
						{ __( 'Unlock powerful plugins built to boost performance, elevate design, and grow your business.', 'location-weather' ) }
					</p>
				</div>
				<div className="splw-more-plugins-grid">
					{ morePlugins.map( ( plugin ) => (
						<a
							key={ plugin.name }
							href={ plugin.url }
							target="_blank"
							rel="noopener noreferrer"
							className="splw-plugin-card"
						>
							<div className="splw-plugin-card-icon">
								{ plugin.icon }
							</div>
							<div className="splw-plugin-card-content">
								<h3 className="splw-plugin-card-title">{ plugin.name }</h3>
								<p className="splw-plugin-card-desc">{ plugin.description }</p>
							</div>
							<span className="splw-plugin-card-arrow">
								<ArrowRight />
							</span>
						</a>
					) ) }
				</div>
			</div>
		</section>
	);
};

export default AboutUs;
