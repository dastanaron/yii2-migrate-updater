update migration to migrate/redo
================================

На русском [ТУТ](README_RU.md)

This extension allows you not to lose data when executing `./yii migrate/redo`

We all know that Yii2 does not have a table update system. There is redo, which
removes the table and recreates it according to the specified rules. This is good, but sometimes,
You need to update the system, including the tables and still not lose data.

This solution allows this, but provided that the new fields appearing in the table will be with the resolution `null`.
In addition, the new table should not delete the old columns.

Then maybe we'll fix it, but in the initial stages it will be like this.


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist dastanaron/yii2-migrate-updater "*"
```

or add

```
"dastanaron/yii2-migrate-updater": "*"
```

to the require section of your `composer.json` file
and run 

```
php composer.phar update
```


Usage
-----

The use is fairly simple, you must install this solution
and connect the class in your migration class.

To save migrations, you need to connect the migration extension class, and use it,
to create their own migrations. 
In addition, you must declare a public variable with the table name `public $tableName`

```php
<?php

use dastanaron\yiimigrate\updater\ExtMigration;

class m171226_130601_youmigrationclass extends ExtMigration {
    
    public $tableName = 'youmigrationtable';
    
    //body class
    
}
```
By default, the class uses a flag that allows or denies the storage of data,
it is specified in the parent class as `public $saveData = true;` if you do not need to try to save data,
reassign this property in its class to disable this functionality

```php
<?php

use dastanaron\yiimigrate\updater\ExtMigration;

class m171226_130601_youmigrationclass extends ExtMigration {
    
    public $tableName = 'youmigrationtable';
    
    public $saveData = false; //disable data saving
    
    //body class
    
}
```

This functionality is implemented by the auxiliary class TableData. Below, you can see its methods
and properties that you can use outside of migration extensions and for diagnostics.

Methods of the TableData class
--------------

* ** `getData ()` ** - makes a request to the database to receive data, using `yii\db\Query`
* ** `Dump ($type = 'create')` ** - creates or reads the dump of the table, respectively the passed parameters `create` or` read`
* ** `getInsertString ()` ** - gets the assembled `insert sql` string. For diagnosis

## Attention

After reading, the dump is deleted

If you want to see an array of collected or other data, then
you can use the public properties of the class `TableData`

Public Properties
-------------------

* ** `$tablename` ** - the name of the table, is obtained in the constructor
* ** `$selecteddata` ** - after the request, will contain an array of data.
* ** `$sqlstring` ** - the query string is obtained after its assembly