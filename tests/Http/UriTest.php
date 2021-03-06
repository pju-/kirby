<?php

namespace Kirby\Http;

use PHPUnit\Framework\TestCase;

class UriTest extends TestCase
{

    protected $_SERVER = null;

    protected function setUp()
    {
        Uri::$current = null;

        $this->example1 = 'https://getkirby.com';
        $this->example2 = 'https://testuser:weakpassword@getkirby.com:3000/docs/getting-started/?q=awesome#top';

        $this->_SERVER = $_SERVER;
    }

    public function tearDown()
    {
        $_SERVER = $this->_SERVER;
    }

    public function testValidScheme()
    {
        $url = new Uri;

        $url->setScheme('http');
        $this->assertEquals('http', $url->scheme());

        $url->setScheme('https');
        $this->assertEquals('https', $url->scheme());
    }

    /**
     * @expectedException Kirby\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid URL scheme: abc
     */
    public function testInvalidScheme()
    {
        $url = new Uri;
        $url->setScheme('abc');
    }

    public function testValidHost()
    {
        $url = new Uri;

        $url->setHost('getkirby.com');
        $this->assertEquals('getkirby.com', $url->host());
    }

    public function testMissingHost()
    {
        $url = new Uri(['host' => false]);
        $this->assertEquals(null, $url->host());
    }

    public function testIsAbsolute()
    {
        $url = new Uri(['host' => 'localhost']);
        $this->assertTrue($url->isAbsolute());
    }

    public function testIsNotAbsolute()
    {
        $url = new Uri;
        $this->assertFalse($url->isAbsolute());
    }

    public function testValidPort()
    {
        $url = new Uri(['port' => 1234]);
        $this->assertEquals(1234, $url->port());

        $url = new Uri(['port' => null]);
        $this->assertEquals(null, $url->port());
    }

    public function testZeroPort()
    {
        $url = new Uri(['port' => 0]);
        $this->assertEquals(null, $url->port());
    }

    /**
     * @expectedException TypeError
     */
    public function testInvalidPortFormat1()
    {
        $url = new Uri(['port' => 'a']);
    }

    /**
     * @expectedException Kirby\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid port format: 12010210210
     */
    public function testInvalidPortFormat2()
    {
        $url = new Uri(['port' => 12010210210]);
    }

    public function testValidUsername()
    {
        $url = new Uri(['username' => 'testuser']);
        $this->assertEquals('testuser', $url->username());

        $url = new Uri(['username' => null]);
        $this->assertEquals(null, $url->username());
    }

    public function testValidPassword()
    {
        $url = new Uri(['password' => 'weakpassword']);
        $this->assertEquals('weakpassword', $url->password());

        $url = new Uri(['password' => null]);
        $this->assertEquals(null, $url->password());
    }

    public function testValidPath()
    {
        $url = new Uri(['path' => '/a/b/c']);
        $this->assertEquals('a/b/c', $url->path()->toString());

        $url = new Uri(['path' => ['a', 'b', 'c']]);
        $this->assertEquals('a/b/c', $url->path()->toString());

        $url = new Uri(['path' => null]);
        $this->assertTrue($url->path()->isEmpty());
    }

    public function testValidQuery()
    {
        $url = new Uri(['query' => 'foo=bar']);
        $this->assertEquals('foo=bar', $url->query()->toString());

        $url = new Uri(['query' => '?foo=bar']);
        $this->assertEquals('foo=bar', $url->query()->toString());

        $url = new Uri(['query' => ['foo' => 'bar']]);
        $this->assertEquals('foo=bar', $url->query()->toString());

        $url = new Uri(['query' => null]);
        $this->assertTrue($url->query()->isEmpty());
    }

    public function testValidFragment()
    {
        $url = new Uri(['fragment' => 'top']);
        $this->assertEquals('top', $url->fragment());

        $url = new Uri(['fragment' => '#top']);
        $this->assertEquals('top', $url->fragment());

        $url = new Uri(['fragment' => null]);
        $this->assertEquals(null, $url->fragment());
    }

    public function testAuth()
    {
        $url = new Uri(['username' => 'testuser', 'password' => 'weakpassword']);
        $this->assertEquals('testuser:weakpassword', $url->auth());
    }

    public function testBase()
    {
        $url = new Uri(['scheme' => 'https', 'host' => 'getkirby.com']);
        $this->assertEquals('https://getkirby.com', $url->base());

        $url->username = 'testuser';
        $url->password = 'weakpassword';

        $this->assertEquals('https://testuser:weakpassword@getkirby.com', $url->base());

        $url->port = 3000;
        $this->assertEquals('https://testuser:weakpassword@getkirby.com:3000', $url->base());
    }

    public function testBaseWithoutHost()
    {
        $url = new Uri;
        $this->assertEquals(null, $url->base());
    }

    public function testToArray()
    {
        $url = new Uri($this->example2);

        $this->assertEquals([
            'scheme'   => 'https',
            'host'     => 'getkirby.com',
            'port'     => 3000,
            'path'     => ['docs', 'getting-started'],
            'username' => 'testuser',
            'password' => 'weakpassword',
            'query'    => ['q' => 'awesome'],
            'fragment' => 'top',
            'params'   => [],
            'slash'    => true,
        ], $url->toArray());
    }

    public function testToString()
    {
        $url = new Uri($this->example1);
        $this->assertEquals($this->example1, $url->toString());
        $this->assertEquals($this->example1, (string)$url);

        $url = new Uri($this->example2);
        $this->assertEquals($this->example2, $url->toString());
        $this->assertEquals($this->example2, (string)$url);
    }

    public function testBuild()
    {

        // path + params + query
        $uri = new Uri('https://getkirby.com/search?q=something', [
            'params' => ['page' => 2]
        ]);

        $this->assertEquals('https://getkirby.com/search/page:2?q=something', $uri->toString());

        // path + slash + params + query
        $uri = new Uri('https://getkirby.com/search/?q=something', [
            'params' => ['page' => 2]
        ]);

        $this->assertEquals('https://getkirby.com/search/page:2/?q=something', $uri->toString());

        // params replacement
        $uri = new Uri('https://getkirby.com/search/page:2?q=something', [
            'params' => ['page' => 3]
        ]);

        $this->assertEquals('https://getkirby.com/search/page:3?q=something', $uri->toString());

        // params removal without slash
        $uri = new Uri('https://getkirby.com/search/page:2?q=something', [
            'params' => []
        ]);

        $this->assertEquals('https://getkirby.com/search?q=something', $uri->toString());

        // params removal with slash
        $uri = new Uri('https://getkirby.com/search/page:2/?q=something', [
            'params' => []
        ]);

        $this->assertEquals('https://getkirby.com/search/?q=something', $uri->toString());

    }

}
