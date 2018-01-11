<?php

namespace dastanaron\yiimigrate\updater;

use Symfony\Component\CssSelector\Tests\Parser\ParserTest;
use yii\db\Query;

/**
 * Class TableData
 * @package dastanaron\yiimigrate\updater
 */
class TableData
{
    /**
     * @var string
     */
    public $tablename;

    /**
     * @var array
     */
    public $selecteddata;

    /**
     * @var string
     */
    public $sqlstring;

    /**
     * TableData constructor.
     * @param $tablename
     */
    public function __construct($tablename)
    {
        $this->tablename = $tablename;
    }

    public function getData()
    {
        $query = (new Query())->select(['*'])->from($this->tablename);

        $this->selecteddata = $query->all();

        return $this;

    }

    /**
     * @param string $type
     * @return mixed
     */
    public function Dump($type = 'create')
    {
        switch ($type) {
            case 'create':
                return $this->getData()->buildInsert()->CreateTmpDump();
            case 'read':
                return $this->getData()->buildInsert()->getDumpSql();
            default:
                return false;
        }
    }

    /**
     * @return string
     */
    public function getInsertString()
    {
        return $this->getData()->buildInsert()->sqlstring;
    }

    /**
     * @return $this
     */
    protected function buildInsert()
    {

        $sqlstring = '';

        if(!empty($this->selecteddata)) {

            foreach($this->selecteddata as $data) {

                $sqlstring .= "INSERT INTO $this->tablename SET ";

                $countData = count($data);
                $iteration_intro = 1;
                foreach($data as $key=>$value) {

                    $sqlstring .= "$key = ".$this->insertType($value);

                    if($iteration_intro != $countData) {
                        $sqlstring .= ", ";
                    }
                    else {
                        $sqlstring .= ";".PHP_EOL;
                    }
                    $iteration_intro++;
                }

            }
        }

        $this->sqlstring = $sqlstring;
        return $this;
    }

    /**
     * @return bool|int
     */
    protected function CreateTmpDump()
    {
        if(!empty($this->sqlstring)) {
            return file_put_contents($this->tablename.'-dump.sql', $this->sqlstring);
        }
        else {
            return false;
        }
    }

    /**
     * @return bool|string
     */
    protected function getDumpSql()
    {
        $dir = scandir('.');

        $dump = '';

        if(file_exists($this->tablename.'-dump.sql')) {
            $dump = file_get_contents($this->tablename.'-dump.sql');
            unlink($this->tablename.'-dump.sql');
        }

        return $dump;

    }


    /**
     * @param mixed $var
     * @return int|string
     */
    protected function insertType($var)
    {
        $type = gettype($var);

        if(empty($var)) {
            return 'null';
        }

        if(is_numeric($var)) {
            return (int) $var;
        }

        if(is_string($var)) {
            return "\"$var\"";
        }

    }
}