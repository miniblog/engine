<?php

declare(strict_types=1);

namespace Miniblog\Engine\Schema\Thing\CreativeWork;

use Miniblog\Engine\Schema\Thing\CreativeWork;

/**
 * @link https://schema.org/Article
 */
class Article extends CreativeWork
{
    /**
     * Supersedes `CreativeWork.text`.
     *
     * @link https://schema.org/articleBody
     * @see setText()
     * @see getText()
     * @see setBody()
     * @see getBody()
     */
    private ?string $articleBody;

    /**
     * @return static
     */
    public function setArticleBody(?string $body): self
    {
        $this->articleBody = $body;
        return $this;
    }

    public function getArticleBody(): ?string
    {
        return $this->articleBody;
    }

    /**
     * @see $articleBody
     * @return static
     */
    public function setText(?string $text): self
    {
        return $this->setArticleBody($text);
    }

    /**
     * @see $articleBody
     */
    public function getText(): ?string
    {
        return $this->getArticleBody();
    }

    /**
     * @see $articleBody
     * @return static
     */
    public function setBody(?string $body): self
    {
        return $this->setArticleBody($body);
    }

    /**
     * @see $articleBody
     */
    public function getBody(): ?string
    {
        return $this->getArticleBody();
    }

    public function isValid(): bool
    {
        return (
            parent::isValid()
            && (null !== $this->getDescription() && '' !== $this->getDescription())
            && (null !== $this->getArticleBody() && '' !== $this->getArticleBody())
            && null !== $this->getDatePublished()
        );
    }
}
