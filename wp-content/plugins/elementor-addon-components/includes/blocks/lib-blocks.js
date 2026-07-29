/**
 * Utilitaires pour les appareils (responsive) dans les blocs ACF.
 * @since 2.4.7
 */
import { IconDesktop, IconTablet, IconMobile, IconTabletLand, IconMobileLand } from './lib-icons.js';

const { useState, useEffect } = window.wp.element || {};
const { select } = window.wp.data || {};
const { __ } = window.wp.i18n || {};

const DEVICES = ['desktop', 'tabletLand', 'tablet', 'mobileLand', 'mobile'];
const DEFAULT_SUFFIX = 'Width';
const DEFAULT_VALUES = {
	desktop: 60,
	tabletLand: 60,
	tablet: 80,
	mobileLand: 80,
	mobile: 100
};
const baseFontSize = 16; // 1rem = 16px
const vpMin = 320; // viewport de 320à 1200
const vpMax = 1200;
const precision = 4; // 4 chiffres après la virgule

function getKeyFromDevice(device, suffix = DEFAULT_SUFFIX) {
	if ( ! DEVICES.includes( device )) {
		device = 'desktop';
	}
	return `${device}${suffix}`;
}

function safeSourceObj(src) {
	return (src && typeof src === 'object') ? src : {};
}

export const getValueForActiveDevice = (srcObj, suffix = DEFAULT_SUFFIX) => {
	const sso = safeSourceObj( srcObj );
	const device = sso.activeDevice || 'desktop';
	const sfx = (typeof sso.suffix === 'string' && sso.suffix.length) ? sso.suffix : suffix;
	const key = getKeyFromDevice( device, sfx );

	if (sso[key] != null) {
		return Number( sso[key] );
	}

	const fallbacks = DEVICES.map( d => getKeyFromDevice( d, sfx ) );
	for (let fk of fallbacks) {
		if (sso[fk] != null) {
			return Number( sso[fk] );
		}
	}

	let base;
	if (device.startsWith( 'tablet' )) {
		base = 'tablet';
	} else if (device.startsWith( 'mobile' )) {
		base = 'mobile';
	} else {
		base = 'desktop';
	}

	if (DEFAULT_VALUES && DEFAULT_VALUES[base] != null) {
		return Number( DEFAULT_VALUES[base] );
	}
	return Number( DEFAULT_VALUES.desktop );
};

export const setValueForActiveDevice = (srcObj, setFn, val, suffix = DEFAULT_SUFFIX) => {
	const sso = Object.assign( {}, safeSourceObj( srcObj ) );
	const device = sso.activeDevice || 'desktop';
	const sfx = (typeof sso.suffix === 'string' && sso.suffix.length) ? sso.suffix : suffix;
	sso[getKeyFromDevice( device, sfx )] = Number( val );
	setFn( sso );
};

export const setActiveDevice = (srcObj, setFn, deviceKey, suffix = DEFAULT_SUFFIX) => {
	const sso = Object.assign( {}, safeSourceObj( srcObj ) );
	const device = DEVICES.includes( deviceKey ) ? deviceKey : 'desktop';
	sso.activeDevice = device;
	const sfx = (typeof sso.suffix === 'string' && sso.suffix.length) ? sso.suffix : suffix;
	const key = getKeyFromDevice( device, sfx );

	if (sso[key] == null) {
		const fallbacks = DEVICES.map( d => getKeyFromDevice( d, sfx ) );
		let found = null;
		for (let fk of fallbacks) {
			if (sso[fk] != null) {
				found = sso[fk]; break; }
		}
		let base;
		if (device.startsWith( 'tablet' )) {
			base = 'tablet';
		} else if (device.startsWith( 'mobile' )) {
			base = 'mobile';
		} else {
			base = 'desktop';
		}
		sso[key] = (found != null) ? Number( found ) : (DEFAULT_VALUES[base] != null ? Number( DEFAULT_VALUES[base] ) : Number( DEFAULT_VALUES.desktop ));
	}
	setFn( sso );
};

export const createLabelWithIcons = (createElement, labelText, srcObj, setFn) => {
	const sso = safeSourceObj( srcObj );
	const activeDevice = sso.activeDevice || 'desktop';
	const base = { display: 'inline-flex', cursor: 'pointer', color: 'currentColor' };
	const activeStyle = { outline: '2px solid #0073aa', borderRadius: 4 };

	const makeIcon = (title, renderFn, deviceKey) =>
		createElement(
			'span',
			{
				title,
				style: Object.assign( {}, base, deviceKey === activeDevice ? activeStyle : {} ),
				onClick: (evt) => {
					evt.stopPropagation();
					setActiveDevice( srcObj, setFn, deviceKey );
				}
			},
			renderFn( createElement )
		);

	return createElement(
		'div',
		{ style: { display: 'flex', alignItems: 'center', gap: 8 } },
		createElement( 'span', { style: { fontSize: 11, lineHeight: 1 } }, labelText ),
		createElement(
			'span',
			{ style: { display: 'flex', gap: 6, alignItems: 'center' } },
			makeIcon( __( 'Desktop > 1200', 'eac-components' ), IconDesktop, 'desktop' ),
			makeIcon( __( 'Tablet > 992', 'eac-components' ), IconTabletLand, 'tabletLand' ),
			makeIcon( __( 'Tablet > 768', 'eac-components' ), IconTablet, 'tablet' ),
			makeIcon( __( 'Mobile > 576', 'eac-components' ), IconMobileLand, 'mobileLand' ),
			makeIcon( __( 'Mobile > 320', 'eac-components' ), IconMobile, 'mobile' )
		)
	);
};

