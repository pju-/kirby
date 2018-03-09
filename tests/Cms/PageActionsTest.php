<?php

namespace Kirby\Cms;

class PageActionsTestStore extends PageStoreDefault
{

    public static $exists = true;

    public function exists(): bool
    {
        return static::$exists;
    }

    public function delete(): bool
    {
        static::$exists = false;
        return true;
    }

}

class PageActionsTest extends TestCase
{

    public function setUp()
    {
        App::removePlugins();
    }

    public function pageDummy()
    {
        return new Page([
            'slug'     => 'test',
            'template' => 'test',
            'store'    => PageActionsTestStore::class
        ]);
    }

    public function testChangeSlug()
    {
        $this->assertHooks([
            'page.changeSlug:before' => function (Page $page, string $slug) {
                $this->assertEquals('test', $page->slug());
                $this->assertEquals('awesome', $slug);
            },
            'page.changeSlug:after' => function (Page $newPage, Page $oldPage) {
                $this->assertEquals('awesome', $newPage->slug());
                $this->assertEquals('test', $oldPage->slug());
            }
        ], function () {
            $result = $this->pageDummy()->changeSlug('awesome');
            $this->assertEquals('awesome', $result->slug());
        });
    }

    public function testChangeTemplate()
    {
        $this->assertHooks([
            'page.changeTemplate:before' => function (Page $page, string $template) {
                $this->assertEquals('test', $page->template());
                $this->assertEquals('awesome', $template);
            },
            'page.changeTemplate:after' => function (Page $newPage, Page $oldPage) {
                $this->assertEquals('awesome', $newPage->template());
                $this->assertEquals('test', $oldPage->template());
            }
        ], function () {
            $result = $this->pageDummy()->changeTemplate('awesome');
            $this->assertEquals('awesome', $result->template());
        });
    }

    public function testChangeTitle()
    {
        $this->assertHooks([
            'page.changeTitle:before' => function (Page $page, string $title) {
                $this->assertEquals('test', $page->title()->value());
                $this->assertEquals('awesome', $title);
            },
            'page.changeTitle:after' => function (Page $newPage, Page $oldPage) {
                $this->assertEquals('awesome', $newPage->title()->value());
                $this->assertEquals('test', $oldPage->title()->value());
            }
        ], function () {
            $result = $this->pageDummy()->changeTitle('awesome');
            $this->assertEquals('awesome', $result->title()->value());
        });
    }

    public function testCreate()
    {
        $this->assertHooks([
            'page.create:before' => function (Page $parent = null, array $props) {
                $this->assertEquals('test', $props['slug']);
                $this->assertEquals('test', $props['template']);
            },
            'page.create:after' => function (Page $page) {
                $this->assertEquals('test', $page->slug());
                $this->assertEquals('test', $page->template());
            }
        ], function () {
            $page = Page::create([
                'slug'     => 'test',
                'template' => 'test',
                'site'     => new Site(),
                'store'    => PageActionsTestStore::class
            ]);
        });
    }

    public function testCreateChild()
    {
        $parent = $this->pageDummy();

        $this->assertHooks([
            'page.create:before' => function (Page $parentPage = null, array $props) use ($parent) {
                $this->assertEquals('test-child', $props['slug']);
                $this->assertEquals($parentPage, $parent);
            },
            'page.create:after' => function (Page $page) {
                $this->assertEquals('test-child', $page->slug());
            }
        ], function () use ($parent) {
            $child = $parent->createChild([
                'slug' => 'test-child',
            ]);

            $this->assertEquals($child->parent(), $parent);
        });
    }

    public function testCreateFile()
    {

    }

    public function testDelete()
    {
        PageActionsTestStore::$exists = true;

        $this->assertHooks([
            'page.delete:before' => function (Page $page, bool $force) {
                $this->assertTrue($page->exists());
                $this->assertTrue($force);
            },
            'page.delete:after' => function (Page $page, bool $result) {
                $this->assertFalse($page->exists());
            }
        ], function () {
            $this->pageDummy()->delete($force = true);
        });
    }

    public function testHide()
    {
        $pageDummmy = $this->pageDummy()->clone([
            'num' => 1
        ]);

        $this->assertHooks([
            'page.hide:before' => function (Page $page) {
                $this->assertEquals(1, $page->num());
            },
            'page.hide:after' => function (Page $newPage, Page $oldPage) {
                $this->assertEquals(null, $newPage->num());
                $this->assertEquals(1, $oldPage->num());
            }
        ], function () use ($pageDummmy) {
            $result = $pageDummmy->hide(1);
            $this->assertEquals(null, $result->num());
        });
    }

    public function testSort()
    {
        $this->assertHooks([
            'page.sort:before' => function (Page $page, int $num = null) {
                $this->assertEquals(1, $num);
            },
            'page.sort:after' => function (Page $newPage, Page $oldPage) {
                $this->assertEquals(1, $newPage->num());
                $this->assertEquals(null, $oldPage->num());
            }
        ], function () {
            $result = $this->pageDummy()->sort(1);
            $this->assertEquals(1, $result->num());
        });
    }

}
