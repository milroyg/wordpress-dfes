import { __ } from '@wordpress/i18n';
import { registerPlugin } from '@wordpress/plugins';
import { useSelect } from '@wordpress/data';
import { createPortal, useEffect, useState } from '@wordpress/element';
import { copyText } from '../admin/functions';
import './editor.scss';

const CopyIcon = () => (
	<svg
		width="18"
		height="18"
		viewBox="0 0 18 18"
		fill="none"
		xmlns="http://www.w3.org/2000/svg"
		aria-hidden="true"
		focusable="false"
	>
		<path
			d="M6 6V3.75A.75.75 0 0 1 6.75 3h7.5a.75.75 0 0 1 .75.75v7.5a.75.75 0 0 1-.75.75H12M3.75 6.75h7.5a.75.75 0 0 1 .75.75v7.5a.75.75 0 0 1-.75.75h-7.5a.75.75 0 0 1-.75-.75v-7.5a.75.75 0 0 1 .75-.75Z"
			stroke="currentColor"
			strokeWidth="1.2"
			strokeLinecap="round"
			strokeLinejoin="round"
		/>
	</svg>
);

const CheckIcon = () => (
	<svg
		width="18"
		height="18"
		viewBox="0 0 18 18"
		fill="none"
		xmlns="http://www.w3.org/2000/svg"
		aria-hidden="true"
		focusable="false"
	>
		<path
			d="m4 9 3.5 3.5L14 6"
			stroke="currentColor"
			strokeWidth="1.5"
			strokeLinecap="round"
			strokeLinejoin="round"
		/>
	</svg>
);

const PORTAL_CLASSNAME = 'splw-shortcode-portal';

const SavedTemplateSidebar = () => {
	const { postType, postId } = useSelect(
		( select ) => ( {
			postType: select( 'core/editor' )?.getCurrentPostType?.() || null,
			postId: select( 'core/editor' )?.getCurrentPostId?.() || null,
		} ),
		[]
	);

	const [ copied, setCopied ] = useState( false );
	const [ portalTarget, setPortalTarget ] = useState( null );

	useEffect( () => {
		if ( postType !== 'spl_weather_template' ) {
			setPortalTarget( null );
			return undefined;
		}

		let container = null;

		const ensureContainer = () => {
			const fill = document.querySelector(
				'.interface-complementary-area__fill'
			);
			if ( ! fill ) {
				return;
			}

			let existing = fill.querySelector(
				`:scope > .${ PORTAL_CLASSNAME }`
			);
			if ( ! existing ) {
				existing = document.createElement( 'div' );
				existing.className = PORTAL_CLASSNAME;
				fill.insertBefore( existing, fill.firstChild );
			} else if ( fill.firstElementChild !== existing ) {
				fill.insertBefore( existing, fill.firstChild );
			}

			if ( existing !== container ) {
				container = existing;
				setPortalTarget( existing );
			}
		};

		ensureContainer();

		const observer = new window.MutationObserver( ensureContainer );
		observer.observe( document.body, { childList: true, subtree: true } );

		return () => {
			observer.disconnect();
			if ( container && container.parentNode ) {
				container.parentNode.removeChild( container );
			}
			setPortalTarget( null );
		};
	}, [ postType ] );

	if ( postType !== 'spl_weather_template' || ! portalTarget ) {
		return null;
	}

	const idForShortcode = postId || 0;
	const shortcode = `[location_weather id="${ idForShortcode }"]`;
	const savedTemplatesUrl =
		( window.splWeatherBlockLocalize &&
			window.splWeatherBlockLocalize.savedTemplatesUrl ) ||
		'';

	const handleCopy = async () => {
		const ok = await copyText( shortcode );
		if ( ! ok ) {
			return;
		}
		setCopied( true );
		window.setTimeout( () => setCopied( false ), 1500 );
	};

	return createPortal(
		<div className="splw-shortcode-panel">
			<p className="splw-shortcode-panel__intro">
				{ __(
					'You can use this shortcode anywhere and manage it from',
					'location-weather'
				) }{ ' ' }
				<a
					className="splw-shortcode-panel__link"
					href={ savedTemplatesUrl }
					rel="noopener noreferrer"
				>
					{ __( 'Saved Templates.', 'location-weather' ) }
				</a>
			</p>
			<button
				type="button"
				className="splw-shortcode-panel__chip"
				onClick={ handleCopy }
				aria-label={
					copied
						? __( 'Shortcode copied', 'location-weather' )
						: __( 'Copy shortcode', 'location-weather' )
				}
			>
				<span className="splw-shortcode-panel__code">
					{ shortcode }
				</span>
				<span className="splw-shortcode-panel__copy">
					{ copied ? <span className='splw-shortcode-panel__copy-text'>Copied!</span> : <CopyIcon /> }
				</span>
			</button>
		</div>,
		portalTarget
	);
};

registerPlugin( 'splw-saved-template-sidebar', {
	render: SavedTemplateSidebar,
} );
