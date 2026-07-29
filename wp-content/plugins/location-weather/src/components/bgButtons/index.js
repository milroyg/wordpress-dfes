import { __ } from '@wordpress/i18n';
import { ButtonGroup, Button } from '@wordpress/components';
import './editor.scss';

const BgButtons = ( {
	attributes,
	attributesKey,
	setAttributes,
	label,
	items,
	onClick = false,
} ) => {
	const setBgType = ( newValue ) => {
		if ( onClick ) {
			onClick( newValue );
		} else {
			setAttributes( { [ attributesKey ]: newValue } );
		}
	};

	return (
		<>
			<div className="splw-background spl-weather-component-mb">
				{ /* Background type */ }
				<ButtonGroup
					className={ `splw-background-control spl-weather-component-mb sp-d-flex` }
				>
					<label className="spl-weather-component-title">
						{ label }
					</label>
					<div className={ `splw-background-left` }>
						{ items.map( ( item, i ) => (
							<Button
								className={
									attributes === item.value ? 'active' : ''
								}
								key={ i }
								value={ item.value }
								onClick={ ( e ) =>
									setBgType(
										e.target.closest( 'button' ).value
									)
								}
							>
								<span>{ item.label }</span>
								{ <p>{ item.tooltip }</p> }
							</Button>
						) ) }
					</div>
				</ButtonGroup>
			</div>
		</>
	);
};

export default BgButtons;
