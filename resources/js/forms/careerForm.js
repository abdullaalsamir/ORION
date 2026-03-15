window.submitApplication = async function (e, url) {

    e.preventDefault()

    const form = e.target
    const btn = document.getElementById('submitBtn')
    const fileInput = document.getElementById('cvInput')

    const file = fileInput?.files[0]

    if (!file) {
        alert('Please select a PDF file')
        return
    }

    btn.disabled = true
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Uploading...'

    const formData = new FormData(form)

    if (!formData.has('cv')) {
        formData.append('cv', file)
    }

    try {

        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })

        if (!res.ok) throw new Error('Upload failed')

        window.closeApplyModal()
        window.scrollTo({ top: 0, behavior: 'smooth' })
        window.showSuccessModal()
        form.reset()

    } catch (err) {

        console.error(err)
        alert('Upload failed.')

    } finally {

        btn.disabled = false
        btn.innerText = 'Upload'
    }
}