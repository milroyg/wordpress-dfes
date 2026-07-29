import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { CodeEditor, InputControl, SelectField } from '../../../components';
import { KeyIcon } from '../../icons';
import { ArrowUpRight } from '../../../icons';
import { CheckboxControl } from '@wordpress/components';
import Toggle from 'react-toggle';
import ConfirmationPopup from '../../dashboard-parts/confirmationPopup';
import { deleteWeatherTransients, saveSettingOptions } from '../../functions';
import SaveAndReset from '../../dashboard-parts/saveAndReset';
import InfoText from '../../dashboard-parts/infoText';
import UserDataInfoModal from '../../dashboard-parts/userDataModal';

const jsToPhpBool = ( val ) => {
	return val ? '1' : '0';
};

const phpToJsBool = {
	0: false,
	1: true,
};

export const WeatherAPIKey = ( {
	settingsOptions,
	setSettingsOptions,
	isTourGuide = false,
	nextBtnRef,
	setIsEmptyApiFields,
} ) => {
	const [ initialValues, setInitialValues ] = useState( {
		initialOpenWeatherKey: settingsOptions[ 'open-api-key' ] || '',
		initialWeatherApiKey: settingsOptions[ 'weather-api-key' ] || '',
		initialShortCodeApiType:
			settingsOptions[ 'lw_api_source_type' ] || 'openweather_api',
	} );

	const [ openWeatherKey, setOpenWeatherKey ] = useState(
		initialValues.initialOpenWeatherKey
	);
	const [ weatherAPIKey, setWeatherAPIKey ] = useState(
		initialValues.initialWeatherApiKey
	);
	const [ shortcodeApiType, setShortcodeApiType ] = useState(
		initialValues.initialShortCodeApiType
	);

	const [ showConfPopup, setShowConfPopup ] = useState( false );
	const [ isSaving, setIsSaving ] = useState( false );

	const isChanged =
		initialValues.initialOpenWeatherKey !== openWeatherKey ||
		initialValues.initialWeatherApiKey !== weatherAPIKey ||
		initialValues.initialShortCodeApiType !== shortcodeApiType;

	const saveAPIKeys = ( actionType = 'save' ) => {
		if ( actionType === 'save' && ! isChanged ) {
			return;
		}
		let newOpenKey = openWeatherKey;
		let newWeatherKey = weatherAPIKey;
		let newShortcodeType = shortcodeApiType;

		if ( actionType === 'reset' ) {
			newOpenKey = '';
			newWeatherKey = '';
			newShortcodeType = 'openweather_api';

			setOpenWeatherKey( newOpenKey );
			setWeatherAPIKey( newWeatherKey );
			setShortcodeApiType( newShortcodeType );
			setShowConfPopup( false );
		} else {
			setIsSaving( true );
		}

		const updatedSettings = {
			'open-api-key': newOpenKey,
			'weather-api-key': newWeatherKey,
			lw_api_source_type: newShortcodeType,
		};

		saveSettingOptions(
			updatedSettings,
			actionType,
			setSettingsOptions
		).then( () => {
			setIsSaving( false );
			setInitialValues( {
				initialOpenWeatherKey: newOpenKey,
				initialWeatherApiKey: newWeatherKey,
				initialShortCodeApiType: newShortcodeType,
			} );
		} );
	};

	useEffect( () => {
		if ( nextBtnRef?.current ) {
			const handler = () => {
				if ( ! openWeatherKey && ! weatherAPIKey ) {
					setIsEmptyApiFields( true );
				} else {
					setIsEmptyApiFields( false );
					saveAPIKeys( 'save' );
				}
			};
			nextBtnRef.current.addEventListener( 'click', handler );
			return () => {
				nextBtnRef.current?.removeEventListener( 'click', handler );
			};
		}
	}, [ nextBtnRef, openWeatherKey, weatherAPIKey ] );

	return (
		<div className="splw-settings-weather-api">
			<span className='splw-weather-api-notice'>To show weather data smoothly, please set at least one Weather API key below.</span>
			<InputControl
				label={
					<>
						<img
							src={ `${ splw_admin_settings_localize?.pluginUrl }/includes/Admin/framework/assets/images/openweather-logo.png` }
						/>
						<span>OpenWeather API Key</span>
					</>
				}
				placeholder={ __( 'Place Your API Key ', 'location-weather' ) }
				attributes={ openWeatherKey }
				help={
					<>
						<span className="key-icon">
							<KeyIcon />
						</span>
						<span className="splw-help-txt">
							<a
								href="https://home.openweathermap.org/api_keys"
								target="_blank"
							>
								{ __(
									'Get your API key!',
									'location-weather'
								) }
							</a>{ ' ' }
							{ __(
								'A newly created API key takes approximately 15 minutes to activate and display weather data.',
								'location-weather'
							) }
						</span>
					</>
				}
				onChange={ ( val ) => setOpenWeatherKey( val ) }
			/>
			<hr />
			<InputControl
				label={
					<>
						<img
							src={ `${ splw_admin_settings_localize?.pluginUrl }/includes/Admin/framework/assets/images/weatherApi-logo.png` }
						/>
						<span>WeatherAPI Key</span>
					</>
				}
				placeholder={ __( 'Place Your API Key ', 'location-weather' ) }
				attributes={ weatherAPIKey }
				help={
					<>
						<span className="key-icon">
							<KeyIcon />
						</span>
						<span className="splw-help-txt">
							<a
								href="https://www.weatherapi.com/my/"
								target="_blank"
							>
								{ __(
									'Get your WeatherAPI key!',
									'location-weather'
								) }
							</a>{ ' ' }
							{ __(
								'Activate and display weather data instantly.',
								'location-weather'
							) }
						</span>
					</>
				}
				onChange={ ( val ) => setWeatherAPIKey( val ) }
			/>
			<hr />
			<div
				className="splw-settings-option api-source"
				style={ { marginTop: '16px' } }
			>
				<span className="spl-weather-component-title">
					{ __(
						'API Source Type for Classic Shortcode',
						'location-weather'
					) }
					<InfoText
						text={ __(
							'Select your preferred API source based on your requirements to ensure accurate and up-to-date weather information in your classic shortcodes.',
							'location-weather'
						) }
					/>
				</span>
				<SelectField
					attributes={ shortcodeApiType }
					onChange={ ( val ) => setShortcodeApiType( val ) }
					items={ [
						{ label: 'OpenWeather', value: 'openweather_api' },
						{
							label: 'WeatherAPI',
							value: 'weather_api',
						},
					] }
				/>
			</div>

			{ ! isTourGuide && (
				<SaveAndReset
					onSave={ () => saveAPIKeys( 'save' ) }
					onReset={ () => setShowConfPopup( true ) }
					isChanged={ isChanged }
					isSaving={ isSaving }
				/>
			) }

			{ showConfPopup && (
				<ConfirmationPopup
					onClose={ () => setShowConfPopup( false ) }
					onConfirm={ () => saveAPIKeys( 'reset' ) }
					message={ __(
						'Are you sure to reset?',
						'location-weather'
					) }
				/>
			) }
			{ ! isTourGuide && (
				<div className="splw-settings-api-guideline">
					{ __(
						'See the full Weather API Key Integration',
						'location-weather'
					) }
					<a
						href="https://locationweather.io/api-integration-guidelines/"
						target="_blank"
					>
						{ __( 'Guidelines', 'location-weather' ) }
						<ArrowUpRight />
					</a>
				</div>
			) }
		</div>
	);
};

