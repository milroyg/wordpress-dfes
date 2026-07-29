window.addEventListener( 'load', function () {
	const url = new URL( window.location.href );

	// ====================
	// Pattern Library Handler
	// ====================

	if ( url.searchParams.has( 'splw_pattern_library' ) ) {
		function tryClickPatternButton() {
			const patternButton = document.querySelector( '#splw-patterns-library-modal-button' );
			if ( patternButton ) {
				patternButton.click();
				// clear url.
				url.searchParams.delete( 'splw_pattern_library' );
				history.replaceState( null, '', url.toString() );
				return true;
			}
			return false;
		}

		// Try every 100ms for up to 2 seconds
		let patternAttempts = 0;
		const patternInterval = setInterval( () => {
			patternAttempts++;
			if ( tryClickPatternButton() || patternAttempts > 20 ) {
				clearInterval( patternInterval );
			}
		}, 100 );
	}

	// ====================
	// Block Inserter Handler
	// ====================

	if ( ! url.searchParams.has( 'splwblock_inserter' ) ) {
		return;
	}

	function tryScroll() {
		const headings = document.querySelectorAll( '.block-editor-inserter__panel-title' );
		const locationWeatherHeading = Array.from( headings ).find(
			( h ) => h.textContent.trim() === 'Location Weather'
		);
		if ( locationWeatherHeading ) {
			locationWeatherHeading.scrollIntoView( {
				behavior: 'smooth',
				block: 'start',
				inline: 'nearest',
			} );
			return true;
		}
		return false;
	}

	function tryClick() {
		if ( ! wp.data.dispatch ) {
			return false;
		}
		const { dispatch } = wp.data;
		if ( dispatch( 'core/editor' ) ) {
			dispatch( 'core/editor' ).setIsInserterOpened( true );
		} else if ( dispatch( 'core/edit-post' ) ) {
			dispatch( 'core/edit-post' ).setIsInserterOpened( true );
		}
		// clear url.
		url.searchParams.delete( 'splwblock_inserter' );
		history.replaceState( null, '', url.toString() );

		// Try to scroll every 100ms for up to 3 seconds
		let scrollAttempts = 0;
		const scrollInterval = setInterval( () => {
			scrollAttempts++;
			if ( tryScroll() || scrollAttempts > 30 ) {
				clearInterval( scrollInterval );
			}
		}, 100 );

		return true;
	}

	// Try every 100ms for up to 2 seconds
	let attempts = 0;
	const interval = setInterval( () => {
		attempts++;
		if ( tryClick() || attempts > 20 ) {
			clearInterval( interval );
		}
	}, 100 );
} );
