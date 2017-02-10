<?php

namespace Search;

use Search\Exception\RuntimeException;
use Zend\Hydrator\HydratorInterface;
use Zend\Stdlib\ArrayObject;

/**
 * Special hydrator to deal with types from elastic and hydrate them correctly
 *
 * Uses a config to determine which hydrator to use
 */
class ElasticHydrator implements HydratorInterface
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var HydratorInterface
     */
    protected $defaultHydrator;

    /**
     * ElasticHydrator constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config[static::class] ?? [];
        if (!isset($this->config['default_hydrator'])) {
            throw new RuntimeException('No default hydrator class provided to: ' . static::class);
        }
    }

    /**
     * @return HydratorInterface
     */
    protected function getDefaultHydrator(): HydratorInterface
    {
        if (null === $this->defaultHydrator) {
            $this->defaultHydrator = new $this->config['default_hydrator']();
        }

        return $this->defaultHydrator;
    }

    /**
     * @inheritDoc
     */
    public function extract($object)
    {
        return $this->getHydratorFromType($this->getTypeFromObject($object))
            ->extract($object);
    }

    /**
     * @inheritDoc
     */
    public function hydrate(array $data, $object)
    {
        $type = isset($data['_type']) ? $data['_type'] : null;
        if ($type === null) {
            throw new RuntimeException('No type is set with data from');
        }

        return $this->getHydratorFromType($type)
            ->hydrate(
                $data['_source'],
                $this->getObjectFromType($type, $object)
            );
    }

    /**
     * @param $object
     *
     * @return string
     */
    public function getTypeFromObject($object): string
    {
        if ($object instanceof SearchableDocumentInterface) {
            return $object->getDocumentType();
        }

        foreach ($this->config as $type => $spec) {
            if (is_array($spec) && isset($spec['interface']) && $object instanceof $spec['interface']) {
                return $type;
            }
        }

        throw new RuntimeException(sprintf('No Type configured for: %s', get_class($object)));
    }

    /**
     * @param $type
     *
     * @return mixed|ArrayObject
     */
    protected function getObjectFromType($type, $object)
    {
        if (null !== $object) {
            return $object;
        }

        return new ArrayObject();
    }

    /**
     * @param $type
     *
     * @return HydratorInterface
     */
    protected function getHydratorFromType($type): HydratorInterface
    {
        // Make sure we have a config for this type and a hydrator is set for it
        if (!isset($this->config[$type]) || // make sure we have the type
            !isset($this->config[$type]['hydrator'])
        ) {
            return $this->getDefaultHydrator();
        }

        $className = $this->config[$type]['hydrator'];
        // use the default hydrator if the configured is the default or not a hydrator
        if ($className == $this->config['default_hydrator'] ||
            !in_array(HydratorInterface::class, class_implements($className))
        ) {
            return $this->getDefaultHydrator();
        }

        return new $className();
    }
}
