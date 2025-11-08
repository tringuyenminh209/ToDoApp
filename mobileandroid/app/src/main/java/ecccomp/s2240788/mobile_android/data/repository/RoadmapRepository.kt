package ecccomp.s2240788.mobile_android.data.repository

import ecccomp.s2240788.mobile_android.data.api.ApiService
import ecccomp.s2240788.mobile_android.data.models.*

/**
 * RoadmapRepository
 * Roadmap API呼び出しを担当
 */
class RoadmapRepository(
    private val apiService: ApiService
) {
    /**
     * 人気のロードマップを取得
     */
    suspend fun getPopularRoadmaps(): Result<List<PopularRoadmap>> {
        return try {
            val response = apiService.getPopularRoadmaps()

            if (response.isSuccessful) {
                val body = response.body()
                if (body != null && body.success) {
                    Result.success(body.data)
                } else {
                    Result.failure(Exception("ロードマップの取得に失敗しました"))
                }
            } else {
                val errorMessage = when (response.code()) {
                    401 -> "認証に失敗しました"
                    500 -> "サーバーエラーが発生しました"
                    else -> "ロードマップの取得に失敗しました: ${response.message()}"
                }
                Result.failure(Exception(errorMessage))
            }
        } catch (e: Exception) {
            Result.failure(Exception("ネットワークエラー: ${e.message}"))
        }
    }

    /**
     * AIでロードマップを生成
     */
    suspend fun generateRoadmap(
        topic: String,
        level: String = "beginner"
    ): Result<GeneratedRoadmap> {
        return try {
            val request = GenerateRoadmapRequest(topic, level)
            val response = apiService.generateRoadmap(request)

            if (response.isSuccessful) {
                val body = response.body()
                if (body != null && body.success) {
                    Result.success(body.data)
                } else {
                    Result.failure(Exception("ロードマップの生成に失敗しました"))
                }
            } else {
                val errorMessage = when (response.code()) {
                    400 -> "リクエストが無効です"
                    401 -> "認証に失敗しました"
                    500 -> "サーバーエラーが発生しました"
                    503 -> "AIサービスに接続できません"
                    else -> "ロードマップの生成に失敗しました: ${response.message()}"
                }
                Result.failure(Exception(errorMessage))
            }
        } catch (e: Exception) {
            Result.failure(Exception("ネットワークエラー: ${e.message}"))
        }
    }

    /**
     * ロードマップをインポートしてテンプレートとして保存（自動的に学習パスにクローン）
     */
    suspend fun importRoadmap(
        source: String,
        roadmapId: String? = null,
        topic: String? = null,
        level: String? = null,
        autoClone: Boolean = true
    ): Result<ImportRoadmapData> {
        return try {
            val request = ImportRoadmapRequest(
                source = source,
                roadmap_id = roadmapId,
                topic = topic,
                level = level,
                auto_clone = autoClone
            )
            val response = apiService.importRoadmap(request)

            if (response.isSuccessful) {
                val body = response.body()
                if (body != null && body.success) {
                    Result.success(body.data)
                } else {
                    Result.failure(Exception("ロードマップのインポートに失敗しました"))
                }
            } else {
                val errorMessage = when (response.code()) {
                    400 -> "リクエストが無効です"
                    401 -> "認証に失敗しました"
                    404 -> "ロードマップが見つかりません"
                    500 -> "サーバーエラーが発生しました"
                    else -> "ロードマップのインポートに失敗しました: ${response.message()}"
                }
                Result.failure(Exception(errorMessage))
            }
        } catch (e: Exception) {
            Result.failure(Exception("ネットワークエラー: ${e.message}"))
        }
    }
}

