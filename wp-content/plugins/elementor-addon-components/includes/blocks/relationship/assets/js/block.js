/**
 * EAC Relationship Block JS
 * @since 2.5.0
 */

import { IconNone, IconJustify, IconWide, IconFull, IconLeft, IconCenter, IconRight, IconBetween, IconAround, IconStart, IconEnd, IconH2, IconH3, IconH4, IconDiv } from '../../../lib-icons.js';
import { getValueForActiveDevice, setValueForActiveDevice, createLabelWithIcons, getThemeColors, buildClamp, useDebounce, isTemplate, getEditorPostId, getEditorPostAuthorId } from '../../../lib-blocks.js';
import { sanitizeFloatInput, sanitizeInput } from '../../../lib-sanitize-fields.js';

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
	const { BaseControl, PanelBody, SelectControl, TextControl, RangeControl, Spinner, ToolbarDropdownMenu, ToggleControl, Button, FormTokenField } = components || {};
	const { InspectorControls, useBlockProps, PanelColorSettings, BlockControls, ContrastChecker } = blockEditor || {};
	const { __ } = i18n || {};
	const apiFetch = wp.apiFetch || {};
	/* fin préambule sécurisé */

	registerBlockType('eac-blocks/relationship', {
		edit: function (props) {
			const { attributes = {}, setAttributes, className = '', clientId } = props || {};
			const {
				containerWidth,
				selectedRelationship,
				postSource,
				postId,
				displayType,
				selectedPostfields,
				relationshipCol,
				relationshipGap,
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
				titleLink,
				globalLink,
				nofollowLink,
				previewImg,
				headingTitle
			} = attributes || {};
			const { fontMin = null, fontMax = null, fontClamp = null } = fontText || {};
			const { marginSup = null, marginInf = null, unit = null } = marginTopBottom || {};
			const { desktopCol = null, tabletLandCol = null, tabletCol = null, mobileLandCol = null, mobileCol = null } = relationshipCol || {};
			const { gapMin = null, gapMax = null, gapClamp = null } = relationshipGap || {};

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

			const [relationship, setRelationship] = useState([]);
			const [loadingRelationship, setLoadingRelationship] = useState(false);
			const [errorRelationship, setErrorRelationship] = useState(null);
			const [remoteSizes, setRemoteSizes] = useState(null);
			const [sizesError, setSizesError] = useState(false);

			const themeColors = getThemeColors();
			const sourceId = getSourceId();
			const shouldDebounce = postSource === 'other';
			// Debouncer le sourceId pour éviter des requêtes à chaque frappe lors de la saisie d'un postId manuel, mais ne pas débouncer pour les autres sources (current, author) qui sont stables
			const debouncedSourceId = useDebounce(sourceId, shouldDebounce ? 500 : 0);

			const postFields = [
				{ key: 'image', label: __('Image', 'eac-components') },
				{ key: 'title', label: __('Title', 'eac-components') },
				{ key: 'excerpt', label: __('Excerpt', 'eac-components') },
				{ key: 'createdDate', label: __('Created date', 'eac-components') },
				{ key: 'modifiedDate', label: __('Modified date', 'eac-components') },
				{ key: 'authorName', label: __('Author name', 'eac-components') },
				{ key: 'authorAvatar', label: __('Author avatar', 'eac-components') },
				{ key: 'category', label: __('Categories', 'eac-components') },
				{ key: 'link', label: __('Link', 'eac-components') },
			];

			// État au niveau du composant principal
			const [displayedFields, setDisplayedFields] = useState(() => {
				if (!selectedPostfields || selectedPostfields.length === 0) {
					return postFields.map(f => f.key);
				}
				return selectedPostfields;
			});

			// Initialiser l'attribut si vide au montage
			useEffect(() => {
				if (!selectedPostfields || selectedPostfields.length === 0) {
					const allKeys = postFields.map(f => f.key);
					setAttributes({ selectedPostfields: allKeys });
				}
			}, []);

			// Synchroniser displayedFields avec selectedPostfields
			useEffect(() => {
				if (selectedPostfields && selectedPostfields.length > 0) {
					setDisplayedFields(selectedPostfields);
				}
			}, [selectedPostfields]);

			/** Gestion des événements pour changement postSource et debouncedSourceId (postId ou authorId), charge les relationship */
			useEffect(() => {
				let mounted = true; // uniquement pour la gestion asynchrone et vérifier si le composant est encore monté
				setLoadingRelationship(true);
				setErrorRelationship(null);

				if (!debouncedSourceId || Number(debouncedSourceId) <= 0) {
					// pas de requête si pid invalide
					setRelationship([]);
					setErrorRelationship(false);
					setLoadingRelationship(false);
					return () => { mounted = false; };
				}

				const endpoint = postSource === 'author'
					? `/eac-blocks/v1/acf-relationship-author/${debouncedSourceId}`
					: `/eac-blocks/v1/acf-relationship/${debouncedSourceId}`;

				apiFetch({path: endpoint, method: 'GET', headers: { 'Accept': 'application/json' } })
					.then(result => {
						if (!mounted) return;
						const list = result && Array.isArray(result) ? result : [];
						setRelationship(list);
						setErrorRelationship(false);
					})
					.catch((error) => {
						if (!mounted) return;
						console.error('Error fetching relationship:', error);
						setRelationship([]);
						setErrorRelationship(true);
					})
					.finally(() => {
						if (!mounted) return;
						setLoadingRelationship(false);
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
				? useBlockProps({ className: (className || '') + ' eac-relationship-editor-preview' })
				: { className: (className || '') + ' eac-relationship-editor-preview' };

			const preview = createElement('div', wrapperProps,
				selectedRelationship && serverSideRender
					? createElement(serverSideRender, { block: 'eac-blocks/relationship', attributes: attributes })
					: createElement('div', { style: { padding: '12px', border: '1px dashed #ccc' } },
						loadingRelationship ? (Spinner ? createElement(Spinner) : createElement('span', null, 'Loading...')) : __('Select a relationship to see a preview', 'eac-components')
					)
			);

			const renderSourcePanel = () => {
				// Condition pour afficher le FormTokenField uniquement si le select n'est pas vide
				const shouldDisplayFormTokenField = selectedRelationship && selectedRelationship.length > 0;

				// Créer les labels à partir de displayedFields
				const allLabels = postFields.map(s => s.label);
				const selectedLabels = displayedFields.map(key => {
					const field = postFields.find(s => s.key === key);
					return field ? field.label : key;
				});

				// Fusionner : sélectionnés en premier, puis les autres
				const suggestions = [
					...selectedLabels,
					...allLabels.filter(label => !selectedLabels.includes(label))
				];

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
								selectedRelationship: '',
								selectedPostfields: [], // Vider les champs
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
							setRelationship([]);
							setLoadingRelationship(true);
							setErrorRelationship(null);
							setDisplayedFields([]); // Vider l'état local aussi
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
							setRelationship([]);
							setLoadingRelationship(true);
							setErrorRelationship(null);
							setDisplayedFields([]); // Vider l'état local aussi
						},
					}),
					createElement(SelectControl, {
						label: __('Select relationship', 'eac-components'),
						__next40pxDefaultSize: true,
						__nextHasNoMarginBottom: true,
						value: selectedRelationship,
						options: (() => {
							let base = [];
							if (relationship && relationship.length) {
								base = relationship.map(function (r) { return { label: r.label, value: r.key }; });
								base.unshift({ label: __('Select...', 'eac-components'), value: '' });
							} else {
								base = [{ label: loadingRelationship ? 'Loading...' : __('No relationship found', 'eac-components'), value: '' }];
							}
							return base;
						})(),
						onChange: (v) => {
							if (v !== selectedRelationship) {
								const newAttributes = { selectedRelationship: v };

								// Si un relationship est sélectionné, remplir avec toutes les clés
								// Sinon, vider selectedPostfields
								if (v && v.length > 0) {
									const allKeys = postFields.map(f => f.key);
									newAttributes.selectedPostfields = allKeys;
									setDisplayedFields(allKeys);
								} else {
									newAttributes.selectedPostfields = [];
									setDisplayedFields([]);
								}

								setAttributes(newAttributes);
							}
						},
					}),
					// insertion du FormTokenField cacher si pas de relationship sélectionné
					shouldDisplayFormTokenField && createElement('div', { className: 'form-token-field-wrapper' },
						createElement('label', { htmlFor: 'FormTokenField-' + clientId, className: 'components-base-control__label' },
							__('Select content', 'eac-components')
						),
						createElement(FormTokenField, {
							__next40pxDefaultSize: true,
							__nextHasNoMarginBottom: true,
							__experimentalShowHowTo: false,
							id: 'FormTokenField-' + clientId,
							value: selectedLabels,
							suggestions: suggestions,
							onChange: (tokens) => {
								const selectedKeys = tokens.map(label => {
									const field = postFields.find(s => s.label === label);
									return field ? field.key : label;
								});
								setDisplayedFields(selectedKeys);
								setAttributes({ selectedPostfields: selectedKeys });
							},
							__experimentalValidateInput: (value) => {
								if (!sanitizeInput(value)) {
									return false;
								}

								const normalizedValue = value.toLowerCase().trim();
								const isValidSuggestion = postFields.some(s =>
									s.label.toLowerCase() === normalizedValue
								);

								return isValidSuggestion && !displayedFields.some(key => {
									const field = postFields.find(s => s.key === key);
									return field && field.label.toLowerCase() === normalizedValue;
								});
							},
						}),
						// Ajoutez le texte d'aide ici
						createElement('p', { className: 'components-base-control__help' },
							postFields.map(field => field.label).join(', ')
						)
					)
				);
			};

			const renderLayoutPanel = () => {
				const sourceCol = relationshipCol;
				const setFn = (obj) => setAttributes({ relationshipCol: obj });
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
					createElement(SelectControl, {
						label: __('Display type', 'eac-components'),
						__next40pxDefaultSize: true,
						__nextHasNoMarginBottom: true,
						value: displayType,
						options: [
							{ label: __('Grid', 'eac-components'), value: 'grid' },
							{ label: __('List', 'eac-components'), value: 'list' },
						],
						onChange: (v) => setAttributes({ displayType: v }),
						type: 'string'
					}),
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
						label: __('Title tag', 'eac-components'),
						__nextHasNoMarginBottom: true
					},
						createElement('div',
							{ className: 'eac-heading-icon-row', style: { display: 'flex', columnGap: '8px', justifyContent: 'flex-start', alignItems: 'center', marginBlockStart: '4px' } },
							setHeadingCaption(IconH2(createElement), 'h2', headingTitle, function (v) { setAttributes({ headingTitle: v }); }, 'H2'),
							setHeadingCaption(IconH3(createElement), 'h3', headingTitle, function (v) { setAttributes({ headingTitle: v }); }, 'H3'),
							setHeadingCaption(IconH4(createElement), 'h4', headingTitle, function (v) { setAttributes({ headingTitle: v }); }, 'H4'),
							setHeadingCaption(IconDiv(createElement), 'div', headingTitle, function (v) { setAttributes({ headingTitle: v }); }, 'DIV')
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
					!sizesError && displayType === 'grid'
						? createElement('div', { style: { display: 'flex', gap: '15px', marginBlockEnd: '8px', alignItems: 'flex-start' } },
							// Ratio (fixed)
							displayType === 'grid'
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
							displayType === 'grid' && imageRatio && String(imageRatio) !== ''
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
					selectedPostfields && selectedPostfields.includes('title')
						? createElement(ToggleControl, {
							label: __('Link on title', 'eac-components'),
							__nextHasNoMarginBottom: true,
							checked: !!titleLink,
							onChange: (value) => { setAttributes({ titleLink: value }); },
						})
						: null,
					createElement(ToggleControl, {
						label: __('Enable the link globally', 'eac-components'),
						__nextHasNoMarginBottom: true,
						checked: !!globalLink,
						onChange: (value) => { setAttributes({ globalLink: value }); },
						help: globalLink
							? __('Each item will link to the URL', 'eac-components')
							: __('Items will not have a global link', 'eac-components')
					}),
					createElement(ToggleControl, {
						label: 'Nofollow',
						__nextHasNoMarginBottom: true,
						checked: !!nofollowLink,
						onChange: (value) => { setAttributes({ nofollowLink: value }); },
						help: nofollowLink
							? __('Target link will not be indexed by search engines', 'eac-components')
							: __('Target link will be indexed by search engines', 'eac-components')
					}),
				);
			};

			const renderSpacingPanel = () => {
				const gapAttr = relationshipGap || {};
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
					const next = Object.assign({}, relationshipGap || {});
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
					setAttributes({ relationshipGap: next });
				};

				// Synchroniser clampString avec gapClamp à chaque changement
				useEffect(() => {
					const nextClamp = buildClamp(relationshipGap?.gapMin ?? '', relationshipGap?.gapMax ?? '');
					if (nextClamp !== (relationshipGap?.gapClamp || '')) {
						const next = Object.assign({}, relationshipGap || {});
						next.gapClamp = nextClamp;
						setAttributes({ relationshipGap: next });
					}
				}, [relationshipGap?.gapMin, relationshipGap?.gapMax]);

				const clampString = buildClamp(relationshipGap?.gapMin ?? '', relationshipGap?.gapMax ?? '');

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
			const hasImage = selectedPostfields && selectedPostfields.includes('image');
			const hasLink = selectedPostfields && selectedPostfields.includes('link');

			const inspectorSettings = InspectorControls
				? createElement(InspectorControls, { group: 'settings' },
					renderSourcePanel(),
					renderLayoutPanel(),
					hasImage ? renderImagePanel() : null,
					hasLink ? renderLinkPanel() : null
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