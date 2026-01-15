// store/auth-store.ts
import { User, getCurrentUser, login, logout, register, LoginCredentials, RegisterData } from '@/lib/auth';
import { create } from "zustand";
import { persist } from 'zustand/middleware';

interface AuthState {
    user: User | null;
    isLoading: boolean;
    isAuthenticated: boolean;
    hasHydrated: boolean;
    login: (credentials: LoginCredentials) => Promise<void>;
    register: (data: RegisterData) => Promise<void>;
    logout: () => Promise<void>;
    checkAuth: () => Promise<void>;
    setHasHydrated: () => void;
}

export const useAuthStore = create<AuthState>()(
    persist(
        (set) => ({
            user: null,
            isLoading: false,
            isAuthenticated: false,
            hasHydrated: false,

            login: async (credentials) => {
                set({ isLoading: true });
                try {
                    const response = await login(credentials);
                    set({
                        user: response.user,
                        isAuthenticated: true,
                        isLoading: false
                    });
                } catch (error) {
                    set({ isLoading: false });
                    throw error;
                }
            },

            register: async (data) => {
                set({ isLoading: true });
                try {
                    const response = await register(data);
                    set({
                        user: response.user,
                        isAuthenticated: true,
                        isLoading: false
                    });
                } catch (error) {
                    set({ isLoading: false });
                    throw error;
                }
            },

            logout: async () => {
                await logout();
                set({ user: null, isAuthenticated: false });
            },

            checkAuth: async () => {
                set({ isLoading: true });
                try {
                    const user = await getCurrentUser();
                    set({
                        user,
                        isAuthenticated: !!user,
                        isLoading: false
                    });
                } catch (error) {
                    set({ user: null, isAuthenticated: false, isLoading: false });
                }
            },
            setHasHydrated: () => {
                set({ hasHydrated: true });
            },
        }),
        {
            name: 'auth-storage',
            partialize: (state) => ({ user: state.user, isAuthenticated: state.isAuthenticated }),
            onRehydrateStorage: () => (state) => {
                state?.setHasHydrated();
            },
        }
    )
);