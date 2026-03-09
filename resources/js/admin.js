import * as Turbo from "@hotwired/turbo";

import Sortable from 'sortablejs';

import ace from 'ace-builds/src-noconflict/ace';
import 'ace-builds/src-noconflict/mode-html';
import 'ace-builds/src-noconflict/theme-github';
import 'ace-builds/src-noconflict/ext-searchbox';
import aceWorkerUrl from 'ace-builds/src-noconflict/worker-html?url';
ace.config.setModuleUrl('ace/mode/html_worker', aceWorkerUrl);

import tinymce from 'tinymce';
import 'tinymce/icons/default/icons.min.js';
import 'tinymce/themes/silver/theme.min.js';
import 'tinymce/models/dom/model.min.js';
import 'tinymce/skins/ui/oxide/skin.js';
import 'tinymce/skins/ui/oxide/content.js';
import 'tinymce/skins/content/default/content.js';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/link';
import 'tinymce/plugins/code';
window.tinymce = tinymce;

import * as pdfjsLib from 'pdfjs-dist';
import pdfWorker from 'pdfjs-dist/build/pdf.worker?url';
pdfjsLib.GlobalWorkerOptions.workerSrc = pdfWorker;
window.pdfjsLib = pdfjsLib;

Turbo.start();

async function handleResponse(res) {
    const contentType = res.headers.get("content-type");
    if (contentType && contentType.indexOf("application/json") !== -1) {
        const data = await res.json();
        if (res.ok) return data;
        const errorMsg = data.error || data.message || "Server validation failed.";
        alert("Error: " + errorMsg);
        throw new Error(errorMsg);
    } else {
        const text = await res.text();
        console.error("Server returned non-JSON:", text);
        alert("System Error: The server sent an invalid response.");
        throw new Error("Invalid JSON response");
    }
}

const fetchHeaders = () => ({
    'Accept': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
});

function setupModule(pathPart, storeUrl, updateUrlPrefix, currentIdKey) {
    if (!window.location.pathname.includes(pathPart)) return;
    const addF = document.querySelector('#addModal form');
    const editF = document.getElementById('editForm');

    if (addF) addF.onsubmit = (e) => {
        e.preventDefault();
        fetch(storeUrl, { method: 'POST', body: new FormData(addF), headers: fetchHeaders() })
        .then(handleResponse).then(() => Turbo.visit(window.location.href));
    };

    if (editF) editF.onsubmit = (e) => {
        e.preventDefault();
        const fd = new FormData(editF);
        fd.append('_method', 'PUT');
        if(document.getElementById('editPin')) fd.append('is_pin', document.getElementById('editPin').checked ? 1 : 0);
        fd.append('is_active', document.getElementById('editActive').checked ? 1 : 0);
        fetch(`${updateUrlPrefix}/${window[currentIdKey]}`, { method: 'POST', body: fd, headers: fetchHeaders() })
        .then(handleResponse).then(() => Turbo.visit(window.location.href));
    };
}

