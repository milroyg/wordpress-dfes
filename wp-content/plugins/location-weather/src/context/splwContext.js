import { createContext, useContext, useState } from '@wordpress/element';

// Create the context
export const SplWeatherContext = createContext();

// Create a provider component.
export const SplWeatherProvider = ( { apiUnit, attributes, children } ) => {
	const { currentUnit, displayTemperatureUnit, activeTemperatureUnit } =
		attributes;

	const activeUnit =
		activeTemperatureUnit === 'auto' ? apiUnit : activeTemperatureUnit;
	const [ activeTempUnit, setActiveTempUnit ] = useState( currentUnit );

	const toUnit = {
		metric: 'metric',
		imperial: 'imperial',
	}[ displayTemperatureUnit ];

	const contextValue = {
		apiUnit,
		toUnit,
		activeTempUnit,
		setActiveTempUnit,
	};

	return (
		<SplWeatherContext.Provider value={ contextValue }>
			{ children }
		</SplWeatherContext.Provider>
	);
};

export const useSplWeatherContextData = () => {
	return useContext( SplWeatherContext );
};
