import { createContext, useContext, useState } from '@wordpress/element';
import { getItemFromLocalStorage, setItemOnLocalStorage } from '../controls';

// Create the context
export const TogglePanelBodyContext = createContext();

// Create a provider component.
export const TogglePanelBodyProvider = ( { children } ) => {
	const accordionKey = 'spl-weather-panel-info';
	const defaultValues = { accordion: 'templates', tab: 'general' };
	const { accordion, tab } =
		getItemFromLocalStorage( accordionKey ) || defaultValues;

	const [ openedPanelBody, setOpenedPanelBody ] = useState( accordion );
	const [ activeTab, setActiveTab ] = useState( tab );

	const togglePanelBody = ( panel, event = false ) => {
		if ( panel === openedPanelBody && event ) {
			return;
		}
		const open = openedPanelBody === panel ? '' : panel;
		const newPanelState = event ? panel : open;
		setOpenedPanelBody( newPanelState );
		setItemOnLocalStorage( accordionKey, {
			accordion: newPanelState,
			tab,
		} );
		setActiveTab( 'general' );
		if ( event ) {
			setTimeout( () => {
				const panel = document.querySelector(
					'.components-panel__body.is-opened'
				);
				panel.classList.add( 'active' );
				setTimeout( () => {
					panel.classList.remove( 'active' );
				}, 800 );
			}, 600 );
		}
	};

	const toggleActiveTab = ( tabName ) => {
		setActiveTab( tabName );
		setItemOnLocalStorage( accordionKey, { accordion, tab: tabName } );
	};

	const contextValue = {
		activeTab,
		openedPanelBody,
		toggleActiveTab,
		togglePanelBody,
	};

	return (
		<TogglePanelBodyContext.Provider value={ contextValue }>
			{ children }
		</TogglePanelBodyContext.Provider>
	);
};

export const useTogglePanelBody = () => {
	return useContext( TogglePanelBodyContext );
};
