# 要件定義書 — AI付きTo‑Doアプリ（個人向け）v1.0
作成日: 2025-09-18  / 想定プラットフォーム: iOS/Android（Flutter）、Web（将来）  / バックエンド: **Laravel 12** + MySQL/Redis

> 目的: 「やる気がない・先延ばし」を減らし、**2クリックで開始**できる実行重視のTo‑Do。AIが“今日やる3つ”を提案し、**小さく始める**を支援する。

---

## 0. 用語 / 記法
- **Task**: 1つのやること。
- **Subtask**: Taskの分割ステップ。
- **Session**: 集中実行の1セット（Pomodoroなど）。
- **Nudge**: 文脈に応じた短い促しメッセージ。
- **Top3**: 今日の最重要3件。
- **AI Coach**: チャット風のコーチ機能。

---

## 1. 背景・課題・ゴール
### 1.1 課題
- 先延ばし・やる気不足で着手できない。
- 予定が多く優先度がぶれる。
- やることが大きく曖昧で、**最初の一歩**が分からない。

### 1.2 プロダクトゴール
- **摩擦最小化**: 2クリックでFocus開始。
- **行動の細分化**: AIが20–30分粒度に自動ブレイクダウン。
- **当日最適化**: エネルギー/予定/締切から**Top3**をAIが提案。
- **リフレクション**: 1分日次レビューで改善サイクル。

### 1.3 成功指標（MVP）
- D7継続率 ≥ 35%
- 1日平均**Deep‑work**（集中分）≥ 45分
- 1日**開始までの平均タップ数** ≤ 2.0
- 週あたり**Top3完了率** ≥ 60%

---

## 2. スコープ
### 2.1 MVPに含む
- アカウント/認証（メール + Apple/Google）
- Onboarding（目標/空き時間/通知）
- Home: **Today**（Top3、進捗リング、Start Focus）
- Add/Edit Task + **AIで分割**
- Focus Mode（Pomodoro、ホワイトノイズ、詰まった→ヒント）
- Calendar（ドラッグで時間ブロック）
- Stats/Streak（週/月）
- AI Coach（プリセットプロンプト + 短い会話）
- 日次AMチェックイン/PMレビュー
- Push通知（開始促し、休憩促し、振り返り）

### 2.2 以降（拡張）
- メール/Drive/カレンダー自動取り込みInbox
- Web版、ウィジェット、Apple Watch/Android Wear
- 共有/コラボ（個人→最小の共有へ）

---

## 3. ペルソナ（代表）
- **学生/若手エンジニア**: 自主学習や課題の先延ばしで困る。
- **個人開発者**: 本業後の副業/学習を継続したい。

---

## 4. 主要ユーザーフロー
1) Onboarding（2分）→ 目標/空き時間/通知 → Home
2) 朝のチェックイン → AIが**今日のTop3**を提案 → 1件選んで**Start Focus**
3) 実行中: 詰まった→**60秒ヒント**（Nudge/Coach）
4) 夜のレビュー → 成果/阻害要因 → 明日案の提示

---

## 5. 画面一覧 & 仕様（MVP）
### 5.1 Welcome/Onboarding
- 入力: 目標カテゴリ（学習/仕事/健康）、平日の空き時間帯、通知許諾。
- 出力: 初期テンプレートTask、Top3スロット生成。
- 検証: 必須項目不足時はCTA非活性。

### 5.2 Home — Today
- セクション: Top3カード（優先度点・見積もり・期限）、進捗リング（今日の集中分）、クイックアクション（Start Focus / AIで並べ替え / 延期）。
- 空状態: 「まず5分だけ始めよう」ボタン。
- エラー: ネット不通→ローカルキャッシュで表示。「同期保留」トースト。

### 5.3 Add/Edit Task
- フィールド: タイトル、メモ、期限、見積（分）、優先度、エネルギー（Low/Med/High）、プロジェクト。
- 機能: **AIで分割**→ Subtask（3–5件）生成、並べ替え/削除可能。
- 検証: タイトル必須、見積は0–600分。

