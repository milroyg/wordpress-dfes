import { CopyIcon, DeleteBinIcon, EditPencilIcon } from '../../../icons';
import { Tooltip } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export const SavedTemplatesTableRow = ( {
	item,
	allCheck,
	checkId,
	shortcodeCopied,
	onCheckIdChange,
	onCopyShortCode,
	onDuplicate,
	onDelete,
} ) => {
	const date = new Date( item?.modified );
	const checkBoxValue = allCheck
		? true
		: checkId?.some( ( itemId ) => itemId === item?.id );

	return (
		<tr className="splw-saved-template-table-row">
			<td id={ item?.id } className="splw-saved-template-table-checkBox">
				<input
					type="checkbox"
					onChange={ () => onCheckIdChange( item?.id ) }
					checked={ checkBoxValue }
				/>
			</td>
			<td className="splw-saved-template-table-title">
				<a
					href={ `${ splw_admin_settings_localize?.homeUrl }wp-admin/post.php?post=${ item?.id }&action=edit&splwblock_inserter` }
					rel="noreferrer noopener"
				>
					<span
						dangerouslySetInnerHTML={ {
							__html: item?.title?.rendered || '(No Title)',
						} }
					/>
				</a>
			</td>
			<td className="splw-saved-template-table-shortcode">
				<span
					className="splw-saved-template-shortcode-text"
					onClick={ () => onCopyShortCode( item?.id ) }
				>
					{ `[location_weather id="${ item?.id }"]` }
					<CopyIcon />
				</span>
				<span
					className="splw-shortcode-copy-tooltip"
					style={ {
						opacity: shortcodeCopied === item.id ? 1 : 0,
					} }
				>
					{ __( 'Copied!', 'location-weather' ) }
				</span>
			</td>
			<td className="splw-saved-template-table-date">
				<div className="status">
					{ item?.status === 'publish' ? 'published' : item?.status }
				</div>
				<div>{ date?.toLocaleString( 'en-US' ) }</div>
			</td>
			<td className="splw-saved-template-table-action">
				<div className="splw-saved-template-table-action-btn">
					<Tooltip text="Edit" delay={ 300 } placement="top">
						<a
							aria-label="Edit"
							href={ `${ splw_admin_settings_localize?.homeUrl }wp-admin/post.php?post=${ item?.id }&action=edit&splwblock_inserter` }
							className="splw-saved-template-action sp-action-edit"
							rel="noreferrer"
						>
							<EditPencilIcon />
						</a>
					</Tooltip>
					<Tooltip text="Duplicate" delay={ 300 } placement="top">
						<button
							aria-label="Duplicate"
							className="splw-saved-template-action sp-action-copy"
							onClick={ () => onDuplicate( item?.id ) }
						>
							<CopyIcon />
						</button>
					</Tooltip>
					<Tooltip text="Delete" delay={ 300 } placement="top">
						<button
							aria-label="Delete"
							className="splw-saved-template-action sp-action-delete"
							onClick={ () => onDelete( item?.id ) }
						>
							<DeleteBinIcon />
						</button>
					</Tooltip>
				</div>
			</td>
		</tr>
	);
};
