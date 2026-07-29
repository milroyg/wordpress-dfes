import axios from 'axios';
import { useState, useEffect, useRef, useCallback } from '@wordpress/element';

/**
 * Custom hook to manage block visibility options.
 *
 * Initializes with localized data and syncs changes to the server via AJAX.
 * Used for toggling block show/hide states in the admin dashboard.
 *
 * @returns {Array} [options, setOptions] - Current options and setter function
 *
 * @example
 * const [options, setOptions] = useBlockOptions();
 */
const useBlockOptions = () => {
	const initialData = splw_admin_settings_localize?.blocks_visibility ?? [];
	const [ options, setOptions ] = useState( initialData );
	const isInitialMount = useRef( true );

	/**
	 * Sync block visibility options to the server.
	 *
	 * @param {Array} data - Options data to sync
	 */
	const syncOptions = useCallback( ( data ) => {
		if ( ! data?.length ) {
			return;
		}

		const formData = new FormData();
		formData.append( 'nonce', splw_admin_settings_localize.nonce );
		formData.append( 'action', 'splw_update_block_options' );
		formData.append( 'optionData', JSON.stringify( data ) );

		axios.post( ajaxurl, formData ).catch( ( error ) => {
			console.error( 'Error syncing block visibility options:', error.response?.data?.message || error.message );
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

export default useBlockOptions;
