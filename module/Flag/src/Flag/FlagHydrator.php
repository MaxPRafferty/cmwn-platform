<?php

namespace Flag;

use Application\Exception\NotFoundException;
use User\Service\UserServiceInterface;
use User\UserInterface;
use Zend\Hydrator\HydratorInterface;
use Zend\Stdlib\Extractor\ExtractionInterface;

/**
 * Class FlagHydrator
 * @package Flag
 */
class FlagHydrator implements HydratorInterface, ExtractionInterface
{
    /**
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * @var null|FlagInterface $prototype
     */
    protected $prototype;

    /**
     * FlagHydrator constructor.
     * @param UserServiceInterface $userService
     */
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @return FlagInterface|null
     */
    public function getPrototype()
    {
        return $this->prototype;
    }

    /**
     * @param FlagInterface|null $prototype
     */
    public function setPrototype($prototype)
    {
        $this->prototype = $prototype;
    }

    /**
     * @inheritdoc
     */
    public function extract($object)
    {
        if (!$object instanceof FlagInterface) {
            throw new \InvalidArgumentException('This Hydrator can only extract Flags');
        }

        return $object->getArrayCopy();
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function hydrate(array $data, $object)
    {
        if (!$object instanceof FlagInterface && $this->getPrototype() === null) {
            throw new \InvalidArgumentException("This Hydrator can only hydrate Flags");
        }

        try {
            $data['flagger'] = $data['flagger'] instanceof UserInterface ?
                $data['flagger'] : $this->userService->fetchUser($data['flagger']);
        } catch (NotFoundException $nf) {
            unset($data['flagger']);
        }

        try {
            $data['flaggee'] = $data['flaggee'] instanceof UserInterface ?
                $data['flaggee'] : $this->userService->fetchUser($data['flaggee']);
        } catch (NotFoundException $nf) {
            unset($data['flaggee']);
        }

        $object = $object instanceof FlagInterface ? $object : clone $this->prototype;
        $object->exchangeArray($data);

        return $object;
    }
}
