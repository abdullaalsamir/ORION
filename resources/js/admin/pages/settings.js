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

    if (currPass?.classList.contains('shake')) { triggerShake(currPass); }

    [currPass, newPass, confPass].forEach(input => {
        input?.addEventListener('input', () => { input.classList.remove('border-red-500', 'bg-red-50'); });
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
            strengthBar.style.width = "33%"; strengthBar.className = "h-full bg-red-500 transition-all duration-300";
            strengthText.textContent = "Weak"; strengthText.className = "text-[11px] font-bold uppercase tracking-wide text-red-500";
        } else if (score <= 3) {
            strengthBar.style.width = "66%"; strengthBar.className = "h-full bg-amber-500 transition-all duration-300";
            strengthText.textContent = "Medium"; strengthText.className = "text-[11px] font-bold uppercase tracking-wide text-amber-500";
        } else {
            strengthBar.style.width = "100%"; strengthBar.className = "h-full bg-emerald-500 transition-all duration-300";
            strengthText.textContent = "Strong"; strengthText.className = "text-[11px] font-bold uppercase tracking-wide text-emerald-500";
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

    newPass?.addEventListener('input', () => { updatePasswordStrength(); checkPasswordMatch(); });

    passForm?.addEventListener('submit', e => {
        let hasError = false;

        if (!currPass.value.trim()) { currPass.classList.add('border-red-500', 'bg-red-50'); triggerShake(currPass); hasError = true; }
        if (!newPass.value || newPass.value.length < 8) { newPass.classList.add('border-red-500', 'bg-red-50'); triggerShake(newPass); hasError = true; }
        if (confPass.value !== newPass.value) { confPass.classList.add('border-red-500', 'bg-red-50'); triggerShake(confPass); hasError = true; }

        if (hasError) e.preventDefault();
    });
}