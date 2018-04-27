<?php

namespace Unittest\Condition;

class IsInstanceOf extends AbstractCondition
{
    protected $defaultMessage = '%actual% is not an instance of \'%expected%\'';
    protected $numParams = 2;

    protected function assertValues($values, $message)
    {
        $expected = $values[0];
        if (!($values[1] instanceof $expected)) {
            $this->setMessage($message, [
                'expected' => $values[0],
                'actual' => is_object($values[1]) ? 'Object with class \'' . get_class($values[1]) . '\'' : $values[1]
            ]);
            return false;
        }

        return true;
    }
}
