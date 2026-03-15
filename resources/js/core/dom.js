export function lockScroll() {

    const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth

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

export function animateHeight(selector, updateLogic) {

    const container = document.querySelector(selector)

    if (!container) {
        updateLogic()
        return
    }

    const startHeight = container.offsetHeight
    container.style.height = startHeight + 'px'

    void container.offsetHeight

    updateLogic()

    container.style.height = 'auto'
    const endHeight = container.offsetHeight

    container.style.height = startHeight + 'px'

    void container.offsetHeight

    container.style.height = endHeight + 'px'

    const onEnd = (e) => {

        if (e.propertyName === 'height') {

            container.style.height = 'auto'
            container.removeEventListener('transitionend', onEnd)

        }
    }

    container.addEventListener('transitionend', onEnd)
}

window.lockScroll = lockScroll
window.unlockScroll = unlockScroll
window.animateHeight = animateHeight