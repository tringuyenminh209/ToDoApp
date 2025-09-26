# Tài liệu yêu cầu hệ thống (SRS) — To‑Do App tích hợp AI (Cá nhân) v1.0
Ngày tạo: 2025‑09‑18  | Nền tảng: iOS/Android (Flutter), Web (tương lai) | Backend: **Laravel 12** + MySQL/Redis

> Mục tiêu: Giảm trì hoãn và thiếu động lực. Ứng dụng cho phép **bắt đầu chỉ với 2 thao tác**, AI đề xuất **Top 3 việc hôm nay** và tự động **chia nhỏ nhiệm vụ** thành bước 20–30’.

---

## 0) Thuật ngữ
- **Task**: Một việc cần làm.
- **Subtask**: Bước con của Task.
- **Session**: Phiên tập trung (Pomodoro/đồng hồ đếm ngược).
- **Nudge**: Lời nhắc ngắn theo ngữ cảnh.
- **Top3**: 3 việc quan trọng nhất trong ngày.
- **AI Coach**: Trợ lý huấn luyện mini dạng chat.

---

## 1) Bối cảnh – Vấn đề – Mục tiêu
### 1.1 Vấn đề
- Trì hoãn, khó khởi động nhiệm vụ lớn/khó.
- Nhiều lịch và deadline → khó ưu tiên.
- Không rõ **bước đầu tiên** cần làm gì.

### 1.2 Mục tiêu sản phẩm
- **Giảm ma sát**: 2 chạm để bắt đầu Focus.
- **Chia nhỏ rõ ràng**: AI tự động break‑down 3–5 bước.
- **Tối ưu theo ngày**: Dựa trên năng lượng/lịch/hạn → AI đề xuất **Top3**.
- **Phản hồi nhanh**: Review 1 phút cuối ngày để cải thiện dần.

### 1.3 Chỉ số thành công (MVP)
- D7 Retention ≥ 35%
- Deep‑work trung bình/ngày ≥ 45 phút
- Số thao tác để bắt đầu ≤ 2.0
- Tỷ lệ hoàn thành Top3/tuần ≥ 60%

---

## 2) Phạm vi (Scope)
### 2.1 Bao gồm trong MVP
- Đăng ký/đăng nhập (Email + Apple/Google)
- Onboarding (mục tiêu, khung giờ rảnh, cho phép thông báo)
- **Home/Today**: Top3, vòng tiến độ ngày, **Start Focus**
- Thêm/Sửa Task + **AI chia nhỏ** Subtask
- Focus Mode (Pomodoro, white‑noise, “mắc kẹt → gợi ý 60s”)
- Lịch (kéo‑thả task vào block thời gian)
- Thống kê & Streak (tuần/tháng)
- **AI Coach** (prompt mẫu + hội thoại ngắn)
- Check‑in sáng / Review tối + push notification

### 2.2 Ngoài phạm vi (định hướng mở rộng)
- Inbox gom ý tưởng từ Email/Drive/Calendar
- Bản Web, widget, đồng hồ thông minh
- Chia sẻ/cộng tác (sau MVP)

---

## 3) Persona tiêu biểu
- **Sinh viên/lập trình viên trẻ**: tự học, hay trì hoãn bài tập.
- **Dev cá nhân**: cần duy trì thói quen code/học sau giờ làm.

---

## 4) Luồng người dùng chính
1) Onboarding (2’) → chọn mục tiêu/khung giờ/nhắc → vào Home
2) Sáng: Check‑in (năng lượng/mood/bận) → AI đề xuất **Top3**
3) Chọn 1 việc → **Start Focus** (2 chạm). Mắc kẹt → **Nudge/Hint 60s**
4) Tối: Review nhanh → ghi nhận tiến bộ → gợi ý ngày mai

---

## 5) Danh sách màn hình & đặc tả
### 5.1 Welcome / Onboarding
- Nhập: mục tiêu (Học/Làm/Khỏe), khung giờ rảnh, đồng ý thông báo.
- Kết quả: Tạo mẫu task ban đầu + khởi tạo Top3.
- Validation: Thiếu trường bắt buộc → nút tiếp tục disabled.

### 5.2 Home — Today
- Khu vực: Thẻ **Top3** (priority, estimate, due), vòng tiến độ ngày, Quick Actions (Start Focus / AI sắp xếp / Dời lịch).
- Trạng thái rỗng: Nút **“Bắt đầu 5 phút”**.
- Mất mạng: Hiển thị cache cục bộ + banner “Đang chờ đồng bộ”.

