<?php

namespace Kirby\Cms;

use Kirby\Toolkit\Str;

class Search
{

    public static function files(string $query = null, $params = [])
    {
        return App::instance()->site()->index()->files()->search($query, $params);
    }

    /**
     * Native search method to search for anything within the collection
     */
    public static function collection(Collection $collection, string $query = null, $params = [])
    {

        if (is_string($params) === true) {
            $params = ['fields' => Str::split($params, '|')];
        }

        $defaults = [
            'fields'    => [],
            'minlength' => 2,
            'score'     => [],
            'words'     => false,
        ];

        $options     = array_merge($defaults, $params);
        $collection  = clone $collection;
        $searchwords = preg_replace('/(\s)/u',',', $query);
        $searchwords = Str::split($searchwords, ',', $options['minlength']);

        if (empty($options['stopwords']) === false) {
            $searchwords = array_diff($searchwords, $options['stopwords']);
        }

        if (empty($searchwords) === true) {
            return $collection->limit(0);
        }

        $searchwords = array_map(function ($value) use ($options) {
            return $options['words'] ? '\b' . preg_quote($value) . '\b' : preg_quote($value);
        }, $searchwords);

        $preg    = '!(' . implode('|', $searchwords) . ')!i';
        $results = $collection->filter(function ($item) use ($query, $searchwords, $preg, $options) {

            $data = $item->content()->toArray();
            $keys = array_keys($data);
            $keys[] = 'id';

            if (empty($options['fields']) === false) {
                $keys = array_intersect($keys, $options['fields']);
            }

            $item->searchHits  = 0;
            $item->searchScore = 0;

            foreach ($keys as $key) {

                $score = $options['score'][$key] ?? 1;
                $value = $key === 'id' ? $item->id() : $data[$key];

                // check for a match
                if ($matches = preg_match_all($preg, $value, $r)) {

                    $item->searchHits  += $matches;
                    $item->searchScore += $matches * $score;

                    // check for full matches
                    if ($matches = preg_match_all('!' . preg_quote($query) . '!i', $value, $r)) {
                        $item->searchScore += $matches * $score;
                    }

                }

            }

            return $item->searchHits > 0 ? true : false;

        });

        return $results->sortBy('searchScore', SORT_DESC);
    }

    public static function pages(string $query = null, $params = [])
    {
        return App::instance()->site()->index()->search($query, $params);
    }

    public static function users(string $query = null, $params = [])
    {
        return App::instance()->users()->search($query, $params);
    }

}