<?php


namespace Suggest\Filter;

use Suggest\InvalidArgumentException;
use Suggest\SuggestionContainer;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class FilterCollection
 * @package Suggest\Filter
 */
class FilterCollection implements SuggestedFilterCompositeInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $service;

    /**
     * @var array
     */
    protected $filters;

    /**
     * @param ServiceLocatorInterface
     * @param array
     */
    public function __construct($service, $filters)
    {
        $this->service = $service;
        $this->createFiltersFromConfig($filters);
    }

    /**
     * @inheritdoc
     */
    public function getSuggestions($user)
    {
        $filterContainer = new SuggestionContainer();

        foreach ($this->filters as $filter) {
            $filterContainer->merge($filter->getSuggestions($user));
        }

        return $filterContainer;
    }

    /**
     * @param array $filters
     */
    protected function createFiltersFromConfig($filters)
    {
        foreach ($filters as $filter) {
            $filter = $this->service->get($filter);

            $this->addFilter($filter);
        }
    }

    /**
     * @param SuggestedFilterCompositeInterface $filter
     */
    public function addFilter($filter)
    {
        if (!$filter instanceof SuggestedFilterCompositeInterface) {
            throw new InvalidArgumentException("Invalid Filter");
        }

        $this->filters[] = $filter;
    }
}