export const AdvancedControls = ( { settingsOptions, setSettingsOptions } ) => {
	const [ initialValues, setInitialValues ] = useState( {
		cleanDataOnDelete:
			phpToJsBool[ settingsOptions?.splw_delete_on_remove ] || false,
		skipCache: phpToJsBool[ settingsOptions?.splw_skipping_cache ] || false,
		cache: phpToJsBool[ settingsOptions?.splw_enable_cache ] || false,
		cacheTime: settingsOptions?.splw_cache_time?.all || 15,
		shareData:
			phpToJsBool[ splw_admin_settings_localize?.splw_user_consent ] ||
			false,
		editorPreference:
			splw_admin_settings_localize?.splw_editor_preference || '',
	} );

	const [ cleanDataOnDelete, setCleanDataOnDelete ] = useState(
		initialValues.cleanDataOnDelete
	);
	const [ skipCache, setSkipCache ] = useState( initialValues.skipCache );
	const [ cache, setCache ] = useState( initialValues.cache );
	const [ cacheTime, setCacheTime ] = useState( initialValues.cacheTime );
	const [ shareData, setShareData ] = useState( initialValues.shareData );
	const [ editorPreference, setEditorPreference ] = useState(
		initialValues.editorPreference
	);
	const [ cacheDeletePopup, setCacheDeletePopup ] = useState( false );
	const [ showConfPopup, setShowConfPopup ] = useState( false );

	// User data info modal.
	const [ isOpenModal, setOpenModal ] = useState( false );
	const openModal = () => setOpenModal( true );
	const closeModal = () => setOpenModal( false );

	const [ isSaving, setIsSaving ] = useState( false );

	const isChanged =
		initialValues.cleanDataOnDelete !== cleanDataOnDelete ||
		initialValues.skipCache !== skipCache ||
		initialValues.cache !== cache ||
		initialValues.cacheTime !== cacheTime ||
		initialValues.shareData !== shareData ||
		initialValues.editorPreference !== editorPreference;

	const saveAdvancedControls = ( actionType = 'save' ) => {
		let newCleanOnDelete = cleanDataOnDelete;
		let newSkipCache = skipCache;
		let newCache = cache;
		let newCacheTime = cacheTime;
		let newShareData = shareData;
		let newEditorPreference = editorPreference;

		if ( actionType === 'reset' ) {
			newCleanOnDelete = false;
			newSkipCache = false;
			newCache = false;
			newCacheTime = 15;
			newShareData = true;
			newEditorPreference = '';
			setCleanDataOnDelete( false );
			setSkipCache( false );
			setCache( false );
			setCacheTime( 15 );
			setShowConfPopup( false );
			setShareData( true );
			setEditorPreference( '' );
		} else {
			setIsSaving( true );
		}

		const updatedSettings = {
			splw_delete_on_remove: jsToPhpBool( newCleanOnDelete ),
			splw_skipping_cache: jsToPhpBool( newSkipCache ),
			splw_enable_cache: jsToPhpBool( newCache ),
			splw_cache_time: { all: `${ newCacheTime }` },
		};

		saveSettingOptions(
			updatedSettings,
			actionType,
			setSettingsOptions,
			newShareData,
			newEditorPreference
		).then( () => {
			setInitialValues( {
				cleanDataOnDelete: newCleanOnDelete,
				skipCache: newSkipCache,
				cache: newCache,
				cacheTime: newCacheTime,
				shareData: newShareData,
				editorPreference: newEditorPreference,
			} );
			setIsSaving( false );
		} );
	};

	const purgeCache = () => {
		deleteWeatherTransients().then( () => {
			setCacheDeletePopup( false );
		} );
	};

	return (
		<div className="splw-settings-advanced-controls">
			<div className="splw-settings-checkbox splw-settings-option">
				<span className="spl-weather-component-title">
					{ __( 'Clean-up Data on Deletion', 'location-weather' ) }
					<InfoText
						text={ __(
							'Check this box if you would like location weather to completely clean-up all of its data when the plugin is deleted.',
							'location-weather'
						) }
					/>
				</span>
				<CheckboxControl
					checked={ cleanDataOnDelete }
					onChange={ () =>
						setCleanDataOnDelete( ! cleanDataOnDelete )
					}
					__nextHasNoMarginBottom
				/>
			</div>
			<div className="splw-settings-toggle splw-settings-option">
				<span className="spl-weather-component-title">
					{ __(
						'Skip Cache for Weather Update',
						'location-weather'
					) }
					<InfoText
						text={ __(
							'By enabling this option, you can bypass caching mechanisms in certain plugins or themes, ensuring accurate and timely weather updates. Use this if you encounter caching-related issues.',
							'location-weather'
						) }
					/>
				</span>
				<div className="spl-weather-blocks-settings-toggle-btn">
					<Toggle
						checked={ skipCache }
						icons={ false }
						onChange={ () => setSkipCache( ! skipCache ) }
					/>
				</div>
			</div>
			<div className="splw-settings-toggle splw-settings-option">
				<span className="spl-weather-component-title">
					{ __( 'Cache', 'location-weather' ) }
					<InfoText
						text={ __(
							'Set the duration for storing weather data, balancing freshness and server load. Shorter times provide more real-time data, while longer times reduce server requests.',
							'location-weather'
						) }
					/>
				</span>
				<div className="spl-weather-blocks-settings-toggle-btn">
					<Toggle
						checked={ cache }
						icons={ false }
						onChange={ () => setCache( ! cache ) }
					/>
				</div>
			</div>
			{ cache && (
				<div className="splw-settings-option">
					<span className="spl-weather-component-title">
						{ __( 'Cache Time', 'location-weather' ) }
					</span>
					<div className="splw-settings-cache-time splw-settings-option">
						<InputControl
							attributes={ cacheTime }
							onChange={ ( val ) => setCacheTime( val ) }
							type={ 'number' }
							__next40pxDefaultSize
						/>
						<span className="mins">Mins</span>
					</div>
				</div>
			) }
			<div className="splw-settings-toggle splw-settings-option">
				<span className="spl-weather-component-title">
					{ __( 'Purge Cache', 'location-weather' ) }
				</span>
				<button
					className="splw-setting-purge-cache"
					onClick={ () => setCacheDeletePopup( true ) }
				>
					{ __( 'Delete' ) }
				</button>
				{ cacheDeletePopup && (
					<ConfirmationPopup
						onClose={ () => setCacheDeletePopup( false ) }
						onConfirm={ purgeCache }
						message={ __(
							'Are you sure to clean cache?',
							'location-weather'
						) }
					/>
				) }
			</div>
			<div className="splw-settings-option">
				<span className="spl-weather-component-title">
					{ __( 'Default Editor', 'location-weather' ) }
					<InfoText
						text={ __(
							'Choose which editor opens when you click "Add New Weather". Pick "Ask each time" to keep the welcome popup.',
							'location-weather'
						) }
					/>
				</span>
				<SelectField
					attributes={ editorPreference }
					onChange={ ( val ) => setEditorPreference( val ) }
					items={ [
						{
							label: __( 'Ask each time', 'location-weather' ),
							value: '',
						},
						{
							label: __( 'Block Editor', 'location-weather' ),
							value: 'block_editor',
						},
						{
							label: __(
								'Classic Shortcode',
								'location-weather'
							),
							value: 'classic_shortcode',
						},
					] }
					styles={ { width: '300px' } }
				/>
			</div>
			<hr />
			<div className="splw-settings-toggle splw-settings-option">
				<span className="spl-weather-component-title">
					{ __(
						'Contribute to Location Weather',
						'location-weather'
					) }
				</span>
				<div className="spl-weather-blocks-settings-toggle-btn">
					<Toggle
						checked={ shareData }
						icons={ false }
						onChange={ () => setShareData( ! shareData ) }
					/>
				</div>
			</div>
			<span className="splw-help-text contribute-text">
				{ __(
					'We collect non-sensitive data to fix bugs faster, make smarter decisions, and build features that truly matter to you. See ',
					'location-weather'
				) }
				<span className="splw-settings-modal-btn" onClick={ openModal }>
					{ __( 'what we collect.', 'location-weather' ) }
				</span>
			</span>
			<SaveAndReset
				onSave={ () => saveAdvancedControls( 'save' ) }
				onReset={ () => setShowConfPopup( true ) }
				isChanged={ isChanged }
				isSaving={ isSaving }
			/>
			{ showConfPopup && (
				<ConfirmationPopup
					onClose={ () => setShowConfPopup( false ) }
					onConfirm={ () => saveAdvancedControls( 'reset' ) }
					message={ __(
						'Are you sure to reset?',
						'location-weather'
					) }
				/>
			) }
			{ isOpenModal && <UserDataInfoModal closeModal={ closeModal } /> }
		</div>
	);
};

