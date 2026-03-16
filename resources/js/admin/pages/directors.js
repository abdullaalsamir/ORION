import * as Turbo from "@hotwired/turbo";
import { setupModule } from '../core/api';

export function initDirectorsPage() {
    if (!window.location.pathname.includes('/admin/board-of-directors')) return;

    window.initEditor('#addDesc');
    window.initEditor('#editDesc');

    setupModule('board-of-directors', '/admin/director-actions/store', '/admin/director-actions', 'curDirId');

    window.openDirectorAddModal = () => {
        const form = document.querySelector('#addModal form');
        if (form) form.reset();

        if (typeof window.tinymce !== 'undefined' && window.tinymce.get('addDesc')) {
            window.tinymce.get('addDesc').setContent('');
        }

        const modal = document.getElementById('addModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };

    window.openDirectorEditModal = (item, slug) => {
        window.curDirId = item.id;

        document.getElementById('editName').value = item.name;
        document.getElementById('editDesignation').value = item.designation;
        document.getElementById('editActive').checked = item.is_active == 1;

        document.getElementById('editPreview').innerHTML =
            `<img src="/${slug}/${item.image_path.split('/').pop()}?t=${Date.now()}" class="w-full h-full object-cover">`;

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

    window.deleteDirector = (id) => {
        if (confirm('Delete this Profile?')) {
            window.axios.delete(`/admin/director-actions/${id}`)
                .then(() => Turbo.visit(window.location.href));
        }
    };
}