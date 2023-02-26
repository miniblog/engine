<?php

declare(strict_types=1);

namespace Miniblog\Engine\Schema;

use ReflectionClass;
use ReflectionProperty;

use function array_intersect_key;
use function get_called_class;
use function ucfirst;

use const null;

/**
 * @link https://schema.org/Thing
 * @phpstan-consistent-constructor
 */
class Thing
{
    /**
     * @var array<string,array<string,string>>
     */
    private static array $classPropertySetterNames = [];

    /**
     * @link https://schema.org/identifier
     */
    private ?string $identifier;

    /**
     * @link https://schema.org/name
     */
    private ?string $name;

    /**
     * @link https://schema.org/description
     */
    private ?string $description;

    public function __construct()
    {
        $this
            ->setIdentifier(null)
            ->setName(null)
            ->setDescription(null)
        ;
    }

    /**
     * @return static
     */
    public function setIdentifier(?string $id): self
    {
        $this->identifier = $id;
        return $this;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    /**
     * @return static
     */
    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return static
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return static
     */
    public function setBody(?string $body): self
    {
        return $this;
    }

    public function getBody(): ?string
    {
        return null;
    }

    public function isValid(): bool
    {
        return (
            (null !== $this->getIdentifier() && '' !== $this->getIdentifier())
            && (null !== $this->getName() && '' !== $this->getName())
        );
    }

    /**
     * @param array<string,mixed> $propertyValues
     * @return static
     */
    public static function createFromArray(array $propertyValues): self
    {
        $calledClassName = get_called_class();

        if (!isset(self::$classPropertySetterNames[$calledClassName])) {
            $class = new ReflectionClass($calledClassName);

            $propertySetterNames = [];

            do {
                foreach ($class->getProperties(ReflectionProperty::IS_PRIVATE) as $privateProperty) {
                    if ($privateProperty->isStatic()) {
                        continue;
                    }

                    $propertyName = $privateProperty->getName();
                    $setterName = 'set' . ucfirst($propertyName);

                    if (
                        $class->hasMethod($setterName)
                        && $class->getMethod($setterName)->isPublic()
                    ) {
                        $propertySetterNames[$propertyName] = $setterName;
                    }
                }
            } while ($class = $class->getParentClass());

            self::$classPropertySetterNames[$calledClassName] = $propertySetterNames;
        }

        $propertySetterNames = self::$classPropertySetterNames[$calledClassName];
        $filteredPropertyValues = array_intersect_key($propertyValues, $propertySetterNames);

        $thing = new static();

        foreach ($filteredPropertyValues as $propertyName => $propertyValue) {
            $setterName = $propertySetterNames[$propertyName];
            $thing->{$setterName}($propertyValue);
        }

        return $thing;
    }
}
