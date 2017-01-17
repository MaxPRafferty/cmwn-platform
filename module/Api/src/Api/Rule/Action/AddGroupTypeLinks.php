<?php

namespace Api\Rule\Action;

use Api\Links\GroupLink;
use Group\GroupInterface;
use Group\Service\GroupServiceInterface;
use Rule\Action\ActionInterface;
use Rule\Item\RuleItemInterface;
use ZF\Hal\Link\LinkCollection;

/**
 * An Action that will add
 */
class AddGroupTypeLinks implements ActionInterface
{
    /**
     * @var GroupServiceInterface
     */
    protected $groupService;

    /**
     * AddGroupTypeLinks constructor.
     *
     * @param GroupServiceInterface $groupService
     */
    public function __construct(GroupServiceInterface $groupService)
    {
        $this->groupService = $groupService;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(RuleItemInterface $item)
    {
        /** @var GroupInterface $group */
        /** @var LinkCollection $links */
        $group = $item->getParam('group');
        $links = $item->getParam('links');
        $types = $this->groupService->fetchChildTypes($group);
        array_walk($types, function ($type) use (&$links, &$group) {
            $links->add(new GroupLink($type, $group->getGroupId()));
        });
    }
}
