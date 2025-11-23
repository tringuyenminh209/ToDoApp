# Android App Design Guidelines
**Kizamu - AI-Powered Todo Application**

---

## 1. COLOR PALETTE

### Primary Colors (Jade Green)
- **Primary**: `#0FA968` - Main brand color for CTAs, highlights, and primary actions
- **Primary Hover**: `#0B8C57` - Interactive state
- **Primary Press**: `#09764B` - Pressed state
- **Primary Light**: `#E6F8F1` - Light background for badges, highlights

### Accent Colors (Slate Blue)
- **Accent**: `#475569` - Secondary text, icons
- **Accent Hover**: `#1D4ED8` - Interactive hover state
- **Accent Light**: `#EEF5FF` - Light backgrounds

### Status Colors
- **Success**: `#22C55E` (Light: `#D1FAE5`) - Completion, positive states
- **Warning**: `#F59E0B` (Light: `#FEF3C7`) - Caution, alerts
- **Error**: `#DC2626` (Light: `#FEE2E2`) - Errors, destructive actions
- **Info**: `#1F6FEB` (Light: `#DBEAFE`) - Information, next review dates

### Text Colors
- **Primary**: `#0B1220` - Main text, headings
- **Secondary**: `#475569` - Secondary content
- **Tertiary**: `#64748B` - Body text
- **Muted**: `#94A3B8` - Disabled, placeholder text

### Surface Colors
- **Background**: `#F8FAFC` - Main screen background
- **Surface**: `#F8F9FA` - Card backgrounds
- **Surface Variant**: `#E9ECEF` - Secondary surface
- **Line**: `#E2E8F0` - Borders and dividers
- **Line Variant**: `#CBD5E1` - Subtle borders

---

## 2. TYPOGRAPHY

### Font Scaling
All text uses Android system fonts (Roboto by default via Material Design 3).

### Text Sizes & Usage
| Size | Usage | Weight |
|------|-------|--------|
| **32sp** | Large headings, progress percentages | Bold (700) |
| **26sp** | Task detail title | Bold (700) |
| **20sp** | Activity titles, section headers | Bold (700) |
| **18sp** | Section titles within cards | Bold (700) |
| **16sp** | Card titles, larger text | Bold (700) |
| **14sp** | Body text, labels | Normal/Bold |
| **13sp** | Secondary body text | Normal |
| **12sp** | Small labels, metadata | Normal/Bold |
| **11sp** | Muted labels, timestamps | Normal |
| **10sp** | Chips, badges | Normal |

### Font Weights
- **Bold (700)**: Headlines, section titles, emphasis
- **Normal (400)**: Body text, descriptions
- **Light (300)**: Secondary metadata (rarely used)

### Line Spacing
- Headings: `1.0x` (tight)
- Body text: `1.2x` to `1.3x` (readable)
- Metadata: `1.0x` (compact)

---

## 3. LAYOUT & SPACING SYSTEM

### Spacing Values (Dimens)
```
spacing_xs    = 4dp   (minimal gaps)
spacing_sm    = 8dp   (small gaps)
spacing_md    = 12dp  (medium gaps)
spacing_lg    = 16dp  (standard padding)
spacing_xl    = 24dp  (large padding)
spacing_2xl   = 32dp  (extra large)
spacing_3xl   = 48dp  (maximum)
```

### Standard Margins & Padding
- **Card padding**: `16dp` (lg)
- **Section margins**: `16dp` horizontal, `12dp` vertical
- **Component margins**: `8-12dp` (sm/md)
- **NestedScrollView padding**: `100dp` bottom (for bottom nav)

### Border Radius (Dimens)
```
radius_sm    = 8dp   (buttons, small chips)
radius_md    = 12dp  (icon buttons, badges)
radius_lg    = 20dp  (cards, FAB)
radius_xl    = 24dp  (large modals)
```

---

## 4. CARD STYLES & ELEVATION

### Standard Card
```xml
<com.google.android.material.card.MaterialCardView
    android:layout_width="match_parent"
    android:layout_height="wrap_content"
    app:cardBackgroundColor="@color/white"
    app:cardCornerRadius="20dp"
    app:cardElevation="2dp"
    app:strokeWidth="0.5dp"
    app:strokeColor="@color/line_variant">
```

