package ecccomp.s2240788.mobile_android.utils

import android.Manifest
import android.app.Activity
import android.content.Intent
import android.content.pm.PackageManager
import android.os.Bundle
import android.speech.RecognitionListener
import android.speech.RecognizerIntent
import android.speech.SpeechRecognizer
import androidx.core.app.ActivityCompat
import androidx.core.content.ContextCompat

/**
 * Helper class for Speech Recognition (Voice Input)
 * Phase 1: Basic speech-to-text without metadata
 */
class SpeechRecognitionHelper(private val activity: Activity) {

    private var speechRecognizer: SpeechRecognizer? = null
    private var onResultCallback: ((String) -> Unit)? = null
    private var onErrorCallback: ((String) -> Unit)? = null
    private var onReadyCallback: (() -> Unit)? = null
    private var onListeningCallback: (() -> Unit)? = null

    companion object {
        const val REQUEST_RECORD_AUDIO = 1001
    }

    /**
     * Check if device supports speech recognition
     */
    fun isSpeechRecognitionAvailable(): Boolean {
        return SpeechRecognizer.isRecognitionAvailable(activity)
    }

    /**
     * Check if RECORD_AUDIO permission is granted
     */
    fun hasRecordAudioPermission(): Boolean {
        return ContextCompat.checkSelfPermission(
            activity,
            Manifest.permission.RECORD_AUDIO
        ) == PackageManager.PERMISSION_GRANTED
    }

    /**
     * Request RECORD_AUDIO permission
     */
    fun requestRecordAudioPermission() {
        ActivityCompat.requestPermissions(
            activity,
            arrayOf(Manifest.permission.RECORD_AUDIO),
            REQUEST_RECORD_AUDIO
        )
    }

    /**
     * Start listening for voice input
     *
     * @param language Language code (e.g., "ja-JP", "en-US", "vi-VN")
     * @param onReady Called when recognition is ready
     * @param onListening Called when user can start speaking
     * @param onResult Called when speech is recognized (returns transcribed text)
     * @param onError Called when error occurs (returns error message)
     */
    fun startListening(
        language: String = "ja-JP",
        onReady: (() -> Unit)? = null,
        onListening: (() -> Unit)? = null,
        onResult: (text: String) -> Unit,
        onError: (error: String) -> Unit
    ) {
        // Check if speech recognition is available
        if (!isSpeechRecognitionAvailable()) {
            onError("音声認識がこのデバイスで利用できません")
            return
        }

        // Check permission
        if (!hasRecordAudioPermission()) {
            requestRecordAudioPermission()
            onError("録音権限が必要です")
            return
        }

        // Save callbacks
        onReadyCallback = onReady
        onListeningCallback = onListening
        onResultCallback = onResult
        onErrorCallback = onError

        // Create recognizer if needed
        if (speechRecognizer == null) {
            speechRecognizer = SpeechRecognizer.createSpeechRecognizer(activity)
            speechRecognizer?.setRecognitionListener(recognitionListener)
        }

        // Create intent
        val intent = Intent(RecognizerIntent.ACTION_RECOGNIZE_SPEECH).apply {
            putExtra(
                RecognizerIntent.EXTRA_LANGUAGE_MODEL,
                RecognizerIntent.LANGUAGE_MODEL_FREE_FORM
            )
            putExtra(RecognizerIntent.EXTRA_LANGUAGE, language)
            putExtra(RecognizerIntent.EXTRA_PARTIAL_RESULTS, true)
            putExtra(RecognizerIntent.EXTRA_MAX_RESULTS, 1)
        }

        // Start listening
        try {
            speechRecognizer?.startListening(intent)
        } catch (e: Exception) {
            onError("音声認識の開始に失敗しました: ${e.message}")
        }
    }

    /**
     * Stop listening
     */
    fun stopListening() {
        try {
            speechRecognizer?.stopListening()
        } catch (e: Exception) {
            // Ignore errors when stopping
        }
    }

    /**
     * Cancel listening
     */
    fun cancel() {
        try {
            speechRecognizer?.cancel()
        } catch (e: Exception) {
            // Ignore errors when canceling
        }
    }

    /**
     * Destroy speech recognizer (call in onDestroy)
     */
    fun destroy() {
        try {
            speechRecognizer?.destroy()
            speechRecognizer = null
        } catch (e: Exception) {
            // Ignore errors when destroying
        }
        onResultCallback = null
        onErrorCallback = null
        onReadyCallback = null
        onListeningCallback = null
    }

    /**
     * Recognition listener for handling speech recognition events
     */
    private val recognitionListener = object : RecognitionListener {
        override fun onReadyForSpeech(params: Bundle?) {
            // Called when recognizer is ready
            onReadyCallback?.invoke()
        }

        override fun onBeginningOfSpeech() {
            // Called when user starts speaking
            onListeningCallback?.invoke()
        }

        override fun onRmsChanged(rmsdB: Float) {
            // Called when sound level changes (can be used for volume indicator)
            // Not used in Phase 1
        }

        override fun onBufferReceived(buffer: ByteArray?) {
            // Called when buffer is received
            // Not used in Phase 1
        }

        override fun onEndOfSpeech() {
            // Called when user stops speaking
            // Recognition result will come in onResults
        }

        override fun onError(error: Int) {
            val errorMessage = when (error) {
                SpeechRecognizer.ERROR_AUDIO -> "音声入力エラー"
                SpeechRecognizer.ERROR_CLIENT -> "クライアントエラー"
                SpeechRecognizer.ERROR_INSUFFICIENT_PERMISSIONS -> "録音権限がありません"
                SpeechRecognizer.ERROR_NETWORK -> "ネットワークエラー。インターネット接続を確認してください"
                SpeechRecognizer.ERROR_NETWORK_TIMEOUT -> "ネットワークタイムアウト"
                SpeechRecognizer.ERROR_NO_MATCH -> "音声が認識できませんでした。もう一度お試しください"
                SpeechRecognizer.ERROR_RECOGNIZER_BUSY -> "音声認識が利用できません"
                SpeechRecognizer.ERROR_SERVER -> "サーバーエラー"
                SpeechRecognizer.ERROR_SPEECH_TIMEOUT -> "音声入力がタイムアウトしました"
                else -> "音声認識エラー (コード: $error)"
            }
            onErrorCallback?.invoke(errorMessage)
        }

        override fun onResults(results: Bundle?) {
            // Get recognized text
            val matches = results?.getStringArrayList(SpeechRecognizer.RESULTS_RECOGNITION)

            if (!matches.isNullOrEmpty()) {
                val recognizedText = matches[0]
                onResultCallback?.invoke(recognizedText)
            } else {
                onErrorCallback?.invoke("音声が認識できませんでした")
            }
        }

        override fun onPartialResults(partialResults: Bundle?) {
            // Called when partial results are available
            // Can be used to show real-time transcription
            // Not used in Phase 1 (can be added in Phase 3)
        }

        override fun onEvent(eventType: Int, params: Bundle?) {
            // Called for various events
            // Not used in Phase 1
        }
    }
}
