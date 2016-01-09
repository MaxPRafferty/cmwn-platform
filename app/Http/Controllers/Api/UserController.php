<?php

namespace app\Http\Controllers\Api;

use Input;
use app\Transformer\UserTransformer;
use app\Transformer\GroupTransformer;
use app\Transformer\ImageTransformer;
use Illuminate\Support\Facades\Validator;
use app\User;
use app\Image;
use app\Group;
use Hash;

class UserController extends ApiController
{
    public function index()
    {
        $query = \Request::get('name') or null;

        if ($query) {
            $users = User::name($query)->get();
        } else {
            $users = User::take(10)->get();
        }

        return $this->respondWithCollection($users, new UserTransformer());
    }

    public function show($uuid)
    {
        $user = User::findByUuid($uuid);

        if (!$user) {
            return $this->errorNotFound('User not found');
        }

        return $this->respondWithItem($user, new UserTransformer());
    }

    public function update($uuid)
    {
        $user = User::findByUuid($uuid);

        if (!$user->canUpdate($this->currentUser)) {
            return $this->errorInternalError('You are not authorized.');
        }

        $validator = Validator::make(Input::all(), User::$updateRules);

        if (!$validator->passes()) {
            return $this->errorWrongArgs($validator->errors()->all());
        }

        if (!$user->canUpdate($this->currentUser)) {
            return $this->errorInternalError('You are not authorized.');
        }

        if ($user->updateMember(Input::all())) {
            return $this->respondWithItem($user, new UserTransformer());
        } else {
            return $this->errorInternalError('Could not save user.');
        }
    }

    public function create()
    {
        if ($this->currentUser->isSiteAdmin()) {
            $validator = Validator::make(Input::all(), User::$createRules);

            if (!$validator->passes()) {
                return $this->errorWrongArgs($validator->errors()->all());
            }

            $user = new User();

            try {
                $user->updateMember(Input::all());
            } catch (Exception $e) {
                return $this->errorInternalError($e->getMessage());
            }

            return $this->respondWithItem($user, new UserTransformer());
        } else {
            return $this->errorInternalError('You are not authorized to create users.');
        }
    }

    public function createDemoTeacher()
    {
        $validator = Validator::make(Input::all(), User::$createDemoTeacherRules);

        if (!$validator->passes()) {
            return $this->errorWrongArgs($validator->errors()->all());
        }

        $credentials = Input::only('email');
        $credentials['password'] = Hash::make('demo123');

        $user = User::create($credentials);

        try {
            $user->updateMember(Input::all());
        } catch (Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }

        $group = Group::find(2);

        $user->groups()->save($group, array('role_id' => 2));

        return $this->respondWithItem($user, new UserTransformer());
    }

    public function addToGroup($uuid)
    {
        if ($this->currentUser->isSiteAdmin()) {
            $validator = Validator::make(Input::all(), User::$addToGroupRules);

            if ($validator->fails()) {
                return $this->errorWrongArgs($validator->errors()->all());
            }

            $user = User::findByUuid($uuid);

            $group = Group::findByUuid(Input::get('group'));

            if ($user->groups()->save($group, array('role_id' => Input::get('role_id')))) {
                return $this->respondWithArray(array('message' => 'The user has been added to the group.'));
            } else {
                return $this->errorInternalError('Failed to add user to group.');
            }
        } else {
            return $this->errorInternalError('You are not authorized to create users.');
        }
    }

    public function getGroups($userId)
    {
        $user = User::with('groups')->find($userId);

        if (!$user) {
            return $this->errorNotFound('User not found');
        }

        return $this->respondWithCollection($user->groups, new GroupTransformer());
    }

    public function login()
    {
        return csrf_token();
    }

    public function showImage($user_id)
    {
        //$image = User::find(['uuid'=>'38bc77fc-a82f-11e5-8432-6c4008a38944'])->images;
        $image = User::findByUuid($user_id)->images;

        return $this->respondWithCollection($image, new ImageTransformer());
    }

    public function updateImage($uuid)
    {
        $user = User::findByUuid($uuid);

        if (!$user->canUpdate($this->currentUser)) {
            return $this->errorInternalError('You are not authorized.');
        }

        $validator = Validator::make(Input::all(), Image::$imageUpdateRules);

        if ($validator->fails()) {
            return $this->errorWrongArgs($validator->errors()->all());
        }

        if ($user->updateImage(Input::all())) {
            return $this->respondWithArray(array('message' => 'The image has been updated sucessfully.'));
        }
    }

    public function deleteImage($user_id)
    {
        $user = User::findFromInput($user_id);
        if (!$user->canUpdate($this->currentUser)) {
            return $this->errorInternalError('You are not authorized.');
        }

        $validator = Validator::make(Input::all(), Image::$imageUpdateRules);
        if ($validator->passes()) {
            $user = new User();
            if ($user->deleteImage($user_id)) {
                return $this->respondWithArray(array('message' => 'The image has been updated sucessfully.'));
            }

            return $this->errorInternalError('The image failed to delete');
        }
        $messages = print_r($validator->errors()->getMessages(), true);

        return $this->errorInternalError('Input validation error: '.$messages);
    }
}
