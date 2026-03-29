import Swiper from 'swiper'
import { Thumbs, Pagination } from 'swiper/modules'

document.addEventListener('DOMContentLoaded', () => {

    const mainEl = document.querySelector('.homeConcernsMainSwiper')
    const thumbsEl = document.querySelector('.homeConcernsThumbsSwiper')

    if (!mainEl || !thumbsEl) return

    const VISIBLE_THUMBS = 5

    function updateSwiperGap(swiper) {
        const mainHeight = mainEl.offsetHeight
        const thumbWidth = thumbsEl.offsetWidth
        const thumbHeight = thumbWidth * (9 / 20)

        let gap = Math.floor((mainHeight - (thumbHeight * VISIBLE_THUMBS)) / (VISIBLE_THUMBS - 1))
        gap = Math.max(0, gap)

        swiper.params.spaceBetween = gap
        swiper.update()
    }

    const concernsThumbsSwiper = new Swiper('.homeConcernsThumbsSwiper', {
        direction: 'vertical',
        slidesPerView: 'auto',
        watchSlidesProgress: true,
        on: {
            init: function () {
                setTimeout(() => updateSwiperGap(this), 50)
            },
            resize: function () {
                updateSwiperGap(this)
            }
        }
    })

    const childSwipers = new Swiper('.homeConcernsChildSwiper', {
        modules: [Pagination],
        direction: 'horizontal',
        nested: true,
        spaceBetween: 0,
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
    })

    const mainSwiper = new Swiper('.homeConcernsMainSwiper', {
        modules: [Thumbs, Pagination],
        direction: 'vertical',
        spaceBetween: 10,
        pagination: {
            el: '.main-swiper-pagination',
            clickable: true,
        },
        thumbs: {
            swiper: concernsThumbsSwiper,
        }
    })

    let autoTimer

    function getChildSwiper(index) {
        return Array.isArray(childSwipers) ? childSwipers[index] : childSwipers
    }

    function slideNextSequence() {
        const pIndex = mainSwiper.activeIndex
        const cSwiper = getChildSwiper(pIndex)

        if (cSwiper && !cSwiper.isEnd) {
            cSwiper.slideNext()
        } else {
            if (mainSwiper.isEnd) {
                mainSwiper.slideTo(0)
            } else {
                mainSwiper.slideNext()
            }
        }
    }

    function startAuto() {
        clearInterval(autoTimer)
        autoTimer = setInterval(slideNextSequence, 5000)
    }

    function stopAuto() {
        clearInterval(autoTimer)
    }

    mainSwiper.on('slideChange', function () {
        const activeIndex = mainSwiper.activeIndex
        
        const childInstances = Array.isArray(childSwipers) ? childSwipers : [childSwipers]
        if(childInstances[activeIndex]) {
            childInstances[activeIndex].slideTo(0, 0)
        }
        
        if (activeIndex >= VISIBLE_THUMBS - 1) {
            concernsThumbsSwiper.slideTo(activeIndex - VISIBLE_THUMBS + 1)
        } else {
            concernsThumbsSwiper.slideTo(0)
        }

        startAuto()
    })

    startAuto()

    document.addEventListener("visibilitychange", () => {
        if (document.hidden) {
            stopAuto()
        } else {
            startAuto()
        }
    })

})