export function initGlobalHelpers() {
    window.closeModal = (id) => {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('active');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }
    };

    window.updateCount = (el, counterId, limit) => {
        if (!el) return;
        const counter = document.getElementById(counterId);
        if (counter) {
            const len = el.value.length;
            counter.innerText = `${len}/${limit}`;
            counter.classList.toggle('text-red-500', len >= limit);
            counter.classList.toggle('text-slate-300', len < limit);
        }
    };

    window.handlePreview = (input, containerId) => {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const container = document.getElementById(containerId);
                if (container) container.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            };
            reader.readAsDataURL(input.files[0]);
        }
    };

    window.showInlineError = (inputId, errorId, message) => {
        const input = document.getElementById(inputId);
        const error = document.getElementById(errorId);
        if (input) input.classList.add('border-red-500', 'bg-red-50');
        if (error) { error.innerText = message; error.classList.remove('hidden'); }
    };

    window.clearInlineError = (inputId, errorId) => {
        const input = document.getElementById(inputId);
        const error = document.getElementById(errorId);
        if (input) input.classList.remove('border-red-500', 'bg-red-50');
        if (error) error.classList.add('hidden');
    };

    window.initEditor = (selector, options = {}) => {
        if (typeof tinymce === 'undefined') return;

        const element = document.querySelector(selector);
        if (!element) return;

        const existing = tinymce.get(element.id);
        if (existing) {
            existing.remove();
        }

        tinymce.init({
            selector: selector,
            menubar: false,
            height: 300,
            plugins: ['lists', 'link', 'code'],
            toolbar: `undo redo bold semibold italic underline strikethrough subscript superscript alignleft aligncenter alignright alignjustify bullist numlist link code`,
            toolbar_mode: 'wrap',
            statusbar: false,
            promotion: false,
            branding: false,
            license_key: 'gpl',
            forced_root_block: 'div',

            formats: {
                semibold: { inline: 'span', styles: { fontWeight: '600' } }
            },

            content_style: `
            html, body {
                overflow-y: auto !important;
                scrollbar-width: thin;
                scrollbar-color: rgba(0,0,0,0.1) transparent;
            }
            html::-webkit-scrollbar, body::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }
            html::-webkit-scrollbar-thumb, body::-webkit-scrollbar-thumb {
                background-color: rgba(0,0,0,0.1);
                border-radius: 9999px;
            }
            html::-webkit-scrollbar-thumb:hover, body::-webkit-scrollbar-thumb:hover {
                background-color: rgba(0,0,0,0.1);
            }
            `,

            setup: function (editor) {

                editor.ui.registry.addIcon('semibold', `
                <svg width="24" height="24"
                    viewBox="0 0 7.69 7.69"
                    preserveAspectRatio="xMidYMid meet">
                <path fill="currentColor" d="M.64,6.23v-1.85h.56c.24,0,.42.04.54.11.12.07.18.19.18.36,0,.07-.01.13-.04.19-.02.06-.06.11-.1.14-.04.04-.1.06-.17.07h0c.07.03.13.05.18.08.05.03.09.08.12.14.03.06.04.13.04.23,0,.12-.03.21-.08.29s-.13.14-.23.18c-.1.04-.22.06-.36.06h-.66ZM1.51,5.08c.05-.04.08-.1.08-.18,0-.09-.03-.15-.09-.18s-.16-.05-.29-.05h-.25v.48h.28c.13,0,.22-.02.27-.06ZM1.63,5.67c0-.06-.01-.11-.04-.15s-.07-.07-.12-.09-.13-.03-.22-.03h-.29v.56h.3c.24,0,.37-.1.37-.29ZM2.42,6c-.14-.17-.21-.4-.21-.69,0-.19.03-.36.09-.5.06-.14.16-.25.29-.33.13-.08.29-.12.49-.12s.36.04.48.12c.13.08.22.19.28.33s.09.31.09.5c0,.29-.07.52-.21.69-.15.17-.36.26-.65.26s-.51-.09-.65-.26ZM3.59,5.31c0-.45-.17-.68-.52-.68-.12,0-.22.03-.3.08s-.14.13-.17.23c-.04.1-.06.22-.06.36s.02.26.05.36c.04.1.09.18.17.24s.18.08.3.08c.35,0,.52-.23.52-.68ZM4.27,6.23v-1.85h.32v1.57h.78v.27h-1.11ZM5.64,6.23v-1.85h.56c.19,0,.36.03.5.1.14.07.25.17.32.31.08.14.12.3.12.5,0,.21-.04.38-.12.52s-.19.24-.34.31c-.15.07-.33.11-.54.11h-.5ZM6.8,5.3c0-.15-.02-.27-.07-.36-.04-.1-.11-.17-.2-.21-.09-.05-.2-.07-.33-.07h-.24v1.3h.21c.42,0,.63-.22.63-.66Z"/>
                <path fill="currentColor" d="M.64,3.58v-.35c.24.11.45.16.62.16.08,0,.15-.01.21-.03s.1-.05.13-.1c.03-.04.04-.09.04-.15,0-.11-.07-.2-.21-.27-.07-.03-.15-.07-.24-.11-.16-.08-.28-.15-.35-.21-.11-.1-.17-.24-.17-.41,0-.13.03-.23.09-.32.06-.09.14-.15.25-.2.11-.05.23-.07.37-.07.2,0,.4.05.61.14l-.12.3c-.2-.08-.36-.12-.5-.12-.07,0-.12.01-.17.03-.05.02-.09.05-.11.09-.03.04-.04.09-.04.14,0,.06.02.11.05.15.03.04.08.08.15.11.07.03.14.07.23.11.11.05.21.1.29.15s.14.12.19.19c.04.08.07.17.07.28,0,.13-.03.23-.09.32-.06.09-.15.16-.27.21-.12.05-.25.07-.42.07-.21,0-.41-.04-.59-.12ZM2.59,3.6c-.12-.06-.21-.15-.28-.28-.07-.12-.1-.27-.1-.45s.03-.34.09-.46c.06-.13.15-.22.26-.29.11-.07.24-.1.4-.1s.27.03.38.09c.1.06.18.14.24.25.06.11.08.24.08.4v.19h-1.07c0,.15.05.26.12.34.08.08.19.12.32.12.19,0,.37-.04.54-.12v.29c-.16.07-.35.11-.56.11-.16,0-.3-.03-.43-.09ZM3.26,2.49c-.03-.06-.06-.11-.11-.14s-.11-.05-.19-.05c-.1,0-.19.03-.25.1-.07.07-.1.17-.12.3h.71c0-.08-.01-.15-.04-.21ZM5.98,3.67v-.98c0-.24-.09-.37-.27-.37-.24,0-.36.17-.36.5v.85h-.37v-.98c0-.24-.09-.37-.27-.37-.09,0-.16.02-.21.06-.05.04-.09.1-.12.18-.03.08-.04.18-.04.3v.8h-.37v-1.62h.28l.05.22h.02c.03-.06.07-.1.12-.14.05-.04.1-.06.16-.08.06-.02.13-.03.2-.03.24,0,.4.09.48.26h.03c.05-.09.11-.15.2-.19.09-.04.18-.06.29-.06.36,0,.55.2.55.59v1.06h-.37ZM6.72,1.62c0-.13.07-.19.21-.19s.21.06.21.19-.07.19-.21.19-.21-.06-.21-.19ZM6.74,3.67v-1.62h.37v1.62h-.37Z"/>
                </svg>
                `);

                editor.ui.registry.addToggleButton('semibold', {
                        icon: 'semibold',
                        tooltip: 'Semibold',
                        onAction: () => editor.formatter.toggle('semibold'),
                        onSetup: (api) => {
                            return editor.formatter.formatChanged('semibold', state => {
                                api.setActive(state);
                            });
                        }
                    });

                function applyLinkClass() {
                    const links = editor.dom.select('a');
                    links.forEach(link => {
                        editor.dom.addClass(link, 'text-orion-blue');
                        editor.dom.addClass(link, 'no-underline');
                    });
                }

                editor.on('init', function () {
                    applyLinkClass();
                });

                editor.on('change input NodeChange', function () {
                    applyLinkClass();
                    editor.save();
                });
            }
        });
    };
}

export function initLayoutUI() {
    const clockEl = document.getElementById('clock');
    if (window.clockInterval) clearInterval(window.clockInterval);
    if (clockEl) {
        const updateClock = () => {
            const el = document.getElementById('clock');
            if (el) el.innerText = new Date().toLocaleTimeString('en-US', { hour:'2-digit', minute:'2-digit', second:'2-digit', hour12:true });
        };
        updateClock();
        window.clockInterval = setInterval(updateClock, 1000);
    }

    const body = document.body;
    const adminName = body.dataset.adminName;
    const greetingEl = document.getElementById('greetingText');
    if (greetingEl && adminName) {
        const hr = new Date().getHours();
        let txt = hr < 12 ? 'Good Morning' : (hr < 17 ? 'Good Afternoon' : 'Good Evening');
        greetingEl.innerHTML = `<span class="text-slate-400 font-normal">${txt}, </span><span class="text-slate-600 font-bold">${adminName}</span>`;
    }

    const nav = document.querySelector('.sidebar-nav');
    if (nav) nav.onscroll = () => sessionStorage.setItem('sidebar-scroll', nav.scrollTop);
}

