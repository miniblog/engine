<?php

declare(strict_types=1);

namespace Miniblog\Engine\Schema\Thing;

use DateTime;
use Miniblog\Engine\Schema\Thing;

use function is_string;

use const false;
use const null;

/**
 * @link https://schema.org/CreativeWork
 */
class CreativeWork extends Thing
{
    /**
     * @link https://schema.org/text
     * @see setBody()
     * @see getBody()
     */
    private ?string $text;

    /**
     * @link https://schema.org/datePublished
     */
    private ?DateTime $datePublished;

    /**
     * @link https://schema.org/dateModified
     */
    private ?DateTime $dateModified;

    /**
     * @link https://schema.org/inLanguage
     */
    private ?string $inLanguage;

    /**
     * Supersedes `Thing.name`.
     *
     * @link https://schema.org/headline
     * @see setName()
     * @see getName()
     */
    private ?string $headline;

    public function __construct()
    {
        parent::__construct();

        $this
            ->setText(null)
            ->setDatePublished(null)
            ->setDateModified(null)
            ->setInLanguage(null)
            ->setHeadline(null)
        ;
    }

    /**
     * @param string|DateTime|null $value
     * @return static
     */
    protected function setDateTimeProperty(string $name, $value): self
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
     * @return string|DateTime|null
     */
    protected function getDateTimeProperty(string $name, bool $returnObject)
    {
        /** @var DateTime|null */
        $dateTime = $this->{$name};

        if (null === $dateTime) {
            return $dateTime;
        }

        return $returnObject
            ? $dateTime
            : $dateTime->format('c')
        ;
    }

    /**
     * @return static
     */
    public function setText(?string $text): self
    {
        $this->text = $text;
        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @see $text
     * @return static
     */
    public function setBody(?string $body): self
    {
        return $this->setText($body);
    }

    /**
     * @see $text
     */
    public function getBody(): ?string
    {
        return $this->getText();
    }

    /**
     * @param string|DateTime|null $value
     * @return static
     */
    public function setDatePublished($value): self
    {
        return $this->setDateTimeProperty('datePublished', $value);
    }

    /**
     * Returns the date-time string, or `null`, by default.
     *
     * @return string|DateTime|null
     */
    public function getDatePublished(bool $returnObject = false)
    {
        return $this->getDateTimeProperty('datePublished', $returnObject);
    }

    /**
     * @param string|DateTime|null $value
     * @return static
     */
    public function setDateModified($value): self
    {
        return $this->setDateTimeProperty('dateModified', $value);
    }

    /**
     * Returns the date-time string, or `null`, by default.
     *
     * @return string|DateTime|null
     */
    public function getDateModified(bool $returnObject = false)
    {
        return $this->getDateTimeProperty('dateModified', $returnObject);
    }

    /**
     * @return static
     */
    public function setInLanguage(?string $languageCode): self
    {
        $this->inLanguage = $languageCode;
        return $this;
    }

    public function getInLanguage(): ?string
    {
        return $this->inLanguage;
    }

    /**
     * @return static
     */
    public function setHeadline(?string $headline): self
    {
        $this->headline = $headline;
        return $this;
    }

    public function getHeadline(): ?string
    {
        return $this->headline;
    }

    /**
     * @see $headline
     * @return static
     */
    public function setName(?string $name): self
    {
        return $this->setHeadline($name);
    }

    /**
     * @see $headline
     */
    public function getName(): ?string
    {
        return $this->getHeadline();
    }
}
