import * as Turbo from "@hotwired/turbo";
import Sortable from 'sortablejs';

export function initCareerPage() {
    if (!window.location.pathname.includes('/admin/career')) return;

    window.initEditor('#addDesc');
    window.initEditor('#editDesc');

    window.openModal = (id) => {
        const m = document.getElementById(id);
        if (m) {
            m.classList.remove('hidden');
            setTimeout(() => m.classList.add('active'), 10);
        }
    };

    window.openCareerAddModal = () => {
        const form = document.getElementById('addForm');
        if (form) form.reset();
        
        const pdfInputs = document.getElementById('addPdfInputs');
        if (pdfInputs) pdfInputs.innerHTML = '';

        if (typeof window.tinymce !== 'undefined' && window.tinymce.get('addDesc')) {
            window.tinymce.get('addDesc').setContent('');
        }
        
        window.openModal('addModal');
    };

    window.openCareerEditModal = (job) => {
        const form = document.getElementById('editForm');
        if (form) form.action = `/admin/career/${job.id}`;
        
        document.getElementById('editTitle').value = job.title;
        document.getElementById('editLocation').value = job.location || '';
        document.getElementById('editFrom').value = job.on_from ? job.on_from.split('T')[0] : '';
        document.getElementById('editTo').value = job.on_to ? job.on_to.split('T')[0] : '';
        document.getElementById('editJobType').value = job.job_type;
        document.getElementById('editApplyType').value = job.apply_type;
        
        const descTextarea = document.getElementById('editDesc');
        const content = job.description || '';
        if (descTextarea) descTextarea.value = content;

        if (typeof window.tinymce !== 'undefined') {
            const editor = window.tinymce.get('editDesc');
            if (editor) {
                editor.setContent(content);
            } else {
                window.initEditor('#editDesc');
                setTimeout(() => {
                    if(window.tinymce.get('editDesc')) window.tinymce.get('editDesc').setContent(content);
                }, 100);
            }
        }

        const act = document.getElementById('editActive');
        if (act) {
            act.checked = job.is_active == 1;
            const statusLabel = document.getElementById('careerStatusLabel');
            if (statusLabel) statusLabel.innerText = act.checked ? 'Active' : 'Inactive';
        }

        window.openModal('editModal');
    };

    window.processFileSelection = async (input, mode) => {
        const file = input.files ? input.files[0] : null;
        const container = document.getElementById(`${mode}PdfInputs`);
        const overlay = document.getElementById(`${mode}Overlay`);
        const btn = document.getElementById(`${mode}SubmitBtn`);

        if (container) container.innerHTML = '';

        if (!file || file.type !== 'application/pdf') return;

        if (typeof window.pdfjsLib === 'undefined') {
            alert("PDF processor is still loading, please try again in a second.");
            input.value = '';
            return;
        }

        if (overlay) overlay.style.display = 'flex';
        if (btn) btn.disabled = true;

        try {
            const arrayBuffer = await file.arrayBuffer();
            const pdf = await window.pdfjsLib.getDocument(arrayBuffer).promise;

            for (let i = 1; i <= pdf.numPages; i++) {
                const page = await pdf.getPage(i);
                const viewport = page.getViewport({ scale: 3 });

                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.width = viewport.width;
                canvas.height = viewport.height;

                await page.render({ canvasContext: context, viewport: viewport }).promise;
                const base64 = canvas.toDataURL('image/webp', 0.7);

                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'pdf_images[]';
                hiddenInput.value = base64;
                if (container) container.appendChild(hiddenInput);
            }
        } catch (error) {
            console.error("Error processing PDF:", error);
            alert("Failed to process PDF file.");
            input.value = '';
        } finally {
            if (overlay) overlay.style.display = 'none';
            if (btn) btn.disabled = false;
        }
    };

    const list = document.getElementById('career-list');
    if (list) {
        new Sortable(list, {
            animation: 150, handle: '.drag-handle',
            onEnd: function () {
                const items = Array.from(list.children).map((el, i) => ({ id: el.dataset.id, order: i }));
                window.axios.post('/admin/career/update-order', { items });
            }
        });
    }

    window.deleteCareer = (id) => {
        if (confirm('Delete this job post?')) {
            window.axios.delete(`/admin/career/${id}`)
                .then(() => Turbo.visit(window.location.href));
        }
    };
}