import apiClient from "./api";

export interface LoginCredentials {
    email: string;
    password: string;
}

export interface RegisterData {
    name: string;
    email: string;
    password: string;
    // passwor_confirmation: string;
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
export interface LogoutResponse{
    message: string;
}


export interface UserResponse{
    success: boolean;
    data: User | null;
    message: string;
    error: string | null;
}

//ログイン
export async function login(credentials: LoginCredentials): Promise<AuthResponse>{
    const response = await apiClient.post<AuthResponse>("/login", credentials);
    
    if(response.data.token){
        localStorage.setItem("auth_token", response.data.token);
    }
    return response.data;
}

//　登録
export async function register(data: RegisterData) : Promise<AuthResponse>{
    const response = await apiClient.post<AuthResponse>("/register", data)

    if(response.data.token){
        localStorage.setItem("auth_token", response.data.token);
    }
    return response.data;
}

//ログアウト
export async function logout(): Promise<LogoutResponse>{
    const response = await apiClient.post<LogoutResponse>("/logout");
    // トークンを削除
    localStorage.removeItem("auth_token");
    return response.data;
}

// ユーザーのデータ
export async function getCurrentUser(): Promise<User | null>{
    try{
        const response =await apiClient.get<UserResponse>("/user");

        if(response.data.success && response.data.data){
            return response.data.data;
        }
        return null;
        
    }catch(error){
        return null;
    }
}