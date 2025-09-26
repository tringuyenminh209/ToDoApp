# UI Hi‑Fi Style Guide — To‑Do App tích hợp AI
**Phiên bản:** v1.1 (Theme B: *Jade + Electric Blue*)  
**Mục tiêu:** Làm mới palette theo phương án B, hiện đại, rõ nét, giữ WCAG 2.2 AA, tương thích web (Laravel/Tailwind) + Flutter.

---

## 0) Tóm tắt thay đổi (v1.0 → v1.1)
- Đổi màu **Primary** từ Green 700 sang **Jade 600 `#0FA968`**; bổ sung **Accent Electric Blue `#1F6FEB`**.
- Cập nhật toàn bộ trạng thái (hover/pressed/focus/disabled) + Dark mode.
- Cập nhật gradient, Tailwind mapping, Flutter theme, JSON tokens.
- Giữ nguyên cấu trúc component & layout; tinh chỉnh focus ring và trạng thái cảnh báo để phân tầng thị giác tốt hơn.

---

## 1) Design Tokens
### 1.1 Màu sắc (Semantic — Light)
- **Primary / Action**: `#0FA968` (Jade 600)
  - **Hover**: `#0B8C57`  
  - **Pressed**: `#09764B`
  - **On‑Primary (Text/Icon)**: `#FFFFFF`
- **Accent / Info**: `#1F6FEB` (Electric Blue)
- **Success**: `#22C55E`  
- **Warning**: `#F59E0B`
- **Danger**: `#DC2626`
- **Text chính**: `#0B1220`
- **Muted**: `#475569`
- **Line/Divider**: `#E2E8F0`
- **BG**: `#F8FAFC`  
- **Panel**: `#FFFFFF`

### 1.2 Dark Mode
- **BG**: `#0A0F1A`  
- **Panel**: `#111827`  
- **Line**: `#1F2937`
- **Text**: `#E5E7EB`
- **Primary**: `#34D399` (Jade 400 tăng sáng để đạt tương phản với BG tối)
- **Accent**: `#69A2FF`

### 1.3 Gradient (CTA/Progress/Empty Hero)
- **Jade → Electric**: `linear(135deg, #0FA968 0%, #1F6FEB 100%)`
- **Soft Jade** (nền nhẹ): `linear(135deg, #E6F8F1 0%, #EEF5FF 100%)`

### 1.4 Typography
- **Font**: Inter (vi/en), Noto Sans JP (ja)  
- **Scale** (px / line-height):
  - Display 32 / 40  
  - H1 24 / 32  
  - H2 20 / 28  
  - H3 18 / 26  
  - Body 16 / 24 (mặc định)  
  - Caption 14 / 20  
  - Micro 12 / 16
- **Letter‑spacing**: Body 0%, Caption −0.2%, Micro +0.2%

### 1.5 Spacing & Layout
- **Grid base**: 4px → 8/12/16/24/32/40/48  
- **Container padding**: 16px; **Card padding**: 16–20px  
- **Khoảng cách dọc**: 12–16 giữa block; 8 giữa label‑control

### 1.6 Radius & Shadow
- **Radius**: Card 16; CTA/FAB 24; Field 12  
- **Shadow Light**:  
  - L1: `0 1px 2px rgba(2,6,23,0.06)`  
  - L2: `0 4px 12px rgba(2,6,23,0.08)`
- **Shadow Dark**: giảm blur, tăng spread nhẹ để tránh “bệt”.

### 1.7 Icon & Ilustration
- **Icon**: Lucide 20/24/28, nét 1.5px  
- **Empty State**: 2 tông xám + điểm nhấn Jade/Accent

### 1.8 CSS Variables
```css
:root{
  --c-primary:#0FA968; --c-primary-hover:#0B8C57; --c-primary-press:#09764B; --on-primary:#FFFFFF;
  --c-accent:#1F6FEB; --c-text:#0B1220; --c-muted:#475569; --c-line:#E2E8F0;
  --c-bg:#F8FAFC; --c-panel:#FFFFFF; --c-success:#22C55E; --c-warn:#F59E0B; --c-danger:#DC2626; --c-info:#1F6FEB;
  --r-card:16px; --r-cta:24px; --r-field:12px; --space:4px; --focus:#A7F3D0; /* jade‑200 */
}
@media (prefers-color-scheme: dark){
  :root{
    --c-bg:#0A0F1A; --c-panel:#111827; --c-line:#1F2937; --c-text:#E5E7EB;
    --c-primary:#34D399; --c-accent:#69A2FF; --focus:#99F6E4; /* teal‑200 */
  }
}
```

