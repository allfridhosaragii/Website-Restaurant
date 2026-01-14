import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

// ============================================
// IMPORTANT: Update this URL before building!
// ============================================
const BASE_URL = 'https://website-restaurant.up.railway.app/api';

const api = axios.create({
    baseURL: BASE_URL,
    timeout: 15000,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
});

// Request interceptor - add auth token
api.interceptors.request.use(
    async (config) => {
        const token = await AsyncStorage.getItem('auth_token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => Promise.reject(error)
);

// Response interceptor - handle errors
api.interceptors.response.use(
    (response) => response,
    async (error) => {
        if (error.response?.status === 401) {
            // Token expired - logout
            await AsyncStorage.removeItem('auth_token');
            await AsyncStorage.removeItem('user');
        }
        return Promise.reject(error);
    }
);

// Auth API
export const authAPI = {
    login: (email, password) => api.post('/auth/login', { email, password }),
    register: (data) => api.post('/auth/register', data),
    logout: () => api.post('/auth/logout'),
    getUser: () => api.get('/auth/user'),
    updateProfile: (data) => api.post('/auth/user?_method=PUT', data, {
        headers: { 'Content-Type': 'multipart/form-data' }
    }),
    googleAuth: (data) => api.post('/auth/google', data),
};

// Menu API
export const menuAPI = {
    getAll: (params) => api.get('/menus', { params }),
    getBySlug: (slug) => api.get(`/menus/${slug}`),
};

// Cart API
export const cartAPI = {
    get: () => api.get('/cart'),
    add: (menuId, quantity) => api.post('/cart', { menu_id: menuId, quantity }),
    update: (id, quantity) => api.put(`/cart/${id}`, { quantity }),
    remove: (id) => api.delete(`/cart/${id}`),
    clear: () => api.delete('/cart'),
};

// Order API
export const orderAPI = {
    getAll: () => api.get('/orders'),
    create: (data) => api.post('/orders', data),
    getById: (id) => api.get(`/orders/${id}`),
};

// Reservation API
export const reservationAPI = {
    getAll: () => api.get('/reservations'),
    create: (data) => api.post('/reservations', data),
    getById: (id) => api.get(`/reservations/${id}`),
};

// Favorites API
export const favoritesAPI = {
    getAll: () => api.get('/favorites'),
    toggle: (menuId) => api.post(`/favorites/${menuId}`),
};

export const dashboardAPI = {
    getStats: () => api.get('/dashboard'),
};

// Admin API
export const adminAPI = {
    // Menus
    getMenus: (params) => api.get('/admin/menus', { params }),
    createMenu: (data) => api.post('/admin/menus', data, {
        headers: { 'Content-Type': 'multipart/form-data' }
    }),
    updateMenu: (slug, data) => api.post(`/admin/menus/${slug}?_method=PUT`, data, {
        headers: { 'Content-Type': 'multipart/form-data' }
    }),
    deleteMenu: (slug) => api.delete(`/admin/menus/${slug}`),

    // Orders
    getOrders: (params) => api.get('/admin/orders', { params }),
    getOrderDetails: (id) => api.get(`/admin/orders/${id}`),
    updateOrderStatus: (id, status) => api.put(`/admin/orders/${id}/status`, { status }),

    // Reservations
    getReservations: (params) => api.get('/admin/reservations', { params }),
    updateReservationStatus: (id, status) => api.put(`/admin/reservations/${id}/status`, { status }),

    // Users
    getUsers: (params) => api.get('/admin/users', { params }),
    updateUserStatus: (id, status) => api.put(`/admin/users/${id}/status`, { status }),

    // Stats
    getStats: () => api.get('/admin/dashboard'),
};

export default api;
