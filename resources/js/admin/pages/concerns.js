import Sortable from 'sortablejs';

export function initConcernsPage() {
    if (!document.querySelector('.leaf-menu-item') || !window.location.pathname.includes('/admin/businesses')) return;

    window.currentGalleryIdToReplace = null;

    window.loadConcern = (menuId, el) => {
        document.querySelectorAll('.leaf-menu-item').forEach(i => i.classList.remove('active', 'border-admin-blue', 'bg-slate-100'));
        el.classList.add('active', 'border-admin-blue', 'bg-slate-100');
        
        window.axios.get(`/admin/concern-actions/fetch/${menuId}`)
            .then(data => { 
                document.getElementById('concernArea').innerHTML = data.html;
                window.initEditor('#concernDesc');
                
                const galleryList = document.getElementById('gallery-sortable');
                if (galleryList) {
                    new Sortable(galleryList, {
                        animation: 150, handle: '.drag-handle',
                        onEnd: () => {
                            let orders = [];
                            galleryList.querySelectorAll('.sortable-gallery-item').forEach((el, index) => {
                                orders.push({ id: el.dataset.id, order: index + 1 });
                            });
                            window.axios.post('/admin/concern-actions/update-gallery-order', { orders });
                        }
                    });
                }
            });
    };

    window.toggleRedirectLabel = (el, menuId) => {
        const isRedirect = el.checked ? 1 : 0;
        window.axios.post(`/admin/concern-actions/update-redirect/${menuId}`, { is_redirect: isRedirect })
            .then(() => {
                const lbl = document.getElementById('redirectLabel');
                if (lbl) {
                    lbl.innerText = el.checked ? 'Redirect On' : 'Redirect Off';
                    lbl.classList.toggle('text-admin-blue', el.checked);
                    lbl.classList.toggle('text-slate-600', !el.checked);
                }
            });
    };

    window.submitInfo = (e, menuId) => {
        e.preventDefault();
        window.axios.post(`/admin/concern-actions/update-info/${menuId}`, new FormData(e.target))
            .then(() => alert('Web Address Updated!'));
    };

    window.submitDesc = (e, menuId) => {
        e.preventDefault();
        if (typeof window.tinymce !== 'undefined') window.tinymce.triggerSave();
        window.axios.post(`/admin/concern-actions/update-description/${menuId}`, new FormData(e.target))
            .then(() => alert('Description Updated!'));
    };

    window.submitCover = (input, menuId) => {
        if (!input.files || input.files.length === 0) return;
        const fd = new FormData();
        fd.append('image', input.files[0]);
        
        window.axios.post(`/admin/concern-actions/upload-cover/${menuId}`, fd)
            .then(() => document.querySelector('.leaf-menu-item.active').click());
    };

    window.deleteCover = (menuId) => {
        if (confirm('Delete Cover Photo?')) {
            window.axios.delete(`/admin/concern-actions/delete-cover/${menuId}`)
                .then(() => document.querySelector('.leaf-menu-item.active').click());
        }
    };

    window.submitGallery = (input, menuId) => {
        if (!input.files || input.files.length === 0) return;
        const fd = new FormData();
        for(let i = 0; i < input.files.length; i++){
            fd.append('photos[]', input.files[i]);
        }
        
        window.axios.post(`/admin/concern-actions/upload-gallery/${menuId}`, fd)
            .then(() => document.querySelector('.leaf-menu-item.active').click());
    };

    window.triggerGalleryReplace = (galleryId) => {
        window.currentGalleryIdToReplace = galleryId;
        document.getElementById('galleryReplaceInput').click();
    };

    window.submitReplaceGallery = (input) => {
        if (!input.files || input.files.length === 0 || !window.currentGalleryIdToReplace) return;
        const fd = new FormData();
        fd.append('image', input.files[0]);
        
        window.axios.post(`/admin/concern-actions/replace-gallery/${window.currentGalleryIdToReplace}`, fd)
            .then(() => document.querySelector('.leaf-menu-item.active').click());
    };

    window.deleteGalleryItem = (galleryId) => {
        if (confirm('Delete this Gallery Photo?')) {
            window.axios.delete(`/admin/concern-actions/delete-gallery/${galleryId}`)
                .then(() => document.querySelector('.leaf-menu-item.active').click());
        }
    };
}