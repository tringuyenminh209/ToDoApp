# ✅ Task Management Implementation Checklist

## 📋 Progress Tracker

**Overall Progress:** 0/13 steps complete (0%)

---

## 🔷 PHASE 1: Core Components (Steps 1-3)

### ☐ Step 1: Create TaskViewModel.kt
**File:** `mobileandroid/app/src/main/java/ecccomp/s2240788/mobile_android/ui/viewmodels/TaskViewModel.kt`

**Subtasks:**
- [ ] Create file with package declaration
- [ ] Add fetchTasks() method with API integration
- [ ] Add completeTask(taskId) method
- [ ] Add startTask(taskId) method
- [ ] Add deleteTask(taskId) method
- [ ] Add applyFilter(filter) method
- [ ] Add LiveData properties (tasks, filteredTasks, isLoading, error)
- [ ] Add TaskFilter enum (ALL, PENDING, COMPLETED)
- [ ] Test fetchTasks() returns data from backend
- [ ] Verify error handling works

**Success Criteria:**
- ✅ File compiles without errors
- ✅ Can fetch tasks from API
- ✅ Can filter tasks by status
- ✅ Error states handled properly

**Time:** 1-2 hours
**Status:** ☐ Not Started | ⏳ In Progress | ✅ Complete

---

### ☐ Step 2: Create TaskAdapter.kt
**File:** `mobileandroid/app/src/main/java/ecccomp/s2240788/mobile_android/ui/adapters/TaskAdapter.kt`

**Subtasks:**
- [ ] Create TaskAdapter class extending ListAdapter
- [ ] Create TaskViewHolder inner class
- [ ] Implement onCreateViewHolder()
- [ ] Implement onBindViewHolder()
- [ ] Add TaskDiffCallback class
- [ ] Handle task click callback
- [ ] Handle complete button callback
- [ ] Handle delete button callback
- [ ] Display task status with styling
- [ ] Display priority with color indicators
- [ ] Display due date with formatting
- [ ] Show strike-through for completed tasks

**Success Criteria:**
- ✅ Adapter compiles without errors
- ✅ Tasks display correctly in list
- ✅ Click events work (click, complete, delete)
- ✅ Visual styling matches design

**Time:** 2-3 hours
**Status:** ☐ Not Started | ⏳ In Progress | ✅ Complete

---

### ☐ Step 3: Create item_task.xml
**File:** `mobileandroid/app/src/main/res/layout/item_task.xml`

**Subtasks:**
- [ ] Create MaterialCardView container
- [ ] Add priority indicator ImageView (iv_priority)
- [ ] Add task title TextView (tv_task_title)
- [ ] Add task description TextView (tv_task_description)
- [ ] Add status badge TextView (tv_status_badge)
- [ ] Add due date TextView (tv_due_date)
- [ ] Add complete button (btn_complete)
- [ ] Add delete button (btn_delete)
- [ ] Style with Material Design 3
- [ ] Add proper constraints and margins

**Success Criteria:**
- ✅ Layout renders correctly in preview
- ✅ All IDs match TaskAdapter code
- ✅ Responsive on different screen sizes
- ✅ Follows Material Design guidelines

**Time:** 1-2 hours
**Status:** ☐ Not Started | ⏳ In Progress | ✅ Complete

---

## 🔷 PHASE 2: MainActivity Update (Steps 4-5)

### ☐ Step 4: Update MainActivity.kt
**File:** `mobileandroid/app/src/main/java/ecccomp/s2240788/mobile_android/ui/activities/MainActivity.kt`

**Subtasks:**
- [ ] Add TaskViewModel initialization
- [ ] Setup RecyclerView with TaskAdapter
- [ ] Setup SwipeRefreshLayout
- [ ] Setup TabLayout for filters
- [ ] Setup FAB click listener (navigate to AddTaskActivity)
- [ ] Observe tasks LiveData and update adapter
- [ ] Observe isLoading and show/hide progress
- [ ] Observe error and show Toast
- [ ] Show empty state when no tasks
- [ ] Implement onResume() to refresh tasks
- [ ] Handle task click (navigate to EditTaskActivity)
- [ ] Handle complete task with confirmation
- [ ] Handle delete task with confirmation dialog

