import Select from 'react-select';
import './editor.scss';

const MultipleSelect = ( {
	attributes,
	setAttributes,
	attributesKey,
	label,
	items,
	objectData = false,
	onChange = false,
	flex = false,
	reset = false,
	onInputChange = false,
} ) => {
	const defaultValues = items?.filter( ( f, i ) =>
		( attributes || [] ).includes( f.value )
	);
	const updateValue = ( data ) => {
		if ( objectData ) {
			const updatedValues = data?.map( ( d ) => {
				return { value: d.value, type: d.type };
			} );
			setAttributes( { [ attributesKey ]: updatedValues } );
		} else {
			const updatedValues = data?.map( ( d ) => d.value );
			setAttributes( { [ attributesKey ]: updatedValues } );
		}
	};

	return (
		<div
			className={ `spl-weather-multi-select ${
				flex ? 'd-flex' : ''
			} spl-weather-component-mb` }
		>
			<p className="spl-weather-component-title">{ label }</p>
			<Select
				defaultValue={ attributes ? attributes : defaultValues }
				isMulti
				options={ items }
				isClearable={ reset }
				onChange={ ( data ) =>
					onChange ? onChange( data ) : updateValue( data )
				}
				onInputChange={ ( e ) =>
					onInputChange ? onInputChange( e ) : ''
				}
				className="spl-weather-basic-multi-select"
			/>
		</div>
	);
};

export default MultipleSelect;
