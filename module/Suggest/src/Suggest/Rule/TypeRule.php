<?php

namespace Suggest\Rule;

use Suggest\SuggestionCollection;
use User\UserInterface;

/**
 * Only offer suggestions for the same user types
 *
 * @package Suggest\Engine
 */
class TypeRule implements RuleCompositeInterface
{
    /**
     * @inheritdoc
     */
    public function apply(SuggestionCollection $suggestionContainer, UserInterface $currentUser)
    {
        /** @var UserInterface $suggestion */
        iterator_apply(
            $suggestionContainer,
            function (UserInterface $suggestion) use (&$suggestionContainer, $currentUser) {
                // in case Removed on a previous iteration
                if (!$suggestionContainer->offsetExists($suggestion->getUserId())) {
                    return;
                }

                if ($suggestion->getType() !== $currentUser->getType()) {
                    $suggestionContainer->offsetUnset($suggestion->getUserId());
                }
            }
        );
    }
}
