/**
 * Widget Builder Tooltip Enhancement
 * Adds tooltips to collapsed sidebar icons
 */
(function() {
    'use strict';

    function addTooltipsToCollapsedIcons() {
        // Find all widget control items when sidebar is collapsed
        const sidebar = document.querySelector('.jltma-widget-sidebar.collapsed');

        if (!sidebar) {
            return;
        }

        const controlItems = sidebar.querySelectorAll('.widget-control-item');

        controlItems.forEach(item => {
            // Get the control type from data attribute
            const controlType = item.getAttribute('data-control-type');

            // Get the label from the control's title attribute (already set in React)
            let tooltipText = item.getAttribute('title');

            // If no title, try to get from data-tooltip
            if (!tooltipText) {
                tooltipText = item.getAttribute('data-tooltip');
            }

            // Set both attributes to ensure tooltip works
            if (tooltipText) {
                item.setAttribute('data-tooltip', tooltipText);
                item.setAttribute('title', ''); // Remove default tooltip
            }
        });
    }

    // Run when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', addTooltipsToCollapsedIcons);
    } else {
        addTooltipsToCollapsedIcons();
    }

    // Also run on sidebar toggle (watch for class changes)
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                setTimeout(addTooltipsToCollapsedIcons, 100);
            }
        });
    });

    // Start observing when sidebar exists
    setTimeout(() => {
        const sidebar = document.querySelector('.jltma-widget-sidebar');
        if (sidebar) {
            observer.observe(sidebar, {
                attributes: true,
                attributeFilter: ['class']
            });
        }
    }, 1000);
})();
