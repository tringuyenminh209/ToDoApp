import axios from "axios";
import { error } from "node:console";
import { config } from "node:process";

// backenからのAPIのURL
const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL

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
        if(error.response?.status == 401){
            // 未認証エラー時の処理
            // ログインページへリダイレクトなど
        }
        return Promise.reject(error);
    }
);

export default apiClient;