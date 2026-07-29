import { RichText, useBlockProps } from '@wordpress/block-editor';
import { InspectorControl } from '../../components';
import SectionHeadingBlockInspector from './inspector/inspector';
import { useEffect, useRef } from '@wordpress/element';
import { TogglePanelBodyProvider } from '../../context';
import dynamicCSS from './dynamicCss';
import { fontFamilyToUrlGenerator } from '../../controls';
import toggleDefaultValue from './toggleDefaultValues';
import { SectionHeadingPreviewImage } from './inspector/icons';

const SectionHeadingEdit = ( { clientId, attributes, setAttributes } ) => {
	const {
		headingHtmlTag,
		headingStyle,
		headingLabel,
		uniqueId,
		subHeading,
		subHeadingLabel,
		subHeadingPosition,
		headingTypography,
		subHeadingTypography,
		isPreview,
	} = attributes;

	if ( isPreview ) {
		return <SectionHeadingPreviewImage />;
	}

	const previousTemplateRef = useRef( headingStyle );

	useEffect( () => {
		const shortClientId = clientId.split( '-' )?.pop();
		const uniqueId = `spl-weather-heading-${ shortClientId }`;
		setAttributes( { uniqueId: uniqueId } );
	}, [ clientId ] );

	// Update dynamic CSS.
	useEffect( () => {
		setAttributes( {
			dynamicCss: dynamicCSS( attributes, 'frontend' ),
		} );
	}, [ attributes ] );

	useEffect( () => {
		if ( previousTemplateRef.current !== headingStyle ) {
			previousTemplateRef.current = headingStyle;
			const defaultValues = toggleDefaultValue( attributes );
			if ( defaultValues ) {
				setAttributes( defaultValues );
			}
		}
	}, [ headingStyle ] );
	// Load Google font link.
	const lwBlockTypography = [ headingTypography, subHeadingTypography ];

	useEffect( () => {
		const fontLists = fontFamilyToUrlGenerator(
			lwBlockTypography,
			'frontend'
		);
		setAttributes( { fontLists: fontLists } );
	}, [ headingTypography, subHeadingTypography ] );

	return (
		<>
			<div { ...useBlockProps() }>
				<style>
					{ fontFamilyToUrlGenerator( lwBlockTypography ) }
					{ dynamicCSS( attributes, 'editor' ) }
				</style>
				<TogglePanelBodyProvider>
					<InspectorControl
						attributes={ attributes }
						setAttributes={ setAttributes }
						Inspector={ SectionHeadingBlockInspector }
					/>
					<div
						className="spl-weather-section-heading-block-wrapper"
						id={ uniqueId }
					>
						<div
							className={ `spl-weather-section-heading ${ headingStyle }` }
						>
							<RichText
								tagName={ headingHtmlTag }
								value={ headingLabel }
								onChange={ ( newValue ) =>
									setAttributes( {
										headingLabel: newValue,
									} )
								}
								className="spl-weather-section-heading-text"
								contentEditable={ true }
							/>
						</div>
						{ subHeading && (
							<div
								className={ `spl-weather-section-sub-heading ${ subHeadingPosition }` }
							>
								<RichText
									tagName={ 'span' }
									value={ subHeadingLabel }
									onChange={ ( newValue ) =>
										setAttributes( {
											subHeadingLabel: newValue,
										} )
									}
									className="spl-weather-section-sub-heading-text"
									contentEditable={ true }
								/>
							</div>
						) }
					</div>
				</TogglePanelBodyProvider>
			</div>
		</>
	);
};

export default SectionHeadingEdit;
