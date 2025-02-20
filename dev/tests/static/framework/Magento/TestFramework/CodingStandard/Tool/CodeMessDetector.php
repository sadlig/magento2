<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * PHP Code Mess v1.3.3 tool wrapper
 */
namespace Magento\TestFramework\CodingStandard\Tool;

use \Magento\TestFramework\CodingStandard\ToolInterface;
use Magento\TestFramework\CodingStandard\Tool\CodeMessOutput;

class CodeMessDetector implements ToolInterface
{
    /**
     * Ruleset directory
     *
     * @var string
     */
    private $rulesetFile;

    /**
     * @var string
     */
    private $reportFile;

    /**
     * @param string $rulesetFile \Directory that locates the inspection rules
     * @param string $reportFile Destination file to write inspection report to
     */
    public function __construct($rulesetFile, $reportFile)
    {
        $this->reportFile = $reportFile;
        $this->rulesetFile = $rulesetFile;
    }

    /**
     * Whether the tool can be ran on the current environment
     *
     * @return bool
     */
    public function canRun()
    {
        return class_exists(\PHPMD\TextUI\Command::class);
    }

    /**
     * @inheritdoc
     */
    public function run(array $whiteList)
    {
        if (empty($whiteList)) {
            return \PHPMD\TextUI\ExitCode::Success;
        }

        $commandLineArguments = [
            'run_file_mock', //emulate script name in console arguments
            implode(',', $whiteList),
            'text', //report format
            $this->rulesetFile,
            '--reportfile',
            $this->reportFile,
        ];

        $options = new \PHPMD\TextUI\CommandLineOptions($commandLineArguments);

        $command = new \PHPMD\TextUI\Command(new CodeMessOutput());

        return $command->run($options, new \PHPMD\RuleSetFactory());
    }
}
