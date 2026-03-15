export function lockScroll() {

    const scrollbarWidth =
        window.innerWidth - document.documentElement.clientWidth

    document.body.style.paddingRight = `${scrollbarWidth}px`

    const header = document.querySelector('header.fixed')
    if (header) header.style.paddingRight = `${scrollbarWidth}px`

    document.documentElement.style.overflow = 'hidden'
    document.body.style.overflow = 'hidden'
}

export function unlockScroll() {

    document.documentElement.style.overflow = ''
    document.body.style.overflow = ''
    document.body.style.paddingRight = ''

    const header = document.querySelector('header.fixed')
    if (header) header.style.paddingRight = ''
}

window.lockScroll = lockScroll
window.unlockScroll = unlockScroll
