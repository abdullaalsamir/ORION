import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';

const csrfToken = document.querySelector('meta[name="csrf-token"]');
if (csrfToken) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.content;
}

window.axios.interceptors.response.use(
    (response) => {
        return response.data;
    },
    (error) => {
        const data = error.response?.data;
        const errorMsg = data?.error || data?.message || "Server validation failed.";
        alert("Error: " + errorMsg);
        console.error("Axios Error:", error);
        
        return Promise.reject(error);
    }
);