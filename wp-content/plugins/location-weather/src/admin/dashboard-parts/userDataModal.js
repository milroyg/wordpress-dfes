import { Modal } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { Arrow } from '../icons';

const UserDataInfoModal = ( { closeModal } ) => {
	return (
		<Modal
			title={ __( 'What We Collect?', 'location-weather' ) }
			onRequestClose={ closeModal }
			className="splw-setup-page-modal"
		>
			<hr />
			<p className="modal-description">
				{ __(
					'We collect only non-sensitive diagnostic data and basic plugin usage information. This may include:',
					'location-weather'
				) }
			</p>
			<ul>
				<li className="modal-description">
					{ __( 'WordPress & PHP version', 'location-weather' ) }
				</li>
				<li className="modal-description">
					{ __( 'Active theme and plugins', 'location-weather' ) }
				</li>
				<li className="modal-description">
					{ __( 'General system details', 'location-weather' ) }
				</li>
				<li className="modal-description">
					{ __(
						'Email address only for sending helpful updates or optional offers.',
						'location-weather'
					) }
				</li>
			</ul>
			<p className="modal-description">
				{ __(
					'This information helps us improve performance, fix issues faster, and ensure Location Weather stays compatible with the popular plugins and themes.',
					'location-weather'
				) }
			</p>
			<p className="modal-description">
				{ __( 'We ', 'location-weather' ) }
				<b>
					{ __(
						'do not collect sensitive personal data, ',
						'location-weather'
					) }
				</b>
				{ __( 'and ', 'location-weather' ) }
				<b>{ __( 'never send spam ', 'location-weather' ) }</b>
				{ __( 'promise.', 'location-weather' ) }
			</p>
			<p className="modal-description">
				<b>{ __( 'Your privacy comes first.', 'location-weather' ) }</b>
			</p>
			<a
				href="https://locationweather.io/information-we-collect/"
				target="_blank"
				className="modal-description"
			>
				{ __( 'Learn More ', 'location-weather' ) }
				<Arrow />
			</a>
		</Modal>
	);
};

export default UserDataInfoModal;
