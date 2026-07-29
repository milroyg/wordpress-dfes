<?php
/**
 * Code météo
 * @since 2.5.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Mapping des weathercodes → libellés EN avec traductions
 */

$code_meteo = array(
	0  => array(
		'en' => 'Clear sky',
		'fr' => 'Ciel clair',
		'es' => 'Cielo despejado',
		'it' => 'Cielo sereno',
		'hi' => 'साफ आसमान',
	),
	1  => array(
		'en' => 'Mainly clear',
		'fr' => 'Principalement clair',
		'es' => 'Principalmente despejado',
		'it' => 'Principalmente sereno',
		'hi' => 'मुख्य रूप से साफ',
	),
	2  => array(
		'en' => 'Partly cloudy',
		'fr' => 'Partiellement nuageux',
		'es' => 'Parcialmente nublado',
		'it' => 'Parzialmente nuvoloso',
		'hi' => 'आंशिक रूप से बादल',
	),
	3  => array(
		'en' => 'Overcast',
		'fr' => 'Couvert',
		'es' => 'Nublado',
		'it' => 'Nuvoloso',
		'hi' => 'अधिक बादल',
	),
	45  => array(
		'en' => 'Fog',
		'fr' => 'Brouillard',
		'es' => 'Niebla',
		'it' => 'Nebbia',
		'hi' => 'कोहरा',
	),
	48  => array(
		'en' => 'Depositing rime fog',
		'fr' => 'Brouillard givrant',
		'es' => 'Niebla escarcha',
		'it' => 'Nebbia di galaverna',
		'hi' => 'तुषार कोहरा',
	),
	51  => array(
		'en' => 'Light drizzle',
		'fr' => 'Bruine légère',
		'es' => 'Llovizna ligera',
		'it' => 'Pioggia leggera intermittente',
		'hi' => 'हल्की बूँदें',
	),
	53  => array(
		'en' => 'Moderate drizzle',
		'fr' => 'Bruine modérée',
		'es' => 'Llovizna moderada',
		'it' => 'Pioggia moderata intermittente',
		'hi' => 'मध्यम बूँदें',
	),
	55  => array(
		'en' => 'Dense drizzle',
		'fr' => 'Bruine dense',
		'es' => 'Llovizna densa',
		'it' => 'Pioggia intensa intermittente',
		'hi' => 'गहरी बूँदें',
	),
	56  => array(
		'en' => 'Light freezing drizzle',
		'fr' => 'Bruine verglaçante légère',
		'es' => 'Llovizna congelante ligera',
		'it' => 'Pioggia leggera congelante intermittente',
		'hi' => 'हल्की ठंडी बूँदें',
	),
	57  => array(
		'en' => 'Dense freezing drizzle',
		'fr' => 'Bruine verglaçante dense',
		'es' => 'Llovizna congelante densa',
		'it' => 'Pioggia intensa congelante intermittente',
		'hi' => 'गहरी ठंडी बूँदें',
	),
	61  => array(
		'en' => 'Light rain',
		'fr' => 'Pluie légère',
		'es' => 'Lluvia ligera',
		'it' => 'Pioggia leggera',
		'hi' => 'हल्की वर्षा',
	),
	63  => array(
		'en' => 'Moderate rain',
		'fr' => 'Pluie modérée',
		'es' => 'Lluvia moderada',
		'it' => 'Pioggia moderata',
		'hi' => 'मध्यम वर्षा',
	),
	65  => array(
		'en' => 'Heavy rain',
		'fr' => 'Pluie forte',
		'es' => 'Lluvia fuerte',
		'it' => 'Pioggia intensa',
		'hi' => 'भारी वर्षा',
	),
	66  => array(
		'en' => 'Light freezing rain',
		'fr' => 'Pluie verglaçante légère',
		'es' => 'Lluvia congelante ligera',
		'it' => 'Pioggia leggera congelante',
		'hi' => 'हल्की जमने वाली वर्षा',
	),
	67  => array(
		'en' => 'Heavy freezing rain',
		'fr' => 'Pluie verglaçante forte',
		'es' => 'Lluvia congelante fuerte',
		'it' => 'Pioggia intensa congelante',
		'hi' => 'भारी जमने वाली वर्षा',
	),
	71  => array(
		'en' => 'Light snow',
		'fr' => 'Neige légère',
		'es' => 'Nieve ligera',
		'it' => 'Neve leggera',
		'hi' => 'हल्की बर्फ',
	),
	73  => array(
		'en' => 'Moderate snow',
		'fr' => 'Neige modérée',
		'es' => 'Nieve moderada',
		'it' => 'Neve moderata',
		'hi' => 'मध्यम बर्फ',
	),
	75  => array(
		'en' => 'Heavy snow',
		'fr' => 'Neige forte',
		'es' => 'Nieve fuerte',
		'it' => 'Neve intensa',
		'hi' => 'भारी बर्फ',
	),
	77  => array(
		'en' => 'Snow grains',
		'fr' => 'Grains de neige',
		'es' => 'Granos de nieve',
		'it' => 'Chicchi di neve',
		'hi' => 'बर्फ के दाने',
	),
	80  => array(
		'en' => 'Slight rain showers',
		'fr' => 'Averses de pluie légères',
		'es' => 'Chubascos de lluvia ligeros',
		'it' => 'Rovesci di pioggia leggeri',
		'hi' => 'हल्की वर्षा की बौछारें',
	),
	81  => array(
		'en' => 'Moderate rain showers',
		'fr' => 'Averses de pluie modérées',
		'es' => 'Chubascos de lluvia moderados',
		'it' => 'Rovesci di pioggia moderati',
		'hi' => 'मध्यम वर्षा की बौछारें',
	),
	82  => array(
		'en' => 'Violent rain showers',
		'fr' => 'Averses de pluie violentes',
		'es' => 'Chubascos de lluvia violentos',
		'it' => 'Rovesci di pioggia violenti',
		'hi' => 'हिंसक वर्षा की बौछारें',
	),
	85  => array(
		'en' => 'Slight snow showers',
		'fr' => 'Averses de neige légères',
		'es' => 'Chubascos de nieve ligeros',
		'it' => 'Rovesci di neve leggeri',
		'hi' => 'हल्की बर्फ की बौछारें',
	),
	86  => array(
		'en' => 'Heavy snow showers',
		'fr' => 'Averses de neige fortes',
		'es' => 'Chubascos de nieve fuertes',
		'it' => 'Rovesci di neve intensi',
		'hi' => 'भारी बर्फ की बौछारें',
	),
	95  => array(
		'en' => 'Thunderstorm',
		'fr' => 'Orage',
		'es' => 'Tormenta',
		'it' => 'Temporale',
		'hi' => 'गरज के साथ वर्षा',
	),
	96  => array(
		'en' => 'Thunderstorm with slight hail',
		'fr' => 'Orage avec grêle légère',
		'es' => 'Tormenta con granizo ligero',
		'it' => 'Temporale con grandine leggera',
		'hi' => 'हल्के ओले के साथ गरज',
	),
	99  => array(
		'en' => 'Thunderstorm with heavy hail',
		'fr' => 'Orage avec grêle forte',
		'es' => 'Tormenta con granizo fuerte',
		'it' => 'Temporale con grandine intensa',
		'hi' => 'भारी ओले के साथ गरज',
	),
);
return $code_meteo;
