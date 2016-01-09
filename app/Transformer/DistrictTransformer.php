<?php

namespace app\Transformer;

use app\District;
use app\Group;
use League\Fractal\TransformerAbstract;

class DistrictTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'organizations',
        'groups',
        'users',
        'superAdmins',
        'admins',
        'members',
        'images',
    ];

    /**
     * Turn this item object into a generic array.
     *
     * @return array
     */
    public function transform(District $district)
    {
        $data = [
            'uuid' => $district->uuid,
            'system_id' => (int) $district->system_id,
            'code' => $district->code,
            'title' => $district->title,
            'can_update' => $district->canUpdate(),
            'description' => $district->description,
            'created_at' => (string) $district->created_at,
        ];

        if (isset($district->pivot->role_id)) {
            $data['role_id'] = $district->pivot->role_id;
        }

        return $data;
    }

    /**
     * Embed Image.
     *
     * @return League\Fractal\Resource\Collection
     */
    public function includeImages(District $district)
    {
        $image = $district->images;
        return $this->collection($image, new ImageTransformer());
    }

    /**
     * Include Organizations.
     *
     * @return League\Fractal\Resource\Collection
     */
    public function includeOrganizations(District $district)
    {
        $organizations = $district->organizations;

        return $this->collection($organizations, new OrganizationTransformer());
    }

    /**
     * Include Groups.
     *
     * @return League\Fractal\Resource\Collection
     */
    public function includeGroups(District $district)
    {
        $groups = $district->groups();

        return $this->collection($groups, new GroupTransformer());
    }

    /**
     * Include Users.
     *
     * @return League\Fractal\Resource\Collection
     */
    public function includeUsers(District $district)
    {
        $users = $district->users;

        return $this->collection($users, new UserTransformer());
    }

    /**
     * Include SuperAdmins.
     *
     * @return League\Fractal\Resource\Collection
     */
    public function includeSuperAdmins(District $district)
    {
        $superAdmins = $district->superAdmins;

        return $this->collection($superAdmins, new UserTransformer());
    }

    /**
     * Include Admins.
     *
     * @return League\Fractal\Resource\Collection
     */
    public function includeAdmins(District $district)
    {
        $admins = $district->admins;

        return $this->collection($admins, new UserTransformer());
    }

    /**
     * Include Members.
     *
     * @return League\Fractal\Resource\Collection
     */
    public function includeMembers(District $district)
    {
        $members = $district->members;

        return $this->collection($members, new UserTransformer());
    }
}
