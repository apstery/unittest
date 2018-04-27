<?php

namespace Unittest\Render;

class Cli
{
    public function render($test)
    {
        foreach ($test->getTestCases() as $name => $testCase) {
            $summary = $testCase->getTestSummary();

            echo $name . PHP_EOL;
            echo PHP_EOL;
            echo '  ' . $summary['numTestsSucceeded'] . '/' . $summary['numTests'] . ' tests succeeded' . PHP_EOL;
            echo '  ' . $summary['numAssertsSucceeded'] . '/' . $summary['numAsserts'] . ' assertions succeeded' . PHP_EOL;
            echo PHP_EOL;

            foreach ($summary['failMessages'] as $method => $messages) {
                foreach ($messages as $line => $message) {
                    echo '  ' . $method . ', at line ' . $line . ':' . PHP_EOL;
                    echo '    ' . $message . PHP_EOL;
                }
            }
            echo PHP_EOL;
        }
    }
}