### 1.9 Token JSON (chuẩn hoá cho dev)
```json
{
  "color":{
    "primary":"#0FA968","primaryHover":"#0B8C57","primaryPressed":"#09764B","onPrimary":"#FFFFFF",
    "accent":"#1F6FEB","text":"#0B1220","muted":"#475569","line":"#E2E8F0","bg":"#F8FAFC","panel":"#FFFFFF",
    "success":"#22C55E","warn":"#F59E0B","danger":"#DC2626","info":"#1F6FEB"
  },
  "dark":{
    "bg":"#0A0F1A","panel":"#111827","line":"#1F2937","text":"#E5E7EB","primary":"#34D399","accent":"#69A2FF"
  },
  "radius":{"card":16,"cta":24,"field":12},
  "space":[4,8,12,16,24,32],
  "font":{"display":32,"h1":24,"h2":20,"h3":18,"body":16,"caption":14,"micro":12}
}
```

### 1.10 Mapping Tailwind (tiền chế)
```txt
btn-primary: bg-[#0FA968] hover:bg-[#0B8C57] active:bg-[#09764B] text-white rounded-2xl h-12 px-5 shadow
btn-secondary: border border-[#0FA968] text-[#0FA968] hover:bg-slate-50 dark:hover:bg-slate-900/40 rounded-2xl h-12 px-5
card: bg-white rounded-2xl p-5 shadow dark:bg-[#111827]
field: border border-slate-200 rounded-xl h-11 px-4 focus:border-[#0FA968] focus:ring-2 focus:ring-emerald-200 dark:border-[#1F2937]
link: text-[#1F6FEB] hover:underline
progress-ring: from-[#0FA968] to-[#1F6FEB]
```

---

## 2) Component Library (hi‑fi + trạng thái)
### 2.1 Buttons
- **Primary**: nền Jade, chữ trắng; shadow L1  
  - Hover/Pressed như token; Disabled: bg `#94A3B8`, text `#E2E8F0`, no shadow  
  - Focus: outline 2px `var(--focus)` + offset 2px
- **Secondary**: viền Jade, nền Panel; Hover: `#F1F5F9` (light) / `#0F172A` 60% (dark)
- **Tertiary**: text link `--c-accent`; underline khi hover
- **Kích thước**: H=48; IconButton H=44; hit‑area ≥44

### 2.2 Text Field / Select / DateTime
- Nền trắng, viền `#E2E8F0`, radius 12  
- Focus: viền Jade + ring emerald‑200; Error: viền `#DC2626`, helper đỏ 14px  
- Placeholder `#94A3B8`; Label 14/20

### 2.3 Chips (Filter/Energy)
- Neutral: viền `#E2E8F0`, text muted  
- Selected: nền `#E6F4EA` (green‑50), text Jade, viền Jade

### 2.4 Cards
- Panel trắng, shadow L1; header = H3; meta = Caption  
- **Task Card**:
  - Cột trái: ⦿ priority dot (1=gray → 5=red)  
  - Tiêu đề 1 dòng (ellipsis); meta: ⏱, 📅  
  - Actions: [Start] Primary‑sm | […]  
  - Hover (web): shadow L2; Selected: viền Jade 2px

### 2.5 Checklist Item
- Checkbox 20px; nhãn Body 16; subtext Caption 14  
- Done: gạch ngang, text `#94A3B8`, checkbox filled

### 2.6 Timer / Focus Module
- Số mono 48/56; **Work**: nền mờ Soft Jade; **Break**: viền Jade  
- Nút phụ: Pause/Skip/Extend = Secondary  
- Thanh tiến độ 2px bên dưới timer

### 2.7 Progress Ring
- Dày 8px; track `#E2E8F0`; progress dùng **gradient Jade→Electric**  
- % trung tâm (H2), caption “Hôm nay”

### 2.8 Calendar Blocks
- Block radius 12; viền `#CBD5E1`  
- Kéo: outline 2px Jade; tooltip thời lượng  
- Chồng chéo: viền cam `#F59E0B` + nhắc “dời 15’”

### 2.9 FAB
- Tròn 56px, icon 24; shadow L2; Pressed scale 96%

### 2.10 Bottom Tabs
- 5 tab; icon 24; label 12  
- Selected: Jade; Inactive: `#64748B`; Safe‑area iOS

### 2.11 Toast/Snackbar
- Light: nền `#111827` 90% (text trắng 14, radius 12)  
- Dark: panel `#111827`, viền trái 4px theo semantic  
- Auto‑dismiss 2s; có nút Undo khi phá huỷ

### 2.12 Modal / Bottom Sheet
- Sheet drag indicator 36×4; radius top 24  
- Primary action (phải) vs secondary (trái) rõ ràng

### 2.13 Empty States
- Minh hoạ xám, điểm nhấn Jade/Accent  
- Ví dụ Home rỗng: “Không có task — **Bắt đầu 5 phút**” (CTA Primary)

---

## 3) Trạng thái hệ thống
- **Loading**: shimmer card/list; nút spinner 16px  
- **Offline**: banner xám + icon đám mây; nút “Thử lại” Secondary  
- **Error**: thông điệp có hành động (vd: “Thử lại / Tiếp tục offline”)  
- **Overdue**: chấm cam cạnh 📅; tooltip khi chạm giữ

---

