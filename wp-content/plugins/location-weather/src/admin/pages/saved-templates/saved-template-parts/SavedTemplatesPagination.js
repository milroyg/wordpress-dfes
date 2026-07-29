import { LeftArrow, RightArrow } from '../../../icons';
import { __ } from '@wordpress/i18n';

export const SavedTemplatesPagination = ( {
	currentPage,
	pages,
	onPageChange,
} ) => {
	return (
		<div className="splw-saved-template-pagination">
			{ pages?.length > 1 && (
				<>
					<button
						className={ `splw-saved-template-pagination-btn sp-btn-prev ${
							currentPage === 1 ? 'btn-disabled' : ''
						}` }
						onClick={ () =>
							onPageChange(
								currentPage !== 1 ? currentPage - 1 : 1
							)
						}
					>
						<LeftArrow />
					</button>
					{ pages.map( ( item, i ) => (
						<button
							key={ i }
							className={ `splw-saved-template-pagination-btn ${
								currentPage === item
									? 'btn-active'
									: 'sp-btn-numb'
							}` }
							onClick={ ( e ) =>
								onPageChange( Number( e.target?.value ) )
							}
							value={ item }
						>
							{ item }
						</button>
					) ) }
					<button
						className={ `splw-saved-template-pagination-btn sp-btn-next ${
							currentPage === pages?.length ? 'btn-disabled' : ''
						}` }
						onClick={ () =>
							onPageChange(
								currentPage !== pages?.length
									? currentPage + 1
									: pages?.length
							)
						}
					>
						<RightArrow />
					</button>
				</>
			) }
		</div>
	);
};
