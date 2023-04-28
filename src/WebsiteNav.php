<?php

declare(strict_types=1);

namespace Miniblog\Engine;

use DanBettles\Marigold\HttpRequest;

use function array_key_exists;
use function array_unshift;
use function end;

use const false;
use const true;

class WebsiteNav
{
    /**
     * @phpstan-var MenuItemArray[]
     */
    private array $hierarchy;

    private HttpRequest $request;

    /**
     * @phpstan-var MenuItemArray[]
     */
    private array $stepsToCurrent;

    /**
     * @phpstan-param MenuItemArray[] $hierarchy
     */
    public function __construct(array $hierarchy, HttpRequest $request)
    {
        $this
            ->setHierarchy($hierarchy)
            ->setRequest($request)
        ;
    }

    /**
     * @phpstan-param array<MenuItemArray|null> $hierarchy
     * @phpstan-param MenuItemArray[] &$steps
     */
    private function createStepsToCurrent(array $hierarchy, array &$steps): bool
    {
        /** @phpstan-var MatchedRoute */
        $matchedRoute = $this->getRequest()->attributes['route'];

        foreach ($hierarchy as $menuItem) {
            if (!$menuItem) {
                continue;
            }

            $leafOrAncestor = false;

            if ($matchedRoute['id'] === $menuItem['routeId']) {
                $leafOrAncestor = true;
            } elseif (array_key_exists('children', $menuItem)) {
                /** @phpstan-var MenuItemArray[] */
                $childMenuItems = $menuItem['children'];
                $leafOrAncestor = $this->createStepsToCurrent($childMenuItems, $steps);
            }

            if ($leafOrAncestor) {
                array_unshift($steps, $menuItem);
                return true;
            }
        }

        return false;
    }

    /**
     * @phpstan-return MenuItemArray[]
     */
    public function getStepsToCurrent(): array
    {
        if (!isset($this->stepsToCurrent)) {
            $hierarchy = $this->getHierarchy();

            $stepsToCurrent = [];
            $this->createStepsToCurrent($hierarchy, $stepsToCurrent);

            $this->stepsToCurrent = $stepsToCurrent ?: $hierarchy;
        }

        return $this->stepsToCurrent;
    }

    /**
     * @phpstan-return MenuItemArray[]
     */
    public function getChildrenOfCurrent(): array
    {
        $stepsToCurrent = $this->getStepsToCurrent();
        $current = end($stepsToCurrent) ?: [];

        /** @phpstan-var MenuItemArray[] */
        return $current['children'] ?? [];
    }

    /**
     * @phpstan-param MenuItemArray[] $hierarchy
     */
    private function setHierarchy(array $hierarchy): self
    {
        $this->hierarchy = $hierarchy;
        return $this;
    }

    /**
     * @phpstan-return MenuItemArray[]
     */
    public function getHierarchy(): array
    {
        return $this->hierarchy;
    }

    private function setRequest(HttpRequest $request): self
    {
        $this->request = $request;
        return $this;
    }

    public function getRequest(): HttpRequest
    {
        return $this->request;
    }
}
