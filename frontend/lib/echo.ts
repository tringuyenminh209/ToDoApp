/**
 * Laravel Echo + Reverb: フォーカスセッションのリアルタイム同期（モバイル→Web）
 * NEXT_PUBLIC_REVERB_* が設定されているときのみ有効。未設定なら5秒ポーリングにフォールバック。
 */

const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL || "http://localhost:8080/api";

export function isRealtimeEnabled(): boolean {
  if (typeof window === "undefined") return false;
  return Boolean(process.env.NEXT_PUBLIC_REVERB_APP_KEY);
}

export type Unsubscribe = () => void;

/**
 * フォーカスセッションのリアルタイム購読を開始する。
 * セッション開始/停止/一時停止/再開時に focusSessionChanged を dispatch する。
 */
export function subscribeFocusSession(userId: number): Unsubscribe | null {
  if (typeof window === "undefined" || !userId) return null;
  if (!isRealtimeEnabled()) return null;

  let EchoClass: new (opts: Record<string, unknown>) => {
    private: (channel: string) => { listen: (event: string, callback: () => void) => void };
    leave: (channel: string) => void;
    disconnect: () => void;
  };
  let PusherLib: unknown = null;

  try {
    PusherLib = require("pusher-js");
    EchoClass = require("laravel-echo").default;
    if (PusherLib && typeof window !== "undefined") {
      (window as unknown as { Pusher: unknown }).Pusher = PusherLib;
    }
  } catch {
    return null;
  }

  const token = localStorage.getItem("auth_token");
  if (!token) return null;

  const host = process.env.NEXT_PUBLIC_REVERB_HOST || "localhost";
  const port = process.env.NEXT_PUBLIC_REVERB_PORT || "8080";
  const scheme = process.env.NEXT_PUBLIC_REVERB_SCHEME || "http";
  const key = process.env.NEXT_PUBLIC_REVERB_APP_KEY;

  const echoInstance: {
    private: (channel: string) => { listen: (event: string, callback: () => void) => void };
    leave: (channel: string) => void;
    disconnect: () => void;
  } = new EchoClass({
    broadcaster: "pusher",
    key,
    cluster: "reverb", // Pusher-js 必須。Reverb では未使用だが指定しないと "Options object must provide a cluster" になる
    wsHost: host,
    wsPort: Number(port),
    wssPort: Number(port),
    forceTLS: scheme === "https",
    enabledTransports: ["ws", "wss"],
    disableStats: true,
    authEndpoint: `${API_BASE_URL.replace(/\/$/, "")}/broadcasting/auth`,
    auth: {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
      },
    },
  });

  echoInstance.private(`user.${userId}`).listen(".focus-session.updated", () => {
    window.dispatchEvent(new Event("focusSessionChanged"));
  });

  return () => {
    try {
      echoInstance.leave(`user.${userId}`);
      echoInstance.disconnect();
    } catch {
      // ignore
    }
  };
}
