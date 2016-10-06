<?php

namespace Sitewards\DBCompare\Worker\Item;

use Doctrine\DBAL\Connection;
use Sitewards\DBCompare\Worker\DBWorker;
use Symfony\Component\Filesystem\Filesystem;

class StoreConfigWorker
{
    /** @var Connection */
    private $oConnection;
    /** @var string */
    private $sDiffFileName = 'diff_core_config.sql';
    /** @var Filesystem */
    private $oFileSystem;

    /**
     * @param Connection $oConnection
     */
    public function __construct(Connection $oConnection)
    {
        $this->oConnection = $oConnection;
        $this->oFileSystem = new Filesystem();
    }

    /**
     * Process the difference files
     */
    public function processDifferenceFile()
    {
        $this->cleanUpOldFile();

        $oDBStatement = $this->oConnection->prepare($this->getDifferenceSql());
        $oDBStatement->execute();

        while ($aRowData = $oDBStatement->fetch()) {
            $this->writeDifferenceToFile($aRowData);
        }
    }

    /**
     * Build an sql string to get the difference between two databases
     *
     * @return string
     */
    private function getDifferenceSql()
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
    private function writeDifferenceToFile(array $aRowData)
    {
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

    /**
     * Remove any old diff files and create a new empty one
     */
    private function cleanUpOldFile()
    {
        $this->oFileSystem->remove($this->sDiffFileName);
        $this->oFileSystem->touch($this->sDiffFileName);
    }
}