export function restoreSidebarScroll() {
    const nav = document.querySelector('.sidebar-nav');
    if (nav && sessionStorage.getItem('sidebar-scroll')) nav.scrollTop = sessionStorage.getItem('sidebar-scroll');
}

export function initTreeLogic() {
    document.querySelectorAll('.collapse-toggle').forEach(btn => {
        btn.onclick = (e) => {
            e.preventDefault();
            const container = document.getElementById(btn.dataset.target);
            if (!container) return;
            if (container.classList.contains('hidden')) {
                container.classList.remove('hidden');
                requestAnimationFrame(() => container.classList.add('expanded'));
                btn.querySelector('i').style.transform = 'rotate(90deg)';
            } else {
                container.classList.remove('expanded');
                btn.querySelector('i').style.transform = 'rotate(0deg)';
                setTimeout(() => { if (!container.classList.contains('expanded')) container.classList.add('hidden'); }, 300);
            }
        };
    });
}

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
            fetch('/admin/menus/update-order', { method: 'POST', headers: { ...fetchHeaders(), 'Content-Type': 'application/json' }, body: JSON.stringify({ menus }) }).then(handleResponse);
        }});
    });

    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.onclick = (e) => {
            e.preventDefault();
            const d = btn.dataset;
            editForm.action = `/admin/menus/${d.id}`;
            document.getElementById('editName').value = d.name;
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
            fetch(`/admin/menus/${id}`, {
                method: 'DELETE',
                headers: fetchHeaders()
            })
            .then(handleResponse)
            .then(() => Turbo.visit(window.location.href));
        }
    };

    if(editParent) editParent.onchange = () => checkParentStatus(editParent.options[editParent.selectedIndex].dataset.active);
    if(editActive) editActive.onchange = () => lbl.innerText = editActive.checked ? 'Active' : 'Inactive';
}

export function initPagesPage() {
    const modal = document.getElementById('pageModal');
    if (!modal || !window.location.pathname.includes('/admin/pages')) return;
    const editor = ace.edit("ace-editor");
    editor.session.setMode("ace/mode/html");
    editor.setTheme("ace/theme/github");
    const applyFormatting = (type) => {
        const selectedText = editor.getSelectedText();
        const toggleTag = (text, tagName) => {
            const start = `<${tagName}>`, end = `</${tagName}>`;
            return (text.startsWith(start) && text.endsWith(end)) ? text.substring(start.length, text.length - end.length) : `${start}${text}${end}`;
        };
        switch (type) {
            case 'b': editor.insert(toggleTag(selectedText, 'b')); break;
            case 'i': editor.insert(toggleTag(selectedText, 'i')); break;
            case 'p': editor.insert(toggleTag(selectedText, 'p')); break;
            case 'h1': editor.insert(toggleTag(selectedText, 'h1')); break;
            case 'h2': editor.insert(toggleTag(selectedText, 'h2')); break;
            case 'br': editor.insert(`<br>\n`); break;
            case 'ul':
            case 'ol':
                const items = selectedText
                    .split('\n')
                    .map(line => line.trim())
                    .filter(line => line.length > 0)
                    .map(line => {
                        line = line.replace(/^[-*•]\s+/, '');
                        line = line.replace(/^\d+\.\s+/, '');
                        return `  <li>${line}</li>`;
                    })
                    .join('\n');
                editor.insert(`<${type}>\n${items}\n</${type}>`);
                break;
            case 'a':
                const linkHtml = `<a href="https://google.com" target="_blank" rel="noopener noreferrer" class="text-orion-blue no-underline">Google</a>\n`;
                editor.insert(linkHtml);
                break;
        }
        editor.focus();
    };
    document.querySelectorAll('#editor-toolbar button').forEach(btn => btn.onclick = () => applyFormatting(btn.dataset.format));
    let curPageId = null;
    document.querySelectorAll('.edit-page').forEach(btn => {
        btn.onclick = (e) => {
            e.preventDefault();
            curPageId = btn.dataset.id;
            document.getElementById('modalTitle').innerText = `Edit: ${btn.dataset.name}`;
         
            fetch(`/admin/banners/get-for-editor/${curPageId}`, { headers: fetchHeaders() })
            .then(handleResponse)
            .then(images => {
                const strip = document.getElementById('imageStrip');
                strip.innerHTML = images.length ? '' : 'No Banners';
                images.forEach(img => {
                    const div = document.createElement('div');
                    div.className = "shrink-0 w-full h-auto rounded-xl overflow-hidden border-1 border-transparent hover:border-admin-blue cursor-pointer transition-all duration-300";
                    div.innerHTML = `<img src="${img.url}" class="w-full h-full object-cover">`;
                    div.onclick = () => { 
                        const widthAttr = img.width ? ` width="${img.width}"` : '';
                        const heightAttr = img.height ? ` height="${img.height}"` : '';
                         
                        const aspectStyle = (img.width && img.height) ? ` style="aspect-ratio: ${img.width} / ${img.height};"` : '';
                         
                        const htmlString = `<div class="banner rounded-xl shimmer">\n  <img src="${img.url}"${widthAttr}${heightAttr}${aspectStyle} alt="${btn.dataset.name}" class="w-full h-auto block rounded-xl object-cover" onload="this.parentElement.classList.remove('shimmer')">\n</div>\n`;
                         
                        editor.insert(htmlString); 
                        editor.focus(); 
                    };
                    strip.appendChild(div);
                });
            });
            const dec = document.createElement('textarea'); dec.innerHTML = btn.dataset.content || '';
            editor.setValue(dec.value, -1);
            modal.classList.remove('hidden');
            setTimeout(() => { modal.classList.add('active'); editor.resize(); }, 10);
        };
    });
    const save = document.getElementById('savePage');
    if (save) save.onclick = () => {
        fetch(`/admin/pages/${curPageId}`, { method: 'PUT', headers: { ...fetchHeaders(), 'Content-Type': 'application/json' }, body: JSON.stringify({ content: editor.getValue() }) })
        .then(handleResponse).then(() => Turbo.visit(window.location.href));
    };
}