**Success Criteria:**
- ✅ Task list displays on screen
- ✅ Pull-to-refresh works
- ✅ Filters work (All/Pending/Completed)
- ✅ FAB navigates to AddTaskActivity
- ✅ Actions (complete/delete) work
- ✅ Empty state shows when no tasks
- ✅ Loading and error states work

**Time:** 2-3 hours
**Status:** ☐ Not Started | ⏳ In Progress | ✅ Complete

---

### ☐ Step 5: Update activity_main.xml
**File:** `mobileandroid/app/src/main/res/layout/activity_main.xml`

**Subtasks:**
- [ ] Add AppBarLayout with Toolbar
- [ ] Add TabLayout for filters (id: tab_layout)
- [ ] Add SwipeRefreshLayout (id: swipe_refresh)
- [ ] Add RecyclerView (id: rv_tasks)
- [ ] Add empty state LinearLayout (id: empty_state_view)
- [ ] Add FloatingActionButton (id: fab_add_task)
- [ ] Setup proper constraints and layouts
- [ ] Add CoordinatorLayout for FAB behavior
- [ ] Style with colors and dimensions

**Success Criteria:**
- ✅ Layout renders correctly
- ✅ All IDs match MainActivity code
- ✅ FAB positioned correctly
- ✅ Tabs positioned in AppBar
- ✅ Empty state centered

**Time:** 1 hour
**Status:** ☐ Not Started | ⏳ In Progress | ✅ Complete

---

## 🔷 PHASE 3: Add Task Feature (Steps 6-8)

### ☐ Step 6: Update AddTaskActivity.java
**File:** `mobileandroid/app/src/main/java/ecccomp/s2240788/mobile_android/ui/activities/AddTaskActivity.java`

**Subtasks:**
- [ ] Add ViewBinding setup
- [ ] Add AddTaskViewModel initialization
- [ ] Setup Toolbar with back button
- [ ] Setup priority Spinner with adapter
- [ ] Setup due date picker button
- [ ] Setup clear date button
- [ ] Setup save button click listener
- [ ] Implement validateInputs() method
- [ ] Implement createTask() method
- [ ] Observe ViewModel LiveData (isLoading, error, success)
- [ ] Show/hide progress bar during save
- [ ] Handle success (show Toast, finish activity)
- [ ] Handle errors (show Toast)

**Success Criteria:**
- ✅ Form displays correctly
- ✅ Date picker works
- ✅ Priority selection works
- ✅ Validation works (title required)
- ✅ Save creates task successfully
- ✅ Returns to MainActivity after save
- ✅ New task appears in list

**Time:** 2-3 hours
**Status:** ☐ Not Started | ⏳ In Progress | ✅ Complete

---

### ☐ Step 7: Create AddTaskViewModel.kt
**File:** `mobileandroid/app/src/main/java/ecccomp/s2240788/mobile_android/ui/viewmodels/AddTaskViewModel.kt`

**Subtasks:**
- [ ] Create file with package declaration
- [ ] Add ApiService initialization
- [ ] Add LiveData properties (isLoading, error, taskCreated)
- [ ] Implement createTask() method
- [ ] Validate inputs (title required)
- [ ] Create CreateTaskRequest object
- [ ] Call apiService.createTask()
- [ ] Handle success response
- [ ] Handle error responses (422, 500)
- [ ] Handle network exceptions
- [ ] Add clearError() method

**Success Criteria:**
- ✅ File compiles without errors
- ✅ Can create task via API
- ✅ Validation works
- ✅ Error handling works
- ✅ Success state triggers

**Time:** 1 hour
**Status:** ☐ Not Started | ⏳ In Progress | ✅ Complete

---

### ☐ Step 8: Verify AddTask Layout
**File:** `mobileandroid/app/src/main/res/layout/activity_add_task.xml`