### Card Variants

**Elevated Card** (for hero sections, primary focus)
- Elevation: `4dp`
- Stroke: `none` (0dp)
- Corner radius: `20dp`
- Background: `white`

**Light Accent Card** (for info, highlights)
- Background color: `@color/primary_light`
- Stroke: `1dp` + `@color/primary`
- Elevation: `2dp`
- Corner radius: `16dp`

**Header Card** (sticky headers)
- Elevation: `4dp`
- Corner radius: `0dp`
- Stroke: `none`
- Background: `white`

### Empty State Cards
- Background: `@color/primary_light` (circular)
- Corner radius: `50dp`
- Elevation: `0dp`
- Size: `100dp` width/height

---

## 5. BUTTON STYLES

### Primary Button
```xml
<com.google.android.material.button.MaterialButton
    android:layout_width="match_parent"
    android:layout_height="56dp"
    app:backgroundTint="@color/primary"
    app:cornerRadius="20dp"
    android:textColor="@color/white"
    app:icon="@drawable/ic_icon"
    app:iconPadding="8dp" />
```

### Outlined Button
```xml
style="@style/Widget.Material3.Button.OutlinedButton"
app:backgroundTint="@color/surface"
app:strokeColor="@color/line"
app:strokeWidth="1dp"
android:textColor="@color/text_primary"
```

### Icon Button
```xml
style="@style/Widget.Material3.Button.IconButton"
android:layout_width="40dp"
android:layout_height="40dp"
app:backgroundTint="@android:color/transparent"
app:cornerRadius="12dp"
```

### Button Heights
- **Small**: `40dp`
- **Medium**: `48dp`
- **Large**: `56-64dp` (CTA buttons)

---

## 6. RECYCLERVIEW ITEM DESIGNS

### Knowledge Card Item
**File**: `item_knowledge_card.xml`

**Structure**:
1. **Header Strip** (4dp colored bar, primary color)
2. **Type Badge** (rounded chip with icon + label)
3. **Title** (16sp bold, 2 lines max)
4. **Preview Text** (13sp secondary, 2 lines max)
5. **Tag Chips** (10sp, single line horizontal)
6. **Footer Metadata** (surface background, icons + labels)
   - Category icon + label
   - Last review date
   - Next review date (in accent color)
7. **Action Buttons** (Favorite, Menu)

**Card styling**:
- Corner radius: `16dp`
- Elevation: `3dp`
- Padding: `16dp`
- Margin bottom: `12dp`

### Learning Path Card Item
**File**: `item_path_card.xml`

**Structure**:
1. **Header Row**:
   - Icon (48dp, inside rounded background)
   - Title & Description (2-line max)
   - Status Badge (custom drawable)
2. **Stats Row** (3 columns):
   - Duration (hours)
   - Milestones count
   - Tasks count
3. **Progress Section**:
   - Linear progress bar (8dp height)
   - Progress text
4. **Bottom Info Row**:
   - Usage count + icon
   - Target date
   - More button

**Card styling**:
- Corner radius: `20dp`
- Elevation: `2dp`
- Padding: `20dp`
- Stroke: `0.5dp`, `line_variant` color

### Task Card Item
**Similar structure to path cards**:
- Icon + Title/Description
- Metadata chips (due date, priority)
- Progress bar
- Status indicator

---

## 7. NAVIGATION PATTERNS

### Bottom Navigation
```xml
<com.google.android.material.bottomnavigation.BottomNavigationView
    android:id="@+id/bottom_navigation"
    android:layout_width="match_parent"
    android:layout_height="wrap_content"
    android:background="@color/white"
    app:elevation="8dp"
    app:labelVisibilityMode="labeled"
    app:itemIconTint="@color/bottom_nav_item_color"
    app:itemTextColor="@color/bottom_nav_item_color"
    app:itemActiveIndicatorStyle="@null" />
```

**Menu Items**:
1. Home (MainActivity)
2. Calendar (CalendarActivity)
3. Paths (PathsActivity)
4. Knowledge (KnowledgeActivity)
5. Settings (SettingsActivity)

