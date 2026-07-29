import { __ } from '@wordpress/i18n';
import { memo } from '@wordpress/element';
import { blockRegisterInfo, inArray, layoutIconItems } from '../../controls';
import { useTogglePanelBody } from '../../context';
import './editor.scss';
import ReadyPatternsBtn from '../readyPatternsButton';

const RenderTemplatePreset = ( { blockName, setAttributes } ) => {
	const { togglePanelBody } = useTogglePanelBody();
	const templates = layoutIconItems[ blockName ];
	const skipTemplate = templates[ 0 ]?.value || '';
	const skipAdditionalDataLayout = templates[ 0 ]?.additional_layout || '';

	const blockOptions = {
		vertical: {
			title: __( 'Weather Vertical Card Templates', 'location-weather' ),
			subTitle: __(
				'Choose a vertical card weather template to get started',
				'location-weather'
			),
		},
		horizontal: {
			title: __( 'Weather Horizontal Templates', 'location-weather' ),
			subTitle: __(
				'Choose a horizontal weather template to get started',
				'location-weather'
			),
		},
		tabs: {
			title: __( 'Weather Tabs Templates', 'location-weather' ),
			subTitle: __(
				'Choose a tabs weather template to get started',
				'location-weather'
			),
		},
		table: {
			title: __( 'Weather Table Templates', 'location-weather' ),
			subTitle: __(
				'Choose a table weather template to get started',
				'location-weather'
			),
		},
		grid: {
			title: __( 'Weather Grid Templates', 'location-weather' ),
			subTitle: __(
				'Choose a grid weather template to get started',
				'location-weather'
			),
		},
		grid: {
			title: __( 'Weather Grid Templates', 'location-weather' ),
			subTitle: __(
				'Choose a grid weather template to get started',
				'location-weather'
			),
		},
		'aqi-minimal': {
			title: __( 'AQI Minimal Card Templates', 'location-weather' ),
			subTitle: __(
				'Choose a minimal air quality card template to get started.',
				'location-weather'
			),
		},
	};

	const blockNames = {
		vertical: 'vertical-card',
		'owm-map': 'map',
	};
	const modifiedBlockName = `sp-location-weather-pro/${
		blockNames[ blockName ] || blockName
	}`;

	const demoLink = blockRegisterInfo[ modifiedBlockName ]?.demoLink;

	return (
		<div className="spl-weather-layout-variation-picker-modal">
			<div className="spl-weather-layout-modal-label">
				<h3>{ blockOptions[ blockName ]?.title }</h3>
				<p>{ blockOptions[ blockName ]?.subTitle }</p>
			</div>

			<ul
				className={ `spl-weather-layout-modal-items sp-d-grid sp-grid-cols-${
					inArray( [ 'vertical', 'aqi-minimal' ], blockName )
						? '3'
						: '2'
				} sp-p-0` }
			>
				{ templates?.map(
					(
						{
							label,
							value,
							icon: Icon,
							additional_layout,
							onlyPro,
						},
						i
					) => (
						<li
							key={ i }
							className={ `spl-weather-layout-modal-item ${
								onlyPro
									? 'spl-only-pro-card'
									: 'sp-cursor-pointer'
							}` }
							onClick={ () => {
								! onlyPro &&
									setAttributes( {
										template: value,
										active_additional_data_layout:
											additional_layout || '',
									} );
								togglePanelBody( 'templates', true );
							} }
						>
							<div className="splw-preset-icon-wrapper">
								<Icon isPopup={ true } />
								{ onlyPro && (
									<span className="spl-pro-badge">
										<a
											href={ demoLink }
											target="_blank"
											rel="noopener noreferrer"
											className="spl-pro-card-demo-link"
										>
											{ __( 'Demo', 'location-weather' ) }
										</a>
										<a
											href="https://locationweather.io/pricing/"
											target="_blank"
											rel="noopener noreferrer"
											className="spl-pro-card-link"
										>
											{ __( 'PRO', 'location-weather' ) }
										</a>
									</span>
								) }
							</div>
							<p>{ label }</p>
						</li>
					)
				) }
			</ul>

			<div className="spl-weather-layout-modal-skip-button-wrapper sp-d-flex sp-justify-center sp-align-i-center">
				<ReadyPatternsBtn
					blockName={ blockName }
					label={ __( 'Start with Ready Patterns', 'location-weather' ) }
				/>
				<span
					className="spl-weather-layout-modal-skip-button"
					onClick={ () => {
						setAttributes( {
							template: skipTemplate,
							active_additional_data_layout:
								skipAdditionalDataLayout,
						} );
						togglePanelBody( 'templates', true );
					} }
				>
					{ __( 'Skip', 'location-weather' ) }
				</span>
			</div>
		</div>
	);
};

export default memo( RenderTemplatePreset );
