<?php

declare(strict_types=1);

namespace Miniblog\Engine;

use DateTime;
use ReflectionClass;
use ReflectionProperty;

use function array_intersect_key;
use function is_string;
use function ucfirst;

use const null;

class Article
{
    /**
     * @var array<string,string>
     */
    private static array $propertySetterNames;

    private ?string $id;

    private ?string $title;

    private ?string $description;

    private ?string $body;

    private ?DateTime $publishedAt;

    private ?DateTime $updatedAt;

    public function __construct()
    {
        $this
            ->setId(null)
            ->setTitle(null)
            ->setDescription(null)
            ->setBody(null)
            ->setPublishedAt(null)
            ->setUpdatedAt(null)
        ;
    }

    public function setId(?string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setBody(?string $body): self
    {
        $this->body = $body;
        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @param DateTime|string|null $value
     */
    private function setDateTimeProperty(string $name, $value): self
    {
        $dateTime = null;

        if ($value instanceof DateTime) {
            $dateTime = $value;
        } elseif (is_string($value)) {
            $dateTime = new DateTime($value);
        }

        $this->{$name} = $dateTime;

        return $this;
    }

    /**
     * @param DateTime|string|null $something
     */
    public function setPublishedAt($something): self
    {
        return $this->setDateTimeProperty('publishedAt', $something);
    }

    public function getPublishedAt(): ?DateTime
    {
        return $this->publishedAt;
    }

    /**
     * @param DateTime|string|null $something
     */
    public function setUpdatedAt($something): self
    {
        return $this->setDateTimeProperty('updatedAt', $something);
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function isValid(): bool
    {
        return (
            (null !== $this->getId() && '' !== $this->getId())
            && (null !== $this->getTitle() && '' !== $this->getTitle())
            && (null !== $this->getBody() && '' !== $this->getBody())
            && null !== $this->getPublishedAt()
        );
    }

    /**
     * @param array<string,mixed> $array
     */
    public static function createFromArray(array $array): self
    {
        if (!isset(self::$propertySetterNames)) {
            $class = new ReflectionClass(__CLASS__);

            self::$propertySetterNames = [];

            foreach ($class->getProperties(ReflectionProperty::IS_PRIVATE) as $privateProperty) {
                $propertyName = $privateProperty->getName();
                $setterName = 'set' . ucfirst($propertyName);

                if (
                    $class->hasMethod($setterName)
                    && $class->getMethod($setterName)->isPublic()
                ) {
                    self::$propertySetterNames[$propertyName] = $setterName;
                }
            }
        }

        $article = new self();

        foreach (array_intersect_key($array, self::$propertySetterNames) as $propertyName => $propertyValue) {
            $setterName = self::$propertySetterNames[$propertyName];
            $article->{$setterName}($propertyValue);
        }

        return $article;
    }
}
