import * as Turbo from "@hotwired/turbo";
import Sortable from 'sortablejs';

export function initFooterPage() {
    if (!window.location.pathname.includes('/admin/footer')) return;

    const qlSortable = document.getElementById('ql-sortable');
    if (qlSortable) {
        new Sortable(qlSortable, {
            animation: 150,
            handle: '.drag-handle'
        });
    }

    const socialSortable = document.getElementById('social-sortable');
    if (socialSortable) {
        new Sortable(socialSortable, {
            animation: 150,
            handle: '.drag-handle'
        });
    }
    
    window.openFooterModal = (id) => {
        const modal = document.getElementById(id);
        if (!modal) return;
        
        modal.classList.remove('hidden');

        const form = modal.querySelector('form');
        if (form) form.scrollTop = 0;

        setTimeout(() => modal.classList.add('active'), 10);
        
        modal.querySelectorAll('input[maxlength], textarea[maxlength]').forEach(el => {
            const match = el.getAttribute('oninput')?.match(/'([^']+)'/);
            if (match) window.updateCount(el, match[1], el.getAttribute('maxlength'));
        });
    };
    
    window.fetchFooterMap = () => {
        const url = document.getElementById('map_input').value;
        if (url.includes('google.com/maps')) { 
            document.getElementById('map_preview').src = url; 
            
            const btn = document.getElementById('mapSaveBtn');
            if (btn) {
                btn.disabled = false; 
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }
    };
    
    document.querySelectorAll('.modal-overlay form').forEach(form => {
        form.onsubmit = (e) => {
            e.preventDefault();
            
            const btn = form.closest('.modal-content').querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';
            }

            window.axios.post(form.action, new FormData(form))
                .then(() => Turbo.visit(window.location.href));
        };
    });
}