export function initBannersPage() {
    if (!document.querySelector('.leaf-menu-item') || !window.location.pathname.includes('/admin/banners')) return;

    window.loadBanners = (menuId, el) => {
        document.querySelectorAll('.leaf-menu-item').forEach(i => i.classList.remove('active', 'border-admin-blue', 'bg-slate-100'));
        el.classList.add('active', 'border-admin-blue', 'bg-slate-100');
        window.currentMenuId = menuId;
        fetch(`/admin/banners/fetch/${menuId}`, { headers: fetchHeaders() }).then(handleResponse).then(data => { document.getElementById('imageArea').innerHTML = data.html; });
    };

    window.openBannerUploadModal = () => {
        if (!window.currentMenuId) { alert("Please select a page first"); return; }
        document.getElementById('uploadForm').reset();
        
        const previewContainer = document.getElementById('uploadPreviewContainer');
        previewContainer.style.aspectRatio = '48/9';
        previewContainer.innerHTML = `<i class="fas fa-cloud-arrow-up text-2xl text-slate-300 mb-2 group-hover:text-admin-blue transition-colors"></i><span id="uploadPlaceholderText" class="text-slate-400 text-[10px] uppercase tracking-widest text-center px-2">Click to select 48:9 image</span>`;
        
        document.getElementById('ratioSlider').value = 0;
        
        const mwInput = document.getElementById('maxWidthInput');
        if(mwInput) {
            mwInput.value = 2000;
            mwInput.classList.remove('border-red-500');
            document.getElementById('maxWidthError').classList.add('hidden');
        }

        const modal = document.getElementById('uploadModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };

    const ratioSlider = document.getElementById('ratioSlider');
    if (ratioSlider) {
        ratioSlider.addEventListener('input', function() {
            const container = document.getElementById('uploadPreviewContainer');
            const text = document.getElementById('uploadPlaceholderText');
            const ratios = ['48/9', '23/9', '16/9'];
            const ratioText = ['48:9', '23:9', '16:9'];
            
            container.style.aspectRatio = ratios[this.value];
            if (text) text.innerText = `Click to select ${ratioText[this.value]} image`;
        });
    }

    window.openBannerEditModal = (id, name, fullSlug, isActive) => {
        window.currentBannerId = id;
        document.getElementById('editPreviewContainer').innerHTML = `<img src="/${fullSlug}/${name}?t=${Date.now()}" class="w-full h-full object-contain">`;
        document.getElementById('editActiveToggle').checked = (isActive == 1);
        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };

    document.getElementById('uploadForm').onsubmit = function(e) {
        e.preventDefault();
        
        const mwInput = document.getElementById('maxWidthInput');
        const mwError = document.getElementById('maxWidthError');
        const mwValue = parseInt(mwInput.value, 10);

        if (isNaN(mwValue) || mwValue < 500 || mwValue > 2000) {
            mwInput.classList.add('border-red-500');
            mwError.classList.remove('hidden');
            return;
        } else {
            mwInput.classList.remove('border-red-500');
            mwError.classList.add('hidden');
        }

        fetch(`/admin/banners/upload/${window.currentMenuId}`, { method: 'POST', body: new FormData(this), headers: fetchHeaders() })
        .then(handleResponse).then(() => { closeModal('uploadModal'); loadBanners(window.currentMenuId, document.querySelector('.leaf-menu-item.active')); });
    };

    document.getElementById('editForm').onsubmit = function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        fd.append('is_active', document.getElementById('editActiveToggle').checked ? 1 : 0);
        fetch(`/admin/banners/${window.currentBannerId}`, { method: 'POST', body: fd, headers: fetchHeaders() })
        .then(handleResponse).then(() => { closeModal('editModal'); loadBanners(window.currentMenuId, document.querySelector('.leaf-menu-item.active')); });
    };

    window.deleteBannerImage = (id) => {
        if (confirm('Delete this Banner Image?')) fetch(`/admin/banners/${id}`, { method: 'DELETE', headers: fetchHeaders() }).then(handleResponse).then(() => loadBanners(window.currentMenuId, document.querySelector('.leaf-menu-item.active')));
    };
}

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
            updateCount(el, counterId, el?.getAttribute('maxlength'));
        });

        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };

    window.deleteSlider = (id) => {
        if (confirm('Delete this slider?')) {
            fetch(`/admin/sliders/${id}`, { 
                method: 'DELETE', 
                headers: fetchHeaders() 
            })
            .then(handleResponse)
            .then(() => Turbo.visit(window.location.href));
        }
    };

    addF.onsubmit = (e) => {
        e.preventDefault();
        fetch('/admin/sliders', { method: 'POST', body: new FormData(addF), headers: fetchHeaders() })
        .then(handleResponse).then(() => Turbo.visit(window.location.href));
    };

    editF.onsubmit = (e) => {
        e.preventDefault();
        const fd = new FormData(editF);
        fd.append('_method', 'PUT');
        fd.append('is_active', document.getElementById('editActive').checked ? 1 : 0);
        fetch(`/admin/sliders/${window.currentSliderId}`, { method: 'POST', body: fd, headers: fetchHeaders() })
        .then(handleResponse).then(() => Turbo.visit(window.location.href));
    };

    new Sortable(list, { animation: 150, handle: '.drag-handle', onEnd: () => {
        let orders = [];
        document.querySelectorAll('.sortable-item').forEach((el, index) => orders.push({ id: el.dataset.id, order: index + 1 }));
        fetch('/admin/sliders/update-order', { method: 'POST', headers: { ...fetchHeaders(), 'Content-Type': 'application/json' }, body: JSON.stringify({ orders }) }).then(handleResponse);
    }});
}

