<?php

namespace Kirby\Cms;

class BlueprintTabTest extends TestCase
{

    public function tab(array $props = [])
    {
        return new BlueprintTab(array_merge([
            'name'    => 'test',
            'columns' => []
        ], $props));
    }

    public function columns()
    {
        return [
            [
                'width'    => '1/3',
                'sections' => [
                    [
                        'name'   => 'fields',
                        'type'   => 'fields',
                        'fields' => [
                            [
                                'name' => 'title',
                                'type' => 'text'
                            ],
                            [
                                'name' => 'text',
                                'type' => 'textarea'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'width'    => '2/3',
                'sections' => [
                    [
                        'name'     => 'cover',
                        'type'     => 'files',
                        'headline' => 'Cover'
                    ],
                    [
                        'name'     => 'gallery',
                        'type'     => 'files',
                        'headline' => 'Gallery'
                    ]
                ]
            ]
        ];
    }

    public function testColumns()
    {
        $tab = $this->tab([
            'columns' => $this->columns()
        ]);

        $this->assertInstanceOf(Collection::class, $tab->columns());
        $this->assertCount(2, $tab->columns());
    }

    public function testEmptyColumns()
    {
        $this->assertInstanceOf(Collection::class, $this->tab()->columns());
        $this->assertCount(0, $this->tab()->columns());
    }

    public function testId()
    {
        $this->assertEquals('my-id', $this->tab(['id' => 'my-id'])->id());
    }

    public function testDefaultId()
    {
        $this->assertEquals('test', $this->tab()->id());
    }

    public function testLabel()
    {
        $this->assertEquals('Test', $this->tab(['label' => 'Test'])->label());
    }

    public function testDefaultLabel()
    {
        $this->assertEquals('Main', $this->tab()->label());
    }

    public function testName()
    {
        $this->assertEquals('test', $this->tab()->name());
    }

    public function testSections()
    {
        $tab = $this->tab([
            'columns' => $this->columns()
        ]);

        $this->assertInstanceOf(Collection::class, $tab->sections());
        $this->assertCount(3, $tab->sections());
        $this->assertEquals('fields', $tab->sections()->first()->name());
        $this->assertEquals('gallery', $tab->sections()->last()->name());
    }

    public function testSection()
    {
        $tab = $this->tab([
            'columns' => $this->columns()
        ]);

        $this->assertInstanceOf(BlueprintSection::class, $tab->section('fields'));
        $this->assertEquals('fields', $tab->section('fields')->name());
    }

    public function testMissingSection()
    {
        $tab = $this->tab([
            'columns' => $this->columns()
        ]);

        $this->assertNull($tab->section('something'));
    }

}