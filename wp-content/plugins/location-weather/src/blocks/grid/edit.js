import { useEffect, useRef } from '@wordpress/element';
import { useBlockProps } from '@wordpress/block-editor';
import { fontFamilyToUrlGenerator, jsonStringify } from '../../controls';
import { TogglePanelBodyProvider } from '../../context';
import { InspectorControl } from '../../components';
import { GridPreview } from './icons';
import Inspector from '../shared/inspector';
import dynamicCss from './dynamicCss';
import { EditorWrapper } from '../shared/wrapper';
import toggleDefaultValue from './toggleDefaultValue';
import Render from './render';

const WeatherGridEdit = ( { clientId, attributes, setAttributes } ) => {
	const {
		isPreview,
		template,
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
		return <GridPreview />;
	}

	useEffect( () => {
		const shortClientId = clientId.split( '-' )?.pop();
		const uniqueId = `spl-weather-grid-${ shortClientId }`;
		setAttributes( { uniqueId: uniqueId } );
	}, [ clientId ] );

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

export default WeatherGridEdit;
