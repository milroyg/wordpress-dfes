// Custom slider function to handle horizontal scrolling of weather items.
const customSlider = ( sliderContainer ) => {
	const sliderItems = sliderContainer.querySelectorAll(
		'.spl-weather-custom-slider-item'
	);
	const navPrev = sliderContainer.querySelector(
		'.spl-weather-custom-slider-nav-prev'
	);
	const navNext = sliderContainer.querySelector(
		'.spl-weather-custom-slider-nav-next'
	);
	let currentPosition = 0;

	// Get computed style to read the gap value
	const sliderStyles = window.getComputedStyle( sliderContainer );
	const gap = parseInt( sliderStyles.gap ) || 0;

	const expandedItemWidth = sliderItems[ 0 ]?.offsetWidth;
	const itemWidth = sliderItems[ 1 ]?.offsetWidth;

	// Calculate visible items including gap
	const visibleItems = Math.floor(
		sliderContainer.offsetWidth / ( itemWidth + gap )
	);

	// Scroll amount includes gaps between items
	const scrollAmount = ( visibleItems - 1 ) * ( itemWidth + gap );

	// Max position calculation includes gaps
	const maxPosition =
		( sliderItems.length - visibleItems ) * ( itemWidth + gap ) +
		( expandedItemWidth - itemWidth ); // Add the difference if first item is expanded (in case of horizontal accordion)

	// Function to update scroll position
	const updateScrollPosition = ( position ) => {
		sliderContainer.scrollTo( {
			left: position,
			behavior: 'smooth',
		} );
	};

	// Previous button click handler
	navPrev?.addEventListener( 'click', () => {
		currentPosition = Math.max( 0, currentPosition - scrollAmount );
		updateScrollPosition( currentPosition );
		updateButtonStates( currentPosition );
	} );

	// Next button click handler
	navNext?.addEventListener( 'click', () => {
		currentPosition = currentPosition + scrollAmount;
		updateScrollPosition( currentPosition );
		updateButtonStates( currentPosition );
	} );

	// Update button states
	const updateButtonStates = ( position = 0 ) => {
		navPrev?.classList.toggle( 'active', position > 0 );
		navNext?.classList.toggle( 'active', position < maxPosition );
	};

	// Initialize button states
	updateButtonStates();
};

// this function control forecast clickable card events.
function preloaderInitialize() {
	const preloader = document.querySelectorAll( '.spl-weather-preloader' );
	if ( preloader.length > 0 ) {
		preloader.forEach( ( loader ) => {
			const id = loader.getAttribute( 'data-preloader-id' );
			const mainEl = document.querySelector(
				'#' + id + ' .spl-weather-template-wrapper'
			);
			if ( mainEl ) {
				mainEl.style.opacity = 0;
				mainEl.style.transition = 'opacity 0.6s';
				mainEl.addEventListener(
					'transitionend',
					() => {
						mainEl.style.opacity = 1;
						loader.remove();
					},
					{ once: true }
				);
			}
		} );
	}
}

