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
            $tags[ "home" ] = "/";
            $tags[ "login" ] = "/auth/login";
            $tags[ "register" ] = "/auth/register";
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

		if ($user->districts->count()){
            $tags = array_add($tags, 'Districts', '/districts');
        }

        if ($user->organizations->count()){
            $tags = array_add($tags, 'Organizations', '/organizations');
        }
        if ($user->groups->count()){
            $tags = array_add($tags, 'Groups', '/groups');
        }
        return $tags;
    }
}
