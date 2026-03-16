import * as Turbo from "@hotwired/turbo";
import { setupModule } from '../core/api';

export function initNewsPage() {
    if (!window.location.pathname.includes('news-and-announcements')) return;

    window.initEditor('#addDesc');
    window.initEditor('#editDesc');

    setupModule('news-and-announcements', '/admin/news-actions/store', '/admin/news-actions', 'curNewsId');
    
    window.openNewsAddModal = () => {
        const form = document.querySelector('#addModal form');
        if (form) form.reset();

        if (typeof window.tinymce !== 'undefined' && window.tinymce.get('addDesc')) {
            window.tinymce.get('addDesc').setContent('');
        }

        const pin = form.querySelector('input[name="is_pin"][type="checkbox"]');
        if (pin) {
            pin.checked = true;
            window.togglePinText(pin, 'addPinLabel');
        }

        const modal = document.getElementById('addModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };
    
    window.openNewsEditModal = (item, slug) => {
        window.curNewsId = item.id;
        document.getElementById('editTitle').value = item.title;
        document.getElementById('editDate').value = item.news_date.split('T')[0];
        document.getElementById('editActive').checked = item.is_active == 1;
        
        if(document.getElementById('editPin')) {
            document.getElementById('editPin').checked = item.is_pin == 1;
            window.togglePinText(document.getElementById('editPin'), 'editPinLabel');
        }
        
        const preview = document.getElementById('editPreview');
        preview.classList.remove('p-6');
        if (item.file_type === 'pdf') {
            preview.classList.add('p-6');
            preview.innerHTML = `<div class="flex flex-col items-center justify-center text-center"><i class="fas fa-file-pdf text-red-600 text-5xl mb-3"></i><span class="text-[11px] font-bold text-slate-600 uppercase font-sans">PDF Notice</span></div>`;
        } else {
            preview.innerHTML = `<img src="/${slug}/${item.file_path.split('/').pop()}?t=${Date.now()}" class="w-full h-full object-cover">`;
        }
        
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

    window.handleNewsPreview = function (input, previewId, fileNameId) {
        const preview = document.getElementById(previewId);
        const fileNameEl = document.getElementById(fileNameId);
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        
        const form = input.closest('form');
        const titleInp = form.querySelector('input[name="title"]');
        if (titleInp && !titleInp.value) {
            titleInp.value = file.name.replace(/\.[^/.]+$/, "").replace(/[-_]/g, ' ');
            const counterId = titleInp.getAttribute('oninput')?.match(/'([^']+)'/)?.[1];
            if(counterId) window.updateCount(titleInp, counterId, titleInp.getAttribute('maxlength'));
        }

        if (fileNameEl) { fileNameEl.textContent = file.name; fileNameEl.classList.remove('hidden'); }
        preview.innerHTML = ''; preview.classList.remove('p-6');
        if (file.type === 'application/pdf') {
            preview.classList.add('p-6');
            preview.innerHTML = `<div class="flex flex-col items-center justify-center text-center"><i class="fas fa-file-pdf text-red-600 text-5xl mb-3"></i><span class="text-[11px] font-bold text-slate-600 uppercase font-sans">PDF Notice</span></div>`;
        } else {
            const reader = new FileReader();
            reader.onload = (e) => preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            reader.readAsDataURL(file);
        }
    };

    window.togglePinText = (el, labelId) => {
        const label = document.getElementById(labelId);
        if (!label) return;
        label.innerText = el.checked ? "Pin Yes" : "Pin No";
        label.classList.toggle('text-admin-blue', el.checked);
    };

    window.deleteNews = (id) => { 
        if(confirm('Delete this News?')) { 
            window.axios.delete(`/admin/news-actions/${id}`)
                .then(() => Turbo.visit(window.location.href)); 
        } 
    };
}