export const AdditionalCodes = ( { settingsOptions, setSettingsOptions } ) => {
	const [ initialCodes, setInitialCodes ] = useState( {
		css: settingsOptions?.splw_custom_css,
		js: settingsOptions?.splw_custom_js,
	} );
	const [ showConfPopup, setShowConfPopup ] = useState( false );
	const [ customCss, setCustomCss ] = useState( initialCodes.css );
	const [ customJs, setCustomJs ] = useState( initialCodes.js );

	const [ isSaving, setIsSaving ] = useState( false );

	const isChanged =
		initialCodes.css !== customCss || initialCodes.js !== customJs;

	const saveAdditionalCodes = () => {
		const updatedSettings = {
			splw_custom_css: customCss,
			splw_custom_js: customJs,
		};
		setIsSaving( true );

		saveSettingOptions( updatedSettings, 'save', setSettingsOptions ).then(
			() => {
				setIsSaving( false );
				setInitialCodes( {
					css: customCss,
					js: customJs,
				} );
			}
		);
	};

	const resetAdditionalCodes = () => {
		const updatedSettings = {
			splw_custom_css: '',
			splw_custom_js: '',
		};

		saveSettingOptions( updatedSettings, 'reset', setSettingsOptions ).then(
			() => {
				setInitialCodes( {
					css: '',
					js: '',
				} );
				setCustomCss( '' );
				setCustomJs( '' );
				setShowConfPopup( false );
			}
		);
	};

	return (
		<div className="splw-settings-additional-codes">
			<CodeEditor
				label={ __( 'Custom CSS', 'location-weather' ) }
				attributes={ customCss }
				onChange={ ( val ) => setCustomCss( val ) }
				height="200px"
			/>
			<CodeEditor
				label={ __( 'Custom JS', 'location-weather' ) }
				attributes={ customJs }
				onChange={ ( val ) => setCustomJs( val ) }
				defaultLanguage="javascript"
				height="200px"
			/>
			<SaveAndReset
				onSave={ saveAdditionalCodes }
				onReset={ () => setShowConfPopup( true ) }
				isChanged={ isChanged }
				isSaving={ isSaving }
			/>
			{ showConfPopup && (
				<ConfirmationPopup
					onClose={ () => setShowConfPopup( false ) }
					onConfirm={ resetAdditionalCodes }
					message={ __(
						'Are you sure to reset?',
						'location-weather'
					) }
				/>
			) }
		</div>
	);
};