### 5.3 Add / Edit Task
- Trường: title, note, deadline, estimate (phút), priority, **energy (Low/Med/High)**, project.
- Nút **“Nhờ AI chia nhỏ”** → tạo 3–5 Subtask, cho phép sửa/xóa/kéo sắp xếp.
- Ràng buộc: title bắt buộc; estimate 0–600.

### 5.4 Focus Mode
- Timer (25/50 tuỳ chỉnh), còn lại, **Hint khi mắc kẹt**, BGM, Skip/Extend.
- Tạm dừng: chọn lý do ngắn (bị gián đoạn/thiếu năng lượng/khó quá).
- Kết thúc: ghi chú kết quả → lưu **Session**.

### 5.5 Calendar
- Ngày/Tuần, kéo‑thả task vào block thời gian; cảnh báo chồng chéo.

### 5.6 Stats & Streak
- Chỉ số: chuỗi ngày, tổng deep‑work, heatmap theo giờ/thu.
- Insight: “Thứ Ba 9–11h là giờ vàng của bạn”.

### 5.7 AI Coach (mini chat)
- Prompt nhanh: “Lập kế hoạch 45’ cho …”, “Bắt đầu khi lười thế nào?”.
- An toàn: né tư vấn y tế/pháp lý/nguy hiểm.

### 5.8 Settings
- Thông báo (giờ/tần suất), ngôn ngữ (vi/ja/en), xuất dữ liệu (JSON/CSV).

---

## 6) Tiêu chuẩn thiết kế UX/UI (chi tiết)
> Mục tiêu: nhất quán, dễ đọc, đẩy **first action** lên trước. Tuân **WCAG 2.2 AA**.

### 6.1 Design Tokens
- **Màu (Light)**
  - Primary: `#2E7D32` (Green 700) / Hover `#256628` / Pressed `#1F5622`
  - Accent: `#0077CC` (thông tin) / Warning: `#F59E0B` / Danger: `#DC2626`
  - Text: `#0F172A` / Muted: `#475569` / Line: `#E2E8F0` / BG: `#F8FAFC` / Panel: `#FFFFFF`
- **Màu (Dark)**
  - BG: `#0B1220` / Panel: `#111827` / Text: `#E5E7EB` / Line: `#1F2937`
  - Primary: `#34D399` (Green 400 để nổi trên nền tối)
- **Chữ (Typography)**
  - JP: Noto Sans JP (400/500/700) | Latinh: Inter (400/600/700)
  - Thang cỡ: 12/14/16/18/20/24/32 (Body = 16)
- **Khoảng cách (Spacing)**: bội số 4 (4/8/12/16/24/32)
- **Bo góc (Radius)**: 16 (card), 24 (CTA chính)
- **Đổ bóng (Shadow)**: mềm cấp 2–6
- **Icon**: bộ Lucide

### 6.2 Quy ước Component
- **Button**: Primary (đặc) / Secondary (viền) / Tertiary (text). Vùng chạm tối thiểu 44×44.
- **Task Card**: tiêu đề + metadata (⏱estimate / 📅due / ●priority) + Actions (Start/Breakdown).
- **Checklist**: vuốt để hoàn thành, giữ để sắp xếp.
- **Progress Ring**: tiến độ ngày/tuần, animation 200–400ms.
- **Timer**: số lớn dễ nhìn; nhóm thao tác nhỏ (Skip/Extend/Hint) đặt cạnh.
- **Toast/Bottom Sheet**: thành công tự ẩn ~2s; lỗi yêu cầu đóng rõ ràng.
- **Empty State**: minh hoạ + CTA **“Bắt đầu 5 phút”**.

### 6.3 Tương tác & Chuyển động
- Thời lượng chuyển cảnh 200–300ms, easing `cubic-bezier(0.2,0,0,1)`.
- Sự kiện quan trọng (bắt đầu/hoàn thành) kèm rung nhẹ (haptics).

### 6.4 Truy cập (Accessibility)
- Tương phản chữ ≥ 4.5:1.
- Focus rõ ràng; VoiceOver/TalkBack có nhãn.
- Vùng chạm ≥ 44px; không chỉ dựa vào màu (kèm icon/text).

### 6.5 Microcopy
- Gợi hành động: “Bắt đầu 5 phút”, “Chia nhỏ rồi làm từng bước”.
- Lỗi kèm gợi ý giải pháp (VD: mất mạng → “Thử lại / Tiếp tục offline”).

### 6.6 i18n
- vi/ja/en; định dạng ngày/giờ; chọn ngôn ngữ ở header; mặc định theo OS.

