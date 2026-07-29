/**
 * EAC Temp Hours Block JS
 * @since 2.5.1
 */

import { getThemeColors } from '../../../lib-blocks.js';

(function () {
	'use strict';

	if (window.wp === undefined) {
		console.warn('Block.js: window.wp undefined');
		return;
	}

	const { blocks, element, components, blockEditor, i18n, serverSideRender } = window.wp;
	const { registerBlockType } = blocks || {};
	const { createElement, Fragment, useState, useEffect, useRef } = element || {};
	const { BaseControl, TextControl, PanelBody, Spinner, SelectControl, ToggleControl, RangeControl, Notice, Button } = components || {};
	const InputControl = (components && components.__experimentalInputControl) || {};
	const ToggleGroupControl = (components && components.__experimentalToggleGroupControl) || {};
	const ToggleGroupControlOption = (components && components.__experimentalToggleGroupControlOption) || {};
	const NumberControl = (components && components.__experimentalNumberControl) || {};
	const { InspectorControls, BlockControls, BlockAlignmentToolbar, useBlockProps, PanelColorSettings, FontSizePicker, MediaUpload, MediaUploadCheck, ContrastChecker } = blockEditor || {};
	const { __ } = i18n || {};
	const apiFetch = wp.apiFetch || {};

	registerBlockType('eac-blocks/meteo', {
		edit: function (props) {
			const { attributes, setAttributes, className, clientId } = props || {};
			const {
				city,
				meteoType,
				renderType,
				selectedLanguage,
				chartType,
				tempUnit,
				dateFormat,
				timeFormat,
				clientIdent,
				displayXGrid,
				displayYGrid,
				displayHumidity,
				fillLineContent,
				marginTopBottom,
				badgeStyle,
				imageCardId,
				chartLabelFontSize,
				chartColors
			} = attributes || {};
			const { marginSup = null, marginInf = null, unit = null } = marginTopBottom || {};

			const [languages, setLanguages] = useState([]);
			const [loadingLanguages, setLoadingLanguages] = useState(false);
			const [errorLanguages, setErrorLanguages] = useState(null);
			const wrapperRef = useRef(null);
			const themeColors = getThemeColors();
			const [tempCity, setTempCity] = useState(city);
			const [notice, setNotice] = useState({ type: '', message: '' });
			const blockName = wp.data.select('core/block-editor').getBlock(clientId)?.name;

			// Synchroniser clientId dans l'attribut clientIdent au premier rendu
			useEffect(() => {
				if (!clientIdent) {
					setAttributes({ clientIdent: clientId });
				}
			}, []);

			// Dispatcher l'événement quand la div .eac-meteo__temp-chart existe dans le DOM (après rendu serveur)
			useEffect(() => {
				const wrapper = wrapperRef.current;
				if (!wrapper || !city) return;

				if (!chartType) return;

				// Return si pollution
				if ('pollution' === meteoType) return;

				// Pour 'day', renderType DOIT être 'chart'
				if ('day' === meteoType && 'chart' !== renderType) return;

				// Pour 'week', renderType n'a pas d'importance (vide), on charge la chart directement

				// Observer les changements du DOM pour détecter quand la div cible est prête
				const observer = new MutationObserver(() => {
					const target = wrapper.querySelector('.eac-meteo__temp-chart');
					if (target) {
						const evt = new CustomEvent('eacMeteoChart', {
							bubbles: true,
							cancelable: true,
							detail: { clientIdent: clientIdent }
						});
						wrapper.dispatchEvent(evt);
						observer.disconnect(); // Déconnecter après le dispatch — un nouvel observer sera créé au prochain changement d'attributs
					}
				});

				// Attacher l'observer pour détecter l'arrivée de la div cible
				observer.observe(wrapper, { childList: true, subtree: true });

				// Cleanup : déconnecter observer et nettoyer les timeouts au unmount
				return () => {
					observer.disconnect();
					wrapper.removeEventListener('eacMeteoChart', null);
				};
			}, [clientIdent, city, selectedLanguage, meteoType, renderType, chartType, tempUnit, dateFormat, timeFormat, fillLineContent, displayXGrid, displayYGrid, displayHumidity, chartLabelFontSize, marginTopBottom, chartColors]);

			// Charger la liste des langues au montage du composant
			useEffect(() => {
				let mounted = true;
				setLoadingLanguages(true);
				setErrorLanguages(null);

				apiFetch({ path: '/eac-blocks/v1/language', method: 'GET', headers: { 'Accept': 'application/json' } })
					.then(data => {
						if (!mounted) return;
						setLanguages(data || []);
						setErrorLanguages(false);
					})
					.catch((error) => {
						if (!mounted) return;
						setLanguages([]);
						setErrorLanguages(true);
					})
					.finally(() => {
						if (!mounted) return;
						setLoadingLanguages(false);
					});

				return () => { mounted = false; };
			}, []);

			// Le rendu éditeur se fait via un rendu serveur (serverSideRender)
			const blockProps = useBlockProps({ className: (className || '') + ' eac-meteo-editor-preview' });
			const previewContent = city && serverSideRender
				? createElement(serverSideRender, { key: JSON.stringify(attributes), block: blockName, attributes: attributes })
				: createElement('div', { style: { padding: '12px', border: '1px dashed #ccc' } },
					city ? (Spinner ? createElement(Spinner) : createElement('span', null, 'Loading...')) : __('Type a city to see a preview', 'eac-components')
				)

			const preview = createElement('div', blockProps,
				createElement('div', { ref: wrapperRef }, previewContent)
			);

			const noticeElement = notice.message ?
				createElement(Notice, {
					status: notice.type,
					isDismissible: true,
				}, notice.message) : null;

			const renderLocalisationPanel = () => {
				const sanitizeInput = (value) => {
					// Normalise les caractères (é = e + accent composé → é seul)
					const normalized = value.normalize('NFD');
					// Garde les lettres, espaces, tirets, et accents
					return normalized.replace(/[^\p{L}\s\-]/gu, '').normalize('NFC');
				};

				const handleInputChange = (value) => {
					let sanitized = sanitizeInput(value);
					if (sanitized.length > 50 || sanitized.length < 3) {
						sanitized = sanitized.substring(0, 50);
						setNotice({
							type: 'warning',
							message: 'Max 50 caracs'
						});
					}
					setTempCity(sanitized);
				};

				const handleinputKeyDown = (evt) => {
					if (evt.key !== 'Enter') return;
					const trimed = tempCity.trim();
					setAttributes({ city: trimed });
					setNotice({ type: 'success', message: `Sauvegardé: ${trimed}` });
				};

				return createElement(PanelBody, { title: __('Location', 'eac-components'), initialOpen: true },
					createElement(InputControl, {
						label: __('City', 'eac-components'),
						__next40pxDefaultSize: true,
						//isPressEnterToChange: true,
						value: tempCity,
						onChange: handleInputChange,
						onKeyDown: handleinputKeyDown,
						placeHolder: 'Ex: paris',
						help: __('City name then press Enter', 'eac-components')
					}),
					createElement(SelectControl, {
						label: __('Country (optional)', 'eac-components'),
						__next40pxDefaultSize: true,
						__nextHasNoMarginBottom: true,
						value: selectedLanguage || '',
						options: (() => {
							let base = [];
							if (languages && Object.keys(languages).length) {
								base = Object.entries(languages).map(([key, value]) => ({
									label: value,
									value: key
								}));
								base.unshift({ label: __('Select...', 'eac-components'), value: '' });
							} else {
								base = [{ label: loadingLanguages ? 'Loading...' : __('No language found', 'eac-components'), value: '' }];
							}
							return base;
						})(),
						onChange: (lng) => {
							if (lng !== selectedLanguage) {
								setAttributes({ selectedLanguage: lng });
							}
						}
					}),
				)
			};

			const renderMeteoPanel = () => {
				// Récupérer la locale WordPress
				const getLocale = () => {
					const eacLocale = window.eacLocale?.locale || 'en_GB';
					return eacLocale.replace('_', '-');
				};

				// Récupérer la locale WordPress
				const getTimeZone = () => {
					const eacLocale = window.eacLocale?.tz || 'Europe/London';
					return eacLocale;
				};

				// Formatter une date
				const formatDate = (isoString, format) => {
					// Gérer les deux formats : 2026-05-22 ou 2026/05/22
					const normalizedString = isoString.replace(/\//g, '-');
					const [year, month, day] = normalizedString.split('-');
					const date = new Date(isoString + 'T00:00:00');

					const formatters = {
						'Y-m-d': () => `${year}-${month}-${day}`,
						'd-m-Y': () => `${day}-${month}-${year}`,
						'm/d/Y': () => `${month}/${day}/${year}`,
						'd/m/Y': () => `${day}/${month}/${year}`,
						'F j Y': () => {
							const monthName = new Intl.DateTimeFormat(getLocale(), { month: 'long', timeZone: getTimeZone() }).format(date);
							return `${monthName} ${parseInt(day)} ${year}`;
						},
						'j F Y': () => {
							const monthName = new Intl.DateTimeFormat(getLocale(), { month: 'long', timeZone: getTimeZone() }).format(date);
							return `${parseInt(day)} ${monthName} ${year}`;
						},
						'D j F Y': () => {
							const parts = new Intl.DateTimeFormat(getLocale(), { weekday: 'short', month: 'long', timeZone: getTimeZone() }).formatToParts(date);
							const weekday = parts.find(p => p.type === 'weekday')?.value || '';
							const monthName = parts.find(p => p.type === 'month')?.value || '';
							return `${weekday} ${parseInt(day)} ${monthName} ${year}`;
						},
					};

					return formatters[format] ? formatters[format]() : isoString;
				};

				// Options du SelectControl
				const today = new Date().toISOString().slice(0, 10);
				const dateFormatOptions = [
					{ label: formatDate(today, 'Y-m-d') + ' (Y-m-d)', value: 'Y-m-d' },
					{ label: formatDate(today, 'd-m-Y') + ' (d-m-Y)', value: 'd-m-Y' },
					{ label: formatDate(today, 'm/d/Y') + ' (m/d/Y)', value: 'm/d/Y' },
					{ label: formatDate(today, 'd/m/Y') + ' (d/m/Y)', value: 'd/m/Y' },
					/**{ label: formatDate(today, 'F j Y') + ' (F j Y)', value: 'F j Y' },
					{ label: formatDate(today, 'j F Y') + ' (j F Y)', value: 'j F Y' },*/
				];

				// Les options de l'unité de température
				const unitOptions = [
					createElement(ToggleGroupControlOption, { label: 'Celsius', value: 'celsius' }),
					createElement(ToggleGroupControlOption, { label: 'Fahrenheit', value: 'fahrenheit' })
				];

				// Les options des format temps
				const meteoTypeOptions = [
					createElement(ToggleGroupControlOption, { label: __('Day', 'eac-components'), value: 'day' }),
					createElement(ToggleGroupControlOption, { label: __('Week', 'eac-components'), value: 'week' }),
					//createElement(ToggleGroupControlOption, { label: __('Air quality', 'eac-components'), value: 'pollution' })
				];

				const renderTypeOptions = [
					createElement(ToggleGroupControlOption, { label: __('Chart', 'eac-components'), value: 'chart' }),
					createElement(ToggleGroupControlOption, { label: __('Badge', 'eac-components'), value: 'badge-block' }),
					createElement(ToggleGroupControlOption, { label: __('Badge inline', 'eac-components'), value: 'badge-inline' }),
					createElement(ToggleGroupControlOption, { label: __('Card', 'eac-components'), value: 'card' })
				];

				const chartTypeOptions = [
					createElement(ToggleGroupControlOption, { label: __('Line', 'eac-components'), value: 'line' }),
					createElement(ToggleGroupControlOption, { label: __('Bar', 'eac-components'), value: 'bar' }),
					createElement(ToggleGroupControlOption, { label: __('Horizontal bar', 'eac-components'), value: 'horizontalBar' })
				];

				const timeFormatOptions = [
					createElement(ToggleGroupControlOption, { label: __('24 hours', 'eac-components'), value: '24' }),
					createElement(ToggleGroupControlOption, { label: __('12 hours', 'eac-components'), value: '12' })
				];

				return createElement(PanelBody, { title: __('Settings', 'eac-components'), initialOpen: false },
					createElement(ToggleGroupControl, {
						label: __('Meteo type', 'eac-components'),
						__next40pxDefaultSize: true,
						__nextHasNoMarginBottom: true,
						isBlock: true,
						isAdaptativeWidth: true,
						value: meteoType,
						onChange: (mt) => { setAttributes({ meteoType: mt }); }
					}, ...meteoTypeOptions),
					'day' === meteoType
						? createElement(ToggleGroupControl, {
							label: __('Render type', 'eac-components'),
							__next40pxDefaultSize: true,
							__nextHasNoMarginBottom: true,
							isBlock: true,
							isAdaptativeWidth: true,
							value: renderType,
							onChange: (rt) => { setAttributes({ renderType: rt }); }
						}, ...renderTypeOptions)
						: null,
					(('day' === meteoType && 'chart' === renderType) || 'week' === meteoType)
						? createElement(ToggleGroupControl, {
							label: __('Chart type', 'eac-components'),
							__next40pxDefaultSize: true,
							__nextHasNoMarginBottom: true,
							isBlock: true,
							isAdaptativeWidth: true,
							value: chartType,
							onChange: (ct) => { setAttributes({ chartType: ct }); }
						}, ...chartTypeOptions)
						: null,
					createElement(ToggleGroupControl, {
						label: __('Temp unit', 'eac-components'),
						__next40pxDefaultSize: true,
						__nextHasNoMarginBottom: true,
						isBlock: true,
						isAdaptativeWidth: true,
						value: tempUnit,
						onChange: (ut) => { setAttributes({ tempUnit: ut }); }
					}, ...unitOptions),
					createElement(SelectControl, {
						label: __('Date format', 'eac-components'),
						__next40pxDefaultSize: true,
						__nextHasNoMarginBottom: true,
						value: dateFormat,
						options: dateFormatOptions,
						onChange: (df) => setAttributes({ dateFormat: df })
					}),
					'day' === meteoType
						? createElement(ToggleGroupControl, {
							label: __('Time format', 'eac-components'),
							__next40pxDefaultSize: true,
							__nextHasNoMarginBottom: true,
							isBlock: true,
							isAdaptativeWidth: true,
							value: timeFormat,
							onChange: (tf) => { setAttributes({ timeFormat: tf }); }
						}, ...timeFormatOptions)
						: null
				)
			};

			const renderContentPanel = () => {
				return createElement(PanelBody, { title: __('Content', 'eac-components'), initialOpen: false },
					createElement(ToggleControl, {
						label: __('X grid', 'eac-components'),
						__nextHasNoMarginBottom: true,
						checked: !!displayXGrid,
						onChange: (value) => { setAttributes({ displayXGrid: value }); },
					}),
					createElement(ToggleControl, {
						label: __('Y grid', 'eac-components'),
						__nextHasNoMarginBottom: true,
						checked: !!displayYGrid,
						onChange: (value) => { setAttributes({ displayYGrid: value }); },
					}),
					chartType === 'line'
						? createElement(ToggleControl, {
							label: __('Fill the line', 'eac-components'),
							__nextHasNoMarginBottom: true,
							checked: !!fillLineContent,
							onChange: (value) => { setAttributes({ fillLineContent: value }); },
						})
						: null,
					meteoType === 'week' && chartType === 'line'
						? createElement(ToggleControl, {
							label: __('Humidity', 'eac-components'),
							__nextHasNoMarginBottom: true,
							checked: !!displayHumidity,
							onChange: (value) => { setAttributes({ displayHumidity: value }); },
						})
						: null
				)
			};

			const renderSpacingPanel = () => {
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

				return createElement(PanelBody, { title: __('Spacing', 'eac-components'), initialOpen: true },
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
				)
			};

			const renderImagePanel = () => {
				// Pour l'affichage en éditeur uniquement, code avec getEntityRecords
				const onRemoveImage = () => {
					setAttributes({ imageCardId: 0 });
				};
				const mediaRecords = wp.data.select('core').getEntityRecords('postType', 'attachment', { include: [imageCardId] });
				const imageUrl = mediaRecords?.[0]?.source_url || '';

				return createElement(PanelBody, { title: __('Image', 'eac-components'), initialOpen: false, className: 'eac-section-panelimage' },
					createElement(MediaUploadCheck, null,
						createElement(MediaUpload, {
							onSelect: (media) => { setAttributes({ imageCardId: media.id }); },
							allowedTypes: ['image'],
							value: imageCardId,
							render: ({ open }) => createElement('div',
								{
									style: {
										backgroundImage: imageUrl ? `url(${imageUrl})` : 'none',
										backgroundSize: 'cover',
										backgroundPosition: 'center',
										minHeight: '200px',
										border: '2px dashed #ccc',
										display: 'flex',
										alignItems: 'center',
										justifyContent: 'center'
									}
								},
								createElement('div', null,
									createElement(Button, {
										onClick: open, variant: 'primary'
									},
										imageCardId ? __('Change', 'eac-components') : __('Add background image', 'eac-components'),
									),
									imageCardId
										? createElement(Button, {
											onClick: onRemoveImage, variant: 'secondary'
										},
											__('Remove', 'eac-components')
										) : null
								)
							)
						})
					)
				);
			};

			const renderColorPanel = () => {
				const getBadgeStyle = () => {
					return badgeStyle && typeof badgeStyle === 'object' ? badgeStyle : { 'bgColor': null, 'color': null };
				};
				const setBadgeStyle = (next) => {
					setAttributes({ badgeStyle: Object.assign({}, getBadgeStyle(), next) });
				};
				const gbs = getBadgeStyle();
				const textColorChecker = gbs.color || '#3a3a3a';
				const backgroundChecker = gbs.bgColor || '#f8f9fa';

				const getChartColors = () => {
					return chartColors && typeof chartColors === 'object' ? chartColors : { 'singleLine': null, 'multipleLineLow': null, 'multipleLineAvg': null, 'multipleLineHigh': null };
				};
				const setChartColors = (next) => {
					setAttributes({ chartColors: Object.assign({}, getChartColors(), next) });
				};
				const gcc = getChartColors();
				const palette = themeColors();

				return createElement(PanelBody, { title: __('Color', 'eac-components'), initialOpen: false, className: 'eac-section-panelcolor' },
					createElement(PanelColorSettings, {
						className: 'eac-section-color',
						title: null,
						initialOpen: false,
						colorSettings: [
							...((renderType === 'badge-block' || renderType === 'badge-inline' || renderType === 'card') ? [
								{
									label: __('Background', 'eac-components'),
									value: gbs.bgColor || '',
									onChange: (bgc) => { setBadgeStyle({ bgColor: bgc || '' }); },
									colors: palette,
									allowReset: true,
									clearable: true,
								}
							] : []),
							...((renderType === 'badge-block' || renderType === 'badge-inline' || renderType === 'card') ? [
								{
									label: __('Color', 'eac-components'),
									value: gbs.color || '',
									onChange: (c) => { setBadgeStyle({ color: c || '' }); },
									colors: palette,
									allowReset: true,
									clearable: true
								}
							] : []),
							...((meteoType === 'day' && renderType === 'chart') ? [
								{
									label: __('Color', 'eac-components'),
									value: gcc.singleLine || '',
									onChange: (cl) => { setChartColors({ singleLine: cl || '' }); },
									colors: palette,
									allowReset: true,
									clearable: true
								}
							] : []),
							...(meteoType === 'week' ? [
								{
									label: __('High line color', 'eac-components'),
									value: gcc.multipleLineHigh || '',
									onChange: (cl) => { setChartColors({ multipleLineHigh: cl || '' }); },
									colors: palette,
									allowReset: true,
									clearable: true
								}
							] : []),
							...(meteoType === 'week' ? [
								{
									label: __('Avg line color', 'eac-components'),
									value: gcc.multipleLineAvg || '',
									onChange: (cl) => { setChartColors({ multipleLineAvg: cl || '' }); },
									colors: palette,
									allowReset: true,
									clearable: true
								}
							] : []),
							...(meteoType === 'week' ? [
								{
									label: __('Low line color', 'eac-components'),
									value: gcc.multipleLineLow || '',
									onChange: (cl) => { setChartColors({ multipleLineLow: cl || '' }); },
									colors: palette,
									allowReset: true,
									clearable: true
								}
							] : [])
						],
					}),
					// ContrastChecker pour display autre que les chart
					(renderType === 'badge-block' || renderType === 'badge-inline' || renderType === 'card')
						? createElement(ContrastChecker, {
							backgroundColor: backgroundChecker,
							textColor: textColorChecker,
							isLargeText: false
						}) : null,
				);
			};

			const renderTypographyPanel = () => {
				const getBadgeStyle = () => {
					return badgeStyle && typeof badgeStyle === 'object' ? badgeStyle : { 'fontSize': null, 'fontSizeCity': null, 'fontSizeTemp': null };
				};
				const setBadgeStyle = (next) => {
					setAttributes({ badgeStyle: Object.assign({}, getBadgeStyle(), next) });
				};
				const gbs = getBadgeStyle();

				return createElement(PanelBody, { title: __('Typography', 'eac-components'), initialOpen: false },
					(meteoType === 'week' || renderType === 'chart')
						? createElement(NumberControl, {
							label: __('Labels', 'eac-components'),
							__next40pxDefaultSize: true,
							value: chartLabelFontSize || 10,
							onChange: (cfs) => {
								setAttributes({ chartLabelFontSize: Math.max(8, Math.min(20, Math.round(cfs ?? 10))) });
							},
							min: 8,
							max: 20,
							step: 1,
							help: __('Labels font size', 'eac-components')
						})
						: null,
					(meteoType !== 'week' && renderType !== 'chart')
						? createElement(BaseControl, {
							label: __('Default', 'eac-components'),
							__nextHasNoMarginBottom: true
						},
							createElement(FontSizePicker, {
								__next40pxDefaultSize: true,
								units: ['rem', 'em'],
								withReset: false,
								withSlider: true,
								fontSizes: [],
								value: gbs.fontSize,
								onChange: (fs) => { setBadgeStyle({ fontSize: fs }); },
							})
						)
						: null,
					(meteoType !== 'week' && renderType !== 'chart')
						? createElement(BaseControl, {
							label: __('City', 'eac-components'),
							__nextHasNoMarginBottom: true
						},
							createElement(FontSizePicker, {
								__next40pxDefaultSize: true,
								units: ['em', 'rem'],
								withReset: false,
								withSlider: true,
								fontSizes: [],
								value: gbs.fontSizeCity,
								onChange: (fs) => { setBadgeStyle({ fontSizeCity: fs }); },
							})
						)
						: null,
					(meteoType !== 'week' && renderType !== 'chart')
						? createElement(BaseControl, {
							label: __('Temperature', 'eac-components'),
							__nextHasNoMarginBottom: true
						},
							createElement(FontSizePicker, {
								__next40pxDefaultSize: true,
								units: ['em', 'rem'],
								withReset: false,
								withSlider: true,
								fontSizes: [],
								value: gbs.fontSizeTemp,
								onChange: (fs) => { setBadgeStyle({ fontSizeTemp: fs }); },
							})
						)
						: null,
				);
			};

			/**
			const { BlockControls, BlockAlignmentToolbar, AlignmentToolbar, useBlockProps, ... } = blockEditor || {};
			const blockControls = BlockControls ? createElement(BlockControls, null,
				(renderType !== 'chart' && meteoType !== 'week') ? [
					createElement(BlockAlignmentToolbar, {
						value: attributes.align || '',
						onChange: (val) => { setAttributes({ align: val || '' }); },
						controls: ['left', 'center', 'right']
					}),
					createElement(AlignmentToolbar, {
						value: attributes.textAlign || '',
						onChange: (val) => { setAttributes({ textAlign: val || '' }); }
					})
				] : null
			) : null;
			"textAlign": {
				"type": "string",
					"default": ""
			},
			*/

			// Les contrôles d'alignement sont disponibles uniquement pour les types d'affichage autres que 'chart' et 'week'
			const blockControls = BlockControls ? createElement(BlockControls, null,
				(renderType !== 'chart' && meteoType !== 'week') ? createElement(BlockAlignmentToolbar, {
					value: attributes.align || '',
					onChange: (val) => { setAttributes({ align: val || '' }); },
					controls: ['left', 'center', 'right']
				}) : null
			) : null;

			const inspectorSettings = InspectorControls
				? createElement(InspectorControls, { group: 'settings' },
					renderLocalisationPanel(),
					renderMeteoPanel(),
					((meteoType === 'day' && renderType === 'chart') || meteoType === 'week') ? renderContentPanel() : null,
				)
				: null;

			const inspectorStyles = InspectorControls
				? createElement(InspectorControls, { group: 'styles' },
					renderSpacingPanel(),
					renderColorPanel(),
					renderTypographyPanel()
				)
				: null;

			return createElement(Fragment, null, inspectorSettings, inspectorStyles, blockControls, preview);
		},
		save: function () {
			// Server-side render via render_callback in block.php — save returns null.
			return null;
		}
	});
})();
