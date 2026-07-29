import { __ } from '@wordpress/i18n';

export const SavedTemplatesHeader = ( {
	selectBulkValue,
	setSelectBulkValue,
	onApplyBulkAction,
	onSearch,
} ) => {
	return (
		<div className="splw-saved-template-header">
			<div className="splw-saved-template-header-left">
				<select
					name="bulk-action"
					className="splw-saved-template-select"
					value={ selectBulkValue }
					onChange={ ( e ) => setSelectBulkValue( e.target.value ) }
				>
					<option value="">
						{ __( 'Bulk Action', 'location-weather' ) }
					</option>
					<option value="publish">
						{ __( 'Publish', 'location-weather' ) }
					</option>
					<option value="draft">
						{ __( 'Draft', 'location-weather' ) }
					</option>
					<option value="delete">
						{ __( 'Delete', 'location-weather' ) }
					</option>
				</select>
				<button
					className="splw-saved-template-select-apply"
					onClick={ onApplyBulkAction }
				>
					{ __( 'Apply', 'location-weather' ) }
				</button>
				<input
					name="search-weather-template"
					className="splw-saved-template-search-field"
					type="text"
					placeholder="Search..."
					spellCheck="false"
					data-ms-editor="true"
					onChange={ onSearch }
				/>
			</div>
			<div className="splw-saved-template-header-right">
				<a
					href={ `${ splw_admin_settings_localize?.homeUrl }wp-admin/post-new.php?post_type=spl_weather_template&splwblock_inserter` }
					className="splw-saved-template-add-new"
					rel="noreferrer"
				>
					<i className="dashicons dashicons-plus-alt2"></i>
					{ __( 'Add New Template', 'location-weather' ) }
				</a>
			</div>
		</div>
	);
};
