import { parse } from '@wordpress/blocks';
import apiFetch from '@wordpress/api-fetch';
import { select, subscribe } from '@wordpress/data';
import { getBlockType } from '@wordpress/blocks';
import verticalBlockCss from '../blocks/vertical/dynamicCss';
import horizontalBlockCss from '../blocks/horizontal/dynamicCss';
import gridBlockCss from '../blocks/grid/dynamicCss';
import tabsBlockCss from '../blocks/tabs/dynamicCss';
import tableBlockCss from '../blocks/table/dynamicCss';
import windyMapBlocksCss from '../blocks/windy-map/dynamicCss';
import aqiMinimalBlockCss from '../blocks/aqi-minimal/dynamicCss';
import sectionHeadingBlockCss from '../blocks/section-heading/dynamicCss';

const blockCssGenerator = {
	'sp-location-weather-pro/vertical-card': verticalBlockCss,
	'sp-location-weather-pro/horizontal': horizontalBlockCss,
	'sp-location-weather-pro/tabs': tabsBlockCss,
	'sp-location-weather-pro/grid': gridBlockCss,
	'sp-location-weather-pro/table': tableBlockCss,
	'sp-location-weather-pro/windy-map': windyMapBlocksCss,
	'sp-location-weather-pro/aqi-minimal': aqiMinimalBlockCss,
	'sp-location-weather-pro/section-heading': sectionHeadingBlockCss,
};

// Recursively process blocks to collect CSS, fonts, and detect custom blocks.
const processBlocksRecursively = ( blocks ) => {
	if ( ! blocks || ! blocks.length ) {
		return { css: '', fonts: [], hasOurBlock: false };
	}

	let combinedCSS = '';
	let fonts = [];
	let hasOurBlock = false;

	blocks.forEach( ( block ) => {
		const uniqueId = block.attributes?.uniqueId;

		// Detect your custom blocks
		if ( uniqueId && uniqueId.startsWith( 'spl-weather' ) ) {
			hasOurBlock = true;

			const blockFonts = block?.attributes?.fontLists ?? '';
			if ( typeof blockFonts === 'string' && blockFonts.trim() ) {
				try {
					const parsed = JSON.parse( blockFonts );
					if ( Array.isArray( parsed ) ) {
						fonts.push(
							...parsed.filter(
								( f ) => typeof f === 'string' && f.trim()
							)
						);
					}
				} catch {
					// Case: single string -> "Roboto:400"
					if ( /^[A-Za-z0-9\s]+:\d+$/.test( blockFonts ) ) {
						fonts.push( blockFonts );
					}
				}
			}

			// Generate CSS for this block
			const blockCss = blockCssGenerator[ block.name ];
			if ( blockCss ) {
				const css = blockCss( block.attributes, 'frontend' );
				combinedCSS += `/* CSS for ${ block.name } - ${ uniqueId } */\n${ css }\n`;
			}
		}

		// Recurse into inner blocks
		if ( block.innerBlocks?.length ) {
			const {
				css: childCSS,
				fonts: childFonts,
				hasOurBlock: childHas,
			} = processBlocksRecursively( block.innerBlocks );

			combinedCSS += childCSS;
			fonts.push( ...childFonts );
			if ( childHas ) hasOurBlock = true;
		}
	} );

	// Deduplicate and validate fonts
	const uniqueFonts = [
		...new Set(
			fonts.filter(
				( f ) =>
					typeof f === 'string' && /^[A-Za-z0-9\s]+:\d+$/.test( f )
			)
		),
	];

	return { css: combinedCSS, fonts: uniqueFonts, hasOurBlock };
};

// Parse content → return CSS/fonts/flags
const collectBlockAssets = ( content ) => {
	if ( ! content || typeof content !== 'string' ) {
		return { css: '', fonts: [], hasOurBlock: false };
	}

	let blocks = [];
	try {
		blocks = parse( content );
	} catch ( err ) {
		console.error( 'Error parsing blocks:', err );
		return { css: '', fonts: [], hasOurBlock: false };
	}

	return processBlocksRecursively( blocks );
};

// Collect reusable block refs from core/block blocks
const collectReusableRefs = ( blocks, set = new Set() ) => {
	if ( ! blocks || ! blocks.length ) return set;
	blocks.forEach( ( block ) => {
		if ( block.name === 'core/block' && block.attributes?.ref ) {
			set.add( block.attributes.ref );
		}
		if ( block.innerBlocks?.length )
			collectReusableRefs( block.innerBlocks, set );
	} );
	return set;
};

// Call API to save CSS/fonts
const saveToApi = async ( {
	postId,
	css,
	fonts,
	preview = false,
	blockType = '',
	refId = '',
	slug = '',
	widgetId = '',
	theme = '',
	hasBlock = false,
	hasRefs = false,
} ) => {
	try {
		await apiFetch( {
			path: '/spl-weather/v2/weather-save-block-css',
			method: 'POST',
			data: {
				post_id: postId,
				ref_id: refId,
				slug,
				widget_id: widgetId,
				theme,
				block_css: css || '',
				fonts: fonts || [],
				preview,
				block_type: blockType,
				has_block: hasBlock,
				has_refs: hasRefs,
			},
		} );
	} catch ( err ) {
		console.error(
			`CSS save error (postId: ${ postId }, refId: ${ refId || 'N/A' })`,
			err
		);
	}
};

