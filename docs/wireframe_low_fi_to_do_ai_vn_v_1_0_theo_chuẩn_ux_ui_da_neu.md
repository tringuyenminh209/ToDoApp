# Wireframe low‑fi — To‑Do App tích hợp AI (Bản tiếng Việt) v1.0
> Lo‑fi, đơn sắc (grayscale). Áp dụng chuẩn UX/UI đã nêu (WCAG 2.2 AA, vùng chạm ≥44px, motion 200–300ms). Mục tiêu: **2 chạm để bắt đầu Focus**.

## Ký hiệu chung
- [BTN] = Button chính; (Input) = trường nhập; {Chip} = nhãn/chọn nhanh; ⦿ = dot priority; ⏱ = estimate; 📅 = due; … = menu.
- Tabs đáy: Home | Calendar | Stats | Coach | Settings
- FAB: nút tròn nổi góc phải dưới.

---

## 1) Onboarding (3 bước)
### 1.1 Màn 1 — Chọn mục tiêu
```
┌─────────────────────────────────────┐
│   Chào mừng!                       │
│   Mục tiêu chính của bạn là gì?    │
│  (chọn 1–2)                        │
│  [ ] Học tập  [ ] Công việc        │
│  [ ] Sức khỏe [ ] Khác…            │
│                                     │
│  [Tiếp tục]        [Bỏ qua]        │
└─────────────────────────────────────┘
```

### 1.2 Màn 2 — Khung giờ rảnh
```
┌─────────────────────────────────────┐
│ Bạn thường rảnh khi nào?            │
│ {Sáng} {Chiều} {Tối}                │
│ (Chọn giờ cụ thể) [09:00–11:00]     │
│ {Thêm khung giờ}                    │
│                                     │
│ [Tiếp tục]                          │
└─────────────────────────────────────┘
```

### 1.3 Màn 3 — Thông báo & ngôn ngữ
```
┌─────────────────────────────────────┐
│ Cho phép gửi nhắc?  (Bật/Tắt)       │
│ Ngôn ngữ: (vi ▼)                     │
│                                     │
│ [Bắt đầu sử dụng]                   │
└─────────────────────────────────────┘
```

---

## 2) Home — Today
### 2.1 Trạng thái bình thường
```
┌─────────────────────────────────────┐
│ Today — Thu, 18/09                  │   ⟳ (pull to refresh)
│ ─────────────────────────────────   │
│  ⭘ Vòng tiến độ ngày: 45%           │
│  Quick: [AI sắp xếp] [Bắt đầu 5p]   │
│         [Dời lịch thông minh]       │
│ ─────────────────────────────────   │
│ Top 3 hôm nay                       │
│ 1) ⦿ Task A (⏱25’ · 📅 Hôm nay 15:00) … [Start]
│    ─ Sub 1  ○  Sub 2  ○              
│ 2) ⦿ Task B (⏱40’) … [Start]         
│ 3) ⦿ Task C (⏱20’ · 📅 Mai) … [Start]
│ ─────────────────────────────────   │
│ [ + Thêm task ]                      │
└─────────────────────────────────────┘
              (FAB) ● Start Focus

Tabs: [Home]*  Calendar  Stats  Coach  Settings
```

### 2.2 Trạng thái rỗng / offline
```
┌─────────────────────────────────────┐
│ Không có task nào.                  │
│ [Bắt đầu 5 phút]                    │
│ Banner: Đang offline — sẽ đồng bộ.  │
└─────────────────────────────────────┘
```

---

## 3) Add / Edit Task
```
┌─────────────────────────────────────┐
│ Thêm nhiệm vụ                       │
│ (Tiêu đề) _______________________    │
│ (Ghi chú ngắn) ________________     │
│ (Deadline) [ 18/09 15:00  ] (📅)     │
│ (Estimate phút) [ 25 ]              │
│ Priority: {1}{2}{3●}{4}{5}          │
│ Energy: {Low}{Med●}{High}           │
│ Project: (Chọn…)                    │
│ ─────────────────────────────────   │
│ [Nhờ AI chia nhỏ]                   │
│  Subtasks:                          │
│   ▢ Bước 1  (≡)   …                 │
│   ▢ Bước 2  (≡)   …                 │
│   [+ Thêm bước]                     │
│ ─────────────────────────────────   │
│ [Lưu]            [Huỷ]              │
└─────────────────────────────────────┘

Validation: Tiêu đề bắt buộc; Estimate 0–600; cảnh báo khi quá hạn.
```

---

## 4) Focus Mode
```
┌─────────────────────────────────────┐
│ Task A                               │
│  [25:00]                             │  (số lớn, mono)
│  [Pause]  [Skip]  [Extend +5’]       │
│  Hint 60s: [Tôi đang mắc kẹt]        │
│ ─────────────────────────────────    │
│ Ghi chú sau phiên:  ______________   │
│ [Kết thúc & Lưu]                     │
└─────────────────────────────────────┘

Sheet “Tôi đang mắc kẹt”
┌───────────────────────┐
│ Bạn đang gặp vấn đề gì?│
│ {Chia nhỏ hơn} {Giảm độ khó}
│ {Đổi sang bước khởi động 5’}
│ [Nhờ AI gợi ý]          │
└───────────────────────┘
```

