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
                'Edit Profile' => '/profile/edit',
                'Upload CSV' => '/admin/importfiles',
                'Cloudinary Image' => '/admin/playground',
            );
            return $tags;
        }
        
        $tags['Games'] = '/profile';

        //Districts Menu
        $districtMembers = $user->getUserInRoleable('app\District')->wherePivot('user_id', $user->uuid);
        if ($districtMembers->count()){
            if($districtMembers->count()>1) {
                $tags = array_add($tags, 'Districts', '/districts');
            }
            foreach($districtMembers->get() as $district){
            $tags[$district->title] = '/districts/'.$district->pivot->roleable_id;
         }
        }

        //Organizations menu
        $organizationMembers = $user->getUserInRoleable('app\Organization')->wherePivot('user_id', $user->uuid);
        if ($organizationMembers->count()){
            if($organizationMembers->count()>1) {
                $tags = array_add($tags, 'Organizations', '/organizations');
            }
            foreach($organizationMembers->get() as $organization){
                $tags[$organization->title] = '/organizations/'.$organization->pivot->roleable_id;
            }
        }

        //Groups menu
        $groupMembers = $user->getUserInRoleable('app\Group')->wherePivot('user_id', $user->uuid);

        if ($groupMembers->count()){
            if($groupMembers->count()>1) {
                $tags = array_add($tags, 'My Classes', '/groups');
                foreach($groupMembers->get() as $group){
                    $tags[' - '.$group->title] = '/groups/'.$group->pivot->roleable_id;
                }
            } elseif ($groupMembers->count() === 1) {
                foreach($groupMembers->get() as $group){
                    $tags[$group->title] = '/groups/'.$group->pivot->roleable_id;
                }
            }
            

            $tags['Friends'] = '/friends';
            $tags['Suggested Friends'] = '/suggestedfriends';
        }


        $tags['Edit Profile'] = '/profile/edit';
        $tags['Logout'] = '/auth/logout';


        return $tags;
    }
}
