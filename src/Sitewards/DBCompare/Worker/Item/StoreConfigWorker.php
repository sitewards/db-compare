<?php

namespace Sitewards\DBCompare\Worker\Item;

use Doctrine\DBAL\Connection;
use Sitewards\DBCompare\Worker\DBWorker;
use Symfony\Component\Filesystem\Filesystem;

class StoreConfigWorker
{
    /** @var Connection */
    private $oConnection;
    private $sDiffFileName = 'diff_core_config.sql';

    public function __construct(Connection $oConnection)
    {
        $this->oConnection = $oConnection;
    }

    /**
     *
     */
    public function processDifferenceFile()
    {
        $oFileSystem = new Filesystem();
        $oFileSystem->remove($this->sDiffFileName);
        $oFileSystem->touch($this->sDiffFileName);

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

        $oDBStatement = $this->oConnection->prepare($sDiffSql);
        $oDBStatement->execute();

        while ($aRowData = $oDBStatement->fetch()) {
            file_put_contents(
                $this->sDiffFileName,
                sprintf(
                    "INSERT INTO core_config_data (config_id, scope, scope_id, path, value) VALUE (\"%s\", \"%s\", \"%s\", \"%s\", \"%s\") ON DUPLICATE KEY UPDATE value=VALUE(value);\n",
                    $aRowData['config_id'],
                    $aRowData['scope'],
                    $aRowData['scope_id'],
                    $aRowData['path'],
                    $aRowData['value']
                ),
                FILE_APPEND | LOCK_EX
            );
        }
    }
}