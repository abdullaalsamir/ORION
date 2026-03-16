import * as Turbo from "@hotwired/turbo";
import Sortable from 'sortablejs';

export function initVideoGalleryPage() {
    if (!window.location.pathname.includes('/admin/video-gallery')) return;

    window.curVideoId = null;

    const list = document.getElementById('video-sortable-list');
    if (list) {
        new Sortable(list, {
            animation: 150,
            handle: '.drag-handle',
            onEnd: () => {
                let orders = [];
                list.querySelectorAll('.sortable-video-item').forEach((el, index) => {
                    orders.push({ id: el.dataset.id, order: index + 1 });
                });
                window.axios.post('/admin/video-gallery-actions/update-order', { orders });
            }
        });
    }

    window.openVideoAddModal = () => {
        document.getElementById('addVideoForm').reset();
        document.getElementById('addThumbPreview').innerHTML = `<i class="fas fa-image text-3xl text-slate-300 mb-2 group-hover:text-admin-blue"></i><span class="text-slate-400 font-bold text-[9px] uppercase tracking-widest text-center px-4">Upload Cover</span>`;
        document.getElementById('addVideoName').innerHTML = 'Select Video File<br>(Max 200MB)';
        
        const modal = document.getElementById('addModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };

    window.openVideoEditModal = (item) => {
        window.curVideoId = item.id;
        document.getElementById('editTitle').value = item.title;
        document.getElementById('editActive').checked = item.is_active == 1;
        document.getElementById('videoStatusLabel').innerText = item.is_active == 1 ? 'Active' : 'Inactive';
        
        document.getElementById('editThumbPreview').innerHTML = `<img src="/video-gallery-files/thumbnails/${item.thumbnail_path.split('/').pop()}?t=${Date.now()}" class="w-full h-full object-cover">`;
        document.getElementById('editVideoName').innerText = 'Click to Replace Video';

        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };

    window.deleteVideo = (id) => {
        if (confirm('Delete this Video?')) {
            window.axios.delete(`/admin/video-gallery-actions/${id}`)
                .then(() => Turbo.visit(window.location.href));
        }
    };

    document.getElementById('editActive')?.addEventListener('change', function() {
        document.getElementById('videoStatusLabel').innerText = this.checked ? 'Active' : 'Inactive';
    });

    document.getElementById('addVideoForm').onsubmit = function(e) {
        e.preventDefault();
        const btn = document.getElementById('addSubmitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Uploading...';
        
        window.axios.post('/admin/video-gallery-actions/store', new FormData(this))
            .then(() => Turbo.visit(window.location.href))
            .finally(() => { btn.disabled = false; btn.innerText = 'Upload Video'; });
    };

    document.getElementById('editForm').onsubmit = function(e) {
        e.preventDefault();
        const btn = document.getElementById('editSubmitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';
        
        const fd = new FormData(this);
        fd.append('_method', 'PUT');
        fd.set('is_active', document.getElementById('editActive').checked ? 1 : 0);

        window.axios.post(`/admin/video-gallery-actions/${window.curVideoId}`, fd)
            .then(() => Turbo.visit(window.location.href))
            .finally(() => { btn.disabled = false; btn.innerText = 'Update Video'; });
    };
}