import * as ReactDOM from '@wordpress/element';
import Render from './render';
import './style.scss';

window.addEventListener( 'DOMContentLoaded', () => {
	const rootElement = document.getElementById(
		'spl-weather-pro-block-admin-page'
	);
	if ( ! rootElement ) return;

	// Try to find existing wrapper
	let reactWrapper = document.querySelector(
		'.sp-location-weather-block-setting'
	);

	// If not found, create it
	if ( ! reactWrapper ) {
		reactWrapper = document.createElement( 'div' );
		reactWrapper.className = 'sp-location-weather-block-setting';

		// Insert React wrapper BEFORE current children
		rootElement.insertBefore( reactWrapper, rootElement.firstChild );
	}

	if ( typeof ReactDOM.createRoot === 'function' ) {
		const root = ReactDOM.createRoot( reactWrapper );
		root.render( <Render /> );
	} else if ( typeof ReactDOM.render === 'function' ) {
		ReactDOM.render( <Render />, reactWrapper );
	}
} );
