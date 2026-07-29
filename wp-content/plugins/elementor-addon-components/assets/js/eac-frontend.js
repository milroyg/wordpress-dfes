(() => {
    'use strict';

    // Gestion des font-size dans le theme Hueman
    if (typeof window.fitText !== 'undefined') {
        document.querySelectorAll(':header').forEach(header => {
            header.removeAttribute('style');
        });
        window.removeEventListener('resize', window.fitText);
        window.removeEventListener('orientationchange', window.fitText);
    }

    // Implémente le proto startsWith pour IE11
    if (!String.prototype.startsWith) {
        String.prototype.startsWith = function (searchString, position) {
            position = position || 0;
            return this.substring(position, searchString.length) === searchString;
        };
    }

    // Transforme la chaîne en slug, équivalent ~ à sanitize_title
    if (!String.prototype.toSlug) {
        String.prototype.toSlug = function () {
            let str = this;
            str = str.trim().normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
            str = str.replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-+|-+$/g, '');
            return str;
        }
    }

    // Initialisation de la Fancybox
    if (window.Fancybox) {
        const language = window.navigator.userLanguage || window.navigator.language;
        const lng = language.split("-");
        const langFr = {
            fr: {
                CLOSE: "Fermer",
                NEXT: "Suivant",
                PREV: "Précédent",
                ERROR: "Le contenu ne peut être chargé. <br/> Essayer plus tard.",
                PLAY_START: "Lancer le diaporama",
                PLAY_STOP: "Diaporama sur pause",
                FULL_SCREEN: "Plein écran",
                THUMBS: "Miniatures",
                DOWNLOAD: "Télécharger",
                SHARE: "Partager",
                ZOOM: "Zoom"
            }
        };
        // window.Fancybox.defaults.i18n = { ...window.Fancybox.defaults.i18n, ...langFr };
        window.Fancybox.defaults.lang = lng[0];
        window.Fancybox.defaults.idleTime = 600;
        window.Fancybox.defaults.thumbs.autoStart = false;
        // window.Fancybox.defaults.buttons = ["zoom", "slideShow", "thumbs", "close"];
    }

    // Enable/Disable mouse focus
    document.body.addEventListener('mousedown', () => {
        document.body.classList.add('eac-using-mouse');
    });
    document.body.addEventListener('keydown', () => {
        document.body.classList.remove('eac-using-mouse');
    });

    function triggerKeyDownToClickEvent(evt) {
        const id = evt.code || evt.key || 0;
        if ('Space' === id) {
            evt.preventDefault();
            const activeElement = document.activeElement;

            if (activeElement.getAttribute('href') !== '#' && !activeElement.hasAttribute('data-fancybox')) {
                activeElement.dispatchEvent(new MouseEvent('click', { cancelable: true }));
            } else {
                activeElement.click();
            }
        }
    }

    // Evénement sur les boutons et les liens avec la touche Space pour l'accessibilité
    [
        'a.eac-accessible-link',
        '.mega-menu_nav-wrapper .mega-menu_top-link',
        '.mega-menu_nav-wrapper .mega-menu_sub-link',
        '.sitemap-posts-list a',
        '.swiper-pagination-bullet',
        '.eac-breadcrumbs-item a',
        '.woocommerce-mini-cart-item.mini_cart_item a',
        '.al-post__navigation-digit .page-numbers'
    ].forEach(selector => {
        document.body.addEventListener('keydown', evt => {
            if (evt.target.matches(selector)) {
                triggerKeyDownToClickEvent(evt);
            }
        });
    });

    // Les adresses e-mail obfusquées
    document.querySelectorAll('a.eac-accessible-link.obfuscated-link').forEach(item => {
        const dataHref = item.getAttribute('data-link');
        if (dataHref) {
            const dataMask = '#actus.';
            const mailTo = 'mailto:';
            const newHref = dataHref.replace(dataMask, '@');
            item.setAttribute('href', mailTo + newHref);
            item.removeAttribute('data-link');
        }
    });

    // Les numéro de téléphone obfusqués
    document.querySelectorAll('a.eac-accessible-link.obfuscated-tel').forEach(item => {
        const dataHref = item.getAttribute('data-link');
        if (dataHref) {
            const dataMask = '#actus.';
            const telTo = 'tel:';
            const newHref = dataHref.replace(dataMask, '');
            item.setAttribute('href', telTo + newHref);
            item.removeAttribute('data-link');
        }
    });
})();