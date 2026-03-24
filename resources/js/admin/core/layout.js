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
}

export function updateSidebarActiveState(targetUrl = null) {
    const pathName = targetUrl 
        ? new URL(targetUrl, window.location.origin).pathname 
        : window.location.pathname;

    document.querySelectorAll('.sidebar-nav a').forEach(link => {
        const linkUrl = new URL(link.href, window.location.origin).pathname;

        let isActive = false;
        
        if (linkUrl === '/admin' || linkUrl === '/admin/') {
            isActive = pathName === '/admin' || pathName === '/admin/';
        } else {
            isActive = pathName.startsWith(linkUrl);
        }

        if (isActive) {
            link.classList.add('bg-white/10', 'text-accent', 'border-accent');
            link.classList.remove('text-slate-300', 'hover:bg-white/5', 'hover:text-white', 'border-transparent');
        } else {
            link.classList.remove('bg-white/10', 'text-accent', 'border-accent');
            link.classList.add('text-slate-300', 'hover:bg-white/5', 'hover:text-white', 'border-transparent');
        }
    });
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