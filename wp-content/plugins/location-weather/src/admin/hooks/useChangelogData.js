import axios from 'axios';
import { useState, useEffect } from '@wordpress/element';

// Use this hook for retrieving changelog data.
const useChangelogData = ( showSidebar ) => {
	const [ changelog, setChangelog ] = useState( 'Loading...' );

	const data = new FormData();

	data.append( 'nonce', splw_admin_settings_localize.nonce );
	data.append( 'action', 'splw_changelog_data' );

	const fetchApi = async ( data ) => {
		try {
			const response = await axios.post( ajaxurl, data );
			const { changelog } = response.data;
			setChangelog( changelog );
		} catch ( error ) {
			console.error( 'Error fetching data:', error.message );
		}
	};
	useEffect( () => {
		if ( showSidebar ) {
			fetchApi( data );
		}
	}, [ showSidebar ] );
	return changelog;
};

export default useChangelogData;
