import { memo } from '@wordpress/element';
import { inArray } from '../../../../controls';
import WindyMap from './windyMap';
import { useTogglePanelBody } from '../../../../context';

const RenderMap = ( { attributes } ) => {
	const { togglePanelBody } = useTogglePanelBody();
	const { displayWeatherMap, blockName } = attributes;

	return (
		<>
			{ displayWeatherMap && (
				<div
					className={ `sp-weather-card-map-renderer ${
						inArray( [ 'tabs', 'grid' ], blockName )
							? ''
							: 'sp-w-50'
					}` }
					onClick={ () => togglePanelBody( 'map-preferences', true ) }
				>
					<WindyMap attributes={ attributes } />
				</div>
			) }
		</>
	);
};

export default memo( RenderMap );