## 4) Mẫu màn hình hi‑fi
### 4.1 Home — Today
- Header H1 “Today · Thu, 18/09”  
- **Progress Ring** top; 3 Quick actions: **AI sắp xếp / Bắt đầu 5p / Dời lịch**  
- Section Top3 (card cao 88–104, gap 12); [Start] nổi bật  
- FAB góc phải dưới

### 4.2 Add / Edit Task
- Form 1 cột: Thông tin → Thời gian → Năng lượng/Ưu tiên → Subtasks  
- Nút **“Nhờ AI chia nhỏ”** = Primary‑outline (icon *magic‑wand*)

### 4.3 Focus Mode
- Timer lớn trung tâm; nền Soft Jade trong phiên làm việc  
- Hàng nút: Pause / Skip / Extend +5’  
- Dưới: Note nhanh (field 1 dòng, expandable)

### 4.4 Calendar — Tuần
- Header có tuần ±; Quick filter (All / Học / Công việc)  
- Kéo thả: từ danh sách → lưới thời gian (mobile: mở drawer)

---

## 5) Motion & Haptics
- Chuyển cảnh 200–300ms; easing `cubic-bezier(0.2,0,0,1)`  
- **Start Focus**: scale‑in 98%→100% + haptics success  
- **Hoàn thành task**: checkmark vẽ 300ms; confetti nhẹ (tắt được)

---

## 6) Accessibility (A11y)
- Tương phản chữ ≥ 4.5:1  
- Hit‑area ≥44×44; focus‑ring rõ 2px  
- Label cho icon‑only; alt‑text cho minh hoạ  
- VoiceOver/TalkBack: order tiêu điểm logic

---

## 7) Content & Microcopy (vi)
- **CTA**: “Bắt đầu 5 phút”  
- **Nudge**: “Hít sâu 1 nhịp → làm bước 1 trong 2 phút nhé?”  
- **Empty**: “Chưa có việc nào. Tạo 1 việc nhỏ trước đã?”  
- **Error**: “Không thể đồng bộ. Bạn vẫn có thể tiếp tục offline.”

---

## 8) Handoff cho Dev
### 8.1 Tailwind Mapping (cập nhật theo Theme B)
```txt
.btn-primary { @apply bg-[#0FA968] hover:bg-[#0B8C57] active:bg-[#09764B] text-white rounded-2xl h-12 px-5 shadow; }
.btn-secondary { @apply border border-[#0FA968] text-[#0FA968] hover:bg-slate-50 dark:hover:bg-slate-900/40 rounded-2xl h-12 px-5; }
.card { @apply bg-white dark:bg-[#111827] rounded-2xl p-5 shadow; }
.field { @apply border border-slate-200 dark:border-[#1F2937] rounded-xl h-11 px-4 focus:border-[#0FA968] focus:ring-2 focus:ring-emerald-200; }
.link { @apply text-[#1F6FEB] hover:underline; }
```

### 8.2 Flutter Theme (Material 3)
```dart
final theme = ThemeData(
  colorScheme: ColorScheme.light(
    primary: Color(0xFF0FA968),
    secondary: Color(0xFF1F6FEB),
    error: Color(0xFFDC2626),
    surface: Color(0xFFFFFFFF),
    background: Color(0xFFF8FAFC),
    onPrimary: Color(0xFFFFFFFF),
  ),
  textTheme: TextTheme(
    displayLarge: TextStyle(fontSize:32, height:1.25, fontWeight: FontWeight.w700),
    titleLarge: TextStyle(fontSize:24, height:1.33, fontWeight: FontWeight.w700),
    titleMedium: TextStyle(fontSize:20, height:1.4, fontWeight: FontWeight.w600),
    bodyLarge: TextStyle(fontSize:16, height:1.5),
    bodyMedium: TextStyle(fontSize:14, height:1.42),
  ),
  useMaterial3: true,
);
```

### 8.3 Mẫu kiểu màu cho biểu đồ/AI tag
- Series A: Jade `#0FA968`  
- Series B: Electric `#1F6FEB`  
- Neutral: Slate `#94A3B8`  
- Danger spike: `#DC2626`

---

## 9) Migration Notes (từ v1.0 → v1.1)
- Thay toàn bộ `#2E7D32` → `#0FA968`; `#256628` → `#0B8C57`; `#1F5622` → `#09764B`  
- Cập nhật link/CTA phụ sang `#1F6FEB`  
- Progress ring gradient chuyển sang **Jade→Electric**  
- Kiểm tra lại dark mode: primary `#34D399` + accent `#69A2FF`  
- Rà soát tương phản AA cho nút trên nền brand & gradient.

---

## 10) Checklist QA trước khi chốt hi‑fi
- [ ] Text/Icon tương phản ≥ 4.5:1 (WCAG 2.2 AA)  
- [ ] Hit‑area, focus‑ring đúng spec  
- [ ] 2 chạm để bắt đầu Focus từ Home  
- [ ] Trạng thái đầy đủ: default/hover/pressed/focus/disabled/loading  
- [ ] i18n: vi/ja/en tách chuỗi  
- [ ] Dark mode: semantic màu kiểm đủ  
- [ ] Tailwind/Flutter theme đồng bộ token

