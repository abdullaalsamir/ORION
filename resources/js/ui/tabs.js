import { animateHeight } from '../core/dom'

window.switchTab = function (name) {

    animateHeight('#show-smooth-wrapper', () => {

        document
            .querySelectorAll('.tab-pane')
            .forEach(p => p.classList.add('hidden'))

        document
            .querySelectorAll('.tab-btn')
            .forEach(b => b.classList.remove('active'))

        document
            .getElementById('tab-content-' + name)
            ?.classList.remove('hidden')

        document
            .getElementById('tab-btn-' + name)
            ?.classList.add('active')
    })
}