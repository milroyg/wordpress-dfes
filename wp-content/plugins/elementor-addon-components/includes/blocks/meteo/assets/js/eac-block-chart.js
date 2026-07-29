/**
 * Gestion des graphiques météo avec Chart.js
 * Supporte plusieurs instances avec clientIdent pour éviter les conflits
 * @since 2.5.1
 */

const { __ } = window.wp.i18n || {};

// Fonction utilitaire pour ajuster une couleur hex
function adjustColor(color, percent) {
	const num = parseInt(color.replace("#", ""), 16);
	const amt = Math.round(2.55 * percent);
	const R = (num >> 16) + amt;
	const G = (num >> 8 & 0x00FF) + amt;
	const B = (num & 0x0000FF) + amt;
	return "#" + (0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 +
		(G < 255 ? G < 1 ? 0 : G : 255) * 0x100 +
		(B < 255 ? B < 1 ? 0 : B : 255))
		.toString(16).slice(1);
}

// Fonction utilitaire pour convertir hex en rgba
function hexToRgba(hex, alpha) {
	const r = parseInt(hex.slice(1, 3), 16);
	const g = parseInt(hex.slice(3, 5), 16);
	const b = parseInt(hex.slice(5, 7), 16);
	return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}

(function () {
	'use strict';

	function renderMeteoDayChart(wrapper, clientIdent) {
		if (!wrapper || !clientIdent) return;

		/**const currentClientIdent = wrapper.getAttribute('data-client-ident');
		if (currentClientIdent !== clientIdent) return;*/

		if (typeof Chart === 'undefined') return;

		// Initialiser l'objet de stockage des instances globales
		if (!window.chartsInstances) {
			window.chartsInstances = {};
		}

		// Détruire l'ancienne instance de chart si elle existe
		if (window.chartsInstances[clientIdent]) {
			window.chartsInstances[clientIdent].destroy();
		}

		// Enregistrer le plugin DataLabels si disponible
		if (window.ChartDataLabels) {
			Chart.register(window.ChartDataLabels);
		}

		const lineShadowPlugin = {
			id: 'lineShadow',
			beforeDatasetsDraw(chart, args, options) {
				if (chart.config.type !== 'line') return;
				if (options?.display === false) return;
				const ctx = chart.ctx;
				ctx.save();
				ctx.shadowColor = options.color || 'rgba(0,0,0,0.35)';
				ctx.shadowBlur = options.blur ?? 10;
				ctx.shadowOffsetX = options.offsetX ?? 0;
				ctx.shadowOffsetY = options.offsetY ?? 4;
			},
			afterDatasetsDraw(chart) {
				if (chart.config.type !== 'line') return;
				chart.ctx.restore();
			}
		};
		Chart.register(lineShadowPlugin);

		const dataSettings = wrapper.getAttribute('data-settings');
		if (!dataSettings) return;

		let settings;
		try {
			settings = JSON.parse(dataSettings);
		} catch (e) {
			console.error('Erreur lors du parsing de data-settings:', e);
			return;
		}

		const chart_type = settings.data_type;
		const isHorizontalBar = chart_type === 'horizontalBar';
		const isBar = chart_type === 'bar';
		const isLine = chart_type === 'line';
		const actualChartType = isHorizontalBar ? 'bar' : chart_type;

		let xAxis = settings.x_axis.split(',').map(x => x.trim());
		let yAxisAvg = settings.y_axis.split(',').map(Number);
		let yAxisMin = settings.y_axis_min ? settings.y_axis_min.split(',').map(Number) : [];
		let yAxisMax = settings.y_axis_max ? settings.y_axis_max.split(',').map(Number) : [];
		let yAxisHumidity = isLine && settings.y_axis_humidity && settings.y_axis_humidity.length > 0 ? settings.y_axis_humidity.split(',').map(Number) : [];

		if (isHorizontalBar) {
			xAxis = xAxis.reverse();
			yAxisAvg = yAxisAvg.reverse();
			if (yAxisMin.length > 0) yAxisMin = yAxisMin.reverse();
			if (yAxisMax.length > 0) yAxisMax = yAxisMax.reverse();
		}

		const xLabel = settings.x_label;
		const yLabel = settings.y_label;
		const titre = settings.data_title;
		const dataShadow = settings.data_shadow;
		const dataFill = settings.data_fill;
		const xGrid = settings.data_x_grid;
		const yGrid = settings.data_y_grid;
		const dataFsize = settings.data_fsize;

		// Fonction responsive clamp
		function getResponsiveFontSize() {
			const width = window.innerWidth;
			const minSize = dataFsize * 0.7;
			const maxSize = dataFsize * 1.3;
			const responsive = width * 0.02;
			return Math.max(minSize, Math.min(maxSize, responsive));
		}

		// Gestion des couleurs : une seule ou plusieurs
		let colors = [];
		if (settings.data_colors) {
			colors = settings.data_colors.split(',').map(c => c.trim());
		} else {
			colors = [settings.data_color];
		}

		// Créer les datasets
		let datasets = [];

		// Déterminer les labels des datasets
		const datasetLabels = [
			__('Low', 'eac-components'),
			__('Avg', 'eac-components'),
			__('High', 'eac-components')
		];

		// Données à afficher dans l'ordre : [yAxisMin, yAxisAvg, yAxisMax]
		const allData = [
			{ label: datasetLabels[0], data: yAxisMin },
			{ label: datasetLabels[1], data: yAxisAvg },
			{ label: datasetLabels[2], data: yAxisMax }
		];

		// Créer les datasets pour chaque série de données
		allData.forEach((item, index) => {
			// Passer si pas de données
			if (!item.data || item.data.length === 0) {
				return;
			}

			let dataColor = colors[index] || colors[0];
			let effectColors = {
				bdColor: dataColor,
				bgColor: hexToRgba(dataColor, 0.3),
				bgBarColor: hexToRgba(dataColor, 0.8),
				pointBg: dataColor,
				pointHoverBg: adjustColor(dataColor, -20)
			};

			let datasetConfig = {
				label: item.label,
				data: item.data,
				borderColor: effectColors.bdColor,
				backgroundColor: effectColors.bgColor,
				tension: 0.4,
				/** cas d'une seule ligne, les valeurs sont dans yAxisAvg deuxième index (1) du tableau allData */
				fill: dataFill ? ((yAxisMin.length > 0 && index === 0) || (yAxisMin.length === 0 && index === 1) ? 'start' : index - 1) : false,
				yAxisID: 'y' // Axe Y gauche par défaut
			};

			if (isLine) {
				Object.assign(datasetConfig, {
					pointRadius: 5,
					pointHoverRadius: 5,
					pointBackgroundColor: effectColors.pointBg,
					pointBorderColor: '#ffffff',
					pointBorderWidth: 2,
					borderWidth: 3
				});
			} else if (isBar || isHorizontalBar) {
				Object.assign(datasetConfig, {
					backgroundColor: effectColors.bgBarColor,
					borderColor: effectColors.bdColor,
					borderWidth: 1,
					borderRadius: 4,
					borderSkipped: false,
					hoverBackgroundColor: effectColors.bgBarColor,
					hoverBorderColor: effectColors.bdColor
				});
			}

			datasets.push(datasetConfig);
		});

		// Ajouter le dataset humidité si c'est un graphique en ligne
		if (isLine && yAxisHumidity.length > 0) {
			const humidityColor = '#4BC0C0'; // Couleur cyan/turquoise
			let effectColorsHumidity = {
				bdColor: humidityColor,
				bgColor: hexToRgba(humidityColor, 0.3),
				pointBg: humidityColor,
				pointHoverBg: adjustColor(humidityColor, -20)
			};

			let humidityDataset = {
				label: __('Humidity avg', 'eac-components'),
				data: yAxisHumidity,
				borderColor: effectColorsHumidity.bdColor,
				backgroundColor: effectColorsHumidity.bgColor,
				tension: 0.4,
				fill: false,
				yAxisID: 'y2', // Axe Y droit
				pointRadius: 5,
				pointHoverRadius: 5,
				pointBackgroundColor: effectColorsHumidity.pointBg,
				pointBorderColor: '#ffffff',
				pointBorderWidth: 2,
				borderWidth: 3,
				borderDash: [5, 5] // Trait pointillé pour différencier
			};

			datasets.push(humidityDataset);
		}

		// Configuration des échelles
		const scales = {
			x: {
				display: true,
				position: 'bottom',
				title: {
					display: true,
					text: isHorizontalBar ? yLabel : xLabel,
					font: { size: getResponsiveFontSize() + 2, weight: 'bold' },
					color: 'rgba(0, 0, 0, 0.8)',
					padding: { top: 5, bottom: 5 }
				},
				grid: {
					display: xGrid,
					color: 'rgba(0, 0, 0, 0.08)',
					drawBorder: true,
					borderColor: 'rgba(0, 0, 0, 0.2)'
				},
				ticks: {
					color: 'rgba(0, 0, 0, 0.6)',
					font: { size: getResponsiveFontSize() },
					padding: 8
				}
			},
			y: {
				display: true,
				position: 'left',
				...(isLine && { // yAxisAvg données pour une seule ligne sinon yAxisMin et yAxisMax pour plusieurs lignes
					min: Math.round(Math.min(...yAxisMin.map(Number), ...yAxisAvg.map(Number)) - 2),
					max: Math.round(Math.max(...yAxisMax.map(Number), ...yAxisAvg.map(Number)) + 2)
				}),
				title: {
					display: true,
					text: isHorizontalBar ? xLabel : yLabel,
					font: { size: getResponsiveFontSize() + 2, weight: 'bold' },
					color: 'rgba(0, 0, 0, 0.8)',
					padding: { left: 10, right: 5 }
				},
				grid: {
					display: yGrid,
					color: 'rgba(0, 0, 0, 0.08)',
					drawBorder: true,
					borderColor: 'rgba(0, 0, 0, 0.2)'
				},
				ticks: {
					color: 'rgba(0, 0, 0, 0.6)',
					font: { size: getResponsiveFontSize() },
					padding: 8
				}
			}
		};

		// Ajouter l'axe Y droit pour l'humidité si c'est un graphique en ligne
		if (isLine && yAxisHumidity.length > 0) {
			scales.y2 = {
				type: 'linear',
				display: true,
				position: 'right',
				min: 0,
				max: 100,
				title: {
					display: true,
					text: __('Humidity (%)', 'eac-components'),
					font: { size: getResponsiveFontSize() + 2, weight: 'bold' },
					color: 'rgba(76, 192, 192, 0.9)',
					padding: { left: 10, right: 10 }
				},
				grid: {
					display: false,
					drawBorder: true,
					borderColor: 'rgba(76, 192, 192, 0.3)'
				},
				ticks: {
					color: 'rgba(76, 192, 192, 0.8)',
					font: { size: getResponsiveFontSize(), weight: 'bold' },
					padding: 8
				}
			};
		}

		const canvas = wrapper.querySelector('canvas');
		if (!canvas) return;
		const ctx = canvas.getContext('2d');
		const gradient = ctx.createLinearGradient(0, 0, canvas.width, canvas.height);
		gradient.addColorStop(0, '#f8f9fa');
		gradient.addColorStop(1, '#ffffff');

		const chart = new Chart(ctx, {
			type: actualChartType,
			data: {
				labels: xAxis,
				datasets: datasets
			},
			options: {
				indexAxis: isHorizontalBar ? 'y' : 'x',
				layout: { padding: { left: 5, right: isHorizontalBar ? 25 : 5, top: 5, bottom: 10 } },
				responsive: true,
				maintainAspectRatio: true,
				interaction: { mode: 'none' },
				plugins: {
					lineShadow: {
						display: dataShadow && isLine,
						color: 'rgba(0,0,0,0.35)',
						blur: 8,
						offsetX: 0,
						offsetY: 6
					},
					datalabels: {
						display: true,
						color: 'rgba(0, 0, 0, 0.8)',
						font: {
							size: getResponsiveFontSize(),
							//weight: 'bold'
						},
						anchor: isHorizontalBar ? 'end' : 'end',
						align: isHorizontalBar ? 'right' : 'top',
						offset: 0,
						formatter: (value) => {
							return value;
						}
					},
					title: {
						display: true,
						text: titre,
						font: { size: 16, weight: 'bold' },
						padding: { top: 10, bottom: datasets.length > 1 ? 5 : 30 },
						color: 'rgba(0, 0, 0, 0.8)'
					},
					legend: {
						display: datasets.length > 1,
						position: 'top',
						padding: { bottom: 10 },
					},
					tooltip: {
						enabled: true
					}
				},
				scales: scales
			}
		});
		const color1 = hexToRgba('#d42542', 0.2);
		const color2 = hexToRgba('#6472e1', 0.2);
		//chart.canvas.style.background = `linear-gradient(135deg, ${color1} 0%, ${color2} 100%)`;
		window.chartsInstances[clientIdent] = chart;
	}

	// Backend
	document.addEventListener('eacMeteoChart', (evt) => {
		const clientIdent = evt.detail?.clientIdent; // clientIdent

		if (!clientIdent) return;

		let wrapper = null;

		//if (evt.target.classList.contains('eac-meteo-editor-preview')) {
			wrapper = evt.target.querySelector(
				`.eac-meteo__temp-chart[data-client-ident="${clientIdent}"]`
			);
		//}

		if (wrapper) {
			requestAnimationFrame(() => renderMeteoDayChart(wrapper, clientIdent));
		}
	});

	// Frontend
	document.addEventListener('DOMContentLoaded', () => {
		const charts = document.querySelectorAll('.eac-meteo__temp-chart');

		charts.forEach((wrapper) => {
			const clientIdent = wrapper.getAttribute('data-client-ident');
			requestAnimationFrame(() => renderMeteoDayChart(wrapper, clientIdent));
		});
	});
})();