import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay, EffectFade } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/effect-fade';

import Alpine from 'alpinejs'
window.Alpine = Alpine
Alpine.start()

window.animateHeight = function (selector, updateLogic) {
    const container = document.querySelector(selector);
    if (!container) {
        updateLogic();
        return;
    }

    const startHeight = container.offsetHeight;
    container.style.height = startHeight + 'px';
    
    void container.offsetHeight; 

    updateLogic();

    container.style.height = 'auto';
    const endHeight = container.offsetHeight;

    container.style.height = startHeight + 'px';
    
    void container.offsetHeight;

    container.style.height = endHeight + 'px';

    const onEnd = (e) => {
        if (e.propertyName === 'height') {
            container.style.height = 'auto';
            container.removeEventListener('transitionend', onEnd);
        }
    };
    container.addEventListener('transitionend', onEnd);
}

window.setFilterMode = function(mode) {
    window.filterMode = mode;
    document.querySelectorAll('.filter-mode-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('mode-' + mode).classList.add('active');
    animateHeight('#main-smooth-wrapper', () => applyFilter());
};

window.setLetter = function(letter, event) {
    window.selectedLetter = letter;
    document.querySelectorAll('.letter-btn').forEach(b => b.classList.remove('active'));
    if(event && event.target) event.target.classList.add('active');
    animateHeight('#main-smooth-wrapper', () => applyFilter());
};

function applyFilter() {
    const cards = document.querySelectorAll('.index-card');
    let visibleCount = 0;
    const mode = window.filterMode || 'generic';
    const letter = window.selectedLetter || 'all';

    cards.forEach(card => {
        const compareVal = mode === 'generic' ? card.dataset.generic : card.dataset.trade;
        if (letter === 'all' || compareVal.startsWith(letter)) {
            card.classList.remove('hidden');
            visibleCount++;
        } else {
            card.classList.add('hidden');
        }
    });
    document.getElementById('no-results').classList.toggle('hidden', visibleCount > 0);
}

window.switchTab = function(name) {
    animateHeight('#show-smooth-wrapper', () => {
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('tab-content-' + name).classList.remove('hidden');
        document.getElementById('tab-btn-' + name).classList.add('active');
    });
};

document.addEventListener('DOMContentLoaded', () => {
    const productImages = document.querySelectorAll('.product-image');
    
    productImages.forEach(img => {
        const revealImage = () => {
            img.parentElement.classList.remove('shimmer');
            img.classList.add('is-loaded');
        };

        if (img.complete) {
            revealImage();
        } else {
            img.addEventListener('load', revealImage);
            
            img.addEventListener('error', () => {
                img.parentElement.classList.remove('shimmer');
                img.parentElement.classList.add('bg-slate-100');
            });
        }
    });

    const topLevelItems = document.querySelectorAll('nav > ul > li.group');

    topLevelItems.forEach(item => {
        item.addEventListener('mouseenter', function () {
            const dropdownWrapper = this.querySelector(':scope > div.absolute');
            if (!dropdownWrapper) return;

            const dropdownMenu = dropdownWrapper.querySelector('.level-2-menu');
            if (!dropdownMenu) return;

            dropdownWrapper.classList.remove('right-0');
            dropdownWrapper.classList.add('left-0');

            dropdownWrapper.style.visibility = 'hidden';
            dropdownWrapper.style.pointerEvents = 'none';
            dropdownWrapper.classList.remove('left-0', 'right-0');
            dropdownWrapper.classList.add('left-0');

            const parentRect = this.getBoundingClientRect();
            const dropdownWidth = dropdownMenu.offsetWidth;

            const spaceOnRight = window.innerWidth - parentRect.left;

            if (dropdownWidth > spaceOnRight) {
                dropdownWrapper.classList.remove('left-0');
                dropdownWrapper.classList.add('right-0');
            }

            dropdownWrapper.style.visibility = '';
            dropdownWrapper.style.pointerEvents = '';
        });
    });

    const subMenuItems = document.querySelectorAll('.group\\/sub');

    subMenuItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            const dropdown = this.querySelector('.level-3-menu');
            if (!dropdown) return;

            const chevron = this.querySelector('.sub-chevron');
            
            dropdown.classList.remove('is-active', 'is-ready', 'is-flipped');

            const parentRect = this.getBoundingClientRect();
            const dropdownWidth = 224;
            const spaceOnRight = window.innerWidth - (parentRect.right + dropdownWidth);

            if (spaceOnRight < 0) {
                dropdown.classList.add('is-flipped');
                if (chevron) {
                    chevron.classList.remove('fa-chevron-right');
                    chevron.classList.add('fa-chevron-left');
                }
            } else {
                if (chevron) {
                    chevron.classList.remove('fa-chevron-left');
                    chevron.classList.add('fa-chevron-right');
                }
            }

            void dropdown.offsetWidth; 

            dropdown.classList.add('is-ready', 'is-active');
        });

        item.addEventListener('mouseleave', function() {
            const dropdown = this.querySelector('.level-3-menu');
            if (dropdown) {
                dropdown.classList.remove('is-active');
                
                setTimeout(() => {
                    if (!dropdown.classList.contains('is-active')) {
                        dropdown.classList.remove('is-ready', 'is-flipped');
                    }
                }, 200); 
            }
        });
    });

    if (document.querySelector('.homeSwiper')) {
        new Swiper('.homeSwiper', {
            modules: [Navigation, Pagination, Autoplay, EffectFade],
            
            simulateTouch: false,
            loop: true,
            effect: 'fade',
            speed: 1000,
            fadeEffect: {
                crossFade: true
            },
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
    }
});

