<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    /**
     * Get all notifications for authenticated user
     * GET /api/notifications
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $perPage = $request->query('per_page', 20);
            $type = $request->query('type'); // Filter by type
            $unread = $request->query('unread'); // Show only unread

            $query = Notification::where('user_id', $userId)
                ->orderBy('created_at', 'desc');

            // Filter by type
            if ($type) {
                $query->where('type', $type);
            }

            // Filter unread only
            if ($unread === 'true' || $unread === '1') {
                $query->where('is_read', false);
            }

            $notifications = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $notifications->items(),
                'pagination' => [
                    'total' => $notifications->total(),
                    'per_page' => $notifications->perPage(),
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get unread notifications count
     * GET /api/notifications/unread/count
     */
    public function unreadCount(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;

            $count = Notification::where('user_id', $userId)
                ->where('is_read', false)
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'unread_count' => $count
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get unread count',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get notification statistics
     * GET /api/notifications/stats
     */
    public function stats(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $days = $request->query('days', 7);

            $total = Notification::where('user_id', $userId)
                ->where('created_at', '>=', now()->subDays($days))
                ->count();

            $unread = Notification::where('user_id', $userId)
                ->where('is_read', false)
                ->count();

            $byType = Notification::where('user_id', $userId)
                ->where('created_at', '>=', now()->subDays($days))
                ->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_notifications' => $total,
                    'unread_notifications' => $unread,
                    'read_notifications' => $total - $unread,
                    'by_type' => $byType,
                    'period_days' => $days,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get notification stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark notification as read
     * PUT /api/notifications/{id}/read
     */
    public function markAsRead(Request $request, int $id): JsonResponse
    {
        try {
            $userId = $request->user()->id;

            $notification = Notification::where('id', $id)
                ->where('user_id', $userId)
                ->firstOrFail();

            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => '通知を既読にしました',
                'data' => $notification
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     * PUT /api/notifications/mark-all-read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;

            $count = Notification::where('user_id', $userId)
                ->where('is_read', false)
                ->update(['is_read' => true]);

            return response()->json([
                'success' => true,
                'message' => 'すべての通知を既読にしました',
                'data' => [
                    'marked_count' => $count
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark all as read',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete notification
     * DELETE /api/notifications/{id}
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $userId = $request->user()->id;

            $notification = Notification::where('id', $id)
                ->where('user_id', $userId)
                ->firstOrFail();

            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => '通知を削除しました'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete all read notifications
     * DELETE /api/notifications/clear-read
     */
    public function clearRead(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;

            $count = Notification::where('user_id', $userId)
                ->where('is_read', true)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => '既読通知を削除しました',
                'data' => [
                    'deleted_count' => $count
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear read notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a notification (for testing or admin purposes)
     * POST /api/notifications
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:reminder,achievement,motivational,system',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'data' => 'nullable|array',
            'scheduled_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $notification = Notification::create([
                'user_id' => $request->user()->id,
                'type' => $request->type,
                'title' => $request->title,
                'message' => $request->message,
                'data' => $request->data,
                'scheduled_at' => $request->scheduled_at ?? now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => '通知を作成しました',
                'data' => $notification
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent notifications (last 24 hours)
     * GET /api/notifications/recent
     */
    public function recent(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;

            $notifications = Notification::where('user_id', $userId)
                ->where('created_at', '>=', now()->subDay())
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $notifications
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch recent notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
