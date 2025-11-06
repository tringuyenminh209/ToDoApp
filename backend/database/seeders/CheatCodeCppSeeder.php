<?php

namespace Database\Seeders;

use App\Models\CheatCodeLanguage;
use App\Models\CheatCodeSection;
use App\Models\CodeExample;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CheatCodeCppSeeder extends Seeder
{
    /**
     * Seed C++ cheat code data from quickref.me
     * Reference: https://quickref.me/cpp
     */
    public function run(): void
    {
        // Create C++ Language
        $cppLanguage = CheatCodeLanguage::create([
            'name' => 'cpp',
            'display_name' => 'C++',
            'slug' => 'cpp',
            'color' => '#00599C',
            'description' => 'C++ quick reference cheat sheet that provides basic syntax and methods.',
            'category' => 'programming',
            'popularity' => 85,
            'is_active' => true,
            'sort_order' => 5,
        ]);

        // Section 1: Getting Started
        $section1 = $this->createSection($cppLanguage, 'Getting Started', 1, 'C++ basics and introduction');

        $this->createExample($section1, $cppLanguage, 'hello.cpp', 1,
            "#include <iostream>\n\nint main() {\n    std::cout << \"Hello QuickRef\\n\";\n    return 0;\n}",
            'Basic Hello World program',
            "Hello QuickRef\n",
            'easy'
        );

        $this->createExample($section1, $cppLanguage, 'Compiling and running', 2,
            "\$ g++ hello.cpp -o hello\n\$ ./hello\nHello QuickRef",
            'Compiling and running C++ programs',
            "Hello QuickRef",
            'easy'
        );

        $this->createExample($section1, $cppLanguage, 'Variables', 3,
            "int number = 5;       // Integer\nfloat f = 0.95;       // Floating number\ndouble PI = 3.14159;  // Floating number\nchar yes = 'Y';       // Character\nstd::string s = \"ME\"; // String (text)\nbool isRight = true;  // Boolean\n\n// Constants\nconst float RATE = 0.8;",
            'Variable declarations and types',
            null,
            'easy'
        );

        $this->createExample($section1, $cppLanguage, 'Uniform initialization', 4,
            "int age {25};         // Since C++11\nstd::cout << age;     // Print 25",
            'Uniform initialization syntax',
            "25",
            'easy'
        );

        $this->createExample($section1, $cppLanguage, 'Primitive Data Types', 5,
            "| Data Type | Size         | Range            |\n| --------- | ------------ | ---------------- |\n| int       | 4 bytes      | -231 to 231-1  |\n| float     | 4 bytes      | N/A            |\n| double    | 8 bytes      | N/A            |\n| char      | 1 byte       | -128 to 127     |\n| bool      | 1 byte       | true / false    |\n| void      | N/A          | N/A            |\n| wchar_t   | 2 or 4 bytes | 1 wide character |",
            'Primitive data types table',
            null,
            'easy'
        );

        $this->createExample($section1, $cppLanguage, 'User Input', 6,
            "int num;\n\nstd::cout << \"Type a number: \";\nstd::cin >> num;\n\nstd::cout << \"You entered \" << num;",
            'Reading user input',
            null,
            'easy'
        );

        $this->createExample($section1, $cppLanguage, 'Swap', 7,
            "int a = 5, b = 10;\nstd::swap(a, b);\n\n// Outputs: a=10, b=5\nstd::cout << \"a=\" << a << \", b=\" << b;",
            'Swapping variables',
            "a=10, b=5",
            'easy'
        );

        $this->createExample($section1, $cppLanguage, 'Comments', 8,
            "// A single one line comment in C++\n\n/* This is a multiple line comment\n   in C++ */",
            'Comment syntax',
            null,
            'easy'
        );

        $this->createExample($section1, $cppLanguage, 'If statement', 9,
            "if (a == 10) {\n    // do something\n}",
            'Basic if statement',
            null,
            'easy'
        );

        $this->createExample($section1, $cppLanguage, 'Loops', 10,
            "for (int i = 0; i < 10; i++) {\n    std::cout << i << \"\\n\";\n}",
            'For loop',
            "0\n1\n2\n3\n4\n5\n6\n7\n8\n9",
            'easy'
        );

        $this->createExample($section1, $cppLanguage, 'Functions', 11,
            "#include <iostream>\n \nvoid hello(); // Declaring\n \nint main() {  // main function\n    hello();    // Calling\n}\n \nvoid hello() { // Defining\n    std::cout << \"Hello QuickRef!\\n\";\n}",
            'Function declaration and definition',
            "Hello QuickRef!\n",
            'easy'
        );

        $this->createExample($section1, $cppLanguage, 'References', 12,
            "int i = 1;\nint& ri = i; // ri is a reference to i\n\nri = 2; // i is now changed to 2\nstd::cout << \"i=\" << i;\n\ni = 3;   // i is now changed to 3\nstd::cout << \"ri=\" << ri;",
            'References - aliases for variables',
            "i=2\nri=3",
            'medium'
        );

        $this->createExample($section1, $cppLanguage, 'Namespaces', 13,
            "#include <iostream>\nnamespace ns1 {int val(){return 5;}}\nint main()\n{\n    std::cout << ns1::val();\n}",
            'Namespace usage',
            "5",
            'easy'
        );

        $this->createExample($section1, $cppLanguage, 'Using namespace', 14,
            "#include <iostream>\nnamespace ns1 {int val(){return 5;}}\nusing namespace ns1;\nusing namespace std;\nint main()\n{\n    cout << val(); \n}",
            'Using namespace directive',
            "5",
            'easy'
        );

        // Section 2: C++ Arrays
        $section2 = $this->createSection($cppLanguage, 'C++ Arrays', 2, 'Array operations');

        $this->createExample($section2, $cppLanguage, 'Declaration', 1,
            "std::array<int, 3> marks; // Definition\nmarks[0] = 92;\nmarks[1] = 97;\nmarks[2] = 98;\n\n// Define and initialize\nstd::array<int, 3> = {92, 97, 98};\n\n// With empty members\nstd::array<int, 3> marks = {92, 97};\nstd::cout << marks[2]; // Outputs: 0",
            'Array declaration and initialization',
            "0",
            'easy'
        );

        $this->createExample($section2, $cppLanguage, 'Manipulation', 2,
            "std::array<int, 6> marks = {92, 97, 98, 99, 98, 94};\n\n// Print first element\nstd::cout << marks[0];\n\n// Change 2th element to 99\nmarks[1] = 99;\n\n// Take input from the user\nstd::cin >> marks[2];",
            'Array manipulation',
            null,
            'easy'
        );

        $this->createExample($section2, $cppLanguage, 'Displaying', 3,
            "char ref[5] = {'R', 'e', 'f'};\n\n// Range based for loop\nfor (const int &n : ref) {\n    std::cout << std::string(1, n);\n}\n\n// Traditional for loop\nfor (int i = 0; i < sizeof(ref); ++i) {\n    std::cout << ref[i];\n}",
            'Displaying array elements',
            "Ref",
            'easy'
        );

        $this->createExample($section2, $cppLanguage, 'Multidimensional', 4,
            "int x[2][6] = {\n    {1,2,3,4,5,6}, {6,5,4,3,2,1}\n};\nfor (int i = 0; i < 2; ++i) {\n    for (int j = 0; j < 6; ++j) {\n        std::cout << x[i][j] << \" \";\n    }\n}\n// Outputs: 1 2 3 4 5 6 6 5 4 3 2 1",
            'Multidimensional arrays',
            "1 2 3 4 5 6 6 5 4 3 2 1",
            'medium'
        );

        // Section 3: C++ Conditionals
        $section3 = $this->createSection($cppLanguage, 'C++ Conditionals', 3, 'Conditional statements');

        $this->createExample($section3, $cppLanguage, 'If Clause', 1,
            "if (a == 10) {\n    // do something\n}",
            'Basic if statement',
            null,
            'easy'
        );

        $this->createExample($section3, $cppLanguage, 'If-Else', 2,
            "int number = 16;\n\nif (number % 2 == 0)\n{\n    std::cout << \"even\";\n}\nelse\n{\n    std::cout << \"odd\";\n}\n\n// Outputs: even",
            'If-else statement',
            "even",
            'easy'
        );

        $this->createExample($section3, $cppLanguage, 'Else if Statement', 3,
            "int score = 99;\nif (score == 100) {\n    std::cout << \"Superb\";\n}\nelse if (score >= 90) {\n    std::cout << \"Excellent\";\n}\nelse if (score >= 80) {\n    std::cout << \"Very Good\";\n}\nelse if (score >= 70) {\n    std::cout << \"Good\";\n}\nelse if (score >= 60)\n    std::cout << \"OK\";\nelse\n    std::cout << \"What?\";",
            'Else-if chain',
            "Excellent",
            'easy'
        );

        $this->createExample($section3, $cppLanguage, 'Relational Operators', 4,
            "| a == b | a is equal to b              |\n| a != b | a is NOT equal to b          |\n| a < b  | a is less than b             |\n| a > b  | a is greater b               |\n| a <= b | a is less than or equal to b |\n| a >= b | a is greater or equal to b   |",
            'Relational operators table',
            null,
            'easy'
        );

        $this->createExample($section3, $cppLanguage, 'Assignment Operators', 5,
            "| Example | Equivalent to    |\n| ------- | ---------------- |\n| a += b  | Aka a = a + b  |\n| a -= b  | Aka a = a - b  |\n| a *= b  | Aka a = a * b  |\n| a /= b  | Aka a = a / b  |\n| a %= b  | Aka a = a % b  |",
            'Assignment operators table',
            null,
            'easy'
        );

        $this->createExample($section3, $cppLanguage, 'Logical Operators', 6,
            "| Example       | Meaning               |\n| ------------- | --------------------- |\n| exp1 && exp2  | Both are true (AND) |\n| exp1 || exp2 | Either is true (OR) |\n| !exp          | exp is false (NOT)  |",
            'Logical operators table',
            null,
            'easy'
        );

        $this->createExample($section3, $cppLanguage, 'Bitwise Operators', 7,
            "| Operator | Description             |\n| -------- | ----------------------- |\n| a & b    | Binary AND              |\n| a | b    | Binary OR               |\n| a ^ b    | Binary XOR              |\n| ~ a      | Binary One's Complement |\n| a << b   | Binary Shift Left       |\n| a >> b   | Binary Shift Right      |",
            'Bitwise operators table',
            null,
            'medium'
        );

        $this->createExample($section3, $cppLanguage, 'Ternary Operator', 8,
            "int x = 3, y = 5, max;\nmax = (x > y) ? x : y;\n\n// Outputs: 5\nstd::cout << max << std::endl;",
            'Ternary conditional operator',
            "5",
            'easy'
        );

        // Section 4: C++ Loops
        $section4 = $this->createSection($cppLanguage, 'C++ Loops', 4, 'Looping constructs');

        $this->createExample($section4, $cppLanguage, 'For Loop', 1,
            "for (int i = 0; i < 10; i++) {\n    std::cout << i << \"\\n\";\n}",
            'Basic for loop',
            "0\n1\n2\n3\n4\n5\n6\n7\n8\n9",
            'easy'
        );

        $this->createExample($section4, $cppLanguage, 'While Loop', 2,
            "int i = 0;\nwhile (i < 5) {\n    std::cout << i << \" \";\n    i++;\n}",
            'While loop',
            "0 1 2 3 4",
            'easy'
        );

        $this->createExample($section4, $cppLanguage, 'Do-While Loop', 3,
            "int i = 0;\ndo {\n    std::cout << i << \" \";\n    i++;\n} while (i < 5);",
            'Do-while loop',
            "0 1 2 3 4",
            'easy'
        );

        $this->createExample($section4, $cppLanguage, 'Range-based For Loop', 4,
            "std::array<int, 5> arr = {1, 2, 3, 4, 5};\nfor (int x : arr) {\n    std::cout << x << \" \";\n}",
            'Range-based for loop (C++11)',
            "1 2 3 4 5",
            'easy'
        );

        $this->createExample($section4, $cppLanguage, 'Break and Continue', 5,
            "for (int i = 0; i < 10; i++) {\n    if (i == 5) break;\n    if (i % 2 == 0) continue;\n    std::cout << i << \" \";\n}",
            'Break and continue statements',
            "1 3",
            'easy'
        );

        // Section 5: C++ Functions
        $section5 = $this->createSection($cppLanguage, 'C++ Functions', 5, 'Function definitions and usage');

        $this->createExample($section5, $cppLanguage, 'Function Declaration', 1,
            "#include <iostream>\n \nvoid hello(); // Declaring\n \nint main() {  // main function\n    hello();    // Calling\n}\n \nvoid hello() { // Defining\n    std::cout << \"Hello QuickRef!\\n\";\n}",
            'Function declaration and definition',
            "Hello QuickRef!\n",
            'easy'
        );

        $this->createExample($section5, $cppLanguage, 'Function Parameters', 2,
            "int add(int a, int b) {\n    return a + b;\n}\n\nint result = add(5, 3);\nstd::cout << result; // Outputs: 8",
            'Function with parameters',
            "8",
            'easy'
        );

        $this->createExample($section5, $cppLanguage, 'Default Parameters', 3,
            "int multiply(int a, int b = 2) {\n    return a * b;\n}\n\nmultiply(5);    // Returns 10\nmultiply(5, 3); // Returns 15",
            'Default function parameters',
            null,
            'easy'
        );

        $this->createExample($section5, $cppLanguage, 'Function Overloading', 4,
            "int add(int a, int b) {\n    return a + b;\n}\n\ndouble add(double a, double b) {\n    return a + b;\n}",
            'Function overloading',
            null,
            'medium'
        );

        $this->createExample($section5, $cppLanguage, 'Pass by Reference', 5,
            "void swap(int& a, int& b) {\n    int temp = a;\n    a = b;\n    b = temp;\n}\n\nint x = 5, y = 10;\nswap(x, y);\n// x = 10, y = 5",
            'Passing parameters by reference',
            null,
            'medium'
        );

        $this->createExample($section5, $cppLanguage, 'Pass by Pointer', 6,
            "void increment(int* ptr) {\n    (*ptr)++;\n}\n\nint num = 5;\nincrement(&num);\n// num is now 6",
            'Passing parameters by pointer',
            null,
            'medium'
        );

        // Section 6: C++ Classes
        $section6 = $this->createSection($cppLanguage, 'C++ Classes', 6, 'Object-oriented programming');

        $this->createExample($section6, $cppLanguage, 'Class Definition', 1,
            "class MyClass {\n  public:             // Access specifier\n    int myNum;        // Attribute (int variable)\n    string myString;  // Attribute (string variable)\n};",
            'Basic class definition',
            null,
            'easy'
        );

        $this->createExample($section6, $cppLanguage, 'Creating an Object', 2,
            "MyClass myObj;  // Create an object of MyClass\n\nmyObj.myNum = 15;          // Set the value of myNum to 15\nmyObj.myString = \"Hello\";  // Set the value of myString to \"Hello\"\n\ncout << myObj.myNum << endl;         // Output 15\ncout << myObj.myString << endl;      // Output \"Hello\"",
            'Creating and using objects',
            "15\nHello",
            'easy'
        );

        $this->createExample($section6, $cppLanguage, 'Constructors', 3,
            "class MyClass {\n  public:\n    int myNum;\n    string myString;\n    MyClass() {  // Constructor\n      myNum = 0;\n      myString = \"\";\n    }\n};\n\nMyClass myObj;  // Create an object of MyClass\n\ncout << myObj.myNum << endl;         // Output 0\ncout << myObj.myString << endl;      // Output \"\"",
            'Class constructor',
            "0\n",
            'easy'
        );

        $this->createExample($section6, $cppLanguage, 'Destructors', 4,
            "class MyClass {\n  public:\n    int myNum;\n    string myString;\n    MyClass() {  // Constructor\n      myNum = 0;\n      myString = \"\";\n    }\n    ~MyClass() {  // Destructor\n      cout << \"Object destroyed.\" << endl;\n    }\n};\n\nMyClass myObj;  // Create an object of MyClass\n\n// Code here...\n\n// Object is destroyed automatically when the program exits the scope",
            'Class destructor',
            "Object destroyed.",
            'medium'
        );

        $this->createExample($section6, $cppLanguage, 'Class Methods', 5,
            "class MyClass {\n  public:\n    int myNum;\n    string myString;\n    void myMethod() {  // Method/function defined inside the class\n      cout << \"Hello World!\" << endl;\n    }\n};\n\nMyClass myObj;  // Create an object of MyClass\nmyObj.myMethod();  // Call the method",
            'Class methods',
            "Hello World!",
            'easy'
        );

        $this->createExample($section6, $cppLanguage, 'Access Modifiers', 6,
            "class MyClass {\n  public:     // Public access specifier\n    int x;    // Public attribute\n  private:    // Private access specifier\n    int y;    // Private attribute\n  protected:  // Protected access specifier\n    int z;    // Protected attribute\n};\n\nMyClass myObj;\nmyObj.x = 25;  // Allowed (public)\nmyObj.y = 50;  // Not allowed (private)\nmyObj.z = 75;  // Not allowed (protected)",
            'Access modifiers',
            null,
            'easy'
        );

        $this->createExample($section6, $cppLanguage, 'Getters and Setters', 7,
            "class MyClass {\n  private:\n    int myNum;\n  public:\n    void setMyNum(int num) {  // Setter\n      myNum = num;\n    }\n    int getMyNum() {  // Getter\n      return myNum;\n    }\n};\n\nMyClass myObj;\nmyObj.setMyNum(15);  // Set the value of myNum to 15\ncout << myObj.getMyNum() << endl;  // Output 15",
            'Getter and setter methods',
            "15",
            'easy'
        );

        $this->createExample($section6, $cppLanguage, 'Inheritance', 8,
            "class Vehicle {\n  public:\n    string brand = \"Ford\";\n    void honk() {\n      cout << \"Tuut, tuut!\" << endl;\n    }\n};\n\nclass Car : public Vehicle {\n  public:\n    string model = \"Mustang\";\n};\n\nCar myCar;\nmyCar.honk();  // Output \"Tuut, tuut!\"\ncout << myCar.brand + \" \" + myCar.model << endl;  // Output \"Ford Mustang\"",
            'Class inheritance',
            "Tuut, tuut!\nFord Mustang",
            'medium'
        );

        // Section 7: C++ Preprocessor
        $section7 = $this->createSection($cppLanguage, 'C++ Preprocessor', 7, 'Preprocessor directives');

        $this->createExample($section7, $cppLanguage, 'Includes', 1,
            "#include \"iostream\"\n#include <iostream>",
            'Include directives',
            null,
            'easy'
        );

        $this->createExample($section7, $cppLanguage, 'Defines', 2,
            "#define FOO\n#define FOO \"hello\"\n\n#undef FOO",
            'Define and undefine macros',
            null,
            'easy'
        );

        $this->createExample($section7, $cppLanguage, 'If', 3,
            "#ifdef DEBUG\n  console.log('hi');\n#elif defined VERBOSE\n  ...\n#else\n  ...\n#endif",
            'Conditional compilation',
            null,
            'medium'
        );

        $this->createExample($section7, $cppLanguage, 'Error', 4,
            "#if VERSION == 2.0\n  #error Unsupported\n  #warning Not really supported\n#endif",
            'Error and warning directives',
            null,
            'medium'
        );

        $this->createExample($section7, $cppLanguage, 'Macro', 5,
            "#define DEG(x) ((x) * 57.29)",
            'Function-like macro',
            null,
            'medium'
        );

        $this->createExample($section7, $cppLanguage, 'Token concat', 6,
            "#define DST(name) name##_s name##_t\nDST(object);   #=> object_s object_t;",
            'Token concatenation',
            null,
            'medium'
        );

        $this->createExample($section7, $cppLanguage, 'Stringification', 7,
            "#define STR(name) #name\nchar * a = STR(object);   #=> char * a = \"object\";",
            'Stringification macro',
            null,
            'medium'
        );

        $this->createExample($section7, $cppLanguage, '__FILE__ and __LINE__', 8,
            "#define LOG(msg) console.log(__FILE__, __LINE__, msg)\n#=> console.log(\"file.txt\", 3, \"hey\")",
            'File and line macros',
            null,
            'medium'
        );

        // Section 8: Miscellaneous
        $section8 = $this->createSection($cppLanguage, 'Miscellaneous', 8, 'Additional C++ features');

        $this->createExample($section8, $cppLanguage, 'Escape Sequences', 1,
            "| Escape Sequences | Characters            |\n| ---------------- | --------------------- |\n| \\b              | Backspace             |\n| \\f              | Form feed             |\n| \\n              | Newline               |\n| \\r              | Return                |\n| \\t              | Horizontal tab        |\n| \\v              | Vertical tab          |\n| \\\\              | Backslash             |\n| \\'              | Single quotation mark |\n| \\\"              | Double quotation mark |\n| \\?              | Question mark         |\n| \\0              | Null Character        |",
            'Escape sequences table',
            null,
            'easy'
        );

        $this->createExample($section8, $cppLanguage, 'Keywords', 2,
            "alignas, alignof, and, and_eq, asm, auto, bitand, bitor, bool, break, case, catch, char, char8_t, char16_t, char32_t, class, compl, concept, const, consteval, constexpr, constinit, const_cast, continue, co_await, co_return, co_yield, decltype, default, delete, do, double, dynamic_cast, else, enum, explicit, export, extern, false, float, for, friend, goto, if, inline, int, long, mutable, namespace, new, noexcept, not, not_eq, nullptr, operator, or, or_eq, private, protected, public, reflexpr, register, reinterpret_cast, requires, return, short, signed, sizeof, static, static_assert, static_cast, struct, switch, synchronized, template, this, thread_local, throw, true, try, typedef, typeid, typename, union, unsigned, using, virtual, void, volatile, wchar_t, while, xor, xor_eq, final, override",
            'C++ keywords list',
            null,
            'easy'
        );

        // Update counts
        $this->updateLanguageCounts($cppLanguage);
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
        if (str_contains($titleLower, 'class') || str_contains($titleLower, 'inheritance') || str_contains($descLower, 'oop')) {
            $tags[] = 'oop';
        }
        if (str_contains($titleLower, 'array') || str_contains($descLower, 'array')) {
            $tags[] = 'array';
        }
        if (str_contains($titleLower, 'function') || str_contains($descLower, 'function')) {
            $tags[] = 'function';
        }
        if (str_contains($titleLower, 'loop') || str_contains($titleLower, 'for') || str_contains($titleLower, 'while')) {
            $tags[] = 'loop';
        }
        if (str_contains($titleLower, 'conditional') || str_contains($titleLower, 'if') || str_contains($titleLower, 'switch')) {
            $tags[] = 'conditional';
        }
        if (str_contains($titleLower, 'pointer') || str_contains($titleLower, 'reference') || str_contains($titleLower, '&') || str_contains($titleLower, '*')) {
            $tags[] = 'pointer';
        }
        if (str_contains($titleLower, 'preprocessor') || str_contains($titleLower, 'macro') || str_contains($titleLower, '#define')) {
            $tags[] = 'preprocessor';
        }
        if (str_contains($titleLower, 'operator') || str_contains($titleLower, 'bitwise') || str_contains($titleLower, 'logical')) {
            $tags[] = 'operator';
        }
        if (str_contains($titleLower, 'namespace')) {
            $tags[] = 'namespace';
        }
        if (str_contains($titleLower, 'constructor') || str_contains($titleLower, 'destructor')) {
            $tags[] = 'constructor';
        }

        // Add basic tags
        $tags[] = 'cpp';
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

