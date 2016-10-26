<?php

namespace Api\V1\Rest\GameData;

use Game\Service\SaveGameServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class GameDataResourceFactory
 * @package Api\V1\Rest\GameData
 */
class GameDataResourceFactory implements FactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /**@var SaveGameServiceInterface $saveGameService*/
        $saveGameService = $serviceLocator->get(SaveGameServiceInterface::class);
        return new GameDataResource($saveGameService);
    }
}