**Subtasks:**
- [ ] Verify layout exists and renders
- [ ] Check Toolbar (id: toolbar)
- [ ] Check Title input (id: et_title, til_title)
- [ ] Check Description input (id: et_description)
- [ ] Check Priority Spinner (id: spinner_priority)
- [ ] Check Due Date button (id: btn_select_due_date)
- [ ] Check Selected Date text (id: tv_selected_date)
- [ ] Check Clear Date button (id: btn_clear_due_date)
- [ ] Check Save button (id: btn_save_task)
- [ ] Check ProgressBar (id: progress_bar)
- [ ] Update IDs if mismatched

**Success Criteria:**
- ✅ Layout renders in preview
- ✅ All IDs match Java code
- ✅ Form fields are accessible
- ✅ Proper styling applied

**Time:** 30 minutes
**Status:** ☐ Not Started | ⏳ In Progress | ✅ Complete

---

## 🔷 PHASE 4: Edit Task Feature (Steps 9-10)

### ☐ Step 9: Update EditTaskActivity.kt
**File:** `mobileandroid/app/src/main/java/ecccomp/s2240788/mobile_android/ui/activities/EditTaskActivity.kt`

**Subtasks:**
- [ ] Replace skeleton with full implementation
- [ ] Add ViewBinding setup
- [ ] Add EditTaskViewModel initialization
- [ ] Get task_id from Intent extras
- [ ] Call viewModel.loadTask(taskId)
- [ ] Setup Toolbar with back button
- [ ] Setup priority Spinner
- [ ] Setup due date picker
- [ ] Setup save button
- [ ] Observe task LiveData and populate form
- [ ] Observe isLoading state
- [ ] Observe error state
- [ ] Observe taskUpdated success
- [ ] Implement validateInputs()
- [ ] Implement updateTask()
- [ ] Handle success (Toast, finish)

**Success Criteria:**
- ✅ Loads existing task data
- ✅ Form pre-fills correctly
- ✅ Date picker works
- ✅ Priority selection works
- ✅ Save updates task successfully
- ✅ Returns to MainActivity
- ✅ Updated task shows in list

**Time:** 2-3 hours
**Status:** ☐ Not Started | ⏳ In Progress | ✅ Complete

---

### ☐ Step 10: Create EditTaskViewModel.kt
**File:** `mobileandroid/app/src/main/java/ecccomp/s2240788/mobile_android/ui/viewmodels/EditTaskViewModel.kt`

**Subtasks:**
- [ ] Create file with package declaration
- [ ] Add ApiService initialization
- [ ] Add LiveData properties (task, isLoading, error, taskUpdated)
- [ ] Implement loadTask(taskId) method
- [ ] Implement updateTask() method
- [ ] Validate inputs
- [ ] Create CreateTaskRequest with updated data
- [ ] Call apiService.updateTask()
- [ ] Handle success response
- [ ] Handle error responses (404, 422, 500)
- [ ] Handle network exceptions
- [ ] Add clearError() method

**Success Criteria:**
- ✅ File compiles without errors
- ✅ Can load task from API
- ✅ Can update task via API
- ✅ Validation works
- ✅ Error handling works

**Time:** 1 hour
**Status:** ☐ Not Started | ⏳ In Progress | ✅ Complete

---

## 🔷 PHASE 5: Resources & Polish (Steps 11-13)

### ☐ Step 11: Add Drawables
**Files:** Multiple in `res/drawable/`

**Subtasks:**
- [ ] Create badge_completed.xml (green rounded)
- [ ] Create badge_in_progress.xml (blue rounded)
- [ ] Verify ic_priority.xml exists
- [ ] Verify ic_calendar.xml exists
- [ ] Verify ic_check.xml exists
- [ ] Verify ic_delete.xml exists
- [ ] Verify ic_add.xml exists
- [ ] Verify ic_task.xml exists (for empty state)
- [ ] If missing, create or download from Material Icons

**Success Criteria:**
- ✅ All drawables render in preview
- ✅ Badges have proper colors
- ✅ Icons are visible in layouts

