/**
 * Shortcode select component.
 */
import { __ } from '@wordpress/i18n';
import { escapeHTML } from '@wordpress/escape-html';

const DynamicShortcodeInput = ( {
	attributes: { shortcode },
	shortCodeList,
	shortcodeUpdate,
} ) => (
	<div className="splwp-gutenberg-shortcode editor-styles-wrapper">
		<select
			className="splwp-shortcode-selector"
			onChange={ ( e ) => shortcodeUpdate( e ) }
			value={ shortcode }
		>
			<option value={ 0 }>
				{ escapeHTML(
					__( '-- Select a Shortcode --', 'location-weather' )
				) }
			</option>
			{ shortCodeList.map( ( shortcodeItem ) => {
				const title =
					shortcodeItem.title.length > 30
						? `${ shortcodeItem.title.substring( 0, 25 ) }.... #( ${
								shortcodeItem.id
						  } )`
						: `${ shortcodeItem.title } #( ${ shortcodeItem.id } )`;

				return (
					<option
						value={ shortcodeItem.id.toString() }
						key={ shortcodeItem.id.toString() }
					>
						{ escapeHTML( title ) }
					</option>
				);
			} ) }
		</select>
	</div>
);

export default DynamicShortcodeInput;
