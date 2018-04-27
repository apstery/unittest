<?php

namespace Unittest;

use Exception;

class ErrorException extends Exception
{
}

function handleError($errno, $errstr, $errfile, $errline)
{
    throw new ErrorException($errstr, $errno);
}

class Test
{
    protected $numTests = 0;
    protected $numTestsSucceeded = 0;
    protected $numAsserts = 0;
    protected $numAssertsSucceeded = 0;
    protected $messages = [];
    protected $testCases = [];

    public function __construct($files)
    {
        $this->files = $files;
    }

    public function __invoke()
    {
        foreach ($this->files as $file) {
            $testClass = basename($file, '.php');
            $head = file_get_contents($file, false, null, 0, 512);

            if (preg_match('/\bclass\s+([^\s]+)/', $head, $match)) {
                $testClass = $match[1];
            }
            if (preg_match('/\bnamespace\s+([^;]+)/', $head, $match)) {
                $testClass = $match[1] . '\\' . $testClass;
            }

            include $file;
            $testCase = new $testClass();

            if (!($testCase instanceof Testcase)) {
                throw new Exception("Test case {$testClass} does not extend Unittest\Testcase");
            }

            $testCase();

            $this->numTests += $testCase->getNumTests();
            $this->numTestsSucceeded += $testCase->getNumTestsSucceeded();
            $this->numAsserts += $testCase->getNumAsserts();
            $this->numAssertsSucceeded += $testCase->getNumAssertsSucceeded();
            $this->messages[$testClass] = $testCase->getMessages();
            $this->testCases[$testClass] = $testCase;
        }
    }

    public function getTestCases()
    {
        return $this->testCases;
    }

    public static function listTests($directory, $baseDir = '', $flat = false)
    {
        $tests = [];

        if (file_exists("{$directory}/.testignore")) {
            return null;
        }

        $dirs = glob("{$directory}/*", GLOB_ONLYDIR);
        foreach ($dirs as $dir) {
            $d = basename($dir);
            $subtests = self::listTests($dir, $baseDir, $flat);
            if ($subtests && count($subtests) > 0) {
                if ($flat) {
                    $tests = array_merge($tests, $subtests);
                } else {
                    $tests[$d] = $subtests;
                }
            }
        }

        $files = glob("{$directory}/*.php");
        foreach ($files as $file) {
            if (basename($file, '.php') != 'config' && basename($file, '.example.php') != 'config') {
                $cls = basename($file, '.php');
                if ($baseDir && substr($file, 0, strlen($baseDir)) == $baseDir) {
                    $file = substr($file, strlen($baseDir));
                }
                if ($flat) {
                    $tests[] = $file;
                } else {
                    $tests[$cls] = $file;
                }
            }
        }

        return $tests;
    }
}

set_error_handler('Unittest\\handleError');
