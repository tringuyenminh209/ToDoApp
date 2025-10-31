package ecccomp.s2240788.mobile_android.data.repository

import ecccomp.s2240788.mobile_android.data.models.*

/**
 * Repository for Timetable data
 * Provides sample data for demonstration
 */
object TimetableRepository {
    
    /**
     * Get sample timetable data
     * Based on the HTML demo timetable
     */
    fun getSampleTimetable(): Timetable {
        return Timetable(
            classes = getSampleClasses(),
            studies = getSampleStudies()
        )
    }
    
    /**
     * Sample classes data
     */
    private fun getSampleClasses(): List<ClassModel> {
        return listOf(
            // Monday (day 1)
            ClassModel(
                id = "1-1",
                name = "IT就職作品開発",
                room = "1603",
                day = 1,
                period = 1,
                color = ClassColor.PRIMARY,
                teacher = "山田先生"
            ),
            ClassModel(
                id = "1-2",
                name = "IT就職作品開発",
                room = "1603",
                day = 1,
                period = 2,
                color = ClassColor.PRIMARY,
                teacher = "山田先生"
            ),
            ClassModel(
                id = "1-3",
                name = "AWSクラウド演習I",
                room = "3501",
                day = 1,
                period = 3,
                color = ClassColor.ACCENT,
                teacher = "佐藤先生"
            ),
            ClassModel(
                id = "1-4",
                name = "AWSクラウド演習I",
                room = "3501",
                day = 1,
                period = 4,
                color = ClassColor.ACCENT,
                teacher = "佐藤先生"
            ),
            
            // Tuesday (day 2)
            ClassModel(
                id = "2-1",
                name = "モバイルアプリ開発演習",
                room = "1604",
                day = 2,
                period = 1,
                color = ClassColor.SUCCESS,
                teacher = "鈴木先生"
            ),
            ClassModel(
                id = "2-2",
                name = "モバイルアプリ開発演習",
                room = "1604",
                day = 2,
                period = 2,
                color = ClassColor.SUCCESS,
                teacher = "鈴木先生"
            ),
            ClassModel(
                id = "2-3",
                name = "AI活用演習",
                room = "1604",
                day = 2,
                period = 3,
                color = ClassColor.WARNING,
                teacher = "田中先生"
            ),
            ClassModel(
                id = "2-4",
                name = "AI活用演習",
                room = "1604",
                day = 2,
                period = 4,
                color = ClassColor.WARNING,
                teacher = "田中先生"
            ),
            
            // Wednesday (day 3)
            ClassModel(
                id = "3-1",
                name = "IoT制作演習I",
                room = "3501",
                day = 3,
                period = 1,
                color = ClassColor.DANGER,
                teacher = "伊藤先生"
            ),
            ClassModel(
                id = "3-2",
                name = "IoT制作演習I",
                room = "3501",
                day = 3,
                period = 2,
                color = ClassColor.DANGER,
                teacher = "伊藤先生"
            ),
            
            // Thursday (day 4)
            ClassModel(
                id = "4-1",
                name = "外国語IV(英語中級)",
                room = "1302",
                day = 4,
                period = 1,
                color = ClassColor.PRIMARY,
                teacher = "Johnson先生"
            ),
            ClassModel(
                id = "4-2",
                name = "就職対策I",
                room = "3303",
                day = 4,
                period = 2,
                color = ClassColor.ACCENT,
                teacher = "高橋先生"
            ),
            ClassModel(
                id = "4-3",
                name = "IT就職作品開発",
                room = "1603",
                day = 4,
                period = 3,
                color = ClassColor.PRIMARY,
                teacher = "山田先生"
            ),
            ClassModel(
                id = "4-4",
                name = "IT就職作品開発",
                room = "1603",
                day = 4,
                period = 4,
                color = ClassColor.PRIMARY,
                teacher = "山田先生"
            ),
            
            // Friday (day 5)
            ClassModel(
                id = "5-3",
                name = "デスクトップアプリ開発",
                room = "2301",
                day = 5,
                period = 3,
                color = ClassColor.SUCCESS,
                teacher = "小林先生"
            ),
            ClassModel(
                id = "5-4",
                name = "デスクトップアプリ開発",
                room = "2301",
                day = 5,
                period = 4,
                color = ClassColor.SUCCESS,
                teacher = "小林先生"
            )
        )
    }
    
