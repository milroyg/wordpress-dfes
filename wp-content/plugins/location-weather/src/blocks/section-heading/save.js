import { RichText, useBlockProps } from '@wordpress/block-editor';

const SectionHeadingSave = ( { attributes } ) => {
	const {
		headingHtmlTag,
		headingStyle,
		headingLabel,
		uniqueId,
		subHeading,
		subHeadingLabel,
		subHeadingPosition,
	} = attributes;
	const blockProps = useBlockProps.save( {
		className: 'spl-weather-section-heading-block-wrapper',
		id: uniqueId,
	} );
	return (
		<div { ...blockProps }>
			<div className={ `spl-weather-section-heading ${ headingStyle }` }>
				<RichText.Content
					tagName={ headingHtmlTag }
					value={ headingLabel }
					className="spl-weather-section-heading-text"
				/>
			</div>
			{ subHeading && (
				<div
					className={ `spl-weather-section-sub-heading ${ subHeadingPosition }` }
				>
					<RichText.Content
						tagName={ 'span' }
						value={ subHeadingLabel }
						className="spl-weather-section-sub-heading-text"
					/>
				</div>
			) }
		</div>
	);
};

export default SectionHeadingSave;
