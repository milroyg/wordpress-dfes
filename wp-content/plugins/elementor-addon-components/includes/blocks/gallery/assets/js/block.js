/**
 * EAC Gallery Block JS
 * @since 2.4.8
 */

import { IconNone, IconJustify, IconWide, IconFull, IconLeft, IconCenter, IconRight, IconBetween, IconAround, IconStart, IconEnd, IconH2, IconH3, IconH4, IconDiv } from '../../../lib-icons.js';
import { getValueForActiveDevice, setValueForActiveDevice, createLabelWithIcons, getThemeColors, buildClamp, useDebounce, isTemplate, getEditorPostId, getEditorPostAuthorId } from '../../../lib-blocks.js';
import { sanitizeFloatInput } from '../../../lib-sanitize-fields.js';

(function () {
	'use strict';

	if (window.wp === undefined) {
		console.warn('Block.js: window.wp undefined');
		return;
	}

	/* Ultra-minimal préambule block.js — compatible WP 6.5 */
	const { blocks, element, components, blockEditor, i18n, serverSideRender, data } = window.wp;
	const { createElement, Fragment, useState, useEffect } = element || {};
	const { registerBlockType } = blocks || {};
	const { BaseControl, PanelBody, SelectControl, TextControl, RangeControl, Spinner, ToolbarDropdownMenu, ToggleControl, Button } = components || {};
	const { InspectorControls, useBlockProps, PanelColorSettings, BlockControls, ContrastChecker } = blockEditor || {};
	const { __ } = i18n || {};
	const apiFetch = wp.apiFetch || {};
	/* fin préambule sécurisé */

	registerBlockType('eac-blocks/gallery', {
		edit: function (props) {
			const { attributes = {}, setAttributes, className = '' } = props || {};
			const {
				containerWidth,
				selectedGallery,
				postSource,
				postId,
				galleryCol,
				galleryGap,
				fontText,
				marginTopBottom,
				blockBackground,
				itemBackground,
				colorText,
				alignmentHrzText,
				alignmentVrtText,
				itemStyle,
				itemBorder,
				itemBorderRadius,
				imageSizes,
				imageRatio,
				imagePosition,
				globalLink,
				nofollowLink,
				addDescription,
				addFancybox,
				previewImg,
				headingCaption,
			} = attributes || {};
			const { fontMin = null, fontMax = null, fontClamp = null } = fontText || {};
			const { marginSup = null, marginInf = null, unit = null } = marginTopBottom || {};
			const { desktopCol = null, tabletLandCol = null, tabletCol = null, mobileLandCol = null, mobileCol = null } = galleryCol || {};
			const { gapMin = null, gapMax = null, gapClamp = null } = galleryGap || {};

			// Hooks sûrs
			if (!useState || !useEffect) {
				return createElement('div', { className: className, style: { padding: '12px', border: '1px dashed #ccc' } },
					__('Editor not available (missing React hooks)', 'eac-components')
				);
			}

			const getSourceId = () => {
				if (postSource === 'current') {
					return getEditorPostId();
				} else if (postSource === 'other') {
					return postId;
				} else if (postSource === 'author') {
					return getEditorPostAuthorId();
				}
				return null;
			};

			const [galleries, setGalleries] = useState([]);
			const [loadingGalleries, setLoadingGalleries] = useState(false);
			const [errorGalleries, setErrorGalleries] = useState(null);
			const [remoteSizes, setRemoteSizes] = useState(null);
			const [sizesError, setSizesError] = useState(false);

			const themeColors = getThemeColors();
			const sourceId = getSourceId();
			const shouldDebounce = postSource === 'other';
			// Debouncer le sourceId pour éviter des requêtes à chaque frappe lors de la saisie d'un postId manuel, mais ne pas débouncer pour les autres sources (current, author) qui sont stables
			const debouncedSourceId = useDebounce(sourceId, shouldDebounce ? 500 : 0);

			/** Gestion des événements pour changement postSource et debouncedSourceId (postId ou authorId), charge les galleries */
			useEffect(() => {
				let mounted = true;
				setLoadingGalleries(true);
				setErrorGalleries(null);

				if (!debouncedSourceId || Number(debouncedSourceId) <= 0) {
					// pas de requête si pid invalide
					setGalleries([]);
					setErrorGalleries(false);
					setLoadingGalleries(false);
					return () => { mounted = false; };
				}

				const endpoint = postSource === 'author'
					? `/eac-blocks/v1/acf-gallery-author/${debouncedSourceId}`
					: `/eac-blocks/v1/acf-gallery/${debouncedSourceId}`;

				apiFetch({path: endpoint, method: 'GET', headers: { 'Accept': 'application/json' } })
					.then(result => {
						if (!mounted) return;
						const list = result && Array.isArray(result) ? result : [];
						setGalleries(list);
						setErrorGalleries(false);
					})
					.catch((error) => {
						if (!mounted) return;
						console.error('Error fetching galleries:', error);
						setGalleries([]);
						setErrorGalleries(true);
					})
					.finally(() => {
						if (!mounted) return;
						setLoadingGalleries(false);
					});

				return () => { mounted = false; };
			}, [postSource, debouncedSourceId]);

			/** Gestion de la liste des tailles d'images appelé au chargement uniquement pas de dépendances */
			useEffect(() => {
				let mounted = true;

				apiFetch({ path: '/eac-blocks/v1/image-sizes', method: 'GET', headers: { 'Accept': 'application/json' } })
					.then(data => { if (!mounted) return; setRemoteSizes(data || {}); setSizesError(false); })
					.catch((error) => { if (!mounted) return; setRemoteSizes({}); setSizesError(true); });

				return () => { mounted = false; };
			}, []);

			const wrapperProps = (typeof useBlockProps === 'function')
				? useBlockProps({ className: (className || '') + ' eac-gallery-editor-preview' })
				: { className: (className || '') + ' eac-gallery-editor-preview' };

			const preview = createElement('div', wrapperProps,
				selectedGallery && serverSideRender
					? createElement(serverSideRender, { block: 'eac-blocks/gallery', attributes: attributes })
					: createElement('div', { style: { padding: '12px', border: '1px dashed #ccc' } },
						loadingGalleries ? (Spinner ? createElement(Spinner) : createElement('span', null, 'Loading...')) : __('Select a gallery to see a preview', 'eac-components')
					)
			);

			const renderSourcePanel = () => {
				return createElement(PanelBody, { title: __('Source', 'eac-components'), initialOpen: true },
					createElement(SelectControl, {
						label: __('Post source', 'eac-components'),
						__next40pxDefaultSize: true,
						__nextHasNoMarginBottom: true,
						value: postSource,
						options: [
							{ label: __('Current post', 'eac-components'), value: 'current' },
							{ label: __('Other post ID', 'eac-components'), value: 'other' },
							{ label: __('Author', 'eac-components'), value: 'author' },
						],
						help: postSource === 'author' && ! isTemplate()
							? __('It is preferable to use this option in a template', 'eac-components')
							: undefined,
						onChange: function (val) {
							if (val === postSource) return;

							const newAttributes = {
								selectedGallery: '',
							};

							if (val === 'current') {
								newAttributes.postSource = 'current';
								newAttributes.postId = 0;
							} else if (val === 'other') {
								newAttributes.postSource = 'other';
								newAttributes.postId = 0;
							} else if (val === 'author') {
								newAttributes.postSource = 'author';
								newAttributes.postId = 0;
							}

							setAttributes(newAttributes);

							// vider UI immédiatement — le useEffect se chargera du fetch
							setGalleries([]);
							setLoadingGalleries(true);
							setErrorGalleries(null);
						},
					}),
					postSource === 'other' && createElement(TextControl, {
						label: __('Post ID', 'eac-components'),
						__next40pxDefaultSize: true,
						__nextHasNoMarginBottom: true,
						value: postId ? String(postId) : '',
						onChange: (val) => {
							const digits = String(val).replace(/\D+/g, '');
							const pid = digits === '' ? 0 : parseInt(digits, 10);
							setAttributes({ postId: pid });

							// vider UI immédiatement — le useEffect se chargera du fetch
							setGalleries([]);
							setLoadingGalleries(true);
							setErrorGalleries(null);
						},
					}),
					createElement(SelectControl, {
						label: __('Select gallery', 'eac-components'),
						__next40pxDefaultSize: true,
						__nextHasNoMarginBottom: true,
						value: selectedGallery,
						options: (() => {
							let base = [];
							if (galleries && galleries.length) {
								base = galleries.map(function (r) { return { label: r.label, value: r.key }; });
								base.unshift({ label: __('Select...', 'eac-components'), value: '' });
							} else {
								base = [{ label: loadingGalleries ? 'Loading...' : __('No gallery found', 'eac-components'), value: '' }];
							}
							return base;
						})(),
						onChange: (v) => {
							if (v !== selectedGallery) {
								setAttributes({ selectedGallery: v });
							}
						},
					}),
				);
			};

			const renderLayoutPanel = () => {
				const sourceCol = galleryCol;
				const setFn = (obj) => setAttributes({ galleryCol: obj });
				const setHeadingCaption = (iconEl, value, current, onClick, label) => {
					return createElement(Button,
						{
							isSecondary: true,
							onClick: function () { onClick(value); },
							'aria-pressed': current === value,
							className: 'eac-heading-btn' + (current === value ? ' is-active' : ''),
							title: label
						},
						iconEl
					);
				};

				return createElement(PanelBody, { title: __('Layout', 'eac-components'), initialOpen: false },
					createElement(TextControl, {
						label: createLabelWithIcons(createElement, __('Columns', 'eac-components'), sourceCol, setFn),
						__next40pxDefaultSize: true,
						__nextHasNoMarginBottom: true,
						value: getValueForActiveDevice(sourceCol),
						onChange: (val) => {
							const digits = String(val).replace(/\D+/g, '');
							setValueForActiveDevice(sourceCol, setFn, digits);
						},
						type: 'number',
						min: 1,
						max: 6,
						step: 1
					}),
					createElement(BaseControl, {
						label: __('Caption tag', 'eac-components'),
						__nextHasNoMarginBottom: true
					},
						createElement('div',
							{ className: 'eac-heading-icon-row', style: { display: 'flex', columnGap: '8px', justifyContent: 'flex-start', alignItems: 'center', marginBlockStart: '4px' } },
							setHeadingCaption(IconH2(createElement), 'h2', headingCaption, function (v) { setAttributes({ headingCaption: v }); }, 'H2'),
							setHeadingCaption(IconH3(createElement), 'h3', headingCaption, function (v) { setAttributes({ headingCaption: v }); }, 'H3'),
							setHeadingCaption(IconH4(createElement), 'h4', headingCaption, function (v) { setAttributes({ headingCaption: v }); }, 'H4'),
							setHeadingCaption(IconDiv(createElement), 'div', headingCaption, function (v) { setAttributes({ headingCaption: v }); }, 'DIV')
						)
					),
				);
			};

			const renderImagePanel = () => {
				const rawSizes = remoteSizes || {};
				const options = Object.keys(rawSizes).map(key => ({ value: key, label: rawSizes[key] }));
				options.unshift({ value: '', label: __('Select...', 'eac-components') });
				const currentValue = imageSizes || 'medium';
				const currentPosition = (typeof imagePosition === 'number') ? imagePosition : 50;

				const onChangePosition = (val) => {
					const num = Number(val);
					const clamped = isNaN(num) ? 50 : Math.max(0, Math.min(100, Math.round(num)));
					setAttributes({ imagePosition: clamped });
				};

				return createElement(PanelBody, { title: __('Image', 'eac-components'), initialOpen: false },
					remoteSizes === null
						? createElement('div', null, __('Loading...', 'eac-components'))
						: sizesError
							? createElement('div', { style: { color: 'red' } }, __('Failed to load sizes', 'eac-components'))
							: null,
					createElement(SelectControl, {
						label: __('Resolution', 'eac-components'),
						__next40pxDefaultSize: true,
						__nextHasNoMarginBottom: true,
						value: currentValue,
						options: options,
						onChange: (newVal) => setAttributes({ imageSizes: newVal || '' }),
					}),
					!sizesError
						? createElement('div', { style: { display: 'flex', gap: '15px', marginBlockEnd: '8px', alignItems: 'flex-start' } },
							createElement('div', { style: { flex: '0 0 90px' } },
								createElement(SelectControl, {
									label: __('Ratio', 'eac-components'),
									__next40pxDefaultSize: true,
									__nextHasNoMarginBottom: true,
									value: imageRatio,
									options: [
										{ label: __('Default', 'eac-components'), value: '' },
										{ label: __('Square', 'eac-components'), value: '1 / 1' },
										{ label: __('Standard', 'eac-components'), value: '4 / 3' },
										{ label: __('Classic', 'eac-components'), value: '3 / 2' },
										{ label: __('Wide', 'eac-components'), value: '16 / 9' },
										{ label: __('Tall', 'eac-components'), value: '9 / 16' }
									],
									onChange: (newVal) => setAttributes({ imageRatio: newVal || '' }),
								})
							),
							imageRatio && String(imageRatio) !== ''
								? createElement('div', { style: { flex: '1 1 0', minInlineSize: 0 } },
									createElement(RangeControl, {
										label: __('Vertical position (%)', 'eac-components'),
										__next40pxDefaultSize: true,
										__nextHasNoMarginBottom: true,
										value: currentPosition,
										onChange: onChangePosition,
										type: 'number',
										min: 5,
										max: 100,
										step: 5
									})
								)
								: null,
						)
						: null,
					createElement('div', { style: { marginTop: '15px' } },
						createElement(ToggleControl, {
							label: __('Add description', 'eac-components'),
							__nextHasNoMarginBottom: true,
							checked: !!addDescription,
							onChange: (value) => { setAttributes({ addDescription: value }); },
						})
					),
					createElement('div', { style: { marginTop: '15px' } },
						createElement(ToggleControl, {
							label: __('Lightbox on image', 'eac-components'),
							__nextHasNoMarginBottom: true,
							checked: !!addFancybox,
							onChange: (value) => { setAttributes({ addFancybox: value }); },
						})
					)
				);
			};

			const renderLinkPanel = () => {
				return createElement(PanelBody, { title: __('Link', 'eac-components'), initialOpen: false },
					createElement('span', { style: { display: 'block', marginBlockEnd: '8px', color: '#666', fontSize: '13px' } },
						__('If the gallery has a URL, link or page link', 'eac-components')
					),
					addFancybox === false
						? createElement(ToggleControl, {
							label: __('Enable the link globally', 'eac-components'),
							__nextHasNoMarginBottom: true,
							checked: !!globalLink,
							onChange: (value) => { setAttributes({ globalLink: value }); },
							help: globalLink
								? __('Each item will link to the URL', 'eac-components')
								: __('Items will not have a global link', 'eac-components')
						}
						)
						: null,
					createElement(ToggleControl, {
						label: 'Nofollow',
						__nextHasNoMarginBottom: true,
						checked: !!nofollowLink,
						onChange: (value) => { setAttributes({ nofollowLink: value }); },
						help: nofollowLink
							? __('Target link will not be indexed by search engines', 'eac-components')
							: __('Target link will be indexed by search engines', 'eac-components')
					}
					),
				);
			};

			const renderSpacingPanel = () => {
				const gapAttr = galleryGap || {};
				const currentMin = gapAttr.gapMin ?? '';
				const currentMax = gapAttr.gapMax ?? '';

				const onMarginChange = (side, value) => {
					const parsed = value === '' ? null : (value === null ? null : Number(value));
					const newSpacing = Object.assign({}, (marginTopBottom || {}), {});
					if (side === 'top') newSpacing.marginSup = parsed;
					else if (side === 'bottom') newSpacing.marginInf = parsed;
					setAttributes({ marginTopBottom: newSpacing });
				};

				const onSpacingUnitChange = (unit) => {
					const newSpacing = Object.assign({}, (marginTopBottom || {}), { unit: unit || 'px' });
					setAttributes({ marginTopBottom: newSpacing });
				};

				const onChangeAttr = (key, val) => {
					const next = Object.assign({}, galleryGap || {});
					if (key === 'gapMin' || key === 'gapMax') {
						if (val === '' || val === null) {
							delete next[key];
						} else {
							const num = Number(val);
							next[key] = Number.isNaN(num) ? val : num;
						}
					} else {
						next[key] = val;
					}
					setAttributes({ galleryGap: next });
				};

				// Synchroniser clampString avec gapClamp à chaque changement
				useEffect(() => {
					const nextClamp = buildClamp(galleryGap?.gapMin ?? '', galleryGap?.gapMax ?? '');
					if (nextClamp !== (galleryGap?.gapClamp || '')) {
						const next = Object.assign({}, galleryGap || {});
						next.gapClamp = nextClamp;
						setAttributes({ galleryGap: next });
					}
				}, [galleryGap?.gapMin, galleryGap?.gapMax]);

				const clampString = buildClamp(galleryGap?.gapMin ?? '', galleryGap?.gapMax ?? '');

				return createElement(PanelBody, { title: __('Spacing', 'eac-components'), initialOpen: true, className: 'eac-section-spacing' },
					createElement('div', { style: { display: 'flex', gap: '8px', marginBlockEnd: '8px', alignItems: 'start' } },
						createElement('div', { style: { flex: '1 1 0', minInlineSize: 0 } },
							createElement(TextControl, {
								label: __('Min (rem)', 'eac-components'),
								__next40pxDefaultSize: true,
								__nextHasNoMarginBottom: true,
								value: currentMin === undefined || currentMin === null ? '' : String(currentMin),
								onChange: (v) => {
									const normalized = sanitizeFloatInput(v);
									onChangeAttr('gapMin', normalized === '' ? '' : parseFloat(normalized));
								},
								type: 'number',
								min: 0,
								step: 0.1,
								help: __('Minimum gap size', 'eac-components')
							})
						),
						createElement('div', { style: { flex: '1 1 0', minInlineSize: 0 } },
							createElement(TextControl, {
								label: __('Max (rem)', 'eac-components'),
								__next40pxDefaultSize: true,
								__nextHasNoMarginBottom: true,
								value: currentMax === undefined || currentMax === null ? '' : String(currentMax),
								onChange: (v) => {
									const normalized = sanitizeFloatInput(v);
									onChangeAttr('gapMax', normalized === '' ? '' : parseFloat(normalized));
								},
								type: 'number',
								min: 0,
								step: 0.1,
								help: __('Maximum gap size', 'eac-components')
							})
						)
					),
					createElement('div', { style: { marginBlock: '8px' } },
						createElement('label', { style: { display: 'inline-block', fontSize: '11px', fontWeight: 500, textTransform: 'uppercase', marginBlockEnd: '8px' } }, __('Responsive formula', 'eac-components')),
						createElement(TextControl, {
							value: clampString,
							__next40pxDefaultSize: true,
							__nextHasNoMarginBottom: true,
							readOnly: true,
							help: __('CSS clamp() formula for fluid gap', 'eac-components')
						})
					),
					createElement('div', { style: { display: 'flex', gap: '8px', alignItems: 'flex-start', marginBlockEnd: '8px' } },
						createElement('div', { style: { flex: '1 1 0', minInlineSize: 0 } },
							createElement(TextControl, {
								label: __('Margin top', 'eac-components'),
								__next40pxDefaultSize: true,
								__nextHasNoMarginBottom: true,
								value: marginSup === undefined || marginSup === null ? '' : String(marginSup),
								onChange: (v) => {
									const digits = String(v).replace(/\D+/g, '');
									const cleaned = digits === '' ? '' : parseInt(digits, 10);
									onMarginChange('top', cleaned);
								},
								type: 'number'
							})
						),
						createElement('div', { style: { flex: '1 1 0', minInlineSize: 0 } },
							createElement(TextControl, {
								label: __('Bottom', 'eac-components'),
								__next40pxDefaultSize: true,
								__nextHasNoMarginBottom: true,
								value: marginInf === undefined || marginInf === null ? '' : String(marginInf),
								onChange: (v) => {
									const digits = String(v).replace(/\D+/g, '');
									const cleaned = digits === '' ? '' : parseInt(digits, 10);
									onMarginChange('bottom', cleaned);
								},
								type: 'number'
							})
						),
						// Unit (fixed)
						createElement('div', { style: { flex: '0 0 80px' } },
							createElement(SelectControl, {
								label: __('Unit', 'eac-components'),
								__next40pxDefaultSize: true,
								__nextHasNoMarginBottom: true,
								value: unit || 'px',
								options: [
									{ label: 'px', value: 'px' },
									{ label: 'rem', value: 'rem' },
									{ label: 'em', value: 'em' },
									{ label: '%', value: '%' },
									{ label: 'vh', value: 'vh' }
								],
								onChange: onSpacingUnitChange
							})
						)
					)
				);
			};

			const renderColorPanel = () => {
				const palette = themeColors();
				// Couleurs avec fallback
				const textColorWithFallback = colorText || '#000000';
				const itemBackgroundWithFallback = itemBackground || '#ffffff';

				return createElement(PanelBody, { title: __('Color', 'eac-components'), initialOpen: false, className: 'eac-section-panelcolor' },
					createElement(PanelColorSettings, {
						className: 'eac-section-color',
						title: null,
						initialOpen: false,
						colorSettings: [
							{
								value: blockBackground,
								onChange: (c) => { setAttributes({ blockBackground: c }); },
								colors: palette,
								label: __('Block background', 'eac-components'),
								allowReset: true,           // permet reset dans certaines versions
								clearable: true,            // alternative selon version
								//disableCustomColors: true // restreindre aux couleurs du thème
							},
							{
								value: itemBackground,
								onChange: (c) => { setAttributes({ itemBackground: c }); },
								colors: palette,
								label: __('Item background', 'eac-components'),
								allowReset: true,
								clearable: true
							},
							{
								value: colorText,
								onChange: (c) => { setAttributes({ colorText: c }); },
								colors: palette,
								label: __('Item color', 'eac-components'),
								allowReset: true,
								clearable: true
							}
						]
					}),
					// ContrastChecker pour l'item principal
					createElement(ContrastChecker, {
						backgroundColor: itemBackgroundWithFallback,
						textColor: textColorWithFallback,
						isLargeText: false
					}),
				);
			};

			const renderTypographyPanel = () => {
				const textAttr = fontText || {};
				const currentMin = textAttr.fontMin ?? '';
				const currentMax = textAttr.fontMax ?? '';

				const onChangeAttr = (key, val) => {
					const next = Object.assign({}, fontText || {});
					if (key === 'fontMin' || key === 'fontMax') {
						if (val === '' || val === null) {
							delete next[key];
						} else {
							const num = Number(val);
							next[key] = Number.isNaN(num) ? val : num;
						}
					} else {
						next[key] = val;
					}
					setAttributes({ fontText: next });
				};

				// Synchroniser clampString avec fontClamp à chaque changement
				useEffect(() => {
					const nextClamp = buildClamp(fontText?.fontMin ?? '', fontText?.fontMax ?? '');
					if (nextClamp !== (fontText?.fontClamp || '')) {
						const next = Object.assign({}, fontText || {});
						next.fontClamp = nextClamp;
						setAttributes({ fontText: next });
					}
				}, [fontText?.fontMin, fontText?.fontMax]);

				const clampString = buildClamp(fontText?.fontMin ?? '', fontText?.fontMax ?? '');

				return createElement(PanelBody, { title: __('Typography', 'eac-components'), initialOpen: false },
					createElement('div', { style: { display: 'flex', gap: '8px', marginBlockEnd: '8px', alignItems: 'start' } },
						createElement('div', { style: { flex: '1 1 0', minInlineSize: 0 } },
							createElement(TextControl, {
								label: __('Min (rem)', 'eac-components'),
								__next40pxDefaultSize: true,
								__nextHasNoMarginBottom: true,
								value: currentMin === undefined || currentMin === null ? '' : String(currentMin),
								onChange: (v) => {
									const normalized = sanitizeFloatInput(v);
									onChangeAttr('fontMin', normalized === '' ? '' : parseFloat(normalized));
								},
								type: 'number',
								min: 0,
								step: 0.1,
								help: __('Minimum font size', 'eac-components')
							})
						),
						createElement('div', { style: { flex: '1 1 0', minInlineSize: 0 } },
							createElement(TextControl, {
								label: __('Max (rem)', 'eac-components'),
								__next40pxDefaultSize: true,
								__nextHasNoMarginBottom: true,
								value: currentMax === undefined || currentMax === null ? '' : String(currentMax),
								onChange: (v) => {
									const normalized = sanitizeFloatInput(v);
									onChangeAttr('fontMax', normalized === '' ? '' : parseFloat(normalized));
								},
								type: 'number',
								min: 0,
								step: 0.1,
								help: __('Maximum font size', 'eac-components')
							})
						)
					),
					// Responsive formula (read-only)
					createElement('div', { style: { marginBlockStart: '8px' } },
						createElement('label', { style: { display: 'inline-block', fontSize: '11px', fontWeight: 500, textTransform: 'uppercase', marginBlockEnd: '8px' } }, __('Responsive formula', 'eac-components')),
						createElement(TextControl, {
							value: clampString,
							__next40pxDefaultSize: true,
							__nextHasNoMarginBottom: true,
							readOnly: true,
							help: __('CSS clamp() formula for fluid typography', 'eac-components')
						})
					)
				);
			};

			// panneau combiné: bord + border-radius
			const renderItemBorderPanel = () => {
				const getBorder = () => {
					return itemBorder && typeof itemBorder === 'object' ? itemBorder : { inlineSize: 0, color: '' };
				};
				const setBorder = (next) => {
					setAttributes({ itemBorder: Object.assign({}, getBorder(), next) });
				};

				const getItemBorderRadius = () => {
					return itemBorderRadius && typeof itemBorderRadius === 'object'
						? itemBorderRadius
						: { width: 0, unit: 'px' };
				};
				const setItemBorderRadius = (next) => {
					setAttributes({ itemBorderRadius: Object.assign({}, getItemBorderRadius(), next) });
				};

				const palette = themeColors();
				const gb = getBorder();
				const gbr = getItemBorderRadius();

				return createElement(PanelBody, { title: __('Border item', 'eac-components'), initialOpen: false, className: 'eac-section-border' },
					createElement(SelectControl, {
						label: __('Style', 'eac-components'),
						__next40pxDefaultSize: true,
						__nextHasNoMarginBottom: true,
						value: itemStyle,
						options: [
							{ label: __('Default', 'eac-components'), value: '' },
							{ label: 'Style 1', value: 'style-1' },
							{ label: 'Style 2', value: 'style-2' },
							{ label: 'Style 3', value: 'style-3' },
							{ label: 'Style 4', value: 'style-4' },
							{ label: 'Style 5', value: 'style-5' },
							{ label: 'Style 6', value: 'style-6' }
						],
						onChange: (newStyle) => setAttributes({ itemStyle: newStyle || '' }),
						type: 'string'
					}),
					itemStyle === ''
						? createElement(RangeControl, {
							label: __('Width (px)', 'eac-components'),
							__next40pxDefaultSize: true,
							__nextHasNoMarginBottom: true,
							value: Number(gb.width) || 0,
							onChange: (v) => {
								const digits = String(v).replace(/\D+/g, '');
								const width = digits === '' ? 0 : parseInt(digits, 10);
								setBorder({ width: Math.max(0, Math.min(20, width)) });
							},
							min: 0,
							max: 20
						})
						: null,
					itemStyle === ''
						? createElement(PanelColorSettings, {
							title: null,
							initialOpen: false,
							colorSettings: [
								{
									value: gb.color || '',
									onChange: (c) => { setBorder({ color: c || '' }); },
									colors: palette,
									label: __('Color', 'eac-components'),
									allowReset: true,
									clearable: true
								}
							]
						})
						: null,
					itemStyle === ''
						? createElement('div', { style: { display: 'flex', gap: '8px', alignItems: 'flex-start' } },
							createElement('div',
								{ style: { flexGrow: 1, minWidth: 0 } },
								createElement(RangeControl, {
									label: __('Radius', 'eac-components'),
									__next40pxDefaultSize: true,
									__nextHasNoMarginBottom: true,
									value: Number(gbr.width) || 0,
									onChange: (v) => {
										const digits = String(v).replace(/\D+/g, '');
										const width = digits === '' ? 0 : parseInt(digits, 10);
										setItemBorderRadius({ width: Number(width) || 0 });
									},
									min: 0,
									max: 50
								})
							),
							createElement('div', { style: { flex: '0 0 60px', marginInlineStart: 8 } },
								createElement(SelectControl, {
									label: __('Unit', 'eac-components'),
									__next40pxDefaultSize: true,
									__nextHasNoMarginBottom: true,
									value: gbr.unit || 'px',
									options: [
										{ label: 'px', value: 'px' },
										{ label: '%', value: '%' },
										{ label: 'rem', value: 'rem' },
										{ label: 'em', value: 'em' }
									],
									onChange: (u) => { setItemBorderRadius({ unit: u || 'px' }); }
								})
							)
						)
						: null,
				);
			};

			// rendu de l'alignement horizontal et vertical du texte
			const renderControlsToolbarText = () => {
				const iconLeftEl = IconLeft(createElement);
				const iconCenterEl = IconCenter(createElement);
				const iconRigthtEl = IconRight(createElement);
				const iconStartEl = IconStart(createElement);
				const iconBetweenEl = IconBetween(createElement);
				const iconAroundEl = IconAround(createElement);
				const iconEndEl = IconEnd(createElement);
				const itemsHrz = [
					{ key: 'start', icon: iconLeftEl, label: __('Horizontal start', 'eac-components') },
					{ key: 'center', icon: iconCenterEl, label: __('Horizontal center', 'eac-components') },
					{ key: 'end', icon: iconRigthtEl, label: __('Horizontal end', 'eac-components') }
				];

				const itemsVrt = [
					{ key: 'start', icon: iconStartEl, label: __('Vertical start', 'eac-components') },
					{ key: 'space-between', icon: iconBetweenEl, label: __('Vertical space between', 'eac-components') },
					{ key: 'space-around', icon: iconAroundEl, label: __('Vertical space around', 'eac-components') },
					{ key: 'end', icon: iconEndEl, label: __('Vertical end', 'eac-components') },
				];

				const currentIcon = () => {
					if (alignmentHrzText) {
						const found = itemsHrz.find(i => i.key === alignmentHrzText);
						if (found) return found.icon;
					}
					if (alignmentVrtText) {
						const foundV = itemsVrt.find(i => i.key === alignmentVrtText);
						if (foundV) return foundV.icon;
					}
					// fallback generic justify icon
					return IconJustify(createElement);
				};

				return createElement(BlockControls, null,
					createElement(ToolbarDropdownMenu, {
						icon: currentIcon(),
						label: __('Alignment', 'eac-components'),
						controls: [
							...itemsHrz.map((item) => {
								return {
									title: item.label,
									icon: item.icon,
									onClick: () => { setAttributes({ alignmentHrzText: item.key }); },
									isActive: alignmentHrzText === item.key
								};
							}),
							...itemsVrt.map((item) => {
								return {
									title: item.label,
									icon: item.icon,
									onClick: () => { setAttributes({ alignmentVrtText: item.key }); },
									isActive: alignmentVrtText === item.key
								};
							})
						]
					})
				);
			};

			// rendu de la largeur du conteneur
			const renderControlsToolbarContainer = () => {
				const currentWidth = containerWidth || 'container-none';
				const iconNoneEl = IconNone(createElement);
				const iconWideEl = IconWide(createElement);
				const iconFullEl = IconFull(createElement);
				const currentIcon = () => {
					if (currentWidth === 'container-wide') return iconWideEl;
					if (currentWidth === 'container-full') return iconFullEl;
					return IconNone(createElement);
				};

				return createElement(
					BlockControls,
					null,
					createElement(ToolbarDropdownMenu, {
						icon: currentIcon(),
						label: __('Container width', 'eac-components'),
						controls: [
							{
								title: __('None', 'eac-components'),
								icon: iconNoneEl,
								onClick: () => setAttributes({ containerWidth: 'container-none' }),
								isActive: currentWidth === 'container-none'
							},
							{
								title: __('Wide width', 'eac-components'),
								icon: iconWideEl,
								onClick: () => setAttributes({ containerWidth: 'container-wide' }),
								isActive: currentWidth === 'container-wide'
							},
							{
								title: __('Full width', 'eac-components'),
								icon: iconFullEl,
								onClick: () => setAttributes({ containerWidth: 'container-full' }),
								isActive: currentWidth === 'container-full'
							}
						]
					})
				);
			};

			const toolbarAlignment = renderControlsToolbarText();
			const toolbarContainer = renderControlsToolbarContainer();

			const inspectorSettings = InspectorControls
				? createElement(InspectorControls, { group: 'settings' },
					renderSourcePanel(),
					renderLayoutPanel(),
					renderImagePanel(),
					renderLinkPanel()
				)
				: null;

			const inspectorStyles = InspectorControls
				? createElement(InspectorControls, { group: 'styles' },
					renderSpacingPanel(),
					renderColorPanel(),
					renderTypographyPanel(),
					renderItemBorderPanel()
				)
				: null;

			return createElement(Fragment, null, toolbarAlignment, toolbarContainer, inspectorSettings, inspectorStyles, preview);
		},
		save: () => null
	});
})(); // fin IIFE