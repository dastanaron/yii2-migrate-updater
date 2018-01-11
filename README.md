update migration to migrate/redo
================================
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

For simplicity, we need to add the code to the `up ()` section, which will read the dump,
and in the section `down ()` - create, before deleting.

*** Do not try to write a dump into a public class variable, it will not work,
since yii console starts methods separately, and not as a single class ***

See the example below:

```php

use yii\db\Migration;
use dastanaron\yiimigrate\updater\TableData; //connect class

class m180109_131518_youtable extends Migration
{
    public $tableName = 'test_table'; //Make a variable for the table name, so it's more convenient

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->defaultValue(0),
            'category' => $this->integer(4)->null(),
            'comment' => $this->string(200)->null(),
            'cr_time' => $this->dateTime()->notNull(),
            'up_time' => $this->dateTime()->notNull(),
        ], $tableOptions);

        $this->AlterTable();

        $tableData = new TableData($this->tableName); //call an instance of the class

        $sqldump = $tableData->Dump('read'); //Read the collected dump

        if(!empty($sqldump)) { //check if not empty, then execute it
            $this->execute($sqldump);
        }

        $this->addForeignKey('fk_test_1', $this->tableName, 'user_id', 'users', 'id');

    }

    public function down()
    {
        $tableData = new TableData($this->tableName); //call an instance of the class

        $tableData->Dump('create'); //Creating dump

        $this->dropTable($this->tableName); //Drop table
    }

    public function AlterTable()
    {
        $sql = "ALTER TABLE `$this->tableName` CHANGE `up_time` `up_time` DATETIME on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;";
        $this->execute($sql);
    }


    /*public function createTable($table, $columns, $options = null)
    {
       $this->dropTable($this->tableName);
       parent::createTable($table, $columns, $options = null);
    }*/

}
```