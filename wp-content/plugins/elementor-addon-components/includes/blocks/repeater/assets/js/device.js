// device.js — ES module, no React.
// Notifie le callback uniquement si au moins un bloc a attributes.monitorDevice === true

const getStoreName = () => {
    if (typeof wp === 'undefined' || !wp.data) return null;
    const editSite = wp.data.select('core/edit-site');
    if (editSite && typeof editSite.getDeviceType === 'function') return 'core/edit-site';
    const editor = wp.data.select('core/editor');
    if (editor && typeof editor.getDeviceType === 'function') return 'core/editor';
    return null;
};

const storeName = getStoreName();

export function getDeviceType() {
    if (!storeName) return null;
    return wp.data.select(storeName).getDeviceType();
}

function anyBlockWantsMonitoring() {
    const blockEditor = wp.data.select('core/block-editor');
    if (!blockEditor || typeof blockEditor.getBlocks !== 'function') return false;
    const blocks = blockEditor.getBlocks();
    return blocks.some(b => b && b.attributes && b.attributes.monitorDevice === true);
}

// subscribeIfMonitored(callback, options)
// options:
//  - debounceMs: ms to debounce (default 1000)
//  - immediate: call callback immediately if monitored (default true)
export function subscribeIfMonitored(callback, options = {}) {
    if (!storeName || typeof callback !== 'function') return () => { };
    const { debounceMs = 1000, immediate = true } = options;

    let lastDevice = wp.data.select(storeName).getDeviceType();
    let timer = null;

    if (immediate && anyBlockWantsMonitoring()) {
        callback(lastDevice);
    }

    const unsub = wp.data.subscribe(() => {
        const currentDevice = wp.data.select(storeName).getDeviceType();
        if (currentDevice === lastDevice) return;
        lastDevice = currentDevice;

        if (!anyBlockWantsMonitoring()) return;

        if (debounceMs > 0) {
            clearTimeout(timer);
            timer = setTimeout(() => {
                callback(lastDevice);
            }, debounceMs);
        } else {
            callback(lastDevice);
        }
    });

    return () => {
        clearTimeout(timer);
        try { unsub(); } catch (e) { }
    };
}
