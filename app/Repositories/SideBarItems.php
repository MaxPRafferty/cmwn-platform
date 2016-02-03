<?php

namespace app\Repositories;

use Illuminate\Support\Facades\Auth;

class SideBarItems
{
    public function getAll()
    {
        $tags = array();
        if (!Auth::check()) {
            $tags = array(
                'Home' => '/',
                'Login' => '/auth/login',
                'Register' => '/auth/register',
            );

            return $tags;
        }

        $user = Auth::user();
        if ($user->type == 1) {
            $tags = array(
                'Members' => '/users/members',
                'Roles' => '/users/members',
                'Districts' => '/districts',
                'Organizations' => '/organizations',
                'Groups' => '/groups',
                'Games' => '/games',
                'Edit Profile' => '/profile/edit',
            );

            return $tags;
        }

        $tags['Games'] = '/profile';

        $friendCount = $user->friends()->count();

        if ($friendCount > 0) {
            $tags['Friends'] = '/friends';
        }

        //Districts Menu
        $districtMembers = $user->getUserInRoleable('app\District')->wherePivot('user_id', $user->id);
        if ($districtMembers->count()) {
            if ($districtMembers->count() > 1) {
                $tags = array_add($tags, 'Districts', '/districts');
            } elseif ($districtMembers->count() === 1) {
                $tags[$districtMembers->get()[0]->title] = '/district/'.$districtMembers->get()[0]->uuid;
            }
        }

        //Organizations menu
        $organizationMembers = $user->getUserInRoleable('app\Organization')->wherePivot('user_id', $user->id);
        if ($organizationMembers->count()) {
            if ($organizationMembers->count() > 1) {
                $tags = array_add($tags, 'My Schools', '/organizations');
            } elseif ($organizationMembers->count() === 1) {
                $tags[$organizationMembers->get()[0]->title] = '/organization/'.$organizationMembers->get()[0]->uuid;
            }
        }

        //Groups menu
        $groupMembers = $user->getUserInRoleable('app\Group')->wherePivot('user_id', $user->id);

        if ($groupMembers->count()) {
            if ($groupMembers->count() > 1) {
                $tags = array_add($tags, 'My Classes', '/groups');
            } elseif ($groupMembers->count() === 1) {
                $tags[$groupMembers->get()[0]->title] = '/group/'.$groupMembers->get()[0]->uuid;
            }
        }

        $tags['Edit Profile'] = '/profile/edit';
        $tags['Logout'] = '/logout';

        return $tags;
    }
}
