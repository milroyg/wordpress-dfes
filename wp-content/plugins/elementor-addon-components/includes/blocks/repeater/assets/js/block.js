/**
 * EAC Repeater Block JS
 * @since 2.4.7
 * @since 2.4.8 Sanitize les Control, ajout du contrast checker, suppression requêtes AJAX pour REST API
 * @since 2.4.9 Suppression du control Select2, utilisation du control FormTokenField
 */

import { IconNone, IconJustify, IconWide, IconFull, IconLeft, IconCenter, IconRight, IconBetween, IconAround, IconStart, IconEnd, IconH2, IconH3, IconH4, IconDiv } from '../../../lib-icons.js';
import { getValueForActiveDevice, setValueForActiveDevice, createLabelWithIcons, getThemeColors, buildClamp, useDebounce, getEditorPostId } from '../../../lib-blocks.js';
import { sanitizeFloatInput, sanitizeInput } from '../../../lib-sanitize-fields.js';
import { renderHasImage, renderHasUrl } from './util.js';

(function () {
	'use strict';

	if (window.wp === undefined) {
		console.warn('Block.js: window.wp undefined');
		return;
	}

	/* Ultra-minimal préambule block.js — compatible WP 6.5 */
	const { blocks, element, components, blockEditor, i18n, serverSideRender, data } = window.wp;
	const { createElement, Fragment, useState, useEffect, useRef } = element || {};
	const { registerBlockType } = blocks || {};
	const { BaseControl, PanelBody, SelectControl, TextControl, RangeControl, Spinner, ToolbarDropdownMenu, ToggleControl, Button, FormTokenField } = components || {};
	const { InspectorControls, useBlockProps, PanelColorSettings, BlockControls, ContrastChecker } = blockEditor || {};
	const { __ } = i18n || {};
	const apiFetch = wp.apiFetch || {};
	/* fin préambule sécurisé */

	registerBlockType('eac-blocks/repeater', {
		edit: function (props) {
			const { attributes = {}, setAttributes, className = '' } = props || {};
			const {
				containerWidth,
				selectedRepeater,
				selectedSubfields,
				postSource,
				displayType,
				postId,
				headingFaq,
				repeaterWidth,
				repeaterCol,
				repeaterGap,
				fontText,
				marginTopBottom,
				blockBackground,
				itemBackground,
				colorText,
				colorTitleFaq,
				colorTitleFaqBackground,
				colorTitleTable,
				colorTitleTableBackground,
				alignmentHrzText,
				alignmentVrtText,
				itemStyle,
				itemBorder,
				itemBorderRadius,
				imageSizes,
				imageRatio,
				imagePosition,
				linkAsButton,
				globalLink,
				nofollowLink
			} = attributes || {};
			const { fontMin = null, fontMax = null, fontClamp = null } = fontText || {};
			const { marginSup = null, marginInf = null, unit = null } = marginTopBottom || {};
			const { desktopWidth = null, tabletLandWidth = null, tabletWidth = null, mobileLandWidth = null, mobileWidth = null } = repeaterWidth || {};
			const { desktopCol = null, tabletLandCol = null, tabletCol = null, mobileLandCol = null, mobileCol = null } = repeaterCol || {};
			const { gapMin = null, gapMax = null, gapClamp = null } = repeaterGap || {};

			// Hooks sûrs
			if (!useState || !useEffect) {
				return createElement('div', { className: className, style: { padding: '12px', border: '1px dashed #ccc' } },
					__('Editor not available (missing React hooks)', 'eac-components')
				);
			}

			const [repeaters, setRepeaters] = useState([]);
			const [subfields, setSubfields] = useState([]);
			const [loadingRepeaters, setLoadingRepeaters] = useState(false);
			const [loadingSubfields, setLoadingSubfields] = useState(false);
			const [errorRepeaters, setErrorRepeaters] = useState(null);
			const [errorSubfields, setErrorSubfields] = useState(null);
			const [remoteSizes, setRemoteSizes] = useState(null);
			const [sizesError, setSizesError] = useState(false);

			const themeColors = getThemeColors();
			const pid = postSource === 'other' ? postId : getEditorPostId();
			// useEffect dans la fonction useDebounce (surveille changement de postId) de 500ms pour éviter les requêtes trop fréquentes lors de la saisie du postId
			const debouncedPid = useDebounce(pid, 500);

			/** Gestion des événements pour changement postSource et postId, charge les repeaters */
			useEffect(() => {
				let mounted = true;
				setLoadingRepeaters(true);
				setErrorRepeaters(null);

				if (!debouncedPid || Number(debouncedPid) <= 0) {
					// pas de requête si pid invalide
					setRepeaters([]);
					setErrorRepeaters(false);
					setLoadingRepeaters(false);
					return () => { mounted = false; };
				}

				apiFetch({ path: `/eac-blocks/v1/acf-repeater/${debouncedPid}`, method: 'GET', headers: { 'Accept': 'application/json' } })
					.then(result => {
						if (!mounted) return;
						const list = result && Array.isArray(result) ? result : [];
						setRepeaters(list);
						setErrorRepeaters(false);
					})
					.catch((error) => {
						if (!mounted) return;
						console.error('Error fetching repeaters:', error);
						setRepeaters([]);
						setErrorRepeaters(true);
					})
					.finally(() => {
						if (!mounted) return;
						setLoadingRepeaters(false);
					});
				return () => { mounted = false; };
			}, [postSource, debouncedPid]);

			/** Gestion des événements pour changement selectedRepeater postSource et postId, charge les sous-champs */
			useEffect(() => {
				let mounted = true;
				setLoadingSubfields(true);
				setErrorSubfields(null);

				if (!selectedRepeater || !debouncedPid || Number(debouncedPid) <= 0) {
					setSubfields([]);
					setLoadingSubfields(false);
					setErrorSubfields(null);
					return;
				}

				apiFetch({ path: `/eac-blocks/v1/acf-subfield/${debouncedPid}/${encodeURIComponent(selectedRepeater)}/`, method: 'GET', headers: { 'Accept': 'application/json' } })
					.then(result => {
						if (!mounted) return;
						const list = result && Array.isArray(result) ? result : [];
						setSubfields(list);
						setErrorSubfields(false);

						// n'initialise selectedSubfields que si l'attribut est vide
						const existingSelected = Array.isArray(selectedSubfields)
							? selectedSubfields.map(String)
							: [];
						const keys = list
							.map(item => (item && item.key ? String(item.key) : null))
							.filter(keyStr => keyStr !== null);
						if (existingSelected.length === 0 && keys.length > 0) {
							setAttributes({ selectedSubfields: keys });
						}
					})
					.catch((error) => {
						if (!mounted) return;
						console.error('Error fetching subfields:', error);
						setSubfields([]);
						setErrorSubfields(true);
					})
					.finally(() => {
						if (!mounted) return;
						setLoadingSubfields(false);
					});
				return () => { mounted = false; };

			}, [selectedRepeater, postSource, debouncedPid]);

			/** Gestion de la liste des tailles d'images appelé au chargement uniquement pas de dépendances */
			useEffect(() => {
				let mounted = true;

				apiFetch({ path: '/eac-blocks/v1/image-sizes', method: 'GET', headers: { 'Accept': 'application/json' } })
					.then(data => { if (!mounted) return; setRemoteSizes(data || {}); setSizesError(false); })
					.catch((error) => { if (!mounted) return; setRemoteSizes({}); setSizesError(true); });

				return () => { mounted = false; };
			}, []);

			const wrapperProps = (typeof useBlockProps === 'function')
				? useBlockProps({ className: (className || '') + ' eac-repeater-editor-preview' })
				: { className: (className || '') + ' eac-repeater-editor-preview' };

			const preview = createElement('div', wrapperProps,
				selectedRepeater && serverSideRender
					? createElement(serverSideRender, { block: 'eac-blocks/repeater', attributes: attributes })
					: createElement('div', { style: { padding: '12px', border: '1px dashed #ccc' } },
						loadingRepeaters ? (Spinner ? createElement(Spinner) : createElement('span', null, 'Loading…')) : __('Select a repeater to see a preview', 'eac-components')
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
						],
						onChange: function (val) {
							if (val === postSource) return;
							if (val === 'current') {
								setAttributes({
									postSource: 'current',
									postId: 0,
									selectedRepeater: '',
									selectedSubfields: '',
								});
							} else {
								setAttributes({
									postSource: 'other',
									selectedRepeater: '',
									selectedSubfields: '',
								});
							}
							// vider UI immédiatement — le useEffect se chargera du fetch
							setRepeaters([]);
							setLoadingRepeaters(true);
							setErrorRepeaters(null);
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
							setRepeaters([]);
							setLoadingRepeaters(true);
							setErrorRepeaters(null);
						},
					}),
					createElement(SelectControl, {
						label: __('Select repeater', 'eac-components'),
						__next40pxDefaultSize: true,
						__nextHasNoMarginBottom: true,
						value: selectedRepeater,
						options: (() => {
							let base = [];
							if (repeaters && repeaters.length) {
								base = repeaters.map(function (r) { return { label: r.label, value: r.key }; });
								base.unshift({ label: __('Select...', 'eac-components'), value: '' });
							} else {
								base = [{ label: loadingRepeaters ? 'Loading...' : __('No repeater found', 'eac-components'), value: '' }];
							}
							return base;
						})(),
						onChange: (v) => {
							if (v !== selectedRepeater) {
								setAttributes({ selectedRepeater: v, selectedSubfields: '' });
							}
						},
					}),
					// insertion du FormTokenField
					createElement('div', { className: 'form-token-field-wrapper' },
						createElement('label', { htmlFor: 'FormTokenField-' + props.clientId, className: 'components-base-control__label' },
							__('Select subfields', 'eac-components')
						),
						createElement(FormTokenField, {
							__next40pxDefaultSize: true,
							__nextHasNoMarginBottom: true,
							__experimentalShowHowTo: false,
							id: 'FormTokenField-' + props.clientId,
							value: (selectedSubfields || []).map(key => {
								const field = subfields.find(s => s.key === key);
								return field ? field.label : key;
							}),
							suggestions: (subfields && subfields.length)
								? subfields.map(s => s.label)
								: [loadingSubfields ? 'Loading...' : __('No subfield found', 'eac-components')],
							onChange: (tokens) => {
								// Convertir les labels sélectionnés en clés
								const selectedKeys = tokens.map(label => {
									const field = subfields.find(s => s.label === label);
									return field ? field.key : label;
								});
								setAttributes({ selectedSubfields: selectedKeys });
							},
							__experimentalValidateInput: (value) => { // Retourner true si valide, false sinon
								if (!sanitizeInput(value)) {
									return false;
								}

								const normalizedValue = value.toLowerCase().trim();
								const isValidSuggestion = subfields.some(s =>
									s.label.toLowerCase() === normalizedValue
								);

								return isValidSuggestion && !(selectedSubfields || []).some(key => {
									const field = subfields.find(s => s.key === key);
									return field && field.label.toLowerCase() === normalizedValue;
								});
							},
						})
					)
				);
			};

			const renderLayoutPanel = () => {
				const sourceWidth = repeaterWidth;
				const setFn = (obj) => setAttributes({ repeaterWidth: obj });
				const sourceCol = repeaterCol;
				const setFn2 = (obj) => setAttributes({ repeaterCol: obj });
				const setHeadingFaq = (iconEl, value, current, onClick, label) => {
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
					createElement(SelectControl, {
						label: __('Display type', 'eac-components'),
						__next40pxDefaultSize: true,
						__nextHasNoMarginBottom: true,
						value: displayType,
						options: [
							{ label: __('Grid', 'eac-components'), value: 'grid' },
							{ label: __('List', 'eac-components'), value: 'list' },
							{ label: 'FAQ', value: 'faq' },
							{ label: __('Table', 'eac-components'), value: 'table' },
						],
						onChange: (v) => setAttributes({ displayType: v }),
						type: 'string'
					}),
					displayType === 'faq'
						? createElement(BaseControl, {
							label: __('Question tag', 'eac-components'),
							__nextHasNoMarginBottom: true },
							createElement('div',
								{ className: 'eac-heading-icon-row', style: { display: 'flex', columnGap: '8px', justifyContent: 'flex-start', alignItems: 'center', marginBlockStart: '4px' } },
								setHeadingFaq(IconH2(createElement), 'h2', headingFaq, function (v) { setAttributes({ headingFaq: v }); }, 'H2'),
								setHeadingFaq(IconH3(createElement), 'h3', headingFaq, function (v) { setAttributes({ headingFaq: v }); }, 'H3'),
								setHeadingFaq(IconH4(createElement), 'h4', headingFaq, function (v) { setAttributes({ headingFaq: v }); }, 'H4'),
								setHeadingFaq(IconDiv(createElement), 'div', headingFaq, function (v) { setAttributes({ headingFaq: v }); }, 'DIV')
							)
						)
						: null,
					displayType === 'faq'
						? createElement(RangeControl, {
							label: createLabelWithIcons(createElement, __('Repeater width (%)', 'eac-components'), sourceWidth, setFn),
							__next40pxDefaultSize: true,
							__nextHasNoMarginBottom: true,
							value: getValueForActiveDevice(sourceWidth),
							onChange: (val) => { setValueForActiveDevice(sourceWidth, setFn, val); },
							type: 'number',
							min: 20,
							max: 100,
							step: 5
						})
						: null,
					(displayType === 'grid' || displayType === 'list')
						? createElement(TextControl, {
							label: createLabelWithIcons(createElement, __('Columns', 'eac-components'), sourceCol, setFn2),
							__next40pxDefaultSize: true,
							__nextHasNoMarginBottom: true,
							value: getValueForActiveDevice(sourceCol),
							onChange: (val) => {
								const digits = String(val).replace(/\D+/g, '');
								setValueForActiveDevice(sourceCol, setFn2, digits);
							},
							type: 'number',
							min: 1,
							max: 6,
							step: 1
						})
						: null,
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

					!sizesError && displayType !== 'table'
						? createElement('div', { style: { display: 'flex', gap: '15px', marginBlockEnd: '8px', alignItems: 'flex-start' } },
							// Ratio (fixed)
							displayType !== 'table'
								? createElement('div', { style: { flex: '0 0 90px' } },
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
								)
								: null,
							// Vertical position (flexible, only shown when imageRatio set)
							displayType !== 'table' && imageRatio && String(imageRatio) !== ''
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
						: null
				);
			};

			const renderLinkPanel = () => {
				return createElement(PanelBody, { title: __('Link', 'eac-components'), initialOpen: false },
					(displayType === 'grid' || displayType === 'list')
						? createElement(ToggleControl, {
								label: __('Display link as button', 'eac-components'),
								__nextHasNoMarginBottom: true,
								checked: !!linkAsButton,
								onChange: (value) => { setAttributes({ linkAsButton: value }); },
								help: linkAsButton
									? __('Link displayed as button', 'eac-components')
									: __('Link displayed as link', 'eac-components')
							}
						)
						: null,
					(displayType === 'grid' || displayType === 'list')
						? createElement(ToggleControl, {
								label: __('Enable the link globally', 'eac-components'),
								__nextHasNoMarginBottom: true,
								checked: !!globalLink,
								onChange: (value) => { setAttributes({ globalLink: value }); },
								help: globalLink
									? __('Each item will link to the URL found in the subfield', 'eac-components')
									: __('Items will not have a global link', 'eac-components')
							}
						)
						: null,
					displayType !== 'faq'
						? createElement(ToggleControl, {
								label: 'Nofollow',
								__nextHasNoMarginBottom: true,
								checked: !!nofollowLink,
								onChange: (value) => { setAttributes({ nofollowLink: value }); },
								help: nofollowLink
									? __('Target link will not be indexed by search engines', 'eac-components')
									: __('Target link will be indexed by search engines', 'eac-components')
							}
						)
						: null,
				);
			};

			const renderSpacingPanel = () => {
				const gapAttr = repeaterGap || {};
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
					const next = Object.assign({}, repeaterGap || {});
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
					setAttributes({ repeaterGap: next });
				};

				// Synchroniser clampString avec gapClamp à chaque changement
				useEffect(() => {
					const nextClamp = buildClamp(repeaterGap?.gapMin ?? '', repeaterGap?.gapMax ?? '');
					if (nextClamp !== (repeaterGap?.gapClamp || '')) {
						const next = Object.assign({}, repeaterGap || {});
						next.gapClamp = nextClamp;
						setAttributes({ repeaterGap: next });
					}
				}, [repeaterGap.gapMin, repeaterGap.gapMax]);

				const clampString = buildClamp(repeaterGap?.gapMin ?? '', repeaterGap?.gapMax ?? '');

				return createElement(PanelBody, { title: __('Spacing', 'eac-components'), initialOpen: true },
					displayType !== 'table'
						? createElement('div', { style: { display: 'flex', gap: '8px', marginBlockEnd: '8px', alignItems: 'start' } },
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
						)
						: null,
					displayType !== 'table'
						? createElement('div', { style: { marginBlock: '8px' } },
							createElement('label', { style: { display: 'inline-block', fontSize: '11px', fontWeight: 500, textTransform: 'uppercase', marginBlockEnd: '8px' } }, __('Responsive formula', 'eac-components')),
							createElement(TextControl, {
								value: clampString,
								__next40pxDefaultSize: true,
								__nextHasNoMarginBottom: true,
								readOnly: true,
								help: __('CSS clamp() formula for fluid gap', 'eac-components')
							})
						)
						: null,
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
				const faqTitleColorWithFallback = colorTitleFaq || '#1346CD';
				const faqTitleBgWithFallback = colorTitleFaqBackground || '#F1F1F1';
				const titleTableColorWithFallback = colorTitleTable || '#000000';
				const titleTableBgWithFallback = colorTitleTableBackground || '#F1F1F1';

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
								//disableCustomColors: true // décommente pour supprimer color checker
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
							},
							...(displayType === 'faq' ? [
								{
									value: colorTitleFaqBackground,
									onChange: (c) => { setAttributes({ colorTitleFaqBackground: c }); },
									colors: palette,
									label: __('FAQ title background color', 'eac-components'),
									allowReset: true,
									clearable: true
								}
							] : []),
							...(displayType === 'faq' ? [
								{
									value: colorTitleFaq,
									onChange: (c) => { setAttributes({ colorTitleFaq: c }); },
									colors: palette,
									label: __('FAQ title color', 'eac-components'),
									allowReset: true,
									clearable: true
								}
							] : []),
							...(displayType === 'table' ? [
								{
									value: colorTitleTableBackground,
									onChange: (c) => { setAttributes({ colorTitleTableBackground: c }); },
									colors: palette,
									label: __('Heading background color', 'eac-components'),
									allowReset: true,
									clearable: true
								}
							] : []),
							...(displayType === 'table' ? [
								{
									value: colorTitleTable,
									onChange: (c) => { setAttributes({ colorTitleTable: c }); },
									colors: palette,
									label: __('Heading color', 'eac-components'),
									allowReset: true,
									clearable: true
								}
							] : []),
						],
					}),
					// ContrastChecker pour l'item principal
					createElement(ContrastChecker, {
						backgroundColor: itemBackgroundWithFallback,
						textColor: textColorWithFallback,
						isLargeText: false
					}),
					// ContrastChecker pour FAQ (conditionnel)
					displayType === 'faq' ? createElement(ContrastChecker, {
						backgroundColor: faqTitleBgWithFallback,
						textColor: faqTitleColorWithFallback,
						isLargeText: true
					}) : null,
					// ContrastChecker pour table (conditionnel)
					displayType === 'table' ? createElement(ContrastChecker, {
						backgroundColor: titleTableBgWithFallback,
						textColor: titleTableColorWithFallback,
						isLargeText: false
					}) : null,
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
				}, [fontText.fontMin, fontText.fontMax]);

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
								min: 0.2,
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
								min: 0.2,
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
					return itemBorder && typeof itemBorder === 'object' ? itemBorder : { width: 0, color: '' };
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
					displayType !== 'table'
						? createElement(SelectControl, {
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
						})
						: null,
					itemStyle === ''
						? createElement(RangeControl, {
							label: __('Width (px)', 'eac-components'),
							__next40pxDefaultSize: true,
							__nextHasNoMarginBottom: true,
							value: Number(gb.width) || 0,
							onChange: (v) => { setBorder({ width: Number(v) || 0 }); },
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
					(displayType !== 'table' && itemStyle === '')
						? createElement('div', { style: { display: 'flex', gap: '8px', alignItems: 'flex-start' } },
							createElement('div',
								{ style: { flexGrow: 1, minInlineSize: 0 } },
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
				const iconLeftEl    = IconLeft(createElement);
				const iconCenterEl  = IconCenter(createElement);
				const iconRigthtEl  = IconRight(createElement);
				const iconStartEl   = IconStart(createElement);
				const iconBetweenEl = IconBetween(createElement);
				const iconAroundEl = IconAround(createElement);
				const iconEndEl     = IconEnd(createElement);
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
				const iconNoneEl   = IconNone(createElement);
				const iconWideEl   = IconWide(createElement);
				const iconFullEl   = IconFull(createElement);
				const currentIcon  = () => {
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

			const toolbarAlignment = displayType !== 'faq' ? renderControlsToolbarText() : null;
			const toolbarContainer = displayType !== 'faq' ? renderControlsToolbarContainer() : null;

			const inspectorSettings = InspectorControls
				? createElement(InspectorControls, { group: 'settings' },
					renderSourcePanel(),
					renderLayoutPanel(),
					displayType !== 'faq' && renderHasImage(subfields, selectedSubfields) ? renderImagePanel() : null,
					displayType !== 'faq' && renderHasUrl(subfields, selectedSubfields) ? renderLinkPanel() : null
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