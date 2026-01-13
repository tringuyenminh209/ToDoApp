import apiClient from "./api";

export interface LoginCredentials {
    email: string;
    password: string;
}

export interface RegisterData {
    name: string,
    email: string;
    password: string;
    passwor_confirmation: string;
}

export interface User {
    id: number;
    name: string;
    email: string;
}

//ログイン
export async function login(credentials: LoginCredentials){
    const response = await apiClient.post("/login", credentials);
    return response.data;
}

//　登録
export async function register(data: RegisterData){
    const response = await apiClient.post("/register", data)
    return response.data;
}

//ログアウト
export async function logout() {
    const response = await apiClient.post("/logout");
    return response.data;
}

// ユーザーのデータ
export async function getCurrentUer(): Promise<User | null>{
    try{
        const response =await apiClient.get("/user");
        return response.data;
    }catch(error){
        return null;
    }
}