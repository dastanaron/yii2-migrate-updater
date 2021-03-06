<?php

namespace dastanaron\yiimigrate\updater;

use yii\db\Migration;
use dastanaron\yiimigrate\updater\TableData;

/**
 * Class ExtMigration
 * @package dastanaron\yiimigrate\updater
 */
class ExtMigration extends Migration
{
    /**
     * @var bool
     */
    public $saveData = true;

    /**
     * @var string
     */
    public $tableName;

    /**
     * @inheritdoc
     * @param string $table
     * @param array $columns
     * @param null $options
     * @throws \Exception
     */
    public function createTable($table, $columns, $options = null)
    {
        parent::createTable($table, $columns, $options);

        if($this->saveData === true) $this->executeDump();
    }

    /**
     * Insert dumpfile to the database table
     * @throws \Exception
     */
    protected function executeDump()
    {
        $tableData = new TableData($this->getTablename());

        $sqldump = $tableData->Dump('read');

        if (!empty($sqldump)) {
            $this->execute($sqldump);
        }
    }

    /**
     * @inheritdoc
     * @param string $table
     * @throws \Exception
     */
    public function dropTable($table)
    {
        if($this->saveData === true) $this->createDump();
        parent::dropTable($table);
    }

    /**
     * Created dump database table
     * @throws \Exception
     */
    protected function createDump()
    {
        $tableData = new TableData($this->getTablename());

        $tableData->Dump('create');
    }

    /**
     * The method returned the $this->tableName;
     * @return string
     * @throws \Exception
     */
    public function getTablename()
    {
        if(empty($this->tableName)) {
            throw new \Exception('The tableName property is not declared');
        }
        else {
            return $this->tableName;
        }
    }

}