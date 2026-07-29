import { __ } from '@wordpress/i18n';
import { __experimentalColorGradientControl as ColorGradientControl } from '@wordpress/block-editor';
import {
	BgButtons,
	Border,
	ButtonGroup,
	ColorPicker,
	FlatBorder,
	RangeControl,
	Spacing,
	SpPopover,
	Toggle,
	TypographyNew,
} from '../../../components';
import {
	BgIcon,
	GradientIcon,
	TransparentIcon,
} from '../../../components/background/svgIcons';
import { inArray } from '../../../controls';

export const HeadingStyleTab = ( { attributes, setAttributes } ) => {
	const {
		headingColor,
		headingTypography,
		headingFontSize,
		headingFontSpacing,
		headingLineHeight,
		headingBgColor,
		headingBgType,
		headingBgGradient,
		headingBorder,
		headingBorderRadius,
		headingBorderWidth,
		headingPadding,
		headingStyle,
		bottomLineHorPosition,
		bottomLineColor,
		bottomLineThickness,
		bottomLineWidth,
		sideLineColor,
		sideLineThickness,
		sideLineWidth,
		sideLineType,
		sideLineGap,
		fullWidthBottomLineThickness,
		fullWidthBottomLineColor,
		fullWidthBottomLine,
	} = attributes;
	return (
		<>
			<SpPopover label={ __( 'Heading', 'location-weather' ) }>
				<ColorPicker
					label={ __( 'Color', 'location-weather' ) }
					value={ headingColor }
					attributesKey={ 'headingColor' }
					setAttributes={ setAttributes }
					defaultColor="#2f2f2f"
				/>
				<TypographyNew
					attributes={ {
						typography: headingTypography,
						typographyKey: 'headingTypography',
						fontSize: headingFontSize,
						fontSizeKey: 'headingFontSize',
						fontSpacing: headingFontSpacing,
						fontSpacingKey: 'headingFontSpacing',
						lineHeight: headingLineHeight,
						lineHeightKey: 'headingLineHeight',
					} }
					setAttributes={ setAttributes }
					fontSizeDefault={ { unit: 'px', value: 36 } }
				/>
			</SpPopover>
			{ headingStyle === 'heading-two' && (
				<>
					<SpPopover
						label={ __( 'Bottom Line', 'location-weather' ) }
					>
						<ColorPicker
							label={ __( 'Line Color', 'location-weather' ) }
							value={ bottomLineColor }
							attributesKey={ 'bottomLineColor' }
							setAttributes={ setAttributes }
							defaultColor="#F26C0D"
						/>
						<RangeControl
							label={ __( 'Thickness', 'location-weather' ) }
							setAttributes={ setAttributes }
							attributes={ bottomLineThickness }
							attributesKey={ 'bottomLineThickness' }
							max={ 20 }
							defaultValue={ { value: 2, unit: 'px' } }
						/>
						<RangeControl
							label={ __( 'Width', 'location-weather' ) }
							setAttributes={ setAttributes }
							attributes={ bottomLineWidth }
							attributesKey={ 'bottomLineWidth' }
							max={ 900 }
							defaultValue={ { value: 140, unit: 'px' } }
						/>
						<RangeControl
							label={ __(
								'Horizontal Position',
								'location-weather'
							) }
							setAttributes={ setAttributes }
							attributes={ bottomLineHorPosition }
							attributesKey={ 'bottomLineHorPosition' }
							max={ 900 }
							defaultValue={ { value: 0, unit: '%' } }
						/>
					</SpPopover>
					<Toggle
						label={ __(
							'Full Width Bottom Line',
							'location-weather'
						) }
						attributes={ fullWidthBottomLine }
						setAttributes={ setAttributes }
						attributesKey={ 'fullWidthBottomLine' }
					/>
				</>
			) }
			{ fullWidthBottomLine && (
				<SpPopover
					label={ __( 'Full Width Bottom Line', 'location-weather' ) }
				>
					<ColorPicker
						label={ __( 'Line Color', 'location-weather' ) }
						value={ fullWidthBottomLineColor }
						attributesKey={ 'fullWidthBottomLineColor' }
						setAttributes={ setAttributes }
						defaultColor="#FFE0B3"
					/>
					<RangeControl
						label={ __( 'Thickness', 'location-weather' ) }
						setAttributes={ setAttributes }
						attributes={ fullWidthBottomLineThickness }
						attributesKey={ 'fullWidthBottomLineThickness' }
						max={ 20 }
						defaultValue={ { value: 2, unit: 'px' } }
					/>
				</SpPopover>
			) }

			{ headingStyle === 'heading-three' && (
				<SpPopover label={ __( 'Side Line', 'location-weather' ) }>
					<ButtonGroup
						label={ __( 'Show Line', 'location-weather' ) }
						attributes={ sideLineType }
						setAttributes={ setAttributes }
						attributesKey={ 'sideLineType' }
						items={ [
							{
								label: __( 'Left', 'location-weather' ),
								value: 'left',
							},
							{
								label: __( 'Right', 'location-weather' ),
								value: 'right',
							},
							{
								label: __( 'Both', 'location-weather' ),
								value: 'both',
							},
						] }
					/>
					<ColorPicker
						label={ __( 'Line Color', 'location-weather' ) }
						value={ sideLineColor }
						attributesKey={ 'sideLineColor' }
						setAttributes={ setAttributes }
						defaultColor="#F26C0D"
					/>
					<RangeControl
						label={ __( 'Thickness', 'location-weather' ) }
						setAttributes={ setAttributes }
						attributes={ sideLineThickness }
						attributesKey={ 'sideLineThickness' }
						max={ 20 }
						defaultValue={ { value: 2, unit: 'px' } }
					/>
					<RangeControl
						label={ __( 'Width', 'location-weather' ) }
						setAttributes={ setAttributes }
						attributes={ sideLineWidth }
						attributesKey={ 'sideLineWidth' }
						max={ 900 }
						defaultValue={ { value: 50, unit: 'px' } }
					/>
					<RangeControl
						label={ __( 'Gap', 'location-weather' ) }
						setAttributes={ setAttributes }
						attributes={ sideLineGap }
						attributesKey={ 'sideLineGap' }
						max={ 900 }
						defaultValue={ { value: 12, unit: 'px' } }
					/>
				</SpPopover>
			) }
			<BgButtons
				label={ __( 'Background Type', 'location-weather' ) }
				attributes={ headingBgType }
				attributesKey={ 'headingBgType' }
				setAttributes={ setAttributes }
				items={ [
					{
						label: <TransparentIcon />,
						value: 'transparent',
						tooltip: 'Transparent',
					},
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
				] }
			/>
			{ headingBgType === 'solid' && (
				<ColorPicker
					label={ __( 'Background Color', 'location-weather' ) }
					value={ headingBgColor }
					attributesKey={ 'headingBgColor' }
					setAttributes={ setAttributes }
					defaultColor="#FFE0B3"
				/>
			) }
			{ headingBgType === 'gradient' && (
				<div className="splw-background spl-weather-component-mb">
					<ColorGradientControl
						gradientValue={ headingBgGradient }
						gradients={ [] }
						onGradientChange={ ( newValue ) =>
							setAttributes( {
								headingBgGradient: newValue,
							} )
						}
					/>
				</div>
			) }
			{ inArray( [ 'heading-one', 'heading-four' ], headingStyle ) && (
				<>
					<Border
						borderColorBtn={ false }
						attributes={ {
							border: headingBorder,
							borderWidth: headingBorderWidth,
						} }
						attributesKey={ {
							border: 'headingBorder',
							borderWidth: 'headingBorderWidth',
						} }
						defaultValue={ {
							unit: 'px',
							value: {
								top: '0',
								right: '0',
								bottom: '0',
								left: '0',
							},
						} }
						setAttributes={ setAttributes }
					/>
					{ /* <SpPopover label={ __( 'Border', 'location-weather' ) }>
						<FlatBorder
							attributes={ {
								border: headingBorder,
								borderWidth: headingBorderWidth,
							} }
							attributesKey={ {
								border: 'headingBorder',
								borderWidth: 'headingBorderWidth',
							} }
							defaultValue={ {
								unit: 'px',
								value: {
									top: '0',
									right: '0',
									bottom: '0',
									left: '0',
								},
							} }
							setAttributes={ setAttributes }
						/>
					</SpPopover> */ }
					<Spacing
						label={ __( 'Border Radius', 'location-weather' ) }
						attributes={ headingBorderRadius }
						attributesKey={ 'headingBorderRadius' }
						setAttributes={ setAttributes }
						radiusIndicators={ true }
						defaultValue={ {
							unit: 'px',
							value: {
								top: '0',
								right: '0',
								bottom: '0',
								left: '0',
							},
						} }
					/>
				</>
			) }
			<Spacing
				label={ __( 'Padding', 'location-weather' ) }
				attributes={ headingPadding }
				attributesKey={ 'headingPadding' }
				setAttributes={ setAttributes }
				defaultValue={ {
					unit: 'px',
					value: {
						top: '0',
						right: '0',
						bottom: '0',
						left: '0',
					},
				} }
			/>
		</>
	);
};