// this function manage dropdown live filter scripts.
function initializedForecastSelect( block ) {
	const lwSelects = block.querySelectorAll( '.spl-weather-forecast-select' );
	if ( lwSelects?.length > 0 ) {
		lwSelects.forEach( ( select ) => {
			const active = select.querySelector(
				'.spl-weather-forecast-selected-option'
			);
			const svg = select.querySelector(
				'.spl-weather-forecast-select-svg'
			);
			const selectTrigger = select.querySelector(
				'.spl-weather-select-active-item'
			);
			const list = select.querySelector(
				'.spl-weather-forecast-select-list'
			);

			// Toggle dropdown list on trigger click
			selectTrigger.addEventListener( 'click', () => {
				const isOpen = list.classList.contains( 'sp-d-flex' );
				list.classList.toggle( 'sp-d-flex', ! isOpen );
				list.classList.toggle( 'sp-d-hidden', isOpen );
				svg.classList.toggle( 'active', ! isOpen );
				svg.classList.toggle( 'inactive', isOpen );
			} );

			// Handle option selection
			list.addEventListener( 'click', ( e ) => {
				const prevActive = list.querySelector(
					'.spl-weather-forecast-select-item.active'
				);
				prevActive.classList.remove( 'active' );
				const item = e.target.closest(
					'.spl-weather-forecast-select-item'
				);
				item.classList.add( 'active' );
				active.innerText = item.innerText;
				active.dataset.value = item.dataset.value;

				list.classList.replace( 'sp-d-flex', 'sp-d-hidden' );
				svg.classList.replace( 'active', 'inactive' );
			} );
		} );
	}
}
// this function manage forecast select or tabs type live filter scripts.
function initializedForecastDataToggle( block ) {
	const forecasts = block.querySelectorAll( '.splwb-forecast' );
	if ( forecasts.length > 0 ) {
		forecasts.forEach( ( forecast ) => {
			const querySelector = ( selector ) => {
				return forecast.querySelector( selector );
			};
			const querySelectorAll = ( selector ) => {
				return forecast.querySelectorAll( selector );
			};
			const forecastSelect = querySelector(
				'.spl-weather-forecast-select'
			);
			const forecastTabs = querySelector( '.spl-weather-forecast-tabs' );
			// forecast select.
			if ( forecastSelect ) {
				const items = querySelector(
					'.spl-weather-forecast-select-list'
				);
				items?.addEventListener( 'click', ( e ) => {
					const active = e.target.closest(
						'.spl-weather-forecast-select-item'
					);
					const value = active.dataset.value;

					const elements = querySelectorAll(
						'.spl-weather-forecast-value'
					);
					elements?.forEach( ( el ) => {
						const isGradientSeparator = el.querySelector(
							'.splw-separator-gradient'
						);
						const isTemp = el.classList.contains( 'temperature' );
						const match = el.classList.contains( value );
						el.classList.toggle( 'sp-d-hidden', ! match );
					} );
				} );
			}
			//forecast tabs
			if ( forecastTabs ) {
				forecastTabs.addEventListener( 'click', ( event ) => {
					const clickedTab = event.target.closest(
						'.spl-weather-forecast-tab'
					);
					if ( ! clickedTab ) {
						return;
					}
					// remove active from all.
					querySelector(
						'.spl-weather-forecast-tab.active'
					).classList.remove( 'active' );
					clickedTab.classList.add( 'active' );
					const elements = querySelectorAll(
						'.spl-weather-forecast-value'
					);
					elements?.forEach( ( el ) => {
						const value = clickedTab.dataset.value;
						const match = el.classList.contains( value );
						const isTemp = el.classList.contains( 'temperature' );
						if ( isTemp ) {
							el.style.display =
								value === 'temperature' ? 'flex' : 'none';
						}
						el.classList.toggle( 'sp-d-hidden', ! match );
					} );
				} );
			}
		} );
	}
}

function splwSwiperInit( block ) {
	// swiper js.
	const allSwiper = block.querySelectorAll( '.spl-weather-swiper' );
	if ( allSwiper?.length > 0 ) {
		allSwiper.forEach( ( containerEl ) => {
			const optionsAttr = containerEl.getAttribute(
				'data-weather-carousel'
			);

			let swiperOptions = {};

			try {
				swiperOptions = JSON.parse( optionsAttr );
			} catch ( err ) {
				console.error( 'Invalid Swiper options JSON', err );
				return;
			}
			// Initialized Swiper.
			const swiper = new Swiper( containerEl, swiperOptions );
		} );
	}
}

