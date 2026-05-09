import { create } from 'zustand';

const useAuthStore = create((set) => ({
  user: JSON.parse(localStorage.getItem('rinonet_user') || 'null'),
  token: localStorage.getItem('rinonet_token') || null,
  isAuthenticated: !!localStorage.getItem('rinonet_token'),

  login: (token, user) => {
    localStorage.setItem('rinonet_token', token);
    localStorage.setItem('rinonet_user', JSON.stringify(user));
    set({ user, token, isAuthenticated: true });
  },

  logout: () => {
    localStorage.removeItem('rinonet_token');
    localStorage.removeItem('rinonet_user');
    set({ user: null, token: null, isAuthenticated: false });
  },

  updateUser: (user) => {
    localStorage.setItem('rinonet_user', JSON.stringify(user));
    set({ user });
  },
}));

export default useAuthStore;
