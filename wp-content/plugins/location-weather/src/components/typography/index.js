import Select from 'react-select';
import { __ } from '@wordpress/i18n';
import { RangeControl } from '@wordpress/components';
import { useEffect, useState, memo } from '@wordpress/element';
import { HeadingIcon } from './svgIcon';
import { InputControl, SelectField, ComponentTopSection } from '../index';
import './editor.scss';
import { useDeviceType } from '../../controls';
import {
	fetchFonts,
	fontWeightMap,
	getFontWeightList,
	textStylesOptions,
} from './utils';

const TypographyNew = ( {
	setAttributes,
	attributes,
	fontSizeDefault = { unit: 'px', value: 16 },
} ) => {
	const [ allFonts, setAllFonts ] = useState( [] );
	const [ fontLists, setFontLists ] = useState( [] );
	const {
		typography,
		typographyKey,
		fontSize,
		fontSizeKey,
		fontSpacing,
		fontSpacingKey,
		lineHeight,
		lineHeightKey,
	} = attributes;
	const deviceType = useDeviceType();

	useEffect( () => {
		if ( allFonts.length === 0 ) {
			fetchFonts().then( ( data ) => {
				let fonts = data.items.map( ( item ) => {
					return {
						label: item.family,
						value: item.family,
						font: { family: item.family, variants: item.variants },
					};
				} );
				setAllFonts( fonts );
				setFontLists( fonts.filter( ( font, i ) => i < 50 && font ) );
			} );
		}
	}, [] );

	const fontSearch = ( inputValue ) => {
		const filteredFonts = allFonts
			.filter( ( font ) => {
				return font.label
					.toLowerCase()
					.includes( inputValue.toLowerCase() );
			} )
			.filter( ( font, i ) => i < 30 && font );
		setFontLists( filteredFonts );
	};

	// font family.
	const { family, fontWeight, style } = typography;
	const defaultFamilyOption = {
		label: 'Default',
		value: 'Default',
		font: {
			family: 'Default',
			variants: [ '300', '400', '500', '600', '700', '800' ],
		},
	};
	const activeFontFamily =
		family === ''
			? defaultFamilyOption
			: allFonts?.find( ( { value } ) => value === family );

	const allFamilyList = [ defaultFamilyOption, ...fontLists ];
	const isAvailableOnList = allFamilyList?.find(
		( font ) => font.value === activeFontFamily?.value
	);
	const fontFamilySelectOptions = isAvailableOnList
		? allFamilyList
		: [ defaultFamilyOption, activeFontFamily, ...fontLists ];

	// functions.
	const onChangeFontStyle = ( fontStyle ) => {
		const arrayOfStyles = fontWeightMap[ fontStyle ]?.split( ' ' );
		const style = fontWeightMap[ fontStyle ].includes( 'italic' )
			? 'italic'
			: '';
		const fontWeight = arrayOfStyles[ arrayOfStyles?.length - 1 ];

		setAttributes( {
			[ typographyKey ]: {
				...typography,
				fontWeight,
				style,
			},
		} );
	};

	const onChangeLineHeight = ( value ) => {
		setAttributes( {
			[ lineHeightKey ]: {
				...lineHeight,
				device: {
					...lineHeight?.device,
					[ deviceType ]: value,
				},
			},
		} );
	};

	const onChangeFontSize = ( newValue ) => {
		setAttributes( {
			[ fontSizeKey ]: {
				...fontSize,
				device: {
					...fontSize?.device,
					[ deviceType ]: newValue,
				},
			},
		} );
		if ( fontSize?.unit[ deviceType ] === 'px' ) {
			const dynamicLineHeight = parseInt( newValue * 1.2 );
			onChangeLineHeight( dynamicLineHeight );
		}
	};

	const onChangeTextStyles = ( attributesKey, value ) => {
		const newValue = typography[ attributesKey ] === value ? '' : value;
		setAttributes( {
			[ typographyKey ]: {
				...typography,
				[ attributesKey ]: newValue,
			},
		} );
	};

	const isDefaultFontSize =
		fontSizeDefault?.value === fontSize?.device[ deviceType ] &&
		fontSizeDefault?.unit === fontSize?.unit[ deviceType ];

	return (
		<div className="spl-weather-typography">
			<div className="spl-weather-typography-header spl-weather-component-mb sp-d-flex sp-justify-center sp-align-i-center sp-gap-8px">
				<HeadingIcon />
				<span>{ __( 'Typography', 'location-weather' ) }</span>
			</div>
			<div className="spl-weather-typography-select-fields sp-d-flex sp-justify-between">
				<div className="spl-weather-typography-family spl-weather-select-field spl-weather-component-mb">
					<div className="spl-weather-font-family-header sp-mb-8px">
						<label className="spl-weather-component-title">
							{ __( 'Font family', 'location-weather' ) }
						</label>
					</div>
					<Select
						classNamePrefix={ 'spl-weather-font-family-select' }
						options={ fontFamilySelectOptions }
						value={ activeFontFamily }
						placeholder={ activeFontFamily?.label }
						onChange={ ( nextFont ) => {
							setAttributes( {
								[ typographyKey ]: {
									...typography,
									family:
										'Default' !== nextFont?.font?.family
											? nextFont?.font?.family
											: '',
									fontWeight:
										'regular' ===
										nextFont?.font?.variants[ 0 ]
											? '400'
											: nextFont?.font?.variants[ 0 ],
								},
							} );
						} }
						onInputChange={ ( inputValue ) =>
							fontSearch( inputValue )
						}
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
				</div>
				<SelectField
					label={ __( 'Font Style', 'location-weather' ) }
					attributes={
						activeFontFamily?.font?.variants.includes(
							`${ fontWeight }${ style }`
						)
							? `${ fontWeight }${ style }`
							: fontWeight
					}
					items={ getFontWeightList( activeFontFamily ) }
					onChange={ ( newStyle ) => onChangeFontStyle( newStyle ) }
					__nextHasNoMarginBottom
					__next40pxDefaultSize
				/>
			</div>
			<div className="spl-weather-typography-font-size-picker spl-weather-component-mb">
				<ComponentTopSection
					label={ __( 'Font Size', 'location-weather' ) }
					attributes={ fontSize }
					attributesKey={ fontSizeKey }
					setAttributes={ setAttributes }
					units={ [ 'px', '%', 'em' ] }
					onReset={
						isDefaultFontSize
							? false
							: () => {
									setAttributes( {
										[ fontSizeKey ]: {
											...fontSize,
											unit: {
												...fontSize.unit,
												[ deviceType ]:
													fontSizeDefault?.unit,
											},
										},
									} );
									onChangeFontSize( fontSizeDefault?.value );
							  }
					}
				/>
				<RangeControl
					color="#f26c0d"
					value={ fontSize?.device[ deviceType ] }
					onChange={ ( newValue ) => onChangeFontSize( newValue ) }
					__nextHasNoMarginBottom
					__next40pxDefaultSize
				/>
			</div>
			<div className="spl-weather-typography-line-height-latter-spacing-wrapper sp-d-flex sp-gap-8px spl-weather-component-mb">
				<div className="spl-weather-typography-line-height-picker">
					<ComponentTopSection
						label={ __( 'Line Height', 'location-weather' ) }
						attributes={ lineHeight }
						attributesKey={ lineHeightKey }
						setAttributes={ setAttributes }
						units={ [ 'px', '%', 'em' ] }
					/>
					<InputControl
						attributes={ lineHeight?.device[ deviceType ] }
						mb={ false }
						type="number"
						onChange={ ( newValue ) =>
							onChangeLineHeight( newValue )
						}
					/>
				</div>
				<div className="spl-weather-typography-letter-spacing-picker">
					<ComponentTopSection
						label={ __( 'Letter Spacing', 'location-weather' ) }
						attributes={ fontSpacing }
						attributesKey={ fontSpacingKey }
						setAttributes={ setAttributes }
						units={ [ 'px', '%', 'em' ] }
					/>
					<InputControl
						attributes={ fontSpacing?.device?.[ deviceType ] ?? 0 }
						mb={ false }
						type="number"
						onChange={ ( newValue ) => {
							setAttributes( {
								[ fontSpacingKey ]: {
									...( fontSpacing || {} ),
									device: {
										...( fontSpacing?.device || {} ),
										[ deviceType ]: newValue ?? 0,
									},
								},
							} );
						} }
					/>
				</div>
			</div>
			<div className="spl-weather-typography-multiple-button-group spl-weather-component-mb">
				<ComponentTopSection
					label={ __( 'Text Format', 'location-weather' ) }
				/>
				<div className="spl-weather-button-group-list">
					{ textStylesOptions?.map(
						( { label, key, value }, index ) => (
							<button
								key={ index }
								className={ `components-button${
									typography[ key ] === value ? ' active' : ''
								}` }
								onClick={ () =>
									onChangeTextStyles( key, value )
								}
							>
								<span title={ value }>{ label }</span>
							</button>
						)
					) }
				</div>
			</div>
		</div>
	);
};

export default memo( TypographyNew );
