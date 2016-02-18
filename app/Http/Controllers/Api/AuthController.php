<?php

namespace app\Http\Controllers\Api;

use Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use app\User;

class AuthController extends ApiController
{
    /**
     * Handle an authentication attempt.
     *
     * @return Response
     */
    public function authenticate()
    {
        $hash = base64_decode(substr(\Request::header('Authorization'), 6));
        list($email, $password) = explode(':', $hash);

        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            return $this->respondWithArray(['message' => 'Welcome!']);
        } else {
            if (Auth::attempt(['username' => $email, 'password' => $password])) {
                return $this->respondWithArray(['message' => 'Welcome!']);
            } else {
                return $this->errorUnauthorized('Login attempt was unsuccessful.');
            }
        }
    }

    public function updatePassword()
    {
        $user = User::findByUuid(Input::get('user'));

        $validator = Validator::make($data = Input::all(), User::$passwordUpdateRules);

        if ($validator->fails()) {
            return $this->errorWrongArgs($validator->errors()->all());
        }

        if (!$user->canUpdate($this->currentUser)) {
            return $this->errorUnauthorized();
        }

        if ($user->updatePassword($user, $data['password_confirmation'])) {
            return $this->respondWithArray(array('message' => 'The password has been updated successfully!'));
        }

        return $this->errorInternalError('Something went wrong, please try again.');
    }

    public function logout()
    {
        Auth::logout();
    }
}
