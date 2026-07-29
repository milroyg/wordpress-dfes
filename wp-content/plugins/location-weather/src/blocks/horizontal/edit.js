import { __ } from '@wordpress/i18n';
import { useEffect, useRef } from '@wordpress/element';
import { useBlockProps } from '@wordpress/block-editor';
import { fontFamilyToUrlGenerator, jsonStringify } from '../../controls';
import { TogglePanelBodyProvider } from '../../context';
import { InspectorControl } from '../../components';
import dynamicCss from './dynamicCss';
import Inspector from '../shared/inspector';
import { EditorWrapper } from '../shared/wrapper';
import Render from './render';
import { HorizontalPreview } from './icons';

const HorizontalBlockEdit = ( { clientId, attributes, setAttributes } ) => {
	const {
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
		return <HorizontalPreview />;
	}

	useEffect( () => {
		const shortClientId = clientId.split( '-' )?.pop();
		const uniqueId = `spl-weather-horizontal-${ shortClientId }`;
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
		setAttributes( { fontLists: jsonStringify( fontLists ) } );
	}, [ jsonStringify( lwBlockTypography ) ] );

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

export default HorizontalBlockEdit;
