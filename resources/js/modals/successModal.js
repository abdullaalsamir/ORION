window.showSuccessModal = function () {

    const modal = document.getElementById('successModal')
    const content = document.getElementById('successModalContent')

    if (!modal) return

    document.body.style.overflow = 'hidden'

    modal.classList.remove('hidden', 'opacity-0', 'pointer-events-none')

    requestAnimationFrame(() => {

        modal.classList.add('opacity-100')

        if (content) {
            content.classList.remove('translate-y-8', 'opacity-0')
            content.classList.add('translate-y-0', 'opacity-100')
        }

    })
}

window.closeSuccessModal = function () {

    const modal = document.getElementById('successModal')
    const content = document.getElementById('successModalContent')

    if (!modal) return

    if (content) {
        content.classList.add('translate-y-8', 'opacity-0')
        content.classList.remove('translate-y-0', 'opacity-100')
    }

    modal.classList.remove('opacity-100')
    modal.classList.add('opacity-0')

    setTimeout(() => {

        modal.classList.add('hidden', 'pointer-events-none')
        document.body.style.overflow = ''

    }, 300)
}