### Activity Headers
**Pattern**: Back button | Logo | Title | Action buttons

```xml
<!-- Back Button (40dp icon button) -->
<com.google.android.material.button.MaterialButton
    style="@style/Widget.Material3.Button.IconButton"
    app:icon="@drawable/ic_arrow_back"
    app:iconTint="@color/text_primary" />

<!-- Logo (32dp card) -->
<com.google.android.material.card.MaterialCardView
    android:layout_width="32dp"
    android:layout_height="32dp"
    app:cardBackgroundColor="@color/primary_light"
    app:cardCornerRadius="8dp" />

<!-- Title -->
<TextView android:textSize="20sp" android:textStyle="bold" />

<!-- Action Buttons (share, edit, delete) -->
```

**Header styling**:
- Elevation: `4dp`
- Corner radius: `0dp` (full width)
- Padding: `16dp`
- Background: `white`

---

## 8. CHIP & BADGE STYLES

### Filter Chips
```xml
<com.google.android.material.chip.Chip
    style="@style/Widget.Material3.Chip.Filter"
    app:chipBackgroundColor="@color/surface"
    app:chipCornerRadius="8dp"
    android:checked="false" />

<!-- When selected: background changes to primary -->
```

**Spacing**: `8dp` horizontal spacing within ChipGroup

### Category Chips
- Height: `24-32dp` minimum
- Text size: `10-11sp`
- Background: `@color/primary_light` or `@color/surface`
- Corner radius: `8dp`

### Status Badge
```xml
<LinearLayout
    android:background="@drawable/badge_featured"
    android:paddingHorizontal="12dp"
    android:paddingVertical="6dp">
    <TextView
        android:textSize="12sp"
        android:textStyle="bold"
        android:textColor="@color/primary" />
</LinearLayout>
```

---

## 9. SEARCH BAR

**File**: Used in KnowledgeActivity, CheatCodeActivity

```xml
<com.google.android.material.card.MaterialCardView
    app:cardCornerRadius="20dp"
    app:cardElevation="2dp">
    <LinearLayout
        android:orientation="horizontal"
        android:gravity="center_vertical"
        android:padding="12dp">
        <ImageView
            android:layout_width="24dp"
            android:layout_height="24dp"
            app:tint="@color/text_muted" />
        <EditText
            android:layout_width="0dp"
            android:layout_weight="1"
            android:background="@android:color/transparent"
            android:textColor="@color/text_primary"
            android:textColorHint="@color/text_muted"
            android:textSize="14sp" />
    </LinearLayout>
</com.google.android.material.card.MaterialCardView>
```

---

## 10. CODE STRUCTURE PATTERNS

### BaseActivity Pattern
All activities inherit from `BaseActivity`:
```kotlin
class YourActivity : BaseActivity() {
    private lateinit var binding: ActivityYourBinding

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityYourBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupWindowInsets()  // Handle system bars
        setupViewModel()
        setupUI()
        setupObservers()
        setupClickListeners()
    }
}
```

### ViewBinding Pattern
- All activities use ViewBinding for type-safe view access
- Inflate binding in onCreate
- Use `binding.viewId` to access views

### ViewModel Pattern
```kotlin
private lateinit var viewModel: YourViewModel

private fun setupViewModel() {
    viewModel = ViewModelProvider(this)[YourViewModel::class.java]
}

private fun setupObservers() {
    viewModel.liveData.observe(this) { data ->
        // Update UI
    }
}
```

### Adapter Pattern (DiffUtil + ListAdapter)
```kotlin
class YourAdapter(
    private val onItemClick: (Item) -> Unit
) : ListAdapter<Item, YourAdapter.ViewHolder>(ItemDiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemYourBinding.inflate(
            LayoutInflater.from(parent.context),
            parent,
            false
        )
        return ViewHolder(binding)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    inner class ViewHolder(private val binding: ItemYourBinding) :
        RecyclerView.ViewHolder(binding.root) {
        fun bind(item: Item) {
            binding.apply {
                tvTitle.text = item.title
                root.setOnClickListener { onItemClick(item) }
            }
        }
    }

    class ItemDiffCallback : DiffUtil.ItemCallback<Item>() {
        override fun areItemsTheSame(old: Item, new: Item) =
            old.id == new.id
        override fun areContentsTheSame(old: Item, new: Item) =
            old == new
    }
}
```

