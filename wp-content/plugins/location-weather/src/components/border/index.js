import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { Spacing, ButtonGroup, ColorPicker, SpPopover } from '../index';
import './editor.scss';
import { borderStyles } from '../../controls';

const Border = ( {
	attributes,
	attributesKey,
	setAttributes,
	defaultValue = {
		unit: 'px',
		value: {
			top: '0',
			right: '0',
			bottom: '0',
			left: '0',
		},
	},
} ) => {
	const { border, borderWidth } = attributes;

	const [ activeState, setActiveState ] = useState( 'color' );

	const borderColor = ( newColor ) => {
		setAttributes( {
			[ attributesKey.border ]: {
				...attributes?.border,
				[ activeState ]: newColor,
			},
		} );
	};

	const borderOptions = {
		color: 'Default',
		hoverColor: 'Hover',
		activeColor: 'Active',
	};

	let borderColorOptions = Object.entries( borderOptions ).reduce(
		( acc, [ key, label ] ) =>
			key === 'color' || key in border
				? [ ...acc, { label, value: key } ]
				: acc,
		[]
	);

	return (
		<SpPopover label={ __( 'Border', 'location-weather' ) }>
			<SpPopover.Content>
				<div className="spl-weather-border-component">
					<ButtonGroup
						label={ false }
						attributes={ border?.style }
						items={ borderStyles }
						onClick={ ( newStyle ) => {
							setAttributes( {
								[ attributesKey.border ]: {
									...attributes.border,
									style: newStyle,
								},
							} );
						} }
					/>
					<Spacing
						label={ __( 'Width', 'location-weather' ) }
						attributes={ borderWidth }
						attributesKey={ attributesKey.borderWidth }
						setAttributes={ setAttributes }
						defaultValue={ defaultValue }
					/>
					{ /* Border Color */ }
					{ borderColorOptions?.length > 1 && (
						<ButtonGroup
							label={ false }
							attributes={ activeState }
							items={ borderColorOptions }
							onClick={ ( value ) => setActiveState( value ) }
						/>
					) }
					<ColorPicker
						label={ __( 'Color', 'location-weather' ) }
						value={ border[ activeState ] }
						onChange={ borderColor }
					/>
				</div>
			</SpPopover.Content>
		</SpPopover>
	);
};

export default Border;