**Time:** 1 hour
**Status:** ☐ Not Started | ⏳ In Progress | ✅ Complete

---

### ☐ Step 12: Add Colors
**File:** `mobileandroid/app/src/main/res/values/colors.xml`

**Subtasks:**
- [ ] Add priority_high (#F44336 - Red)
- [ ] Add priority_medium (#FF9800 - Orange)
- [ ] Add priority_low (#4CAF50 - Green)
- [ ] Add success (#4CAF50 - Green)
- [ ] Add stroke_light (#E0E0E0 - Light gray)
- [ ] Add background (#F5F5F5 - Light gray)
- [ ] Verify primary, error, text_primary, text_secondary exist

**Success Criteria:**
- ✅ All colors defined
- ✅ Colors used in layouts render correctly

**Time:** 15 minutes
**Status:** ☐ Not Started | ⏳ In Progress | ✅ Complete

---

### ☐ Step 13: Add Strings
**File:** `mobileandroid/app/src/main/res/values/strings.xml`

**Subtasks:**
- [ ] Add add_task string
- [ ] Add edit_task string
- [ ] Add complete_task string
- [ ] Add delete_task string
- [ ] Add no_tasks string
- [ ] Add priority_indicator string
- [ ] Add task_title string
- [ ] Add task_description string
- [ ] Add priority string
- [ ] Add due_date string
- [ ] Add select_due_date string
- [ ] Add save_task string

**Success Criteria:**
- ✅ All strings defined
- ✅ Japanese translations provided
- ✅ Strings used in code/layouts

**Time:** 15 minutes
**Status:** ☐ Not Started | ⏳ In Progress | ✅ Complete

---

## 🧪 FINAL TESTING

### ☐ Integration Testing
- [ ] Full create → view → edit → complete → delete flow works
- [ ] All filters work (All/Pending/Completed)
- [ ] Pull-to-refresh updates list
- [ ] Empty state shows/hides correctly
- [ ] Loading states work throughout
- [ ] Error handling works for all operations
- [ ] Task with priority shows correct color
- [ ] Task with due date shows correctly
- [ ] Overdue tasks show red date
- [ ] Completed tasks show strike-through

### ☐ UI/UX Testing
- [ ] No UI glitches or overlapping elements
- [ ] Smooth animations and transitions
- [ ] Back button works correctly everywhere
- [ ] No crashes or ANRs
- [ ] Works on different screen sizes
- [ ] Works on emulator
- [ ] Works on real device

### ☐ Backend Integration
- [ ] All API calls succeed with valid data
- [ ] Error responses handled correctly
- [ ] Network timeout handled
- [ ] Backend unavailable handled
- [ ] Token expiration handled (401 auto-logout)

**Time:** 2-3 hours
**Status:** ☐ Not Started | ⏳ In Progress | ✅ Complete

---

## 📊 Progress Summary

**Phase 1 (Core):** 0/3 steps ☐☐☐
**Phase 2 (MainActivity):** 0/2 steps ☐☐
**Phase 3 (Add Task):** 0/3 steps ☐☐☐
**Phase 4 (Edit Task):** 0/2 steps ☐☐
**Phase 5 (Resources):** 0/3 steps ☐☐☐

**Total:** 0/13 steps (0%)

---

## 🎯 Quick Status Check

Run this checklist after each coding session:

**Daily Standup:**
- What did I complete today?
- What am I working on next?
- Any blockers?

**Before Committing:**
- [ ] Code compiles without errors
- [ ] No lint warnings
- [ ] Tested on device/emulator
- [ ] All new features work
- [ ] No regressions

**Before Declaring Complete:**
- [ ] All 13 steps checked
- [ ] All testing complete
- [ ] No known bugs
- [ ] Documentation updated
- [ ] Ready for user testing

---

## 📝 Notes Section

**Issues Encountered:**
- (Add any issues here as you work)

**Deviations from Plan:**
- (Note any changes to the original design)

**Additional Work Needed:**
- (List any follow-up tasks)

**Completed:** __ / __ / ____
**By:** _______________