/**
 * Crée un récupérateur de couleurs du thème à partir d'une fonction de sélection de store.
 *
 * @returns {Function} Fonction getThemeColors qui retourne un tableau normalisé de couleurs:
 *                     [{ color: string, name?: string }, ...]
 */
export const getThemeColors = () => {
	/**
	 * Récupère et normalise les couleurs définies dans les settings de l'éditeur.
	 *
	 * Comportement :
	 * - Essaie 'core/block-editor'.getSettings() puis 'core/editor'.getSettings().
	 * - Accepte les formats : ['#fff', ...] ou [{ name, color }, ...] ou [{ color }, ...].
	 * - Retourne toujours un tableau d'objets { color: string, name?: string }.
	 *
	 * @returns {Array<{color: string, name?: string}>} Tableau normalisé de couleurs du thème.
	 */
	return () => {
		let settings = null;

		if (select) {
			const editorSettings =
				(select( 'core/block-editor' ) && select( 'core/block-editor' ).getSettings && select( 'core/block-editor' ).getSettings()) ||
				(select( 'core/editor' ) && select( 'core/editor' ).getSettings && select( 'core/editor' ).getSettings()) ||
				null;

			settings = editorSettings;
		}

		const themeColors = (settings && settings.colors) ? settings.colors : [];

		if ( ! Array.isArray( themeColors )) {
			return [];
		}

		return themeColors.map((colorEntry) => {
			if (typeof colorEntry === 'string') {
				return { color: colorEntry };
			}
			if (colorEntry && colorEntry.color) {
				return colorEntry;
			}
			return { color: String( colorEntry ) };
		});
	};
};

/** Création de la fonction CSS clamp */
function fmt(n) {
	const s = Number.parseFloat( n ).toFixed( precision );
	return s.replace( /\.?0+$/, '' );
}

function buildMiddleRemPlusVw(minRem, maxRem, vpMinPx, vpMaxPx, baseFs) {
	if (minRem === '' || maxRem === '' || minRem === null || maxRem === null || Number( maxRem ) <= Number( minRem )) {
		return '';
	}
	const minR = Number( minRem );
	const maxR = Number( maxRem );
	if (Number.isNaN( minR ) || Number.isNaN( maxR )) {
		return '';
	}

	const minPx = minR * baseFs;
	const maxPx = maxR * baseFs;
	const deltaVp = vpMaxPx - vpMinPx;
	if (deltaVp === 0) {
		return '';
	}

	const slopePerPx = (maxPx - minPx) / deltaVp;
	const slopeVw = slopePerPx * 100.0;
	const offsetPx = minPx - (slopePerPx * vpMinPx);
	const offsetRem = offsetPx / baseFs;

	return `${fmt( offsetRem )}rem + ${fmt( slopeVw )}vw`;
}

export const buildClamp = (attrMin = '', attrMax = '') => {
	if (attrMin === '' || attrMax === '') {
		return '';
	}
	const middle = buildMiddleRemPlusVw( attrMin, attrMax, vpMin, vpMax, baseFontSize );
	if ( ! middle) {
		return '';
	}
	return `clamp( ${fmt( attrMin )}rem, ${middle}, ${fmt( attrMax )}rem )`;
};

// export ES module
export const getDefaultColorScheme = () => {
	try {
		if (typeof window !== 'undefined' && window.matchMedia) {
			return window.matchMedia( '(prefers-color-scheme: dark)' ).matches ? 'dark' : 'light';
		}
	} catch (e) {
	}
	return 'light';
}

// Enable/Disable mouse keyboard focus
document.body.addEventListener('mousedown', () => {
	if ( ! document.body.classList.contains( 'eac-using-mouse' )) {
		document.body.classList.add( 'eac-using-mouse' );
	}
});
document.body.addEventListener('keydown', () => {
	if (document.body.classList.contains( 'eac-using-mouse' )) {
		document.body.classList.remove( 'eac-using-mouse' );
	}
});

/**
 * debounce pour éviter les erreurs dans la console lors de la saisie d'un post ID
 * value : le post ID à surveiller
 * delay : le délai de debounce en ms (par défaut 500ms)
 * retourne la valeur debounced qui ne se met à jour qu'après le délai sans changement de la valeur d'entrée
 */
export const useDebounce = (value, delay = 500) => {
	const [debounced, setDebounced] = useState(value);

	useEffect(() => {
		const timer = setTimeout(() => setDebounced(value), delay);
		return () => clearTimeout(timer);
	}, [value, delay]);

	return debounced;
};

/** Vérifier si le post en cours est un template (pour adapter les sources de données) */
export const isTemplate = () => {
	try {
		if ( select && select('core/editor') && typeof select('core/editor').getEditedPostAttribute === 'function' ) {
			const postType = select('core/editor').getEditedPostAttribute('type');
			return postType === 'wp_template' || postType === 'wp_template_part';
		}
	} catch (e) { }
	return false;
};

/** Récupérer l'ID du post en cours d'édition */
export const getEditorPostId = () => {
	try {
		if (select && select('core/editor') && typeof select('core/editor').getCurrentPostId === 'function') {
			return select('core/editor').getCurrentPostId();
		}
	} catch (e) { }
	return 0;
};

/** Récupérer l'ID de l'auteur du post en cours d'édition */
export const getEditorPostAuthorId = () => {
	try {
		if (isTemplate()) {
			return 1;
		}
		if (select && select('core/editor') && typeof select('core/editor').getEditedPostAttribute === 'function') {
			return select('core/editor').getEditedPostAttribute('author');
		}
	} catch (e) { }
	return 0;
};