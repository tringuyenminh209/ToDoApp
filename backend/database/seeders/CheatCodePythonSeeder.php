<?php

namespace Database\Seeders;

use App\Models\CheatCodeLanguage;
use App\Models\CheatCodeSection;
use App\Models\CodeExample;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CheatCodePythonSeeder extends Seeder
{
    /**
     * Seed Python cheat code data from quickref.me
     * Reference: https://quickref.me/python
     */
    public function run(): void
    {
        // Create Python Language
        $pythonLanguage = CheatCodeLanguage::create([
            'name' => 'python',
            'display_name' => 'Python',
            'slug' => 'python',
            'color' => '#3776AB',
            'description' => 'The Python cheat sheet is a one-page reference sheet for the Python 3 programming language.',
            'category' => 'programming',
            'popularity' => 98,
            'is_active' => true,
            'sort_order' => 4,
        ]);

        // Section 1: Getting Started
        $section1 = $this->createSection($pythonLanguage, 'Getting Started', 1, 'Python basics and introduction');

        $this->createExample($section1, $pythonLanguage, 'Hello World', 1,
            ">>> print(\"Hello, World!\")\nHello, World!",
            'The famous "Hello World" program in Python',
            "Hello, World!",
            'easy'
        );

        $this->createExample($section1, $pythonLanguage, 'Variables', 2,
            "age = 18      # age is of type int\nname = \"John\" # name is now of type str\nprint(name)",
            'Python can\'t declare a variable without assignment',
            "John",
            'easy'
        );

        $this->createExample($section1, $pythonLanguage, 'Data Types', 3,
            "| str                          | Text     |\n| ---------------------------- | -------- |\n| int, float, complex          | Numeric  |\n| list, tuple, range           | Sequence |\n| dict                         | Mapping  |\n| set, frozenset               | Set      |\n| bool                         | Boolean  |\n| bytes, bytearray, memoryview | Binary   |",
            'Python built-in data types',
            null,
            'easy'
        );

        $this->createExample($section1, $pythonLanguage, 'Slicing String', 4,
            ">>> msg = \"Hello, World!\"\n>>> print(msg[2:5])\nllo",
            'String slicing',
            "llo",
            'easy'
        );

        $this->createExample($section1, $pythonLanguage, 'Lists', 5,
            "mylist = []\nmylist.append(1)\nmylist.append(2)\nfor item in mylist:\n    print(item) # prints out 1,2",
            'List operations',
            "1\n2",
            'easy'
        );

        $this->createExample($section1, $pythonLanguage, 'If Else', 6,
            "num = 200\nif num > 0:\n    print(\"num is greater than 0\")\nelse:\n    print(\"num is not greater than 0\")",
            'If-else statement',
            "num is greater than 0",
            'easy'
        );

        $this->createExample($section1, $pythonLanguage, 'Loops', 7,
            "for item in range(6):\n    if item == 3: break\n    print(item)\nelse:\n    print(\"Finally finished!\")",
            'For loop with break and else',
            "0\n1\n2",
            'easy'
        );

        $this->createExample($section1, $pythonLanguage, 'Functions', 8,
            ">>> def my_function():\n...     print(\"Hello from a function\")\n...\n>>> my_function()\nHello from a function",
            'Function definition and call',
            "Hello from a function",
            'easy'
        );

        $this->createExample($section1, $pythonLanguage, 'File Handling', 9,
            "with open(\"myfile.txt\", \"r\", encoding='utf8') as file:\n    for line in file:\n        print(line)",
            'Reading file line by line',
            null,
            'easy'
        );

        $this->createExample($section1, $pythonLanguage, 'Arithmetic', 10,
            "result = 10 + 30 # => 40\nresult = 40 - 10 # => 30\nresult = 50 * 5  # => 250\nresult = 16 / 4  # => 4.0 (Float Division)\nresult = 16 // 4 # => 4 (Integer Division)\nresult = 25 % 2  # => 1\nresult = 5 ** 3  # => 125",
            'Arithmetic operators',
            null,
            'easy'
        );

        $this->createExample($section1, $pythonLanguage, 'Plus-Equals', 11,
            "counter = 0\ncounter += 10           # => 10\ncounter = 0\ncounter = counter + 10  # => 10\n\nmessage = \"Part 1.\"\n\n# => Part 1.Part 2.\nmessage += \"Part 2.\"",
            'Augmented assignment operators',
            null,
            'easy'
        );

        $this->createExample($section1, $pythonLanguage, 'f-Strings (Python 3.6+)', 12,
            ">>> website = 'Quickref.ME'\n>>> f\"Hello, {website}\"\n\"Hello, Quickref.ME\"\n\n>>> num = 10\n>>> f'{num} + 10 = {num + 10}'\n'10 + 10 = 20'",
            'Formatted string literals',
            "Hello, Quickref.ME\n10 + 10 = 20",
            'easy'
        );

        // Section 2: Python Built-in Data Types
        $section2 = $this->createSection($pythonLanguage, 'Python Built-in Data Types', 2, 'Basic data types in Python');

        $this->createExample($section2, $pythonLanguage, 'Strings', 1,
            "hello = \"Hello World\"\nhello = 'Hello World'\n\nmulti_string = \"\"\"Multiline Strings\nLorem ipsum dolor sit amet,\nconsectetur adipiscing elit \"\"\"",
            'String declarations',
            null,
            'easy'
        );

        $this->createExample($section2, $pythonLanguage, 'Numbers', 2,
            "x = 1    # int\ny = 2.8  # float\nz = 1j   # complex\n\n>>> print(type(x))\n<class 'int'>",
            'Numeric types',
            "<class 'int'>",
            'easy'
        );

        $this->createExample($section2, $pythonLanguage, 'Booleans', 3,
            "my_bool = True \nmy_bool = False\n\nbool(0)     # => False\nbool(1)     # => True",
            'Boolean type and conversion',
            null,
            'easy'
        );

        $this->createExample($section2, $pythonLanguage, 'Lists', 4,
            "list1 = [\"apple\", \"banana\", \"cherry\"]\nlist2 = [True, False, False]\nlist3 = [1, 5, 7, 9, 3]\nlist4 = list((1, 5, 7, 9, 3))",
            'List creation',
            null,
            'easy'
        );

        $this->createExample($section2, $pythonLanguage, 'Tuple', 5,
            "my_tuple = (1, 2, 3)\nmy_tuple = tuple((1, 2, 3))",
            'Tuple creation - immutable sequence',
            null,
            'easy'
        );

        $this->createExample($section2, $pythonLanguage, 'Set', 6,
            "set1 = {\"a\", \"b\", \"c\"}   \nset2 = set((\"a\", \"b\", \"c\"))",
            'Set creation - unique items',
            null,
            'easy'
        );

        $this->createExample($section2, $pythonLanguage, 'Dictionary', 7,
            ">>> empty_dict = {}\n>>> a = {\"one\": 1, \"two\": 2, \"three\": 3}\n>>> a[\"one\"]\n1\n>>> a.keys()\ndict_keys(['one', 'two', 'three'])\n>>> a.values()\ndict_values([1, 2, 3])\n>>> a.update({\"four\": 4})\n>>> a.keys()\ndict_keys(['one', 'two', 'three', 'four'])\n>>> a['four']\n4",
            'Dictionary operations',
            "1\ndict_keys(['one', 'two', 'three'])\ndict_values([1, 2, 3])\ndict_keys(['one', 'two', 'three', 'four'])\n4",
            'easy'
        );

        $this->createExample($section2, $pythonLanguage, 'Casting - Integers', 8,
            "x = int(1)   # x will be 1\ny = int(2.8) # y will be 2\nz = int(\"3\") # z will be 3",
            'Type casting to integer',
            null,
            'easy'
        );

        $this->createExample($section2, $pythonLanguage, 'Casting - Floats', 9,
            "x = float(1)     # x will be 1.0\ny = float(2.8)   # y will be 2.8\nz = float(\"3\")   # z will be 3.0\nw = float(\"4.2\") # w will be 4.2",
            'Type casting to float',
            null,
            'easy'
        );

        $this->createExample($section2, $pythonLanguage, 'Casting - Strings', 10,
            "x = str(\"s1\") # x will be 's1'\ny = str(2)    # y will be '2'\nz = str(3.0)  # z will be '3.0'",
            'Type casting to string',
            null,
            'easy'
        );

        // Section 3: Python Advanced Data Types
        $section3 = $this->createSection($pythonLanguage, 'Python Advanced Data Types', 3, 'Advanced data structures');

        $this->createExample($section3, $pythonLanguage, 'Heaps', 1,
            "import heapq\n\nmyList = [9, 5, 4, 1, 3, 2]\nheapq.heapify(myList) # turn myList into a Min Heap\nprint(myList)    # => [1, 3, 2, 5, 9, 4]\nprint(myList[0]) # first value is always the smallest in the heap\n\nheapq.heappush(myList, 10) # insert 10\nx = heapq.heappop(myList)  # pop and return smallest item\nprint(x)                   # => 1",
            'Heap operations',
            "[1, 3, 2, 5, 9, 4]\n1\n1",
            'medium'
        );

        $this->createExample($section3, $pythonLanguage, 'Max Heap using Min Heap', 2,
            "myList = [9, 5, 4, 1, 3, 2]\nmyList = [-val for val in myList] # multiply by -1 to negate\nheapq.heapify(myList)\n\nx = heapq.heappop(myList)\nprint(-x) # => 9 (making sure to multiply by -1 again)",
            'Using min heap as max heap',
            "9",
            'medium'
        );

        $this->createExample($section3, $pythonLanguage, 'Stacks and Queues', 3,
            "from collections import deque\n\nq = deque()          # empty\nq = deque([1, 2, 3]) # with values\n\nq.append(4)     # append to right side\nq.appendleft(0) # append to left side\nprint(q)    # => deque([0, 1, 2, 3, 4])\n\nx = q.pop() # remove & return from right\ny = q.popleft() # remove & return from left\nprint(x)    # => 4\nprint(y)    # => 0\nprint(q)    # => deque([1, 2, 3])\n\nq.rotate(1) # rotate 1 step to the right\nprint(q)    # => deque([3, 1, 2])",
            'Deque for stacks and queues',
            "deque([0, 1, 2, 3, 4])\n4\n0\ndeque([1, 2, 3])\ndeque([3, 1, 2])",
            'medium'
        );

        // Section 4: Python Strings
        $section4 = $this->createSection($pythonLanguage, 'Python Strings', 4, 'String operations');

        $this->createExample($section4, $pythonLanguage, 'Array-like', 1,
            ">>> hello = \"Hello, World\"\n>>> print(hello[1])\ne\n>>> print(hello[-1])\nd",
            'Accessing string characters',
            "e\nd",
            'easy'
        );

        $this->createExample($section4, $pythonLanguage, 'Looping', 2,
            ">>> for char in \"foo\":\n...     print(char)\nf\no\no",
            'Looping through string',
            "f\no\no",
            'easy'
        );

        $this->createExample($section4, $pythonLanguage, 'Slicing string', 3,
            ">>> msg = \"Hello, World!\"\n>>> print(msg[2:5])\nllo\n>>> print(msg[:5])\nHello\n>>> print(msg[7:])\nWorld!\n>>> print(msg[-6:-1])\nWorld",
            'String slicing examples',
            "llo\nHello\nWorld!\nWorld",
            'easy'
        );

        $this->createExample($section4, $pythonLanguage, 'String Methods', 4,
            "text = \"Hello World\"\nprint(text.upper())      # => HELLO WORLD\nprint(text.lower())      # => hello world\nprint(text.strip())      # => Hello World\nprint(text.replace(\"H\", \"J\"))  # => Jello World\nprint(text.split(\" \"))   # => ['Hello', 'World']",
            'Common string methods',
            "HELLO WORLD\nhello world\nHello World\nJello World\n['Hello', 'World']",
            'easy'
        );

        // Section 5: Python Functions
        $section5 = $this->createSection($pythonLanguage, 'Python Functions', 5, 'Function definitions and usage');

        $this->createExample($section5, $pythonLanguage, 'Positional arguments', 1,
            "def positional_args(x, y, z):\n    return x, y, z\n\n# => (1, 2, 3)\npositional_args(1, 2, 3)",
            'Positional function arguments',
            "(1, 2, 3)",
            'easy'
        );

        $this->createExample($section5, $pythonLanguage, 'Keyword arguments', 2,
            "def keyword_args(**kwargs):\n    return kwargs\n\n# => {\"big\": \"foot\", \"loch\": \"ness\"}\nkeyword_args(big=\"foot\", loch=\"ness\")",
            'Keyword arguments with **kwargs',
            "{\"big\": \"foot\", \"loch\": \"ness\"}",
            'medium'
        );

        $this->createExample($section5, $pythonLanguage, 'Returning multiple', 3,
            "def swap(x, y):\n    return y, x\n\nx = 1\ny = 2\nx, y = swap(x, y)  # => x = 2, y = 1",
            'Returning multiple values',
            null,
            'easy'
        );

        $this->createExample($section5, $pythonLanguage, 'Default Value', 4,
            "def add(x, y=10):\n    return x + y\n\nadd(5)      # => 15\nadd(5, 20)  # => 25",
            'Default parameter values',
            "15\n25",
            'easy'
        );

        $this->createExample($section5, $pythonLanguage, 'Anonymous functions', 5,
            "# => True\n(lambda x: x > 2)(3)\n\n# => 5\n(lambda x, y: x ** 2 + y ** 2)(2, 1)",
            'Lambda functions',
            "True\n5",
            'medium'
        );

        // Section 6: Python Modules
        $section6 = $this->createSection($pythonLanguage, 'Python Modules', 6, 'Importing and using modules');

        $this->createExample($section6, $pythonLanguage, 'Import modules', 1,
            "import math\nprint(math.sqrt(16))  # => 4.0",
            'Importing entire module',
            "4.0",
            'easy'
        );

        $this->createExample($section6, $pythonLanguage, 'From a module', 2,
            "from math import ceil, floor\nprint(ceil(3.7))   # => 4.0\nprint(floor(3.7))  # => 3.0",
            'Importing specific functions',
            "4.0\n3.0",
            'easy'
        );

        $this->createExample($section6, $pythonLanguage, 'Import all', 3,
            "from math import *",
            'Importing all from module',
            null,
            'easy'
        );

        $this->createExample($section6, $pythonLanguage, 'Shorten module', 4,
            "import math as m\n\n# => True\nmath.sqrt(16) == m.sqrt(16)",
            'Module aliasing',
            null,
            'easy'
        );

        $this->createExample($section6, $pythonLanguage, 'Functions and attributes', 5,
            "import math\ndir(math)",
            'Listing module functions and attributes',
            null,
            'easy'
        );

        // Section 7: Python File Handling
        $section7 = $this->createSection($pythonLanguage, 'Python File Handling', 7, 'Reading and writing files');

        $this->createExample($section7, $pythonLanguage, 'Read file - Line by line', 1,
            "with open(\"myfile.txt\") as file:\n    for line in file:\n        print(line)",
            'Reading file line by line',
            null,
            'easy'
        );

        $this->createExample($section7, $pythonLanguage, 'Read file - With line number', 2,
            "file = open('myfile.txt', 'r')\nfor i, line in enumerate(file, start=1):\n    print(\"Number %s: %s\" % (i, line))",
            'Reading file with line numbers',
            null,
            'easy'
        );

        $this->createExample($section7, $pythonLanguage, 'Write a string', 3,
            "contents = {\"aa\": 12, \"bb\": 21}\nwith open(\"myfile1.txt\", \"w+\") as file:\n    file.write(str(contents))",
            'Writing string to file',
            null,
            'easy'
        );

        $this->createExample($section7, $pythonLanguage, 'Read a string', 4,
            "with open('myfile1.txt', \"r+\") as file:\n    contents = file.read()\nprint(contents)",
            'Reading string from file',
            null,
            'easy'
        );

        $this->createExample($section7, $pythonLanguage, 'Write an object', 5,
            "contents = {\"aa\": 12, \"bb\": 21}\nwith open(\"myfile2.txt\", \"w+\") as file:\n    file.write(json.dumps(contents))",
            'Writing JSON object to file',
            null,
            'medium'
        );

        $this->createExample($section7, $pythonLanguage, 'Read an object', 6,
            "with open('myfile2.txt', \"r+\") as file:\n    contents = json.load(file)\nprint(contents)",
            'Reading JSON object from file',
            null,
            'medium'
        );

        $this->createExample($section7, $pythonLanguage, 'Delete a File', 7,
            "import os\nos.remove(\"myfile.txt\")",
            'Deleting a file',
            null,
            'easy'
        );

        $this->createExample($section7, $pythonLanguage, 'Check and Delete', 8,
            "import os\nif os.path.exists(\"myfile.txt\"):\n    os.remove(\"myfile.txt\")\nelse:\n    print(\"The file does not exist\")",
            'Checking file existence before deletion',
            null,
            'easy'
        );

        $this->createExample($section7, $pythonLanguage, 'Delete Folder', 9,
            "import os\nos.rmdir(\"myfolder\")",
            'Deleting a folder',
            null,
            'easy'
        );

        // Section 8: Python Classes & Inheritance
        $section8 = $this->createSection($pythonLanguage, 'Python Classes & Inheritance', 8, 'Object-oriented programming');

        $this->createExample($section8, $pythonLanguage, 'Defining', 1,
            "class MyNewClass:\n    pass\n\n# Class Instantiation\nmy = MyNewClass()",
            'Basic class definition',
            null,
            'easy'
        );

        $this->createExample($section8, $pythonLanguage, 'Constructors', 2,
            "class Animal:\n    def __init__(self, voice):\n        self.voice = voice\n \ncat = Animal('Meow')\nprint(cat.voice)    # => Meow\n \ndog = Animal('Woof') \nprint(dog.voice)    # => Woof",
            'Class constructor',
            "Meow\nWoof",
            'easy'
        );

        $this->createExample($section8, $pythonLanguage, 'Method', 3,
            "class Dog:\n\n    # Method of the class\n    def bark(self):\n        print(\"Ham-Ham\")\n \ncharlie = Dog()\ncharlie.bark()   # => \"Ham-Ham\"",
            'Class methods',
            "Ham-Ham",
            'easy'
        );

        $this->createExample($section8, $pythonLanguage, 'Class Variables', 4,
            "class MyClass:\n    class_variable = \"A class variable!\"\n\n# => A class variable!\nprint(MyClass.class_variable)\n\nx = MyClass()\n \n# => A class variable!\nprint(x.class_variable)",
            'Class variables',
            "A class variable!\nA class variable!",
            'easy'
        );

        $this->createExample($section8, $pythonLanguage, 'Super() Function', 5,
            "class ParentClass:\n    def print_test(self):\n        print(\"Parent Method\")\n \nclass ChildClass(ParentClass):\n    def print_test(self):\n        print(\"Child Method\")\n        # Calls the parent's print_test()\n        super().print_test()\n\n>>> child_instance = ChildClass()\n>>> child_instance.print_test()\nChild Method\nParent Method",
            'Using super() to call parent methods',
            "Child Method\nParent Method",
            'medium'
        );

        $this->createExample($section8, $pythonLanguage, '__repr__() method', 6,
            "class Employee:\n    def __init__(self, name):\n        self.name = name\n \n    def __repr__(self):\n        return self.name\n \njohn = Employee('John')\nprint(john)  # => John",
            'String representation method',
            "John",
            'medium'
        );

        $this->createExample($section8, $pythonLanguage, 'User-defined exceptions', 7,
            "class CustomError(Exception):\n    pass",
            'Custom exception classes',
            null,
            'medium'
        );

        $this->createExample($section8, $pythonLanguage, 'Polymorphism', 8,
            "class ParentClass:\n    def print_self(self):\n        print('A')\n \nclass ChildClass(ParentClass):\n    def print_self(self):\n        print('B')\n \nobj_A = ParentClass()\nobj_B = ChildClass()\n \nobj_A.print_self() # => A\nobj_B.print_self() # => B",
            'Polymorphism example',
            "A\nB",
            'medium'
        );

        $this->createExample($section8, $pythonLanguage, 'Overriding', 9,
            "class ParentClass:\n    def print_self(self):\n        print(\"Parent\")\n \nclass ChildClass(ParentClass):\n    def print_self(self):\n        print(\"Child\")\n \nchild_instance = ChildClass()\nchild_instance.print_self() # => Child",
            'Method overriding',
            "Child",
            'medium'
        );

        $this->createExample($section8, $pythonLanguage, 'Inheritance', 10,
            "class Animal: \n    def __init__(self, name, legs):\n        self.name = name\n        self.legs = legs\n        \nclass Dog(Animal):\n    def sound(self):\n        print(\"Woof!\")\n \nYoki = Dog(\"Yoki\", 4)\nprint(Yoki.name) # => YOKI\nprint(Yoki.legs) # => 4\nYoki.sound()     # => Woof!",
            'Class inheritance',
            "Yoki\n4\nWoof!",
            'medium'
        );

        // Section 9: Miscellaneous
        $section9 = $this->createSection($pythonLanguage, 'Miscellaneous', 9, 'Additional Python features');

        $this->createExample($section9, $pythonLanguage, 'Comments', 1,
            "# This is a single line comments.\n\n\"\"\" Multiline strings can be written\n    using three \"s, and are often used\n    as documentation.\n\"\"\"\n\n''' Multiline strings can be written\n    using three 's, and are often used\n    as documentation.\n'''",
            'Comment syntax',
            null,
            'easy'
        );

        $this->createExample($section9, $pythonLanguage, 'Generators', 2,
            "def double_numbers(iterable):\n    for i in iterable:\n        yield i + i",
            'Generator functions',
            null,
            'medium'
        );

        $this->createExample($section9, $pythonLanguage, 'Generator to list', 3,
            "values = (-x for x in [1,2,3,4,5])\ngen_to_list = list(values)\n\n# => [-1, -2, -3, -4, -5]\nprint(gen_to_list)",
            'Converting generator to list',
            "[-1, -2, -3, -4, -5]",
            'medium'
        );

        $this->createExample($section9, $pythonLanguage, 'Handle exceptions', 4,
            "try:\n    # Use \"raise\" to raise an error\n    raise IndexError(\"This is an index error\")\nexcept IndexError as e:\n    pass                 # Pass is just a no-op. Usually you would do recovery here.\nexcept (TypeError, NameError):\n    pass                 # Multiple exceptions can be handled together, if required.\nelse:                    # Optional clause to the try/except block. Must follow all except blocks\n    print(\"All good!\")   # Runs only if the code in try raises no exceptions\nfinally:                 # Execute under all circumstances\n    print(\"We can clean up resources here\")",
            'Exception handling with try-except-else-finally',
            "We can clean up resources here",
            'medium'
        );

        // Update counts
        $this->updateLanguageCounts($pythonLanguage);
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
        if (str_contains($titleLower, 'list') || str_contains($titleLower, 'array') || str_contains($descLower, 'list')) {
            $tags[] = 'list';
        }
        if (str_contains($titleLower, 'string') || str_contains($descLower, 'string')) {
            $tags[] = 'string';
        }
        if (str_contains($titleLower, 'function') || str_contains($titleLower, 'lambda') || str_contains($descLower, 'function')) {
            $tags[] = 'function';
        }
        if (str_contains($titleLower, 'file') || str_contains($descLower, 'file')) {
            $tags[] = 'file-handling';
        }
        if (str_contains($titleLower, 'module') || str_contains($titleLower, 'import') || str_contains($descLower, 'module')) {
            $tags[] = 'module';
        }
        if (str_contains($titleLower, 'exception') || str_contains($titleLower, 'error') || str_contains($titleLower, 'try')) {
            $tags[] = 'exception';
        }
        if (str_contains($titleLower, 'generator') || str_contains($titleLower, 'yield')) {
            $tags[] = 'generator';
        }
        if (str_contains($titleLower, 'dict') || str_contains($titleLower, 'dictionary')) {
            $tags[] = 'dictionary';
        }
        if (str_contains($titleLower, 'tuple') || str_contains($titleLower, 'set')) {
            $tags[] = 'data-structure';
        }
        if (str_contains($titleLower, 'heap') || str_contains($titleLower, 'deque') || str_contains($titleLower, 'queue')) {
            $tags[] = 'data-structure';
        }

        // Add basic tags
        $tags[] = 'python';
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

