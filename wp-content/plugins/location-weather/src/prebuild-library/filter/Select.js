import { useEffect, useRef, useState, useCallback } from '@wordpress/element';

const Select = ( props ) => {
	const { onChange, options, value, contentWH } = props;
	const [ isOpen, setIsOpen ] = useState( false );
	const selectRef = useRef( null );

	const handleOptionClick = ( option ) => {
		setIsOpen( false );
		onChange( option.value );
	};

	const handleClickOutside = useCallback( ( e ) => {
		if ( selectRef?.current && ! selectRef?.current.contains( e.target ) ) {
			setIsOpen( false );
		} else if (
			selectRef?.current &&
			selectRef?.current.contains( e.target ) &&
			! e.target.classList?.contains( 'splw-patterns-reserve-button' )
		) {
			setIsOpen(
				selectRef?.current.classList?.contains( 'open' ) ? false : true
			);
		}
	}, [] );

	useEffect( () => {
		document.addEventListener( 'mousedown', handleClickOutside );
		return () =>
			document.removeEventListener( 'mousedown', handleClickOutside );
	}, [ handleClickOutside ] );

	const selectedOption = options?.find( ( item ) => item.value === value );

	return (
		<div
			ref={ selectRef }
			className={ `splw_patterns_filter_select ${
				isOpen ? 'open' : ''
			}` }
		>
			<div className="splw_patterns_filter_selected">
				{ selectedOption ? selectedOption.label : ' ' }
				<i className="splwp-icon-chevron"></i>
			</div>
			{ isOpen && (
				<ul
					className="splw_patterns_filter_select_options"
					style={ {
						minWidth: contentWH?.width || '100px',
						maxHeight: contentWH?.height || '160px',
					} }
				>
					{ options.map( ( option, k ) => (
						<li
							className="splw-patterns-reserve-button splw_patterns_filter_select_option"
							key={ k }
							onClick={ () => handleOptionClick( option ) }
						>
							{ option.label }
						</li>
					) ) }
				</ul>
			) }
		</div>
	);
};

export default Select;
