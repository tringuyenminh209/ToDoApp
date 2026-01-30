/**
 * Type declarations for laravel-echo and pusher-js (no official @types)
 */
declare module "laravel-echo" {
  const Echo: new (options: Record<string, unknown>) => {
    private(channel: string): { listen(event: string, callback: () => void): void };
    leave(channel: string): void;
    disconnect(): void;
  };
  export default Echo;
}

declare module "pusher-js" {
  const Pusher: unknown;
  export = Pusher;
}
