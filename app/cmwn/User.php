<?php

namespace app;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use app\cmwn\Traits\RoleTrait;
use app\cmwn\Traits\EntityTrait;
use app\cmwn\Users\UsersRelationshipHandler;

class User extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, SoftDeletes, RoleTrait, EntityTrait;
    protected $dates = ['deleted_at'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'username',
        'student_id',
        'gender',
    ];

    public $relationship;

    public function setRelationshipAttribute($value = 'working')
    {
        $this->relationship = $value;
    }

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /*
     * Register all the form validation rules here for User
     */
    public static $createRules = array(
        'first_name' => 'required|string|min:2',
        'middle_name' => 'string|min:2',
        'last_name' => 'required|string|min:2',
        'email' => 'required|unique:users,email',
        'username' => 'required|alpha_dash|unique:users,username',
        'student_id' => 'required|alpha_dash|unique:users,username',
    );

    public static $createDemoTeacherRules = array(
        'first_name' => 'required|string|min:2',
        'last_name' => 'required|string|min:2',
        'email' => 'required|unique:users,email',
    );

    public static $createDemoStudentRules = array(
        'code' => 'required|string|regex:/(?i)^mreco$/',
        'username' => 'required|alpha_dash|unique:users,username',
    );

    public static $updateRules = array(
        'first_name' => 'string|min:2',
        'middle_name' => 'string|min:2',
        'last_name' => 'string|min:2',
        'email' => 'email|min:2',
        'student_id' => 'unique:users',
    );

    public static $deleteRules = array(
        //'id'=>'required|regex:/^[0-9]?$/',
    );

    public static $passwordUpdateRules = array(
        'user_id' => 'required|string',
        'current_password' => 'required',
        'password' => 'required|confirmed',
        'password_confirmation' => 'required',
    );

    public static $addToGroupRules = array(
        'group' => 'required',
        'role_id' => 'required|integer',
    );

    public function guardianReference()
    {
        return $this->belongsToMany('app\User', 'guardian_reference', 'user_id');
    }

    public function getRoles()
    {
        $roles = DB::table('roleables')
        ->select(DB::raw('roleable_type as entity, MAX(role_id) as role_id'))
        ->where('user_id', $this->id)
        ->orderByRaw(DB::raw("FIELD(roleable_type,'app\District','app\Organization','app\Group')"))
        ->groupBy('roleable_type')->get();

        return $roles;
    }

    public function images()
    {
        return $this->morphMany('app\Image', 'imageable');
    }

    public function districts()
    {
        return $this->morphedByMany('app\District', 'roleable')->withPivot('role_id');
    }

    public function getUserInRoleable($entity)
    {
        return $this->morphedByMany($entity, 'roleable');
    }

    public function organizations()
    {
        return $this->morphedByMany('app\Organization', 'roleable')->withPivot('role_id');
    }

    public function groups()
    {
        return $this->morphedByMany('app\Group', 'roleable')->withPivot('role_id');
    }

    public function takingClasses()
    {
        return $this->morphedByMany('app\Group', 'roleable')->wherePivot('role_id', 1);
    }

    public function teachingClasses()
    {
        return $this->morphedByMany('app\Group', 'roleable')->wherePivot('role_id', 2);
    }

    public function children()
    {
        return $this->belongsToMany('app\User', 'child_guardian', 'guardian_id', 'child_id');
    }

    public function guardians()
    {
        return $this->belongsToMany('app\User', 'child_guardian', 'child_id', 'guardian_id');
    }

    public function friends()
    {
        return $this->belongsToMany('app\User', 'friends', 'user_id', 'friend_id');
    }

    public function students()
    {
    }

    public function games()
    {
        return $this->belongsToMany('app\Game');
    }

    public function flips()
    {
        return $this->morphedByMany('app\Flip', 'roleable')->withPivot('role_id');
    }

    public static function findFromInput($input) // TODO remove this method
    {
        if ($input ==  'me') {
            return Auth::user();
        } else {
            return self::find($input);
        }
    }

    /**
     * Determins if user is a site admin.
     *
     * @return bool
     */
    public function isSiteAdmin()
    {
        return (Auth::user()->type == 1);
    }

    /**
     * Determins if one user can update another.
     *
     * @return bool
     */
    public function canUpdate(User $user)
    {
        return ($this->id == $user->id || $user->isSiteAdmin() ||
                 UsersRelationshipHandler::isUserInSameEntity($user, $this, 'districts') ||
                 UsersRelationshipHandler::isUserInSameEntity($user, $this, 'organizations') ||
                 UsersRelationshipHandler::isUserInSameEntity($user, $this, 'groups'));
    }

    public function entities($entity, $role_ids)
    {
        $result = $this->$entity();
        $result = $result->where(function ($query) use ($role_ids) {
            $query = $query->whereIn('role_id', $role_ids);
        });

        return $result;
    }

    public function acceptedfriends()
    {
        return $this->belongsToMany('app\User', 'friends', 'user_id', 'friend_id')->wherePivot('status', 1);
    }

    public function pendingfriends()
    {
        return $this->belongsToMany('app\User', 'friends', 'user_id', 'friend_id')->wherePivot('status', 0);
    }

    public function friendrequests()
    {
        return $this->belongsToMany('app\User', 'friends', 'friend_id')->wherePivot('friend_id', $this->id)->wherePivot('status', 0);
    }

    public function blockedrequests()
    {
        return $this->belongsToMany('app\User', 'friends', 'user_id', 'friend_id')->wherePivot('status', -2);
    }

    public function suggestedfriends()
    {
        $groups = $this->groups->lists('id');
        $suggested = self::whereHas('groups', function ($query) use ($groups) {
            $query->whereIn('roleable_id', $groups)->whereIn('role_id', [1]);
        })->where('id', '!=', $this->id)->lists('id')->toArray();
        $ids = [];

        foreach ($suggested as $friend_id) {
            $areWeFriends = UsersRelationshipHandler::areWeFriends($this->id, $friend_id);
            if (!$areWeFriends) {
                $ids[] = $friend_id;
            }
        }

        $data = self::whereIn('id', $ids)->get();
        foreach ($data as $user) {
            $pendingfriend = (self::getRelationship($user->id)) ? 'Pending' : null;
            $requestedfriend = (self::getRelationship($user->id, 'friend_id')) ? 'requested' : null;
            if ($requestedfriend) {
                $user->relationship = $requestedfriend;
            }
            if ($pendingfriend) {
                $user->relationship = $pendingfriend;
            }
        }

        return $data;
    }

    public static function getRelationship($user_id, $field = 'user_id', $status = 0)
    {
        return self::whereHas('friends', function ($query) use ($user_id, $field, $status) {
            $query->where($field, $user_id)->where('status', $status);
        })->count();
    }

    public function siblings()
    {
        return false;
    }

    public function hasRole(Array $roles)
    {
        foreach ($roles as $role) {
            if ($this->role->contains('title', $role)) {
                return true;
            }
        }

        return false;
    }

    public function updateMember($params)
    {
        if (isset($params['username'])) {
            $this->username = $params['username'];
        }

        if (isset($params['student_id'])) {
            $this->student_id = $params['student_id'];
        }

        if (isset($params['first_name'])) {
            $this->first_name = $params['first_name'];
        }

        if (isset($params['middle_name'])) {
            $this->middle_name = $params['middle_name'];
        }

        if (isset($params['last_name'])) {
            $this->last_name = $params['last_name'];
        }

        if (isset($params['gender'])) {
            $this->gender = $params['gender'];
        }

        if (isset($params['birthdate'])) {
            $this->dob = $params['birthdate'];
        }

        if ($this->save()) {
            return true;
        }

        return false;
    }

    public static function deleteMember($id)
    {
        $user = self::find($id);
        if (!$user->role()->detach()) { // TODO move to event listener.
            $user->delete();
        }

        if ($user) {
            return true;
        }

        return false;
    }

    /**
     * Scope a query to only include users of a given type.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUuid($query, $val)
    {
        return $query->where('uuid', $val);
    }

    public function scopeName($query, $val)
    {
        return $query->where('name', $val);
    }

    public function deleteImage($user_id)
    {
        $user = self::find($user_id);
        $image = new Image();
        if ($user->images()->delete()) {
            return true;
        }

        return false;
    }

    public function updatePassword($user, $newPassword)
    {
        return $user->fill([
            'password' => \Hash::make($newPassword),
        ])->save();
    }

    public static function getUniqueUsername($username = 'WorldChanger')
    {
        $count = count(User::whereRaw("username REGEXP '^{$username}(-[0-9]+)?$'")->get());

        return ($count > 0) ? "{$username}-{$count}" : $username;
    }
}
