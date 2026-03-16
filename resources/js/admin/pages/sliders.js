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
        setTimeout(() => modal.classList.add('active'), 10);
    };

    window.openSliderEditModal = (slider) => {
        window.currentSliderId = slider.id;
        document.getElementById('editH1').value = slider.header_1;
        document.getElementById('editH2').value = slider.header_2;
        document.getElementById('editDesc').value = slider.description;
        document.getElementById('editActive').checked = slider.is_active == 1;
        document.getElementById('editPreview').innerHTML = `<img src="/storage/${slider.image_path}?t=${Date.now()}" class="w-full h-full object-cover">`;
        
        ['editH1', 'editH2', 'editDesc'].forEach(id => {
            const el = document.getElementById(id);
            const counterId = id.replace('edit', 'editC').replace('H1', '1').replace('H2', '2').replace('Desc', 'D');
            window.updateCount(el, counterId, el?.getAttribute('maxlength'));
        });

        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };

    window.deleteSlider = (id) => {
        if (confirm('Delete this slider?')) {
            window.axios.delete(`/admin/sliders/${id}`)
                .then(() => Turbo.visit(window.location.href));
        }
    };

    addF.onsubmit = (e) => {
        e.preventDefault();
        window.axios.post('/admin/sliders', new FormData(addF))
            .then(() => Turbo.visit(window.location.href));
    };

    editF.onsubmit = (e) => {
        e.preventDefault();
        const fd = new FormData(editF);
        fd.append('_method', 'PUT');
        fd.append('is_active', document.getElementById('editActive').checked ? 1 : 0);
        
        window.axios.post(`/admin/sliders/${window.currentSliderId}`, fd)
            .then(() => Turbo.visit(window.location.href));
    };

    new Sortable(list, { animation: 150, handle: '.drag-handle', onEnd: () => {
        let orders = [];
        document.querySelectorAll('.sortable-item').forEach((el, index) => orders.push({ id: el.dataset.id, order: index + 1 }));
        window.axios.post('/admin/sliders/update-order', { orders });
    }});
}