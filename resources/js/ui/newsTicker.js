document.addEventListener('DOMContentLoaded', () => {

    const newsSection = document.getElementById('home-news-section')

    if (!newsSection) return

    setTimeout(() => {

        const newsRightCol = newsSection.querySelector('.lg\\:col-span-8')
        const newsTrack = newsRightCol
            ? newsRightCol.querySelector('.space-y-3')
            : null

        if (!newsTrack) return

        const items = Array.from(newsTrack.children)
        const totalItems = items.length
        const VISIBLE_COUNT = 4

        if (totalItems <= VISIBLE_COUNT) return

        newsRightCol.style.position = 'relative'
        newsRightCol.style.overflow = 'hidden'

        newsTrack.style.position = 'absolute'
        newsTrack.style.top = '0'
        newsTrack.style.left = '0'
        newsTrack.style.width = '100%'
        newsTrack.style.transition = 'transform 0.6s cubic-bezier(0.4,0,0.2,1)'
        newsTrack.style.margin = '0'

        items.forEach(item => {
            item.style.marginRight = '15px'
        })

        let itemHeight = items[0].offsetHeight
        const gap = 12

        let viewportHeight = (itemHeight * VISIBLE_COUNT) + (gap * (VISIBLE_COUNT - 1))

        newsRightCol.style.height = `${viewportHeight}px`

        const MAX_INDEX = totalItems - VISIBLE_COUNT

        const scrollbar = document.createElement('div')
        Object.assign(scrollbar.style, {
            position: 'absolute',
            top: '0',
            right: '0',
            width: '6px',
            height: '100%',
            backgroundColor: 'rgba(0,0,0,0.05)',
            borderRadius: '3px',
            zIndex: '10'
        })

        const thumb = document.createElement('div')
        Object.assign(thumb.style, {
            position: 'absolute',
            top: '0',
            width: '100%',
            backgroundColor: '#08519e',
            borderRadius: '3px',
            transition: 'top 0.6s ease'
        })

        scrollbar.appendChild(thumb)
        newsRightCol.appendChild(scrollbar)

        let currentIndex = 0
        let autoTimer = null

        const updateThumb = () => {
            const thumbHeight = (VISIBLE_COUNT / totalItems) * viewportHeight
            const scrollRatio = currentIndex / MAX_INDEX

            thumb.style.height = `${thumbHeight}px`
            thumb.style.top = `${scrollRatio * (viewportHeight - thumbHeight)}px`
        }

        const goToIndex = (index) => {
            if (index > MAX_INDEX) currentIndex = 0
            else currentIndex = index

            newsTrack.style.transform = `translateY(-${(itemHeight + gap) * currentIndex}px)`
            updateThumb()
        }

        const startAuto = () => {
            stopAuto()
            autoTimer = setInterval(() => {
                goToIndex(currentIndex + 1)
            }, 5000)
        }

        const stopAuto = () => {
            if (autoTimer) clearInterval(autoTimer)
        }

        updateThumb()
        startAuto()

        newsRightCol.addEventListener('mouseenter', stopAuto)
        newsRightCol.addEventListener('mouseleave', startAuto)

        window.addEventListener('resize', () => {
            itemHeight = items[0].offsetHeight
            viewportHeight = (itemHeight * VISIBLE_COUNT) + (gap * (VISIBLE_COUNT - 1))
            newsRightCol.style.height = `${viewportHeight}px`
            goToIndex(currentIndex)
        })

    }, 200)

})