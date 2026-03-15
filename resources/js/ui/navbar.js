document.addEventListener('DOMContentLoaded', () => {

    const topLevelItems = document.querySelectorAll('nav > ul > li.group')

    topLevelItems.forEach(item => {

        item.addEventListener('mouseenter', function () {

            const dropdownWrapper = this.querySelector(':scope > div.absolute')
            if (!dropdownWrapper) return

            const dropdownMenu = dropdownWrapper.querySelector('.level-2-menu')
            if (!dropdownMenu) return

            dropdownWrapper.classList.remove('right-0')
            dropdownWrapper.classList.add('left-0')

            dropdownWrapper.style.visibility = 'hidden'
            dropdownWrapper.style.pointerEvents = 'none'

            dropdownWrapper.classList.remove('left-0', 'right-0')
            dropdownWrapper.classList.add('left-0')

            const parentRect = this.getBoundingClientRect()
            const dropdownWidth = dropdownMenu.offsetWidth

            const spaceOnRight = window.innerWidth - parentRect.left

            if (dropdownWidth > spaceOnRight) {

                dropdownWrapper.classList.remove('left-0')
                dropdownWrapper.classList.add('right-0')

            }

            dropdownWrapper.style.visibility = ''
            dropdownWrapper.style.pointerEvents = ''
        })
    })


    const subMenuItems = document.querySelectorAll('.group\\/sub')

    subMenuItems.forEach(item => {

        item.addEventListener('mouseenter', function () {

            const dropdown = this.querySelector('.level-3-menu')
            if (!dropdown) return

            const chevron = this.querySelector('.sub-chevron')

            dropdown.classList.remove('is-active', 'is-ready', 'is-flipped')

            const parentRect = this.getBoundingClientRect()

            const dropdownWidth = 256

            const spaceOnRight =
                window.innerWidth - (parentRect.right + dropdownWidth)

            if (spaceOnRight < 0) {

                dropdown.classList.add('is-flipped')

                if (chevron) {
                    chevron.classList.remove('fa-chevron-right')
                    chevron.classList.add('fa-chevron-left')
                }

            } else {

                if (chevron) {
                    chevron.classList.remove('fa-chevron-left')
                    chevron.classList.add('fa-chevron-right')
                }

            }

            void dropdown.offsetWidth

            dropdown.classList.add('is-ready', 'is-active')
        })


        item.addEventListener('mouseleave', function () {

            const dropdown = this.querySelector('.level-3-menu')

            if (dropdown) {

                dropdown.classList.remove('is-active')

                setTimeout(() => {

                    if (!dropdown.classList.contains('is-active')) {

                        dropdown.classList.remove('is-ready', 'is-flipped')
                        dropdown.scrollTop = 0

                    }

                }, 200)
            }
        })
    })

})