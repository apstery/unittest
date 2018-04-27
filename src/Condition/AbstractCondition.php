<?php

namespace Unittest\Condition;

abstract class AbstractCondition
{
    protected $defaultMessage = 'The assertion failed';
    protected $numParams = 0;

    protected $message = null;

    protected function setMessage($message, $params = [])
    {
        if (!$message) {
            $message = $this->defaultMessage;
        }

        $replace = [];
        foreach ($params as $key => $value) {
            $replace["%{$key}%"] = $value;
        }

        $this->message = strtr($message, $replace);
    }

    abstract protected function assertValues($values, $message);

    public function __invoke($values)
    {
        $this->message = null;
        $message = $this->defaultMessage;

        if (count($values) > $this->numParams) {
            $message = $values[$this->numParams];
            array_splice($values, $this->numParams);
        }

        return $this->assertValues($values, $message);
    }

    public function getMessage()
    {
        return $this->message;
    }
}
