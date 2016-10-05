<?php

/**
 * @category    Sitewards
 * @package     Sitewards_DBCompare
 * @copyright   Copyright (c) Sitewards GmbH (http://www.sitewards.com/)
 */

namespace Sitewards\DBCompare\Command;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Sitewards\DBCompare\Exception\FileNotFoundException;
use Sitewards\DBCompare\Exception\FileNotReadableException;
use Sitewards\DBCompare\Exception\MySqlImportException;
use Symfony\Component\Filesystem\Filesystem;

class DBCompareCommand extends Command
{
    /**
     * Names used for the temporary databases
     */
    const S_MAIN_DB_NAME = 'db_comp_main_db';
    const S_MERGE_DB_NAME = 'db_comp_merge_db';

    /**
     * Set-up the db:compare command
     */
    protected function configure()
    {
        $this->setName('db:compare');
        $this->setDescription('Compare two database and get a diff file');
        $this->setHelp(
            'This command compares two database files and produces a difference file that can be use to migrate changes'
        );
    }

    /**
     * Execution of the db:compare command
     *
     * @param InputInterface $oInput
     * @param OutputInterface $oOutput
     */
    protected function execute(InputInterface $oInput, OutputInterface $oOutput)
    {
        $oOutput->writeln('Staring the db:compare');

        $sMainDBPath = $this->getFilePath($oInput, $oOutput, 'Please enter the main database file path:');
        $sMergingDB = $this->getFilePath($oInput, $oOutput, 'Please enter the merging database file path:');
        $sItemToMerge = $this->getItemToMerge($oInput, $oOutput);

        $oOutput->writeln('Main DB file: ' . $sMainDBPath);
        $oOutput->writeln('Merging DB file: ' . $sMergingDB);
        $oOutput->writeln('Merging item: ' . $sItemToMerge);

        $sDBUser = $this->getDBInformation($oInput, $oOutput, 'Please enter a valid local database user:');
        $sDBPassword = $this->getSensitiveDBInformation(
            $oInput,
            $oOutput,
            'Please enter a valid local database password:'
        );

        $oOutput->writeln('DB User: ' . $sDBUser);
        $oOutput->writeln('DB Password: ' . $sDBPassword);

        $oDBConnection = $this->getDatabaseConnection($sDBUser, $sDBPassword);
        $this->buildTempDatabases($oDBConnection);
        $this->insertFromFile($sDBUser, $sDBPassword, self::S_MAIN_DB_NAME, $sMainDBPath);
        $this->insertFromFile($sDBUser, $sDBPassword, self::S_MERGE_DB_NAME, $sMergingDB);
        $this->getDifferencesInDatabase($oDBConnection);
        $this->cleanTempDatabases($oDBConnection);

        $oOutput->writeln('Ending the db:compare');
    }

    private function getDifferencesInDatabase(Connection $oDBConnection, $iItemType = 0)
    {
        if ($iItemType === 0) {
            $oFileSystem = new Filesystem();
            $oFileSystem->remove('diff_core_config.sql');
            $oFileSystem->touch('diff_core_config.sql');
            $sql = sprintf('SELECT
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
                        )', self::S_MERGE_DB_NAME, self::S_MAIN_DB_NAME);

            $stmt = $oDBConnection->prepare($sql);
            $stmt->execute();

            while ($row = $stmt->fetch()) {
                file_put_contents(
                    'diff_core_config.sql',
                    sprintf(
                        "INSERT INTO core_config_data (config_id, scope, scope_id, path, value) VALUE (\"%s\", \"%s\", \"%s\", \"%s\", \"%s\") ON DUPLICATE KEY UPDATE value=VALUE(value);\n",
                        $row['config_id'],
                        $row['scope'],
                        $row['scope_id'],
                        $row['path'],
                        $row['value']
                    ),
                    FILE_APPEND | LOCK_EX
                );
            }
        }
    }

