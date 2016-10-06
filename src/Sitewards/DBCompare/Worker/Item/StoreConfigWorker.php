<?php

namespace Sitewards\DBCompare\Worker\Item;

use Sitewards\DBCompare\Worker\DBWorker;

class StoreConfigWorker extends AbstractItemWorker
{
    const S_WORKER_ID = 'system_config';

    /**
     * Build an sql string to get the difference between two databases
     *
     * @return string
     */
    protected function getDifferenceSql()
    {
        $sDiffSql = sprintf(
            'SELECT
                new_config.config_id,
                new_config.scope,
                new_config.scope_id,
                new_config.path,
                new_config.value
            FROM
                %s.core_config_data AS new_config
            WHERE
                ROW(
                    new_config.config_id,
                    new_config.scope,
                    new_config.scope_id,
                    new_config.path,
                    new_config.value
                ) NOT IN (
                    SELECT
                        old_config.config_id,
                        old_config.scope,
                        old_config.scope_id,
                        old_config.path,
                        old_config.value
                    FROM
                        %s.core_config_data AS old_config
                )',
            DBWorker::S_MERGE_DB_NAME,
            DBWorker::S_MAIN_DB_NAME
        );
        return $sDiffSql;
    }

    /**
     * Write an insert into script for a given difference
     *
     * @param array $aRowData
     */
    protected function writeDifferenceToFile(array $aRowData)
    {
        file_put_contents(
            $this->getDiffFileName(),
            sprintf(
                "INSERT INTO core_config_data (config_id, scope, scope_id, path, value) VALUE (%s, %s, %s, %s, %s) ON DUPLICATE KEY UPDATE value=VALUE(value);\n",
                $this->getConnection()->quote($aRowData['config_id'], \PDO::PARAM_INT),
                $this->getConnection()->quote($aRowData['scope'], \PDO::PARAM_STR),
                $this->getConnection()->quote($aRowData['scope_id'], \PDO::PARAM_INT),
                $this->getConnection()->quote($aRowData['path'], \PDO::PARAM_STR),
                $this->getConnection()->quote($aRowData['value'], \PDO::PARAM_STR)
            ),
            FILE_APPEND | LOCK_EX
        );
    }
}