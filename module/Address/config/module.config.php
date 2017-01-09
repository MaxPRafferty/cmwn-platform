<?php

return [
    \Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory::class => [
        \Address\Service\AddressService::class => ['Table/Addresses'],
    ],
];