window.showSuccessModal = function () {
    const modal = document.getElementById('successModal');
    if (!modal) return;

    const box = modal.querySelector('div');

    modal.classList.remove('pointer-events-none', 'opacity-0');
    modal.classList.add('opacity-100');

    box.classList.remove('translate-y-8', 'opacity-0');
    box.classList.add('translate-y-0', 'opacity-100');
};

window.closeSuccessModal = function () {
    const modal = document.getElementById('successModal');
    if (!modal) return;

    const box = modal.querySelector('div');

    box.classList.remove('translate-y-0', 'opacity-100');
    box.classList.add('translate-y-8', 'opacity-0');

    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0');

    setTimeout(() => {
        modal.classList.add('pointer-events-none');
    }, 300);
};

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('successModal')) {
        const form = document.querySelector('form');
        if (form) form.reset();

        setTimeout(showSuccessModal, 150);
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const allSections = document.querySelectorAll('section.mt-16');
    let newsSection = null;

    allSections.forEach(sec => {
        const h2 = sec.querySelector('h2');
        if (h2 && h2.textContent.trim() === 'News & Announcements') {
            newsSection = sec;
        }
    });

    if (!newsSection) return;

    setTimeout(() => {
        const newsRightCol = newsSection.querySelector('.lg\\:col-span-8');
        const newsTrack = newsRightCol ? newsRightCol.querySelector('.space-y-3') : null;

        if (!newsTrack) return;

        const items = Array.from(newsTrack.children);
        const totalItems = items.length;
        const VISIBLE_COUNT = 4;

        if (totalItems <= VISIBLE_COUNT) return;

        newsRightCol.style.position = 'relative';
        newsRightCol.style.overflow = 'hidden';

        newsTrack.style.position = 'absolute';
        newsTrack.style.top = '0';
        newsTrack.style.left = '0';
        newsTrack.style.width = '100%';
        newsTrack.style.transition = 'transform 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
        newsTrack.style.margin = '0';

        items.forEach(item => {
            item.style.marginRight = '15px';
        });

        let itemHeight = items[0].offsetHeight;
        const gap = 12;
        let viewportHeight = (itemHeight * VISIBLE_COUNT) + (gap * (VISIBLE_COUNT - 1));

        newsRightCol.style.height = `${viewportHeight}px`;

        const MAX_INDEX = totalItems - VISIBLE_COUNT;

        const scrollbar = document.createElement('div');
        Object.assign(scrollbar.style, {
            position: 'absolute',
            top: '0',
            right: '0',
            width: '6px',
            height: '100%',
            backgroundColor: 'rgba(0, 0, 0, 0.05)',
            borderRadius: '3px',
            zIndex: '10'
        });

        const thumb = document.createElement('div');
        Object.assign(thumb.style, {
            position: 'absolute',
            top: '0',
            width: '100%',
            backgroundColor: '#0054a6',
            borderRadius: '3px',
            transition: 'top 0.6s ease'
        });

        scrollbar.appendChild(thumb);
        newsRightCol.appendChild(scrollbar);

        let currentIndex = 0;
        let autoTimer = null;

        const updateThumb = () => {
            const thumbHeight = (VISIBLE_COUNT / totalItems) * viewportHeight;
            const scrollRatio = currentIndex / MAX_INDEX;

            thumb.style.height = `${thumbHeight}px`;
            thumb.style.top = `${scrollRatio * (viewportHeight - thumbHeight)}px`;
        };

        const goToIndex = (index) => {
            if (index > MAX_INDEX) {
                currentIndex = 0;
            } else {
                currentIndex = index;
            }

            newsTrack.style.transform = `translateY(-${(itemHeight + gap) * currentIndex}px)`;
            updateThumb();
        };

        const startAuto = () => {
            stopAuto();
            autoTimer = setInterval(() => {
                goToIndex(currentIndex + 1);
            }, 5000);
        };

        const stopAuto = () => {
            if (autoTimer) clearInterval(autoTimer);
        };

        updateThumb();
        startAuto();

        newsRightCol.addEventListener('mouseenter', stopAuto);
        newsRightCol.addEventListener('mouseleave', startAuto);

        window.addEventListener('resize', () => {
            itemHeight = items[0].offsetHeight;
            viewportHeight = (itemHeight * VISIBLE_COUNT) + (gap * (VISIBLE_COUNT - 1));
            newsRightCol.style.height = `${viewportHeight}px`;
            goToIndex(currentIndex);
        });

    }, 200);
});