### 5.4 Focus Mode
- タイマー（25/50カスタム）、残り時間、**詰まった→ヒント**、BGM、スキップ/延長。
- 中断時: 「中断理由」ショートセレクト（割り込み/低エネ/難しすぎ）。
- 完了時: 成果ノート入力→Session保存。

### 5.5 Calendar
- 週/日ビュー、Taskをドラッグで時間ブロック化。
- 競合時は重なり警告、片方を提案移動。

### 5.6 Stats & Streak
- 指標: 連続日数、Deep‑work合計、曜日/時間帯ヒートマップ。
- インサイト: 「火曜9–11があなたのゴールデンタイム」などのNudge。

### 5.7 AI Coach（ミニチャット）
- プリセット: 「45分計画を作る」「5分で始めるには？」
- セーフティ: 医療/法律/危険行為は回避メッセージ。

### 5.8 Settings
- 通知（時間・頻度）、言語（ja/vi/en）、データエクスポート（JSON/CSV）。

---

## 6. UX/UI デザイン標準（詳細）
> 目標: 一貫性・可読性・**最初の一歩**を押し出す。WCAG 2.2 AA準拠。

### 6.1 デザイントークン（Design Tokens）
- **Color（Light）**
  - Primary: `#2E7D32`（Green 700） / Hover `#256628` / Pressed `#1F5622`
  - Accent: `#0077CC`（情報） / Warning: `#F59E0B` / Danger: `#DC2626`
  - Text: `#0F172A` / Muted: `#475569` / Line: `#E2E8F0` / BG: `#F8FAFC` / Panel: `#FFFFFF`
- **Color（Dark）**
  - BG: `#0B1220` / Panel: `#111827` / Text: `#E5E7EB` / Line: `#1F2937`
  - Primary: `#34D399`（Green 400相当、暗所で視認性）
- **Typography**
  - 日本語: Noto Sans JP（400/500/700）/ 英数字: Inter（400/600/700）
  - Scale: 12/14/16/18/20/24/32（Body=16）
- **Spacing**: 4の倍数（4/8/12/16/24/32）
- **Radius**: 16（カード）/ 24（主要CTA）
- **Shadow**: Soft‐medium（elevation 2–6）
- **Icon**: Lucideアイコンセット

### 6.2 コンポーネント規約
- **Buttons**: Primary（塗り）/ Secondary（線）/ Tertiary（テキスト）。最小タップ領域 44×44。
- **Cards**: タイトル + メタ（estimate/due/priority）+ アクション（Start/Breakdown）。
- **Checklist**: スワイプで完了、長押しで並べ替え。
- **Progress Ring**: 日/週進捗を示す。アニメは200–400ms。
- **Timer**: 大きな残り時間 + 小さな操作群（Skip/Extend/Hint）。
- **Toast/Sheet**: 成功2s、自動消滅。エラーは明示的閉じる。
- **Empty State**: イラスト + 「5分だけ始めよう」ファーストアクション。

### 6.3 インタラクション/モーション
- 遷移200–300ms、自然なイージング（cubic‑bezier(0.2,0,0,1)）。
- 重要な状態変化（開始/完了）は軽い触覚（iOS: success/impact）。

### 6.4 アクセシビリティ
- コントラスト比: 文字 4.5:1以上。
- フォーカス可視、VoiceOver/TalkBackラベル。
- タップ領域 ≥ 44px。
- 配色だけに依存せず、アイコン/テキスト併用。

### 6.5 コピー/マイクロ文言
- 行動誘導: 「まず5分だけ」/「小さく始める」。
- エラー: 行動可能な提案込み（例: ネット不通→「再試行/オフラインで続行」）。

### 6.6 i18n
- ja/vi/en。日時/数値/複数形対応。右上に切替。初回はOS言語。

---

## 7. 情報設計（IA）
- Bottom Tabs: **Home / Calendar / Stats / Coach / Settings**
- FAB（Home）: **Start Focus**

---

