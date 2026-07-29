import { __ } from '@wordpress/i18n';
import { CodeEditor, Toggle, InputControl } from '../../components';

export const VisibilityTab = ( { attributes, setAttributes } ) => {
	const { splwHideOnDesktop, splwHideOnTablet, splwHideOnMobile } =
		attributes;
	return (
		<>
			<Toggle
				label={ __( 'Hide on Desktop', 'location-weather' ) }
				attributes={ splwHideOnDesktop }
				setAttributes={ setAttributes }
				attributesKey={ 'splwHideOnDesktop' }
			/>
			<Toggle
				label={ __( 'Hide on Tablet', 'location-weather' ) }
				attributes={ splwHideOnTablet }
				setAttributes={ setAttributes }
				attributesKey={ 'splwHideOnTablet' }
			/>
			<Toggle
				label={ __( 'Hide on Mobile', 'location-weather' ) }
				attributes={ splwHideOnMobile }
				setAttributes={ setAttributes }
				attributesKey={ 'splwHideOnMobile' }
			/>
		</>
	);
};

export const AdvancedTab = ( { attributes, setAttributes } ) => {
	const { customClassName, customCss, showPreloader } = attributes;

	const customClassSet = ( className ) => {
		setAttributes( { customClassName: className } );
	};

	return (
		<div className="spl-weather-component-mb">
			<Toggle
				label={ __( 'Preloader', 'location-weather' ) }
				attributes={ showPreloader }
				setAttributes={ setAttributes }
				attributesKey={ 'showPreloader' }
			/>
			<InputControl
				label={ __( 'Additional CSS Class(es)', 'location-weather' ) }
				attributes={ customClassName }
				onChange={ ( val ) => customClassSet( val ) }
			/>
			<CodeEditor
				label={ __( 'Custom CSS', 'location-weather' ) }
				attributes={ customCss }
				attributesKey="customCss"
				setAttributes={ setAttributes }
			/>
		</div>
	);
};
