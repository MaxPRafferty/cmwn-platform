<?php

namespace Api\V1\Rest\User;

use Api\ScopeAwareInterface;
use Friend\FriendInterface;
use Friend\FriendTrait;
use User\User;
use User\UserInterface;

/**
 * A UserEntity represents a user through the API
 *
 * @SWG\Definition(
 *     description="A UserEntity represents a user through the API",
 *     @SWG\Property(
 *         type="object",
 *         property="_links",
 *         description="Links the user might have",
 *         allOf={
 *             @SWG\Schema(ref="#/definitions/SelfLink"),
 *         }
 *     ),
 *     allOf={
 *         @SWG\Schema(ref="#/definitions/User"),
 *         @SWG\Schema(ref="#/definitions/SelfLink"),
 *     }
 * )
 */
class UserEntity extends User implements
    UserInterface,
    ScopeAwareInterface
{
    /**
     * @var string
     */
    protected $type;

    /**
     * There are different type of user but the entity is agnostic to those classes
     *
     * @param $type
     */
    protected function setType($type)
    {
        if ($this->type === null && !empty($type)) {
            $this->type = $type;
        }
    }

    /**
     * @inheritdoc
     */
    public function getType(): string
    {
        return (string)$this->type;
    }

    /**
     * @inheritdoc
     */
    public function getEntityType()
    {
        return strtolower($this->getType());
    }
}
