<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * フォーカスセッション開始/停止時にWebへリアルタイム通知（モバイル→Web同期用）
 */
class FocusSessionUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $userId,
        public string $action, // 'started' | 'stopped' | 'paused' | 'resumed'
        public ?array $session = null
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->userId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'focus-session.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'action' => $this->action,
            'session' => $this->session,
        ];
    }
}
