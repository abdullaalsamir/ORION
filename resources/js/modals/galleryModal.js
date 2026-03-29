import Swiper from 'swiper'
import { Navigation, Thumbs, FreeMode } from 'swiper/modules'
import { lockScroll, unlockScroll } from '../core/scroll'

window.galleryMainSwiper = null
window.galleryThumbsSwiper = null

window.openGalleryModal = function(title, mainImages, thumbImages) {

    const mainWrapper = document.getElementById('mainSwiperWrapper')
    const thumbsWrapper = document.getElementById('thumbsSwiperWrapper')

    mainWrapper.innerHTML = ''
    thumbsWrapper.innerHTML = ''

    if (mainImages.length > 0) {
        const titlePlaceholder = document.getElementById('galleryTitlePlaceholder')
        if (titlePlaceholder) titlePlaceholder.src = mainImages[0]
    }

    mainImages.forEach((mainUrl, index) => {
        const thumbUrl = thumbImages[index]

        const mainSlide = `
        <div class="swiper-slide flex justify-center items-center relative">
            <div class="relative w-full max-w-[calc(100vh*20/9)] shimmer">
                <img src="${mainUrl}" 
                    class="w-full h-auto object-contain select-none" 
                    draggable="false"
                    onload="this.parentElement.classList.remove('shimmer')">
            </div>
        </div>`

        mainWrapper.insertAdjacentHTML('beforeend', mainSlide)

        const thumbSlide = `
        <div class="swiper-slide cursor-pointer overflow-hidden rounded-lg relative shimmer bg-slate-800 aspect-20/9">
            <img src="${thumbUrl}" 
                class="w-full h-full object-cover"
                onload="this.parentElement.classList.remove('shimmer')">
        </div>`

        thumbsWrapper.insertAdjacentHTML('beforeend', thumbSlide)

    })

    setTimeout(() => {
        document.querySelectorAll('.gallery-slide-title')
            .forEach(el => el.textContent = title)
    }, 0)

    lockScroll()

    const modal = document.getElementById('galleryModal')
    modal.classList.remove('hidden')

    requestAnimationFrame(() => {

        modal.classList.remove('opacity-0')
        modal.classList.add('opacity-100')

        initGallerySwipers()

    })
}

function initGallerySwipers() {

    if (window.galleryThumbsSwiper) window.galleryThumbsSwiper.destroy(true, true)
    if (window.galleryMainSwiper) window.galleryMainSwiper.destroy(true, true)

    const thumbsCount = document.querySelectorAll('#thumbsSwiperWrapper .swiper-slide').length

    window.galleryThumbsSwiper = new Swiper('.thumbsGallerySwiper', {

        modules: [FreeMode, Navigation],

        spaceBetween: 12,
        slidesPerView: 'auto',
        freeMode: true,
        watchSlidesProgress: true,
        slideToClickedSlide: true,

        loop: true,

        navigation: thumbsCount > 6 ? {
            nextEl: '.thumbs-next',
            prevEl: '.thumbs-prev',
        } : false,
    })

    window.galleryMainSwiper = new Swiper('.mainGallerySwiper', {

        modules: [Navigation, Thumbs],

        spaceBetween: 10,

        loop: true,

        navigation: {
            nextEl: '.mainGallerySwiper .swiper-button-next',
            prevEl: '.mainGallerySwiper .swiper-button-prev',
        },

        thumbs: {
            swiper: window.galleryThumbsSwiper,
        },
    })
}

window.closeGalleryModal = function() {

    const modal = document.getElementById('galleryModal')

    modal.classList.remove('opacity-100')
    modal.classList.add('opacity-0')

    setTimeout(() => {

        modal.classList.add('hidden')

        unlockScroll()

        if (window.galleryThumbsSwiper) window.galleryThumbsSwiper.destroy(true, true)
        if (window.galleryMainSwiper) window.galleryMainSwiper.destroy(true, true)

        document.getElementById('mainSwiperWrapper').innerHTML = ''
        document.getElementById('thumbsSwiperWrapper').innerHTML = ''

    }, 300)
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('galleryModal')

        if (modal && !modal.classList.contains('hidden')) {
            closeGalleryModal()
        }
    }
})