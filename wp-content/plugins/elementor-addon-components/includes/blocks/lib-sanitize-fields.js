/**
 * lib-sanitize-fields.js - nettoyage des controls texte
 * @since 2.4.8
 * @since 2.5.0 ajout de sanitizeInput pour FormTokenField
 * @since 2.5.0 ajout de l'option stripLeadingZeros à sanitizeDigits
 */

// utilitaires
export const sanitizeDigits = (value, { stripLeadingZeros = false } = {}) => {
	if (value == null) {
		return '';
	}
	// Convertit en string, supprime tout sauf chiffres
	const digitsOnly = String( value ).replace( /\D+/g, '' );
	if ( ! stripLeadingZeros) {
		return digitsOnly;
	}
	// Supprime les zéros en tête (garde "0" si tout est zéro)
	const trimmed = digitsOnly.replace( /^0+(?=\d)/, '' );
	return trimmed;
};

export const sanitizeFloatInput = (raw) => {
	const s = String( raw ).replace( ',', '.' );          // virgule -> point
	// garder uniquement chiffres et points, puis n'autoriser qu'un point
	const cleaned = s.replace( /[^0-9.]+/g, '' ).replace( /^\.*/, '' ).replace( /\.{2,}/g, '.' );
	const firstDotIndex = cleaned.indexOf( '.' );
	const normalized = firstDotIndex === -1
		? cleaned
		: cleaned.slice( 0, firstDotIndex + 1 ) + cleaned.slice( firstDotIndex + 1 ).replace( /\./g, '' );
	return normalized;
};

// Fonction de sanitization de FormTokenField pour rejeter les entrées potentiellement dangereuses (balises HTML, event handlers, caractères spéciaux, scripts, données encodées)
export const sanitizeInput = (value) => {
	// Rejeter les balises HTML
	if (/<[^>]*>/g.test(value)) return false;

	// Rejeter les event handlers
	if (/on\w+\s*=/i.test(value)) return false;

	// Rejeter les caractères dangereux
	if (/[<>'"]/g.test(value)) return false;

	// Rejeter les scripts
	if (/javascript:/i.test(value)) return false;

	// Rejeter les données encodées
	if (/%3C|%3E|&#/i.test(value)) return false;

	return true;
};