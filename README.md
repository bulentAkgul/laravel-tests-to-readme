# Laravel Tests To Readme

This package aims to generate readme files while tests are being executed. To make this work, you need to call it inside a test method. So, it won't convert each and every test methods to readme file automatically.

### Installation and Configuration

```
sail composer require bakgul/laravel-tests-to-readme --dev
```

```
sail artisan vendor:publish --tag=to-readme-config
```

Now, you can go and change the settings after publishing config file, which is 'to-readme.php'

Finally, we need a folder to store readme files. You can name it as you wish, but it has to be located under one of the folder of the test class path. For example, if your test class is in 

```base_path('tests/Feature/EndpointTests/UserEndpointTests')```

then you can create a folder in 

`base_path()` or `base_path('tests')` or `base_path('tests/Feature')` etc.

### Some Notes
1. I suggest you keep readme folder on the root level. That being said, each file will be named based on the testing class. So, if you have multiple classes with the same name, you can't use `base_path('readme')` for all of them. In that case, you may want to create multiple readme folders.
2. This package doesn't produce final `README.md` file. You need to create it manually.
3. When you add a new test with a call to this package, your new method will be added to the target file.
4. You can modify the output because it won't be overwritten once it's being printed out.
5. When you rename a test method, it will be added to the target file. But you need to delete the old one manually.
6. You can add paragraph, lists, tables in between code blocks. They will be preserved.

### How It Works

You should call this package in your test method.

```php
class MyClassTest extends TestCase
{
    /** @test */
    public function my_test_method_does_things(): void
    {
        (new ToReadme([
            'test' => $this->getName();
            'class' => MyClass::class,
            'method' => 'doSomething',
            'args' => [1, 2],
            'message' => 'Anything you want to print.',
            'result' => 3
        ]))->write();

        // Your test ...
    }
}
```

The code above will generate a `readme/MyClass.md` file and put the content like between the horizontal lines down below.

---
### MyClass

#### doSomething

```
/**
 * If you have Phpdoc, it will be displated here
 */
public function doSomething(int $num1, int $num2): int
```

```php
// My test method does things.

// Anything you want to print.

(new MyClass)->doSomething(1, 2);

// 3
```
---

As you may have noticed, we passed a value as result, and it's printed in the code block. I suggest you do this way, but you don't have to specify the outcome of the method. When the key of "result" doesn't exist, we will try to execute the method and produce the outcome to print.

### Phpdoc vs Typehint

This package can use both to produce file. If you make `merge_phpdoc_and_method_declaration => true`, we will move types from phpdoc to typehint. Otherwise, we won't change anything.

The method down below

```php
/**
 * It does some stuff
 * 
 * @param array $arr
 * @param int   $int
 * @return array
 */
 public function do($arr, $int) { return [$arr, $int]; }
```

will be printed like this:

```php
/**
 * It does some stuff
 */
 public function do(array $arr, int $int): array
```

When you use both, and there is an inconsistency between phpdoc and typehints, a warning message will be printed. The method down below

```php
/**
 * It does some stuff
 * 
 * @param array $arr
 * @return array
 */
 public function do(array|string $arr) { return $arr; }
```

will be printed like this:
```php
WARNINGS:
   - mismatched types.

/**
 * It does some stuff
 */
 public function do(array $arr, int $int): array
```

There are other inconsistency checks, too. You can find them in `to-readme.php`

### Some Limitations
1. As it's mentioned before, when you don't pass 'result,' we will try to call the method and get the output. If the class has dependencies, it will probably won't work. In such cases, you better specify the result manually.
2. The array returned by the method will be converted to Json string and then modifyed through some character replacements to make it looks like a PHP array. It might not turn out well in all cases.
3. Identifier heading levels cannot be used anywhere else in readme.
4. First line that starts with '//' has to be the name of the test method.
5. I couldn't find any way to print out the callable arguments. So, you need to add them manually.

## License

This is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).