<?php

/**
 * @category    Sitewards
 * @package     Sitewards_DBCompare
 * @copyright   Copyright (c) Sitewards GmbH (http://www.sitewards.com/)
 */

namespace Sitewards\DBCompare;

use Sitewards\DBCompare\Factory\ItemFactory;
use Sitewards\DBCompare\Question\DBQuestion;
use Sitewards\DBCompare\Question\FileQuestion;
use Sitewards\DBCompare\Question\WorkerQuestion;
use Sitewards\DBCompare\Validator\FilePath;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Sitewards\DBCompare\Command\DBCompareCommand;

class CompareApplication extends Application
{
    /**
     * Gets the name of the command based on input.
     *
     * @param InputInterface $oInput The input interface
     *
     * @return string The command name
     */
    protected function getCommandName(InputInterface $oInput)
    {
        return 'db:compare';
    }

    /**
     * Gets the default commands that should always be available.
     *
     * @return \Symfony\Component\Console\Command\Command[]
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function getDefaultCommands()
    {
        // Keep the core default commands to have the HelpCommand
        // which is used when using the --help option
        $aDefaultCommands = parent::getDefaultCommands();

        /** @var HelperSet $oHelperSet */
        $oHelperSet      = $this->getDefaultHelperSet();
        $oQuestionHelper = $oHelperSet->get('question');

        $aDefaultCommands[] = new DBCompareCommand(
            new FileQuestion(
                $oQuestionHelper,
                new FilePath()
            ),
            new WorkerQuestion($oQuestionHelper),
            new DBQuestion($oQuestionHelper),
            new ItemFactory()
        );

        return $aDefaultCommands;
    }

    /**
     * Overridden so that the application doesn't expect the command
     * name to be the first argument.
     *
     * @return \Symfony\Component\Console\Input\InputDefinition
     */
    public function getDefinition()
    {
        $oInputDefinition = parent::getDefinition();
        // clear out the normal first argument, which is the command name
        $oInputDefinition->setArguments();

        return $oInputDefinition;
    }
}