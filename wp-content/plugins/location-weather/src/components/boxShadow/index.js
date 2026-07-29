import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { ButtonGroup, ColorPicker, Spacing, Toggle } from '../index';
import { jsonStringify } from '../../controls';

const BoxShadow = ( {
	shadowColorBtn = true,
	attributes,
	attributesKey,
	setAttributes,
	defaultShadowColor = '#949494',
	enableAttributeKey,
	enableAttribute,
} ) => {
	const [ buttonTab, setButtonTab ] = useState( 'color' );

	const shadowColor = ( newColor ) => {
		setAttributes( {
			[ attributesKey ]: { ...attributes, [ buttonTab ]: newColor },
		} );
	};

	return (
		<div className="spl-weather-box-shadow-component">
			<Toggle
				label={ __( 'Enable Box Shadow', 'location-weather' ) }
				attributes={ enableAttribute }
				attributesKey={ enableAttributeKey }
				setAttributes={ setAttributes }
			/>
			{ enableAttribute && (
				<>
					<Spacing
						label={ __( 'Box Shadow', 'location-weather' ) }
						attributes={ attributes }
						attributesKey={ attributesKey }
						setAttributes={ setAttributes }
						boxUnits={ true }
						defaultValue={ {
							unit: 'Outset',
							value: {
								top: '0',
								right: '3',
								bottom: '6',
								left: '0',
							},
						} }
						linkButton={ false }
						units={ [ 'Outset', 'Inset' ] }
						labelItem={ {
							top: __( 'X Offset', 'location-weather' ),
							right: __( 'Y Offset', 'location-weather' ),
							bottom: __( 'Blur', 'location-weather' ),
							left: __( 'Spread', 'location-weather' ),
						} }
					/>
					{ /* Shadow Color */ }
					{ shadowColorBtn && (
						<ButtonGroup
							label={ __( 'Shadow Color', 'location-weather' ) }
							attributes={ buttonTab }
							items={ [
								{ label: 'Default', value: 'color' },
								{ label: 'Hover', value: 'hoverColor' },
							] }
							onClick={ ( value ) => setButtonTab( value ) }
						/>
					) }
					<ColorPicker
						label={ __( 'Shadow Color', 'location-weather' ) }
						value={ attributes[ buttonTab ] }
						onChange={ shadowColor }
						defaultColor={ defaultShadowColor }
					/>
				</>
			) }
		</div>
	);
};

export default BoxShadow;
