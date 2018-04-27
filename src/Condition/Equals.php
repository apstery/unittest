<?php

namespace Unittest\Condition;

class Equals extends AbstractCondition
{
    protected $defaultMessage = 'Value \'%actual%\' is not equal to expected \'%expected%\'';
    protected $numParams = 2;

    protected function assertValues($values, $message)
    {
        if ($values[0] !== $values[1]) {
            $this->setMessage($message, [
                'expected' => '(' . gettype($values[0]) . ')' . $values[0],
                'actual' => '(' . gettype($values[1]) . ')' . $values[1]
            ]);
            return false;
        }

        return true;
    }
}
