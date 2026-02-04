import axios from 'axios';
import Cookies from 'js-cookie';

const api = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_URL,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Add a request interceptor to inject the token
api.interceptors.request.use(
  (config) => {
    const token = Cookies.get('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Add a response interceptor to handle errors
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
       // Redirect to login if unauthorized (optional: only if not already on login)
       if (typeof window !== 'undefined' && !window.location.pathname.includes('/admin/login')) {
           // window.location.href = '/admin/login'; // logic handled by middleware/guard usually
       }
    }
    return Promise.reject(error);
  }
);

export default api;
