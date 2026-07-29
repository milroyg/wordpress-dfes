import { __ } from '@wordpress/i18n';
import { useEffect, useRef } from '@wordpress/element';
import { useBlockProps } from '@wordpress/block-editor';
import { fontFamilyToUrlGenerator, jsonStringify } from '../../controls';
import { InspectorControl } from '../../components';
import Inspector from '../shared/inspector';
import { TabsPreview } from './icons';
import { TogglePanelBodyProvider } from '../../context';
import dynamicCss from './dynamicCss';
import { EditorWrapper } from '../shared/wrapper';
import toggleDefaultValue from './toggleDefaultValue';
import Render from './render';

const TabsBlockEdit = ( { clientId, attributes, setAttributes } ) => {
	const {
		template,
		isPreview,
		customCss,
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
	} = attributes;

	if ( isPreview ) {
		return <TabsPreview />;
	}

	useEffect( () => {
		const shortClientId = clientId.split( '-' )?.pop();
		const uniqueId = `spl-weather-tabs-${ shortClientId }`;
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
	}, [ template ] );

	return (
		<div { ...useBlockProps() }>
			<style>
				{ fontFamilyToUrlGenerator( lwBlockTypography ) }
				{ dynamicCss( attributes, 'editor' ) }
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

export default TabsBlockEdit;
