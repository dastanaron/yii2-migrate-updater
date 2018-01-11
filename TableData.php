<?php

namespace dastanaron\yiimigrate\updater;

use yii\db\Query;

/**
 * Class TableData
 * @package dastanaron\yiimigrate\updater
 */
class TableData
{
    public static function getData($tablename)
    {
        $query = (new Query())->select([
            '*'
        ])
        ->from($tablename);

        return $query->all();
    }
}