    /**
     * Sample study tasks
     */
    private fun getSampleStudies(): List<StudyModel> {
        return listOf(
            StudyModel(
                id = "study-1",
                title = "Java OOP プログラミング課題",
                type = StudyType.HOMEWORK,
                subject = "IT就職作品開発",
                dueDate = "2025-11-05",
                priority = Priority.HIGH,
                description = "クラスの継承とポリモーフィズムを使った実装",
                completed = false
            ),
            StudyModel(
                id = "study-2",
                title = "AWS EC2インスタンス設定復習",
                type = StudyType.REVIEW,
                subject = "AWSクラウド演習I",
                dueDate = "2025-11-03",
                priority = Priority.MEDIUM,
                description = "セキュリティグループとIAMロールの設定を復習",
                completed = false
            ),
            StudyModel(
                id = "study-3",
                title = "機械学習中間テスト",
                type = StudyType.EXAM,
                subject = "AI活用演習",
                dueDate = "2025-11-08",
                priority = Priority.HIGH,
                description = "教師あり学習と教師なし学習の範囲",
                completed = false
            ),
            StudyModel(
                id = "study-4",
                title = "Kotlinコルーチン練習",
                type = StudyType.HOMEWORK,
                subject = "モバイルアプリ開発演習",
                dueDate = "2025-11-04",
                priority = Priority.MEDIUM,
                description = "非同期処理の実装練習",
                completed = true
            ),
            StudyModel(
                id = "study-5",
                title = "英語プレゼンテーション準備",
                type = StudyType.HOMEWORK,
                subject = "外国語IV(英語中級)",
                dueDate = "2025-11-06",
                priority = Priority.HIGH,
                description = "技術トピックについて5分間のプレゼン",
                completed = false
            )
        )
    }
    
    /**
     * Get classes for specific day and period
     */
    fun getClassByDayAndPeriod(day: Int, period: Int): ClassModel? {
        return getSampleClasses().find { it.day == day && it.period == period }
    }
    
    /**
     * Get all classes for a specific day
     */
    fun getClassesByDay(day: Int): List<ClassModel> {
        return getSampleClasses().filter { it.day == day }
    }
    
    /**
     * Get current or next class based on current time
     */
    fun getCurrentClass(): ClassModel? {
        val calendar = java.util.Calendar.getInstance()
        val currentDay = calendar.get(java.util.Calendar.DAY_OF_WEEK) - 1 // 0-6
        val currentHour = calendar.get(java.util.Calendar.HOUR_OF_DAY)
        val currentMinute = calendar.get(java.util.Calendar.MINUTE)
        val currentTime = currentHour * 60 + currentMinute
        
        // Class times (8:00 start, 50 min class, 10 min break)
        val classTimes = listOf(
            8 * 60 to 8 * 60 + 50,      // Period 1: 8:00-8:50
            9 * 60 to 9 * 60 + 50,      // Period 2: 9:00-9:50
            10 * 60 to 10 * 60 + 50,    // Period 3: 10:00-10:50
            11 * 60 to 11 * 60 + 50,    // Period 4: 11:00-11:50
            13 * 60 to 13 * 60 + 50     // Period 5: 13:00-13:50
        )
        
        // Find current class
        for (i in classTimes.indices) {
            val (start, end) = classTimes[i]
            if (currentTime in start..end) {
                return getClassByDayAndPeriod(currentDay, i + 1)
            }
        }
        
        return null
    }
    
    /**
     * Get next class
     */
    fun getNextClass(): ClassModel? {
        val calendar = java.util.Calendar.getInstance()
        val currentDay = calendar.get(java.util.Calendar.DAY_OF_WEEK) - 1
        val currentHour = calendar.get(java.util.Calendar.HOUR_OF_DAY)
        val currentMinute = calendar.get(java.util.Calendar.MINUTE)
        val currentTime = currentHour * 60 + currentMinute
        
        val classTimes = listOf(
            8 * 60 to 8 * 60 + 50,
            9 * 60 to 9 * 60 + 50,
            10 * 60 to 10 * 60 + 50,
            11 * 60 to 11 * 60 + 50,
            13 * 60 to 13 * 60 + 50
        )
        
        // Find next class today
        for (i in classTimes.indices) {
            val (start, _) = classTimes[i]
            if (currentTime < start) {
                val nextClass = getClassByDayAndPeriod(currentDay, i + 1)
                if (nextClass != null) return nextClass
            }
        }
        
        // If no class found today, return first class tomorrow
        var nextDay = (currentDay + 1) % 7
        var daysChecked = 0
        while (daysChecked < 7) {
            val classes = getClassesByDay(nextDay)
            if (classes.isNotEmpty()) {
                return classes.minByOrNull { it.period }
            }
            nextDay = (nextDay + 1) % 7
            daysChecked++
        }
        
        return null
    }
    
    /**
     * Get incomplete studies
     */
    fun getIncompleteStudies(): List<StudyModel> {
        return getSampleStudies().filter { !it.completed }
    }
    
    /**
     * Get studies by priority
     */
    fun getStudiesByPriority(priority: Priority): List<StudyModel> {
        return getSampleStudies().filter { it.priority == priority && !it.completed }
    }
}
