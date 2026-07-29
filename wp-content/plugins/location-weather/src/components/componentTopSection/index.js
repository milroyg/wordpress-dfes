import { memo } from '@wordpress/element';
import Responsive from '../responsive';
import Units from '../units';
import ResetButton from '../resetButton';

const ComponentTopSection = ( {
	label,
	units = false,
	attributes,
	setAttributes,
	attributesKey = '',
	onReset = false,
} ) => {
	return (
		<div className="spl-weather-header-control sp-mb-8px">
			<div className="spl-weather-header-control-left">
				<label className="spl-weather-component-title">{ label }</label>
				{ attributes?.device && <Responsive /> }
			</div>
			{ units && (
				<div className="spl-weather-header-control-right">
					{ onReset && <ResetButton onClick={ () => onReset() } /> }
					<Units
						attributes={ attributes }
						setAttributes={ setAttributes }
						attributesKey={ attributesKey }
						units={ units }
					/>
				</div>
			) }
		</div>
	);
};

export default memo( ComponentTopSection );
