<?php

/**
 * @category    Sitewards
 * @package     Sitewards_DBCompare
 * @copyright   Copyright (c) Sitewards GmbH (http://www.sitewards.com/)
 */

namespace Sitewards\DBCompare\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DBCompareCommand extends Command
{
    /**
     * Set-up the db:comapre command
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
        $oOutput->writeln('db:compare run successfully');
    }
}