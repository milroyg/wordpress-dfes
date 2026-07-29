// util.js - Module pour les utilitaires du repeater block

export const renderHasUrl = (fields, selectedFields) => {
    return (Array.isArray(selectedFields) ? selectedFields : [])
        .some(sf => {
            const subfieldObj = fields.find(s => s.key === sf);
            return subfieldObj && ['url', 'link', 'page_link'].includes(subfieldObj.type);
        });
};

export const renderHasImage = (fields, selectedFields) => {
    return (Array.isArray(selectedFields) ? selectedFields : [])
        .some(sf => {
            const subfieldObj = fields.find(s => s.key === sf);
            return subfieldObj && ['image', 'attachment'].includes(subfieldObj.type);
        });
};