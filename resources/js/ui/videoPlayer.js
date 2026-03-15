import Plyr from 'plyr'

document.addEventListener('DOMContentLoaded', () => {

    const players = []

    const plyrVideos = document.querySelectorAll('.plyr-video')

    plyrVideos.forEach(video => {

        const container = video.closest('.relative')
        const shimmer = container?.querySelector('.video-shimmer')
        const poster = video.getAttribute('poster')

        const posterImg = new Image()
        posterImg.src = poster

        posterImg.onload = () => {

            shimmer?.classList.add('hidden')
            video.classList.remove('opacity-0')

            const player = new Plyr(video, {
                controls: [
                    'play-large',
                    'play',
                    'progress',
                    'current-time',
                    'mute',
                    'volume',
                    'fullscreen'
                ]
            })

            players.push(player)
        }
    })

    document.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            players.forEach(player => player.destroy())
        })
    })
})