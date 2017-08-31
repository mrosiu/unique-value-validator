<?php

namespace Mrosiu\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueValue extends Constraint
{
    public $message = "Provided value is not unique";

    public $class;

    public $property;

    public $classProperty;

    public function getTargets()
    {
        return [self::CLASS_CONSTRAINT, self::PROPERTY_CONSTRAINT];
    }
}
