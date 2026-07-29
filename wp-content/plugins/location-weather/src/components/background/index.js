import { __ } from '@wordpress/i18n';
import { __experimentalColorGradientControl as ColorGradientControl } from '@wordpress/block-editor';
import { ColorPicker } from '..';
import BgButtons from '../bgButtons';
import { BgIcon, GradientIcon } from './svgIcons';
import { inArray } from '../../controls';

const SPLWBackgroundPicker = ( {
	background,
	attributesKey,
	setAttributes,
} ) => {
	const bgButtonOption = [
		{
			label: <BgIcon />,
			value: 'solid',
			tooltip: 'Solid',
		},
		{
			label: <GradientIcon />,
			value: 'gradient',
			tooltip: 'Gradient',
		},
	];

	const backgroundKeys = Object.keys( background );
	const options = bgButtonOption?.filter( ( item ) =>
		inArray( backgroundKeys, item?.value )
	);
	const backgroundType = background?.style || 'solid';

	const changeBackgroundValue = ( key, value ) => {
		setAttributes( {
			[ attributesKey ]: {
				...background,
				[ key ]: value,
			},
		} );
	};

	return (
		<>
			<BgButtons
				label={ __( 'Background Type', 'location-weather' ) }
				attributes={ backgroundType }
				onClick={ ( value ) => changeBackgroundValue( 'style', value ) }
				items={ options }
			/>
			{ 'solid' === backgroundType && (
				<ColorPicker
					label={ __( 'Background Color', 'location-weather' ) }
					value={ background?.solid }
					onChange={ ( value ) =>
						changeBackgroundValue( 'solid', value )
					}
				/>
			) }
			{ 'gradient' === backgroundType && (
				<div className="splw-background spl-weather-component-mb">
					<ColorGradientControl
						gradientValue={ background?.gradient }
						gradients={ [] }
						onGradientChange={ ( value ) =>
							changeBackgroundValue( 'gradient', value )
						}
					/>
				</div>
			) }
		</>
	);
};

export default SPLWBackgroundPicker;
