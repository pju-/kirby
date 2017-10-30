<?php

namespace Kirby\Cms;

use Kirby\Data\Data;

class SiteBlueprint extends Blueprint
{

    public function __construct()
    {
        parent::__construct('site');
    }

    public function fallback() {

        return [
            'name'   => 'site',
            'title'  => 'Site',
            'layout' => [
                [
                    'width'    => '1/1',
                    'sections' => [
                        [
                            'headline' => 'Pages',
                            'type'     => 'pages',
                            'parent'   => '/'
                        ]
                    ]
                ]
            ]
        ];
    }

    public function data()
    {

        // TODO: remove the fixed default blueprint for the site
        return $this->data = $this->fallback();

        $data = parent::data();

        if ($this->isDefault() === true || empty($data['layout']) === true) {
            return $this->data = $this->fallback();
        }

        foreach ($data['layout'] as $layoutKey => $layoutColumn) {
            foreach ($layoutColumn['sections'] as $sectionKey => $sectionAttributes) {
                if ($sectionAttributes['type'] === 'fields') {
                    $fields = new Fields($this->model(), $sectionAttributes['fields']);
                    $data['layout'][$layoutKey]['sections'][$sectionKey]['fields'] = array_values($fields->toArray());
                }
            }
        }

        return $data;

    }

}