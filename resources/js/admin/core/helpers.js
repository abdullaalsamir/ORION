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
        if (typeof window.tinymce === 'undefined') return;

        const element = document.querySelector(selector);
        if (!element) return;

        const existing = window.tinymce.get(element.id);
        if (existing) {
            existing.remove();
        }

        window.tinymce.init({
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
                <svg width="24" height="24" viewBox="0 0 7.69 7.69" preserveAspectRatio="xMidYMid meet">
                <path fill="currentColor" d="M.64,6.23v-1.85h.56c.24,0,.42.04.54.11.12.07.18.19.18.36,0,.07-.01.13-.04.19-.02.06-.06.11-.1.14-.04.04-.1.06-.17.07h0c.07.03.13.05.18.08.05.03.09.08.12.14.03.06.04.13.04.23,0,.12-.03.21-.08.29s-.13.14-.23.18c-.1.04-.22.06-.36.06h-.66ZM1.51,5.08c.05-.04.08-.1.08-.18,0-.09-.03-.15-.09-.18s-.16-.05-.29-.05h-.25v.48h.28c.13,0,.22-.02.27-.06ZM1.63,5.67c0-.06-.01-.11-.04-.15s-.07-.07-.12-.09-.13-.03-.22-.03h-.29v.56h.3c.24,0,.37-.1.37-.29ZM2.42,6c-.14-.17-.21-.4-.21-.69,0-.19.03-.36.09-.5.06-.14.16-.25.29-.33.13-.08.29-.12.49-.12s.36.04.48.12c.13.08.22.19.28.33s.09.31.09.5c0,.29-.07.52-.21.69-.15.17-.36.26-.65.26s-.51-.09-.65-.26ZM3.59,5.31c0-.45-.17-.68-.52-.68-.12,0-.22.03-.3.08s-.14.13-.17.23c-.04.1-.06.22-.06.36s.02.26.05.36c.04.1.09.18.17.24s.18.08.3.08c.35,0,.52-.23.52-.68ZM4.27,6.23v-1.85h.32v1.57h.78v.27h-1.11ZM5.64,6.23v-1.85h.56c.19,0,.36.03.5.1.14.07.25.17.32.31.08.14.12.3.12.5,0,.21-.04.38-.12.52s-.19.24-.34.31c-.15.07-.33.11-.54.11h-.5ZM6.8,5.3c0-.15-.02-.27-.07-.36-.04-.1-.11-.17-.2-.21-.09-.05-.2-.07-.33-.07h-.24v1.3h.21c.42,0,.63-.22.63-.66Z"/>
                <path fill="currentColor" d="M.64,3.58v-.35c.24.11.45.16.62.16.08,0,.15-.01.21-.03s.1-.05.13-.1c.03-.04.04-.09.04-.15,0-.11-.07-.2-.21-.27-.07-.03-.15-.07-.24-.11-.16-.08-.28-.15-.35-.21-.11-.1-.17-.24-.17-.41,0-.13.03-.23.09-.32.06-.09.14-.15.25-.2.11-.05.23-.07.37-.07.2,0,.4.05.61.14l-.12.3c-.2-.08-.36-.12-.5-.12-.07,0-.12.01-.17.03-.05.02-.09.05-.11.09-.03.04-.04.09-.04.14,0,.06.02.11.05.15.03.04.08.08.15.11.07.03.14.07.23.11.11.05.21.1.29.15s.14.12.19.19c.04.08.07.17.07.28,0,.13-.03.23-.09.32-.06.09-.15.16-.27.21-.12.05-.25.07-.42.07-.21,0-.41-.04-.59-.12ZM2.59,3.6c-.12-.06-.21-.15-.28-.28-.07-.12-.1-.27-.1-.45s.03-.34.09-.46c.06-.13.15-.22.26-.29.11-.07.24-.1.4-.1s.27.03.38.09c.1.06.18.14.24.25.06.11.08.24.08.4v.19h-1.07c0,.15.05.26.12.34.08.08.19.12.32.12.19,0,.37-.04.54-.12v.29c-.16.07-.35.11-.56.11-.16,0-.3-.03-.43-.09ZM3.26,2.49c-.03-.06-.06-.11-.11-.14s-.11-.05-.19-.05c-.1,0-.19.03-.25.1-.07.07-.1.17-.12.3h.71c0-.08-.01-.15-.04-.21ZM5.98,3.67v-.98c0-.24-.09-.37-.27-.37-.24,0-.36.17-.36.5v.85h-.37v-.98c0-.24-.09-.37-.27-.37-.09,0-.16.02-.21.06-.05.04-.09.1-.12.18-.03.08-.04.18-.04.3v.8h-.37v-1.62h.28l.05.22h.02c.03-.06.07-.1.12-.14.05-.04.1-.06.16-.08.06-.02.13-.03.2-.03.24,0,.4.09.48.26h.03c.05-.09.11-.15.2-.19.09-.04.18-.06.29-.06.36,0,.55.2.55.59v1.06h-.37ZM6.72,1.62c0-.13.07-.19.21-.19s.21.06.21.19-.07.19-.21.19-.21-.06-.21-.19ZM6.74,3.67v-1.62h.37v1.62h-.37Z"/>
                </svg>
                `);

                editor.ui.registry.addToggleButton('semibold', {
                    icon: 'semibold',
                    tooltip: 'Semibold',
                    onAction: () => editor.formatter.toggle('semibold'),
                    onSetup: (api) => editor.formatter.formatChanged('semibold', state => api.setActive(state))
                });

                function applyLinkClass() {
                    const links = editor.dom.select('a');
                    links.forEach(link => {
                        editor.dom.addClass(link, 'text-orion-blue');
                        editor.dom.addClass(link, 'no-underline');
                    });
                }

                editor.on('init', () => applyLinkClass());
                editor.on('change input NodeChange', () => { applyLinkClass(); editor.save(); });
            }
        });
    };
}