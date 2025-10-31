package ecccomp.s2240788.mobile_android.services

import android.app.*
import android.content.Context
import android.content.Intent
import android.os.Build
import android.os.CountDownTimer
import android.os.IBinder
import androidx.core.app.NotificationCompat
import ecccomp.s2240788.mobile_android.R
import ecccomp.s2240788.mobile_android.ui.activities.FocusSessionActivity

/**
 * FocusTimerService
 * バックグラウンドでタイマーを実行するForeground Service
 * - Notification を表示
 * - タイマー完了時に通知
 */
class FocusTimerService : Service() {

    private var countDownTimer: CountDownTimer? = null
    private var timeRemainingMillis: Long = 0
    private var totalTimeMillis: Long = 0
    private var isRunning = false

    companion object {
        const val CHANNEL_ID = "focus_timer_channel"
        const val NOTIFICATION_ID = 1001
        
        const val ACTION_START = "ACTION_START"
        const val ACTION_PAUSE = "ACTION_PAUSE"
        const val ACTION_STOP = "ACTION_STOP"
        
        const val EXTRA_DURATION_MILLIS = "EXTRA_DURATION_MILLIS"
        const val EXTRA_TASK_TITLE = "EXTRA_TASK_TITLE"
    }

    override fun onCreate() {
        super.onCreate()
        createNotificationChannel()
    }

    override fun onStartCommand(intent: Intent?, flags: Int, startId: Int): Int {
        when (intent?.action) {
            ACTION_START -> {
                totalTimeMillis = intent.getLongExtra(EXTRA_DURATION_MILLIS, 25 * 60 * 1000L)
                timeRemainingMillis = totalTimeMillis
                val taskTitle = intent.getStringExtra(EXTRA_TASK_TITLE) ?: "Focus Session"
                startTimer(taskTitle)
            }
            ACTION_PAUSE -> {
                pauseTimer()
            }
            ACTION_STOP -> {
                stopTimer()
            }
        }
        return START_NOT_STICKY
    }

    override fun onBind(intent: Intent?): IBinder? = null

    /**
     * タイマーを開始
     */
    private fun startTimer(taskTitle: String) {
        if (isRunning) return
        
        isRunning = true
        startForeground(NOTIFICATION_ID, buildNotification(taskTitle, timeRemainingMillis))

        countDownTimer = object : CountDownTimer(timeRemainingMillis, 1000) {
            override fun onTick(millisUntilFinished: Long) {
                timeRemainingMillis = millisUntilFinished
                updateNotification(taskTitle, millisUntilFinished)
            }

            override fun onFinish() {
                onTimerComplete(taskTitle)
            }
        }.start()
    }

    /**
     * タイマーを一時停止
     */
    private fun pauseTimer() {
        countDownTimer?.cancel()
        isRunning = false
        stopForeground(STOP_FOREGROUND_REMOVE)
        stopSelf()
    }

    /**
     * タイマーを停止
     */
    private fun stopTimer() {
        countDownTimer?.cancel()
        isRunning = false
        stopForeground(STOP_FOREGROUND_REMOVE)
        stopSelf()
    }

    /**
     * タイマー完了時の処理
     */
    private fun onTimerComplete(taskTitle: String) {
        isRunning = false
        
        // 完了通知を表示
        val completionNotification = buildCompletionNotification(taskTitle)
        val notificationManager = getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
        notificationManager.notify(NOTIFICATION_ID + 1, completionNotification)
        
        stopForeground(STOP_FOREGROUND_REMOVE)
        stopSelf()
    }

    /**
     * Notification を更新
     */
    private fun updateNotification(taskTitle: String, millisRemaining: Long) {
        val notification = buildNotification(taskTitle, millisRemaining)
        val notificationManager = getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
        notificationManager.notify(NOTIFICATION_ID, notification)
    }

    /**
     * Notification を作成
     */
    private fun buildNotification(taskTitle: String, millisRemaining: Long): Notification {
        val minutes = (millisRemaining / 1000) / 60
        val seconds = (millisRemaining / 1000) % 60
        val timeText = String.format("%02d:%02d", minutes, seconds)

        // Intent to open FocusSessionActivity
        val intent = Intent(this, FocusSessionActivity::class.java).apply {
            flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
        }
        val pendingIntent = PendingIntent.getActivity(
            this, 0, intent,
            PendingIntent.FLAG_UPDATE_CURRENT or PendingIntent.FLAG_IMMUTABLE
        )

        // Pause action
        val pauseIntent = Intent(this, FocusTimerService::class.java).apply {
            action = ACTION_PAUSE
        }
        val pausePendingIntent = PendingIntent.getService(
            this, 0, pauseIntent,
            PendingIntent.FLAG_UPDATE_CURRENT or PendingIntent.FLAG_IMMUTABLE
        )

        return NotificationCompat.Builder(this, CHANNEL_ID)
            .setContentTitle("Focus Timer: $taskTitle")
            .setContentText("Thời gian còn lại: $timeText")
            .setSmallIcon(R.drawable.ic_timer)
            .setContentIntent(pendingIntent)
            .setOngoing(true)
            .setPriority(NotificationCompat.PRIORITY_HIGH)
            .addAction(R.drawable.ic_pause, "Pause", pausePendingIntent)
            .setProgress(100, calculateProgress(), false)
            .build()
    }

    /**
     * 完了 Notification を作成
     */
    private fun buildCompletionNotification(taskTitle: String): Notification {
        val intent = Intent(this, FocusSessionActivity::class.java).apply {
            flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
        }
        val pendingIntent = PendingIntent.getActivity(
            this, 0, intent,
            PendingIntent.FLAG_UPDATE_CURRENT or PendingIntent.FLAG_IMMUTABLE
        )

        return NotificationCompat.Builder(this, CHANNEL_ID)
            .setContentTitle("Focus Session hoàn thành!")
            .setContentText("$taskTitle - Tuyệt vời! Bạn đã hoàn thành phiên làm việc.")
            .setSmallIcon(R.drawable.ic_check)
            .setContentIntent(pendingIntent)
            .setAutoCancel(true)
            .setPriority(NotificationCompat.PRIORITY_HIGH)
            .build()
    }

    /**
     * プログレスを計算 (0-100)
     */
    private fun calculateProgress(): Int {
        return ((totalTimeMillis - timeRemainingMillis) * 100 / totalTimeMillis).toInt()
    }

    /**
     * Notification Channel を作成
     */
    private fun createNotificationChannel() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            val channel = NotificationChannel(
                CHANNEL_ID,
                "Focus Timer",
                NotificationManager.IMPORTANCE_HIGH
            ).apply {
                description = "Focus Timer notifications"
                setShowBadge(true)
            }

            val notificationManager = getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
            notificationManager.createNotificationChannel(channel)
        }
    }

    override fun onDestroy() {
        super.onDestroy()
        countDownTimer?.cancel()
    }
}