export function initProductsPage() {
    const gForm = document.getElementById('genForm');
    const pForm = document.getElementById('prodForm');
    if (!gForm || !pForm || !window.location.pathname.includes('/admin/products')) return;

    const tinyFields = [
        'indications', 'dosage_admin', 'use_children',
        'use_pregnancy_lactation', 'contraindications', 'precautions',
        'side_effects', 'drug_interactions', 'high_risk',
        'overdosage', 'storage', 'presentation',
        'how_supplied', 'commercial_pack', 'packaging',
        'official_specification'
    ];

    tinyFields.forEach(f => initEditor(`#p_${f}`));

    window.loadProducts = (id, el) => {
        document.querySelectorAll('.generic-list-item').forEach(item => {
            item.classList.remove('active', 'border-admin-blue', 'bg-blue-50/50', 'border-red-500', 'bg-red-50');
            if (item.classList.contains('archived-item')) item.classList.add('bg-red-50/50', 'border-red-100');
            else item.classList.add('bg-white', 'border-slate-200');
        });
        if (el.classList.contains('archived-item')) el.classList.add('active', 'border-red-500', 'bg-red-50');
        else el.classList.add('active', 'border-admin-blue', 'bg-blue-50/50');

        window.currentGenId = id;
        window.isArchivedMode = (id === 0);

        fetch(`/admin/products-actions/fetch/${id}`, {
            headers: fetchHeaders()
        })
        .then(handleResponse)
        .then(data => {
            document.getElementById('productArea').innerHTML = data.html;
        });
    };

    window.openGenericModal = () => {
        window.currentEditGenericId = null;
        gForm.reset();
        clearInlineError('genName', 'genNameError');
        document.getElementById('genTitle').innerText = "Add Generic";
        document.getElementById('genericModal').classList.remove('hidden');
        setTimeout(() => document.getElementById('genericModal').classList.add('active'), 10);
    };

    window.openEditGeneric = (g) => {
        window.currentEditGenericId = g.id;
        document.getElementById('genName').value = g.name;
        document.getElementById('genActive').checked = (g.is_active == 1);
        document.getElementById('genTitle').innerText = "Edit Generic";
        document.getElementById('genericModal').classList.remove('hidden');
        setTimeout(() => document.getElementById('genericModal').classList.add('active'), 10);
    };

    gForm.onsubmit = (e) => {
        e.preventDefault();
        const url = window.currentEditGenericId ? `/admin/products-actions/generic-update/${window.currentEditGenericId}` : `/admin/products-actions/generic-store`;
        const fd = new FormData(gForm);
        if (window.currentEditGenericId) fd.append('_method', 'PUT');
        fd.set('is_active', document.getElementById('genActive').checked ? 1 : 0);
        fetch(url, { method: 'POST', body: fd, headers: fetchHeaders() })
            .then(handleResponse).then(() => window.location.reload())
            .catch(() => showInlineError('genName', 'genNameError', "Generic name already exists."));
    };

    window.openAddProduct = () => {
        pForm.reset();
        window.currentEditProductId = null;

        tinyFields.forEach(f => {
            const elId = `p_${f}`;
            if (typeof tinymce !== 'undefined' && tinymce.get(elId)) {
                tinymce.get(elId).setContent('');
            }
        });

        document.getElementById('p_generic_id_wrapper').classList.add('hidden');
        document.getElementById('prodTitle').innerText = "Add Product";

        const btn = document.getElementById('prodSubmitBtn');
        btn.innerText = "Add Product";
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-success');

        document.getElementById('prodPreview').innerHTML =
            `<i class="fas fa-cloud-arrow-up text-2xl text-slate-300 mb-2"></i>`;

        document.getElementById('productModal').classList.remove('hidden');
        setTimeout(() => document.getElementById('productModal').classList.add('active'), 10);
    };

    window.openEditProduct = (p) => {
        window.currentEditProductId = p.id;
        clearInlineError('p_trade_name', 'prodNameError');
        document.getElementById('prodTitle').innerText = "Edit Product";

        const btn = document.getElementById('prodSubmitBtn');
        btn.innerText = "Update Product";
        btn.classList.remove('btn-success');
        btn.classList.add('btn-primary');

        const wrapper = document.getElementById('p_generic_id_wrapper');
        const select = document.getElementById('p_generic_id');

        if (window.isArchivedMode) {
            wrapper.classList.remove('hidden');
            select.disabled = false;
            select.value = p.generic_id || '';
        } else {
            wrapper.classList.add('hidden');
            select.disabled = true;
            select.value = p.generic_id || '';
        }

        const allFields = [
            'trade_name','preparation','therapeutic_class','indications',
            'dosage_admin','use_children','use_pregnancy_lactation',
            'contraindications','precautions','side_effects',
            'drug_interactions','high_risk','overdosage','storage',
            'presentation','how_supplied','commercial_pack',
            'packaging','official_specification'
        ];

        allFields.forEach(f => {
            const elId = 'p_' + f;
            const el = document.getElementById(elId);
            const content = p[f] || '';
            
            if (el) el.value = content;

            if (tinyFields.includes(f) && typeof tinymce !== 'undefined') {
                const editor = tinymce.get(elId);
                if (editor) {
                    editor.setContent(content);
                } else {
                    initEditor(`#${elId}`);
                    setTimeout(() => {
                        if (tinymce.get(elId)) {
                            tinymce.get(elId).setContent(content);
                        }
                    }, 100);
                }
            }
        });

        document.getElementById('p_active').checked = (p.is_active == 1);

        document.getElementById('prodPreview').innerHTML =
            `<img src="/storage/${p.image_path}?t=${Date.now()}" class="w-full h-full object-cover">`;

        document.getElementById('productModal').classList.remove('hidden');
        setTimeout(() => document.getElementById('productModal').classList.add('active'), 10);
    };

    pForm.onsubmit = (e) => {
        e.preventDefault();
        
        if (typeof tinymce !== 'undefined') {
            tinymce.triggerSave();
        }

        const fileInput = document.getElementById('prodInput');
        if (!window.currentEditProductId && fileInput.files.length === 0) { alert("Please select a product image."); return; }
        const url = window.currentEditProductId ? `/admin/products-actions/product-update/${window.currentEditProductId}` : `/admin/products-actions/product-store/${window.currentGenId}`;
        const fd = new FormData(pForm);
        if (window.currentEditProductId) fd.append('_method', 'PUT');
        fd.set('is_active', document.getElementById('p_active').checked ? 1 : 0);
        
        fetch(url, { method: 'POST', body: fd, headers: fetchHeaders() })
            .then(handleResponse).then(() => window.location.reload())
            .catch(() => showInlineError('p_trade_name', 'prodNameError', "Trade name already exists."));
    };

    window.deleteGeneric = (id) => { if (confirm('Delete this Generic?')) fetch(`/admin/products-actions/generic-delete/${id}`, { method: 'DELETE', headers: fetchHeaders() }).then(handleResponse).then(() => window.location.reload()); };
    window.deleteProduct = (id) => { if (confirm('Delete this Product?')) fetch(`/admin/products-actions/product-delete/${id}`, { method: 'DELETE', headers: fetchHeaders() }).then(handleResponse).then(() => window.location.reload()); };
}

