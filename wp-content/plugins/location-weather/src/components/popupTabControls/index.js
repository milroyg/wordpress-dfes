import { createContext, useContext, useState } from '@wordpress/element';
import { ButtonGroup } from '..';

const TabContext = createContext();

const PopupTabControls = ( { children } ) => {
	const [ activeTab, setActiveTab ] = useState( 'typography' );

	return (
		<TabContext.Provider value={ activeTab }>
			<ButtonGroup
				attributes={ activeTab }
				onClick={ ( value ) => setActiveTab( value ) }
				items={ [
					{ label: 'Typography', value: 'typography' },
					{ label: 'Appearance', value: 'appearance' },
				] }
			/>
			{ children }
		</TabContext.Provider>
	);
};

const Typography = ( { children } ) => {
	const active = useContext( TabContext );
	return active === 'typography' ? children : null;
};

const Appearance = ( { children } ) => {
	const active = useContext( TabContext );
	return active === 'appearance' ? children : null;
};

PopupTabControls.Typography = Typography;
PopupTabControls.Appearance = Appearance;

export default PopupTabControls;
