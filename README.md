update migration to migrate/redo
================================
The extension allows you to save data when performing a ./yii migrate/redo

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist migrateupdater/yii2-migrate-updater "*"
```

or add

```
"migrateupdater/yii2-migrate-updater": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
<?= \dastanaron\yiimigrate\updater\AutoloadExample::widget(); ?>```