import { Spinner } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export const SaveAndReset = ( { onSave, onReset, isChanged, isSaving } ) => {
	return (
		<div className={ `splw-settings-save-wrapper` }>
			<button
				className={ `splw-settings-save-btn ${
					isChanged ? 'active' : ''
				}` }
				onClick={ onSave }
			>
				{ isSaving ? (
					<>
						<Spinner /> { __( 'Saving…', 'location-weather' ) }
					</>
				) : (
					<>
						<img
							src={ `${ splw_admin_settings_localize?.pluginUrl }/includes/Admin/framework/assets/images/save-icon.svg` }
						/>
						{ __( 'Save Changes', 'location-weather' ) }
					</>
				) }
			</button>
			<button className="splw-settings-reset-btn" onClick={ onReset }>
				<img
					src={ `${ splw_admin_settings_localize?.pluginUrl }/includes/Admin/framework/assets/images/reset-icon.svg` }
				/>
				{ __( 'Reset', 'location-weather' ) }
			</button>
		</div>
	);
};

export default SaveAndReset;
