<?php

namespace app\Repositories;
use Illuminate\Support\Facades\Auth;

class SideBarItems
{
    protected $role = null;
	public function __construct(){
		if (!Auth::check()){
			return false;
		}
		if ($role = Auth::user()->role) {
			foreach ($role as $rol) {
				$this->role[] = $rol->title;
			}
		}
		if (Auth::check()) {
            if (Auth::user()->type==1) {
                unset($this->role);
                $this->role[] = 'site_admin';
            }
        }
    }


	public function getAll(){
        $tags = array();
        if (!Auth::check()){
            $tags = array(
                'Home' => '/',
                'Login' => '/auth/login',
                'Register' => '/auth/register'
            );
            return $tags;
        }

		$user = Auth::user();
        if ($user->type==1){
            $tags = array(
                'Members' => '/users/members',
                'Roles' => '/users/members',
                'Ditricts' => '/districts',
                'Organizations' => '/organizations',
                'Groups' => '/groups',
                'Games' => '/games',
                'Edit Profile' => '/users/'.$user->uuid,
                'Upload CSV' => '/admin/importfiles',
                'Cloudinary Image' => '/admin/playground',
            );
            return $tags;
        }

        //Districts Menu
        $districtSuperAdmins = $user->getUserInRoleable('app\District')->wherePivot('user_id', $user->uuid)->wherePivot('role_id', 1)->orWherePivot('role_id', 2);
        $districtMembers = $user->getUserInRoleable('app\District')->wherePivot('user_id', $user->uuid)->wherePivot('role_id', 3);
        if ($districtSuperAdmins->count()){
            $tags = array_add($tags, 'Districts', '/districts');
        }

        if ($districtMembers->count()){
         foreach($districtMembers->get() as $district){
            $tags[$district->pivot->roleable_id] = '/districts/'.$district->pivot->roleable_id;
         }
        }

        //Organizations menu
        $organizationSuperAdmins = $user->getUserInRoleable('app\Organization')->wherePivot('user_id', $user->uuid)->wherePivot('role_id', 1)->orWherePivot('role_id', 2);
        $organizationMembers = $user->getUserInRoleable('app\Organization')->wherePivot('user_id', $user->uuid)->wherePivot('role_id', 3);
        if ($organizationSuperAdmins->count()){
            $tags = array_add($tags, 'Organizations', '/organizations');
        }
        if ($organizationMembers->count()){
            foreach($organizationMembers->get() as $organization){
                $tags[$organization->pivot->roleable_id] = '/organizations/'.$organization->pivot->roleable_id;
            }
        }

        //Groups menu
        $groupSuperAdmins = $user->getUserInRoleable('app\Group')->wherePivot('user_id', $user->uuid)->wherePivot('role_id', 1)->orWherePivot('role_id', 2);
        $groupMembers = $user->getUserInRoleable('app\Group')->wherePivot('user_id', $user->uuid)->wherePivot('role_id', 3);
        if ($groupSuperAdmins->count()){
            $tags = array_add($tags, 'Groups', '/groups');
        }
        if ($groupMembers->count()){
            foreach($groupMembers->get() as $group){
                $tags[$group->pivot->roleable_id] = '/groups/'.$group->pivot->roleable_id;
            }
            $tags['Friends'] = '/friends';
            $tags['Suggested Friends'] = '/suggestedfriends';
        }

        $tags['Games'] = '/games';
        $tags['Edit Profile'] = '/users/'.$user->uuid;
        $tags['Logout'] = '/auth/logout';


        return $tags;
    }
}
