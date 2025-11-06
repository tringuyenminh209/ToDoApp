<?php

namespace Database\Seeders;

use App\Models\CheatCodeLanguage;
use App\Models\CheatCodeSection;
use App\Models\CodeExample;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CheatCodeJavaScriptSeeder extends Seeder
{
    /**
     * Seed JavaScript cheat code data from quickref.me
     * Reference: https://quickref.me/javascript
     */
    public function run(): void
    {
        // Create JavaScript Language
        $jsLanguage = CheatCodeLanguage::create([
            'name' => 'javascript',
            'display_name' => 'JavaScript',
            'slug' => 'javascript',
            'color' => '#F7DF1E',
            'description' => 'JavaScript is a lightweight, interpreted programming language. A complete quick reference for beginners.',
            'category' => 'programming',
            'popularity' => 95,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        // Section 1: Getting Started
        $section1 = $this->createSection($jsLanguage, 'Getting Started', 1, 'JavaScript basics and introduction');

        $this->createExample($section1, $jsLanguage, 'Console', 1,
            "// => Hello world!\nconsole.log('Hello world!');\n\n// => Hello QuickRef.ME\nconsole.warn('hello %s', 'QuickRef.ME');\n\n// Prints error message to stderr\nconsole.error(new Error('Oops!'));",
            'Console methods for output',
            "Hello world!\nHello QuickRef.ME\nError: Oops!",
            'easy'
        );

        $this->createExample($section1, $jsLanguage, 'Numbers', 2,
            "let amount = 6;\nlet price = 4.99;",
            'Number declarations',
            null,
            'easy'
        );

        $this->createExample($section1, $jsLanguage, 'Variables', 3,
            "let x = null;\nlet name = \"Tammy\";\nconst found = false;\n\n// => Tammy, false, null\nconsole.log(name, found, x);\n\nvar a;\nconsole.log(a); // => undefined",
            'Variable declarations with let, const, and var',
            "Tammy false null\nundefined",
            'easy'
        );

        $this->createExample($section1, $jsLanguage, 'Strings', 4,
            "let single = 'Wheres my bandit hat?';\nlet double = \"Wheres my bandit hat?\";\n\n// => 21\nconsole.log(single.length);",
            'String declarations and properties',
            "21",
            'easy'
        );

        $this->createExample($section1, $jsLanguage, 'Arithmetic Operators', 5,
            "5 + 5 = 10     // Addition\n10 - 5 = 5     // Subtraction\n5 * 10 = 50    // Multiplication\n10 / 5 = 2     // Division\n10 % 5 = 0     // Modulo",
            'Basic arithmetic operators',
            null,
            'easy'
        );

        $this->createExample($section1, $jsLanguage, 'Comments', 6,
            "// This line will denote a comment\n\n/*  \nThe below configuration must be \nchanged before deployment. \n*/",
            'Comment syntax',
            null,
            'easy'
        );

        $this->createExample($section1, $jsLanguage, 'Assignment Operators', 7,
            "let number = 100;\n\n// Both statements will add 10\nnumber = number + 10;\nnumber += 10;\n\nconsole.log(number); \n// => 120",
            'Assignment operators',
            "120",
            'easy'
        );

        $this->createExample($section1, $jsLanguage, 'String Interpolation', 8,
            "let age = 7;\n\n// String concatenation\n'Tommy is ' + age + ' years old.';\n\n// String interpolation\n`Tommy is \${age} years old.`;",
            'String concatenation vs template literals',
            null,
            'easy'
        );

        $this->createExample($section1, $jsLanguage, 'let Keyword', 9,
            "let count; \nconsole.log(count); // => undefined\ncount = 10;\nconsole.log(count); // => 10",
            'let keyword usage',
            "undefined\n10",
            'easy'
        );

        $this->createExample($section1, $jsLanguage, 'const Keyword', 10,
            "const numberOfColumns = 4;\n\n// TypeError: Assignment to constant...\nnumberOfColumns = 8;",
            'const keyword - immutable binding',
            null,
            'easy'
        );

        // Section 2: JavaScript Conditionals
        $section2 = $this->createSection($jsLanguage, 'JavaScript Conditionals', 2, 'Conditional statements and operators');

        $this->createExample($section2, $jsLanguage, 'if Statement', 1,
            "const isMailSent = true;\n\nif (isMailSent) {\n  console.log('Mail sent to recipient');\n}",
            'Basic if statement',
            "Mail sent to recipient",
            'easy'
        );

        $this->createExample($section2, $jsLanguage, 'Ternary Operator', 2,
            "var x=1;\n\n// => true\nresult = (x == 1) ? true : false;",
            'Ternary conditional operator',
            null,
            'easy'
        );

        $this->createExample($section2, $jsLanguage, 'Logical Operator ||', 3,
            "true || false;       // true\n10 > 5 || 10 > 20;   // true\nfalse || false;      // false\n10 > 100 || 10 > 20; // false",
            'Logical OR operator',
            null,
            'easy'
        );

        $this->createExample($section2, $jsLanguage, 'Logical Operator &&', 4,
            "true && true;        // true\n1 > 2 && 2 > 1;      // false\ntrue && false;       // false\n4 === 4 && 3 > 1;    // true",
            'Logical AND operator',
            null,
            'easy'
        );

        $this->createExample($section2, $jsLanguage, 'Comparison Operators', 5,
            "1 > 3                // false\n3 > 1                // true\n250 >= 250           // true\n1 === 1              // true\n1 === 2              // false\n1 === '1'            // false",
            'Comparison operators',
            null,
            'easy'
        );

        $this->createExample($section2, $jsLanguage, 'Logical Operator !', 6,
            "let lateToWork = true;\nlet oppositeValue = !lateToWork;\n\n// => false\nconsole.log(oppositeValue);",
            'Logical NOT operator',
            "false",
            'easy'
        );

        $this->createExample($section2, $jsLanguage, 'Nullish coalescing operator ??', 7,
            "null ?? 'I win';           //  'I win'\nundefined ?? 'Me too';     //  'Me too'\n\nfalse ?? 'I lose'          //  false\n0 ?? 'I lose again'        //  0\n'' ?? 'Damn it'            //  ''",
            'Nullish coalescing operator',
            null,
            'medium'
        );

        $this->createExample($section2, $jsLanguage, 'else if', 8,
            "const size = 10;\n\nif (size > 100) {\n  console.log('Big');\n} else if (size > 20) {\n  console.log('Medium');\n} else if (size > 4) {\n  console.log('Small');\n} else {\n  console.log('Tiny');\n}\n// Print: Small",
            'else if chain',
            "Small",
            'easy'
        );

        $this->createExample($section2, $jsLanguage, 'switch Statement', 9,
            "const food = 'salad';\n\nswitch (food) {\n  case 'oyster':\n    console.log('The taste of the sea');\n    break;\n  case 'pizza':\n    console.log('A delicious pie');\n    break;\n  default:\n    console.log('Enjoy your meal');\n}",
            'Switch statement',
            "Enjoy your meal",
            'easy'
        );

        $this->createExample($section2, $jsLanguage, '== vs ===', 10,
            "0 == false   // true\n0 === false  // false, different type\n1 == \"1\"     // true,  automatic type conversion \n1 === \"1\"    // false, different type\nnull == undefined  // true\nnull === undefined // false\n'0' == false       // true\n'0' === false      // false",
            'Equality operators comparison',
            null,
            'easy'
        );

        // Section 3: JavaScript Functions
        $section3 = $this->createSection($jsLanguage, 'JavaScript Functions', 3, 'Function definitions and usage');

        $this->createExample($section3, $jsLanguage, 'Functions', 1,
            "// Defining the function:\nfunction sum(num1, num2) {\n  return num1 + num2;\n}\n\n// Calling the function:\nsum(3, 6); // 9",
            'Function declaration',
            null,
            'easy'
        );

        $this->createExample($section3, $jsLanguage, 'Anonymous Functions', 2,
            "// Named function\nfunction rocketToMars() {\n  return 'BOOM!';\n}\n\n// Anonymous function\nconst rocketToMars = function() {\n  return 'BOOM!';\n}",
            'Named vs anonymous functions',
            null,
            'easy'
        );

        $this->createExample($section3, $jsLanguage, 'Arrow Functions - Two arguments', 3,
            "const sum = (param1, param2) => { \n  return param1 + param2; \n}; \nconsole.log(sum(2,5)); // => 7",
            'Arrow function with two parameters',
            "7",
            'easy'
        );

        $this->createExample($section3, $jsLanguage, 'Arrow Functions - No arguments', 4,
            "const printHello = () => { \n  console.log('hello'); \n}; \nprintHello(); // => hello",
            'Arrow function with no parameters',
            "hello",
            'easy'
        );

        $this->createExample($section3, $jsLanguage, 'Arrow Functions - Single argument', 5,
            "const checkWeight = weight => { \n  console.log(`Weight : \${weight}`); \n}; \ncheckWeight(25); // => Weight : 25",
            'Arrow function with single parameter',
            "Weight : 25",
            'easy'
        );

        $this->createExample($section3, $jsLanguage, 'Concise arrow functions', 6,
            "const multiply = (a, b) => a * b; \n// => 60 \nconsole.log(multiply(2, 30));",
            'Concise arrow function syntax',
            "60",
            'easy'
        );

        $this->createExample($section3, $jsLanguage, 'return Keyword', 7,
            "// With return\nfunction sum(num1, num2) {\n  return num1 + num2;\n}\n\n// The function doesn't output the sum\nfunction sum(num1, num2) {\n  num1 + num2;\n}",
            'Return keyword importance',
            null,
            'easy'
        );

        $this->createExample($section3, $jsLanguage, 'Calling Functions', 8,
            "// Defining the function\nfunction sum(num1, num2) {\n  return num1 + num2;\n}\n\n// Calling the function\nsum(2, 4); // 6",
            'Function invocation',
            null,
            'easy'
        );

        $this->createExample($section3, $jsLanguage, 'Function Expressions', 9,
            "const dog = function() {\n  return 'Woof!';\n}",
            'Function expression',
            null,
            'easy'
        );

        $this->createExample($section3, $jsLanguage, 'Function Parameters', 10,
            "// The parameter is name\nfunction sayHello(name) {\n  return `Hello, \${name}!`;\n}",
            'Function parameters',
            null,
            'easy'
        );

        $this->createExample($section3, $jsLanguage, 'Function Declaration', 11,
            "function add(num1, num2) {\n  return num1 + num2;\n}",
            'Function declaration syntax',
            null,
            'easy'
        );

        // Section 4: JavaScript Scope
        $section4 = $this->createSection($jsLanguage, 'JavaScript Scope', 4, 'Variable scope and visibility');

        $this->createExample($section4, $jsLanguage, 'Scope', 1,
            "function myFunction() { \n  \n  var pizzaName = \"Margarita\";\n  // Code here can use pizzaName\n  \n}\n\n// Code here can't use pizzaName",
            'Function scope',
            null,
            'easy'
        );

        $this->createExample($section4, $jsLanguage, 'Block Scoped Variables', 2,
            "const isLoggedIn = true;\n\nif (isLoggedIn == true) {\n  const statusMessage = 'Logged in.';\n}\n\n// Uncaught ReferenceError...\nconsole.log(statusMessage);",
            'Block scope with const',
            null,
            'easy'
        );

        $this->createExample($section4, $jsLanguage, 'Global Variables', 3,
            "// Variable declared globally\nconst color = 'blue';\n\nfunction printColor() {\n  console.log(color);\n}\n\nprintColor(); // => blue",
            'Global scope',
            "blue",
            'easy'
        );

        $this->createExample($section4, $jsLanguage, 'let vs var', 4,
            "for (let i = 0; i < 3; i++) {\n  // This is the Max Scope for 'let'\n  // i accessible ✔️\n}\n// i not accessible ❌\n\nfor (var i = 0; i < 3; i++) {\n  // i accessible ✔️\n}\n// i accessible ✔️",
            'let vs var scope difference',
            null,
            'medium'
        );

        $this->createExample($section4, $jsLanguage, 'Loops with closures', 5,
            "// Prints 3 thrice, not what we meant.\nfor (var i = 0; i < 3; i++) {\n  setTimeout(_ => console.log(i), 10);\n}\n\n// Prints 0, 1 and 2, as expected.\nfor (let j = 0; j < 3; j++) { \n  setTimeout(_ => console.log(j), 10);\n}",
            'Closure behavior with var vs let',
            "3\n3\n3\n0\n1\n2",
            'medium'
        );

        // Section 5: JavaScript Arrays
        $section5 = $this->createSection($jsLanguage, 'JavaScript Arrays', 5, 'Array operations and methods');

        $this->createExample($section5, $jsLanguage, 'Arrays', 1,
            "const fruits = [\"apple\", \"orange\", \"banana\"];\n\n// Different data types\nconst data = [1, 'chicken', false];",
            'Array declaration',
            null,
            'easy'
        );

        $this->createExample($section5, $jsLanguage, 'Property .length', 2,
            "const numbers = [1, 2, 3, 4];\n\nconsole.log(numbers.length); // => 4",
            'Array length property',
            "4",
            'easy'
        );

        $this->createExample($section5, $jsLanguage, 'Accessing Elements', 3,
            "const fruits = [\"apple\", \"orange\", \"banana\"];\n\nconsole.log(fruits[0]); // => apple\nconsole.log(fruits[1]); // => orange",
            'Accessing array elements by index',
            "apple\norange",
            'easy'
        );

        $this->createExample($section5, $jsLanguage, '.push() method', 4,
            "const items = ['pencil', 'notebook', 'eraser'];\nitems.push('backpack');\nconsole.log(items);",
            'Adding elements to end of array',
            "['pencil', 'notebook', 'eraser', 'backpack']",
            'easy'
        );

        $this->createExample($section5, $jsLanguage, '.pop() method', 5,
            "const items = ['pencil', 'notebook', 'eraser'];\nconst removedItem = items.pop();\nconsole.log(removedItem); // => eraser",
            'Removing last element from array',
            "eraser",
            'easy'
        );

        $this->createExample($section5, $jsLanguage, '.shift() method', 6,
            "const items = ['pencil', 'notebook', 'eraser'];\nconst firstItem = items.shift();\nconsole.log(firstItem); // => pencil",
            'Removing first element from array',
            "pencil",
            'easy'
        );

        $this->createExample($section5, $jsLanguage, '.unshift() method', 7,
            "const items = ['pencil', 'notebook'];\nitems.unshift('eraser');\nconsole.log(items);",
            'Adding element to beginning of array',
            "['eraser', 'pencil', 'notebook']",
            'easy'
        );

        $this->createExample($section5, $jsLanguage, '.slice() method', 8,
            "const fruits = ['apple', 'orange', 'banana', 'mango'];\nconst citrus = fruits.slice(1, 3);\nconsole.log(citrus); // => ['orange', 'banana']",
            'Extracting portion of array',
            "['orange', 'banana']",
            'easy'
        );

        $this->createExample($section5, $jsLanguage, '.splice() method', 9,
            "const fruits = ['apple', 'orange', 'banana'];\nfruits.splice(1, 1, 'mango');\nconsole.log(fruits); // => ['apple', 'mango', 'banana']",
            'Removing and inserting elements',
            "['apple', 'mango', 'banana']",
            'medium'
        );

        $this->createExample($section5, $jsLanguage, '.forEach() method', 10,
            "const fruits = ['apple', 'orange', 'banana'];\nfruits.forEach(fruit => {\n  console.log(fruit);\n});",
            'Iterating over array elements',
            "apple\norange\nbanana",
            'easy'
        );

        $this->createExample($section5, $jsLanguage, '.map() method', 11,
            "const numbers = [1, 2, 3];\nconst doubled = numbers.map(num => num * 2);\nconsole.log(doubled); // => [2, 4, 6]",
            'Transforming array elements',
            "[2, 4, 6]",
            'easy'
        );

        $this->createExample($section5, $jsLanguage, '.filter() method', 12,
            "const numbers = [1, 2, 3, 4, 5];\nconst evens = numbers.filter(num => num % 2 === 0);\nconsole.log(evens); // => [2, 4]",
            'Filtering array elements',
            "[2, 4]",
            'easy'
        );

        $this->createExample($section5, $jsLanguage, '.reduce() method', 13,
            "const numbers = [1, 2, 3, 4];\nconst sum = numbers.reduce((acc, num) => acc + num, 0);\nconsole.log(sum); // => 10",
            'Reducing array to single value',
            "10",
            'medium'
        );

        // Section 6: JavaScript Objects
        $section6 = $this->createSection($jsLanguage, 'JavaScript Objects', 6, 'Object creation and manipulation');

        $this->createExample($section6, $jsLanguage, 'Object Literal', 1,
            "const person = {\n  name: 'John',\n  age: 30,\n  city: 'New York'\n};",
            'Object literal syntax',
            null,
            'easy'
        );

        $this->createExample($section6, $jsLanguage, 'Accessing Properties', 2,
            "const person = { name: 'John', age: 30 };\n\nconsole.log(person.name);     // => John\nconsole.log(person['age']);   // => 30",
            'Accessing object properties',
            "John\n30",
            'easy'
        );

        $this->createExample($section6, $jsLanguage, 'Adding Properties', 3,
            "const person = { name: 'John' };\nperson.age = 30;\nperson['city'] = 'New York';\nconsole.log(person);",
            'Adding properties to object',
            "{ name: 'John', age: 30, city: 'New York' }",
            'easy'
        );

        $this->createExample($section6, $jsLanguage, 'Object Methods', 4,
            "const person = {\n  name: 'John',\n  greet: function() {\n    return `Hello, I'm \${this.name}`;\n  }\n};\nconsole.log(person.greet());",
            'Object methods',
            "Hello, I'm John",
            'easy'
        );

        $this->createExample($section6, $jsLanguage, 'Object.keys()', 5,
            "const person = { name: 'John', age: 30 };\nconst keys = Object.keys(person);\nconsole.log(keys); // => ['name', 'age']",
            'Getting object keys',
            "['name', 'age']",
            'easy'
        );

        $this->createExample($section6, $jsLanguage, 'Object.values()', 6,
            "const person = { name: 'John', age: 30 };\nconst values = Object.values(person);\nconsole.log(values); // => ['John', 30]",
            'Getting object values',
            "['John', 30]",
            'easy'
        );

        // Section 7: JavaScript Loops
        $section7 = $this->createSection($jsLanguage, 'JavaScript Loops', 7, 'Looping constructs');

        $this->createExample($section7, $jsLanguage, 'for Loop', 1,
            "for (let i = 0; i < 5; i++) {\n  console.log(i);\n}",
            'Basic for loop',
            "0\n1\n2\n3\n4",
            'easy'
        );

        $this->createExample($section7, $jsLanguage, 'for...of Loop', 2,
            "const fruits = ['apple', 'orange', 'banana'];\nfor (const fruit of fruits) {\n  console.log(fruit);\n}",
            'for...of loop for iterables',
            "apple\norange\nbanana",
            'easy'
        );

        $this->createExample($section7, $jsLanguage, 'for...in Loop', 3,
            "const person = { name: 'John', age: 30 };\nfor (const key in person) {\n  console.log(key, person[key]);\n}",
            'for...in loop for objects',
            "name John\nage 30",
            'easy'
        );

        $this->createExample($section7, $jsLanguage, 'while Loop', 4,
            "let i = 0;\nwhile (i < 5) {\n  console.log(i);\n  i++;\n}",
            'while loop',
            "0\n1\n2\n3\n4",
            'easy'
        );

        $this->createExample($section7, $jsLanguage, 'do...while Loop', 5,
            "let i = 0;\ndo {\n  console.log(i);\n  i++;\n} while (i < 5);",
            'do...while loop',
            "0\n1\n2\n3\n4",
            'easy'
        );

        // Section 8: JavaScript Classes
        $section8 = $this->createSection($jsLanguage, 'JavaScript Classes', 8, 'ES6 classes and OOP');

        $this->createExample($section8, $jsLanguage, 'Class Declaration', 1,
            "class Person {\n  constructor(name) {\n    this.name = name;\n  }\n  \n  greet() {\n    return `Hello, I'm \${this.name}`;\n  }\n}\n\nconst john = new Person('John');\nconsole.log(john.greet());",
            'Class declaration and instantiation',
            "Hello, I'm John",
            'medium'
        );

        $this->createExample($section8, $jsLanguage, 'Class Inheritance', 2,
            "class Animal {\n  constructor(name) {\n    this.name = name;\n  }\n}\n\nclass Dog extends Animal {\n  bark() {\n    return 'Woof!';\n  }\n}\n\nconst dog = new Dog('Buddy');\nconsole.log(dog.name);",
            'Class inheritance with extends',
            "Buddy",
            'medium'
        );

        // Section 9: JavaScript Promises
        $section9 = $this->createSection($jsLanguage, 'JavaScript Promises', 9, 'Promise-based asynchronous programming');

        $this->createExample($section9, $jsLanguage, 'Creating Promise', 1,
            "const promise = new Promise((resolve, reject) => {\n  if (res) {\n    resolve('Resolved!');\n  }\n  else {\n    reject(Error('Error'));\n  }\n});\n\npromise.then((res) => console.log(res), (err) => console.error(err));",
            'Creating and using promises',
            null,
            'medium'
        );

        $this->createExample($section9, $jsLanguage, '.then() method', 2,
            "const promise = new Promise((resolve, reject) => {    \n  setTimeout(() => {\n    resolve('Result');\n  }, 200);\n});\n\npromise.then((res) => {\n  console.log(res);\n}, (err) => {\n  console.error(err);\n});",
            'Promise then method',
            "Result",
            'medium'
        );

        $this->createExample($section9, $jsLanguage, '.catch() method', 3,
            "const promise = new Promise((resolve, reject) => {  \n  setTimeout(() => {\n    reject(Error('Promise Rejected Unconditionally.'));\n  }, 1000);\n});\n\npromise.catch((err) => {\n  console.error(err);\n});",
            'Promise catch method',
            "Error: Promise Rejected Unconditionally.",
            'medium'
        );

        $this->createExample($section9, $jsLanguage, 'Promise.all()', 4,
            "const promise1 = new Promise((resolve, reject) => {\n  setTimeout(() => {\n    resolve(3);\n  }, 300);\n});\nconst promise2 = new Promise((resolve, reject) => {\n  setTimeout(() => {\n    resolve(2);\n  }, 200);\n});\n\nPromise.all([promise1, promise2]).then((res) => {\n  console.log(res[0]);\n  console.log(res[1]);\n});",
            'Promise.all for multiple promises',
            "3\n2",
            'medium'
        );

        // Section 10: JavaScript Async-Await
        $section10 = $this->createSection($jsLanguage, 'JavaScript Async-Await', 10, 'Async/await syntax for promises');

        $this->createExample($section10, $jsLanguage, 'Async Function', 1,
            "function helloWorld() {\n  return new Promise(resolve => {\n    setTimeout(() => {\n      resolve('Hello World!');\n    }, 2000);\n  });\n}\n\nasync function msg() {\n  const msg = await helloWorld();\n  console.log('Message:', msg);\n}\n\nmsg(); // Message: Hello World! <-- after 2 seconds",
            'Async/await basics',
            "Message: Hello World!",
            'medium'
        );

        $this->createExample($section10, $jsLanguage, 'Error Handling', 2,
            "let json = '{ \"age\": 30 }'; // incomplete data\n\ntry {\n  let user = JSON.parse(json); // <-- no errors\n  console.log( user.name ); // no name!\n} catch (e) {\n  console.error( \"Invalid JSON data!\" );\n}",
            'Try-catch error handling',
            "undefined",
            'easy'
        );

        // Section 11: JavaScript Requests
        $section11 = $this->createSection($jsLanguage, 'JavaScript Requests', 11, 'HTTP requests and fetch API');

        $this->createExample($section11, $jsLanguage, 'JSON', 1,
            "const jsonObj = {\n  \"name\": \"Rick\",\n  \"id\": \"11A\",\n  \"level\": 4  \n};",
            'JSON object syntax',
            null,
            'easy'
        );

        $this->createExample($section11, $jsLanguage, 'fetch api', 2,
            "fetch(url, {\n    method: 'POST',\n    headers: {\n      'Content-type': 'application/json',\n      'apikey': apiKey\n    },\n    body: data\n  }).then(response => {\n    if (response.ok) {\n      return response.json();\n    }\n    throw new Error('Request failed!');\n  }, networkError => {\n    console.log(networkError.message)\n  })",
            'Fetch API POST request',
            null,
            'medium'
        );

        $this->createExample($section11, $jsLanguage, 'JSON Formatted', 3,
            "fetch('url-that-returns-JSON')\n.then(response => response.json())\n.then(jsonResponse => {\n  console.log(jsonResponse);\n});",
            'Fetch API with JSON response',
            null,
            'easy'
        );

        $this->createExample($section11, $jsLanguage, 'async await syntax', 4,
            "const getSuggestions = async () => {\n  const wordQuery = inputField.value;\n  const endpoint = `\${url}\${queryParams}\${wordQuery}`;\n  try{\n    const response = await fetch(endpoint, {cache: 'no-cache'});\n    if(response.ok){\n      const jsonResponse = await response.json()\n    }\n  }\n  catch(error){\n    console.log(error)\n  }\n}",
            'Fetch API with async/await',
            null,
            'medium'
        );

        // Update counts
        $this->updateLanguageCounts($jsLanguage);
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
        if (str_contains($titleLower, 'class') || str_contains($titleLower, 'object') || str_contains($descLower, 'oop')) {
            $tags[] = 'oop';
        }
        if (str_contains($titleLower, 'array') || str_contains($descLower, 'array')) {
            $tags[] = 'array';
        }
        if (str_contains($titleLower, 'string') || str_contains($descLower, 'string')) {
            $tags[] = 'string';
        }
        if (str_contains($titleLower, 'function') || str_contains($titleLower, 'arrow') || str_contains($descLower, 'function')) {
            $tags[] = 'function';
        }
        if (str_contains($titleLower, 'promise') || str_contains($titleLower, 'async') || str_contains($titleLower, 'await')) {
            $tags[] = 'async';
        }
        if (str_contains($titleLower, 'loop') || str_contains($titleLower, 'for') || str_contains($titleLower, 'while')) {
            $tags[] = 'loop';
        }
        if (str_contains($titleLower, 'fetch') || str_contains($titleLower, 'request') || str_contains($titleLower, 'http')) {
            $tags[] = 'http';
        }
        if (str_contains($titleLower, 'scope') || str_contains($titleLower, 'let') || str_contains($titleLower, 'var') || str_contains($titleLower, 'const')) {
            $tags[] = 'scope';
        }
        if (str_contains($titleLower, 'conditional') || str_contains($titleLower, 'if') || str_contains($titleLower, 'switch')) {
            $tags[] = 'conditional';
        }

        // Add basic tags
        $tags[] = 'javascript';
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