## 8. システム構成
```
Flutter（モバイル）
   ↓ REST/JSON + HTTPS（HTTP/2）
Laravel 12（API） — PHP 8.3 — Nginx — Docker
   ├ MySQL 8（RDS等）
   ├ Redis（Queue/Cache/RateLimit）
   ├ Horizon（ジョブ監視）
   ├ Sanctum（モバイルToken認証）
   └ Telescope（開発時）
外部: FCM（Push）/ OpenAI等LLM / Google Calendar
```

---

## 9. バックエンド設計（Laravel 12）
### 9.1 技術スタック
- PHP 8.3 / Laravel 12.x
- パッケージ: **laravel/sanctum**, laravel/horizon, fruitcake/cors, laravel/scout（任意）, spatie/laravel-permission（将来）
- 品質: PHPStan（level 6）、Pint、Pest or PHPUnit、OpenAPI生成（`darkaonline/l5-swagger` 等）

### 9.2 認証/権限
- モバイル: SanctumのPersonal Access Token（端末別に発行）。
- 2FA（将来）。

### 9.3 データモデル（ERD）
- **users**(id, name, email, password_hash, locale, timezone, created_at)
- **projects**(id, user_id, name, color)
- **tasks**(id, user_id, project_id?, title, note, due_at?, estimate_min?, priority[1–5], energy[low/med/high], status[pending/doing/done], created_at, updated_at)
- **subtasks**(id, task_id, title, order, done)
- **sessions**(id, user_id, task_id?, start_at, duration_min, outcome[done/skip/interrupted], notes)
- **nudges**(id, user_id, message, context, created_at)
- **ai_summaries**(id, user_id, day, highlights JSON, blockers JSON, plan JSON)
- **push_tokens**(id, user_id, platform, token)
- **integrations**(id, user_id, provider[google_calendar], access_token(enc), refresh_token(enc), scope, synced_at)
- **attachments**(id, task_id, url, mime)
- 主要インデックス: tasks(user_id, status, due_at), sessions(user_id, start_at)

### 9.4 API設計（REST, JSON, OpenAPI 3.1）
**共通**
- 認証: `Authorization: Bearer <token>`（Sanctum）
- エラーフォーマット（例）:
```json
{"error": {"code": "VALIDATION_ERROR", "message": "title is required", "fields": {"title": ["required"]}}}
```

**エンドポイント（抜粋）**
- `POST /auth/register` / `POST /auth/login` / `POST /auth/logout`
- `GET /me`（プロフィール）
- `GET /tasks?status=&due_before=&q=` / `POST /tasks` / `GET /tasks/{id}` / `PATCH /tasks/{id}` / `DELETE /tasks/{id}`
- `POST /tasks/{id}/subtasks` / `PATCH /subtasks/{id}` / `DELETE /subtasks/{id}`
- `POST /sessions/start` / `POST /sessions/stop` / `GET /sessions?from=&to=`
- `GET /stats/weekly` / `GET /stats/monthly`
- `POST /push/register`（FCMトークン）
- `POST /calendar/sync`（Google OAuth後のpull one‑way）

**AI系**
- `POST /ai/breakdown` → 入力: `{ "title": "Học Java 2h", "context": {"level": "beginner"} }`
  - 出力: `{ "subtasks": [{"title":"環境準備(10m)"}, {"title":"基礎復習(20m)"}, ...] }`
- `POST /ai/plan-today` → 入力: `{ "tasks": [...], "energy": "low", "calendar": [...] }`
  - 出力: `{ "top3": [...], "blocks": [{"taskId":1, "start":"09:00", "end":"09:30"}] }`
- `POST /ai/nudge` → 入力: `{ "state": {"reason":"procrastination", "time":"evening"} }`
- `POST /ai/review` → PMレビュー要約

**バリデーション例（Laravel Rule）**
- title: `required|string|max:120`
- estimate_min: `nullable|integer|min:0|max:600`
- due_at: `nullable|date|after:now`

**Etag/キャッシュ**
- `GET`系は`ETag/If-None-Match`対応、リストは`updated_at`降順 + カーソルページング。

