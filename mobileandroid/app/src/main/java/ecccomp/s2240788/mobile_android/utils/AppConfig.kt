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
    // Cho local development (emulator Android):
    // 10.0.2.2 は emulator からホストPCの localhost へアクセスするためのIP
    const val BASE_URL = "http://10.0.2.2:8080/api/"
    
    // Cho Port Forwarding (thay YOUR_PUBLIC_IP bằng IP public của bạn):
    // const val BASE_URL = "http://123.45.67.89:8080/api/"
    
    // Cho Ngrok (thay abc123.ngrok-free.app bằng URL ngrok của bạn):
    // const val BASE_URL = "https://abc123.ngrok-free.app/api/"
    
    // Cho Cloudflare Tunnel (thay todoapp.yourdomain.com bằng domain của bạn):
    // const val BASE_URL = "https://todoapp.yourdomain.com/api/"
    
    // Lưu ý:
    // - Nếu dùng HTTPS (Ngrok, Cloudflare), đổi http:// thành https://
    // - Nếu dùng HTTP (Port Forwarding), giữ nguyên http://
    // - Sau khi thay đổi, rebuild app: ./gradlew clean assembleDebug
}

