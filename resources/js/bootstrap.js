import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Configure axios defaults
window.axios.defaults.timeout = 10000;
window.axios.defaults.headers.common['Accept'] = 'application/json';

// Add request interceptor for loading states
window.axios.interceptors.request.use(
    config => {
        // You can add global loading logic here
        return config;
    },
    error => {
        return Promise.reject(error);
    }
);

// Add response interceptor for error handling
window.axios.interceptors.response.use(
    response => {
        return response;
    },
    error => {
        if (error.response?.status === 401) {
            // Redirect to login if unauthorized
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);
