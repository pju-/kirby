<?php

namespace Kirby\Data\Handler;

use Kirby\Util\Str;
use Kirby\Data\Handler;

/**
 * Kirby Txt Data Handler
 *
 * @package   Kirby Data
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   MIT
 */
class Txt extends Handler
{

    /**
     * Converts an array to Kirby txt
     *
     * @param  array  $data
     * @return string
     */
    public static function encode(array $data): string
    {
        $result = [];

        foreach ($data as $key => $value) {
            if (empty($key) === true || $value === null) {
                continue;
            }

            $key          = Str::ucfirst(Str::slug($key));
            $value        = static::encodeValue($value);
            $result[$key] = static::encodeResult($key, $value);
        }

        return implode("\n\n----\n\n", $result);
    }

    /**
     * Helper for converting value
     *
     * @param  array|string  $value
     * @return string
     */
    protected static function encodeValue($value): string
    {
        // avoid problems with arrays
        if (is_array($value) === true) {
            $value = Yaml::encode($value);
        }

        // escape accidental dividers within a field
        $value = preg_replace('!(\n|^)----(.*?\R*)!', '$1\\----$2', $value);

        return $value;
    }

    /**
     * Helper for converting key and value to result string
     *
     * @param  string $key
     * @param  string $value
     * @return string
     */
    protected static function encodeResult(string $key, string $value): string
    {
        $result = $key . ': ';

        // multi-line content
        if (preg_match('!\R!', $value) === 1) {
            $result .= "\n\n";
        }

        $result .= trim($value);

        return $result;
    }

    /**
     * Parses Kirby txt and returns a multi-dimensional array
     *
     * @param  string $string
     * @return array
     */
    public static function decode(string $string): array
    {
        // remove BOM
        $string = str_replace('\xEF\xBB\xBF', '', $string);
        // explode all fields by the line separator
        $fields = preg_split('!\n----\s*\n*!', $string);
        // start the data array
        $data = [];

        // loop through all fields and add them to the content
        foreach ($fields as $field) {
            $result = static::decodeField($field);
            if ($result !== false) {
                $data[$result['key']] = $result['value'];
            }
        }

        return $data;
    }

    /**
     * Helper for parsing a single field to key and value
     *
     * @param  string       $field
     * @return array|false
     */
    protected static function decodeField(string $field)
    {
        $pos = strpos($field, ':');
        $key = str_replace(['-', ' '], '_', strtolower(trim(substr($field, 0, $pos))));

        // Don't add fields with empty keys
        if (empty($key) === true) {
            return false;
        }

        return ['key' => $key, 'value' => trim(substr($field, $pos + 1))];
    }
}
