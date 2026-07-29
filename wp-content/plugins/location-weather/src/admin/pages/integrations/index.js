import { __ } from '@wordpress/i18n';
import './style.scss';
import Toggle from 'react-toggle';
import { DocsFilled, Demos } from '../../icons';
import { integrationsRegisterInfo } from '../../constants/integrationsConstants';

const Integrations = ( { integrationSettings, integrationToggleHandler } ) => {
	const integrationIds = Object.keys( integrationsRegisterInfo );

	return (
		<section className="splw-integrations-page">
			<div className="splw-integrations-header">
				<h3 className="splw-integrations-title">
					{ __( 'Manage Page Builder Integrations', 'location-weather' ) }
				</h3>
				<p className="splw-integrations-subtitle">
					{ __(
						'Enable only what you need. Keep your site lean, fast, and clutter-free.',
						'location-weather'
					) }
				</p>
			</div>

			<div className="splw-integrations-grid">
				{ integrationIds.map( ( id ) => {
					const integrationInfo = integrationsRegisterInfo[ id ];
					const savedOption = integrationSettings?.find( ( opt ) => opt.id === id );
					const enabled = savedOption?.enabled ?? ! integrationInfo.upcoming;

					if ( ! integrationInfo ) {
						return null;
					}

					return (
						<div
							key={ id }
							className={`splw-integration-card${integrationInfo.upcoming ? " splw-upcoming" : ""}`}
						>
							<div className="splw-integration-card-content">
								<div className="splw-integration-card-header">
									<div
										className="splw-integration-icon"
										style={ {
											background: integrationInfo.iconBg,
										} }
									>
										<span className="splw-integration-icon-inner">
											{ integrationInfo.icon }
										</span>
									</div>
									<div className="splw-integration-info">
										<div className="splw-integration-title-row">
											<div className="splw-integration-name-wrapper">
												<h4 className="splw-integration-name">
													{ integrationInfo.name }
												</h4>
												{ integrationInfo.upcoming && (
													<span className="splw-integration-upcoming-badge">
														{ __( 'Upcoming', 'location-weather' ) }
													</span>
												) }
											</div>
											<div
												className={
													`splw-integration-toggle spl-weather-blocks-settings-toggle-btn ${ integrationInfo.upcoming ? 'splw-toggle-disabled' : '' }`
												}
											>
												<Toggle
													checked={ enabled }
													icons={ false }
													disabled={ integrationInfo.upcoming }
													onChange={ () =>
														integrationToggleHandler( id )
													}
												/>
											</div>
										</div>
										<p className="splw-integration-description">
											{ integrationInfo.description }
										</p>
									</div>
								</div>
								<div className="splw-integration-card-footer">
									{/* <a
										href={ integrationInfo.demoLink }
										target="_blank"
										rel="noopener noreferrer"
										className="splw-integration-action-btn"
									>
										<Demos />
										{ __( 'Demo', 'location-weather' ) }
									</a> */}
									<a
										href={ integrationInfo.docsLink }
										target="_blank"
										rel="noopener noreferrer"
										className="splw-integration-action-btn"
									>
										<DocsFilled />
										{ __( 'Docs', 'location-weather' ) }
									</a>
								</div>
							</div>
						</div>
					);
				} ) }
			</div>
		</section>
	);
};

export default Integrations;