<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels (リアルタイム: フォーカスセッション同期)
|--------------------------------------------------------------------------
| Private channel: user.{id} — 認証済みユーザーのみ購読可能
*/

Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
