/**
 * EAC Repeater & Gallery Blocks - Accessibility & obfuscated emails & keyboard support & global link for grid mode
 * @since 2.4.7 (refactor)
 * @since 2.5.0 Ajout de la gestion bloc relationship
 */

document.addEventListener('DOMContentLoaded', () => {
    const SPACE_KEYS = new Set(['Space', ' ', 'Spacebar']);
    const focusableSelector = 'button, a, [tabindex]:not([tabindex="-1"])';

    // Helpers
    const qs = (sel, ctx = document) => (ctx || document).querySelector(sel);
    const qsAll = (sel, ctx = document) => Array.from((ctx || document).querySelectorAll(sel));
    const isSpaceKey = (evt) => SPACE_KEYS.has(evt.code || evt.key || '');

    // Convertir la touche espace enfoncée en clic pour l'activation du lien au clavier
    const triggerKeyDownToClickEvent = (evt) => {
        if (!isSpaceKey(evt)) return;
        evt.preventDefault();
        const el = document.activeElement;
        if (!el) return;
        el.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true }));
    };

    // 1) Gestion du clavier au niveau de la grille (passage direct des touches Début / Fin / Échap / Tabulation)
    const gridConfigs = [
        {
            grid: '.eac-gallery__block-grid',
            articles: '.acf-gallery__wrapper',
            skip: '.eac-skip-grid'
        },
        {
            grid: '.eac-relationship__block-grid',
            articles: '.acf-relationship__wrapper',
            skip: '.eac-skip-grid'
        },
        {
            grid: '.eac-repeater__block-grid',
            articles: '.acf-repeater__wrapper',
            skip: '.eac-skip-grid'
        }
    ];

    gridConfigs.forEach(config => {
        const blockGrid = qs(config.grid);

        if (blockGrid) {
            blockGrid.addEventListener('keydown', (evt) => {
                const id = evt.code || evt.key || '';
                if (id === 'Tab') return;

                const articles = qsAll(config.articles, blockGrid);
                if (!articles.length) return;

                if (id === 'Home') {
                    evt.preventDefault();
                    // premier article -> premier élément focusable visible
                    for (const art of articles) {
                        const targets = qsAll(focusableSelector, art)
                            .filter(el => !el.hasAttribute('disabled') && el.offsetParent !== null);
                        if (targets.length) { targets[0].focus(); break; }
                    }
                } else if (id === 'End') {
                    evt.preventDefault();
                    // dernier article -> dernier élément focusable visible
                    for (let i = articles.length - 1; i >= 0; i--) {
                        const targets = qsAll(focusableSelector, articles[i])
                            .filter(el => !el.hasAttribute('disabled') && el.offsetParent !== null);
                        if (targets.length) { targets[targets.length - 1].focus(); break; }
                    }
                } else if (id === 'Escape') {
                    const skip = qs(config.skip, blockGrid);
                    if (skip) skip.focus();
                }
            });
        }
    });

    // 2) Comportement des liens globaux pour les cards (repeater + gallery + relationship)
    const cards = [
        ...qsAll('.eac-repeater__block-grid .acf-repeater__wrapper'),
        ...qsAll('.eac-gallery__block-grid .acf-gallery__wrapper'),
        ...qsAll('.eac-relationship__block-grid .acf-relationship__wrapper')
    ];

    cards.forEach((card) => {
        if (!card) return;
        const cardLink = card.querySelector('a.card-link');
        // Prevent internal links from bubbling to card click
        const clickableSelector = 'a:not([data-fancybox]):not(.button-cart):not(.avatar-link)';
        qsAll(clickableSelector, card).forEach((el) => {
            el.addEventListener('click', (evt) => evt.stopPropagation());
            // Allow keyboard activation (space) on those inner links
            el.addEventListener('keydown', (evt) => {
                if (evt.target === el) triggerKeyDownToClickEvent(evt);
            });
        });

        if (cardLink) {
            card.style.cursor = 'pointer';
            card.addEventListener('click', (evt) => {
                // If user selected text, don't follow link
                const noTextSelected = !window.getSelection().toString();
                if (noTextSelected) {
                    cardLink.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true }));
                }
            });
            // Make card activable by Space when focused
            card.addEventListener('keydown', (evt) => {
                if (document.activeElement === card) triggerKeyDownToClickEvent(evt);
            });
        }
    });

    // 3) Accessiblilté des lien dans le contenu: space => click
    const accessibleLinks = [
        ...qsAll('.eac-repeater__block-grid .acf-repeater__wrapper-content a.eac-accessible-link'),
        ...qsAll('.eac-gallery__block-grid .acf-gallery__wrapper a.eac-accessible-link'),
        ...qsAll('.eac-relationship__block-grid .acf-relationship__wrapper-content a.eac-accessible-link'),
        ...qsAll('.eac-repeater__block-grid .acf-repeater__wrapper-content a.eac-accessible-link'),
    ];
    accessibleLinks.forEach((link) => {
        link.addEventListener('keydown', (evt) => {
            if (evt.target === link) triggerKeyDownToClickEvent(evt);
        });
    });

    // 4) Liens mailto masqués: data-link => href
    qsAll('.eac-repeater__block-grid .acf-repeater__wrapper-content a.eac-accessible-link.obfuscated-link')
        .forEach((item) => {
            const dataHref = item.getAttribute('data-link');
            if (!dataHref) return;
            const dataMask = '#actus.'; // replace mask with '@'
            const mailTo = 'mailto:';
            const newHref = dataHref.replace(dataMask, '@');
            item.setAttribute('href', mailTo + newHref);
            item.removeAttribute('data-link');
        });
});
