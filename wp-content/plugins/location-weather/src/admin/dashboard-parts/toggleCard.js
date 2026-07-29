import Toggle from 'react-toggle';
import { Demos, DocsFilled, ProIconFill } from '../icons';
import { blockRegisterInfo, inArray, proBlocks } from '../../controls';

const ToggleCard = ( { attributes, blockShowHideHandler, onlyLiveDemo = false } ) => {
	const { show, name } = attributes;
	if ( ! blockRegisterInfo[ name ] ) {
		return;
	}
	const { icon, demoLink, docLink, title } = blockRegisterInfo[ name ];

	const isPro = inArray( proBlocks, name );

	return (
		<div className="spl-weather-blocks-settings-card">
			{ isPro && (
				<div className="splw-pro-blocks-badge">
					<ProIconFill />
					<span>PRO</span>
				</div>
			) }
			<div className="spl-weather-blocks-settings-card-info">
				<div className="spl-weather-blocks-settings-card-icon">
					{ icon }
				</div>
				<div className="spl-weather-blocks-settings-card-docs">
					<h4>{ title }</h4>
					<ul>
						{ ( !onlyLiveDemo && docLink ) && (
							<li>
								<a href={ docLink } target="_blank">
									<DocsFilled /> Docs
								</a>
							</li>
						) }
						{ demoLink && (
							<li>
								<a href={ demoLink } target="_blank">
									<Demos /> {onlyLiveDemo ? "Live " : ""} Demo
								</a>
							</li>
						) }
					</ul>
				</div>
			</div>
			<div className="spl-weather-blocks-settings-toggle-btn">
				<Toggle
					defaultChecked={ isPro ? false : show }
					icons={ false }
					onChange={ () => blockShowHideHandler( name ) }
					disabled={ isPro }
				/>
			</div>
		</div>
	);
};

export default ToggleCard;
