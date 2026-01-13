import axios from "axios";

// backenからのAPIのURL
const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL || "http://localhost:8080/api";

// Axiosインスタンス
export const apiClient = axios.create({
    baseURL: API_BASE_URL,
    headers: {
        "Content-Type": "application/json",
        Accept: "application/json"
    },
    withCredentials: true, // Cookie承認用 (Laravel Sanctum)
});

// リクエストインターセプター（トークン追加）
apiClient.interceptors.request.use(
    (config) => {
        // 必要に応じてトークンを追加
        const token = localStorage.getItem("auth_token");
        if(token){
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// レスポンスインターセプター（エラーハンドリング）
apiClient.interceptors.response.use(
    (response) => response,
    async (error) =>{
        if(error.response?.status === 401){
            localStorage.removeItem("auth_token");
            if(typeof window !== "undefined"){
                window.location.href = "/auth/login"
            }
        }
        return Promise.reject(error);
    }
);

export default apiClient;