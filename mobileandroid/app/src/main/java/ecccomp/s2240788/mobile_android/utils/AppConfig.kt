package ecccomp.s2240788.mobile_android.utils

/**
 * App Configuration
 * 
 * Thay đổi BASE_URL ở đây để kết nối với server từ xa
 * 
 * Các phương án:
 * 1. Local development: "http://localhost:8080/api/"
 * 2. Port Forwarding: "http://YOUR_PUBLIC_IP:8080/api/"
 * 3. Ngrok: "https://abc123.ngrok-free.app/api/"
 * 4. Cloudflare: "https://todoapp.yourdomain.com/api/"
 */
object AppConfig {
    // ⚠️ THAY ĐỔI URL Ở ĐÂY
    // 
    // Production環境（本番環境）
    const val BASE_URL = "https://api.todokizamu.me/api/"
    
    // Cho local development (emulator Android):
    // 10.0.2.2 は emulator からホストPCの localhost へアクセスするためのIP
    // const val BASE_URL = "http://10.0.2.2:8080/api/"
    
    // Cho Port Forwarding (thay YOUR_PUBLIC_IP bằng IP public của bạn):
    // const val BASE_URL = "http://52.21.89.124:8080/api/"
    
    // Cho Ngrok (thay abc123.ngrok-free.app bằng URL ngrok của bạn):
    // const val BASE_URL = "https://abc123.ngrok-free.app/api/"
    
    // Lưu ý:
    // - Production環境ではHTTPSを使用
    // - 変更後はrebuildが必要: ./gradlew clean assembleDebug
}

