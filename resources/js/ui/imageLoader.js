document.addEventListener('DOMContentLoaded', () => {

    const images = document.querySelectorAll('.product-image')

    images.forEach(img => {

        const reveal = () => {
            img.parentElement.classList.remove('shimmer')
            img.classList.add('is-loaded')
        }

        if (img.complete) reveal()
        else img.addEventListener('load', reveal)

        img.addEventListener('error', () => {
            img.parentElement.classList.remove('shimmer')
            img.parentElement.classList.add('bg-slate-100')
        })
    })
})