---

## 7) Kiến trúc thông tin (IA)
- Bottom Tabs: **Home / Calendar / Stats / Coach / Settings**
- FAB tại Home: **Start Focus**

---

## 8) Kiến trúc hệ thống
```
Flutter (Mobile)
   ↓ REST/JSON + HTTPS
Laravel 12 (API) — PHP 8.3 — Nginx — Docker
   ├ MySQL 8
   ├ Redis (Queue/Cache/RateLimit)
   ├ Horizon (giám sát job)
   ├ Sanctum (token cho mobile)
   └ Telescope (dev)
Tích hợp: FCM (Push) / OpenAI (LLM) / Google Calendar
```

---

## 9) Thiết kế Backend (Laravel 12)
### 9.1 Tech Stack
- PHP 8.3 / Laravel 12.x
- Packages: **laravel/sanctum**, laravel/horizon, fruitcake/cors, laravel/scout (tự chọn), spatie/laravel-permission (tương lai)
- Chất lượng: PHPStan (level 6), Pint, Pest/PHPUnit, OpenAPI (`l5-swagger`)

### 9.2 Xác thực & Phân quyền
- Mobile: Sanctum PAT (mỗi thiết bị một token).
- 2FA (lộ trình sau).

### 9.3 Mô hình dữ liệu (ERD)
- **users**(id, name, email, password_hash, locale, timezone, created_at)
- **projects**(id, user_id, name, color)
- **tasks**(id, user_id, project_id?, title, note, due_at?, estimate_min?, priority[1–5], energy[low/med/high], status[pending/doing/done], created_at, updated_at)
- **subtasks**(id, task_id, title, `order`, done)
- **sessions**(id, user_id, task_id?, start_at, duration_min, outcome[done/skip/interrupted], notes)
- **nudges**(id, user_id, message, context, created_at)
- **ai_summaries**(id, user_id, day, highlights JSON, blockers JSON, plan JSON)
- **push_tokens**(id, user_id, platform, token)
- **integrations**(id, user_id, provider[google_calendar], access_token(enc), refresh_token(enc), scope, synced_at)
- **attachments**(id, task_id, url, mime)
- Index quan trọng: tasks(user_id, status, due_at), sessions(user_id, start_at)

### 9.4 API (REST, JSON, OpenAPI 3.1)
**Chung**
- Header: `Authorization: Bearer <token>` (Sanctum)
- Lỗi chuẩn:
```json
{"error":{"code":"VALIDATION_ERROR","message":"title is required","fields":{"title":["required"]}}}
```

**Endpoints (chính)**
- `POST /auth/register` / `POST /auth/login` / `POST /auth/logout`
- `GET /me`
- `GET /tasks?status=&due_before=&q=` / `POST /tasks` / `GET /tasks/{id}` / `PATCH /tasks/{id}` / `DELETE /tasks/{id}`
- `POST /tasks/{id}/subtasks` / `PATCH /subtasks/{id}` / `DELETE /subtasks/{id}`
- `POST /sessions/start` / `POST /sessions/stop` / `GET /sessions?from=&to=`
- `GET /stats/weekly` / `GET /stats/monthly`
- `POST /push/register`
- `POST /calendar/sync`

**AI**
- `POST /ai/breakdown` — `{ "title": "Học Java 2h", "context": {"level":"beginner"} }` → `{ "subtasks": [...] }`
- `POST /ai/plan-today` — `{ "tasks": [...], "energy":"low", "calendar": [...] }` → `{ "top3": [...], "blocks": [...] }`
- `POST /ai/nudge` — `{ "state": {"reason":"procrastination","time":"evening"} }`
- `POST /ai/review` — tóm tắt PM

**Ràng buộc (Laravel Rules)**
- title: `required|string|max:120`
- estimate_min: `nullable|integer|min:0|max:600`
- due_at: `nullable|date|after:now`

**Cache/Etag**
- `GET` hỗ trợ `ETag/If-None-Match`; danh sách phân trang kiểu cursor; sắp xếp `updated_at` DESC.

### 9.5 Tích hợp AI
- Gọi LLM phía server (OpenAI…).
- **Function calling**: `create_subtasks`, `schedule_today`, `suggest_nudge`.
- An toàn: tránh tư vấn rủi ro; lọc prompt; ẩn danh dữ liệu trước khi gửi.
- Tối ưu chi phí: cache tóm tắt, giới hạn độ dài, batch, điều chỉnh temperature/max_tokens.

