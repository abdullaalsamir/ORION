import * as Turbo from "@hotwired/turbo";
import ace from 'ace-builds/src-noconflict/ace';

export function initPagesPage() {
    const modal = document.getElementById('pageModal');
    if (!modal || !window.location.pathname.includes('/admin/pages')) return;
    
    const editor = ace.edit("ace-editor");
    editor.session.setMode("ace/mode/html");
    editor.setTheme("ace/theme/github");
    editor.session.setUseWrapMode(true);
    editor.setOption("wrap", true);
    editor.setShowPrintMargin(false);
    
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
         
            window.axios.get(`/admin/banners/get-for-editor/${curPageId}`)
            .then(images => {
                const strip = document.getElementById('imageStrip');
                strip.innerHTML = images.length 
                ? '' 
                : `
                    <div class="flex items-center justify-center w-full h-full">
                        <span class="text-[11px] font-bold text-rose-300 uppercase">No Banners Found</span>
                    </div>
                `;
                
                images.forEach(img => {
                    const div = document.createElement('div');
                    div.className = "shrink-0 w-full h-auto rounded-xl overflow-hidden border-1 border-transparent hover:border-admin-blue cursor-pointer transition-all duration-300 relative shimmer bg-slate-100";
                    
                    const imgSrc = img.thumb_url || img.url;
                    div.innerHTML = `<img src="${imgSrc}" class="w-full h-full object-cover opacity-0 transition-opacity duration-300" onload="this.classList.remove('opacity-0'); this.parentElement.classList.remove('shimmer')">`;
                    
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
            
            const dec = document.createElement('textarea'); 
            dec.innerHTML = btn.dataset.content || '';
            editor.setValue(dec.value, -1);
            
            modal.classList.remove('hidden');
            setTimeout(() => { modal.classList.add('active'); editor.resize(); }, 10);
        };
    });
    
    const save = document.getElementById('savePage');
    if (save) save.onclick = () => {
        window.axios.put(`/admin/pages/${curPageId}`, { content: editor.getValue() })
            .then(() => Turbo.visit(window.location.href));
    };
}