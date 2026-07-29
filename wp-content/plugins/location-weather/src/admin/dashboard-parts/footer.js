import {
	FacebookIcon,
	LoveIcon,
	TwitterXIcon,
	WordPressIcon,
	YoutubeIcon,
} from '../icons';
import { __ } from '@wordpress/i18n';

const Footer = () => {
	const socialMediaUrl = [
		{
			name: __( 'Facebook', 'location-weather' ),
			url: 'https://www.facebook.com/ShapedPlugin',
			Icon: FacebookIcon,
		},
		{
			name: __( 'X', 'location-weather' ),
			url: 'https://x.com/ShapedPlugin',
			Icon: TwitterXIcon,
		},
		{
			name: __( 'WordPress', 'location-weather' ),
			url: 'https://profiles.wordpress.org/shapedplugin',
			Icon: WordPressIcon,
		},
		{
			name: __( 'YouTube', 'location-weather' ),
			url: 'https://www.youtube.com/@ShapedPlugin',
			Icon: YoutubeIcon,
		},
	];

	return (
		<div className="spl-weather-settings-page-footer">
			<div className="spl-weather-settings-page-footer-wrapper">
				<div className="spl-weather-settings-page-footer-title">
					<h4>
						{ __( 'Made with', 'location-weather' ) } <LoveIcon />{ ' ' }
						{ __(
							'by the ShapedPlugin LLC Team',
							'location-weather'
						) }
					</h4>
				</div>
				<div className="spl-weather-settings-page-footer-social-media">
					<h4>{ __( 'Get connected with', 'location-weather' ) }</h4>
					<ul>
						{ socialMediaUrl?.map( ( { Icon, name, url }, i ) => (
							<li title={ name } key={ i }>
								<a href={ url } target="_blank">
									<Icon />
								</a>
							</li>
						) ) }
					</ul>
				</div>
			</div>
		</div>
	);
};

export default Footer;
