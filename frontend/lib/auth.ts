import apiClient from "./api";
import { AxiosError } from "axios";

export interface LoginCredentials {
    email: string;
    password: string;
}

export interface RegisterData {
    name: string;
    email: string;
    password: string;
}

export interface User {
    id: number;
    name: string;
    email: string;
}

export interface AuthResponse {
    user: User;
    token: string;
    message: string;
}

export interface LogoutResponse {
    message: string;
}

export interface UserResponse {
    success: boolean;
    data: User | null;
    message: string;
    error: string | null;
}

// エラーレスポンス型を追加
export interface ApiError {
    message: string;
    errors?: {
        [key: string]: string[];
    };
}

// localStorageの安全なアクセス関数
const getStorageItem = (key: string): string | null => {
    if (typeof window === "undefined") return null;
    return localStorage.getItem(key);
};

const setStorageItem = (key: string, value: string): void => {
    if (typeof window === "undefined") return;
    localStorage.setItem(key, value);
};

const removeStorageItem = (key: string): void => {
    if (typeof window === "undefined") return;
    localStorage.removeItem(key);
};

// ログイン
export async function login(credentials: LoginCredentials): Promise<AuthResponse> {
    try {
        const response = await apiClient.post<AuthResponse>("/login", credentials);
        
        if (response.data.token) {
            setStorageItem("auth_token", response.data.token);
        }
        return response.data;
    } catch (error) {
        // エラーレスポンスを適切に処理
        const axiosError = error as AxiosError<ApiError>;
        if (axiosError.response?.data) {
            throw axiosError.response.data;
        }
        throw {
            message: "ログインに失敗しました",
            errors: { network: ["ネットワークエラーが発生しました"] }
        } as ApiError;
    }
}

// 登録
export async function register(data: RegisterData): Promise<AuthResponse> {
    try {
        const response = await apiClient.post<AuthResponse>("/register", data);

        if (response.data.token) {
            setStorageItem("auth_token", response.data.token);
        }
        return response.data;
    } catch (error) {
        // エラーレスポンスを適切に処理
        const axiosError = error as AxiosError<ApiError>;
        if (axiosError.response?.data) {
            throw axiosError.response.data;
        }
        throw {
            message: "登録に失敗しました",
            errors: { network: ["ネットワークエラーが発生しました"] }
        } as ApiError;
    }
}

// ログアウト
export async function logout(): Promise<LogoutResponse> {
    try {
        const response = await apiClient.post<LogoutResponse>("/logout");
        // トークンを削除
        removeStorageItem("auth_token");
        return response.data;
    } catch (error) {
        // エラーが発生してもトークンは削除
        removeStorageItem("auth_token");
        throw error;
    }
}

// ユーザーのデータ
export async function getCurrentUser(): Promise<User | null> {
    try {
        const response = await apiClient.get<UserResponse>("/user");

        if (response.data.success && response.data.data) {
            return response.data.data;
        }
        return null;
    } catch (error) {
        // エラーハンドリング（api.tsのインターセプターで401処理済み）
        const axiosError = error as AxiosError<ApiError>;
        // 401エラーの場合、トークンを削除（念のため）
        if (axiosError.response?.status === 401) {
            removeStorageItem("auth_token");
        }
        return null;
    }
}