---

## 5) Calendar (Ngày/Tuần)
### 5.1 Tuần
```
┌─────────────────────────────────────┐
│ ◄ 18–24/09 ►   (Tuần)              │
│ Th 2  Th 3  Th 4  Th 5  Th 6  Th 7 │
│ |   |■■Task A■■|   |   |■B|   |    │
│ |   |          |   |   |  |   |    │
│ |── thời gian theo trục ────────── │
└─────────────────────────────────────┘
Drag & drop: kéo Task từ danh sách vào slot; cảnh báo chồng chéo.
```

### 5.2 Ngày
```
┌─────────────────────────────────────┐
│ Thứ 5, 18/09 (Ngày)                 │
│ 08:00 |                              │
│ 09:00 |  [Block: Task A 09:00–09:30] │
│ 10:00 |                              │
│ 11:00 |  [Block: Task B 11:00–11:40] │
└─────────────────────────────────────┘
```

Modal xung đột:
```
┌────────────────────────┐
│ Hai block đang chồng.  │
│ [Dời Task A 15’] [Giữ] │
└────────────────────────┘
```

---

## 6) Stats & Streak
```
┌─────────────────────────────────────┐
│ Streak: 7 ngày 🔥                   │
│ Vòng tiến độ tuần: 62%              │
│ Biểu đồ Deep‑work (cột 7 ngày)      │
│  █  ▆  █  ▇  █  █  ▂                 │
│ Heatmap giờ vàng (0–24h × T2–CN)     │
│  ░░▒▒▓▓▓█ …                          │
│ Insight: “Thứ 3 9–11h là giờ vàng.”  │
└─────────────────────────────────────┘
```

---

## 7) AI Coach (mini chat)
```
┌─────────────────────────────────────┐
│ Bạn: “Lập kế hoạch 45’ học Java.”   │
│ Bot: “Đề xuất: 1) Warm‑up 5’ …”     │
│ Quick: {Kế hoạch 45’} {Bắt đầu 5’}  │
│ Nhập tin… [____________________] ⏎ │
└─────────────────────────────────────┘
```

---

## 8) Settings
```
┌─────────────────────────────────────┐
│ Thông báo: 07:30  12:00  20:30      │
│ Tần suất: {Thấp}{Vừa●}{Cao}         │
│ Ngôn ngữ: (vi▼)                      │
│ Đồng bộ Calendar: [Kết nối Google]  │
│ Xuất dữ liệu: [JSON] [CSV]           │
│ Vùng tập trung: {Chặn banner in‑app} │
└─────────────────────────────────────┘
```

---

## 9) Thành phần & Quy ước (tóm tắt lo‑fi)
- **Button**: cao 48px; Primary dùng nền xám đậm (lo‑fi), Secondary viền xám.
- **Card**: bo 16; padding 16; tiêu đề 1 dòng + meta nhỏ.
- **Checklist**: checkbox trái; kéo sắp xếp bằng biểu tượng ≡.
- **Progress Ring**: đường dày; số % ở giữa.
- **Toast**: góc dưới; tự tắt sau 2s; lỗi cần nút “Đóng”.
- **Empty**: minh hoạ ASCII + CTA “Bắt đầu 5 phút”.

---

## 10) Luồng thao tác quan trọng
- **2 chạm bắt đầu Focus**: Home → [Start] trên Top3 → Focus Mode.
- **AI chia nhỏ**: Add Task → [Nhờ AI chia nhỏ] → Subtask 3–5 bước.
- **Check‑in sáng**: Popup 3 câu → sinh **Top3**.
- **Review tối**: Popup tóm tắt → gợi ý ngày mai.

---

## 11) Trạng thái/biến thể cần thiết kế
- Loading: shimmer card.
- Offline: banner xám + biểu tượng mây.
- Overdue: chấm cảnh báo cạnh 📅 due.
- Dài tiêu đề: cắt 1 dòng + tooltip khi giữ.
- Năng lượng thấp: đề xuất “Bắt đầu 5 phút”/task năng lượng thấp.

---

## 12) Ghi chú A11y & Motion
- Tương phản ≥ 4.5:1; phím tắt bàn phím (Web sau này).
- Haptics nhẹ khi bắt đầu/kết thúc Focus.
- Motion 200–300ms; easing `cubic-bezier(0.2,0,0,1)`.

---

## 13) Phụ lục: Wireframe hiển thị cho Tablet (tuỳ chọn)
- Bố cục 2 cột: trái = Today/Calendar; phải = Chi tiết task/subtask.

---

### Tiếp theo
- Nếu ok, mình có thể **xuất file hi‑fi guideline (color + spacing chuẩn)** hoặc tạo **skeleton Flutter** (Home, Add Task, Focus, Calendar, Stats, Coach, Settings) theo wireframe này để chạy thử.

