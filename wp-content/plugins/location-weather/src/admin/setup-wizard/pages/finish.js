import { __ } from '@wordpress/i18n';
import { useState, useEffect } from '@wordpress/element';
import { CheckboxControl, Spinner } from '@wordpress/components';
import axios from 'axios';
import UserDataInfoModal from '../../dashboard-parts/userDataModal';

const FinishPage = ( { websiteType } ) => {
	const phpToJsBool = {
		0: false,
		1: true,
	};
	const initialConsent =
		splw_admin_settings_localize?.splw_user_consent === 'undefined'
			? true
			: phpToJsBool[ splw_admin_settings_localize?.splw_user_consent ];
	const [ shareData, setShareData ] = useState( initialConsent );

	const [ isFinishing, setIsFinishing ] = useState( false );

	const [ isOpenModal, setOpenModal ] = useState( false );
	const openModal = () => setOpenModal( true );
	const closeModal = () => setOpenModal( false );

	const saveUserData = async () => {
		try {
			setIsFinishing( true );
			const formData = new window.FormData();
			formData.append( 'action', 'splw_get_user_consent' );
			formData.append( 'nonce', splw_admin_settings_localize.nonce );
			formData.append( 'shareData', JSON.stringify( shareData ) );
			formData.append( 'website_type', websiteType );

			const response = await axios.post( ajaxurl, formData );

			if ( response?.data?.success ) {
				window.location.href = `${ splw_admin_settings_localize?.homeUrl }wp-admin/admin.php?page=splw_admin_dashboard`;
			}
		} catch ( error ) {
			toastErrorMsg( __( 'Something Went Wrong', 'location-weather' ) );
			console.error( error );
			return { success: false, error };
		}
	};

	return (
		<div className="splw-setup-finish-page">
			<img
				src={ `${ splw_admin_settings_localize?.pluginUrl }/includes/Admin/framework/assets/images/setup-wizard/congratulations.svg` }
				alt="Congratulations"
			/>
			<h3 className="splw-setup-page-title">
				{ __(
					'All Set to Create Weather Showcase!',
					'location-weather'
				) }
			</h3>
			<p
				className="splw-setup-page-desc"
				style={ { width: '636px', textAlign: 'center' } }
			>
				{ __(
					"You're ready to create your first weather block. Customize layouts, choose your location, and publish weather insights instantly on your site.",
					'location-weather'
				) }
			</p>
			<button
				className="splw-setup-wizard-nav-btn next-btn"
				onClick={ saveUserData }
				disabled={ isFinishing }
				aria-busy={ isFinishing }
			>
				{ isFinishing ? (
					<>
						<Spinner /> { __( 'Finishing…', 'location-weather' ) }
					</>
				) : (
					__( "Finish & Let's Get Started", 'location-weather' )
				) }
			</button>
			<div className="splw-setup-finish-page-banner">
				<img
					src={ `${ splw_admin_settings_localize?.pluginUrl }/includes/Admin/framework/assets/images/setup-wizard/finish-page-banner.png` }
				/>
				<h3 className="splw-setup-page-title">
					{ __(
						'Over 500+ Weather Patterns to Match Every Style and Mood',
						'location-weather'
					) }
				</h3>
				<a
					className="splw-setup-wizard-nav-btn prev-btn"
					href="https://locationweather.io/patterns/"
					target="_blank"
				>
					{ __( 'Explore All Patterns', 'location-weather' ) }
				</a>
			</div>
			<div className="spl-weather-checkbox-component-wrapper">
				<p className="splw-setup-page-desc">
					{ __(
						'Help us improve Location Weather and get useful tips by sharing non-sensitive diagnostic data. See ',
						'location-weather'
					) }
					<span
						className="splw-modal-btn"
						style={ {
							fontWeight: '600',
							textDecoration: 'underline',
						} }
						onClick={ openModal }
					>
						{ __( 'what we collect.', 'location-weather' ) }
					</span>
				</p>
				<CheckboxControl
					checked={ shareData }
					onChange={ () => setShareData( ! shareData ) }
					__nextHasNoMarginBottom
				/>
			</div>
			{ isOpenModal && <UserDataInfoModal closeModal={ closeModal } /> }
		</div>
	);
};

export default FinishPage;
