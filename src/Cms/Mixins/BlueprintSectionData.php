<?php

namespace Kirby\Cms\Mixins;

use Exception;
use Kirby\Cms\File;

trait BlueprintSectionData
{

    use BlueprintSectionMax;
    use BlueprintSectionMin;

    protected $data;
    protected $error;
    protected $item;
    protected $limit = 20;
    protected $originalData;
    protected $parent;
    protected $query;
    protected $pagination;

    protected function convertDataToArray(): array
    {
        return $this->result();
    }

    protected function convertPaginationToArray(): array
    {
        $pagination = $this->pagination();

        return [
            'limit' => $pagination->limit(),
            'page'  => $pagination->page(),
            'total' => $pagination->total(),
        ];
    }

    protected function convertParentToArray()
    {
        $parent = $this->parent();

        if (is_a($parent, Page::class) === true) {
            return $parent->id();
        }

        return null;
    }

    public function data()
    {
        if (is_a($this->data, static::ACCEPT) === true) {
            return $this->data;
        }

        $data = $this->stringQuery($this->query());

        if (is_a($data, static::ACCEPT) === false) {
            throw new Exception('Invalid data type');
        }

        $this->originalData = $data;

        // apply the default pagination
        return $this->data = $data->paginate([
            'page'  => 1,
            'limit' => $this->limit()
        ]);

    }

    protected function defaultQuery(): string
    {
        return 'site.children';
    }

    public function error()
    {
        try {
            $this->validate();
            return null;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function isFull(): bool
    {
        if ($max = $this->max()) {
            return $this->total() >= $this->max();
        }

        return false;
    }

    public function item(): array
    {
        return $this->item ?? [];
    }

    protected function itemImageResult($item, $stringTemplateData)
    {
        $image = $this->itemValue($item, 'image', $stringTemplateData);

        if ($image !== null && is_a($image, File::class) === true && $image->type() === 'image') {
            return $image->url();
        }

        return null;
    }

    protected function itemLink($item)
    {
        return $item->id();
    }

    protected function itemTitle($item)
    {
        return $item->title()->value();
    }

    protected function itemToResult($item)
    {

        $stringTemplateData = [$this->modelType($item) => $item];

        return [
            'title' => $this->itemValue($item, 'title', $stringTemplateData),
            'image' => $this->itemImageResult($item, $stringTemplateData),
            'link'  => $this->itemLink($item),
            'info'  => $this->itemValue($item, 'info', $stringTemplateData),
        ];
    }

    protected function itemValue($item, string $key, array $data)
    {
        if ($value = $this->item[$key] ?? null) {
            return $this->stringTemplate($value, $data);
        }

        if (method_exists($this, 'item' . $key) === true) {
            return $this->{'item' . $key}($item);
        }

        return null;
    }

    public function limit(): int
    {
        return $this->limit;
    }

    public function originalData()
    {
        if (is_a($this->originalData, static::ACCEPT) === true) {
            return $this->originalData;
        }

        // query the data first
        $this->data();

        return $this->originalData;
    }

    public function paginate(int $page = 1, int $limit = null)
    {
        // overwrite the default pagination by using the original data set
        $this->data = $this->originalData()->paginate([
            'page'  => $page,
            'limit' => $limit ?? $this->limit()
        ]);

        return $this;
    }

    public function pagination()
    {
        return $this->data()->pagination();
    }

    public function parent()
    {
        if ($parent = $this->data()->parent()) {
            return $parent;
        }

        return $this->model();
    }

    public function query(): string
    {
        return $this->query;
    }

    public function result(): array
    {
        $result = [];

        foreach ($this->data() as $item) {
            $result[] = $this->itemToResult($item);
        }

        return $result;
    }

    protected function setItem(array $item = null)
    {
        $this->item = $item;
        return $this;
    }

    protected function setLimit(int $limit = null)
    {
        $this->limit = $limit;
        return $this;
    }

    protected function setQuery(string $query)
    {
        $this->query = $query;
        return $this;
    }

    public function total(): int
    {
        return $this->pagination()->total();
    }

    protected function validate(): bool
    {
        $this->validateMin();
        $this->validateMax();

        return true;
    }

}
