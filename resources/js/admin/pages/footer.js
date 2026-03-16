import * as Turbo from "@hotwired/turbo";

export function initFooterPage() {
    if (!window.location.pathname.includes('/admin/footer')) return;
    
    window.openFooterModal = (id) => {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.remove('hidden');
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
            document.getElementById('mapSaveBtn').disabled = false; 
        }
    };
    
    document.querySelectorAll('.modal-overlay form').forEach(form => {
        form.onsubmit = (e) => {
            e.preventDefault();
            window.axios.post(form.action, new FormData(form))
                .then(() => Turbo.visit(window.location.href));
        };
    });
}