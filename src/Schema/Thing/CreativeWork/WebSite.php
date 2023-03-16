<?php

declare(strict_types=1);

namespace Miniblog\Engine\Schema\Thing\CreativeWork;

use Miniblog\Engine\Schema\Thing\CreativeWork;

/**
 * @link https://schema.org/WebSite
 */
class WebSite extends CreativeWork
{
    public function isValid(): bool
    {
        return (
            parent::isValid()
            && (null !== $this->getDescription() && '' !== $this->getDescription())
            && null !== $this->getDatePublished()
            && (null !== $this->getInLanguage() && '' !== $this->getInLanguage())
            && (null !== $this->getUrl() && '' !== $this->getUrl())
        );
    }
}
