<?php

/**
 * @copyright Copyright (c) 2015 Yiister
 * @license https://github.com/yiister/yii2-advanced-db/blob/master/LICENSE
 * @link https://github.com/yiister/yii2-advanced-db
 */

namespace yiister\db\helpers;

use Yii;
use yii\db\Connection;

class DbHelper
{
    /**
     * Get database connection
     * @param Connection|null $db
     * @return Connection
     */
    protected static function getDb($db = null)
    {
        if ($db !== null) {
            return $db;
        }
        return Yii::$app->db;
    }

    /**
     * Check foreign key
     * @param string $table
     * @param string[]|string $columns
     * @param string $refTable
     * @param string[]|string $refColumns
     * @param Connection|null $db
     * @return bool
     */
    public static function foreignKeyExists($table, $columns, $refTable, $refColumns, $db = null)
    {
        // @todo Implement multiple foreign keys check
        if (is_array($columns) || is_array($refColumns)) {
            return false;
        }
        $db = static::getDb($db);
        $schema = $db->schema->getTableSchema($table);
        $refTable = trim($db->quoteSql($refTable), '`');
        foreach ($schema->foreignKeys as $foreignKey) {
            if ($foreignKey[0] === $refTable
                && count($foreignKey) == 2
                && isset($foreignKey[$columns]) == true
                && $foreignKey[$columns] == $refColumns
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Build index key
     * @param string $tableName
     * @param string[] $columns
     * @param bool $unique
     * @param Connection|null $db
     * @return string
     */
    public static function buildIndexKey($tableName, $columns, $unique = false, $db = null)
    {
        return ($unique ? 'uq-' : 'ix-')
            . trim(static::getDb($db)->quoteSql($tableName), '`') . '-'
            . implode('-', (array) $columns);
    }

    /**
     * Build foreign key
     * @param string $table
     * @param string[]|string $columns
     * @param string $refTable
     * @param string[]|string $refColumns
     * @param Connection|null $db
     * @return string
     */
    public static function buildForeignKey($table, $columns, $refTable, $refColumns, $db = null)
    {
        return 'fk-' . trim(static::getDb($db)->quoteSql($table), '`') . '-' . implode('-', (array) $columns) . '-'
            . trim(static::getDb($db)->quoteSql($refTable), '`') . '-' . implode('-', (array) $refColumns);
    }
}