### 9.5 AI連携
- プロバイダ: OpenAI等（サーバーサイド呼び出し）。
- **関数呼び出し（function calling）**: `create_subtasks`, `schedule_today`, `suggest_nudge`。
- セーフティ: 危険行為や医療/法務の助言は**回避+一般情報**に留める。
- コスト最適化: レスポンス要約キャッシュ、温度/最大トークン制御、バッチ化。

### 9.6 セキュリティ/プライバシー
- HTTPS必須、HSTS、CORS制限（モバイルOriginのみ）。
- PIIは最小収集。AIへ送る文脈は**必要最小限**にマスク。
- アクセストークン暗号化（MySQL at‑rest + AES‐256）、.envでKMS管理。
- 監査ログ（重要操作: 削除/エクスポート）。
- レート制限: 認証外(10/min/IP)、認証済(120/min/token)。

### 9.7 可観測性
- 構造化ログ(JSON)、リクエストID、重要指標: p95応答<300ms、エラー率<1%。
- Horizonでキュー監視、失敗ジョブ再試行（上限3）。

### 9.8 オフライン/同期
- モバイル側ローカルキャッシュ（Hive/SQLite）。
- 競合: `updated_at`比較 + サーバ優先、差分マージ戦略（メモはCRDT将来）。

---

## 10. 非機能要件
- パフォーマンス: API p95 < 300ms、TTFB < 200ms。
- 可用性: 99.9%（時間外メンテ除く）。
- スケーラビリティ: Horizon + Redisスケール、読み取りはキャッシュ優先。
- バックアップ: DB日次スナップショット、30日保持。

---

## 11. インフラ/DevOps
- Docker Compose（local）/ IaC（Terraform: VPC, RDS, ElastiCache相当）
- 環境: dev / staging / prod（別VPC）
- CI/CD: GitHub Actions
  - Lint/PHPStan/Pest → マイグレーションdry‐run → デプロイ（Blue‑Green）
- 環境変数（例）
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

## 12. テスト計画
- Unit: モデル/サービス/バリデーション（Pest）
- Feature: 認証、Task CRUD、AIエンドポイントstub
- E2E（将来）: Flutter統合テスト（ゴールデン画像含む）
- アクセシビリティ: コントラスト/フォーカス/VoiceOverチェック項目
- 負荷: k6でRPS 100でもp95<300ms確認

---

## 13. 受け入れ基準（例）
- **AI分割**: タイトルのみ入力→平均3–5件のSubtaskが生成され、編集できる。
- **Start Focus**: Homeから2タップ以内でタイマー開始。
- **Today Top3**: 朝のチェックイン後、Top3が表示され、1件選択でFocus開始できる。
- **オフライン**: 機内モードでもHome/Task一覧を閲覧でき、復帰で自動同期。

---

## 14. リスクと対策
- **AI品質ばらつき**: テンプレート + ルール補正、ユーザーフィードバックで改善。
- **先延ばし再発**: 5分開始の強化、詰まり時の**60秒ヒント**を常設。
- **通知疲れ**: 自動最適化（反応率低下時は頻度を下げる）。

---

## 15. ロードマップ（2週間MVP）
- W1: 認証 / Task CRUD / Focus / AI分割 / チェックイン
- W2: Calendar / Stats / Nudge / PMレビュー / 仕上げ & リリース

---

## 付録A: 画面UI仕様（部品レベル）
- **Task Card**: 左色ドット（priority）、タイトル（1行省略）、メタ（⏱estimate・📅due）。右端にStart/More。
- **Timer**: 数字は32pt・等幅。休憩は緑枠、作業は主色塗り。
- **Modal: 詰まった**: 3択（細分化/難易度下げる/延長）+ 具体アクション生成。

## 付録B: APIペイロード例
```json
// POST /tasks
{"title":"Java学習","estimate_min":60,"priority":3,"energy":"med"}
// 201 Created →
{"id":123,"title":"Java学習","status":"pending"}
```

## 付録C: iOS/Androidガイドライン差異
- 戻るナビの位置、シートの高さ、触覚強度をOS別に最適化。

---

### 注記
- 本書は**個人利用中心**に最適化。将来のコラボ機能は別章で拡張。
- Laravel 12を前提にしつつ、マイナー差分は実装時に補正。