### 9.6 Bảo mật & Riêng tư
- Bắt buộc HTTPS, HSTS; CORS giới hạn nguồn mobile.
- Thu thập tối thiểu PII; mask dữ liệu khi gửi AI.
- Mã hoá token (AES‑256 at‑rest); quản lý secret qua .env/KMS.
- Nhật ký kiểm toán (xóa/xuất dữ liệu).
- Rate‑limit: 10/min/IP (khách), 120/min/token (đăng nhập).

### 9.7 Quan sát được (Observability)
- Log cấu trúc JSON, Request‑ID; p95 < 300ms, error < 1%.
- Horizon giám sát queue; retry job thất bại (tối đa 3).

### 9.8 Offline & Đồng bộ
- Cache cục bộ (Hive/SQLite) phía mobile.
- Xung đột: so `updated_at` + ưu tiên server; ghi chú dùng chiến lược merge; (CRDT cho tương lai).

---

## 10) Phi chức năng (NFR)
- Hiệu năng: p95 API < 300ms; TTFB < 200ms.
- Sẵn sàng: 99.9% (trừ bảo trì ngoài giờ).
- Mở rộng: Redis + Horizon; đọc ưu tiên cache.
- Sao lưu: snapshot DB hằng ngày, giữ 30 ngày.

---

## 11) Hạ tầng & DevOps
- Docker Compose (local); IaC Terraform (VPC, DB, Cache)
- Môi trường: dev / staging / prod (VPC tách biệt)
- CI/CD (GitHub Actions): Lint/PHPStan/Pest → migrate dry‑run → deploy Blue‑Green
- Biến môi trường mẫu:
```
APP_ENV=production
APP_KEY=base64:...
DB_DATABASE=todo
DB_USERNAME=...
DB_PASSWORD=...
REDIS_HOST=...
SANCTUM_STATEFUL_DOMAINS=
AI_PROVIDER=openai
OPENAI_API_KEY=...
FCM_SERVER_KEY=...
GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
```

---

## 12) Kế hoạch kiểm thử
- Unit: Model/Service/Validation (Pest)
- Feature: Auth, Task CRUD, AI endpoints (stub)
- E2E (tương lai): Flutter integration + ảnh chuẩn (golden test)
- A11y: kiểm màu tương phản/focus/nhãn đọc màn hình
- Tải: k6 RPS=100 vẫn p95<300ms

---

## 13) Tiêu chí chấp nhận (Acceptance)
- **AI chia nhỏ**: chỉ cần title → sinh 3–5 subtask có thể chỉnh sửa.
- **Start Focus**: từ Home, tối đa 2 chạm để bắt đầu timer.
- **Top3 hôm nay**: sau check‑in sáng, hiển thị Top3; chọn 1 → Focus ngay.
- **Offline**: chế độ máy bay vẫn xem Home/Task; khi online tự đồng bộ.

---

## 14) Rủi ro & Giảm thiểu
- Chất lượng AI dao động → prompt template + rule‑based hậu xử lý + feedback người dùng.
- Tái trì hoãn → nhấn mạnh CTA **“Bắt đầu 5 phút”** + **Hint 60s**.
- Quá tải thông báo → tự điều chỉnh tần suất dựa trên tỷ lệ phản hồi.

---

## 15) Lộ trình (MVP 2 tuần)
- Tuần 1: Auth / Task CRUD / Focus / AI break‑down / Check‑in
- Tuần 2: Calendar / Stats / Nudge / Review tối / polish & phát hành nội bộ

---

## Phụ lục A: Đặc tả UI cấp thành phần
- **Task Card**: chấm màu priority bên trái; title (1 dòng, ellipsis); meta estimate/due; nút Start/More bên phải.
- **Timer**: số cỡ 32pt, mono; work = nền primary; break = viền xanh.
- **Modal “Mắc kẹt”**: 3 lựa chọn (chia nhỏ/giảm độ khó/giãn thời gian) + hành động cụ thể.

## Phụ lục B: Ví dụ payload API
```json
// POST /tasks
{"title":"Học Java","estimate_min":60,"priority":3,"energy":"med"}
// 201 Created
{"id":123,"title":"Học Java","status":"pending"}
```

## Phụ lục C: Khác biệt iOS/Android
- Vị trí nút back, chiều cao bottom sheet, cường độ rung khác nhau; tinh chỉnh theo OS.

---

### Ghi chú
- Tài liệu tối ưu cho **người dùng cá nhân**. Tính năng cộng tác sẽ thêm ở bản kế tiếp.
- Mặc định Laravel 12; chênh lệch nhỏ sẽ điều chỉnh ở giai đoạn implement.

