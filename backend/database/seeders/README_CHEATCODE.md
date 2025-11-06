# CheatCode Seeders - DOL LEAF TodoApp

## 📦 Available Seeders

### ✅ CheatCodePhpSeeder
- **Language:** PHP
- **Source:** https://quickref.me/php
- **Sections:** 7 sections
- **Examples:** ~40 code examples
- **Status:** ✅ Complete

### ✅ CheatCodeJavaSeeder
- **Language:** Java
- **Source:** https://quickref.me/java
- **Sections:** 7 sections
- **Examples:** ~45 code examples
- **Status:** ✅ Complete

### ✅ CheatCodeHtmlSeeder
- **Language:** HTML
- **Source:** https://quickref.me/html
- **Sections:** 6 sections
- **Examples:** ~35 code examples
- **Category:** Markup Language
- **Status:** ✅ Complete

---

## 🚀 Usage

### 1. Run All Seeders
```bash
cd backend
php artisan db:seed
```

### 2. Run Specific CheatCode Seeder
```bash
# PHP only
php artisan db:seed --class=CheatCodePhpSeeder

# Java only
php artisan db:seed --class=CheatCodeJavaSeeder

# HTML only
php artisan db:seed --class=CheatCodeHtmlSeeder
```

### 3. Fresh Migration + Seed
```bash
php artisan migrate:fresh --seed
```

---

## 📊 Database Structure

```
cheat_code_languages (2 records)
├── php (id: 1)
│   ├── Getting Started (10 examples)
│   ├── PHP Types (4 examples)
│   ├── PHP Strings (3 examples)
│   ├── PHP Arrays (6 examples)
│   ├── PHP Functions (5 examples)
│   ├── PHP Classes (4 examples)
│   └── Miscellaneous (2 examples)
│
└── java (id: 2)
    ├── Getting Started (8 examples)
    ├── Java Strings (5 examples)
    ├── Java Arrays (6 examples)
    ├── Java Conditionals (4 examples)
    ├── Java Loops (6 examples)
    ├── Java Collections (6 examples)
    └── Miscellaneous (4 examples)
```

---

## 📝 Java Seeder Details

### Sections Created:

1. **Getting Started** (8 examples)
   - Hello.java - Basic program structure
   - Variables - Primitive types
   - Primitive Data Types - Type reference table
   - Loops - Enhanced for-loop
   - Arrays - Declaration methods
   - Type Casting - Widening/narrowing
   - Conditionals - If-else statements
   - User Input - Scanner usage

2. **Java Strings** (5 examples)
   - Concatenation - String + numeric
   - StringBuilder - Append, delete, insert
   - Comparison - equals() vs ==
   - Manipulation - toUpperCase, replace, trim
   - Information - charAt, indexOf, length

3. **Java Arrays** (6 examples)
   - Declare - Various syntaxes
   - Modify - Element assignment
   - Loop (Read & Modify) - Indexed iteration
   - Loop (Read-Only) - Enhanced for
   - Multidimensional Arrays - 2D arrays
   - Sort - Arrays.sort()

4. **Java Conditionals** (4 examples)
   - If Statement - Basic if
   - If-Else Statement - Branching
   - Switch Statement - Multi-way branching
   - Ternary Operator - Conditional expression

5. **Java Loops** (6 examples)
   - For Loop - Standard iteration
   - Enhanced For Loop - Simplified iteration
   - While Loop - Pre-test loop
   - Do-While Loop - Post-test loop
   - Continue Statement - Skip iteration
   - Break Statement - Exit loop

6. **Java Collections** (6 examples)
   - ArrayList - Dynamic array
   - ArrayList Iteration - Enhanced for
   - HashMap - Key-value storage
   - HashMap Iteration - Lambda forEach
   - HashSet - Unique elements
   - ArrayDeque - Double-ended queue

7. **Miscellaneous** (4 examples)
   - Try-Catch-Finally - Exception handling
   - Regular Expressions - Regex patterns
   - Math Methods - Mathematical operations
   - Lambda Expressions - Functional programming

---

## 🎯 Features

### Auto-Generated Tags
Each code example is tagged based on its content:
- `java`, `basics` - All examples
- `oop` - Classes, objects
- `array` - Array operations
- `string` - String manipulation
- `loop` - Iteration examples
- `collections` - ArrayList, HashMap, etc.
- `error-handling` - Try-catch blocks
- `functional`, `java8` - Lambda expressions

### Difficulty Levels
- **Easy:** Basic syntax and simple operations (90%)
- **Medium:** Advanced features like lambdas, generics (10%)
- **Hard:** Complex patterns (future)

### Example Outputs
Many examples include expected output for verification:
```java
System.out.println("Hello, world!");
// Output: Hello, world!
```

---

## 📈 Statistics

| Language | Sections | Examples | Difficulty Distribution |
|----------|----------|----------|------------------------|
| PHP      | 7        | ~40      | Easy: 85%, Medium: 15% |
| Java     | 7        | ~45      | Easy: 90%, Medium: 10% |
| **Total**| **14**   | **~85**  | **Easy: 87%, Medium: 13%** |

---

## 🔮 Future Enhancements

### Additional Languages (Planned)
- [ ] Python (quickref.me/python)
- [ ] JavaScript (quickref.me/javascript)
- [ ] C++ (quickref.me/cpp)
- [ ] Go (quickref.me/go)
- [ ] Kotlin (quickref.me/kotlin)
- [ ] Swift (quickref.me/swift)

### Features to Add
- [ ] Exercise test cases for auto-grading
- [ ] User favorites tracking
- [ ] View count analytics
- [ ] Difficulty progression paths
- [ ] Interactive code playgrounds

---

## 🛠️ Maintenance

### Adding New Language
1. Create `CheatCode{Language}Seeder.php`
2. Follow structure of existing seeders
3. Use helper methods: `createSection()`, `createExample()`
4. Add to `DatabaseSeeder.php`
5. Update this README

### Updating Existing Data
```bash
# Delete old data
php artisan tinker
>>> App\Models\CheatCodeLanguage::where('name', 'java')->delete();

# Re-seed
php artisan db:seed --class=CheatCodeJavaSeeder
```

---

## 📚 References

- **PHP CheatSheet:** https://quickref.me/php
- **Java CheatSheet:** https://quickref.me/java
- **QuickRef.me:** https://quickref.me (Source for all cheat sheets)

---

## ✅ Testing Checklist

After running seeders, verify:

- [ ] Languages created: `SELECT * FROM cheat_code_languages;`
- [ ] Sections created: `SELECT * FROM cheat_code_sections;`
- [ ] Examples created: `SELECT * FROM code_examples;`
- [ ] Counts updated: Check `sections_count`, `examples_count`
- [ ] Tags generated: Check JSON tags field
- [ ] Slugs unique: Check for duplicate slugs

---

**Last Updated:** 2025-01-06
**Version:** 1.0
**Maintainer:** DOL LEAF Development Team
