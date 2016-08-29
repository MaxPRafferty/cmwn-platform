<?php

namespace Suggest\Engine;

use Job\JobInterface;
use Suggest\Filter\FilterCollection;
use Suggest\InvalidArgumentException;
use Suggest\Rule\SuggestedRuleCompositeInterface;
use Suggest\Service\SuggestedServiceInterface;
use Suggest\SuggestionContainer;
use User\Service\UserServiceInterface;
use User\UserInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class SuggestionEngine
 */
class SuggestionEngine implements JobInterface
{
    const MAX_CAPACITY = 100;
    /**
     * @var ServiceLocatorInterface
     */
    protected $service;

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var SuggestedServiceInterface
     */
    protected $suggestedService;
    /**
     * @var array
     */
    protected $rulesConfig;

    /**
     * @var array
     */
    protected $filtersConfig;

    /**
     * SuggestionEngine constructor.
     * @param ServiceLocatorInterface $locator
     * @param SuggestedServiceInterface $suggestedService
     * @param array $config
     */
    public function __construct($locator, $suggestedService, $config)
    {
        $this->service = $locator;
        $this->suggestedService = $suggestedService;
        $this->rulesConfig = isset($config['rules']) ? $config['rules'] : [];
        $this->filtersConfig = isset($config['filters']) ? $config['filters'] : [];
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        if (!$user instanceof UserInterface) {
            $userService = $this->service->get(UserServiceInterface::class);
            $user = $userService->fetchUser($user);
        }
        $this->user = $user;
    }

    /**
     * @inheritdoc
     * @throws InvalidArgumentException
     */
    public function perform()
    {
        if (!$this->getUser() instanceof UserInterface) {
            throw new \RuntimeException();
        }
        /**@var SuggestionContainer $masterContainer*/
        $masterContainer = new SuggestionContainer();

        $this->getFilterSuggestions($masterContainer);
        $this->applyRules($masterContainer);
        if (count($masterContainer) > SuggestionEngine::MAX_CAPACITY) {
            $masterContainer->exchangeArray(
                array_rand($masterContainer->getArrayCopy(), SuggestionEngine::MAX_CAPACITY)
            );
        }
        $this->attachSuggestions($masterContainer);
    }

    /**
     * @param SuggestionContainer $masterContainer
     */
    protected function attachSuggestions($masterContainer)
    {
        foreach ($masterContainer as $key => $suggestion) {
            $this->suggestedService->attachSuggestedFriendForUser($this->getUser(), $suggestion);
        }
    }

    /**
     * @param SuggestionContainer $masterContainer
     * @throws InvalidArgumentException
     */
    protected function getFilterSuggestions($masterContainer)
    {
        $filterCollection = new FilterCollection($this->service, $this->filtersConfig);
        $masterContainer->merge($filterCollection->getSuggestions($this->user));
    }

    /**
     * @param SuggestionContainer $masterContainer
     * @throws InvalidArgumentException
     */
    protected function applyRules($masterContainer)
    {
        foreach ($this->rulesConfig as $rule) {
            $rule = $this->service->get($rule);
            if (!$rule instanceof SuggestedRuleCompositeInterface) {
                throw new InvalidArgumentException("Invalid rule");
            }
            $rule->apply($masterContainer, $this->getUser());
        }
    }

    /**
     * @inheritdoc
     */
    public function getArrayCopy()
    {
        return [
            'user_id' => $this->getUser()!==null? $this->getUser()->getUserId() : null,
        ];
    }

    /**
     * @inheritdoc
     */
    public function exchangeArray(array $data)
    {
        if (isset($data['user_id'])) {
            $this->setUser($data['user_id']);
        }
    }
}
