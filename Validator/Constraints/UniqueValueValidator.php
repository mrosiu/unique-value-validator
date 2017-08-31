<?php

namespace Mrosiu\Validator\Constraints;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueValueValidator extends ConstraintValidator
{
    /** @var PropertyAccessorInterface */
    protected $propertyAccessor;

    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(
        PropertyAccessorInterface $propertyAccessor,
        EntityManagerInterface $entityManager
    ) {
        $this->propertyAccessor = $propertyAccessor;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueValue) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\UniqueValue');
        }
        if (!$this->propertyAccessor->isReadable($value, $constraint->property)) {
            throw $this->createPropertyNotReadableException($constraint->property, get_class($value));
        }

        if (!property_exists($constraint->class, $constraint->classProperty)) {
            throw $this->createPropertyNotReadableException($constraint->classProperty, $constraint->class);
        }
        $repository = $this->entityManager->getRepository($constraint->class);
        $result = $repository->findOneBy([
            $constraint->classProperty => $this->propertyAccessor->getValue($value, $constraint->property)
        ]);

        if ($result instanceof $constraint->class) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }

    protected function createPropertyNotReadableException($property, $className)
    {
        return new \InvalidArgumentException(sprintf("Property \"%s\" for class \"%s\" is not readable", $property, $className));
    }
}