    /**
     * Insert the sql files given to the tmp databases
     * 
     * @param string $sDBUser
     * @param string $sDBPassword
     * @param string $sDatabaseName
     * @param string $sFilePath
     */
    private function insertFromFile($sDBUser, $sDBPassword, $sDatabaseName, $sFilePath)
    {
        passthru(
            sprintf(
                'mysql -u %s -p%s %s < %s',
                $sDBUser,
                $sDBPassword,
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
     * @param Connection $oDBConnection
     */
    private function buildTempDatabases(Connection $oDBConnection)
    {
        $oSchema = $oDBConnection->getSchemaManager();
        $oSchema->dropAndCreateDatabase(self::S_MAIN_DB_NAME);
        $oSchema->dropAndCreateDatabase(self::S_MERGE_DB_NAME);
    }

    /**
     * @param Connection $oDBConnection
     */
    private function cleanTempDatabases(Connection $oDBConnection)
    {
        $oSchema = $oDBConnection->getSchemaManager();
        $oSchema->dropDatabase(self::S_MAIN_DB_NAME);
        $oSchema->dropDatabase(self::S_MERGE_DB_NAME);
    }

    /**
     * @param InputInterface $oInput
     * @param OutputInterface $oOutput
     * @param string $sQuestion
     * @return string
     */
    private function getFilePath(
        InputInterface $oInput,
        OutputInterface $oOutput,
        $sQuestion = ''
    ) {
        $oQuestionHelper = $this->getHelper('question');
        $oFilePathQuestion = new Question($sQuestion);
        $oFilePathQuestion->setValidator(
            function ($sAnswer) {
                if (!file_exists($sAnswer)) {
                    throw new FileNotFoundException('The file given cannot be found');
                }
                if (!is_readable($sAnswer)) {
                    throw new FileNotReadableException('The file given cannot be read');
                }
                return $sAnswer;
            }
        );
        return $oQuestionHelper->ask($oInput, $oOutput, $oFilePathQuestion);
    }

    /**
     * @param InputInterface $oInput
     * @param OutputInterface $oOutput
     * @param string $sQuestion
     * @return string
     */
    private function getDBInformation(
        InputInterface $oInput,
        OutputInterface $oOutput,
        $sQuestion = ''
    ) {
        $oQuestionHelper = $this->getHelper('question');
        $oDBInfoQuestion = new Question($sQuestion);
        return $oQuestionHelper->ask($oInput, $oOutput, $oDBInfoQuestion);
    }

    /**
     * @param InputInterface $oInput
     * @param OutputInterface $oOutput
     * @param string $sQuestion
     * @return string
     */
    private function getSensitiveDBInformation(
        InputInterface $oInput,
        OutputInterface $oOutput,
        $sQuestion = ''
    ) {
        $oQuestionHelper = $this->getHelper('question');
        $oDBInfoQuestion = new Question($sQuestion);
        $oDBInfoQuestion->setHidden(true);
        return $oQuestionHelper->ask($oInput, $oOutput, $oDBInfoQuestion);
    }

    /**
     * @param InputInterface $oInput
     * @param OutputInterface $oOutput
     * @return string
     */
    private function getItemToMerge(InputInterface $oInput, OutputInterface $oOutput)
    {
        $oQuestionHelper = $this->getHelper('question');
        $oItemQuestion = new ChoiceQuestion(
            'Please select the item you wish to merge',
            ['system config'],
            '0'
        );

        return $oQuestionHelper->ask($oInput, $oOutput, $oItemQuestion);
    }

    /**
     * @param $sDBUser
     * @param $sDBPassword
     * @return Connection
     */
    private function getDatabaseConnection($sDBUser, $sDBPassword)
    {
        $oDBConfig = new Configuration();
        $aConnectionParams = [
            'user' => $sDBUser,
            'password' => $sDBPassword,
            'host' => 'localhost',
            'driver' => 'pdo_mysql',
        ];
        return DriverManager::getConnection($aConnectionParams, $oDBConfig);
    }
}