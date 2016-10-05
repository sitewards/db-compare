<?php

/**
 * @category    Sitewards
 * @package     Sitewards_DBCompare
 * @copyright   Copyright (c) Sitewards GmbH (http://www.sitewards.com/)
 */

namespace Sitewards\DBCompare;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Sitewards\DBCompare\Command\DBCompareCommand;

class CompareApplication extends Application
{
    /**
     * Gets the name of the command based on input.
     *
     * @param InputInterface $input The input interface
     *
     * @return string The command name
     */
    protected function getCommandName(InputInterface $input)
    {
        return 'db:compare';
    }

    /**
     * Gets the default commands that should always be available.
     *
     * @return array An array of default Command instances
     */
    protected function getDefaultCommands()
    {
        // Keep the core default commands to have the HelpCommand
        // which is used when using the --help option
        $aDefaultCommands = parent::getDefaultCommands();

        $aDefaultCommands[] = new DBCompareCommand();

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