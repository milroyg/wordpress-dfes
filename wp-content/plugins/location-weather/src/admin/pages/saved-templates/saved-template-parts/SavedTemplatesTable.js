import { __ } from '@wordpress/i18n';
import { Spinner } from '@wordpress/components';
import { SavedTemplatesTableRow } from './SavedTemplatesTableRow';

export const SavedTemplatesTable = ( {
	tableCol,
	allCheck,
	onAllCheckChange,
	savedTemplateList,
	checkId,
	shortcodeCopied,
	noPostText,
	onCheckIdChange,
	onCopyShortCode,
	onDuplicate,
	onDelete,
} ) => {
	return (
		<table className="splw-saved-template-content-table">
			<thead className="splw-saved-template-table-head">
				<tr>
					{ tableCol?.map( ( item, i ) => (
						<th
							key={ i }
							className={ `splw-saved-template-table-${ item }` }
						>
							{ item !== 'checkBox' ? (
								item
							) : (
								<input
									type="checkbox"
									onChange={ () => {
										onAllCheckChange();
									} }
									checked={ allCheck }
								/>
							) }
						</th>
					) ) }
				</tr>
			</thead>
			<tbody className="splw-saved-template-table-body">
				{ ! noPostText &&
					( ! savedTemplateList ||
						savedTemplateList.length === 0 ) && (
						<tr>
							<td className="splw-saved-template-preloader-no-data">
								<span className="splw-saved-template-loading">
									<Spinner />
								</span>
							</td>
						</tr>
					) }
				{ savedTemplateList?.map( ( item, i ) => (
					<SavedTemplatesTableRow
						key={ i }
						item={ item }
						allCheck={ allCheck }
						checkId={ checkId }
						shortcodeCopied={ shortcodeCopied }
						onCheckIdChange={ onCheckIdChange }
						onCopyShortCode={ onCopyShortCode }
						onDuplicate={ onDuplicate }
						onDelete={ onDelete }
					/>
				) ) }
				{ noPostText &&
					( ! savedTemplateList ||
						savedTemplateList.length === 0 ) && (
						<tr>
							<td className="splw-saved-template-preloader-no-data">
								<span className="splw-saved-template-no-data">
									{ __(
										'No saved template found!',
										'location-weather'
									) }
								</span>
							</td>
						</tr>
					) }
			</tbody>
		</table>
	);
};