window.openApplyModal = function() {
    const modal = document.getElementById('applyModal');
    const content = document.getElementById('applyModalContent');

    document.body.style.overflow = 'hidden';

    if (modal) modal.classList.remove('hidden', 'opacity-0', 'pointer-events-none');

    requestAnimationFrame(() => {
        if (content) content.classList.remove('translate-y-8', 'opacity-0');
    });
};

window.closeApplyModal = function() {
    const modal = document.getElementById('applyModal');
    const content = document.getElementById('applyModalContent');

    if (content) content.classList.add('translate-y-8', 'opacity-0');
    if (modal) modal.classList.add('opacity-0');

    setTimeout(() => {
        if (modal) modal.classList.add('hidden', 'pointer-events-none');
        document.body.style.overflow = '';
    }, 300);
};

window.closeCareerSuccessModal = function() {
    const modal = document.getElementById('successModal');
    const content = document.getElementById('successModalContent');

    if (content) content.classList.add('translate-y-8', 'opacity-0');
    if (modal) modal.classList.add('opacity-0');

    setTimeout(() => {
        if (modal) modal.classList.add('hidden', 'pointer-events-none');
        document.body.style.overflow = '';
    }, 300);
};

window.submitApplication = async function(e, url) {
    e.preventDefault();

    const form = e.target;
    const btn = document.getElementById('submitBtn');
    const fileInput = document.getElementById('cvInput');
    const file = fileInput ? fileInput.files[0] : null;

    if (!file) return alert('Please select a PDF file');

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Uploading...';

    const formData = new FormData(form);
    
    if (!formData.has('cv')) {
        formData.append('cv', file);
    }

    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });

        if (res.ok) {
            window.closeApplyModal();

            requestAnimationFrame(() => {
                window.scrollTo({ top: 0, behavior: 'smooth' });

                const modal = document.getElementById('successModal');
                const content = document.getElementById('successModalContent');

                document.body.style.overflow = 'hidden';

                if (modal) modal.classList.remove('hidden', 'opacity-0', 'pointer-events-none');
                if (content) content.classList.remove('translate-y-8', 'opacity-0');
                
                form.reset();
                const fileNameEl = document.getElementById('fileName');
                if(fileNameEl) fileNameEl.innerText = 'Click to select PDF file';
            });
        } else {
            const contentType = res.headers.get("content-type");
            if (contentType && contentType.includes("application/json")) {
                const data = await res.json();
                let errorMessage = data.message || 'Upload failed.';
                if (data.errors && data.errors.cv) {
                    errorMessage = data.errors.cv[0];
                }
                alert(errorMessage);
            } else {
                console.error('Server returned non-JSON:', await res.text());
                alert('System error: The server returned an invalid response.');
            }
        }
    } catch (err) {
        console.error("Fetch Error:", err);
        alert('A network error occurred. Please check your connection.');
    } finally {
        btn.disabled = false;
        btn.innerText = 'Upload';
    }
};