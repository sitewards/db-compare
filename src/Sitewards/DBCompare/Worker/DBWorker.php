<?php

namespace Sitewards\DBCompare\Worker;

use Sitewards\DBCompare\Factory\DBConnectionFactory;
use Sitewards\DBCompare\Factory\ItemFactory;
use Sitewards\DBCompare\Exception\MySqlImportException;

class DBWorker
{
    /** @var string */
    private $sDBUsername;
    /** @var string */
    private $sDBPassword;
    /** @var int */
    private $iItemWorkerId;
    /** @var \Doctrine\DBAL\Connection */
    private $oConnection;

    /**
     * Names used for the temporary databases
     */
    const S_MAIN_DB_NAME = 'db_comp_main_db';
    const S_MERGE_DB_NAME = 'db_comp_merge_db';

    /**
     * @param string $sDBUsername
     * @param string $sDBPassword
     * @param int $iItemWorkerId
     */
    public function __construct($sDBUsername, $sDBPassword, $iItemWorkerId)
    {
        $this->sDBUsername = $sDBUsername;
        $this->sDBPassword = $sDBPassword;
        $this->iItemWorkerId = $iItemWorkerId;

        $this->oConnection = DBConnectionFactory::getDatabaseConnection($sDBUsername, $sDBPassword);
    }

    /**
     * Create temp databases before we use them
     */
    public function buildTempDatabases()
    {
        $oSchema = $this->oConnection->getSchemaManager();
        $oSchema->dropAndCreateDatabase(self::S_MAIN_DB_NAME);
        $oSchema->dropAndCreateDatabase(self::S_MERGE_DB_NAME);
    }

    /**
     * Drop temp databases after use
     */
    public function cleanTempDatabases()
    {
        $oSchema = $this->oConnection->getSchemaManager();
        $oSchema->dropDatabase(self::S_MAIN_DB_NAME);
        $oSchema->dropDatabase(self::S_MERGE_DB_NAME);
    }

    /**
     * Insert the sql files given to the tmp databases
     *
     * @param string $sDatabaseName
     * @param string $sFilePath
     */
    public function insertFromFile($sDatabaseName, $sFilePath)
    {
        passthru(
            sprintf(
                'mysql -u %s -p%s %s < %s',
                $this->sDBUsername,
                $this->sDBPassword,
                $sDatabaseName,
                $sFilePath
            ),
            $iMysqlError
        );
        if ($iMysqlError !== 0) {
            throw new MySqlImportException(
                sprintf(
                    'The sql file %s could not be imported',
                    $sFilePath
                )
            );
        }
    }

    /**
     * Process the difference file based off the two databases imported
     */
    public function getDifferencesInDatabase()
    {
        $oItemWorker = ItemFactory::createById($this->iItemWorkerId, $this->oConnection);
        $oItemWorker->processDifferenceFile();
    }
}