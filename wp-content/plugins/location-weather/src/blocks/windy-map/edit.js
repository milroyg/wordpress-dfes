import { __ } from '@wordpress/i18n';
import { useEffect } from '@wordpress/element';
import { useBlockProps } from '@wordpress/block-editor';
import { TogglePanelBodyProvider } from '../../context';
import { InspectorControl } from '../../components';
import { WindyMapPreview } from './icons';
import Inspector from '../shared/inspector';
import { jsonStringify } from '../../controls';
import WindyMap from '../shared/templates/maps/windyMap';
import dynamicCss from './dynamicCss';

const WindyMapEdit = ( { clientId, attributes, setAttributes } ) => {
	const { uniqueId, blockName, isPreview } = attributes;

	if ( isPreview ) {
		return <WindyMapPreview />;
	}

	useEffect( () => {
		const shortClientId = clientId.split( '-' )?.pop();
		const uniqueId = `spl-weather-windy-map-${ shortClientId }`;
		setAttributes( { uniqueId: uniqueId } );
	}, [ clientId ] );

	return (
		<div { ...useBlockProps() }>
			<style>{ dynamicCss( attributes, 'editor' ) }</style>
			<TogglePanelBodyProvider>
				<InspectorControl
					attributes={ attributes }
					setAttributes={ setAttributes }
					Inspector={ Inspector }
				/>
				<div
					id={ uniqueId }
					className={ `spl-weather-${ blockName }-card sp-location-weather-block-wrapper` }
				>
					<WindyMap attributes={ attributes } />
				</div>
			</TogglePanelBodyProvider>
		</div>
	);
};

export default WindyMapEdit;
