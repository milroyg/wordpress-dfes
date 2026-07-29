import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';

const VIDEO_ID = 'vLdIHhP4v-I';


export const SavedTemplatesPromo = () => {
	const [ isPlaying, setIsPlaying ] = useState( false );

	const addNewUrl = `${ splw_admin_settings_localize?.homeUrl }wp-admin/post-new.php?post_type=spl_weather_template&splwblock_inserter`;

	return (
		<div className="splw-saved-template-promo">
			<div className="splw-saved-template-promo__text">
				<div className="splw-saved-template-promo__title-wrap">
					<h2 className="splw-saved-template-promo__title">
						{ __( 'Design visually.', 'location-weather' ) }
						<br />
						<span>{ __( 'Place it ', 'location-weather' ) }</span>
						<span className="splw-saved-template-promo__title-accent">
							{ __( 'anywhere.', 'location-weather' ) }
						</span>
					</h2>
					<p className="splw-saved-template-promo__desc">
						{ __(
							'Saved Templates let you build weather widgets using a visual block editor — then paste the shortcode into any ',
							'location-weather'
						) }
						<strong>
							{ __(
								'post, page, or page builder like Elementor, Divi, WPBakery',
								'location-weather'
							) }
						</strong>
						{ __(
							', and more. No coding needed.',
							'location-weather'
						) }
					</p>
				</div>
				<a
					href={ addNewUrl }
					rel="noreferrer"
					className="splw-saved-template-promo__cta"
				>
					<i className="dashicons dashicons-plus-alt2"></i>
					{ __( 'Add New Template', 'location-weather' ) }
				</a>
			</div>
			<div className="splw-saved-template-promo__video">
				{ isPlaying ? (
					<iframe
						className="splw-saved-template-promo__video-frame"
						src={ `https://www.youtube.com/embed/${ VIDEO_ID }?autoplay=1` }
						title={ __(
							'Location Weather overview video',
							'location-weather'
						) }
						allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
						allowFullScreen
					></iframe>
				) : (
					<button
						type="button"
						className="splw-saved-template-promo__video-thumb"
						style={ {
							backgroundImage: `url(https://img.youtube.com/vi/${ VIDEO_ID }/maxresdefault.jpg)`,
						} }
						onClick={ () => setIsPlaying( true ) }
						aria-label={ __(
							'Play overview video',
							'location-weather'
						) }
					>
						<span className="splw-saved-template-promo__video-overlay" />
						<span className="splw-saved-template-promo__video-play">
							<svg
								width="22"
								height="22"
								viewBox="0 0 22 22"
								fill="none"
								xmlns="http://www.w3.org/2000/svg"
								aria-hidden="true"
							>
								<path
									d="M19 11L4 19.6603L4 2.33975L19 11Z"
									fill="#F26C0D"
								/>
							</svg>
						</span>
					</button>
				) }
			</div>
		</div>
	);
};
