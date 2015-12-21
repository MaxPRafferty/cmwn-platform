<?php

namespace app\Transformer;

use app\Organization;
use League\Fractal\TransformerAbstract;
use app\User;
use Illuminate\Support\Facades\Auth;

class OrganizationTransformer extends TransformerAbstract
{

    protected $availableIncludes = [
        'districts',
        'groups',
        'users',
        'superAdmins',
        'admins',
        'members'
    ];

    /**
     * Turn this item object into a generic array.
     *
     * @return array
     */
    public function transform(Organization $organization)
    {
        $user = new User();
        return [
            'uuid'          => $organization->uuid,
            'code'          => $organization->code,
            'title'         => $organization->title,
            'description'   => $organization->description,
            'canupdate'     => $user->canUserUpdateObject('organizations', $organization->id),
            'created_at'    => (string) $organization->created_at,
        ];
    }

    /**
     * Include Districts.
     *
     * @return League\Fractal\Resource\Collection
     */
    public function includeDistricts(Organization $organization)
    {
        $districts = $organization->districts;
        return $this->collection($districts, new DistrictTransformer());
    }

    /**
     * Include Groups.
     *
     * @return League\Fractal\Resource\Collection
     */
    public function includeGroups(Organization $organization)
    {
        $groups = $organization->groups;

        return $this->collection($groups, new GroupTransformer());
    }

    /**
     * Include Users.
     *
     * @return League\Fractal\Resource\Collection
     */
    public function includeUsers(Organization $organization)
    {
        $users = $organization->users;
        return $this->collection($users, new UserTransformer());
    }

    /**
     * Include SuperAdmins.
     *
     * @return League\Fractal\Resource\Collection
     */
    public function includeSuperAdmins(Organization $organization)
    {
        $superAdmins = $organization->superAdmins;
        return $this->collection($superAdmins, new UserTransformer());
    }

    /**
     * Include Admins.
     *
     * @return League\Fractal\Resource\Collection
     */
    public function includeAdmins(Organization $organization)
    {
        $admins = $organization->admins;
        return $this->collection($admins, new UserTransformer());
    }

    /**
     * Include Members.
     *
     * @return League\Fractal\Resource\Collection
     */
    public function includeMembers(Organization $organization)
    {
        $members = $organization->members;

        return $this->collection($members, new UserTransformer());
    }
}
