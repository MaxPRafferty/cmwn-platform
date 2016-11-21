<?php

namespace Suggest\Filter;

use Suggest\InvalidArgumentException;
use Suggest\InvalidFilterException;
use Suggest\SuggestionCollection;
use User\UserInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class FilterCollection
 *
 * @package Suggest\Filter
 */
class FilterCollection implements FilterCompositeInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $service;

    /**
     * @var array The config for filters
     */
    protected $filterConfig = [];

    /**
     * @var FilterCompositeInterface[] The built filters
     */
    protected static $filters;

    /**
     * FilterCollection constructor.
     *
     * @param ServiceLocatorInterface $service
     * @param array $filterConfig
     */
    public function __construct(ServiceLocatorInterface $service, array $filterConfig)
    {
        $this->service      = $service;
        $this->filterConfig = $filterConfig;
    }

    /**
     * @inheritdoc
     */
    public function getSuggestions(SuggestionCollection $suggestionContainer, UserInterface $user)
    {
        $this->createFiltersFromConfig();
        array_walk(
            self::$filters,
            function (FilterCompositeInterface $filter) use (&$suggestionContainer, &$user) {
                $filter->getSuggestions($suggestionContainer, $user);
            }
        );
    }

    /**
     * Creates the filters from the
     */
    protected function createFiltersFromConfig()
    {
        if (null !== self::$filters) {
            return;
        }

        array_walk($this->filterConfig, function ($filterKey) {
            if (!$this->service->has($filterKey)) {
                return;
            }

            $filter = $this->service->get($filterKey);
            if (!$filter instanceof FilterCompositeInterface) {
                throw new InvalidFilterException();
            }

            $this->addFilter($filter);
        });
    }

    /**
     * @param FilterCompositeInterface $filter
     */
    public function addFilter(FilterCompositeInterface $filter)
    {
        array_push(self::$filters, $filter);
    }
}