export const SubHeadingStyleTab = ( { attributes, setAttributes } ) => {
	const {
		subHeadingColor,
		subHeadingTypography,
		subHeadingFontSize,
		subHeadingFontSpacing,
		subHeadingLineHeight,
		subHeadingBgColor,
		subHeadingBgType,
		subHeadingBgGradient,
		subHeadingBorder,
		subHeadingBorderRadius,
		subHeadingBorderWidth,
		subHeadingPadding,
	} = attributes;
	return (
		<>
			<SpPopover label={ __( 'Sub Heading', 'location-weather' ) }>
				<ColorPicker
					label={ __( 'Color', 'location-weather' ) }
					value={ subHeadingColor }
					attributesKey={ 'subHeadingColor' }
					setAttributes={ setAttributes }
					defaultColor="#2f2f2f"
				/>
				<TypographyNew
					attributes={ {
						typography: subHeadingTypography,
						typographyKey: 'subHeadingTypography',
						fontSize: subHeadingFontSize,
						fontSizeKey: 'subHeadingFontSize',
						fontSpacing: subHeadingFontSpacing,
						fontSpacingKey: 'subHeadingFontSpacing',
						lineHeight: subHeadingLineHeight,
						lineHeightKey: 'subHeadingLineHeight',
					} }
					setAttributes={ setAttributes }
					fontSizeDefault={ { unit: 'px', value: 18 } }
				/>
			</SpPopover>
			<BgButtons
				label={ __( 'Background Type', 'location-weather' ) }
				attributes={ subHeadingBgType }
				attributesKey={ 'subHeadingBgType' }
				setAttributes={ setAttributes }
				items={ [
					{
						label: <TransparentIcon />,
						value: 'transparent',
						tooltip: 'Transparent',
					},
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
				] }
			/>
			{ subHeadingBgType === 'solid' && (
				<ColorPicker
					label={ __( 'Background Color', 'location-weather' ) }
					value={ subHeadingBgColor }
					attributesKey={ 'subHeadingBgColor' }
					setAttributes={ setAttributes }
					defaultColor="#FFE0B3"
				/>
			) }
			{ subHeadingBgType === 'gradient' && (
				<div className="splw-background spl-weather-component-mb">
					<ColorGradientControl
						gradientValue={ subHeadingBgGradient }
						gradients={ [] }
						onGradientChange={ ( newValue ) =>
							setAttributes( {
								subHeadingBgGradient: newValue,
							} )
						}
					/>
				</div>
			) }
			<SpPopover label={ __( 'Border', 'location-weather' ) }>
				<FlatBorder
					attributes={ {
						border: subHeadingBorder,
						borderWidth: subHeadingBorderWidth,
					} }
					attributesKey={ {
						border: 'subHeadingBorder',
						borderWidth: 'subHeadingBorderWidth',
					} }
					defaultValue={ {
						unit: 'px',
						value: {
							top: '0',
							right: '0',
							bottom: '0',
							left: '0',
						},
					} }
					setAttributes={ setAttributes }
				/>
			</SpPopover>
			<Spacing
				label={ __( 'Border Radius', 'location-weather' ) }
				attributes={ subHeadingBorderRadius }
				attributesKey={ 'subHeadingBorderRadius' }
				setAttributes={ setAttributes }
				radiusIndicators={ true }
				defaultValue={ {
					unit: 'px',
					value: {
						top: '0',
						right: '0',
						bottom: '0',
						left: '0',
					},
				} }
			/>
			<Spacing
				label={ __( 'Padding', 'location-weather' ) }
				attributes={ subHeadingPadding }
				attributesKey={ 'subHeadingPadding' }
				setAttributes={ setAttributes }
				defaultValue={ {
					unit: 'px',
					value: {
						top: '0',
						right: '0',
						bottom: '0',
						left: '0',
					},
				} }
			/>
		</>
	);
};

