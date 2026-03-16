import * as Turbo from "@hotwired/turbo";
import { setupModule } from '../core/api';

export function initCSRPage() {
    if (!window.location.pathname.includes('/admin/csr')) return;

    window.initEditor('#addDesc');
    window.initEditor('#editDesc');

    setupModule('csr', '/admin/csr-actions/store', '/admin/csr-actions', 'curCsrId');
    
    window.openCsrAddModal = () => {
        const form = document.querySelector('#addModal form');
        if (form) form.reset();

        if (typeof window.tinymce !== 'undefined' && window.tinymce.get('addDesc')) {
            window.tinymce.get('addDesc').setContent('');
        }

        const modal = document.getElementById('addModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };
    
    window.openCsrEditModal = (item, slug) => {
        window.curCsrId = item.id;
        document.getElementById('editTitle').value = item.title;
        document.getElementById('editDate').value = item.csr_date.split('T')[0];
        document.getElementById('editActive').checked = item.is_active == 1;
        document.getElementById('editPreview').innerHTML = `<img src="/${slug}/${item.image_path.split('/').pop()}?t=${Date.now()}" class="w-full h-full object-cover">`;
        
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
        setTimeout(() => modal.classList.add('active'), 10);
    };
    
    window.deleteCsr = (id) => { 
        if(confirm('Delete this CSR?')) {
            window.axios.delete(`/admin/csr-actions/${id}`)
                .then(() => Turbo.visit(window.location.href)); 
        }
    };
}