<?php

namespace Feed\Rule\Action;

use Feed\Service\FeedUserServiceInterface;
use Rule\Action\ActionInterface;
use Rule\Event\Provider\FromEventParamProvider;

class injectUserFeedAction implements ActionInterface
{
    /**
     * @var FeedUserServiceInterface
     */
    protected $feedUseService;

    /**
     * @var string
     */
    protected $providerName;

    public function __construct(FeedUserServiceInterface $feedUserService, string $eventParamProvider = FromEventParamProvider::PROVIDER_NAME)
    {
    }
}
