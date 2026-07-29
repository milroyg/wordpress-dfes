(function () {
    'use strict';

    // utilitaires pour les animations slideUp/slideDown/slideToggle
    function getStyleNumber(el, prop) {
        return parseFloat(window.getComputedStyle(el)[prop]) || 0;
    }

    function slideUp(el, duration = 300) {
        const height = el.scrollHeight;
        const paddingTop = getStyleNumber(el, 'paddingTop');
        const paddingBottom = getStyleNumber(el, 'paddingBottom');
        el.style.boxSizing = 'border-box';
        el.style.height = height + 'px';
        el.style.transitionProperty = 'height, padding-top, padding-bottom';
        el.style.transitionDuration = duration + 'ms';
        el.offsetHeight; // force repaint
        el.style.overflow = 'hidden';
        el.style.height = 0;
        el.style.paddingTop = '0px';
        el.style.paddingBottom = '0px';
        window.setTimeout(() => {
            el.style.display = 'none';
            // restore inline styles removed except those we want gone
            el.style.removeProperty('height');
            el.style.removeProperty('overflow');
            el.style.removeProperty('transition-duration');
            el.style.removeProperty('transition-property');
            el.style.removeProperty('padding-top');
            el.style.removeProperty('padding-bottom');
        }, duration);
    }

    function slideDown(el, duration = 300) {
        // prepare element for measuring
        el.style.removeProperty('display');
        let display = window.getComputedStyle(el).display;
        if (display === 'none') display = 'block';
        el.style.display = display;

        const height = el.scrollHeight;
        const paddingTop = getStyleNumber(el, 'paddingTop');
        const paddingBottom = getStyleNumber(el, 'paddingBottom');

        // start from zero sizes but preserve computed paddings by setting explicit 0 then animating to measured paddings
        el.style.overflow = 'hidden';
        el.style.height = '0px';
        el.style.paddingTop = '0px';
        el.style.paddingBottom = '0px';
        el.offsetHeight; // force repaint

        el.style.transitionProperty = 'height, padding-top, padding-bottom';
        el.style.transitionDuration = duration + 'ms';
        el.style.height = height + 'px';
        el.style.paddingTop = paddingTop + 'px';
        el.style.paddingBottom = paddingBottom + 'px';

        window.setTimeout(() => {
            // clear inline properties to allow natural layout afterwards
            el.style.removeProperty('height');
            el.style.removeProperty('overflow');
            el.style.removeProperty('transition-duration');
            el.style.removeProperty('transition-property');
            el.style.removeProperty('padding-top');
            el.style.removeProperty('padding-bottom');
        }, duration);
    }

    // helper pour slideToggle selon état visible
    function isVisible(el) {
        return !!(el.offsetWidth || el.offsetHeight || el.getClientRects().length);
    }

    function rotateIcons(container, deg) {
        const svgs = container.querySelectorAll('.acf-repeater__faq-toggler svg');
        const is = container.querySelectorAll('.acf-repeater__faq-toggler i');
        svgs.forEach(s => (s.style.transform = `rotate(${deg}deg)`));
        is.forEach(i => (i.style.transform = `rotate(${deg}deg)`));
    }

    // sélection de tous les triggers
    const triggers = Array.from(document.querySelectorAll('.acf-repeater__faq-question'));
    const responses = Array.from(document.querySelectorAll('.acf-repeater__faq-answer'));

    // initialise attributs ARIA (optionnel, utile pour accessibilité)
    triggers.forEach(t => {
        if (!t.hasAttribute('role')) t.setAttribute('role', 'button');
        if (!t.hasAttribute('tabindex')) t.setAttribute('tabindex', '0');
        if (!t.hasAttribute('aria-expanded')) t.setAttribute('aria-expanded', 'false');
    });

    function closeAllExcept(exceptTrigger) {
        triggers.forEach(t => {
            if (t === exceptTrigger) return;
            t.classList.remove('open');
            t.setAttribute('aria-expanded', 'false');
            rotateIcons(t, 0);
        });
        responses.forEach(r => {
            if (r === (exceptTrigger ? exceptTrigger.nextElementSibling : null)) return;
            if (isVisible(r)) slideUp(r, 300);
        });
    }

    function handleToggle(evt) {
        const type = evt.type;
        const trigger = evt.currentTarget;
        const response = trigger.nextElementSibling;

        if (type === 'keydown') {
            const code = evt.code || evt.key || '';
            if (code !== 'Enter' && code !== 'Space' && code !== ' ' && code !== 'Spacebar') return;
            evt.preventDefault();
        } else {
            // click
            evt.preventDefault();
        }

        if (trigger.classList.contains('open')) {
            trigger.classList.remove('open');
            trigger.setAttribute('aria-expanded', 'false');
            rotateIcons(trigger, 0);
            if (isVisible(response)) slideUp(response, 300);
        } else {
            closeAllExcept(trigger);
            trigger.classList.add('open');
            trigger.setAttribute('aria-expanded', 'true');
            rotateIcons(trigger, 180);
            if (!isVisible(response)) slideDown(response, 300);
        }
    }

    triggers.forEach(t => {
        t.addEventListener('click', handleToggle);
        t.addEventListener('keydown', handleToggle);
    });
})();
