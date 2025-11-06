<?php

namespace Database\Seeders;

use App\Models\CheatCodeLanguage;
use App\Models\CheatCodeSection;
use App\Models\CodeExample;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CheatCodeKotlinSeeder extends Seeder
{
    /**
     * Seed Kotlin cheat code data from quickref.me
     * Reference: https://quickref.me/kotlin
     */
    public function run(): void
    {
        // Create Kotlin Language
        $kotlinLanguage = CheatCodeLanguage::create([
            'name' => 'kotlin',
            'display_name' => 'Kotlin',
            'slug' => 'kotlin',
            'color' => '#7F52FF',
            'description' => 'A quick reference cheatsheet for Kotlin that includes usage, examples, and more.',
            'category' => 'programming',
            'popularity' => 88,
            'is_active' => true,
            'sort_order' => 6,
        ]);

        // Section 1: Introduction to Kotlin
        $section1 = $this->createSection($kotlinLanguage, 'Introduction to Kotlin', 1, 'Kotlin basics and introduction');

        $this->createExample($section1, $kotlinLanguage, 'main()', 1,
            "fun main() {\n  println(\"Greetings, QuickRef.ME!\")\n  // Code goes here\n}",
            'The main() function is the starting point of every Kotlin program',
            "Greetings, QuickRef.ME!",
            'easy'
        );

        $this->createExample($section1, $kotlinLanguage, 'Print statement', 2,
            "println(\"Greetings, earthling!\")\nprint(\"Take me to \")\nprint(\"your leader.\")\n\n/*\nPrint:\nGreetings, earthling!\nTake me to your leader.\n*/",
            'Print and println statements',
            "Greetings, earthling!\nTake me to your leader.",
            'easy'
        );

        $this->createExample($section1, $kotlinLanguage, 'Notes', 3,
            "// this is a single line comment\n\n/*\nthis\nnote\nfor\nmany\n*/",
            'Comment syntax',
            null,
            'easy'
        );

        $this->createExample($section1, $kotlinLanguage, 'Execution order', 4,
            "fun main() {\n  println(\"I will be printed first.\")\n  println(\"I will be printed second.\")\n  println(\"I will be printed third.\")\n}",
            'Code execution order',
            "I will be printed first.\nI will be printed second.\nI will be printed third.",
            'easy'
        );

        // Section 2: Data Types and Variables
        $section2 = $this->createSection($kotlinLanguage, 'Data Types and Variables', 2, 'Variables and data types');

        $this->createExample($section2, $kotlinLanguage, 'Mutable variables', 1,
            "var age = 25\nage = 26",
            'Mutable variables with var',
            null,
            'easy'
        );

        $this->createExample($section2, $kotlinLanguage, 'Immutable variables', 2,
            "val goldenRatio = 1.618",
            'Immutable variables with val',
            null,
            'easy'
        );

        $this->createExample($section2, $kotlinLanguage, 'Type inference', 3,
            "// The following variables are assigned a literal value inside double quotes\n// so the inferred type is String\n\nvar color = \"Purple\"",
            'Type inference in Kotlin',
            null,
            'easy'
        );

        $this->createExample($section2, $kotlinLanguage, 'String concatenation', 4,
            "var streetAddress = \"123 Main St.\"\nvar cityState = \"Brooklyn, NY\"\n\nprintln(streetAddress + \" \" + cityState)\n// Print: 123 Main St. Brooklyn, NY",
            'String concatenation',
            "123 Main St. Brooklyn, NY",
            'easy'
        );

        $this->createExample($section2, $kotlinLanguage, 'String Templates', 5,
            "var address = \"123 Main St.\"\nprintln(\"The address is \$address\")\n// prints: The address is 123 Main St.",
            'String templates with \$',
            "The address is 123 Main St.",
            'easy'
        );

        $this->createExample($section2, $kotlinLanguage, 'Built-in Properties and Functions', 6,
            "var monument = \"the Statue of Liberty\"\n\nprintln(monument.capitalize())\n// print: The Statue of Liberty\nprintln(monument.length)\n// print: 21",
            'String properties and functions',
            "The Statue of Liberty\n21",
            'easy'
        );

        $this->createExample($section2, $kotlinLanguage, 'Character escape', 7,
            "print(\"\\\"Excellent!\\\" I cried. \\\"Elementary,\\\" said he.\")\n\n// Print: \"Excellent!\" I cried. \"Elementary,\" said he.",
            'Character escape sequences',
            "\"Excellent!\" I cried. \"Elementary,\" said he.",
            'easy'
        );

        $this->createExample($section2, $kotlinLanguage, 'Arithmetic Operators', 8,
            "5 + 7  // 12\n9 - 2   // 7\n8 * 4   // 32\n25 / 5  // 5\n31 % 2 // 1",
            'Arithmetic operators',
            null,
            'easy'
        );

        $this->createExample($section2, $kotlinLanguage, 'Order of operations', 9,
            "5 + 8 * 2 / 4 - 3 // 6\n3 + (4 + 4) / 2 // 7\n4 * 2 + 1 * 7    // 15\n3 + 18 / 2 * 1   // 12\n6 - 3 % 2 + 2   // 7",
            'Operator precedence',
            null,
            'easy'
        );

        $this->createExample($section2, $kotlinLanguage, 'Enhanced assignment operator', 10,
            "var batteryPercentage = 80\n\n// long syntax\nbatteryPercentage = batteryPercentage + 10\n\n// short syntax with augmented assignment operator\nbatteryPercentage += 10",
            'Augmented assignment operators',
            null,
            'easy'
        );

        $this->createExample($section2, $kotlinLanguage, 'Increment and decrement operators', 11,
            "var year = 2019\nyear++   // 2020\nyear--   // 2019",
            'Increment and decrement operators',
            null,
            'easy'
        );

        $this->createExample($section2, $kotlinLanguage, 'Math library', 12,
            "Math.pow(2.0, 3.0) // 8.0\nMath.min(6, 9)     // 6\nMath.max(10, 12)   // 12\nMath.round(13.7)  // 14",
            'Math library functions',
            null,
            'easy'
        );

        // Section 3: Conditional Expression
        $section3 = $this->createSection($kotlinLanguage, 'Conditional Expression', 3, 'Conditional statements');

        $this->createExample($section3, $kotlinLanguage, 'If expression', 1,
            "var morning = true\n\nif (morning) {\n  println(\"Rise and shine!\")\n}\n// Print: Rise and shine!",
            'Basic if expression',
            "Rise and shine!",
            'easy'
        );

        $this->createExample($section3, $kotlinLanguage, 'Else-expression', 2,
            "var rained = false\n\nif (rained) {\n  println(\"No need to water the plants today.\")\n} else {\n  println(\"The plant needs to be watered!\")\n}\n// print: The plant needs watering!",
            'If-else expression',
            "The plant needs to be watered!",
            'easy'
        );

        $this->createExample($section3, $kotlinLanguage, 'Else-If expressions', 3,
            "var age = 65\n\nif (age < 18) {\n  println(\"You are considered a minor\")\n} else if (age < 60) {\n  println(\"You are considered an adult\")\n} else {\n  println(\"You are considered senior\")\n}\n\n// print: you are considered senior",
            'Else-if chain',
            "You are considered senior",
            'easy'
        );

        $this->createExample($section3, $kotlinLanguage, 'Comparison Operators', 4,
            "var myAge = 19\nvar sisterAge = 11\nvar cousinAge = 11\n\nmyAge > sisterAge  // true\nmyAge < cousinAge  // false\nmyAge >= cousinAge // true\nmyAge <= sisterAge // false",
            'Comparison operators',
            null,
            'easy'
        );

        $this->createExample($section3, $kotlinLanguage, 'Logical Operators', 5,
            "var humid = true\nvar raining = true\nvar jacket = false\n\nprintln(!humid)\n// print: false\nprintln(jacket && raining)\n// print: false\nprintln(humid || raining)\n// print: true",
            'Logical operators',
            "false\nfalse\ntrue",
            'easy'
        );

        $this->createExample($section3, $kotlinLanguage, 'AND operator: &&', 6,
            "var humid = true\nvar raining = true\nvar shorts = false\nvar sunny = false\n\n// true AND true\nprintln(humid && raining) // true\n// true AND false\nprintln(humid && shorts)  // false\n// false AND true\nprintln(sunny && raining) // false\n// false AND false\nprintln(shorts && sunny)  // false",
            'Logical AND operator',
            "true\nfalse\nfalse\nfalse",
            'easy'
        );

        $this->createExample($section3, $kotlinLanguage, 'Or operator: ||', 7,
            "var late = true\nvar skipBreakfast = true\nvar underslept = false\nvar checkEmails = false\n\n// true OR true\nprintln(skipBreakfast || late) // true\n// true OR false\nprintln(late || checkEmails)   // true\n// false OR true\nprintln(underslept || late)    // true\n// false OR false\nprintln(checkEmails || underslept) // false",
            'Logical OR operator',
            "true\ntrue\ntrue\nfalse",
            'easy'
        );

        $this->createExample($section3, $kotlinLanguage, 'NOT operator', 8,
            "var hungry = true\nvar full = false\n\nprintln(!hungry) // false\nprintln(!full)   // true",
            'Logical NOT operator',
            "false\ntrue",
            'easy'
        );

        $this->createExample($section3, $kotlinLanguage, 'Evaluation order', 9,
            "!true && (false || true) // false\n/*\n(false || true) is evaluated first to return true.\nThen, evaluate !true && true and return the final result false\n*/\n\n!false && true || false // true\n/*\n!false is evaluated first to return true.\nThen true && true is evaluated, returning true.\nthen, true || evaluates to false and eventually returns true\n*/",
            'Operator evaluation order',
            null,
            'medium'
        );

        $this->createExample($section3, $kotlinLanguage, 'Nested conditions', 10,
            "var studied = true\nvar wellRested = true\n\nif (wellRested) {\n  println(\"Good luck today!\")\n  if (studied) {\n    println(\"You should prepare for the exam!\")\n  } else {\n    println(\"Spend a few hours studying before the exam!\")\n  }\n}\n\n// Print: Good luck today!\n// print: You should be ready for the exam!",
            'Nested if statements',
            "Good luck today!\nYou should prepare for the exam!",
            'easy'
        );

        $this->createExample($section3, $kotlinLanguage, 'When expression', 11,
            "var grade = \"A\"\n\nwhen (grade) {\n  \"A\" -> println(\"Great job!\")\n  \"B\" -> println(\"Great job!\")\n  \"C\" -> println(\"You passed!\")\n  else -> println(\"Close! Be sure to prepare more next time!\")\n}\n// print: Great job!",
            'When expression (switch-like)',
            "Great job!",
            'easy'
        );

        $this->createExample($section3, $kotlinLanguage, 'Range operator', 12,
            "var height = 46 // inches\n\nif (height in 1..53) {\n  println(\"Sorry, you must be at least 54 inches to ride the coaster\")\n}\n// Prints: Sorry, you must be at least 54 inches to ride the roller coaster",
            'Range operator (in)',
            "Sorry, you must be at least 54 inches to ride the coaster",
            'easy'
        );

        $this->createExample($section3, $kotlinLanguage, 'Equality Operators', 13,
            "var myAge = 22\nvar sisterAge = 21\n\nmyAge == sisterAge // false\nmyAge != sisterAge // true",
            'Equality operators',
            null,
            'easy'
        );

        // Section 4: Collections
        $section4 = $this->createSection($kotlinLanguage, 'Collections', 4, 'Lists, sets, and maps');

        $this->createExample($section4, $kotlinLanguage, 'Immutable list', 1,
            "var programmingLanguages = listOf(\"C#\", \"Java\", \"Kotlin\", \"Ruby\")",
            'Creating immutable list',
            null,
            'easy'
        );

        $this->createExample($section4, $kotlinLanguage, 'Mutable List', 2,
            "var fruits = mutableListOf(\"Orange\", \"Apple\", \"Banana\", \"Mango\")",
            'Creating mutable list',
            null,
            'easy'
        );

        $this->createExample($section4, $kotlinLanguage, 'Access List', 3,
            "var cars = listOf(\"BMW\", \"Ferrari\", \"Volvo\", \"Tesla\")\n\nprintln(cars[2]) // Prints: Volvo",
            'Accessing list elements',
            "Volvo",
            'easy'
        );

        $this->createExample($section4, $kotlinLanguage, 'Size Attribute', 4,
            "var worldContinents = listOf(\"Asia\", \"Africa\", \"North America\", \"South America\", \"Antarctica\", \"Europe\", \"Australia\")\n\nprintln(worldContinents.size) // Prints: 7",
            'Getting list size',
            "7",
            'easy'
        );

        $this->createExample($section4, $kotlinLanguage, 'List Manipulation', 5,
            "var seas = listOf(\"Black Sea\", \"Caribbean Sea\", \"North Sea\")\nprintln(seas.contains(\"North Sea\")) // Prints: true\n\n// The contains() function performs a read operation on any list and determines if the element exists\nseas.add(\"Baltic Sea\") // Error: cannot write to immutable list\n// The add() function can only be called on mutable lists, so the code above throws an error",
            'List manipulation operations',
            "true",
            'easy'
        );

        $this->createExample($section4, $kotlinLanguage, 'Immutable Sets', 6,
            "var primaryColors = setOf(\"Red\", \"Blue\", \"Yellow\")",
            'Creating immutable set',
            null,
            'easy'
        );

        $this->createExample($section4, $kotlinLanguage, 'Mutable Sets', 7,
            "var womenInTech = mutableSetOf(\"Ada Lovelace\", \"Grace Hopper\", \"Radia Perlman\", \"Sister Mary Kenneth Keller\")",
            'Creating mutable set',
            null,
            'easy'
        );

        $this->createExample($section4, $kotlinLanguage, 'Access Collection Elements', 8,
            "var companies = setOf(\"Facebook\", \"Apple\", \"Netflix\", \"Google\")\n\nprintln(companies.elementAt(3))\n// Prints: Google\nprintln(companies.elementAt(4))\n// Returns and Error\nprintln(companies.elementAtOrNull(4))\n// Prints: null",
            'Accessing set elements',
            "Google\nnull",
            'easy'
        );

        $this->createExample($section4, $kotlinLanguage, 'Immutable Map', 9,
            "var averageTemp = mapOf(\"winter\" to 35,  \"spring\" to 60,  \"summer\" to 85, \"fall\" to 55)",
            'Creating immutable map',
            null,
            'easy'
        );

        $this->createExample($section4, $kotlinLanguage, 'Mutable Mapping', 10,
            "var europeanDomains = mutableMapOf(\"Germany\" to \"de\", \"Slovakia\" to \"sk\", \"Hungary\" to \"hu\", \"Norway\" to \"no\")",
            'Creating mutable map',
            null,
            'easy'
        );

        $this->createExample($section4, $kotlinLanguage, 'Retrieve map keys and values', 11,
            "var oscarWinners = mutableMapOf(\"Parasite\" to \"Bong Joon-ho\", \"Green Book\" to \"Jim Burke\", \"The Shape Of Water\" to \"Guillermo del Toro\")\n\nprintln(oscarWinners.keys)\n// Prints: [Parasite, Green Book, The Shape Of Water]\n\nprintln(oscarWinners.values)\n// Prints: [Bong Joon-ho, Jim Burke, Guillermo del Toro]\nprintln(oscarWinners[\"Parasite\"])\n// Prints: Bong Joon-ho",
            'Accessing map keys and values',
            "[Parasite, Green Book, The Shape Of Water]\n[Bong Joon-ho, Jim Burke, Guillermo del Toro]\nBong Joon-ho",
            'easy'
        );

        $this->createExample($section4, $kotlinLanguage, 'Add and remove map entries', 12,
            "var worldCapitals = mutableMapOf(\"United States\" to \"Washington D.C.\", \"Germany\" to \"Berlin\", \"Mexico\" to \"Mexico City\", \"France\" to \"Paris\")\n\nworldCapitals.put(\"Brazil\", \"Brasilia\")\nprintln(worldCapitals)\n// Prints: {United States=Washington D.C., Germany=Berlin, Mexico=Mexico City, France=Paris, Brazil=Brasilia}\n\nworldCapitals.remove(\"Germany\")\nprintln(worldCapitals)\n// Prints: {United States=Washington D.C., Mexico=Mexico City, France=Paris, Brazil=Brasilia}",
            'Adding and removing map entries',
            "{United States=Washington D.C., Germany=Berlin, Mexico=Mexico City, France=Paris, Brazil=Brasilia}\n{United States=Washington D.C., Mexico=Mexico City, France=Paris, Brazil=Brasilia}",
            'easy'
        );

        // Section 5: Function
        $section5 = $this->createSection($kotlinLanguage, 'Function', 5, 'Function definitions and usage');

        $this->createExample($section5, $kotlinLanguage, 'Function', 1,
            "fun greet() {\n  println(\"Hey there!\")\n}\n\nfun main() {\n  //Function call\n  greet() //Prints: Hey there!\n}",
            'Basic function definition',
            "Hey there!",
            'easy'
        );

        $this->createExample($section5, $kotlinLanguage, 'Function Parameters', 2,
            "fun birthday(name: String, age: Int) {\n   println(\"Happy birthday \$name! You turn \$age today!\")\n}\n\nfun main() {\n  birthday(\"Oscar\", 26) \n  //Prints: Happy birthday Oscar! You turn 26 today!\n  birthday(\"Amarah\", 30) \n  //Prints: Happy birthday Amarah! You turn 30 today!\n}",
            'Function with parameters',
            "Happy birthday Oscar! You turn 26 today!\nHappy birthday Amarah! You turn 30 today!",
            'easy'
        );

        $this->createExample($section5, $kotlinLanguage, 'Default Parameters', 3,
            "fun favoriteLanguage(name: String, language: String = \"Kotlin\") {\n  println(\"Hello, \$name. Your favorite programming language is \$language\")  \n}\n\nfun main() {\n  favoriteLanguage(\"Manon\") \n  //Prints: Hello, Manon. Your favorite programming language is Kotlin\n  \n  favoriteLanguage(\"Lee\", \"Java\") \n  //Prints: Hello, Lee. Your favorite programming language is Java\n}",
            'Default function parameters',
            "Hello, Manon. Your favorite programming language is Kotlin\nHello, Lee. Your favorite programming language is Java",
            'easy'
        );

        $this->createExample($section5, $kotlinLanguage, 'Named Parameters', 4,
            "fun findMyAge(currentYear: Int, birthYear: Int) {\n   var myAge = currentYear - birthYear\n   println(\"I am \$myAge years old.\")\n}\n\nfun main() {\n  findMyAge(currentYear = 2020, birthYear = 1995)\n  //Prints: I am 25 years old.\n  findMyAge(birthYear = 1920, currentYear = 2020)\n  //Prints: I am 100 years old.\n}",
            'Named function parameters',
            "I am 25 years old.\nI am 100 years old.",
            'easy'
        );

        $this->createExample($section5, $kotlinLanguage, 'Return Statement', 5,
            "//Return type is declared outside the parentheses\nfun getArea(length: Int, width: Int): Int {\n  var area = length * width\n\n  //return statement\n  return area\n}\n\nfun main() {\n  var myArea = getArea(10, 8)\n  println(\"The area is \$myArea.\")\n  //Prints: The area is 80.\n}",
            'Function with return type',
            "The area is 80.",
            'easy'
        );

        $this->createExample($section5, $kotlinLanguage, 'Single expression function', 6,
            "fun fullName(firstName: String, lastName: String) = \"\$firstName \$lastName\"\n\nfun main() {\n  println(fullName(\"Ariana\", \"Ortega\"))\n  //Prints: Ariana Ortega\n  println(fullName(\"Kai\", \"Gittens\"))\n  //Prints: Kai Gittens\n}",
            'Single expression function',
            "Ariana Ortega\nKai Gittens",
            'easy'
        );

        $this->createExample($section5, $kotlinLanguage, 'Function Literals', 7,
            "fun main() {\n  //Anonymous Function:\n  var getProduct = fun(num1: Int, num2: Int): Int {\n    return num1 * num2\n  }\n  println(getProduct(8, 3))\n  //Prints: 24\n  //Lambda Expression\n  var getDifference = { num1: Int, num2: Int -> num1 - num2 }\n  println(getDifference(10, 3))\n  //Prints: 7\n}",
            'Anonymous functions and lambda expressions',
            "24\n7",
            'medium'
        );

        // Section 6: Class
        $section6 = $this->createSection($kotlinLanguage, 'Class', 6, 'Object-oriented programming');

        $this->createExample($section6, $kotlinLanguage, 'Class Example', 1,
            "//class with properties containing default values\nclass Student {\n  var name = \"Lucia\"\n  var semester = \"Fall\"\n  var gpa = 3.95\n}\n\n//shorthand syntax without class body\nclass Student",
            'Basic class definition',
            null,
            'easy'
        );

        $this->createExample($section6, $kotlinLanguage, 'Class Instance', 2,
            "// Class\nclass Student {\n  var name = \"Lucia\"\n  var semester = \"Fall\"\n  var gpa = 3.95\n}\n\nfun main() {\n  var student = Student()   \n  // Instance\n  println(student.name)     \n  // Prints: Lucia\n  println(student.semester) \n  // Prints: Fall\n  println(student.gpa)      \n  // Prints: 3.95  \n}",
            'Creating class instance',
            "Lucia\nFall\n3.95",
            'easy'
        );

        $this->createExample($section6, $kotlinLanguage, 'Primary Constructor', 3,
            "class Student(val name: String, val gpa: Double, val semester: String, val estimatedGraduationYear: Int) \n\nfun main() {\n  var student = Student(\"Lucia\", 3.95, \"Fall\", 2022) \n  println(student.name)     \n  //Prints: Lucia\n  println(student.gpa)\n  //Prints: 3.95\n  println(student.semester) \n  //Prints: Fall\n  println(student.estimatedGraduationYear) \n  //Prints: 2022\n}",
            'Primary constructor',
            "Lucia\n3.95\nFall\n2022",
            'easy'
        );

        $this->createExample($section6, $kotlinLanguage, 'Initialization Block', 4,
            "class Student(val name: String, val gpa: Double, val semester: String, val estimatedGraduationYear: Int) {\n  init {\n    println(\"\$name has \${estimatedGraduationYear - 2020} years left in college.\")\n  }\n}\n\nfun main() {\n  var student = Student(\"Lucia\", 3.95, \"Fall\", 2022)\n  //Prints: Lucia has 2 years left in college. \n}",
            'Initialization block',
            "Lucia has 2 years left in college.",
            'medium'
        );

        $this->createExample($section6, $kotlinLanguage, 'Member Function', 5,
            "class Student(val name: String, val gpa: Double, val semester: String, val estimatedGraduationYear: Int) {\n\n  init {\n    println(\"\$name has \${estimatedGraduationYear - 2020} years left in college.\")\n  }\n\n  //member function\n  fun calculateLetterGrade(): String {\n    return when {\n      gpa >= 3.0 -> \"A\"\n      gpa >= 2.7 -> \"B\"\n      gpa >= 1.7 -> \"C\"\n      gpa >= 1.0 -> \"D\"\n      else -> \"E\"\n    }\n  }\n}\n\n//When the instance is created and the function is called, the when expression will be executed and return the letter grade\nfun main() {\n  var student = Student(\"Lucia\", 3.95, \"Fall\", 2022)\n  //Prints: Lucia has 2 years left in college.\n  println(\"\${student.name}'s letter grade is \${student.calculateLetterGrade()}.\")\n  //Prints: Lucia's letter grade is A.\n}",
            'Class member functions',
            "Lucia has 2 years left in college.\nLucia's letter grade is A.",
            'medium'
        );

        // Update counts
        $this->updateLanguageCounts($kotlinLanguage);
    }

    private function createSection(CheatCodeLanguage $language, string $title, int $sortOrder, ?string $description = null): CheatCodeSection
    {
        return CheatCodeSection::create([
            'language_id' => $language->id,
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $description,
            'sort_order' => $sortOrder,
            'is_published' => true,
        ]);
    }

    private function createExample(
        CheatCodeSection $section,
        CheatCodeLanguage $language,
        string $title,
        int $sortOrder,
        string $code,
        ?string $description = null,
        ?string $output = null,
        string $difficulty = 'easy'
    ): CodeExample {
        return CodeExample::create([
            'section_id' => $section->id,
            'language_id' => $language->id,
            'title' => $title,
            'slug' => Str::slug($title),
            'code' => $code,
            'description' => $description,
            'output' => $output,
            'difficulty' => $difficulty,
            'tags' => $this->generateTags($title, $description),
            'sort_order' => $sortOrder,
            'is_published' => true,
        ]);
    }

    private function generateTags(string $title, ?string $description): array
    {
        $tags = [];
        $titleLower = strtolower($title);
        $descLower = $description ? strtolower($description) : '';

        // Add tags based on title and description
        if (str_contains($titleLower, 'class') || str_contains($titleLower, 'constructor') || str_contains($descLower, 'oop')) {
            $tags[] = 'oop';
        }
        if (str_contains($titleLower, 'list') || str_contains($titleLower, 'set') || str_contains($titleLower, 'map') || str_contains($titleLower, 'collection')) {
            $tags[] = 'collection';
        }
        if (str_contains($titleLower, 'function') || str_contains($titleLower, 'lambda') || str_contains($titleLower, 'anonymous')) {
            $tags[] = 'function';
        }
        if (str_contains($titleLower, 'string') || str_contains($titleLower, 'template')) {
            $tags[] = 'string';
        }
        if (str_contains($titleLower, 'conditional') || str_contains($titleLower, 'if') || str_contains($titleLower, 'when')) {
            $tags[] = 'conditional';
        }
        if (str_contains($titleLower, 'variable') || str_contains($titleLower, 'val') || str_contains($titleLower, 'var')) {
            $tags[] = 'variable';
        }
        if (str_contains($titleLower, 'operator') || str_contains($titleLower, 'logical') || str_contains($titleLower, 'arithmetic')) {
            $tags[] = 'operator';
        }
        if (str_contains($titleLower, 'mutable') || str_contains($titleLower, 'immutable')) {
            $tags[] = 'immutability';
        }

        // Add basic tags
        $tags[] = 'kotlin';
        $tags[] = 'basics';

        return array_unique($tags);
    }

    private function updateLanguageCounts(CheatCodeLanguage $language): void
    {
        $language->update([
            'sections_count' => $language->sections()->count(),
            'examples_count' => $language->codeExamples()->count(),
            'exercises_count' => $language->exercises()->count(),
        ]);

        // Update section counts
        foreach ($language->sections as $section) {
            $section->update([
                'examples_count' => $section->examples()->count(),
            ]);
        }
    }
}

