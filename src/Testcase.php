<?php

namespace Unittest;

class Testcase
{
    private $numTests = 0;
    private $numTestsSucceeded = 0;
    private $numAsserts = 0;
    private $numAssertsSucceeded = 0;
    private $failMessages = [];
    private $currMethod = null;
    private $currMethodSucceeded = true;
    private $assertionString = '';

    private static $conditions = [];

    public function __invoke()
    {
        $methods = get_class_methods(get_called_class());
        $testMethods = [];
        foreach ($methods as $method) {
            if (strlen($method) > 4 && substr($method, 0, 4) == 'test') {
                $testMethods[] = $method;
            }
        }

        $numMethods = count($testMethods);
        while ($numMethods > 0) {
            $index = rand(0, $numMethods - 1);
            $method = $testMethods[$index];
            array_splice($testMethods, $index, 1);

            $this->currMethod = $method;
            $this->currMethodSucceeded = true;
            $this->numTests++;
            $this->$method();
            if ($this->currMethodSucceeded) {
                $this->numTestsSucceeded++;
            }
            $numMethods--;
        }
    }

    protected function failAssertion($message)
    {
        $trace = debug_backtrace();
        $line = $trace[0]['line'];
        foreach ($trace as $index => $tr) {
            if ($tr['function'] == $this->currMethod) {
                $line = $trace[$index-1]['line'];
            }
        }

        $method = ucfirst(strtolower(preg_replace('/[A-Z]/', ' $0', lcfirst(substr($this->currMethod, 4)))));
        if (!isset($failMessages[$method])) {
            $failMessages[$method] = [];
        }

        $this->failMessages[$method][$line] = $message;
        $this->currMethodSucceeded = false;
        $this->assertionString .= 'F';
    }

    protected function succeedAssertion()
    {
        $this->numAssertsSucceeded++;
        $this->assertionString .= '.';
    }

    protected function assert($condition, $message = null)
    {
        $this->numAsserts++;
        if ($condition) {
            $this->succeedAssertion();
        } else {
            if (!$message) {
                $message = 'The condition failed';
            }
            $this->failAssertion($message);
        }
    }

    public function __call($name, $args)
    {
        if (strlen($name) > 6 && substr($name, 0, 6) == 'assert') {
            $condition = substr($name, 6);
            if (!isset(self::$conditions[$condition])) {
                $conditionClass = 'Unittest\\Condition\\' . $condition;
                if (class_exists($conditionClass)) {
                    self::$conditions[$condition] = new $conditionClass();
                }
            }

            $this->numAsserts++;
            $condObject = self::$conditions[$condition];
            if (!$condObject($args)) {
                $this->failAssertion($condObject->getMessage());
            } else {
                $this->succeedAssertion();
            }
        }
    }

    public function getTestSummary()
    {
        return [
            'numTests' => $this->numTests,
            'numTestsSucceeded' => $this->numTestsSucceeded,
            'numAsserts' => $this->numAsserts,
            'numAssertsSucceeded' => $this->numAssertsSucceeded,
            'failMessages' => $this->failMessages,
        ];
    }
}
