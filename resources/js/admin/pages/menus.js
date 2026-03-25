import * as Turbo from "@hotwired/turbo";
import Sortable from 'sortablejs';

export function initMenuPage() {
    const rootList = document.getElementById('root-menu-list');
    const editForm = document.getElementById('editForm');
    if (!rootList || !editForm || !window.location.pathname.includes('/admin/menus')) return;

    const editParent = document.getElementById('editParent');
    const editActive = document.getElementById('editActive');
    const lbl = document.getElementById('toggleLabel');

    const checkParentStatus = (isParentActive) => {
        if (isParentActive === '0') {
            editActive.checked = false;
            editActive.disabled = true;
            editActive.parentElement.style.opacity = '0.7';
            lbl.innerText = 'Inactive (by Parent)';
            lbl.className = 'ml-3 font-bold text-red-400';
        } else {
            editActive.disabled = false;
            editActive.parentElement.style.opacity = '1';
            lbl.className = 'ml-3 font-bold text-slate-600';
        }
    };

    document.querySelectorAll('.menu-sortable-list').forEach(list => {
        new Sortable(list, { animation: 150, handle: '.drag-handle', onEnd: () => {
            const menus = [];
            const process = (ul, parentId) => {
                Array.from(ul.children).forEach((li, index) => {
                    if (li.dataset.id) {
                        menus.push({ id: li.dataset.id, parent_id: parentId, sort_order: index });
                        const subUl = li.querySelector('.menu-sortable-list');
                        if (subUl) process(subUl, li.dataset.id);
                    }
                });
            };
            process(rootList, null);
            window.axios.post('/admin/menus/update-order', { menus });
        }});
    });

    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.onclick = (e) => {
            e.preventDefault();
            const d = btn.dataset;
            editForm.action = `/admin/menus/${d.id}`;
            document.getElementById('editName').value = d.name;
            document.getElementById('editModalTitle').innerText = `Edit: ${d.name}`;
            editParent.value = d.parent || '';
            document.getElementById(d.multi == '1' ? 'edit-type-multi' : 'edit-type-functional').checked = true;
            
            if (d.parentActive === '0') checkParentStatus('0');
            else {
                editActive.disabled = false;
                editActive.parentElement.style.opacity = '1';
                editActive.checked = d.active === '1';
                lbl.innerText = d.active === '1' ? 'Active' : 'Inactive';
                lbl.className = 'ml-3 font-bold text-slate-600';
            }

            const modal = document.getElementById('editModal');
            modal.classList.remove('hidden');
            setTimeout(() => modal.classList.add('active'), 10);
        };
    });

    window.deleteMenu = (id) => {
        if (confirm('Delete this menu?')) {
            window.axios.delete(`/admin/menus/${id}`)
                .then(() => Turbo.visit(window.location.href));
        }
    };

    if(editParent) editParent.onchange = () => checkParentStatus(editParent.options[editParent.selectedIndex].dataset.active);
    if(editActive) editActive.onchange = () => lbl.innerText = editActive.checked ? 'Active' : 'Inactive';
}