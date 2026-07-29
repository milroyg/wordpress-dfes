import { __ } from '@wordpress/i18n';
import { useEffect, useRef } from '@wordpress/element';
import { useBlockProps } from '@wordpress/block-editor';
import { fontFamilyToUrlGenerator, jsonStringify } from '../../controls';
import { ApiNotice, InspectorControl } from '../../components';
import { TogglePanelBodyProvider } from '../../context';
import Inspector from '../shared/inspector';
import Render from './render';
import { EditorWrapper } from '../shared/wrapper';
import dynamicCss from './dynamicCss';
import { AqiMinimalPreview } from './icons';

const AqiMinimalEdit = ( { clientId, attributes, setAttributes } ) => {
	const {
		isPreview,
		customCss,
		aqiSummaryLabelTypography,
		aqiSummaryDescTypography,
		pollutantConditionLabelTypography,
		pollutantValueTypography,
		dateTimeTypography,
		locationNameTypography,
		weatherAttributionTypography,
		detailedWeatherAndUpdateTypography,
		templateTypography,
	} = attributes;

	if ( isPreview ) {
		return <AqiMinimalPreview />;
	}

	useEffect( () => {
		const uniqueId = `spl-weather-aqi-card${ clientId }`;
		setAttributes( { uniqueId: uniqueId } );
	}, [ clientId ] );

	const lwBlockTypography = [
		aqiSummaryLabelTypography,
		aqiSummaryDescTypography,
		pollutantConditionLabelTypography,
		pollutantValueTypography,
		dateTimeTypography,
		locationNameTypography,
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

export default AqiMinimalEdit;
