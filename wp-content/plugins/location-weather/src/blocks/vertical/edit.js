import { __ } from '@wordpress/i18n';
import { useEffect, useRef } from '@wordpress/element';
import { useBlockProps } from '@wordpress/block-editor';
import { fontFamilyToUrlGenerator, jsonStringify } from '../../controls';
import { InspectorControl } from '../../components';
import { TogglePanelBodyProvider } from '../../context';
import Inspector from '../shared/inspector';
import dynamicCss from './dynamicCss';
import { EditorWrapper } from '../shared/wrapper';
import toggleDefaultValue from './toggleDefaultValue';
import { VerticalPreview } from './icons';
import Render from './render';

const VerticalBlockEdit = ( { clientId, attributes, setAttributes } ) => {
	const {
		isPreview,
		template,
		temperatureScaleTypography,
		weatherDescTypography,
		dateTimeTypography,
		temperatureUnitTypography,
		locationNameTypography,
		additionalDataLabelTypography,
		additionalDataValueTypography,
		weatherComportDataTypography,
		forecastLabelTypography,
		forecastDataTypography,
		weatherAttributionTypography,
		detailedWeatherAndUpdateTypography,
		templateTypography,
		active_additional_data_layout,
		customCss,
	} = attributes;

	if ( isPreview ) {
		return <VerticalPreview />;
	}

	//  change default value.
	const previousTemplateRef = useRef( template );

	useEffect( () => {
		if ( previousTemplateRef.current !== template ) {
			previousTemplateRef.current = template;

			const defaultValues = toggleDefaultValue( attributes );
			if ( defaultValues ) {
				setAttributes( defaultValues );
			}
		}
	}, [ template, active_additional_data_layout ] );

	useEffect( () => {
		const shortClientId = clientId.split( '-' )?.pop();
		const uniqueId = `spl-weather-vertical-${ shortClientId }`;
		setAttributes( { uniqueId: uniqueId } );
	}, [ clientId ] );

	// block font family.
	const lwBlockTypography = [
		temperatureScaleTypography,
		weatherDescTypography,
		dateTimeTypography,
		temperatureUnitTypography,
		locationNameTypography,
		additionalDataLabelTypography,
		additionalDataValueTypography,
		weatherComportDataTypography,
		forecastLabelTypography,
		forecastDataTypography,
		weatherAttributionTypography,
		detailedWeatherAndUpdateTypography,
		templateTypography,
	];

	useEffect( () => {
		const fontLists = fontFamilyToUrlGenerator(
			lwBlockTypography,
			'frontend'
		);
		setAttributes( { fontLists: fontLists } );
	}, [ jsonStringify( lwBlockTypography ) ] );

	const cssString = dynamicCss( attributes, 'frontend' );

	return (
		<div { ...useBlockProps() }>
			<style>
				{ fontFamilyToUrlGenerator( lwBlockTypography ) }
				{ cssString }
				{ customCss + ';' }
			</style>
			<TogglePanelBodyProvider>
				<InspectorControl
					attributes={ attributes }
					setAttributes={ setAttributes }
					Inspector={ Inspector }
				/>
				<EditorWrapper
					attributes={ attributes }
					setAttributes={ setAttributes }
				>
					<Render />
				</EditorWrapper>
			</TogglePanelBodyProvider>
		</div>
	);
};

export default VerticalBlockEdit;
