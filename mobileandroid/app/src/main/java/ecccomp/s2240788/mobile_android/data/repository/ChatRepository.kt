package ecccomp.s2240788.mobile_android.data.repository

import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.*
import ecccomp.s2240788.mobile_android.data.result.ChatResult

/**
 * Chat Repository
 * Chat AI API呼び出しを担当
 * ViewModelとApiServiceの間のレイヤー
 */
class ChatRepository(
    private val apiService: ApiService
) {
    /**
     * 会話リストを取得
     */
    suspend fun getConversations(
        status: String? = null,
        sortBy: String = "last_message_at",
        sortOrder: String = "desc",
        perPage: Int = 20
    ): ChatResult<ChatConversationsResponse> {
        return try {
            val response = apiService.getChatConversations(status, sortBy, sortOrder, perPage)

            if (response.isSuccessful) {
                val data = response.body()?.data
                if (data != null) {
                    ChatResult.Success(data)
                } else {
                    ChatResult.Error("会話リストの取得に失敗しました")
                }
            } else {
                val errorMessage = when (response.code()) {
                    401 -> "認証に失敗しました"
                    403 -> "アクセスが拒否されました"
                    500 -> "サーバーエラーが発生しました"
                    else -> "会話リストの取得に失敗しました: ${response.message()}"
                }
                ChatResult.Error(errorMessage)
            }
        } catch (e: Exception) {
            ChatResult.Error("ネットワークエラー: ${e.message}")
        }
    }

    /**
     * 新しい会話を作成
     */
    suspend fun createConversation(
        message: String,
        title: String? = null
    ): ChatResult<CreateConversationResponse> {
        return try {
            val request = CreateConversationRequest(title, message)
            val response = apiService.createChatConversation(request)

            if (response.isSuccessful) {
                val body = response.body()
                val data = body?.data
                if (data != null) {
                    ChatResult.Success(data)
                } else {
                    // Log để debug
                    android.util.Log.e("ChatRepository", "Response body is null or data is null. Body: $body")
                    ChatResult.Error("会話の作成に失敗しました: レスポンスデータが無効です")
                }
            } else {
                val errorBody = response.errorBody()?.string()
                android.util.Log.e("ChatRepository", "API error: ${response.code()}, body: $errorBody")
                val errorMessage = when (response.code()) {
                    401 -> "認証に失敗しました"
                    422 -> "入力データが無効です: $errorBody"
                    429 -> "リクエストが多すぎます。しばらく待ってください"
                    500 -> "サーバーエラーが発生しました"
                    else -> "会話の作成に失敗しました: ${response.message()}"
                }
                ChatResult.Error(errorMessage)
            }
        } catch (e: Exception) {
            android.util.Log.e("ChatRepository", "Exception in createConversation", e)
            ChatResult.Error("ネットワークエラー: ${e.message}")
        }
    }

    /**
     * 特定の会話を取得（メッセージ履歴込み）
     */
    suspend fun getConversation(conversationId: Long): ChatResult<ChatConversation> {
        return try {
            val response = apiService.getChatConversation(conversationId)

            if (response.isSuccessful) {
                val data = response.body()?.data
                if (data != null) {
                    ChatResult.Success(data)
                } else {
                    ChatResult.Error("会話の取得に失敗しました")
                }
            } else {
                val errorMessage = when (response.code()) {
                    401 -> "認証に失敗しました"
                    403 -> "この会話にアクセスする権限がありません"
                    404 -> "会話が見つかりません"
                    500 -> "サーバーエラーが発生しました"
                    else -> "会話の取得に失敗しました: ${response.message()}"
                }
                ChatResult.Error(errorMessage)
            }
        } catch (e: Exception) {
            ChatResult.Error("ネットワークエラー: ${e.message}")
        }
    }

    /**
     * メッセージを送信
     */
    suspend fun sendMessage(
        conversationId: Long,
        message: String
    ): ChatResult<SendMessageResponse> {
        return try {
            val request = SendMessageRequest(message)
            val response = apiService.sendChatMessage(conversationId, request)

            if (response.isSuccessful) {
                val data = response.body()?.data
                if (data != null) {
                    ChatResult.Success(data)
                } else {
                    ChatResult.Error("メッセージの送信に失敗しました")
                }
            } else {
                val errorMessage = when (response.code()) {
                    400 -> "この会話は現在アクティブではありません"
                    401 -> "認証に失敗しました"
                    403 -> "この会話にアクセスする権限がありません"
                    404 -> "会話が見つかりません"
                    422 -> "メッセージが無効です"
                    429 -> "リクエストが多すぎます。しばらく待ってください"
                    500 -> "サーバーエラーが発生しました"
                    else -> "メッセージの送信に失敗しました: ${response.message()}"
                }
                ChatResult.Error(errorMessage)
            }
        } catch (e: Exception) {
            ChatResult.Error("ネットワークエラー: ${e.message}")
        }
    }

    /**
     * 会話を更新（タイトル、ステータス）
     */
    suspend fun updateConversation(
        conversationId: Long,
        title: String? = null,
        status: String? = null
    ): ChatResult<ChatConversation> {
        return try {
            val request = UpdateConversationRequest(title, status)
            val response = apiService.updateChatConversation(conversationId, request)

            if (response.isSuccessful) {
                val data = response.body()?.data
                if (data != null) {
                    ChatResult.Success(data)
                } else {
                    ChatResult.Error("会話の更新に失敗しました")
                }
            } else {
                val errorMessage = when (response.code()) {
                    401 -> "認証に失敗しました"
                    403 -> "この会話にアクセスする権限がありません"
                    404 -> "会話が見つかりません"
                    422 -> "入力データが無効です"
                    500 -> "サーバーエラーが発生しました"
                    else -> "会話の更新に失敗しました: ${response.message()}"
                }
                ChatResult.Error(errorMessage)
            }
        } catch (e: Exception) {
            ChatResult.Error("ネットワークエラー: ${e.message}")
        }
    }

    /**
     * 会話を削除
     */
    suspend fun deleteConversation(conversationId: Long): ChatResult<Unit> {
        return try {
            val response = apiService.deleteChatConversation(conversationId)

            if (response.isSuccessful) {
                ChatResult.Success(Unit)
            } else {
                val errorMessage = when (response.code()) {
                    401 -> "認証に失敗しました"
                    403 -> "この会話にアクセスする権限がありません"
                    404 -> "会話が見つかりません"
                    500 -> "サーバーエラーが発生しました"
                    else -> "会話の削除に失敗しました: ${response.message()}"
                }
                ChatResult.Error(errorMessage)
            }
        } catch (e: Exception) {
            ChatResult.Error("ネットワークエラー: ${e.message}")
        }
    }

    /**
     * メッセージを送信（Context-Aware版 - tasks + timetable含む）
     */
    suspend fun sendMessageWithContext(
        conversationId: Long,
        message: String
    ): ChatResult<SendMessageResponse> {
        return try {
            val request = SendMessageRequest(message)
            val response = apiService.sendChatMessageWithContext(conversationId, request)

            if (response.isSuccessful) {
                val data = response.body()?.data
                if (data != null) {
                    ChatResult.Success(data)
                } else {
                    ChatResult.Error("メッセージの送信に失敗しました")
                }
            } else {
                val errorMessage = when (response.code()) {
                    400 -> "この会話は現在アクティブではありません"
                    401 -> "認証に失敗しました"
                    403 -> "この会話にアクセスする権限がありません"
                    404 -> "会話が見つかりません"
                    422 -> "メッセージが無効です"
                    429 -> "リクエストが多すぎます。しばらく待ってください"
                    500 -> "サーバーエラーが発生しました"
                    503 -> "AIサービスに接続できません。しばらく待ってください"
                    else -> "メッセージの送信に失敗しました: ${response.message()}"
                }
                ChatResult.Error(errorMessage)
            }
        } catch (e: Exception) {
            ChatResult.Error("ネットワークエラー: ${e.message}")
        }
    }

    /**
     * タスク提案を確認して作成
     */
    suspend fun confirmTaskSuggestion(taskSuggestion: TaskSuggestion): ChatResult<Task> {
        return try {
            val response = apiService.confirmTaskSuggestion(taskSuggestion)

            if (response.isSuccessful) {
                val data = response.body()?.data
                if (data != null) {
                    ChatResult.Success(data)
                } else {
                    ChatResult.Error("タスクの作成に失敗しました")
                }
            } else {
                val errorMessage = when (response.code()) {
                    401 -> "認証に失敗しました"
                    422 -> "入力データが無効です"
                    500 -> "サーバーエラーが発生しました"
                    else -> "タスクの作成に失敗しました: ${response.message()}"
                }
                ChatResult.Error(errorMessage)
            }
        } catch (e: Exception) {
            ChatResult.Error("ネットワークエラー: ${e.message}")
        }
    }
}