### Click Listener Pattern
```kotlin
// In Activity
taskAdapter = MainTaskAdapter(
    onTaskClick = { task ->
        val intent = Intent(this, TaskDetailActivity::class.java)
        intent.putExtra("task_id", task.id)
        startActivity(intent)
    },
    onStartClick = { task ->
        // Handle start action
    },
    onMoreClick = { task ->
        showOptionsBottomSheet(task)
    }
)
```

---

## 11. WINDOW INSETS HANDLING

```kotlin
// In BaseActivity
protected fun setupWindowInsets(
    mainViewId: Int = R.id.main,
    lightStatusBar: Boolean = true
) {
    WindowCompat.setDecorFitsSystemWindows(window, false)
    setupStatusBarAppearance(lightStatusBar)

    // Handle bottom navigation padding
    val bottomNav = findViewById<View>(R.id.bottom_navigation)
    bottomNav?.let {
        ViewCompat.setOnApplyWindowInsetsListener(it) { v, insets ->
            val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            v.setPadding(v.paddingLeft, v.paddingTop, v.paddingRight, systemBars.bottom)
            insets
        }
    }
}
```

---

## 12. LAYOUT STRUCTURE PATTERNS

### Standard Activity Layout
```xml
<androidx.constraintlayout.widget.ConstraintLayout
    android:background="@color/background">

    <!-- Header Card -->
    <com.google.android.material.card.MaterialCardView
        android:id="@+id/header_card"
        app:layout_constraintTop_toTopOf="parent" />

    <!-- Main Content (NestedScrollView) -->
    <androidx.core.widget.NestedScrollView
        android:paddingBottom="100dp"
        app:layout_constraintTop_toBottomOf="@id/header_card"
        app:layout_constraintBottom_toTopOf="@id/bottom_navigation">
        <LinearLayout
            android:orientation="vertical">
            <!-- Cards and content here -->
        </LinearLayout>
    </androidx.core.widget.NestedScrollView>

    <!-- Bottom Navigation -->
    <com.google.android.material.bottomnavigation.BottomNavigationView
        android:id="@+id/bottom_navigation"
        app:layout_constraintBottom_toBottomOf="parent" />
</androidx.constraintlayout.widget.ConstraintLayout>
```

### Empty State Pattern
```xml
<LinearLayout
    android:id="@+id/empty_state"
    android:gravity="center"
    android:orientation="vertical"
    android:visibility="gone">

    <!-- Icon Circle -->
    <com.google.android.material.card.MaterialCardView
        android:layout_width="100dp"
        android:layout_height="100dp"
        app:cardBackgroundColor="@color/primary_light"
        app:cardCornerRadius="50dp">
        <ImageView
            android:src="@drawable/ic_icon"
            app:tint="@color/primary" />
    </com.google.android.material.card.MaterialCardView>

    <!-- Title + Description -->
    <TextView android:textSize="20sp" android:textStyle="bold" />
    <TextView android:textColor="@color/text_muted" />

    <!-- CTA Button -->
    <com.google.android.material.button.MaterialButton
        app:backgroundTint="@color/primary"
        app:cornerRadius="24dp" />
</LinearLayout>
```

---

## 13. COMMON COMPONENT PATTERNS

### Progress Ring (Circular)
```xml
<com.google.android.material.progressindicator.CircularProgressIndicator
    android:layout_width="wrap_content"
    android:layout_height="wrap_content"
    app:indicatorSize="160dp"
    app:trackThickness="14dp"
    app:trackColor="@color/line"
    app:indicatorColor="@color/primary" />
```

### Progress Bar (Linear)
```xml
<com.google.android.material.progressindicator.LinearProgressIndicator
    android:layout_width="match_parent"
    android:layout_height="8dp"
    app:trackThickness="8dp"
    app:indicatorColor="@color/primary"
    app:trackColor="@color/line" />
```

### Spinner (Learning Path Selector)
```xml
<Spinner
    android:layout_width="0dp"
    android:layout_weight="1"
    android:spinnerMode="dropdown"
    android:backgroundTint="@color/primary" />
```

