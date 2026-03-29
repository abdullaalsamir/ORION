function initMapLoaders() {
    const mapWrappers = document.querySelectorAll('.map-wrapper');

    mapWrappers.forEach(wrapper => {
        const iframe = wrapper.querySelector('.map-frame');
        const loader = wrapper.querySelector('.map-loader');

        if (!iframe || !loader) return;

        setTimeout(() => {
            iframe.src = iframe.dataset.src;

            iframe.onload = () => {
                loader.style.opacity = '0';
                iframe.style.opacity = '1';

                setTimeout(() => loader.remove(), 500);
            };
        }, 1000);
    });
}

document.addEventListener("DOMContentLoaded", initMapLoaders);

document.addEventListener("turbo:load", initMapLoaders);