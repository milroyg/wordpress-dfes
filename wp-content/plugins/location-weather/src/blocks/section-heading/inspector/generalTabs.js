import { __ } from '@wordpress/i18n';
import {
	ButtonGroup,
	InputControl,
	RangeControl,
	SelectDropdown,
	SelectField,
	Toggle,
} from '../../../components';
import {
	SectionHeadingTwoIcon,
	SectionHeadingThreeIcon,
	SectionHeadingFourIcon,
} from './icons';
import {
	CenterIcon,
	LeftIcon,
	RightIcon,
} from '../../../components/buttonGroup/svgIcon';

export const HeadingGeneralTab = ( { attributes, setAttributes } ) => {
	const { headingAlignment, headingHtmlTag, headingStyle, headingLabel } =
		attributes;
	return (
		<>
			<InputControl
				label={ __( 'Heading Label', 'location-weather' ) }
				attributes={ headingLabel }
				attributesKey={ 'headingLabel' }
				setAttributes={ setAttributes }
			/>
			<SelectDropdown
				label={ __( 'Heading Style', 'location-weather' ) }
				attributes={ headingStyle }
				attributesKey={ 'headingStyle' }
				setAttributes={ setAttributes }
				options={ [
					{
						label: (
							<span
								style={ {
									fontSize: '18px',
									fontWeight: '500',
								} }
							>
								This is a Heading
							</span>
						),
						value: 'heading-one',
					},
					{
						label: <SectionHeadingTwoIcon />,
						value: 'heading-two',
					},
					{
						label: <SectionHeadingThreeIcon />,
						value: 'heading-three',
					},
					{
						label: <SectionHeadingFourIcon />,
						value: 'heading-four',
					},
				] }
			/>
			<SelectField
				label={ __( 'HTML Tag', 'location-weather' ) }
				attributes={ headingHtmlTag }
				setAttributes={ setAttributes }
				attributesKey={ 'headingHtmlTag' }
				items={ [
					{ label: 'H1', value: 'h1' },
					{ label: 'H2', value: 'h2' },
					{ label: 'H3', value: 'h3' },
					{ label: 'H4', value: 'h4' },
					{ label: 'H5', value: 'h5' },
					{ label: 'H6', value: 'h6' },
					{ label: 'Span', value: 'span' },
					{ label: 'P', value: 'p' },
				] }
			/>

			<ButtonGroup
				label={ __( 'Alignment', 'location-weather' ) }
				attributes={ headingAlignment }
				setAttributes={ setAttributes }
				attributesKey={ 'headingAlignment' }
				items={ [
					{ label: <LeftIcon />, value: 'left' },
					{ label: <CenterIcon />, value: 'center' },
					{ label: <RightIcon />, value: 'right' },
				] }
			/>
		</>
	);
};

export const SubHeadingGeneralTab = ( { attributes, setAttributes } ) => {
	const { subHeading, subHeadingLabel, subHeadingPosition, subHeadingGap } =
		attributes;
	return (
		<>
			<Toggle
				label={ __( 'Sub Heading', 'location-weather' ) }
				attributes={ subHeading }
				setAttributes={ setAttributes }
				attributesKey={ 'subHeading' }
			/>
			{ subHeading && (
				<>
					<InputControl
						label={ __( 'Sub Heading Label', 'location-weather' ) }
						attributes={ subHeadingLabel }
						attributesKey={ 'subHeadingLabel' }
						setAttributes={ setAttributes }
					/>
					<ButtonGroup
						label={ __(
							'Sub Heading Position',
							'location-weather'
						) }
						attributes={ subHeadingPosition }
						setAttributes={ setAttributes }
						attributesKey={ 'subHeadingPosition' }
						items={ [
							{
								label: __( 'Top', 'location-weather' ),
								value: 'top',
							},
							{
								label: __( 'Bottom', 'location-weather' ),
								value: 'bottom',
							},
						] }
					/>
					<RangeControl
						label={ __( 'Gap', 'location-weather' ) }
						setAttributes={ setAttributes }
						attributes={ subHeadingGap }
						attributesKey={ 'subHeadingGap' }
						max={ 100 }
						defaultValue={ { value: 10, unit: 'px' } }
					/>
				</>
			) }
		</>
	);
};
