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
     * Seed Python cheat code data from doleaf
     * Reference: https://doleaf.com/python
     */
    public function run(): void
    {
        // Create Python Language
        $pythonLanguage = CheatCodeLanguage::create([
            'name' => 'python',
            'display_name' => 'Python',
            'slug' => 'python',
            'icon' => 'ic_python',
            'color' => '#3776AB',
            'description' => 'Pythonチートシートは、Python 3プログラミング言語の1ページ参照シートです。',
            'category' => 'programming',
            'popularity' => 98,
            'is_active' => true,
            'sort_order' => 4,
        ]);

        // Section 1: Getting Started
        $section1 = $this->createSection($pythonLanguage, 'はじめに', 1, 'Pythonの基礎と導入', 'getting-started');

        $this->createExample($section1, $pythonLanguage, 'Hello World', 1,
            ">>> print(\"Hello, World!\")\nHello, World!",
            'Pythonで有名な「Hello World」プログラム',
            "Hello, World!",
            'easy'
        );

        $this->createExample($section1, $pythonLanguage, '変数', 2,
            "age = 18      # age is of type int\nname = \"John\" # name is now of type str\nprint(name)",
            'Pythonは代入なしで変数を宣言できません',
            "John",
            'easy'
        );

        $this->createExample($section1, $pythonLanguage, 'データ型', 3,
            "| str                          | Text     |\n| ---------------------------- | -------- |\n| int, float, complex          | Numeric  |\n| list, tuple, range           | Sequence |\n| dict                         | Mapping  |\n| set, frozenset               | Set      |\n| bool                         | Boolean  |\n| bytes, bytearray, memoryview | Binary   |",
            'Pythonの組み込みデータ型',
            null,
            'easy'
        );

        $this->createExample($section1, $pythonLanguage, '文字列スライス', 4,
            ">>> msg = \"Hello, World!\"\n>>> print(msg[2:5])\nllo",
            '文字列のスライス',
            "llo",
            'easy'
        );

        $this->createExample($section1, $pythonLanguage, 'リスト', 5,
            "mylist = []\nmylist.append(1)\nmylist.append(2)\nfor item in mylist:\n    print(item) # prints out 1,2",
            'リスト操作',
            "1\n2",
            'easy'
        );

        $this->createExample($section1, $pythonLanguage, 'If Else', 6,
            "num = 200\nif num > 0:\n    print(\"num is greater than 0\")\nelse:\n    print(\"num is not greater than 0\")",
            'If-else文',
            "num is greater than 0",
            'easy'
        );

        $this->createExample($section1, $pythonLanguage, 'ループ', 7,
            "for item in range(6):\n    if item == 3: break\n    print(item)\nelse:\n    print(\"Finally finished!\")",
            'breakとelseを使ったForループ',
            "0\n1\n2",
            'easy'
        );

        $this->createExample($section1, $pythonLanguage, '関数', 8,
            ">>> def my_function():\n...     print(\"Hello from a function\")\n...\n>>> my_function()\nHello from a function",
            '関数の定義と呼び出し',
            "Hello from a function",
            'easy'
        );

        $this->createExample($section1, $pythonLanguage, 'ファイル処理', 9,
            "with open(\"myfile.txt\", \"r\", encoding='utf8') as file:\n    for line in file:\n        print(line)",
            'ファイルを行ごとに読み込む',
            null,
            'easy'
        );

        $this->createExample($section1, $pythonLanguage, '算術演算', 10,
            "result = 10 + 30 # => 40\nresult = 40 - 10 # => 30\nresult = 50 * 5  # => 250\nresult = 16 / 4  # => 4.0 (Float Division)\nresult = 16 // 4 # => 4 (Integer Division)\nresult = 25 % 2  # => 1\nresult = 5 ** 3  # => 125",
            '算術演算子',
            null,
            'easy'
        );

        $this->createExample($section1, $pythonLanguage, '複合代入', 11,
            "counter = 0\ncounter += 10           # => 10\ncounter = 0\ncounter = counter + 10  # => 10\n\nmessage = \"Part 1.\"\n\n# => Part 1.Part 2.\nmessage += \"Part 2.\"",
            '複合代入演算子',
            null,
            'easy'
        );

        $this->createExample($section1, $pythonLanguage, 'f-文字列（Python 3.6+）', 12,
            ">>> website = 'Doleaf.com'\n>>> f\"Hello, {website}\"\n\"Hello, Doleaf.com\"\n\n>>> num = 10\n>>> f'{num} + 10 = {num + 10}'\n'10 + 10 = 20'",
            'フォーマット済み文字列リテラル',
            "Hello, Doleaf.com\n10 + 10 = 20",
            'easy'
        );

        // Section 2: Python Built-in Data Types
        $section2 = $this->createSection($pythonLanguage, 'Python組み込みデータ型', 2, 'Pythonの基本的なデータ型', 'python-built-in-data-types');

        $this->createExample($section2, $pythonLanguage, '文字列', 1,
            "hello = \"Hello World\"\nhello = 'Hello World'\n\nmulti_string = \"\"\"Multiline Strings\nLorem ipsum dolor sit amet,\nconsectetur adipiscing elit \"\"\"",
            '文字列の宣言',
            null,
            'easy'
        );

        $this->createExample($section2, $pythonLanguage, '数値', 2,
            "x = 1    # int\ny = 2.8  # float\nz = 1j   # complex\n\n>>> print(type(x))\n<class 'int'>",
            '数値型',
            "<class 'int'>",
            'easy'
        );

        $this->createExample($section2, $pythonLanguage, 'ブール型', 3,
            "my_bool = True \nmy_bool = False\n\nbool(0)     # => False\nbool(1)     # => True",
            'ブール型と変換',
            null,
            'easy'
        );

        $this->createExample($section2, $pythonLanguage, 'リスト', 4,
            "list1 = [\"apple\", \"banana\", \"cherry\"]\nlist2 = [True, False, False]\nlist3 = [1, 5, 7, 9, 3]\nlist4 = list((1, 5, 7, 9, 3))",
            'リストの作成',
            null,
            'easy'
        );

        $this->createExample($section2, $pythonLanguage, 'タプル', 5,
            "my_tuple = (1, 2, 3)\nmy_tuple = tuple((1, 2, 3))",
            'タプルの作成 - イミュータブルなシーケンス',
            null,
            'easy'
        );

        $this->createExample($section2, $pythonLanguage, 'セット', 6,
            "set1 = {\"a\", \"b\", \"c\"}   \nset2 = set((\"a\", \"b\", \"c\"))",
            'セットの作成 - ユニークな要素',
            null,
            'easy'
        );

        $this->createExample($section2, $pythonLanguage, '辞書', 7,
            ">>> empty_dict = {}\n>>> a = {\"one\": 1, \"two\": 2, \"three\": 3}\n>>> a[\"one\"]\n1\n>>> a.keys()\ndict_keys(['one', 'two', 'three'])\n>>> a.values()\ndict_values([1, 2, 3])\n>>> a.update({\"four\": 4})\n>>> a.keys()\ndict_keys(['one', 'two', 'three', 'four'])\n>>> a['four']\n4",
            '辞書操作',
            "1\ndict_keys(['one', 'two', 'three'])\ndict_values([1, 2, 3])\ndict_keys(['one', 'two', 'three', 'four'])\n4",
            'easy'
        );

        $this->createExample($section2, $pythonLanguage, 'キャスト - 整数', 8,
            "x = int(1)   # x will be 1\ny = int(2.8) # y will be 2\nz = int(\"3\") # z will be 3",
            '整数への型キャスト',
            null,
            'easy'
        );

        $this->createExample($section2, $pythonLanguage, 'キャスト - 浮動小数点', 9,
            "x = float(1)     # x will be 1.0\ny = float(2.8)   # y will be 2.8\nz = float(\"3\")   # z will be 3.0\nw = float(\"4.2\") # w will be 4.2",
            '浮動小数点への型キャスト',
            null,
            'easy'
        );

        $this->createExample($section2, $pythonLanguage, 'キャスト - 文字列', 10,
            "x = str(\"s1\") # x will be 's1'\ny = str(2)    # y will be '2'\nz = str(3.0)  # z will be '3.0'",
            '文字列への型キャスト',
            null,
            'easy'
        );

        // Section 3: Python Advanced Data Types
        $section3 = $this->createSection($pythonLanguage, 'Python高度なデータ型', 3, '高度なデータ構造', 'python-advanced-data-types');

        $this->createExample($section3, $pythonLanguage, 'ヒープ', 1,
            "import heapq\n\nmyList = [9, 5, 4, 1, 3, 2]\nheapq.heapify(myList) # turn myList into a Min Heap\nprint(myList)    # => [1, 3, 2, 5, 9, 4]\nprint(myList[0]) # first value is always the smallest in the heap\n\nheapq.heappush(myList, 10) # insert 10\nx = heapq.heappop(myList)  # pop and return smallest item\nprint(x)                   # => 1",
            'ヒープ操作',
            "[1, 3, 2, 5, 9, 4]\n1\n1",
            'medium'
        );

        $this->createExample($section3, $pythonLanguage, '最小ヒープを使った最大ヒープ', 2,
            "myList = [9, 5, 4, 1, 3, 2]\nmyList = [-val for val in myList] # multiply by -1 to negate\nheapq.heapify(myList)\n\nx = heapq.heappop(myList)\nprint(-x) # => 9 (making sure to multiply by -1 again)",
            '最小ヒープを最大ヒープとして使用',
            "9",
            'medium'
        );

        $this->createExample($section3, $pythonLanguage, 'スタックとキュー', 3,
            "from collections import deque\n\nq = deque()          # empty\nq = deque([1, 2, 3]) # with values\n\nq.append(4)     # append to right side\nq.appendleft(0) # append to left side\nprint(q)    # => deque([0, 1, 2, 3, 4])\n\nx = q.pop() # remove & return from right\ny = q.popleft() # remove & return from left\nprint(x)    # => 4\nprint(y)    # => 0\nprint(q)    # => deque([1, 2, 3])\n\nq.rotate(1) # rotate 1 step to the right\nprint(q)    # => deque([3, 1, 2])",
            'スタックとキューのためのDeque',
            "deque([0, 1, 2, 3, 4])\n4\n0\ndeque([1, 2, 3])\ndeque([3, 1, 2])",
            'medium'
        );

        // Section 4: Python Strings
        $section4 = $this->createSection($pythonLanguage, 'Python文字列', 4, '文字列操作', 'python-strings');

        $this->createExample($section4, $pythonLanguage, '配列風', 1,
            ">>> hello = \"Hello, World\"\n>>> print(hello[1])\ne\n>>> print(hello[-1])\nd",
            '文字列の文字にアクセス',
            "e\nd",
            'easy'
        );

        $this->createExample($section4, $pythonLanguage, 'ループ', 2,
            ">>> for char in \"foo\":\n...     print(char)\nf\no\no",
            '文字列をループ処理',
            "f\no\no",
            'easy'
        );

        $this->createExample($section4, $pythonLanguage, '文字列スライス', 3,
            ">>> msg = \"Hello, World!\"\n>>> print(msg[2:5])\nllo\n>>> print(msg[:5])\nHello\n>>> print(msg[7:])\nWorld!\n>>> print(msg[-6:-1])\nWorld",
            '文字列スライスの例',
            "llo\nHello\nWorld!\nWorld",
            'easy'
        );

        $this->createExample($section4, $pythonLanguage, '文字列メソッド', 4,
            "text = \"Hello World\"\nprint(text.upper())      # => HELLO WORLD\nprint(text.lower())      # => hello world\nprint(text.strip())      # => Hello World\nprint(text.replace(\"H\", \"J\"))  # => Jello World\nprint(text.split(\" \"))   # => ['Hello', 'World']",
            '一般的な文字列メソッド',
            "HELLO WORLD\nhello world\nHello World\nJello World\n['Hello', 'World']",
            'easy'
        );

        // Section 5: Python Functions
        $section5 = $this->createSection($pythonLanguage, 'Python関数', 5, '関数の定義と使用', 'python-functions');

        $this->createExample($section5, $pythonLanguage, '位置引数', 1,
            "def positional_args(x, y, z):\n    return x, y, z\n\n# => (1, 2, 3)\npositional_args(1, 2, 3)",
            '位置による関数引数',
            "(1, 2, 3)",
            'easy'
        );

        $this->createExample($section5, $pythonLanguage, 'キーワード引数', 2,
            "def keyword_args(**kwargs):\n    return kwargs\n\n# => {\"big\": \"foot\", \"loch\": \"ness\"}\nkeyword_args(big=\"foot\", loch=\"ness\")",
            '**kwargsを使ったキーワード引数',
            "{\"big\": \"foot\", \"loch\": \"ness\"}",
            'medium'
        );

        $this->createExample($section5, $pythonLanguage, '複数の戻り値', 3,
            "def swap(x, y):\n    return y, x\n\nx = 1\ny = 2\nx, y = swap(x, y)  # => x = 2, y = 1",
            '複数の値を返す',
            null,
            'easy'
        );

        $this->createExample($section5, $pythonLanguage, 'デフォルト値', 4,
            "def add(x, y=10):\n    return x + y\n\nadd(5)      # => 15\nadd(5, 20)  # => 25",
            'デフォルトパラメータ値',
            "15\n25",
            'easy'
        );

        $this->createExample($section5, $pythonLanguage, '無名関数', 5,
            "# => True\n(lambda x: x > 2)(3)\n\n# => 5\n(lambda x, y: x ** 2 + y ** 2)(2, 1)",
            'Lambda関数',
            "True\n5",
            'medium'
        );

        // Section 6: Python Modules
        $section6 = $this->createSection($pythonLanguage, 'Pythonモジュール', 6, 'モジュールのインポートと使用', 'python-modules');

        $this->createExample($section6, $pythonLanguage, 'モジュールのインポート', 1,
            "import math\nprint(math.sqrt(16))  # => 4.0",
            'モジュール全体のインポート',
            "4.0",
            'easy'
        );

        $this->createExample($section6, $pythonLanguage, 'モジュールから', 2,
            "from math import ceil, floor\nprint(ceil(3.7))   # => 4.0\nprint(floor(3.7))  # => 3.0",
            '特定の関数のインポート',
            "4.0\n3.0",
            'easy'
        );

        $this->createExample($section6, $pythonLanguage, 'すべてインポート', 3,
            "from math import *",
            'モジュールからすべてをインポート',
            null,
            'easy'
        );

        $this->createExample($section6, $pythonLanguage, 'モジュールの短縮', 4,
            "import math as m\n\n# => True\nmath.sqrt(16) == m.sqrt(16)",
            'モジュールのエイリアス',
            null,
            'easy'
        );

        $this->createExample($section6, $pythonLanguage, '関数と属性', 5,
            "import math\ndir(math)",
            'モジュールの関数と属性の一覧',
            null,
            'easy'
        );

        // Section 7: Python File Handling
        $section7 = $this->createSection($pythonLanguage, 'Pythonファイル処理', 7, 'ファイルの読み書き', 'python-file-handling');

        $this->createExample($section7, $pythonLanguage, 'ファイル読み込み - 行ごと', 1,
            "with open(\"myfile.txt\") as file:\n    for line in file:\n        print(line)",
            'ファイルを行ごとに読み込む',
            null,
            'easy'
        );

        $this->createExample($section7, $pythonLanguage, 'ファイル読み込み - 行番号付き', 2,
            "file = open('myfile.txt', 'r')\nfor i, line in enumerate(file, start=1):\n    print(\"Number %s: %s\" % (i, line))",
            '行番号付きでファイルを読み込む',
            null,
            'easy'
        );

        $this->createExample($section7, $pythonLanguage, '文字列の書き込み', 3,
            "contents = {\"aa\": 12, \"bb\": 21}\nwith open(\"myfile1.txt\", \"w+\") as file:\n    file.write(str(contents))",
            'ファイルに文字列を書き込む',
            null,
            'easy'
        );

        $this->createExample($section7, $pythonLanguage, '文字列の読み込み', 4,
            "with open('myfile1.txt', \"r+\") as file:\n    contents = file.read()\nprint(contents)",
            'ファイルから文字列を読み込む',
            null,
            'easy'
        );

        $this->createExample($section7, $pythonLanguage, 'オブジェクトの書き込み', 5,
            "contents = {\"aa\": 12, \"bb\": 21}\nwith open(\"myfile2.txt\", \"w+\") as file:\n    file.write(json.dumps(contents))",
            'ファイルにJSONオブジェクトを書き込む',
            null,
            'medium'
        );

        $this->createExample($section7, $pythonLanguage, 'オブジェクトの読み込み', 6,
            "with open('myfile2.txt', \"r+\") as file:\n    contents = json.load(file)\nprint(contents)",
            'ファイルからJSONオブジェクトを読み込む',
            null,
            'medium'
        );

        $this->createExample($section7, $pythonLanguage, 'ファイルの削除', 7,
            "import os\nos.remove(\"myfile.txt\")",
            'ファイルを削除する',
            null,
            'easy'
        );

        $this->createExample($section7, $pythonLanguage, '確認して削除', 8,
            "import os\nif os.path.exists(\"myfile.txt\"):\n    os.remove(\"myfile.txt\")\nelse:\n    print(\"The file does not exist\")",
            '削除前にファイルの存在を確認',
            null,
            'easy'
        );

        $this->createExample($section7, $pythonLanguage, 'フォルダの削除', 9,
            "import os\nos.rmdir(\"myfolder\")",
            'フォルダを削除する',
            null,
            'easy'
        );

        // Section 8: Python Classes & Inheritance
        $section8 = $this->createSection($pythonLanguage, 'Pythonクラスと継承', 8, 'オブジェクト指向プログラミング', 'python-classes-inheritance');

        $this->createExample($section8, $pythonLanguage, '定義', 1,
            "class MyNewClass:\n    pass\n\n# Class Instantiation\nmy = MyNewClass()",
            '基本的なクラス定義',
            null,
            'easy'
        );

        $this->createExample($section8, $pythonLanguage, 'コンストラクタ', 2,
            "class Animal:\n    def __init__(self, voice):\n        self.voice = voice\n \ncat = Animal('Meow')\nprint(cat.voice)    # => Meow\n \ndog = Animal('Woof') \nprint(dog.voice)    # => Woof",
            'クラスのコンストラクタ',
            "Meow\nWoof",
            'easy'
        );

        $this->createExample($section8, $pythonLanguage, 'メソッド', 3,
            "class Dog:\n\n    # Method of the class\n    def bark(self):\n        print(\"Ham-Ham\")\n \ncharlie = Dog()\ncharlie.bark()   # => \"Ham-Ham\"",
            'クラスメソッド',
            "Ham-Ham",
            'easy'
        );

        $this->createExample($section8, $pythonLanguage, 'クラス変数', 4,
            "class MyClass:\n    class_variable = \"A class variable!\"\n\n# => A class variable!\nprint(MyClass.class_variable)\n\nx = MyClass()\n \n# => A class variable!\nprint(x.class_variable)",
            'クラス変数',
            "A class variable!\nA class variable!",
            'easy'
        );

        $this->createExample($section8, $pythonLanguage, 'Super()関数', 5,
            "class ParentClass:\n    def print_test(self):\n        print(\"Parent Method\")\n \nclass ChildClass(ParentClass):\n    def print_test(self):\n        print(\"Child Method\")\n        # Calls the parent's print_test()\n        super().print_test()\n\n>>> child_instance = ChildClass()\n>>> child_instance.print_test()\nChild Method\nParent Method",
            'super()を使って親メソッドを呼び出す',
            "Child Method\nParent Method",
            'medium'
        );

        $this->createExample($section8, $pythonLanguage, '__repr__()メソッド', 6,
            "class Employee:\n    def __init__(self, name):\n        self.name = name\n \n    def __repr__(self):\n        return self.name\n \njohn = Employee('John')\nprint(john)  # => John",
            '文字列表現メソッド',
            "John",
            'medium'
        );

        $this->createExample($section8, $pythonLanguage, 'ユーザー定義例外', 7,
            "class CustomError(Exception):\n    pass",
            'カスタム例外クラス',
            null,
            'medium'
        );

        $this->createExample($section8, $pythonLanguage, 'ポリモーフィズム', 8,
            "class ParentClass:\n    def print_self(self):\n        print('A')\n \nclass ChildClass(ParentClass):\n    def print_self(self):\n        print('B')\n \nobj_A = ParentClass()\nobj_B = ChildClass()\n \nobj_A.print_self() # => A\nobj_B.print_self() # => B",
            'ポリモーフィズムの例',
            "A\nB",
            'medium'
        );

        $this->createExample($section8, $pythonLanguage, 'オーバーライド', 9,
            "class ParentClass:\n    def print_self(self):\n        print(\"Parent\")\n \nclass ChildClass(ParentClass):\n    def print_self(self):\n        print(\"Child\")\n \nchild_instance = ChildClass()\nchild_instance.print_self() # => Child",
            'メソッドのオーバーライド',
            "Child",
            'medium'
        );

        $this->createExample($section8, $pythonLanguage, '継承', 10,
            "class Animal: \n    def __init__(self, name, legs):\n        self.name = name\n        self.legs = legs\n        \nclass Dog(Animal):\n    def sound(self):\n        print(\"Woof!\")\n \nYoki = Dog(\"Yoki\", 4)\nprint(Yoki.name) # => YOKI\nprint(Yoki.legs) # => 4\nYoki.sound()     # => Woof!",
            'クラスの継承',
            "Yoki\n4\nWoof!",
            'medium'
        );

        // Section 9: Miscellaneous
        $section9 = $this->createSection($pythonLanguage, 'その他', 9, 'Pythonの追加機能', 'miscellaneous');

        $this->createExample($section9, $pythonLanguage, 'コメント', 1,
            "# This is a single line comments.\n\n\"\"\" Multiline strings can be written\n    using three \"s, and are often used\n    as documentation.\n\"\"\"\n\n''' Multiline strings can be written\n    using three 's, and are often used\n    as documentation.\n'''",
            'コメント構文',
            null,
            'easy'
        );

        $this->createExample($section9, $pythonLanguage, 'ジェネレータ', 2,
            "def double_numbers(iterable):\n    for i in iterable:\n        yield i + i",
            'ジェネレータ関数',
            null,
            'medium'
        );

        $this->createExample($section9, $pythonLanguage, 'ジェネレータからリストへ', 3,
            "values = (-x for x in [1,2,3,4,5])\ngen_to_list = list(values)\n\n# => [-1, -2, -3, -4, -5]\nprint(gen_to_list)",
            'ジェネレータをリストに変換',
            "[-1, -2, -3, -4, -5]",
            'medium'
        );

        $this->createExample($section9, $pythonLanguage, '例外の処理', 4,
            "try:\n    # Use \"raise\" to raise an error\n    raise IndexError(\"This is an index error\")\nexcept IndexError as e:\n    pass                 # Pass is just a no-op. Usually you would do recovery here.\nexcept (TypeError, NameError):\n    pass                 # Multiple exceptions can be handled together, if required.\nelse:                    # Optional clause to the try/except block. Must follow all except blocks\n    print(\"All good!\")   # Runs only if the code in try raises no exceptions\nfinally:                 # Execute under all circumstances\n    print(\"We can clean up resources here\")",
            'try-except-else-finallyを使った例外処理',
            "We can clean up resources here",
            'medium'
        );

        // Update counts
        $this->updateLanguageCounts($pythonLanguage);
    }

    private function createSection(CheatCodeLanguage $language, string $title, int $sortOrder, ?string $description = null, ?string $slug = null): CheatCodeSection
    {
        return CheatCodeSection::create([
            'language_id' => $language->id,
            'title' => $title,
            'slug' => $slug ?? Str::slug($title),
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

