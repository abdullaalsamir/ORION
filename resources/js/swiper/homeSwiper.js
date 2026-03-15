import Swiper from 'swiper'
import { Navigation, Pagination, Autoplay, EffectFade } from 'swiper/modules'
import 'swiper/css'
import 'swiper/css/navigation'
import 'swiper/css/pagination'
import 'swiper/css/effect-fade'

document.addEventListener('DOMContentLoaded', () => {

    if (!document.querySelector('.homeSwiper')) return

    new Swiper('.homeSwiper', {

        modules: [Navigation, Pagination, Autoplay, EffectFade],

        simulateTouch: false,
        loop: true,
        effect: 'fade',
        speed: 1000,

        fadeEffect: { crossFade: true },

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
        }
    })
})