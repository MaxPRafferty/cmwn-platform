<?php

namespace SearchTest\Service;

/**
 * Class SearchPrototype
 */
class SearchPrototype
{
    protected $data;

    public function populate(array $data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
}
