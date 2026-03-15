import { animateHeight } from '../core/dom'

window.filterMode = 'generic'
window.selectedLetter = 'all'

function applyFilter() {

    const cards = document.querySelectorAll('.index-card')
    const mode = window.filterMode
    const letter = window.selectedLetter

    let visible = 0

    cards.forEach(card => {

        const compareVal =
            mode === 'generic'
                ? card.dataset.generic
                : card.dataset.trade

        if (letter === 'all' || compareVal.startsWith(letter)) {
            card.classList.remove('hidden')
            visible++
        } else {
            card.classList.add('hidden')
        }
    })

    document
        .getElementById('no-results')
        ?.classList.toggle('hidden', visible > 0)
}

window.setFilterMode = function (mode) {

    window.filterMode = mode

    document
        .querySelectorAll('.filter-mode-btn')
        .forEach(b => b.classList.remove('active'))

    document
        .getElementById('mode-' + mode)
        ?.classList.add('active')

    animateHeight('#main-smooth-wrapper', applyFilter)
}

window.setLetter = function (letter, event) {

    window.selectedLetter = letter

    document
        .querySelectorAll('.letter-btn')
        .forEach(b => b.classList.remove('active'))

    event?.target?.classList.add('active')

    animateHeight('#main-smooth-wrapper', applyFilter)
}