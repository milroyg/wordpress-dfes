import { __ } from '@wordpress/i18n';

export const SavedTemplatesFooter = ( {
	currentPage,
	totalPages,
	totalPostCount,
	children,
} ) => {
	return (
		<div className="splw-saved-template-footer">
			<div className="splw-saved-template-count">
				Page { currentPage } of { totalPages || 1 } &nbsp;
				<span>[ { totalPostCount } Items ]</span>
			</div>
			{ children }
		</div>
	);
};
