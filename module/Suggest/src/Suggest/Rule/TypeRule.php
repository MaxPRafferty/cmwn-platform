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
    public function apply(SuggestionCollection $suggestionCollection, UserInterface $currentUser)
    {
        $suggestIterator = $suggestionCollection->getIterator();
        $suggestIterator->rewind();
        do {
            /** @var UserInterface $suggested */
            $suggested = $suggestIterator->current();
            $suggestIterator->next();
            if ($suggested === null) {
                break;
            }

            if ($suggested->getType() !== $currentUser->getType()) {
                $suggestionCollection->offsetUnset($suggested->getUserId());
            }
        } while (true);
    }
}
