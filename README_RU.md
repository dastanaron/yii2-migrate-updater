Обновление миграций при migrate/redo
================================
Данное расширение позволяет не терять данные при выполнении `./yii migrate/redo`

Все мы знаем, что у Yii2 нет системы обновления таблиц. Там есть redo, которое
удаляет таблицу и создает заново по указанным правилам. Это хорошо, но иногда,
нужно обновить систему, включая таблицы и при этом не терять данные.

Данное решение это позволяет, но при условии, что новые поля, появившиеся в таблице будут с разрешением `null`.
Кроме того, в новй таблице не должно удаляться старых столбцов.

Потом может и это исправим, но на начальных этапах будет так.


Установка
------------

Для установки, используется композер [composer](http://getcomposer.org/download/).

Запустите

```
php composer.phar require --prefer-dist dastanaron/yii2-migrate-updater "*"
```

или добавьте

```
"dastanaron/yii2-migrate-updater": "*"
```

в секцию require, файла `composer.json`
и запустите

```
php composer.phar update
```


Использование
-----

Использование довольно простое, вы должны установить данное решение
и подключить класс, в вашем классе миграции. 

Для простоты, нам нужно добавить в секцию `up()` код, который будет считывать дамп,
а в секцию `down()` - создавать, перед удалением.

***Не пробуйте записать дамп в публичную переменную класса, работать не будет,
так как yii консоль запускает методы раздельно, а не как один класс***

Смотри пример ниже:

```php

use yii\db\Migration;
use dastanaron\yiimigrate\updater\TableData; //Подключаем класс

class m180109_131518_youtable extends Migration
{
    public $tableName = 'test_table'; //Сделайте переменную для названия таблицы, так удобней

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

        $tableData = new TableData($this->tableName); //вызываем экземпляр класса.

        $sqldump = $tableData->Dump('read'); //Считываем собранный дамп

        if(!empty($sqldump)) { //проверяем, если не пустой, но екзекутаем его
            $this->execute($sqldump);
        }

        $this->addForeignKey('fk_test_1', $this->tableName, 'user_id', 'users', 'id'); //Не забываем про связки, если они вам нужны

    }

    public function down()
    {
        $tableData = new TableData($this->tableName); //Снова вызываем экземпляр. Здесь не получится выполнять его в одном месте, потому что консоль обращается к этим методам раздельно

        $tableData->Dump('create'); //Создаем дамп

        $this->dropTable($this->tableName); //Удаляем таблицу
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


Методы класса
--------------

* **getData()** - делает запрос в базу на получение данных, с помощью yii\db\Query

* **Dump($type = 'create')** - создает либо читает дамп таблицы, соответственно передаваемые параметры `create` или `read`

* **getInsertString()** - получает собранную `insert sql` строку. Для диагностики

## Внимание

После считывания, дамп удаляется

Если требуется посмотреть массив собранный или другие данные, то
можно воспользоваться публичными свойствами

Публичные свойства
-------------------

* **$tablename** - имя таблицы, получается в кострукторе

* **selecteddata** - после запроса, будет содержать массив данных.

* **$sqlstring** - строка запроса, получается после его сборки
