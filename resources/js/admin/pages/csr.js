import * as Turbo from "@hotwired/turbo";
import Sortable from 'sortablejs';
import { setupModule } from '../core/api';

export function initCSRPage() {
    if (!window.location.pathname.includes('/admin/csr')) return;

    window.initEditor('#addDesc');
    window.initEditor('#editDesc');

    setupModule('csr', '/admin/csr-actions/store', '/admin/csr-actions', 'curCsrId');

    document.querySelectorAll('.csr-sortable-list').forEach(list => {
        new Sortable(list, {
            animation: 150,
            handle: '.drag-handle',
            onEnd: () => {
                let orders = [];
                list.querySelectorAll('.sortable-item').forEach((el, index) => {
                    orders.push({ id: el.dataset.id, order: index + 1 });
                });
                window.axios.post('/admin/csr-actions/update-order', { orders });
            }
        });
    });
    
    window.openCsrAddModal = () => {
        const form = document.querySelector('#addModal form');
        if (form) form.reset();

        const addPreview = document.getElementById('addPreview');
        if (addPreview) {
            addPreview.innerHTML = `
                <i class="fas fa-camera text-3xl text-slate-300 mb-2 group-hover:text-admin-blue transition-colors"></i>
                <span class="text-slate-400 font-bold text-[10px] uppercase tracking-widest text-center px-4">Select Image</span>
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
    
    window.openCsrEditModal = (item) => {
        window.curCsrId = item.id;

        const form = document.querySelector('#editModal form');

        document.getElementById('editTitle').value = item.title;
        document.getElementById('editDate').value = item.csr_date.split('T')[0];
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

        if (form) form.scrollTop = 0;

        setTimeout(() => modal.classList.add('active'), 10);
    };
    
    window.deleteCsr = (id) => { 
        if(confirm('Delete this CSR?')) {
            window.axios.delete(`/admin/csr-actions/${id}`)
                .then(() => Turbo.visit(window.location.href)); 
        }
    };
}