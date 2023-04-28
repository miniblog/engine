<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests;

use DanBettles\Marigold\AbstractTestCase;
use DanBettles\Marigold\HttpRequest;
use Miniblog\Engine\WebsiteNav;

class WebsiteNavTest extends AbstractTestCase
{
    public function testIsInstantiable(): void
    {
        $hierarchy = [];
        $request = HttpRequest::createFromGlobals();

        $websiteNav = new WebsiteNav($hierarchy, $request);

        $this->assertSame($hierarchy, $websiteNav->getHierarchy());
        $this->assertSame($request, $websiteNav->getRequest());
    }

    public function testGetstepstocurrentReturnsTheStepsToTheCurrentItem(): void
    {
        $request = new HttpRequest([], [], []);

        $request->attributes['route'] = [
            'id' => 'showBaz',
        ];

        $websiteNav = new WebsiteNav([
            [
                'routeId' => 'showFoo',
                'content' => 'Foo',
                'children' => [
                    [
                        'routeId' => 'showBar',
                        'content' => 'Bar',
                        'children' => [
                            [
                                'routeId' => 'showBaz',
                                'content' => 'Baz',
                            ],
                        ],
                    ],
                ],
            ],
        ], $request);

        $this->assertSame([
            [
                'routeId' => 'showFoo',
                'content' => 'Foo',
                'children' => [
                    [
                        'routeId' => 'showBar',
                        'content' => 'Bar',
                        'children' => [
                            [
                                'routeId' => 'showBaz',
                                'content' => 'Baz',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'routeId' => 'showBar',
                'content' => 'Bar',
                'children' => [
                    [
                        'routeId' => 'showBaz',
                        'content' => 'Baz',
                    ],
                ],
            ],
            [
                'routeId' => 'showBaz',
                'content' => 'Baz',
            ],
        ], $websiteNav->getStepsToCurrent());
    }

    public function testGetstepstocurrentReturnsOnlyTheRootItemIfTheCurrentRouteIsNotInTheHierarchy(): void
    {
        $request = new HttpRequest([], [], []);

        $request->attributes['route'] = [
            'id' => 'showBar',
        ];

        $websiteNav = new WebsiteNav([
            [
                'routeId' => 'showFoo',
                'content' => 'Foo',
            ],
        ], $request);

        $this->assertSame([
            [
                'routeId' => 'showFoo',
                'content' => 'Foo',
            ],
        ], $websiteNav->getStepsToCurrent());
    }

    public function testGetchildrenofcurrentReturnsTheChildrenOfTheCurrentItem(): void
    {
        $request = new HttpRequest([], [], []);

        $request->attributes['route'] = [
            'id' => 'showBar',
        ];

        $websiteNav = new WebsiteNav([
            [
                'routeId' => 'showFoo',
                'content' => 'Foo',
                'children' => [
                    [
                        'routeId' => 'showBar',
                        'content' => 'Bar',
                        'children' => [
                            [
                                'routeId' => 'showBaz',
                                'content' => 'Baz',
                            ],
                        ],
                    ],
                ],
            ],
        ], $request);

        $this->assertSame([
            [
                'routeId' => 'showBaz',
                'content' => 'Baz',
            ],
        ], $websiteNav->getChildrenOfCurrent());
    }

    public function testGetchildrenofcurrentReturnsAnEmptyArrayIfTheCurrentItemHasNoChildren(): void
    {
        $request = new HttpRequest([], [], []);

        $request->attributes['route'] = [
            'id' => 'showFoo',
        ];

        $websiteNav = new WebsiteNav([
            [
                'routeId' => 'showFoo',
                'content' => 'Foo',
            ],
        ], $request);

        $this->assertSame([], $websiteNav->getChildrenOfCurrent());
    }
}
