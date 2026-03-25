import * as Turbo from "@hotwired/turbo";

export function setupModule(pathPart, storeUrl, updateUrlPrefix, currentIdKey) {
    if (!window.location.pathname.includes(pathPart)) return;
    
    const addF = document.querySelector('#addModal form');
    const editF = document.getElementById('editForm');

    if (addF) addF.onsubmit = (e) => {
        e.preventDefault();
        
        const btn = e.submitter || addF.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';
        btn.disabled = true;

        window.axios.post(storeUrl, new FormData(addF))
            .then(() => {
                if (Turbo.cache) Turbo.cache.clear();
                Turbo.visit(window.location.href, { action: 'replace' });
            })
            .catch(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
    };

    if (editF) editF.onsubmit = (e) => {
        e.preventDefault();

        const btn = e.submitter || editF.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Updating...';
        btn.disabled = true;

        const fd = new FormData(editF);
        fd.append('_method', 'PUT');
        
        if (document.getElementById('editPin')) {
            fd.append('is_pin', document.getElementById('editPin').checked ? 1 : 0);
        }
        
        fd.append('is_active', document.getElementById('editActive').checked ? 1 : 0);
        
        window.axios.post(`${updateUrlPrefix}/${window[currentIdKey]}`, fd)
            .then(() => {
                if (Turbo.cache) Turbo.cache.clear();
                Turbo.visit(window.location.href, { action: 'replace' });
            })
            .catch(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
    };
}