import * as Turbo from "@hotwired/turbo";
import Sortable from 'sortablejs';

export function initSlidersPage() {
    const list = document.getElementById('slider-list');
    const addF = document.querySelector('#addModal form');
    const editF = document.getElementById('editForm');
    if (!list || !window.location.pathname.includes('/admin/sliders')) return;

    window.openSliderAddModal = () => {
        addF.reset();
        document.getElementById('addPreview').innerHTML = `<i class="fas fa-cloud-arrow-up text-2xl text-slate-300 mb-2"></i>`;
        const modal = document.getElementById('addModal');
        modal.classList.remove('hidden');
        addF.scrollTop = 0;
        setTimeout(() => modal.classList.add('active'), 10);
    };

    window.openSliderEditModal = (slider) => {
        window.currentSliderId = slider.id;
        
        document.getElementById('editH1').value = slider.header_1;
        document.getElementById('editH2').value = slider.header_2;
        document.getElementById('editDesc').value = slider.description || '';
        
        document.getElementById('editBT').value = slider.button_text || 'Explore More';
        document.getElementById('editLink').value = slider.link_url || '';
        
        document.getElementById('editActive').checked = slider.is_active == 1;
        document.getElementById('editPreview').innerHTML = `<img src="/storage/${slider.image_path}?t=${Date.now()}" class="w-full h-full object-cover">`;
        
        ['editH1', 'editH2', 'editDesc', 'editBT'].forEach(id => {
            const el = document.getElementById(id);
            if (!el) return;
            
            let counterId = '';
            if (id === 'editH1') counterId = 'editC1';
            else if (id === 'editH2') counterId = 'editC2';
            else if (id === 'editDesc') counterId = 'editCD';
            else if (id === 'editBT') counterId = 'editCBT';

            if (counterId) {
                window.updateCount(el, counterId, el.getAttribute('maxlength'));
            }
        });

        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');

        editF.scrollTop = 0;

        setTimeout(() => modal.classList.add('active'), 10);
    };

     window.deleteSlider = (id) => {
        if (confirm('Delete this slider?')) {
            const row = document.querySelector(`.sortable-item[data-id="${id}"]`);
            if (row) {
                row.style.opacity = '0.5';
                row.style.pointerEvents = 'none';
            }

            window.axios.delete(`/admin/sliders/${id}`)
                .then(() => {
                    if (Turbo.cache) Turbo.cache.clear();
                    Turbo.visit(window.location.href, { action: 'replace' });
                });
        }
    };

    addF.onsubmit = (e) => {
        e.preventDefault();
        
        const btn = e.submitter || addF.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Uploading...';
        btn.disabled = true;

        window.axios.post('/admin/sliders', new FormData(addF))
            .then(() => {
                if (Turbo.cache) Turbo.cache.clear();
                Turbo.visit(window.location.href, { action: 'replace' });
            })
            .catch(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
    };

    editF.onsubmit = (e) => {
        e.preventDefault();

        const btn = e.submitter || editF.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Updating...';
        btn.disabled = true;

        const fd = new FormData(editF);
        fd.append('_method', 'PUT');
        fd.append('is_active', document.getElementById('editActive').checked ? 1 : 0);
        
        window.axios.post(`/admin/sliders/${window.currentSliderId}`, fd)
            .then(() => {
                if (Turbo.cache) Turbo.cache.clear();
                Turbo.visit(window.location.href, { action: 'replace' });
            })
            .catch(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
    };

    new Sortable(list, { animation: 150, handle: '.drag-handle', onEnd: () => {
        let orders = [];
        document.querySelectorAll('.sortable-item').forEach((el, index) => orders.push({ id: el.dataset.id, order: index + 1 }));
        window.axios.post('/admin/sliders/update-order', { orders });
    }});
}