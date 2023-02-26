<?php

declare(strict_types=1);

namespace Miniblog\Engine\Schema\Thing;

use Miniblog\Engine\Schema\Thing;

use function trim;

use const null;

/**
 * @link https://schema.org/Person
 */
class Person extends Thing
{
    /**
     * @link https://schema.org/givenName
     */
    private ?string $givenName;

    /**
     * @link https://schema.org/familyName
     */
    private ?string $familyName;

    /**
     * @link https://schema.org/email
     */
    private ?string $email;

    public function __construct()
    {
        parent::__construct();

        $this
            ->setGivenName(null)
            ->setFamilyName(null)
            ->setEmail(null)
        ;
    }

    /**
     * @return static
     */
    public function setGivenName(?string $name): self
    {
        $this->givenName = $name;
        return $this;
    }

    public function getGivenName(): ?string
    {
        return $this->givenName;
    }

    private function hasGivenName(): bool
    {
        return null !== $this->getGivenName() && '' !== $this->getGivenName();
    }

    /**
     * @return static
     */
    public function setFamilyName(?string $name): self
    {
        $this->familyName = $name;
        return $this;
    }

    public function getFamilyName(): ?string
    {
        return $this->familyName;
    }

    private function hasFamilyName(): bool
    {
        return null !== $this->getFamilyName() && '' !== $this->getFamilyName();
    }

    /**
     * Returns the full name of the person.
     *
     * The values of more specific properties are preferred over those of more generic properties defined in ancestor
     * classes: in this case, `Person.givenName` and `Person.familyName` take priority over `Thing.name`.
     */
    public function getFullName(): ?string
    {
        if ($this->hasGivenName() || $this->hasFamilyName()) {
            return trim("{$this->getGivenName()} {$this->getFamilyName()}");
        }

        return $this->getName();
    }

    /**
     * @return static
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function isValid(): bool
    {
        $fullName = $this->getFullName();

        return (
            (null !== $this->getIdentifier() && '' !== $this->getIdentifier())
            && (null !== $fullName && '' !== $fullName)
        );
    }
}
