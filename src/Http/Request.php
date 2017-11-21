<?php

namespace Kirby\Http;

use Kirby\Http\Request\Body;
use Kirby\Http\Request\Query;
use Kirby\Http\Request\Method;
use Kirby\Http\Request\Files;

/**
 * The Request class provides
 * a simple API to inspect incoming
 * requests.
 *
 * @package   Kirby Http
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   MIT
 */
class Request
{

    /**
     * The Method object is a tiny
     * wrapper around the request method
     * name, which will validate and sanitize
     * the given name and always return
     * its uppercase version.
     *
     * Examples:
     *
     * `$request->method()->name()`
     * `$request->method()->is('post')`
     *
     * @var Method
     */
    protected $method;

    /**
     * The Query object is a wrapper around
     * the URL query string, which parses the
     * string and provides a clean API to fetch
     * particular parts of the query
     *
     * Examples:
     *
     * `$request->query()->get('foo')`
     *
     * @var Query
     */
    protected $query;

    /**
     * The Body object is a wrapper around
     * the request body, which parses the contents
     * of the body and provides an API to fetch
     * particular parts of the body
     *
     * Examples:
     *
     * `$request->body()->get('foo')`
     *
     * @var Body
     */
    protected $body;

    /**
     * The Files object is a wrapper around
     * the $_FILES global. It sanitizes the
     * $_FILES array and provides an API to fetch
     * individual files by key
     *
     * Examples:
     *
     * `$request->files()->get('upload')['size']`
     * `$request->file('upload')['size']`
     *
     * @var Files
     */
    protected $files;

    /**
     * Creates a new Request object
     * You can either pass your own request
     * data via the $options array or use
     * the data from the incoming request.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->method   = new Method($options['method'] ?? null);
        $this->query    = new Query($options['query']  ?? null);
        $this->body     = new Body($options['body']   ?? null);
        $this->files    = new Files($options['files']  ?? null);
    }

    /**
     * Returns the Method object
     *
     * @return Method
     */
    public function method(): Method
    {
        return $this->method;
    }

    /**
     * Returns the Query object
     *
     * @return Query
     */
    public function query(): Query
    {
        return $this->query;
    }

    /**
     * Returns the request input as array
     *
     * @return array
     */
    public function data()
    {
        return $this->is('GET') ? $this->query()->toArray() : $this->body()->toArray();
    }

    /**
     * Returns the Body object
     *
     * @return Body
     */
    public function body(): Body
    {
        return $this->body;
    }

    /**
     * Returns the Files object
     *
     * @return Files
     */
    public function files(): Files
    {
        return $this->files;
    }

    /**
     * Fetches a single file array
     * from the Files object by key
     *
     * @param  string $key
     * @return array|null
     */
    public function file(string $key)
    {
        return $this->files->get($key);
    }

    /**
     * Checks if the given method name
     * matches the name of the request method.
     *
     * @param  string  $method
     * @return boolean
     */
    public function is(string $method): bool
    {
        return $this->method()->is($method);
    }
}