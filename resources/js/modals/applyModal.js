window.openApplyModal = function(){

    const modal = document.getElementById('applyModal')
    const content = document.getElementById('applyModalContent')

    if (modal)
        modal.classList.remove('hidden','opacity-0','pointer-events-none')

    requestAnimationFrame(()=>{

        if(content)
            content.classList.remove('translate-y-8','opacity-0')

    })
}

window.closeApplyModal = function(){

    const modal = document.getElementById('applyModal')
    const content = document.getElementById('applyModalContent')

    if(content)
        content.classList.add('translate-y-8','opacity-0')

    if(modal)
        modal.classList.add('opacity-0')

    setTimeout(()=>{

        if(modal)
            modal.classList.add('hidden','pointer-events-none')

    },300)
}