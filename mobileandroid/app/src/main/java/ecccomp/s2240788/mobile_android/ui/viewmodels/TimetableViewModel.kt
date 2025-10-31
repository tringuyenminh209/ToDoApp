package ecccomp.s2240788.mobile_android.ui.viewmodels

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import ecccomp.s2240788.mobile_android.data.models.*
import ecccomp.s2240788.mobile_android.data.repository.TimetableRepository

/**
 * ViewModel for Timetable Screen
 */
class TimetableViewModel : ViewModel() {
    
    // Timetable data
    private val _timetable = MutableLiveData<Timetable>()
    val timetable: LiveData<Timetable> = _timetable
    
    // Current class
    private val _currentClass = MutableLiveData<ClassModel?>()
    val currentClass: LiveData<ClassModel?> = _currentClass
    
    // Next class
    private val _nextClass = MutableLiveData<ClassModel?>()
    val nextClass: LiveData<ClassModel?> = _nextClass
    
    // Current status
    private val _currentStatus = MutableLiveData<TimetableStatus>()
    val currentStatus: LiveData<TimetableStatus> = _currentStatus
    
    // Study list
    private val _studies = MutableLiveData<List<StudyModel>>()
    val studies: LiveData<List<StudyModel>> = _studies
    
    // Loading state
    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading
    
    init {
        loadTimetable()
        updateCurrentStatus()
    }
    
    /**
     * Load timetable data
     */
    fun loadTimetable() {
        _isLoading.value = true
        
        // Get sample data from repository
        val timetableData = TimetableRepository.getSampleTimetable()
        _timetable.value = timetableData
        _studies.value = timetableData.studies.filter { !it.completed }
        
        _isLoading.value = false
    }
    
    /**
     * Update current class status
     */
    fun updateCurrentStatus() {
        val current = TimetableRepository.getCurrentClass()
        val next = TimetableRepository.getNextClass()
        
        _currentClass.value = current
        _nextClass.value = next
        
        // Determine status
        _currentStatus.value = when {
            current != null -> TimetableStatus.ACTIVE
            next != null -> TimetableStatus.NEXT
            else -> TimetableStatus.BREAK
        }
    }
    
    /**
     * Get class by day and period
     */
    fun getClassByDayAndPeriod(day: Int, period: Int): ClassModel? {
        return _timetable.value?.classes?.find { it.day == day && it.period == period }
    }
    
    /**
     * Add new class
     */
    fun addClass(classModel: ClassModel) {
        val currentClasses = _timetable.value?.classes?.toMutableList() ?: mutableListOf()
        currentClasses.add(classModel)
        _timetable.value = _timetable.value?.copy(classes = currentClasses)
    }
    
    /**
     * Update class
     */
    fun updateClass(classModel: ClassModel) {
        val currentClasses = _timetable.value?.classes?.toMutableList() ?: return
        val index = currentClasses.indexOfFirst { it.id == classModel.id }
        if (index != -1) {
            currentClasses[index] = classModel
            _timetable.value = _timetable.value?.copy(classes = currentClasses)
        }
    }
    
    /**
     * Delete class
     */
    fun deleteClass(classId: String) {
        val currentClasses = _timetable.value?.classes?.toMutableList() ?: return
        currentClasses.removeAll { it.id == classId }
        _timetable.value = _timetable.value?.copy(classes = currentClasses)
    }
    
    /**
     * Add study
     */
    fun addStudy(study: StudyModel) {
        val currentStudies = _studies.value?.toMutableList() ?: mutableListOf()
        currentStudies.add(0, study) // Add to top
        _studies.value = currentStudies
        
        // Also update timetable
        val allStudies = _timetable.value?.studies?.toMutableList() ?: mutableListOf()
        allStudies.add(study)
        _timetable.value = _timetable.value?.copy(studies = allStudies)
    }
    
    /**
     * Update study
     */
    fun updateStudy(study: StudyModel) {
        val currentStudies = _studies.value?.toMutableList() ?: return
        val index = currentStudies.indexOfFirst { it.id == study.id }
        if (index != -1) {
            currentStudies[index] = study
            _studies.value = currentStudies
        }
    }
    
    /**
     * Delete study
     */
    fun deleteStudy(studyId: String) {
        val currentStudies = _studies.value?.toMutableList() ?: return
        currentStudies.removeAll { it.id == studyId }
        _studies.value = currentStudies
    }
    
    /**
     * Toggle study completion
     */
    fun toggleStudyCompletion(studyId: String) {
        val currentStudies = _studies.value?.toMutableList() ?: return
        val index = currentStudies.indexOfFirst { it.id == studyId }
        if (index != -1) {
            val study = currentStudies[index]
            currentStudies[index] = study.copy(completed = !study.completed)
            _studies.value = currentStudies
        }
    }
    
    /**
     * Get remaining time for current class (in minutes)
     */
    fun getRemainingTime(): Int {
        val current = _currentClass.value ?: return 0
        
        val calendar = java.util.Calendar.getInstance()
        val currentHour = calendar.get(java.util.Calendar.HOUR_OF_DAY)
        val currentMinute = calendar.get(java.util.Calendar.MINUTE)
        val currentTime = currentHour * 60 + currentMinute
        
        val classTimes = listOf(
            8 * 60 + 50,  // Period 1 end
            9 * 60 + 50,  // Period 2 end
            10 * 60 + 50, // Period 3 end
            11 * 60 + 50, // Period 4 end
            13 * 60 + 50  // Period 5 end
        )
        
        val endTime = classTimes.getOrNull(current.period - 1) ?: return 0
        return maxOf(0, endTime - currentTime)
    }
    
    /**
     * Get time until next class (in minutes)
     */
    fun getTimeUntilNextClass(): Int {
        val next = _nextClass.value ?: return 0
        
        val calendar = java.util.Calendar.getInstance()
        val currentHour = calendar.get(java.util.Calendar.HOUR_OF_DAY)
        val currentMinute = calendar.get(java.util.Calendar.MINUTE)
        val currentTime = currentHour * 60 + currentMinute
        
        val classTimes = listOf(
            8 * 60,  // Period 1 start
            9 * 60,  // Period 2 start
            10 * 60, // Period 3 start
            11 * 60, // Period 4 start
            13 * 60  // Period 5 start
        )
        
        val startTime = classTimes.getOrNull(next.period - 1) ?: return 0
        return maxOf(0, startTime - currentTime)
    }
}

/**
 * Enum for timetable status
 */
enum class TimetableStatus {
    ACTIVE,  // Currently in a class
    NEXT,    // Next class coming up
    BREAK    // Break time / No class
}