### FAB (Floating Action Button)
```xml
<com.google.android.material.floatingactionbutton.FloatingActionButton
    app:srcCompat="@drawable/ic_add"
    app:backgroundTint="@color/primary"
    app:tint="@color/white"
    android:layout_marginEnd="@dimen/spacing_lg"
    android:layout_marginBottom="@dimen/spacing_lg" />
```

---

## 14. RESPONSIVE DESIGN

### Screen Width Handling
- **Small phones**: All components adapt with `match_parent` width
- **Tablets**: Cards use `match_parent` with internal constraints
- No specific tablet layouts in current design

### Flexible Grids
- RecyclerViews use `LinearLayoutManager` (not GridLayoutManager)
- Single-column layout throughout app
- Card width always spans screen width minus horizontal margins

---

## 15. ANIMATION & TRANSITIONS

### Material Transitions
- Default Material activity transitions (implicit)
- No custom animations in current design
- Ripple effects on clickable items via `?attr/selectableItemBackground`

### View State Changes
- Chips: Color transitions between checked/unchecked
- Buttons: Elevation and color changes on press
- Progress bars: Smooth progress updates

---

## 16. BEST PRACTICES

### Do's
- Use Material 3 components from `com.google.android.material`
- Always use `MaterialCardView` for cards (not CardView)
- Apply consistent spacing using `@dimen` values
- Use `ViewBinding` for all views
- Follow `ListAdapter` + `DiffUtil` pattern for RecyclerViews
- Use `ConstraintLayout` for complex layouts
- Handle window insets for notch/gesture navigation
- Test on multiple screen sizes

### Don'ts
- Don't use hard-coded colors; always use `@color/` references
- Don't use hard-coded dimensions; always use `@dimen/` values
- Don't create custom card styles; use Material components
- Don't mix ViewBinding with findViewById()
- Don't use custom fonts; stick with system fonts
- Don't add unnecessary elevation to cards
- Don't use colors outside the defined palette

---

## 17. REFERENCE SNIPPETS

### Full Activity Template
```kotlin
class ExampleActivity : BaseActivity() {

    private lateinit var binding: ActivityExampleBinding
    private lateinit var viewModel: ExampleViewModel
    private lateinit var adapter: ExampleAdapter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityExampleBinding.inflate(layoutInflater)
        setContentView(binding.root)

        setupWindowInsets()
        setupViewModel()
        setupUI()
        setupObservers()
        setupClickListeners()
        loadData()
    }

    private fun setupViewModel() {
        viewModel = ViewModelProvider(this)[ExampleViewModel::class.java]
    }

    private fun setupUI() {
        adapter = ExampleAdapter(
            onItemClick = { item ->
                val intent = Intent(this, DetailActivity::class.java)
                intent.putExtra("item_id", item.id)
                startActivity(intent)
            },
            onActionClick = { item ->
                // Handle action
            }
        )

        binding.rvItems.apply {
            adapter = this@ExampleActivity.adapter
            layoutManager = LinearLayoutManager(this@ExampleActivity)
            setHasFixedSize(true)
        }
    }

    private fun setupObservers() {
        viewModel.items.observe(this) { items ->
            adapter.submitList(items)
            binding.emptyState.visibility =
                if (items.isEmpty()) View.VISIBLE else View.GONE
        }
    }

    private fun setupClickListeners() {
        binding.btnAdd.setOnClickListener {
            startActivity(Intent(this, AddActivity::class.java))
        }
    }

    private fun loadData() {
        viewModel.loadItems()
    }
}
```

---

## 18. LOCALIZATION NOTES

- All text uses string resources from `strings.xml`
- Japanese (`values-ja/strings.xml`) and Vietnamese (`values-vi/strings.xml`) supported
- All numbers formatted using `Locale.getDefault()`
- Date formatting via `SimpleDateFormat` with locale support
- `BaseActivity` applies locale via `LocaleHelper.applyLocale()`

---

## Document Version
- **Last Updated**: November 23, 2025
- **App Version**: 1.0.0
- **Target SDK**: Android 12+
- **Design System**: Material Design 3