// Tabs functionality.
function splWeatherTabsFunctionality( block ) {
	const tabGroups = block.querySelectorAll( '.spl-weather-tabs-group' );
	if ( tabGroups.length > 0 ) {
		const tabPanes = block.querySelectorAll( '.spl-weather-tab-pane' );
		tabGroups.forEach( ( tabGroup ) => {
			const tabButtons = tabGroup.querySelectorAll(
				'.spl-weather-tab-btn'
			);
			// Add click event to each button.
			tabButtons.forEach( ( button ) => {
				button.addEventListener( 'click', () => {
					// Get the target tab ID from data attribute
					const targetTab = button.getAttribute( 'data-tab' );
					// Remove active class from all buttons and panes in this group
					tabButtons.forEach( ( btn ) =>
						btn.classList.remove( 'active' )
					);
					tabPanes.forEach( ( pane ) =>
						pane.classList.remove( 'active' )
					);
					// Add active class to clicked button
					button.classList.add( 'active' );

					// Find and activate the corresponding pane
					const targetPane = block.querySelector(
						`.spl-weather-tab-pane#${ targetTab }`
					);
					if ( targetPane ) {
						targetPane.classList.add( 'active' );
					}
					if ( targetTab === 'map' ) {
						// Trigger a window resize event for initializing the map properly.
						window.dispatchEvent( new Event( 'resize' ) );
					}
				} );
			} );
		} );
	}
}

// Initialize all SPL Weather blocks - called on DOMContentLoaded and Elementor changes
function initializeSplWeatherBlocks() {
	preloaderInitialize();
	document
		.querySelectorAll( '.sp-location-weather-block-wrapper' )
		.forEach( ( blockWrapper ) => {
			const getBlockId = blockWrapper.getAttribute( 'id' );
			const block = document.getElementById( getBlockId );
			const querySelector = ( selector ) => {
				return block.querySelector( selector );
			};
			const querySelectorAll = ( selector ) => {
				return block.querySelectorAll( selector );
			};

			// temperature toggle based on metric.
			initializedForecastSelect( block );
			initializedForecastDataToggle( block );
			splwSwiperInit( block );
			// handle forecast data live filter.
			splWeatherTabsFunctionality( block );
			// Custom slider with minimal feature (instead of swiper).
			const customSliderContainers = querySelectorAll(
				'.spl-weather-custom-slider'
			);
			if ( customSliderContainers.length > 0 ) {
				customSliderContainers.forEach( ( container ) => {
					customSlider( container );
				} );
			}
		} );
}

document.addEventListener( 'DOMContentLoaded', function () {
	initializeSplWeatherBlocks();
} );

window.locationInit = initializeSplWeatherBlocks;

// Elementor integration using IIFE pattern
( function() {
	window.addEventListener( 'elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction(
			'frontend/element_ready/location_weather_saved_template.default',
			function( $scope ) {
				if ( typeof initializeSplWeatherBlocks === 'function' ) {
					initializeSplWeatherBlocks();
				}
			}
		);
	} );
} )();

// Elementor preview editor specific handler - watch for DOM changes
if ( typeof ElementorPreview !== 'undefined' || document.body.classList.contains( 'elementor-editor-preview' ) ) {
	// Watch for DOM changes in Elementor preview
	const observer = new MutationObserver( function( mutations ) {
		mutations.forEach( function( mutation ) {
			if ( mutation.addedNodes.length ) {
				mutation.addedNodes.forEach( function( node ) {
					if ( node.nodeType === 1 ) { // Element node
						if ( node.classList && node.classList.contains( 'sp-location-weather-block-wrapper' ) ) {
							initializeSplWeatherBlocks();
						}
						// Check if added node contains our blocks
						const blocks = node.querySelectorAll && node.querySelectorAll( '.sp-location-weather-block-wrapper' );
						if ( blocks && blocks.length > 0 ) {
							initializeSplWeatherBlocks();
						}
					}
				} );
			}
		} );
	} );

	observer.observe( document.body, {
		childList: true,
		subtree: true
	} );
}

// Fallback: Re-initialize on window load for Elementor
window.addEventListener( 'load', function() {
	if ( document.body.classList.contains( 'elementor-editor-preview' ) ) {
		initializeSplWeatherBlocks();
	}
} );