![image](https://user-images.githubusercontent.com/3457668/148050728-f80fb02c-e24e-4957-b960-8e52796fbf23.png)

### Enabling Rapid-Application-Development-tools, PR-bots, code analyzers and other things

![tests](https://github.com/ajthinking/archetype/workflows/tests/badge.svg)
![version](https://img.shields.io/packagist/v/ajthinking/archetype?color=blue)
[![Total Downloads](https://img.shields.io/packagist/dt/ajthinking/archetype.svg)](https://packagist.org/packages/ajthinking/archetype)

* Programatically modify php files with an intuitive top level read/write API
* Read/write on classes, framework- and language constructs using `FileQueryBuilders` and `AbstractSyntaxTreeQueryBuilders`

## Installation
```bash
composer require ajthinking/archetype
```
> Requires UNIX filesystem, PHP >= 7.4 and Laravel >= 7
 
## `PHPFile` read/write API

```php
use Archetype\Facades\PHPFile;

// Create new files
PHPFile::make()->class('acme/Product.php')
    ->use('Shippable')
    ->public()->property('stock', -1)
    ->save();
```

```php
// Modify existing files  
PHPFile::load('app/Models/User.php')
    ->className('NewClassName')
    ->save();
```

> [More examples](docs.md)

## `LaravelFile` read/write API

```php
use Archetype\Facades\LaravelFile; // extends PHPFile

// Expanding on our User model
LaravelFile::user()
    ->add()->use(['App\Traits\Dumpable', 'App\Contracts\PlayerInterface'])
    ->add()->implements('PlayerInterface')
    ->table('gdpr_users')
    ->add()->fillable('nickname')
    ->remove()->hidden()
    ->empty()->casts()
    ->hasMany('App\Game')
    ->belongsTo('App\Guild')
    ->save();
```

<details><summary>Show output</summary>

```php
<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
```

</details>

> [More examples](docs.md)

## File QueryBuilders
Filter and retrieve a set of files to interact with. 

```php
// find files with the query builder
PHPFile::in('database/migrations')
    ->where('extends', 'Migration')
    ->andWhere('className', 'like', 'Create')
    ->get() // returns Collection of PHPFiles

// Quickly find the Laravel User file
$file = LaravelFile::user();

// Quickly find Laravel specific files
LaravelFile::models()->get();
LaravelFile::controllers()->get();
LaravelFile::serviceProviders()->get();
// ...
```

> [More examples](docs.md)

## Abstract Syntax Tree QueryBuilder

As seen in the previous examples we can query and manipulate nodes with simple or primitive values, such as *strings* and *arrays*. However, if we want to perform custom or more in dept queries we must use the `ASTQueryBuilder`.

Example: how can we fetch explicit column names in a migration file?

```php
LaravelFile::load('database/migrations/2014_10_12_000000_create_users_table.php')
    ->astQuery() // get a ASTQueryBuilder

    ->classMethod()
        ->where('name->name', 'up')
    ->staticCall()
        ->where('class', 'Schema')
        ->where('name->name', 'create')
    ->args
    ->closure()
    ->stmts
    ->methodCall()
        ->where('var->name', 'table')
    ->args
	->value
	->value
	->get(); // exit ASTQueryBuilder, get a Collection        
```

The ASTQueryBuilder examines all possible paths and automatically terminates those that cant complete the query: IMAGE_PLACEHOLDER

The ASTQueryBuilder relies entirely on [nikic/php-parser](https://github.com/nikic/php-parser). Available query methods mirror the `PhpParser` types and properties. To understand this syntax better you may want to tinker with `dd($file->ast())` while building your queries. Basic conventions are listed below. 

* Traverse into *nodes* by using methods (`method()`,`staticCall()` ...)
* Traverse into *node properties* by accessing properties (`args`,`stmts` ...)    
* Filter results with `where(...)`
* Resolving matching paths with `get()`

> `ASTQueryBuilder` also supports *removing*, *replacing* and *injecting* nodes :wrench:

```php
// Replace a node property
$file->astQuery()
    ->class()
    ->name
    ->replaceProperty('name', $newClassName)
    ->commit() // updates the file's AST
    ->end() // exit query
    ->save() 
```

> [More examples](docs.md)

## Errors 😵
If a file can't be parsed, a `FileParseError` will be thrown. This can happen if you try to explicitly load a broken file *but also* when performing queries matching one or more problematic files.

To see *all* offending files run `php artisan archetype:errors`. To ignore files with problems, put them in `config/archetype.php` -> `ignored_paths`.

## Configuration
```bash
php artisan vendor:publish --provider="Archetype\ServiceProvider"
```

## Contributing
PRs welcome :pray:
### Development installation
```
git clone git@github.com:ajthinking/archetype.git
cd archetype
composer install
./vendor/bin/pest
```


## License
MIT


## Acknowledgements
* Built with [nikic/php-parser](https://github.com/nikic/php-parser)
* PSR Printing fixes borrowed from [tcopestake/PHP-Parser-PSR-2-pretty-printer](https://github.com/tcopestake/PHP-Parser-PSR-2-pretty-printer)


## Like this package?
<a href="https://github.com/ajthinking/archetype/stargazers" >Star it :star: </a>

[Say hi: @ajthinking :gem:](https://twitter.com/ajthinking)

[Github Sponsors :octocat::heart:](https://github.com/sponsors/ajthinking)