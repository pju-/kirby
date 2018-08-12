<?php

namespace Kirby\Cms;

use Kirby\Form\Field as FormField;
use Kirby\Image\Image;
use Kirby\Toolkit\I18n;

class DummyPage extends Page {}

class AppPluginsTest extends TestCase
{

    public function setUp()
    {
        App::destroy();
    }

    public function testBlueprint()
    {
        $kirby = new App([
            'blueprints' => [
                'pages/test' => $file = 'test.yml'
            ]
        ]);

        $this->assertEquals($file, $kirby->extension('blueprints', 'pages/test'));
    }

    public function testCollection()
    {
        $pages = new Pages([]);
        $kirby = new App([
            'collections' => [
                'test' => function () use ($pages) {
                    return $pages;
                }
            ],
        ]);

        $this->assertEquals($pages, $kirby->collection('test'));
    }

    public function testController()
    {
        $kirby = new App([
            'controllers' => [
                'test' => function () {
                    return ['foo' => 'bar'];
                }
            ]
        ]);

        $this->assertEquals(['foo' => 'bar'], $kirby->controller('test'));
    }

    public function testFieldMethod()
    {
        $kirby = new App([
            'fieldMethods' => [
                'test' => function () {
                    return 'test';
                }
            ]
        ]);

        $page = new Page(['slug' => 'test']);
        $this->assertEquals('test', $page->customField()->test());

        // reset methods
        Field::$methods = [];
    }

    public function testField()
    {
        $app = new App([
            'fields' => [
                'dummy' => __DIR__ . '/fixtures/fields/DummyField.php'
            ]
        ]);

        $field = new FormField([
            'type'  => 'dummy',
            'name'  => 'dummy',
            'peter' => 'shaw'
        ]);

        $this->assertInstanceOf(FormField::class, $field);
        $this->assertEquals('simpson', $field->homer());
        $this->assertEquals('shaw', $field->peter());
    }

    public function testHook()
    {
        $phpUnit  = $this;

        $kirby = new App([
            'hooks' => [
                'testHook' => function ($message) use ($phpUnit, &$executed) {
                    $phpUnit->assertEquals('test', $message);
                }
            ]
        ]);

        $kirby->trigger('testHook', 'test');
    }

    public function testHooks()
    {
        $phpUnit  = $this;
        $executed = 0;

        $kirby = new App([
            'hooks' => [
                'testHook' => [
                    function ($message) use ($phpUnit, &$executed) {
                        $phpUnit->assertEquals('test', $message);
                        $executed++;
                    },
                    function ($message) use ($phpUnit, &$executed) {
                        $phpUnit->assertEquals('test', $message);
                        $executed++;
                    }
                ]
            ]
        ]);

        $kirby->trigger('testHook', 'test');
        $this->assertEquals(2, $executed);

    }

    public function testPageMethod()
    {
        $kirby = new App([
            'pageMethods' => [
                'test' => function () {
                    return 'test';
                }
            ]
        ]);

        $page = new Page(['slug' => 'test']);
        $this->assertEquals('test', $page->test());

        // reset methods
        Page::$methods = [];
    }

    public function testPageModel()
    {
        $kirby = new App([
            'pageModels' => [
                'dummy' => DummyPage::class
            ]
        ]);

        $page = Page::factory([
            'slug'  => 'test',
            'model' => 'dummy'
        ]);

        $this->assertInstanceOf(DummyPage::class, $page);
    }

    public function testPageModelFromFolder()
    {
        $kirby = new App([
            'roots' => [
                'models' => __DIR__ . '/fixtures/models'
            ]
        ]);

        $page = Page::factory([
            'slug' => 'test',
            'model' => 'test'
        ]);

        $this->assertInstanceOf('TestPage', $page);
    }

    public function testOption()
    {
        $kirby = new App([
            'options' => [
                'testOption' => 'testValue'
            ]
        ]);

        $this->assertEquals('testValue', $kirby->option('testOption'));
    }

    public function testPluginOption()
    {
        App::plugin('test/plugin', [
            'options' => [
                'foo' => 'bar'
            ]
        ]);

        $kirby = new App([
            'options' => [
                'test.plugin.foo' => 'another-bar'
            ]
        ]);

        $this->assertEquals('another-bar', $kirby->option('test.plugin.foo'));
    }

    public function testRoutes()
    {
        $kirby = new App([
            'routes' => [
                [
                    'pattern' => 'test',
                    'action'  => function () {
                        return 'test';
                    }
                ]
            ]
        ]);

        $this->assertEquals('test', $kirby->call('test'));
    }

    public function testRoutesCallback()
    {
        $kirby = new App([
            'routes' => function () {
                return [
                    [
                        'pattern' => 'test',
                        'action'  => function () {
                            return 'test';
                        }
                    ]
                ];
            }
        ]);

        $this->assertEquals('test', $kirby->call('test'));
    }

    public function testSnippet()
    {
        $kirby = new App([
            'snippets' => [
                'header' => $file = 'header.php'
            ]
        ]);

        $this->assertEquals($file, $kirby->extension('snippets', 'header'));
    }

    public function testTemplate()
    {
        $kirby = new App([
            'templates' => [
                'project' => $file = 'project.php'
            ]
        ]);

        $this->assertEquals($file, $kirby->extension('templates', 'project'));
    }

    public function testEmail()
    {
        $kirby = new App([
            'emails' => [
                'contact' => $file = 'contact.php'
            ]
        ]);

        $this->assertEquals($file, $kirby->extension('emails', 'contact'));
    }

    public function testEmailInPlugin()
    {
        App::plugin('test/test', [
            'emails' => [
                'contact' => $file = 'contact.php'
            ]
        ]);

        $kirby = new App();
        $this->assertEquals($file, $kirby->extension('emails', 'contact'));
    }

    public function testTranslation()
    {

        $kirby = new App([
            'translations' => [
                'en' => [
                    'test' => 'English Test'
                ],
                'de' => [
                    'test' => 'Deutscher Test'
                ]
            ]
        ]);

        I18n::$locale = 'en';

        $this->assertEquals('English Test', I18n::translate('test'));

        I18n::$locale = 'de';

        $this->assertEquals('Deutscher Test', I18n::translate('test'));
    }

    public function testTranslationsInPlugin()
    {

        App::plugin('test/test', [
            'translations' => [
                'en' => [
                    'test' => 'English Test'
                ],
                'de' => [
                    'test' => 'Deutscher Test'
                ]
            ]
        ]);

        new App();

        I18n::$locale = 'en';

        $this->assertEquals('English Test', I18n::translate('test'));

        I18n::$locale = 'de';

        $this->assertEquals('Deutscher Test', I18n::translate('test'));

    }

}
