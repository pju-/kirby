<?php

namespace Kirby\Cms;

use PHPUnit\Framework\TestCase;

class ContentTranslationTest extends TestCase
{

    public function testParentAndCode()
    {
        $page = new Page([
            'slug' => 'test'
        ]);

        $translation = new ContentTranslation([
            'parent' => $page,
            'code'   => 'de'
        ]);

        $this->assertEquals($page, $translation->parent());
        $this->assertEquals('de', $translation->code());
    }

    public function testContentAndSlug()
    {
        $page = new Page([
            'slug' => 'test'
        ]);

        $translation = new ContentTranslation([
            'parent'  => $page,
            'code'    => 'de',
            'slug'    => 'test',
            'content' => $content = [
                'title' => 'test'
            ]
        ]);

        $this->assertEquals('test', $translation->slug());
        $this->assertEquals($content, $translation->content());
    }

    public function testContentFile()
    {
        $page = new Page([
            'slug'     => 'test',
            'root'     => '/test',
            'template' => 'project'
        ]);

        $translation = new ContentTranslation([
            'parent' => $page,
            'code'   => 'de',
        ]);

        $this->assertEquals('/test/project.de.txt', $translation->contentFile());
    }

}
