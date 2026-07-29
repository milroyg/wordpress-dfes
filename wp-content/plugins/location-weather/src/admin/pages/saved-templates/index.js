import {
	useEffect,
	useRef,
	useState,
	useMemo,
	useCallback,
} from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { copyText, toastErrorMsg, toastSuccessMsg } from '../../functions';
import { useDispatch, resolveSelect, useSelect } from '@wordpress/data';
import {
	SavedTemplatesHeader,
	SavedTemplatesPromo,
	SavedTemplatesTable,
	SavedTemplatesPagination,
	SavedTemplatesFooter,
} from './saved-template-parts';

const SavedTemplates = () => {
	const tableCol = [ 'checkBox', 'title', 'shortcode', 'date', 'action' ];
	const [ selectBulkValue, setSelectBulkValue ] = useState( '' );
	const [ searchValue, setSearchValue ] = useState( '' );
	const [ currentPage, setCurrentPage ] = useState( 1 );
	const [ allCheck, setAllCheck ] = useState( false );
	const [ checkId, setCheckId ] = useState( [] );
	const [ shortcodeCopied, setShortcodeCopied ] = useState( '' );
	const [ noPostText, setNoPostText ] = useState( false );
	const timeoutRef = useRef( null );

	// Get total post count efficiently using REST API headers.
	const [ totalPostCount, setTotalPostCount ] = useState( 0 );

	useEffect( () => {
		const fetchTotalCount = async () => {
			try {
				const params = new URLSearchParams( {
					status: 'any',
					per_page: 1,
					search: searchValue,
					_fields: 'id',
				} );

				const response = await fetch(
					`/wp-json/wp/v2/spl_weather_template?${ params.toString() }`,
					{
						headers: {
							'X-WP-Nonce': wpApiSettings?.nonce || '',
						},
					}
				);

				// Get total count from response headers (more efficient than fetching all records)
				const total = parseInt(
					response.headers.get( 'X-WP-Total' ) || '0',
					10
				);
				setTotalPostCount( total );
			} catch ( error ) {
				console.error( 'Error fetching total count:', error );
				setTotalPostCount( 0 );
			}
		};

		fetchTotalCount();
	}, [ searchValue ] );

	const savedTemplateList = useSelect(
		( select ) =>
			select( 'core' )?.getEntityRecords(
				'postType',
				'spl_weather_template',
				{
					status: 'any',
					per_page: 10,
					offset: searchValue ? 0 : ( currentPage - 1 ) * 10,
					search: searchValue,
					_fields: [ 'id', 'modified', 'title', 'status' ],
				}
			),
		[ searchValue, currentPage ]
	);

	// Copy Shortcode Upon Clicking Short code.
	const copyShortCodeHandler = useCallback( ( value ) => {
		const updateValue = `[location_weather id="${ value }"]`;
		const copied = copyText( updateValue );

		if ( copied ) {
			setShortcodeCopied( value );
		} else {
			toastErrorMsg(
				__( 'Failed to copy shortcode', 'location-weather' )
			);
		}
	}, [] );

	const checkIdHandler = useCallback(
		( itemId ) => {
			const hasValue = checkId.includes( itemId );
			const updateValue = hasValue
				? checkId?.filter( ( value ) => value !== itemId )
				: [ ...checkId, itemId ];
			setCheckId( updateValue );
			setAllCheck( false );
		},
		[ checkId ]
	);

	// Set Search value with debounce.
	const searchValueHandler = ( e ) => {
		const searchInputValue = e.target?.value;
		if ( timeoutRef.current ) {
			clearTimeout( timeoutRef.current );
		}
		timeoutRef.current = setTimeout( () => {
			setSearchValue( searchInputValue );
		}, 100 );
	};

	// Get Delete entity record.
	const {
		deleteEntityRecord,
		editEntityRecord,
		saveEntityRecord,
		saveEditedEntityRecord,
		invalidateResolution,
	} = useDispatch( 'core' );
	const deleteItemHandler = async ( itemId = null ) => {
		const deleteId = itemId ? [ itemId ] : checkId;
		if ( deleteId?.length < 1 ) {
			return;
		}
		const confirmed = window.confirm(
			__(
				'Are you sure you want to delete this saved template?',
				'location-weather'
			)
		);
		if ( confirmed ) {
			await Promise.all(
				deleteId.map( async ( id ) => {
					try {
						await deleteEntityRecord(
							'postType',
							'spl_weather_template',
							id,
							{ force: true }
						);
					} catch ( error ) {
						// console.error(`Error deleting template ID: ${id}`, error);
						toastErrorMsg(
							`Error deleting template ID: ${ id }: ${ error } `
						);
					}
				} )
			);
			const updateData = itemId
				? checkId?.filter( ( itemValueId ) => itemValueId !== deleteId )
				: [];
			setCheckId( updateData );
			toastSuccessMsg( 'Template deleted successfully.' );
		}
	};

	// Update Post Status.
	const updateStatusHandler = async ( newStatus = 'publish' ) => {
		const updateId = checkId;
		if ( updateId?.length < 1 ) {
			return;
		}
		await Promise.all(
			updateId?.map( async ( id ) => {
				try {
					if ( ! id ) {
						return;
					}
					// Check if record exists in the store
					const record = await resolveSelect(
						'core'
					).getEntityRecord( 'postType', 'spl_weather_template', id );

					if ( ! record ) {
						return;
					}
					await editEntityRecord(
						'postType',
						'spl_weather_template',
						id,
						{ status: newStatus }
					);
					await saveEditedEntityRecord(
						'postType',
						'spl_weather_template',
						id
					);
					toastSuccessMsg(
						'Template post status updated successfully.'
					);
				} catch ( error ) {
					// console.error(`Error update template ID: ${id}`, error);
					toastErrorMsg(
						`Error while updating template ID: ${ id }: ${ error } `
					);
				}
			} )
		);
		setCheckId( [] );
	};

	// Bulk Action Function.
	const bulkActionHandler = () => {
		if ( selectBulkValue === '' ) {
			return;
		}
		switch ( selectBulkValue ) {
			case 'publish':
				updateStatusHandler( 'publish' );
				break;

			case 'draft':
				updateStatusHandler( 'draft' );
				break;

			case 'delete':
				deleteItemHandler();
				break;

			default:
				break;
		}
		setCheckId( [] );
		setAllCheck( false );
		setSelectBulkValue( '' );
	};

	const duplicateShortcodeHandler = async ( templateId ) => {
		try {
			// Get original template
			const original = await resolveSelect( 'core' ).getEntityRecord(
				'postType',
				'spl_weather_template',
				templateId
			);

			if ( ! original ) {
				toastErrorMsg( 'Template not found' );
				return;
			}

			// Save new template (no ID = new record)
			await saveEntityRecord( 'postType', 'spl_weather_template', {
				title: `${ original.title.raw || '(No title)' } (Copy)`,
				content: original.content?.raw || '',
				meta: original.meta || {},
				status: 'draft',
			} );

			// Refresh list
			invalidateResolution( 'getEntityRecords', [
				'postType',
				'spl_weather_template',
			] );

			toastSuccessMsg( 'Template duplicated successfully.' );
		} catch ( error ) {
			toastErrorMsg( `Failed to duplicate template: ${ error.message }` );
		}
	};

	useEffect( () => {
		if ( ! shortcodeCopied ) return;

		const timer = setTimeout( () => {
			setShortcodeCopied( '' );
		}, 2000 );

		return () => clearTimeout( timer );
	}, [ shortcodeCopied ] );

	useEffect( () => {
		if ( savedTemplateList?.length < 1 ) {
			if ( timeoutRef.current ) {
				clearTimeout( timeoutRef.current );
			}
			timeoutRef.current = setTimeout( () => {
				setNoPostText( true );
			}, 300 );
		}
	}, [ savedTemplateList ] );

	// Pagination - memoize to prevent unnecessary recalculations
	const totalPages = useMemo(
		() => Math.ceil( totalPostCount / 10 ),
		[ totalPostCount ]
	);

	const pages = useMemo(
		() => Array.from( { length: totalPages }, ( _, i ) => i + 1 ),
		[ totalPages ]
	);

	return (
		<div className="splw-saved-templates-page-wrapper">
			<div className="splw-saved-templates-page-container">
				<SavedTemplatesHeader
					selectBulkValue={ selectBulkValue }
					setSelectBulkValue={ setSelectBulkValue }
					onApplyBulkAction={ bulkActionHandler }
					onSearch={ searchValueHandler }
				/>
				<SavedTemplatesTable
					tableCol={ tableCol }
					allCheck={ allCheck }
					onAllCheckChange={ () => {
						setAllCheck( ( prev ) => ! prev );
						setCheckId(
							! allCheck
								? savedTemplateList?.map(
										( listItem ) => listItem.id
								  )
								: []
						);
					} }
					savedTemplateList={ savedTemplateList }
					checkId={ checkId }
					shortcodeCopied={ shortcodeCopied }
					noPostText={ noPostText }
					onCheckIdChange={ checkIdHandler }
					onCopyShortCode={ copyShortCodeHandler }
					onDuplicate={ duplicateShortcodeHandler }
					onDelete={ deleteItemHandler }
				/>
				<SavedTemplatesFooter
					currentPage={ currentPage }
					totalPages={ totalPages }
					totalPostCount={ totalPostCount }
					pages={ pages }
				>
					<SavedTemplatesPagination
						currentPage={ currentPage }
						pages={ pages }
						onPageChange={ setCurrentPage }
					/>
				</SavedTemplatesFooter>
			</div>
			<SavedTemplatesPromo />
		</div>
	);
};

export default SavedTemplates;
