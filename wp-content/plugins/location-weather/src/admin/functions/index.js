import axios from 'axios';
import toast from 'react-hot-toast';
import { __ } from '@wordpress/i18n';

export const deleteWeatherTransients = async () => {
	try {
		const formData = new FormData();
		formData.append( 'nonce', splw_admin_settings_localize.nonce );
		formData.append( 'action', 'lwp_clean_weather_transients' );

		const response = await axios.post( ajaxurl, formData );

		if ( response?.data?.success ) {
			toastSuccessMsg(
				__( 'Cache Deleted Successfully', 'location-weather' )
			);
		} else {
			toastErrorMsg( __( 'Something Went Wrong' ) );
			return { success: false, data: response.data };
		}
	} catch ( error ) {
		alert( 'Request error:', error );
		return { success: false, error };
	}
};

export const saveSettingOptions = async (
	settings,
	actionType = 'save',
	setSettingsOptions,
	shareData,
	editorPreference
) => {
	try {
		const formData = new FormData();

		formData.append( 'nonce', splw_admin_settings_localize.nonce );
		formData.append( 'action', 'splw_update_setting_options' );
		formData.append( 'optionData', JSON.stringify( settings ) );
		if ( shareData !== undefined ) {
			formData.append( 'shareData', JSON.stringify( shareData ) );
		}
		if ( editorPreference !== undefined ) {
			formData.append( 'editorPreference', editorPreference );
		}

		const response = await axios.post( ajaxurl, formData );

		if ( response?.data?.success ) {
			if ( actionType === 'save' ) {
				toastSuccessMsg(
					__( 'Saved successfully', 'location-weather' )
				);
			} else {
				toastSuccessMsg(
					__( 'Reset successfully', 'location-weather' )
				);
			}
			setSettingsOptions( response?.data?.data?.options );
		} else {
			toastErrorMsg( __( 'Something Went Wrong', 'location-weather' ) );
			return { success: false, data: response.data };
		}
	} catch ( error ) {
		toastErrorMsg( __( 'Something Went Wrong', 'location-weather' ) );
		console.error( error );
		return { success: false, error };
	}
};

export const toastSuccessMsg = ( message ) => {
	return toast.success( message, {
		style: {
			marginTop: '28px',
			fontSize: '15px',
			padding: '10px 18px',
		},
	} );
};

export const toastErrorMsg = ( message ) => {
	return toast.error( message, {
		style: {
			marginTop: '28px',
			fontSize: '15px',
			padding: '10px 18px',
		},
	} );
};

export const copyText = async ( text ) => {
	// First try Clipboard API
	if ( navigator.clipboard && navigator.clipboard.writeText ) {
		try {
			await navigator.clipboard.writeText( text );
			return true;
		} catch ( e ) {
			console.warn( 'Clipboard API failed, using fallback.', e );
		}
	}

	// Fallback method: hidden textarea + execCommand
	try {
		const textarea = document.createElement( 'textarea' );
		textarea.value = text;

		// Hide from screen
		textarea.style.position = 'fixed';
		textarea.style.opacity = '0';
		textarea.style.pointerEvents = 'none';

		document.body.appendChild( textarea );
		textarea.select();

		const success = document.execCommand( 'copy' );
		document.body.removeChild( textarea );

		return success;
	} catch ( err ) {
		console.error( 'Fallback copy failed:', err );
		return false;
	}
};
