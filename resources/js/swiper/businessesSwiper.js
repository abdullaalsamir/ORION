import Swiper from 'swiper'
import { Navigation, Pagination, Autoplay } from 'swiper/modules'

document.addEventListener('DOMContentLoaded', () => {

    if (!document.querySelector('.businessesSwiper')) return

    new Swiper('.businessesSwiper', {

        modules: [Navigation, Pagination, Autoplay],

        loop: true,
        speed: 1000,

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