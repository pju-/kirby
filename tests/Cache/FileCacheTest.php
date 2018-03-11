<?php

namespace Kirby\Cache;

use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage The cache directory does not exist
     */
    public function testSetupInvalidPath()
    {
        new FileCache('foo');
    }

    public function testSetGetRemove()
    {
        $root = __DIR__ . '/fixtures/file';
        $file = new FileCache($root);

        $file->set('foo', 'A basic value');
        $this->assertTrue(file_exists($root . '/foo'));

        $this->assertEquals('A basic value', $file->get('foo'));
        $this->assertEquals(time(), $file->created('foo'));


        $file->remove('foo');
        $this->assertFalse(file_exists($root . '/foo'));
    }

    public function testSetGetRemoveWithExtension()
    {
        $root = __DIR__ . '/fixtures/file';
        $file = new FileCache([
            'root'      => $root,
            'extension' => 'cache'
        ]);

        $file->set('foo', 'A basic value');
        $this->assertTrue(file_exists($root . '/foo.cache'));

        $this->assertEquals('A basic value', $file->get('foo'));
        $this->assertEquals(time(), $file->created('foo'));

        $file->remove('foo');
        $this->assertFalse(file_exists($root . '/foo.cache'));
    }

    public function testFlush()
    {
        $root = __DIR__ . '/fixtures/file';
        $file = new FileCache($root);

        $file->set('a', 'A basic value');
        $file->set('b', 'A basic value');
        $file->set('c', 'A basic value');
        $this->assertTrue(file_exists($root . '/a'));
        $this->assertTrue(file_exists($root . '/b'));
        $this->assertTrue(file_exists($root . '/c'));

        $file->flush();
        $this->assertFalse(file_exists($root . '/a'));
        $this->assertFalse(file_exists($root . '/b'));
        $this->assertFalse(file_exists($root . '/c'));
    }
}