import './editor.scss';

export const IconSetDisplay = ( { icons, setType } ) => {
	const Icon = icons[ setType ];
	return (
		<div className="spl-weather-icon-set-display-component spl-weather-component-mb">
			<div
				className={ `spl-weather-icon-set-container set-type-${ setType }` }
			>
				<Icon />
			</div>
		</div>
	);
};

export default IconSetDisplay;
