export function initBannersPage() {
    if (!document.querySelector('.leaf-menu-item') || !window.location.pathname.includes('/admin/banners')) return;

    window.loadBanners = (menuId, el) => {
        document.querySelectorAll('.leaf-menu-item').forEach(i => i.classList.remove('active', 'border-admin-blue', 'bg-slate-100'));
        el.classList.add('active', 'border-admin-blue', 'bg-slate-100');
        window.currentMenuId = menuId;
        
        window.axios.get(`/admin/banners/fetch/${menuId}`)
            .then(data => { document.getElementById('imageArea').innerHTML = data.html; });
    };

    window.openBannerUploadModal = () => {
        if (!window.currentMenuId) { alert("Please select a page first"); return; }
        document.getElementById('uploadForm').reset();
        
        const previewContainer = document.getElementById('uploadPreviewContainer');
        previewContainer.style.aspectRatio = '48/9';
        previewContainer.innerHTML = `<i class="fas fa-cloud-arrow-up text-2xl text-slate-300 mb-2 group-hover:text-admin-blue transition-colors"></i><span id="uploadPlaceholderText" class="text-slate-400 text-[10px] uppercase tracking-widest text-center px-2">Click to select 48:9 image</span>`;
        
        document.getElementById('ratioSlider').value = 0;
        
        const mwInput = document.getElementById('maxWidthInput');
        if(mwInput) {
            mwInput.value = 2000;
            mwInput.classList.remove('border-red-500');
            document.getElementById('maxWidthError').classList.add('hidden');
        }

        const modal = document.getElementById('uploadModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };

    const ratioSlider = document.getElementById('ratioSlider');
    if (ratioSlider) {
        ratioSlider.addEventListener('input', function() {
            const container = document.getElementById('uploadPreviewContainer');
            const text = document.getElementById('uploadPlaceholderText');
            const ratios = ['48/9', '23/9', '16/9'];
            const ratioText = ['48:9', '23:9', '16:9'];
            
            container.style.aspectRatio = ratios[this.value];
            if (text) text.innerText = `Click to select ${ratioText[this.value]} image`;
        });
    }

    window.openBannerEditModal = (id, name, fullSlug, isActive) => {
        window.currentBannerId = id;
        document.getElementById('editPreviewContainer').innerHTML = `<img src="/${fullSlug}/${name}?t=${Date.now()}" class="w-full h-full object-contain">`;
        document.getElementById('editActiveToggle').checked = (isActive == 1);
        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };

    document.getElementById('uploadForm').onsubmit = function(e) {
        e.preventDefault();
        
        const mwInput = document.getElementById('maxWidthInput');
        const mwError = document.getElementById('maxWidthError');
        const mwValue = parseInt(mwInput.value, 10);

        if (isNaN(mwValue) || mwValue < 500 || mwValue > 2000) {
            mwInput.classList.add('border-red-500');
            mwError.classList.remove('hidden');
            return;
        } else {
            mwInput.classList.remove('border-red-500');
            mwError.classList.add('hidden');
        }

        window.axios.post(`/admin/banners/upload/${window.currentMenuId}`, new FormData(this))
            .then(() => { window.closeModal('uploadModal'); window.loadBanners(window.currentMenuId, document.querySelector('.leaf-menu-item.active')); });
    };

    document.getElementById('editForm').onsubmit = function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        fd.append('is_active', document.getElementById('editActiveToggle').checked ? 1 : 0);
        
        window.axios.post(`/admin/banners/${window.currentBannerId}`, fd)
            .then(() => { window.closeModal('editModal'); window.loadBanners(window.currentMenuId, document.querySelector('.leaf-menu-item.active')); });
    };

    window.deleteBannerImage = (id) => {
        if (confirm('Delete this Banner Image?')) {
            window.axios.delete(`/admin/banners/${id}`)
                .then(() => window.loadBanners(window.currentMenuId, document.querySelector('.leaf-menu-item.active')));
        }
    };
}