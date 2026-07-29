import { __ } from '@wordpress/i18n';
import DynamicShortcodeInput from './dynamicShortcode';
import { escapeAttribute, escapeHTML } from '@wordpress/escape-html';
import { PanelBody, PanelRow } from '@wordpress/components';
import { InspectorControls } from '@wordpress/block-editor';
const ServerSideRender = wp.serverSideRender;

const Edit = ( { attributes, setAttributes } ) => {
	const shortCodeList = sp_location_weather.shortCodeList;

	const scriptLoad = ( shortcodeId ) => {
		let sp_lwp_BlockLoaded = false;
		let sp_lwp_BlockLoadedInterval = setInterval( function () {
			let uniqId = jQuery( '#splw-location-weather-' + shortcodeId )
				.parent()
				.attr( 'id' );

			if ( document.getElementById( uniqId ) ) {
				// Preloader JS
				jQuery.getScript( sp_location_weather.loadScript );
				// youtube background video.
				if ( jQuery( document ).find( '[data-vbg]' ).length > 0 ) {
					jQuery( document )
						.find( '[data-vbg]' )
						.youtube_background();
				}
				sp_lwp_BlockLoaded = true;
				uniqId = '';
			}
			if ( sp_lwp_BlockLoaded ) {
				clearInterval( sp_lwp_BlockLoadedInterval );
			}
			if ( 0 == shortcodeId ) {
				clearInterval( sp_lwp_BlockLoadedInterval );
			}
		}, 10 );
	};

	const updateShortcode = ( updateShortcode ) => {
		setAttributes( { shortcode: updateShortcode.target.value } );
	};

	const shortcodeUpdate = ( e ) => {
		updateShortcode( e );
		const shortcodeId = e.target.value;
		scriptLoad( shortcodeId );
	};

	document.addEventListener( 'readystatechange', ( event ) => {
		if ( event.target.readyState === 'complete' ) {
			let shortcodeId = escapeAttribute( attributes.shortcode );
			scriptLoad( shortcodeId );
		}
	} );

	if ( attributes.preview ) {
		return (
			<div className="sp_lwp_shortcode_block_preview_image">
				<img
					src={ escapeAttribute(
						sp_location_weather.url +
							'/gutenberg-images/lw-block-preview.svg'
					) }
					alt=""
				/>
			</div>
		);
	}

	if ( shortCodeList.length === 0 ) {
		return (
			<div className="components-placeholder is-large">
				<div className="components-placeholder__label">
					<img
						className="block-editor-block-icon"
						src={ escapeAttribute(
							sp_location_weather.url +
								'/gutenberg-images/lw-icon.svg'
						) }
						alt=""
					/>
					{ escapeHTML(
						__( 'Location Weather', 'location-weather' )
					) }
				</div>
				<div className="components-placeholder__instructions">
					{ escapeHTML(
						__(
							'No shortcode found, Please try to add blocks. ',
							'location-weather'
						)
					) }
				</div>
			</div>
		);
	}

	if ( ! attributes.shortcode || attributes.shortcode === 0 ) {
		return (
			<>
				<InspectorControls>
					<PanelBody title="Select a shortcode">
						<PanelRow>
							<DynamicShortcodeInput
								attributes={ attributes }
								shortCodeList={ shortCodeList }
								shortcodeUpdate={ shortcodeUpdate }
							/>
						</PanelRow>
					</PanelBody>
				</InspectorControls>
				<div className="components-placeholder is-large">
					<div className="components-placeholder__label">
						<img
							className="block-editor-block-icon"
							src={ escapeAttribute(
								sp_location_weather.url +
									'/gutenberg-images/lw-icon.svg'
							) }
							alt=""
						/>
						{ escapeHTML(
							__( 'Location Weather', 'location-weather' )
						) }
					</div>
					<div className="components-placeholder__instructions">
						{ escapeHTML(
							__( 'Select a shortcode', 'location-weather' )
						) }
					</div>
					<DynamicShortcodeInput
						attributes={ attributes }
						shortCodeList={ shortCodeList }
						shortcodeUpdate={ shortcodeUpdate }
					/>
				</div>
			</>
		);
	}

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Select a shortcode', 'location-weather' ) }
				>
					<PanelRow>
						<DynamicShortcodeInput
							attributes={ attributes }
							shortCodeList={ shortCodeList }
							shortcodeUpdate={ shortcodeUpdate }
						/>
					</PanelRow>
				</PanelBody>
			</InspectorControls>
			<ServerSideRender
				block="sp-location-weather-pro/shortcode"
				attributes={ attributes }
			/>
		</>
	);
};

export default Edit;
