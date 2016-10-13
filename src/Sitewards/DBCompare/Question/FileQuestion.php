<?php

namespace Sitewards\DBCompare\Question;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\Question as SymfonyQuestion;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sitewards\DBCompare\Exception\FileNotFoundException;
use Sitewards\DBCompare\Exception\FileNotReadableException;

class FileQuestion
{
    /** @var QuestionHelper */
    private $oQuestionHelper;

    public function __construct(
        QuestionHelper $oQuestionHelper
    ) {
        $this->oQuestionHelper = $oQuestionHelper;
    }

    /**
     * @param QuestionHelper $oQuestionHelper
     * @param InputInterface $oInput
     * @param OutputInterface $oOutput
     * @param string $sQuestion
     * @return string
     * @throws \Symfony\Component\Console\Exception\RuntimeException
     */
    public function getFilePath(
        InputInterface $oInput,
        OutputInterface $oOutput,
        $sQuestion = ''
    )
    {
        $oFilePathQuestion = new SymfonyQuestion($sQuestion);
        $oFilePathQuestion->setValidator(
            function ($sAnswer) {
                if (!file_exists($sAnswer)) {
                    throw new FileNotFoundException(
                        sprintf(
                            'The file %s cannot be found',
                            $sAnswer
                        )
                    );
                }
                if (!is_readable($sAnswer)) {
                    throw new FileNotReadableException(
                        sprintf(
                            'The file %s cannot be read',
                            $sAnswer
                        )
                    );
                }
                return $sAnswer;
            }
        );
        return $this->oQuestionHelper->ask($oInput, $oOutput, $oFilePathQuestion);
    }
}