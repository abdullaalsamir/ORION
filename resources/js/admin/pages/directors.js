import * as Turbo from "@hotwired/turbo";
import Sortable from 'sortablejs';
import { setupModule } from '../core/api';

export function initDirectorsPage() {
    if (!window.location.pathname.includes('/admin/board-of-directors')) return;

    window.initEditor('#addDesc');
    window.initEditor('#editDesc');

    setupModule('board-of-directors', '/admin/director-actions/store', '/admin/director-actions', 'curDirId');

    const list = document.getElementById('directors-sortable-list');
    if (list) {
        new Sortable(list, {
            animation: 150,
            handle: '.drag-handle',
            onEnd: () => {
                let orders = [];
                document.querySelectorAll('.sortable-item').forEach((el, index) => {
                    orders.push({ id: el.dataset.id, order: index + 1 });
                });
                window.axios.post('/admin/director-actions/update-order', { orders });
            }
        });
    }

    window.openDirectorAddModal = () => {
        const form = document.querySelector('#addModal form');
        if (form) {
            form.reset();
        }

        const addPreview = document.getElementById('addPreview');
        if (addPreview) {
            addPreview.innerHTML = `
                <i class="fas fa-camera text-3xl text-slate-300 mb-2 group-hover:text-admin-blue"></i>
                <span class="text-slate-400 font-bold text-[9px] uppercase tracking-widest text-center px-4 opacity-60">Upload Portrait</span>
            `;
        }

        if (typeof window.tinymce !== 'undefined' && window.tinymce.get('addDesc')) {
            window.tinymce.get('addDesc').setContent('');
        }

        const modal = document.getElementById('addModal');
        modal.classList.remove('hidden');
        
        if (form) form.scrollTop = 0;

        setTimeout(() => modal.classList.add('active'), 10);
    };

    window.openDirectorEditModal = (item) => {
        window.curDirId = item.id;

        document.getElementById('editName').value = item.name;
        document.getElementById('editDesignation').value = item.designation;
        document.getElementById('editActive').checked = item.is_active == 1;

        document.getElementById('editPreview').innerHTML =
            `<img src="/storage/${item.image_path}?t=${Date.now()}" class="w-full h-full object-cover">`;

        const content = item.description || '';
        const descTextarea = document.getElementById('editDesc');
        if (descTextarea) descTextarea.value = content;

        if (typeof window.tinymce !== 'undefined') {
            const editor = window.tinymce.get('editDesc');
            if (editor) {
                editor.setContent(content);
            } else {
                window.initEditor('#editDesc');
                setTimeout(() => {
                    if (window.tinymce.get('editDesc')) {
                        window.tinymce.get('editDesc').setContent(content);
                    }
                }, 100);
            }
        }

        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        
        const form = document.querySelector('#editModal form');
        if (form) form.scrollTop = 0;

        setTimeout(() => modal.classList.add('active'), 10);
    };

    window.deleteDirector = (id) => {
        if (confirm('Delete this Profile?')) {
            window.axios.delete(`/admin/director-actions/${id}`)
                .then(() => Turbo.visit(window.location.href));
        }
    };
}