import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor';
import './editor.scss';
import { ArrowUpRight } from '../../icons';
import { blockRegisterInfo } from '../../controls';
import ReadyPatternsBtn from '../readyPatternsButton';

const InspectorControl = ( { attributes, setAttributes, Inspector } ) => {
	const { blockName } = attributes;
	// Modify block names that doesn't match the register info keys.
	const blockNames = {
		vertical: 'vertical-card',
		'owm-map': 'map',
	};
	const modifiedBlockName = `sp-location-weather-pro/${
		blockNames[ blockName ] || blockName
	}`;

	// Close modal.
	// const removeModal = () => {
	// 	if ( modalRoot ) {
	// 		modalRoot.unmount();
	// 		modalRoot = null;
	// 	}
	// 	const modalNode = document.querySelector(
	// 		'.splw-patterns-builder-modal'
	// 	);
	// 	if ( modalNode ) modalNode.remove();
	// 	document.body.classList.remove( 'splw-patterns-popup-open' );
	// };
	// // Open modal.
	// const onInsertButtonClick = ( e ) => {
	// 	e.preventDefault();

	// 	// If modal already exists, do nothing.
	// 	if ( document.querySelector( '.splw-patterns-builder-modal' ) ) return;

	// 	const node = document.createElement( 'div' );
	// 	node.className =
	// 		'splw-patterns-builder-modal splw-patterns-blocks-layouts';
	// 	document.body.appendChild( node );

	// 	modalRoot = createRoot( node );
	// 	modalRoot.render(
	// 		<Library
	// 			isShow={ true }
	// 			onClose={ removeModal }
	// 			currentBlockName={ blockName }
	// 		/>
	// 	);
	// 	document.body.classList.add( 'splw-patterns-popup-open' );

	// 	// Close when clicking outside.
	// 	setTimeout( () => {
	// 		node.addEventListener( 'click', ( e ) => {
	// 			if ( e.target === node ) removeModal();
	// 		} );
	// 	}, 0 );
	// };

	const docLink = blockRegisterInfo[ modifiedBlockName ]?.docLink;
	const demoLink = blockRegisterInfo[ modifiedBlockName ]?.demoLink;
	return (
		<InspectorControls>
			<div className="sp-location-weather-tabs-panel">
				<div className="spl-weather-tab-panel-header">
					<div className="spl-weather-block-doc-link">
						<a href={ docLink } target="_blank">
							{ __( 'Documentation ', 'location-weather' ) }{ ' ' }
							<ArrowUpRight />
						</a>
					</div>
					<div className="spl-weather-block-preview-btn-wrapper">
						<ReadyPatternsBtn blockName={ blockName } />
						<a
							href={ demoLink }
							className="spl-weather-block-preview-btn"
							target="_blank"
						>
							{ __( 'Block Preview', 'location-weather' ) }
						</a>
					</div>
				</div>

				{ Inspector && (
					<Inspector
						attributes={ attributes }
						setAttributes={ setAttributes }
					/>
				) }
			</div>
		</InspectorControls>
	);
};

export default InspectorControl;
