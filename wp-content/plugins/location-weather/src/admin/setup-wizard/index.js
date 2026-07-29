import { useRef, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { LeftArrow, RightArrow, RightArrowLong, TickIcon } from '../icons';
import WelcomePage from './pages/welcome';
import BlocksSetup from './pages/setup';
import classNames from 'classnames';
import ApiIntegrations from './pages/apiInitegration';
import { Toaster } from 'react-hot-toast';
import FinishPage from './pages/finish';

const splwSiteType = splw_admin_settings_localize?.sp_ua_site_type;

const SetupWizard = () => {
	const footer = document.querySelector( '#wpfooter' );
	if ( footer ) {
		footer.style.display = 'none';
	}
	const nextBtnRef = useRef( null );
	const [ stepNumber, setStepNumber ] = useState( 0 );
	const [ websiteType, setWebsiteType ] = useState( splwSiteType );
	const [ isExiting, setIsExiting ] = useState( false );
	const [ errorMessage, setErrorMessage ] = useState( '' );
	const [ isEmptyApiFields, setIsEmptyApiFields ] = useState( false );

	const [ direction, setDirection ] = useState( 'left' );
	const [ animationClass, setAnimationClass ] = useState( '' );
	const [ settingsOptions, setSettingsOptions ] = useState(
		splw_admin_settings_localize?.settings
	);

	let setupSteps = [ 'Welcome', 'Setup', 'API Integration', 'Finish' ];

	const onAnimationEnd = () => {
		if ( isExiting ) {
			if ( direction === 'left' ) {
				setStepNumber( ( prev ) => prev + 1 );
			} else {
				setStepNumber( ( prev ) => prev - 1 );
			}
			setAnimationClass( 'is-entering' );
			setIsExiting( false );
		}
	};

	const nextStep = () => {
		setDirection( 'left' );
		setIsExiting( true );
		setAnimationClass( 'is-exiting' );
	};

	const handleNext = () => {
		if ( stepNumber === 1 && websiteType === '' ) {
			setErrorMessage( true );
			return;
		}
		if (
			setupSteps[ stepNumber ] === 'API Integration' &&
			isEmptyApiFields
		) {
			setErrorMessage( true );
			return;
		}
		setErrorMessage( false );
		nextStep();
	};

	const handlePrev = () => {
		setDirection( 'right' );
		setIsExiting( true );
		setAnimationClass( 'is-exiting' );
	};

	return (
		<div className="sp-location-weather-block-setting">
			<div className="splw-setup-wizard-wrapper">
				<div className={ `splw-setup-wizard-content` }>
					<div className="splw-setup-steps">
						{ setupSteps?.map( ( step, index ) => (
							<div className="splw-setup-step" key={ index }>
								<span
									className={ classNames(
										'splw-setup-step-number',
										{
											active: index === stepNumber,
											previous: index < stepNumber,
										}
									) }
								>
									{ index < stepNumber ? (
										<TickIcon />
									) : (
										'0' + ( index + 1 )
									) }
								</span>
								<span className="splw-setup-step-title">
									{ step }
								</span>
								{ index !== setupSteps.length - 1 && (
									<RightArrowLong />
								) }
							</div>
						) ) }
					</div>
					<div
						className={ `splw-setup-step-page ${ animationClass }` }
						onAnimationEnd={ onAnimationEnd }
					>
						{ setupSteps[ stepNumber ] === 'Welcome' && (
							<WelcomePage />
						) }
						{ setupSteps[ stepNumber ] === 'Setup' && (
							<BlocksSetup
								websiteType={ websiteType }
								setWebsiteType={ setWebsiteType }
								errorMessage={ errorMessage }
							/>
						) }
						{ setupSteps[ stepNumber ] === 'API Integration' && (
							<ApiIntegrations
								settingsOptions={ settingsOptions }
								setSettingsOptions={ setSettingsOptions }
								handleNext={ nextStep }
								nextBtnRef={ nextBtnRef }
								setIsEmptyApiFields={ setIsEmptyApiFields }
								errorMessage={ errorMessage }
							/>
						) }
						{ setupSteps[ stepNumber ] === 'Finish' && (
							<FinishPage websiteType={ websiteType } />
						) }
					</div>
					<div className="splw-setup-wizard-btn-wrapper">
						{ stepNumber !== 0 &&
							stepNumber !== setupSteps.length - 1 && (
								<button
									className="splw-setup-wizard-nav-btn prev-btn"
									onClick={ handlePrev }
								>
									<LeftArrow />
									{ __( 'Previous', 'location-weather' ) }
								</button>
							) }

						{ stepNumber === 0 && (
							<a
								className="splw-setup-wizard-nav-btn prev-btn"
								href={ `${ splw_admin_settings_localize?.homeUrl }wp-admin/admin.php?page=splw_admin_dashboard` }
							>
								{ 'Skip it' }
							</a>
						) }
						{ stepNumber !== setupSteps.length - 1 && (
							<button
								className="splw-setup-wizard-nav-btn next-btn"
								onClick={ handleNext }
								ref={ nextBtnRef }
							>
								{ __( 'Next Step', 'location-weather' ) }
								<RightArrow />
							</button>
						) }
					</div>
				</div>
				<img
					className="setup-wizard-bg"
					src={ `${ splw_admin_settings_localize?.pluginUrl }/includes/Admin/framework/assets/images/setup-wizard/setup-wizard-bg.svg` }
				/>
				<Toaster
					position="top-right"
					toastOptions={ {
						style: {
							padding: '16px 24px',
							fontSize: '18px',
							borderRadius: '10px',
							maxWidth: '400px',
						},
					} }
				/>
			</div>
		</div>
	);
};

export default SetupWizard;
