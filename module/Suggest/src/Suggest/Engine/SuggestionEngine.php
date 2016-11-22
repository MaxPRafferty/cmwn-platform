<?php

namespace Suggest\Engine;

use Application\Utils\NoopLoggerAwareTrait;
use Job\JobInterface;
use Suggest\Filter\FilterCollection;
use Suggest\InvalidArgumentException;
use Suggest\Rule\RuleCollection;
use Suggest\Service\SuggestedServiceInterface;
use Suggest\SuggestionCollection;
use User\Service\UserServiceInterface;
use User\UserInterface;
use Zend\Log\LoggerAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class SuggestionEngine
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SuggestionEngine implements JobInterface, LoggerAwareInterface
{
    use NoopLoggerAwareTrait;

    /**
     * Max number of suggestions to build
     */
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
     * @var RuleCollection
     */
    protected $rules;

    /**
     * @var FilterCollection
     */
    protected $filters;

    /**
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * @var SuggestionCollection
     */
    protected $collection;

    /**
     * SuggestionEngine constructor.
     *
     * @param RuleCollection $ruleCollection
     * @param FilterCollection $filterCollection
     * @param SuggestedServiceInterface $suggestedService
     * @param UserServiceInterface $userService
     */
    public function __construct(
        RuleCollection $ruleCollection,
        FilterCollection $filterCollection,
        SuggestedServiceInterface $suggestedService,
        UserServiceInterface $userService
    ) {
        $this->rules            = $ruleCollection;
        $this->filters          = $filterCollection;
        $this->suggestedService = $suggestedService;
        $this->userService      = $userService;
        $this->collection       = new SuggestionCollection();
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
        if (null !== $user && !$user instanceof UserInterface) {
            $user = $this->userService->fetchUser($user);
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
            $this->getLogger()->crit('Missing user for suggestion engine');
            throw new \RuntimeException('Missing user for suggestion engine');
        }

        $this->getLogger()->notice('Performing suggestions for: ' . $this->getUser());
        $this->getLogger()->debug('Deleting current suggestions');
        $this->suggestedService->deleteAllSuggestionsForUser($this->getUser());

        $this->getLogger()->debug('Running filters');
        $this->filters->getSuggestions($this->collection, $this->getUser());

        $this->getLogger()->debug('Applying rules');
        $this->rules->apply($this->collection, $this->getUser());

        $this->getLogger()->debug(sprintf('Found %d suggestions', $this->collection->count()));

        // This is forward thinking to allow the collection to
        // decide how to weight suggestions
        $this->collection->asort();

        $suggestionCount = 0;
        $iterator        = $this->collection->getIterator();
        $iterator->rewind();
        do {
            $suggestionCount++;
            if ($suggestionCount > self::MAX_CAPACITY) {
                break;
            }

            $suggestedUser = $iterator->current();
            $iterator->next();
            if ($suggestedUser === null) {
                break;
            }

            $this->getLogger()->info(sprintf('Suggesting %s for %s', $suggestedUser, $this->getUser()));
            $this->suggestedService->attachSuggestedFriendForUser($this->getUser(), $suggestedUser);
        } while (true);
    }

    /**
     * @inheritdoc
     */
    public function getArrayCopy()
    {
        return [
            'user_id' => $this->getUser() !== null ? $this->getUser()->getUserId() : null,
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
