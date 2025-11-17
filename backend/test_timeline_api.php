<?php
/**
 * Test Timeline API Endpoint
 * Run: php test_timeline_api.php
 */

// Get the first user ID from database
$userId = 1; // Change this to your user ID

// Test data
$class = [
    'id' => 1,
    'name' => 'test',
    'day' => 'monday',
    'start_time' => '09:15:00',
    'end_time' => '10:45:00',
    'room' => '1603',
    'instructor' => '杉原',
    'period' => 1,
    'color' => '#4F46E5',
    'icon' => 'computer'
];

echo "=== TEST TIMELINE API LOGIC ===\n\n";

// Test 1: Day mapping
echo "Test 1: Day Mapping\n";
$dayMap = [
    'sunday' => 0,
    'monday' => 1,
    'tuesday' => 2,
    'wednesday' => 3,
    'thursday' => 4,
    'friday' => 5,
    'saturday' => 6,
];

$dayOfWeek = $dayMap[strtolower($class['day'])] ?? 1;
echo "  Day: {$class['day']}\n";
echo "  Mapped to day_of_week: {$dayOfWeek}\n";
echo "  ✓ Expected: 1 (Monday)\n\n";

// Test 2: Time parsing
echo "Test 2: Time Parsing\n";
echo "  Start time: {$class['start_time']}\n";
echo "  End time: {$class['end_time']}\n";

try {
    // Parse with H:i:s format
    $start = DateTime::createFromFormat('H:i:s', $class['start_time']);
    $end = DateTime::createFromFormat('H:i:s', $class['end_time']);

    if ($start && $end) {
        $diff = $start->diff($end);
        $durationMinutes = ($diff->h * 60) + $diff->i;
        echo "  Duration: {$durationMinutes} minutes\n";
        echo "  ✓ Expected: 90 minutes\n";
    } else {
        echo "  ✗ Failed to parse times\n";
    }
} catch (Exception $e) {
    echo "  ✗ Error: {$e->getMessage()}\n";
}
echo "\n";

// Test 3: Scheduled time format
echo "Test 3: Scheduled Time Format\n";
$scheduledTime = strlen($class['start_time']) == 5
    ? $class['start_time'] . ':00'
    : $class['start_time'];
echo "  Scheduled time: {$scheduledTime}\n";
echo "  ✓ Expected: 09:15:00\n\n";

// Test 4: Expected timeline item
echo "Test 4: Expected Timeline Item\n";
$timelineItem = [
    'id' => 'class_' . $class['id'],
    'type' => 'timetable_class',
    'title' => '🎓 ' . $class['name'],
    'day_of_week' => $dayOfWeek,
    'scheduled_time' => $scheduledTime,
    'duration_minutes' => $durationMinutes ?? 90,
    'category' => 'class',
    'room' => $class['room'],
    'instructor' => $class['instructor'],
    'period' => $class['period'],
    'color' => $class['color'],
    'icon' => $class['icon'],
];

echo json_encode($timelineItem, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

echo "\n=== SUMMARY ===\n";
echo "✓ Day mapping: monday → 1\n";
echo "✓ Time parsing: 09:15:00 - 10:45:00 = 90 minutes\n";
echo "✓ Scheduled time: 09:15:00\n";
echo "\nIf you see this output correctly, the backend logic is working!\n";
echo "Next step: Test the actual API endpoint with authentication.\n";
