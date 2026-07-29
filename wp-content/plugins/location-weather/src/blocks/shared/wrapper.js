import { __ } from '@wordpress/i18n';
import { memo } from '@wordpress/element';
import useApiData from '../../hooks/useApiData';
import { inArray } from '../../controls';
import SPPreloader from './templates/preloader';
import { ApiNotice, RenderTemplatePreset } from '../../components';
import { SplWeatherProvider } from '../../context';
import { InnerBlocks } from '@wordpress/block-editor';

export const EditorWrapper = memo(
	( { attributes, setAttributes, children } ) => {
		const { weatherData, loading } = useApiData( attributes );
		const weather_data = weatherData?.weather_data || null;
		const error_message = weatherData?.error_message || false;
		const unit = weatherData?.unit || 'metric';

		const {
			uniqueId,
			template,
			blockName,
			customClassName,
			bgColorType,
			imageType,
		} = attributes;

		if ( ! template ) {
			return (
				<RenderTemplatePreset
					blockName={ blockName }
					setAttributes={ setAttributes }
				/>
			);
		}
		if ( loading ) {
			return <SPPreloader />;
		}

		children = {
			...children,
			props: {
				attributes: attributes,
				weatherData: weatherData || {},
			},
		};

		const weatherBasedImage =
			inArray( [ 'vertical', 'horizontal' ], blockName ) &&
			'image' === bgColorType &&
			'weather-based' === imageType &&
			weather_data?.icon
				? ` weather-status-${ weather_data?.icon }`
				: '';

		const hasApiKey = splWeatherBlockLocalize?.weather_api_info?.has_api_key;

		return (
			<>
				<SplWeatherProvider apiUnit={ unit } attributes={ attributes }>
					{ (! hasApiKey && ! error_message ) && <ApiNotice /> }
					<div
						id={ uniqueId }
						className={ `spl-weather-${ blockName }-card sp-location-weather-block-wrapper${ weatherBasedImage } ${ customClassName }${
							error_message ? 'spl-weather-api-error' : ''
						}` }
					>
						{ error_message && (
							<div
								dangerouslySetInnerHTML={ {
									__html: error_message,
								} }
							/>
						) }
						{ weather_data && children }
					</div>
				</SplWeatherProvider>
			</>
		);
	}
);
