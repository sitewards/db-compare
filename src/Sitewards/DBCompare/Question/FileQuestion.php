<?php

namespace Sitewards\DBCompare\Question;

use Sitewards\DBCompare\Validator\FilePath;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FileQuestion
{
    /** @var QuestionHelper */
    private $oQuestionHelper;
    /** @var FilePath */
    private $oValidator;

    /**
     * @param QuestionHelper $oQuestionHelper
     * @param FilePath $oValidator
     */
    public function __construct(
        QuestionHelper $oQuestionHelper,
        FilePath $oValidator
    ) {
        $this->oQuestionHelper = $oQuestionHelper;
        $this->oValidator      = $oValidator;
    }

    /**
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
        $oFilePathQuestion = new Question($sQuestion);
        $oFilePathQuestion->setValidator([$this->oValidator, 'doValidation']);
        return $this->oQuestionHelper->ask($oInput, $oOutput, $oFilePathQuestion);
    }
}