export function initCSRPage() {
    if (!window.location.pathname.includes('/admin/csr')) return;

    initEditor('#addDesc');
    initEditor('#editDesc');

    setupModule('csr-list', '/admin/csr-actions/store', '/admin/csr-actions', 'curCsrId');
    
    window.openCsrAddModal = () => {
        const form = document.querySelector('#addModal form');
        if (form) form.reset();

        if (typeof tinymce !== 'undefined' && tinymce.get('addDesc')) {
            tinymce.get('addDesc').setContent('');
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

        if (typeof tinymce !== 'undefined') {
            const editor = tinymce.get('editDesc');
            if (editor) {
                editor.setContent(content);
            } else {
                initEditor('#editDesc');
                setTimeout(() => {
                    if (tinymce.get('editDesc')) {
                        tinymce.get('editDesc').setContent(content);
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
            fetch(`/admin/csr-actions/${id}`, { method: 'DELETE', headers: fetchHeaders() })
            .then(handleResponse)
            .then(() => Turbo.visit(window.location.href)); 
        }
    };
}

export function initScholarshipPage() {
    setupModule('scholarship', '/admin/scholarship-actions/store', '/admin/scholarship-actions', 'curScholarId');
    
    const list = document.getElementById('scholar-sortable-list');
    if (list) {
        new Sortable(list, {
            animation: 150,
            handle: '.drag-handle',
            ghostClass: 'bg-slate-50',
            onEnd: () => {
                let orders = [];
                list.querySelectorAll('.sortable-item').forEach((el, index) => {
                    orders.push({ id: el.dataset.id, order: index + 1 });
                });
                fetch('/admin/scholarship-actions/update-order', {
                    method: 'POST',
                    headers: { 
                        ...fetchHeaders(), 
                        'Content-Type': 'application/json' 
                    },
                    body: JSON.stringify({ orders })
                })
                .then(handleResponse)
                .catch(err => console.error("Sort order update failed:", err));
            }
        });
    }

    window.openScholarAddModal = () => {
        document.querySelector('#addModal form').reset();
        document.getElementById('addPreview').innerHTML = `<i class="fas fa-camera"></i>`;
        const modal = document.getElementById('addModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };
    
    window.openScholarEditModal = (item, slug) => {
        window.curScholarId = item.id;
        document.getElementById('editName').value = item.name;
        document.getElementById('editDegree').value = item.degree;
        document.getElementById('editSession').value = item.session ? item.session.replace('Session: ', '') : '';
        document.getElementById('editCollege').value = item.medical_college;
        document.getElementById('editActive').checked = item.is_active == 1;
        document.getElementById('editPreview').innerHTML = `<img src="/${slug}/${item.image_path.split('/').pop()}?t=${Date.now()}" class="w-full h-full object-cover">`;
        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };

    window.deleteScholarship = (id) => { if (confirm('Delete this recipient?')) { fetch(`/admin/scholarship-actions/${id}`, { method: 'DELETE', headers: fetchHeaders() }) .then(handleResponse) .then(() => Turbo.visit(window.location.href)); } };
}

export function initMedicalJournalsPage() {
    setupModule('medical-journals', '/admin/journal-actions/store', '/admin/journal-actions', 'curJId');
    document.querySelectorAll('.journal-sortable-list').forEach(list => {
        new Sortable(list, {
            animation: 150,
            handle: '.drag-handle',
            ghostClass: 'bg-slate-50',
            onEnd: () => {
                let orders = [];
                list.querySelectorAll('.sortable-item').forEach((el, index) => {
                    orders.push({ id: el.dataset.id, order: index + 1 });
                });
                fetch('/admin/journal-actions/update-order', {
                    method: 'POST',
                    headers: { 
                        ...fetchHeaders(), 
                        'Content-Type': 'application/json' 
                    },
                    body: JSON.stringify({ orders })
                })
                .then(handleResponse)
                .catch(err => console.error("Sort order update failed:", err));
            }
        });
    });
    window.handlePdfSelect = (input, isEdit = false) => {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const cleanName = file.name.replace(/\.[^/.]+$/, "").replace(/_/g, ' ');
            if (isEdit) {
                const statusEl = document.getElementById('editPdfStatus');
                if (statusEl) {
                    statusEl.innerText = file.name;
                    statusEl.classList.add('text-admin-blue');
                }
            } else {
                const statusEl = document.getElementById('pdfStatusText');
                const titleInput = document.getElementById('titleInput');
                if (statusEl) statusEl.innerText = file.name;
                if (titleInput && !titleInput.value) titleInput.value = cleanName;
            }
        }
    };
    window.openJournalAddModal = () => {
        document.querySelector('#addModal form').reset();
        document.getElementById('pdfStatusText').innerText = "Click to select PDF";
        const modal = document.getElementById('addModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };
    window.openJournalEditModal = (item) => {
        window.curJId = item.id;
        document.getElementById('editTitle').value = item.title;
        document.getElementById('editYear').value = item.year;
        document.getElementById('editActive').checked = item.is_active == 1;
        
        const editStatus = document.getElementById('editPdfStatus');
        if (editStatus) {
            editStatus.innerText = "Click to replace PDF";
            editStatus.classList.remove('text-admin-blue');
        }

        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };
    window.deleteJournal = (id) => { if(confirm('Delete this Medical Journal?')) fetch(`/admin/journal-actions/${id}`, { method: 'DELETE', headers: fetchHeaders() }).then(handleResponse).then(() => Turbo.visit(window.location.href)); };
}

export function initDirectorsPage() {
    if (!window.location.pathname.includes('/admin/board-of-directors')) return;

    initEditor('#addDesc');
    initEditor('#editDesc');

    setupModule('board-of-directors', '/admin/director-actions/store', '/admin/director-actions', 'curDirId');

    window.openDirectorAddModal = () => {
        const form = document.querySelector('#addModal form');
        if (form) form.reset();

        if (typeof tinymce !== 'undefined' && tinymce.get('addDesc')) {
            tinymce.get('addDesc').setContent('');
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
            `<img src="/${slug}/${item.image_path.split('/').pop()}?t=${Date.now()}" 
             class="w-full h-full object-cover">`;

        const content = item.description || '';
        const descTextarea = document.getElementById('editDesc');
        if (descTextarea) descTextarea.value = content;

        if (typeof tinymce !== 'undefined') {
            const editor = tinymce.get('editDesc');
            if (editor) {
                editor.setContent(content);
            } else {
                initEditor('#editDesc');
                setTimeout(() => {
                    if (tinymce.get('editDesc')) {
                        tinymce.get('editDesc').setContent(content);
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
            fetch(`/admin/director-actions/${id}`, {
                method: 'DELETE',
                headers: fetchHeaders()
            })
            .then(handleResponse)
            .then(() => Turbo.visit(window.location.href));
        }
    };
}

export function initReportModule() {
    const map = { 'annual-reports': '/admin/annual-reports-actions', 'quarterly-reports': '/admin/quarterly-reports-actions', 'half-yearly-reports': '/admin/half-yearly-reports-actions', 'price-sensitive-information': '/admin/price-sensitive-information-actions', 'corporate-governance': '/admin/corporate-governance-actions' };
    const seg = window.location.pathname.split('/')[2];
    const base = map[seg];
    if (!base) return;
    setupModule(seg, `${base}/store`, base, 'curRepId');
    window.openReportAddModal = () => {
        document.querySelector('#addModal form').reset();
        updateCount(document.getElementById('addDesc'), 'addCD', 500);
        const modal = document.getElementById('addModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };
    window.openReportEditModal = (item) => {
        window.curRepId = item.id;
        document.getElementById('editTitle').value = item.title;
        document.getElementById('editDate').value = item.publication_date ? item.publication_date.split('T')[0] : '';
        document.getElementById('editDesc').value = item.description || '';
        document.getElementById('editActive').checked = item.is_active == 1;
        updateCount(document.getElementById('editDesc'), 'editCD', 500);
        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
    };
    window.deleteReportItem = (id) => { if(confirm('Delete this Report?')) fetch(`${base}/${id}`, { method: 'DELETE', headers: fetchHeaders() }).then(handleResponse).then(() => Turbo.visit(window.location.href)); };
}

export function initNewsPage() {
    if (!window.location.pathname.includes('news-and-announcements')) return;

    initEditor('#addDesc');
    initEditor('#editDesc');

    setupModule('news-and-announcements', '/admin/news-actions/store', '/admin/news-actions', 'curNewsId');
    
    window.openNewsAddModal = () => {
        const form = document.querySelector('#addModal form');
        if (form) form.reset();

        if (typeof tinymce !== 'undefined' && tinymce.get('addDesc')) {
            tinymce.get('addDesc').setContent('');
        }

        const pin = form.querySelector('input[name="is_pin"][type="checkbox"]');
        const label = document.getElementById('addPinLabel');

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

        if (typeof tinymce !== 'undefined') {
            const editor = tinymce.get('editDesc');
            if (editor) {
                editor.setContent(content);
            } else {
                initEditor('#editDesc');
                setTimeout(() => {
                    if (tinymce.get('editDesc')) {
                        tinymce.get('editDesc').setContent(content);
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
            if(counterId) updateCount(titleInp, counterId, titleInp.getAttribute('maxlength'));
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

    window.deleteNews = (id) => { if(confirm('Delete this News?')) { fetch(`/admin/news-actions/${id}`, { method: 'DELETE', headers: fetchHeaders() }) .then(handleResponse) .then(() => Turbo.visit(window.location.href)); } };
}

export function initComplaintsPage() {
    if (!window.location.pathname.includes('product-complaint')) return;
    window.deleteComplaint = (id) => { if(confirm('Delete this Complaint?')) fetch(`/admin/product-complaint-actions/${id}`, { method: 'DELETE', headers: fetchHeaders() }).then(handleResponse).then(() => Turbo.visit(window.location.href, { action: "replace" })); };
}

export function initCareerPage() {
    if (!window.location.pathname.includes('/admin/career')) return;

    initEditor('#addDesc');
    initEditor('#editDesc');

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

        if (typeof tinymce !== 'undefined' && tinymce.get('addDesc')) {
            tinymce.get('addDesc').setContent('');
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

        if (typeof tinymce !== 'undefined') {
            const editor = tinymce.get('editDesc');
            if (editor) {
                editor.setContent(content);
            } else {
                initEditor('#editDesc');
                setTimeout(() => {
                    if(tinymce.get('editDesc')) tinymce.get('editDesc').setContent(content);
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
                fetch('/admin/career/update-order', {
                    method: 'POST',
                    headers: { ...fetchHeaders(), 'Content-Type': 'application/json' },
                    body: JSON.stringify({ items })
                });
            }
        });
    }

    window.deleteCareer = (id) => {
        if (confirm('Delete this job post?')) {
            fetch(`/admin/career/${id}`, {
                method: 'DELETE',
                headers: fetchHeaders()
            })
            .then(handleResponse)
            .then(() => Turbo.visit(window.location.href));
        }
    };
}

export function initFooterPage() {
    if (!window.location.pathname.includes('/admin/footer')) return;
    window.openFooterModal = (id) => {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('active'), 10);
        modal.querySelectorAll('input[maxlength], textarea[maxlength]').forEach(el => {
            const match = el.getAttribute('oninput')?.match(/'([^']+)'/);
            if (match) updateCount(el, match[1], el.getAttribute('maxlength'));
        });
    };
    window.fetchFooterMap = () => {
        const url = document.getElementById('map_input').value;
        if (url.includes('google.com/maps')) { document.getElementById('map_preview').src = url; document.getElementById('mapSaveBtn').disabled = false; }
    };
    document.querySelectorAll('.modal-overlay form').forEach(form => {
        form.onsubmit = (e) => {
            e.preventDefault();
            fetch(form.action, { method: 'POST', body: new FormData(form), headers: fetchHeaders() }).then(handleResponse).then(() => Turbo.visit(window.location.href));
        };
    });
}

export function initSettingsPage() {
    if (!window.location.pathname.includes('/admin/settings')) return;

    window.updateSvgPreview = (input, imgId) => {
        const file = input.files?.[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = e => document.getElementById(imgId).src = e.target.result;
        reader.readAsDataURL(file);
    };

    const currPass = document.getElementById('currentPassword');
    const newPass = document.getElementById('newPassword');
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    const strengthBox = document.getElementById('passwordStrength');
    const confPass = document.getElementById('confirmPassword');
    const passForm = document.getElementById('passwordForm');
    const overlay = document.getElementById('successOverlay');

    const triggerShake = el => {
        el.classList.remove('shake');
        void el.offsetWidth;
        el.classList.add('shake');
    };

    if (overlay) {
        setTimeout(() => {
            overlay.classList.add('opacity-0');
            setTimeout(() => overlay.remove(), 500);
        }, 2000);
    }

    if (currPass?.classList.contains('shake')) {
        triggerShake(currPass);
    }

    [currPass, newPass, confPass].forEach(input => {
        input?.addEventListener('input', () => {
            input.classList.remove('border-red-500', 'bg-red-50');
        });
    });

    const updatePasswordStrength = () => {
        const value = newPass.value;
        if (!value) {
            strengthBar.style.width = "0%";
            strengthBar.className = "h-full bg-slate-300/40 transition-all duration-300";
            strengthText.textContent = "?";
            strengthText.className = "text-[11px] font-bold uppercase tracking-wide text-slate-400";
            return;
        }

        strengthBox.classList.remove('hidden');

        if (value.length < 8) {
            newPass.classList.add('border-red-500', 'bg-red-50');
            newPass.classList.remove('border-slate-200');
        } else {
            newPass.classList.remove('border-red-500', 'bg-red-50');
            newPass.classList.add('border-slate-200');
        }

        let score = 0;
        if (value.length >= 8) score++;
        if (/[A-Z]/.test(value)) score++;
        if (/[0-9]/.test(value)) score++;
        if (/[^A-Za-z0-9]/.test(value)) score++;

        if (score <= 1) {
            strengthBar.style.width = "33%";
            strengthBar.className = "h-full bg-red-500 transition-all duration-300";
            strengthText.textContent = "Weak";
            strengthText.className = "text-[11px] font-bold uppercase tracking-wide text-red-500";
        } else if (score <= 3) {
            strengthBar.style.width = "66%";
            strengthBar.className = "h-full bg-amber-500 transition-all duration-300";
            strengthText.textContent = "Medium";
            strengthText.className = "text-[11px] font-bold uppercase tracking-wide text-amber-500";
        } else {
            strengthBar.style.width = "100%";
            strengthBar.className = "h-full bg-emerald-500 transition-all duration-300";
            strengthText.textContent = "Strong";
            strengthText.className = "text-[11px] font-bold uppercase tracking-wide text-emerald-500";
        }
    };

    const checkPasswordMatch = () => {
        if (!confPass.value) return;

        const mismatch = confPass.value !== newPass.value;
        confPass.classList.toggle('border-red-500', mismatch);
        confPass.classList.toggle('bg-red-50', mismatch);
        confPass.classList.toggle('border-slate-200', !mismatch);
    };

    newPass?.addEventListener('input', checkPasswordMatch);
    confPass?.addEventListener('input', checkPasswordMatch);
    confPass?.addEventListener('focus', checkPasswordMatch);

    newPass?.addEventListener('input', () => {
        updatePasswordStrength();
        checkPasswordMatch();
    });

    passForm?.addEventListener('submit', e => {
        let hasError = false;

        if (!currPass.value.trim()) {
            currPass.classList.add('border-red-500', 'bg-red-50');
            triggerShake(currPass);
            hasError = true;
        }

        if (!newPass.value || newPass.value.length < 8) {
            newPass.classList.add('border-red-500', 'bg-red-50');
            triggerShake(newPass);
            hasError = true;
        }

        if (confPass.value !== newPass.value) {
            confPass.classList.add('border-red-500', 'bg-red-50');
            triggerShake(confPass);
            hasError = true;
        }

        if (hasError) {
            e.preventDefault();
        }
    });
}

document.addEventListener('turbo:load', () => {
    initGlobalHelpers();
    initLayoutUI();
    restoreSidebarScroll();
    initTreeLogic();
    initMenuPage();
    initPagesPage();
    initBannersPage();
    initSlidersPage();
    initProductsPage();
    initCSRPage();
    initScholarshipPage();
    initMedicalJournalsPage();
    initDirectorsPage();
    initReportModule();
    initNewsPage();
    initComplaintsPage();
    initCareerPage();
    initFooterPage();
    initSettingsPage();
});