export const HeadingAdvancedOptions = ( { attributes, setAttributes } ) => {
	const {
		headingContainerBgColor,
		headingContainerBgType,
		headingContainerBgGradient,
		headingContainerBorder,
		headingContainerBorderRadius,
		headingContainerBorderWidth,
		headingContainerPadding,
		headingContainerMargin,
	} = attributes;
	return (
		<>
			<BgButtons
				label={ __( 'Background Type', 'location-weather' ) }
				attributes={ headingContainerBgType }
				attributesKey={ 'headingContainerBgType' }
				setAttributes={ setAttributes }
				items={ [
					{
						label: <TransparentIcon />,
						value: 'transparent',
						tooltip: 'Transparent',
					},
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
				] }
			/>
			{ headingContainerBgType === 'solid' && (
				<ColorPicker
					label={ __( 'Background Color', 'location-weather' ) }
					value={ headingContainerBgColor }
					attributesKey={ 'headingContainerBgColor' }
					setAttributes={ setAttributes }
					defaultColor="#2f2f2f"
				/>
			) }
			{ headingContainerBgType === 'gradient' && (
				<div className="splw-background spl-weather-component-mb">
					<ColorGradientControl
						gradientValue={ headingContainerBgGradient }
						gradients={ [] }
						onGradientChange={ ( newValue ) =>
							setAttributes( {
								headingContainerBgGradient: newValue,
							} )
						}
					/>
				</div>
			) }
			<SpPopover label={ __( 'Border', 'location-weather' ) }>
				<FlatBorder
					attributes={ {
						border: headingContainerBorder,
						borderWidth: headingContainerBorderWidth,
					} }
					attributesKey={ {
						border: 'headingContainerBorder',
						borderWidth: 'headingContainerBorderWidth',
					} }
					defaultValue={ {
						unit: 'px',
						value: {
							top: '0',
							right: '0',
							bottom: '0',
							left: '0',
						},
					} }
					setAttributes={ setAttributes }
				/>
			</SpPopover>
			<Spacing
				label={ __( 'Border Radius', 'location-weather' ) }
				attributes={ headingContainerBorderRadius }
				attributesKey={ 'headingContainerBorderRadius' }
				setAttributes={ setAttributes }
				radiusIndicators={ true }
				defaultValue={ {
					unit: 'px',
					value: {
						top: '0',
						right: '0',
						bottom: '0',
						left: '0',
					},
				} }
			/>
			<Spacing
				label={ __( 'Padding', 'location-weather' ) }
				attributes={ headingContainerPadding }
				attributesKey={ 'headingContainerPadding' }
				setAttributes={ setAttributes }
				defaultValue={ {
					unit: 'px',
					value: {
						top: '0',
						right: '0',
						bottom: '0',
						left: '0',
					},
				} }
			/>
			<Spacing
				label={ __( 'Margin', 'location-weather' ) }
				attributes={ headingContainerMargin }
				attributesKey={ 'headingContainerMargin' }
				setAttributes={ setAttributes }
				defaultValue={ {
					unit: 'px',
					value: {
						top: '0',
						right: '0',
						bottom: '40',
						left: '0',
					},
				} }
			/>
		</>
	);
};
