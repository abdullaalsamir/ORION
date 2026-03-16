import * as Turbo from "@hotwired/turbo";

export function setupModule(pathPart, storeUrl, updateUrlPrefix, currentIdKey) {
    if (!window.location.pathname.includes(pathPart)) return;
    
    const addF = document.querySelector('#addModal form');
    const editF = document.getElementById('editForm');

    if (addF) addF.onsubmit = (e) => {
        e.preventDefault();
        window.axios.post(storeUrl, new FormData(addF))
            .then(() => Turbo.visit(window.location.href));
    };

    if (editF) editF.onsubmit = (e) => {
        e.preventDefault();
        const fd = new FormData(editF);
        fd.append('_method', 'PUT');
        
        if (document.getElementById('editPin')) {
            fd.append('is_pin', document.getElementById('editPin').checked ? 1 : 0);
        }
        
        fd.append('is_active', document.getElementById('editActive').checked ? 1 : 0);
        
        window.axios.post(`${updateUrlPrefix}/${window[currentIdKey]}`, fd)
            .then(() => Turbo.visit(window.location.href));
    };
}