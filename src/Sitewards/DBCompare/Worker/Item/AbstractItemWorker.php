<?php

use Doctrine\DBAL\Connection;
use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractItemWorker
{
    /** @var Filesystem */
    private $oFileSystem;
    /** @var Connection */
    private $oConnection;
    /** @var string */
    private $sDiffFileName;

    /**
     * @param Connection $oConnection
     */
    public function __construct(Connection $oConnection, $sDiffFileName)
    {
        $this->sDiffFileName = $sDiffFileName;
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
     * @return Connection
     */
    public function getConnection()
    {
        return $this->oConnection;
    }

    /**
     * @return string
     */
    public function getDiffFileName()
    {
        return $this->sDiffFileName;
    }

    /**
     * Remove any old diff files and create a new empty one
     */
    private function cleanUpOldFile()
    {
        $this->oFileSystem->remove($this->sDiffFileName);
        $this->oFileSystem->touch($this->sDiffFileName);
    }

    protected abstract function getDifferenceSql();
    protected abstract function writeDifferenceToFile(array $aRowData);
}