/**
 * Main save handler for Weather Block CSS.
 * - Watches saves for postType entities (posts/pages/templates/widgets).
 * - Parses content, collects reusable refs.
 * - For each ref: fetch reusable block, parse, if has our block -> POST to API with block_type = wp_block.
 * - Also sends API for the main post if it has our block.
 */
const saveBlockCSS = () => {
	/**
	 * 1. Handle Preview Button
	 */
	document.addEventListener( 'click', ( e ) => {
		if ( e.target.matches( '.editor-preview-dropdown__button-external' ) ) {
			( async () => {
				const { getCurrentPostId, getEditedPostContent } =
					select( 'core/editor' );

				const postId = getCurrentPostId();
				const { css, fonts, hasOurBlock } = collectBlockAssets(
					getEditedPostContent()
				);

				await saveToApi( {
					postId,
					css,
					fonts,
					preview: true,
					hasBlock: hasOurBlock,
				} );
			} )();
		}
	} );

	/**
	 * 2. Handle Post/Page/Template/Widget Saves
	 */
	let previousSaving = false;

	subscribe( () => {
		const {
			__experimentalGetDirtyEntityRecords,
			isSavingEntityRecord,
			getEditedEntityRecord,
		} = select( 'core' );
		const coreEditor = select( 'core/editor' );

		const dirtyEntities = __experimentalGetDirtyEntityRecords();

		// Detect a real save (not autosave).
		const isSaving = dirtyEntities.some( ( record ) => {
			const entity = getEditedEntityRecord(
				record.kind,
				record.name,
				record.key
			);

			if ( ! entity ) {
				return false;
			}
			return isSavingEntityRecord( record.kind, record.name, record.key );
		} );

		const isAutosavingPost = coreEditor?.isAutosavingPost
			? () => coreEditor.isAutosavingPost()
			: () => false;
		const shouldTrigger =
			! previousSaving && ! isAutosavingPost() && isSaving;

		if ( ! shouldTrigger ) {
			previousSaving = isSaving;
			return;
		}

		( async () => {
			const processedRefs = new Set();

			for ( const entity of dirtyEntities ) {
				if ( entity.kind !== 'postType' && entity.name !== 'widget' ) {
					continue;
				}

				const record = getEditedEntityRecord(
					entity.kind,
					entity.name,
					entity.key
				);

				// Determine postId
				let postId = record?.id || entity.name || 'unknown';
				if (
					entity.name === 'wp_template_part' ||
					entity.name === 'wp_template' ||
					entity.name === 'widget'
				) {
					postId = entity.name;
				}
				if ( entity.name === 'wp_block' ) {
					record.slug = 'wp_block';
				}

				// Extract content + assets
				let content = '';
				let widgetId = '';
				if ( entity.name === 'widget' ) {
					content = record.instance?.raw?.content || '';
					widgetId = record?.id || '';
				} else if ( typeof record.content === 'string' ) {
					content = record.content;
				} else if ( typeof record.content === 'function' ) {
					try {
						if ( record.blocks ) {
							if (
								wp.blocks &&
								wp.blocks.__unstableSerializeAndClean
							) {
								content = wp.blocks.__unstableSerializeAndClean(
									record.blocks
								);
							} else {
								content = '';
							}
						} else {
							content = '';
						}
					} catch ( error ) {
						// eslint-disable-next-line no-console
						console.log(
							'Error processing content function:',
							error
						);
						content = '';
					}
				} else if (
					record.content &&
					typeof record.content === 'object'
				) {
					content = record.content.raw || '';
				} else {
					content = '';
				}
				if ( ! content || typeof content !== 'string' ) {
					continue;
				}

				const { css, fonts, hasOurBlock } =
					collectBlockAssets( content );

				// Collect all reusable refs used inside this content
				const refsSet = collectReusableRefs( parse( content ) );

				await saveToApi( {
					postId,
					css,
					fonts,
					preview: false,
					slug: record?.slug || '',
					widgetId,
					theme: record?.theme || '',
					hasBlock: hasOurBlock,
					hasRefs: refsSet?.size > 0,
				} );

				if ( ! refsSet || refsSet.size === 0 ) {
					continue;
				}

				// Handle reusable blocks
				for ( const refId of refsSet ) {
					if ( processedRefs.has( refId ) ) continue;
					processedRefs.add( refId );

					try {
						const reusable = await apiFetch( {
							path: `/wp/v2/blocks/${ refId }`,
						} );

						const rawContent =
							reusable?.content?.raw ??
							reusable?.content?.rendered ??
							( typeof reusable?.content === 'string'
								? reusable.content
								: '' );

						if ( ! rawContent || typeof rawContent !== 'string' ) {
							continue;
						}

						const {
							css: innerCSS,
							fonts: innerFonts,
							hasOurBlock: innerHas,
						} = collectBlockAssets( rawContent );

						await saveToApi( {
							postId,
							refId,
							css: innerCSS,
							fonts: innerFonts,
							preview: false,
							blockType: 'wp_block',
							slug: record?.slug || '',
							theme: record?.theme || '',
							hasBlock: innerHas,
						} );
					} catch ( err ) {
						console.error(
							`Error processing reusable block ${ refId }:`,
							err
						);
					}
				}
			}
		} )();

		previousSaving = isSaving;
	} );
};

export default saveBlockCSS;
