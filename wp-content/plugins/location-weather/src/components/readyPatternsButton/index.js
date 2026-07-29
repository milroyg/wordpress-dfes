import { createRoot } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import Library from '../../prebuild-library/Library';
import './editor.scss';

const ReadyPatternsBtn = ( { blockName, label = false } ) => {
	let modalRoot = null;
	// Close modal.
	const removeModal = () => {
		if ( modalRoot ) {
			modalRoot.unmount();
			modalRoot = null;
		}
		const modalNode = document.querySelector(
			'.splw-patterns-builder-modal'
		);
		if ( modalNode ) modalNode.remove();
		document.body.classList.remove( 'splw-patterns-popup-open' );
	};
	// Open modal.
	const onInsertButtonClick = ( e ) => {
		e.preventDefault();

		// If modal already exists, do nothing.
		if ( document.querySelector( '.splw-patterns-builder-modal' ) ) return;

		const node = document.createElement( 'div' );
		node.className =
			'splw-patterns-builder-modal splw-patterns-blocks-layouts';
		document.body.appendChild( node );

		modalRoot = createRoot( node );
		modalRoot.render(
			<Library
				isShow={ true }
				onClose={ removeModal }
				currentBlockName={ blockName }
			/>
		);
		document.body.classList.add( 'splw-patterns-popup-open' );

		// Close when clicking outside.
		setTimeout( () => {
			node.addEventListener( 'click', ( e ) => {
				if ( e.target === node ) removeModal();
			} );
		}, 0 );
	};
	return (
		<button
			type="button"
			data-block={ blockName }
			onClick={ onInsertButtonClick }
			className="spl-weather-patterns-btn"
		>
			{ label || __( 'Ready Patterns', 'location-weather' ) }
		</button>
	);
};

export default ReadyPatternsBtn;
