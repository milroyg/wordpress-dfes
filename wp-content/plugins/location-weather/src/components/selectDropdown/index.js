import { __ } from '@wordpress/i18n';
import './editor.scss';
import SpPopover from '../popover';

const SelectDropdown = ( {
	label,
	options,
	attributes,
	setAttributes,
	attributesKey,
} ) => {
	return (
		<>
			<SpPopover label={ label }>
				<ul className="spl-weather-select-dropdown">
					{ options?.map( ( option, index ) => (
						<li
							key={ index }
							className={ `spl-weather-select-dropdown-option ${
								attributes === option.value ? 'active' : ''
							}  ` }
							onClick={ () =>
								setAttributes( {
									[ attributesKey ]: option.value,
								} )
							}
						>
							{ option.label && <span>{ option.label }</span> }
							{ option.icon && <span>{ option.icon }</span> }
						</li>
					) ) }
				</ul>
			</SpPopover>
		</>
	);
};

export default SelectDropdown;
