import axios from "axios";

// Laravel APIのベースURL（環境変数から取得）
const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000/api";

// Axiosインスタンス作成
export const apiClient = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
  withCredentials: true, // Cookie認証用（Laravel Sanctum）
});

// リクエストインターセプター（トークン追加など）
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
  async (error) => {
    if (error.response?.status === 401) {
      // 未認証エラー時の処理
      // ログインページへリダイレクトなど
    }
    return Promise.reject(error);
  }
);

export default apiClient;