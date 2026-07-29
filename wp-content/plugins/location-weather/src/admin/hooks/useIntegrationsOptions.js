import axios from 'axios';
import { useState, useEffect, useRef, useCallback } from '@wordpress/element';

/**
 * Custom hook to manage integrations visibility options.
 *
 * Initializes with localized data and syncs changes to the server via AJAX.
 * Used for toggling integrations enable/disable states in the admin dashboard.
 *
 * @returns {Array} [options, setOptions] - Current options and setter function
 *
 * @example
 * const [options, setOptions] = useIntegrationsOptions();
 */
const useIntegrationsOptions = () => {
	const initialData = splw_admin_settings_localize?.integrations_options ?? [];
	const [ options, setOptions ] = useState( initialData );
	const isInitialMount = useRef( true );

	/**
	 * Sync integrations options to the server.
	 *
	 * @param {Array} data - Options data to sync
	 */
	const syncOptions = useCallback( ( data ) => {
		if ( ! data?.length ) {
			return;
		}

		const formData = new FormData();
		formData.append( 'nonce', splw_admin_settings_localize.nonce );
		formData.append( 'action', 'splw_update_integrations_options' );
		formData.append( 'optionData', JSON.stringify( data ) );

		axios.post( ajaxurl, formData ).catch( ( error ) => {
			console.error( 'Error syncing integrations options:', error.response?.data?.message || error.message );
		} );
	}, [] );

	// Sync to server when options change (skip initial mount)
	useEffect( () => {
		if ( isInitialMount.current ) {
			isInitialMount.current = false;
			return;
		}

		syncOptions( options );
	}, [ options, syncOptions ] );

	return [ options, setOptions ];
};

export default useIntegrationsOptions;