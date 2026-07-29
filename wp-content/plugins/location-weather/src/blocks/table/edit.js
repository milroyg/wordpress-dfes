import { __ } from '@wordpress/i18n';
import { useEffect, useRef } from '@wordpress/element';
import { useBlockProps } from '@wordpress/block-editor';
import { fontFamilyToUrlGenerator, jsonStringify } from '../../controls';
import { InspectorControl } from '../../components';
import { TogglePanelBodyProvider } from '../../context';
import Inspector from '../shared/inspector';
import dynamicCss from './dynamicCss';
import { TablePreview } from './icons';
import Render from './render';
import { EditorWrapper } from '../shared/wrapper';

const TableBlockEdit = ( { clientId, attributes, setAttributes } ) => {
	const {
		template,
		blockName,
		isPreview,
		splwPadding,
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
		return <TablePreview />;
	}

	useEffect( () => {
		const shortClientId = clientId.split( '-' )?.pop();
		const uniqueId = `spl-weather-table-${ shortClientId }`;
		setAttributes( { uniqueId: uniqueId } );
	}, [ clientId ] );

	const padding = template === 'table-two' ? '20' : '0';
	const defaultPadding = {
		...splwPadding,
		device: {
			...splwPadding.device,
			Desktop: {
				top: padding,
				right: padding,
				bottom: padding,
				left: padding,
			},
		},
	};

	const previousTemplateRef = useRef( template );
	useEffect( () => {
		if ( previousTemplateRef.current !== template ) {
			previousTemplateRef.current = template;

			if ( template === 'table-two' ) {
				setAttributes( { splwPadding: defaultPadding } );
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

export default TableBlockEdit;
