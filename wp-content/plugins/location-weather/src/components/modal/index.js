const Modal = ( { opened, onClose, children, className = '' } ) => {
	return (
		<div
			className={ `spl-weather-modal${
				opened ? ' sp-d-block' : ' sp-d-hidden'
			} ${ className }` }
		>
			{ children }
			<div
				onClick={ onClose }
				className="spl-weather-modal-close-button sp-cursor-pointer sp-d-flex sp-justify-center sp-align-i-center"
			>
				✕
			</div>
		</div>
	);
};

export default Modal;
