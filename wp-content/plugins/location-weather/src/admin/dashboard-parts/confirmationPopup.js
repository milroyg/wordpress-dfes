import { Popover } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const ConfirmationPopup = ( {
	onClose,
	onConfirm,
	message = __( 'Are You Sure?', 'location-weather' ),
	confirmLabel = __( 'Yes', 'location-weather' ),
	cancelLabel = __( 'Cancel', 'location-weather' ),
} ) => {
	return (
		<Popover
			className="splw-cache-delete-popup"
			position="middle center"
			onClose={ onClose }
		>
			<span>{ message }</span>

			<div className="splw-cache-delete-buttons">
				<button className="cancel" onClick={ onClose }>
					{ cancelLabel }
				</button>

				<button className="ok" onClick={ onConfirm }>
					{ confirmLabel }
				</button>
			</div>
		</Popover>
	);
};

export default ConfirmationPopup;
