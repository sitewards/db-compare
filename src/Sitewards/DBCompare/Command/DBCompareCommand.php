<?php

/**
 * @category    Sitewards
 * @package     Sitewards_DBCompare
 * @copyright   Copyright (c) Sitewards GmbH (http://www.sitewards.com/)
 */

namespace Sitewards\DBCompare\Command;

use Sitewards\DBCompare\Worker\DBWorker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\Exception\ConnectionException;

class DBCompareCommand extends Command
{
    /** @var \Sitewards\DBCompare\Question\FileQuestion */
    private $oFileQuestion;

    /** @var \Sitewards\DBCompare\Question\WorkerQuestion */
    private $oWorkerQuestion;

    /** @var \Sitewards\DBCompare\Question\DBQuestion */
    private $oDBQuestion;

    /** @var \Sitewards\DBCompare\Factory\ItemFactory */
    private $oItemFactory;

    /**
     * DBCompareCommand constructor.
     * @param \Sitewards\DBCompare\Question\FileQuestion $oFileQuestion
     * @param null $sName
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    public function __construct(
        \Sitewards\DBCompare\Question\FileQuestion $oFileQuestion,
        \Sitewards\DBCompare\Question\WorkerQuestion $oWorkerQuestion,
        \Sitewards\DBCompare\Question\DBQuestion $oDBQuestion,
        \Sitewards\DBCompare\Factory\ItemFactory $oItemFactory,
        $sName = null)
    {
        parent::__construct($sName);

        $this->oFileQuestion   = $oFileQuestion;
        $this->oWorkerQuestion = $oWorkerQuestion;
        $this->oDBQuestion = $oDBQuestion;
        $this->oItemFactory = $oItemFactory;
    }

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
     * @return null
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    protected function execute(InputInterface $oInput, OutputInterface $oOutput)
    {
        try {
            $oOutput->writeln('Staring the db:compare');

            /*$sMainDBPath = $this->oFileQuestion->getFilePath(
                $oInput,
                $oOutput,
                'Please enter the main database file path:'
            );
            $sMergingDB = $this->oFileQuestion->getFilePath(
                $oInput,
                $oOutput,
                'Please enter the merging database file path:'
            );*/
            $sItemToMerge = $this->oWorkerQuestion->getMergeWorker(
                $oInput,
                $oOutput
            );

            $sDBUser = $this->oDBQuestion->getBasicInformation(
                $oInput,
                $oOutput,
                'Please enter a valid local database user:'
            );
            $sDBPassword = $this->oDBQuestion->getSensitiveDBInformation(
                $oInput,
                $oOutput,
                'Please enter a valid local database password:'
            );

            $oDBWorker = new DBWorker(
                $sDBUser,
                $sDBPassword,
                $sItemToMerge,
                $this->oItemFactory
            );
            //$oDBWorker->buildTempDatabases();
            //$oDBWorker->insertFromFile(DBWorker::S_MAIN_DB_NAME, $sMainDBPath);
            //$oDBWorker->insertFromFile(DBWorker::S_MERGE_DB_NAME, $sMergingDB);
            $oDBWorker->getDifferencesInDatabase();
            //$oDBWorker->cleanTempDatabases();

            $oOutput->writeln('Ending the db:compare');
        } catch (ConnectionException $oException) {
            $oFormatter = $this->getHelper('formatter');
            $oOutput->writeln(
                $oFormatter->formatBlock(
                    'The database information provided is not valid',
                    'error'
                )
            );
        } catch (\Exception $oException) {
            $oFormatter = $this->getHelper('formatter');
            $oOutput->writeln(
                $oFormatter->formatBlock(
                    $oException->getMessage(),
                    'error'
                )
            );
        }
    }
}